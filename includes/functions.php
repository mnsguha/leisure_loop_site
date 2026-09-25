<?php
/**
 * Core helper functions
 */

if (session_status() !== PHP_SESSION_ACTIVE) {
    session_start();
}

// One CSRF token per session: embedded by every form, verified by every mutating endpoint.
if (empty($_SESSION['csrf_token'])) {
    try {
        $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
    } catch (Exception $e) {
        $_SESSION['csrf_token'] = bin2hex(openssl_random_pseudo_bytes(32));
    }
}

/**
 * Stamp the "form was rendered" time used by the anti-bot time-trap.
 * Call once at the top of every page controller — NEVER on API endpoints
 * (stamping there would reset the trap on the submit request itself).
 */
function csrf_stamp_form() {
    $_SESSION['form_ts'] = time();
}

/**
 * Sliding-window hit counter stored in the session.
 * Returns true when the bucket is already full (request should be rejected).
 */
function lead_rate_session_hit($bucket, $max, $windowSec) {
    $now = time();
    $hits = array_values(array_filter((array) ($_SESSION[$bucket] ?? []), function ($ts) use ($now, $windowSec) {
        return ((int) $ts) > ($now - $windowSec);
    }));
    if (count($hits) >= $max) {
        $_SESSION[$bucket] = $hits;
        return true;
    }
    $hits[] = $now;
    $_SESSION[$bucket] = $hits;
    return false;
}

/**
 * Sliding-window per-IP counter in the system temp dir (flock-guarded).
 * Redis-based tiers are not available in this stack; this is the
 * documented session + IP-file equivalent of the rate-limit rule.
 */
function lead_rate_ip_hit($ip, $max, $windowSec) {
    $now = time();
    $file = rtrim(sys_get_temp_dir(), DIRECTORY_SEPARATOR) . DIRECTORY_SEPARATOR . 'll_rl_' . md5((string) $ip) . '.json';
    $hits = [];
    $raw = @file_get_contents($file);
    if ($raw !== false) {
        $decoded = json_decode($raw, true);
        if (is_array($decoded)) {
            $hits = array_values(array_filter($decoded, function ($ts) use ($now, $windowSec) {
                return ((int) $ts) > ($now - $windowSec);
            }));
        }
    }
    if (count($hits) >= $max) {
        @file_put_contents($file, json_encode($hits));
        return true;
    }
    $hits[] = $now;
    $fp = @fopen($file, 'c+');
    if ($fp) {
        @flock($fp, LOCK_EX);
        @ftruncate($fp, 0);
        @fwrite($fp, json_encode($hits));
        @flock($fp, LOCK_UN);
        @fclose($fp);
    }
    return false;
}

/**
 * Anti-bot guard for JSON lead/booking endpoints.
 * Order: honeypot (silent fake success) -> CSRF -> time-trap -> rate limits.
 * Exits with a JSON payload in every failure path.
 */
function lead_guard_json($post) {
    $honeypot = isset($post['fax_office']) ? trim((string) $post['fax_office']) : '';
    if ($honeypot !== '') {
        echo json_encode([
            'success' => true,
            'message' => 'Thank you! Our travel expert will contact you shortly.',
            'sync_status' => 'queued'
        ]);
        exit;
    }

    $token = isset($post['csrf_token']) ? (string) $post['csrf_token'] : '';
    if ($token === '' || !hash_equals((string) ($_SESSION['csrf_token'] ?? ''), $token)) {
        echo json_encode(['success' => false, 'message' => 'Your session expired. Please refresh the page and try again.']);
        exit;
    }

    $formTs = (int) ($_SESSION['form_ts'] ?? 0);
    if ($formTs <= 0 || (time() - $formTs) < 3) {
        echo json_encode(['success' => false, 'message' => 'Please wait a moment, then try again.']);
        exit;
    }

    if (lead_rate_session_hit('lead_form_hits', 5, 600)) {
        echo json_encode(['success' => false, 'message' => 'Too many requests. Please try again in a few minutes.']);
        exit;
    }

    if (lead_rate_ip_hit($_SERVER['REMOTE_ADDR'] ?? '0.0.0.0', 15, 600)) {
        echo json_encode(['success' => false, 'message' => 'Too many requests. Please try again in a few minutes.']);
        exit;
    }
}

/**
 * Same guard for native (non-AJAX) forms that redirect back with error flags.
 */
function lead_guard_redirect($post, $errorFlag) {
    $backTo = function ($flag) {
        $redirect = isset($_SERVER['HTTP_REFERER']) && $_SERVER['HTTP_REFERER'] !== '' ? $_SERVER['HTTP_REFERER'] : 'index.php';
        $redirect .= (strpos($redirect, '?') !== false ? '&' : '?') . $flag . '=1';
        header('Location: ' . $redirect);
        exit;
    };

    $honeypot = isset($post['fax_office']) ? trim((string) $post['fax_office']) : '';
    if ($honeypot !== '') {
        header('Location: ' . (isset($_SERVER['HTTP_REFERER']) && $_SERVER['HTTP_REFERER'] !== '' ? $_SERVER['HTTP_REFERER'] : 'index.php'));
        exit;
    }

    $token = isset($post['csrf_token']) ? (string) $post['csrf_token'] : '';
    if ($token === '' || !hash_equals((string) ($_SESSION['csrf_token'] ?? ''), $token)) {
        $backTo($errorFlag);
    }

    $formTs = (int) ($_SESSION['form_ts'] ?? 0);
    if ($formTs <= 0 || (time() - $formTs) < 3) {
        $backTo($errorFlag);
    }

    if (lead_rate_session_hit('lead_form_hits', 5, 600)) {
        $backTo($errorFlag);
    }

    if (lead_rate_ip_hit($_SERVER['REMOTE_ADDR'] ?? '0.0.0.0', 15, 600)) {
        $backTo($errorFlag);
    }
}

/**
 * Check if user is logged in as admin
 */
function isAdmin() {
    return isset($_SESSION['admin_logged_in']) && $_SESSION['admin_logged_in'] === true;
}

/**
 * Redirect if not admin. Also enforces CSRF on every admin POST
 * (covers all admin pages and api-sync-crm, which funnel through here).
 */
function requireAdmin() {
    if (!isAdmin()) {
        header('Location: login.php');
        exit;
    }
    if (($_SERVER['REQUEST_METHOD'] ?? 'GET') === 'POST') {
        $token = isset($_POST['csrf_token']) ? (string) $_POST['csrf_token'] : '';
        if ($token === '' && isset($_SERVER['HTTP_X_CSRF_TOKEN'])) {
            $token = (string) $_SERVER['HTTP_X_CSRF_TOKEN'];
        }
        if ($token === '' || !hash_equals((string) ($_SESSION['csrf_token'] ?? ''), $token)) {
            http_response_code(403);
            header('Content-Type: application/json');
            echo json_encode(['success' => false, 'message' => 'Invalid request token. Please refresh the page and retry.']);
            exit;
        }
    }
}

/**
 * PDF download ownership: booking endpoints grant the submitting session
 * access to its own slip/voucher ids; downloads require the grant (or admin).
 */
function pdf_grant_access(string $bucket, string $id): void {
    $list = $_SESSION['ll_pdf_ids'][$bucket] ?? [];
    $list[] = (string) $id;
    $_SESSION['ll_pdf_ids'][$bucket] = array_slice(array_unique($list), -20);
}

function pdf_allowed(string $bucket, string $id): bool {
    if (isAdmin()) {
        return true;
    }
    return in_array((string) $id, $_SESSION['ll_pdf_ids'][$bucket] ?? [], true);
}

/**
 * Generate a clean slug from a string
 */
function slugify($text) {
    $text = preg_replace('~[^\pL\d]+~u', '-', $text);
    $text = iconv('utf-8', 'us-ascii//TRANSLIT', $text);
    $text = preg_replace('~[^-\w]+~', '', $text);
    $text = trim($text, '-');
    $text = preg_replace('~-+~', '-', $text);
    $text = strtolower($text);
    return empty($text) ? 'n-a' : $text;
}

/**
 * Check whether a table contains a given column.
 */
function tableHasColumn($pdo, $table, $column) {
    static $cache = [];

    if (!$pdo) {
        return false;
    }

    $key = $table . '.' . $column;
    if (array_key_exists($key, $cache)) {
        return $cache[$key];
    }

    try {
        $stmt = $pdo->prepare("SHOW COLUMNS FROM `$table` LIKE ?");
        $stmt->execute([$column]);
        $cache[$key] = (bool) $stmt->fetch();
    } catch (Throwable $e) {
        $cache[$key] = false;
    }

    return $cache[$key];
}

/**
 * Return the first available non-empty value from a row.
 */
function firstFilledValue($row, $keys, $default = '') {
    foreach ($keys as $key) {
        if (isset($row[$key]) && $row[$key] !== null && $row[$key] !== '') {
            return $row[$key];
        }
    }

    return $default;
}

/**
 * Handle single image upload.
 * 
 * @param string $fileInputName The name of the file input in $_FILES
 * @param string $uploadDir The absolute path to save the file
 * @param string $publicPath The base public path to save in DB
 * @return string|null The public path to the uploaded file, or null if no upload/failed
 */
function handleImageUpload($fileInputName, $uploadDir, $publicPath = 'images/dest/') {
    if (isset($_FILES[$fileInputName]) && $_FILES[$fileInputName]['error'] === UPLOAD_ERR_OK) {
        $tmpName = $_FILES[$fileInputName]['tmp_name'];
        $fileName = basename($_FILES[$fileInputName]['name']);
        
        if (!is_dir($uploadDir)) {
            mkdir($uploadDir, 0755, true);
        }
        
        $ext = strtolower(pathinfo($fileName, PATHINFO_EXTENSION));
        $allowed = ['webp', 'jpg', 'jpeg', 'png', 'gif'];
        if (in_array($ext, $allowed)) {
            $newFileName = uniqid() . '_' . preg_replace("/[^a-zA-Z0-9.\-_]/", "", $fileName);
            $targetPath = rtrim($uploadDir, '/') . '/' . $newFileName;
            
            if (move_uploaded_file($tmpName, $targetPath)) {
                return rtrim($publicPath, '/') . '/' . $newFileName;
            }
        }
    }
    return null;
}

/**
 * Handle array image upload (e.g. for dynamic rows).
 * 
 * @param string $fileInputName The name of the array file input in $_FILES
 * @param int|string $index The array key index
 * @param string $uploadDir The absolute path to save the file
 * @param string $publicPath The base public path to save in DB
 * @return string|null The public path to the uploaded file, or null if no upload/failed
 */
function handleArrayImageUpload($fileInputName, $index, $uploadDir, $publicPath = 'images/dest/') {
    if (isset($_FILES[$fileInputName]['error'][$index]) && $_FILES[$fileInputName]['error'][$index] === UPLOAD_ERR_OK) {
        $tmpName = $_FILES[$fileInputName]['tmp_name'][$index];
        $fileName = basename($_FILES[$fileInputName]['name'][$index]);
        
        if (!is_dir($uploadDir)) {
            mkdir($uploadDir, 0755, true);
        }
        
        $ext = strtolower(pathinfo($fileName, PATHINFO_EXTENSION));
        $allowed = ['webp', 'jpg', 'jpeg', 'png', 'gif'];
        if (in_array($ext, $allowed)) {
            $newFileName = uniqid() . '_' . preg_replace("/[^a-zA-Z0-9.\-_]/", "", $fileName);
            $targetPath = rtrim($uploadDir, '/') . '/' . $newFileName;
            
            if (move_uploaded_file($tmpName, $targetPath)) {
                return rtrim($publicPath, '/') . '/' . $newFileName;
            }
        }
    }
    return null;
}
?>
