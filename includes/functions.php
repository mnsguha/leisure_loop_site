<?php
/**
 * Core helper functions
 */

// --- Tunables ---------------------------------------------------------------
const LL_TIME_TRAP_SEC = 2.0;          // min seconds between form render and submit (anti-bot time-trap)
const LL_TRUSTED_PROXIES = [];         // client IPs allowed to set X-Forwarded-For / X-Forwarded-Proto
const LL_FORCE_SECURE_COOKIES = false; // set true when TLS terminates at a trusted proxy in front of Apache

/** Stable per-request id (uuid format) for API envelopes and log correlation. */
function ll_request_id(): string {
    static $id = null;
    if ($id === null) {
        $raw = bin2hex(random_bytes(16));
        $id = sprintf(
            '%s-%s-%s-%s-%s',
            substr($raw, 0, 8),
            substr($raw, 8, 4),
            substr($raw, 12, 4),
            substr($raw, 16, 4),
            substr($raw, 20, 12)
        );
    }
    return $id;
}

/**
 * Standard API response envelope (Part 1, Rule 10).
 * Keeps the legacy `success` / `message` keys and mirrors every $data key at
 * the top level so pre-envelope JS consumers (booking_id, sync_status, ...) keep working.
 * Always exits — all JSON controller outcomes funnel through here.
 */
function ll_json_response(string $status, string $code, string $message, array $data = [], array $errors = [], int $httpCode = 200): void {
    if (!headers_sent()) {
        header('Content-Type: application/json');
    }
    if ($httpCode !== 200) {
        http_response_code($httpCode);
    }
    $payload = [
        'status' => $status,
        'request_id' => ll_request_id(),
        'timestamp' => gmdate('c'),
        'code' => $code,
        'message' => $message,
        'errors' => array_values($errors),
        'success' => ($status === 'success'),
        'data' => $data,
    ];
    foreach ($data as $k => $v) {
        if (!array_key_exists($k, $payload)) {
            $payload[$k] = $v;
        }
    }
    echo json_encode($payload);
    exit;
}

/** Client IP — honours X-Forwarded-For only when REMOTE_ADDR is in LL_TRUSTED_PROXIES. */
function ll_client_ip(): string {
    $remote = (string) ($_SERVER['REMOTE_ADDR'] ?? '0.0.0.0');
    if (LL_TRUSTED_PROXIES && in_array($remote, LL_TRUSTED_PROXIES, true)) {
        $xff = (string) ($_SERVER['HTTP_X_FORWARDED_FOR'] ?? '');
        if ($xff !== '') {
            $candidate = trim(explode(',', $xff)[0]);
            if (filter_var($candidate, FILTER_VALIDATE_IP)) {
                return $candidate;
            }
        }
    }
    return $remote;
}

/** True when the request arrived over HTTPS (directly, or via a trusted proxy). */
function ll_is_https(): bool {
    if (LL_FORCE_SECURE_COOKIES) {
        return true;
    }
    $https = (string) ($_SERVER['HTTPS'] ?? '');
    if ($https !== '' && strtolower($https) !== 'off') {
        return true;
    }
    if (LL_TRUSTED_PROXIES && in_array((string) ($_SERVER['REMOTE_ADDR'] ?? ''), LL_TRUSTED_PROXIES, true)) {
        return strtolower((string) ($_SERVER['HTTP_X_FORWARDED_PROTO'] ?? '')) === 'https';
    }
    return false;
}

/** Correlate a security rejection in error.log (Rule 90) — never logs request bodies (Rule 91). */
function ll_log_security(string $code): void {
    error_log(sprintf(
        '[security] INFO request_id=%s code=%s ip=%s uri=%s',
        ll_request_id(),
        $code,
        ll_client_ip(),
        (string) ($_SERVER['REQUEST_URI'] ?? '-')
    ));
}

// Hardened session cookie (Rule 69: HttpOnly; SameSite=Strict; Secure when HTTPS).
ini_set('session.use_strict_mode', '1');
session_set_cookie_params([
    'lifetime' => 0,
    'path' => '/',
    'domain' => '',
    'secure' => ll_is_https(),
    'httponly' => true,
    'samesite' => 'Strict',
]);
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
    $_SESSION['form_ts'] = microtime(true);
}

/**
 * Multi-tier rate-limit adapter (Part 1, Rule 70).
 * Tiers: 'session' (per browser session), 'endpoint' (per endpoint + client), 'ip'.
 * Persistence: Redis when the extension is reachable, otherwise a flock-guarded
 * file per key under sys_get_temp_dir() (documented fallback — docs/ADR-001).
 * Returns true when the bucket is full (request should be rejected).
 */
function lead_rate_hit(string $tier, string $key, int $max, int $windowSec): bool {
    $now = time();
    $bucket = $tier . ':' . $key;

    if ($tier !== 'session') {
        $redis = lead_rate_redis();
        if ($redis !== null) {
            try {
                $redisKey = 'll:rl:' . md5($bucket);
                $count = (int) $redis->incr($redisKey);
                if ($count === 1) {
                    $redis->expire($redisKey, $windowSec);
                }
                if ($count > $max) {
                    return true;
                }
                return false;
            } catch (Throwable $e) {
                // Redis failed mid-flight — fall through to local storage.
            }
        }
    }

    if ($tier === 'session') {
        $hits = array_values(array_filter((array) ($_SESSION['ll_rl'][$key] ?? []), function ($ts) use ($now, $windowSec) {
            return ((int) $ts) > ($now - $windowSec);
        }));
        if (count($hits) >= $max) {
            $_SESSION['ll_rl'][$key] = $hits;
            return true;
        }
        $hits[] = $now;
        $_SESSION['ll_rl'][$key] = $hits;
        return false;
    }

    return lead_rate_file_hit($bucket, $max, $windowSec, $now);
}

/** Lazily connect to local Redis; returns null (unavailable) otherwise. */
function lead_rate_redis() {
    static $state = 0; // 0 = unknown, 1 = connected, -1 = unavailable
    static $redis = null;
    if ($state === -1) {
        return null;
    }
    if ($state === 1) {
        return $redis;
    }
    $state = -1;
    if (!extension_loaded('redis')) {
        return null;
    }
    try {
        $redis = new Redis();
        $redis->connect('127.0.0.1', 6379, 0.25);
        $state = 1;
        return $redis;
    } catch (Throwable $e) {
        $redis = null;
        return null;
    }
}

/** Sliding-window file counter (flock-guarded) with opportunistic GC. */
function lead_rate_file_hit(string $bucket, int $max, int $windowSec, int $now): bool {
    $dir = defined('LL_RL_DIR') && LL_RL_DIR ? LL_RL_DIR : rtrim(sys_get_temp_dir(), DIRECTORY_SEPARATOR);
    $file = $dir . DIRECTORY_SEPARATOR . 'll_rl_' . md5($bucket) . '.json';
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
        lead_rate_gc($dir, $now);
        return true;
    }
    $hits[] = $now;
    $fp = @fopen($file, 'c+');
    if (!$fp) {
        lead_rate_store_warning($dir);
        return false; // fail open, warned once (availability over strictness for public forms)
    }
    @flock($fp, LOCK_EX);
    @ftruncate($fp, 0);
    @fwrite($fp, json_encode($hits));
    @flock($fp, LOCK_UN);
    @fclose($fp);
    lead_rate_gc($dir, $now);
    return false;
}

/** Opportunistically unlink rate files older than 2 hours (1-in-50 writes). */
function lead_rate_gc(string $dir, int $now): void {
    static $done = false;
    if ($done || random_int(1, 50) !== 1) {
        return;
    }
    $done = true;
    foreach ((array) @glob($dir . DIRECTORY_SEPARATOR . 'll_rl_*.json') as $f) {
        if (@filemtime($f) < $now - 7200) {
            @unlink($f);
        }
    }
}

/** One-shot warning when the rate-limit store cannot be written. */
function lead_rate_store_warning(string $dir): void {
    static $warned = false;
    if ($warned) {
        return;
    }
    $warned = true;
    error_log('[security] WARNING rate-limit store unwritable: ' . $dir);
}

/**
 * Anti-bot guard for JSON lead/booking endpoints.
 * Order: honeypot (silent fake success) -> CSRF -> time-trap -> rate limits.
 * Exits with a JSON payload in every failure path.
 */
function lead_guard_json($post) {
    $honeypot = isset($post['fax_office']) ? trim((string) $post['fax_office']) : '';
    if ($honeypot !== '') {
        ll_json_response('success', 'OK', 'Thank you! Our travel expert will contact you shortly.', ['sync_status' => 'queued']);
    }

    $token = isset($post['csrf_token']) ? (string) $post['csrf_token'] : '';
    if ($token === '' || !hash_equals((string) ($_SESSION['csrf_token'] ?? ''), $token)) {
        ll_log_security('CSRF_REJECT');
        ll_json_response('error', 'CSRF_REJECT', 'Your session expired. Please refresh the page and try again.');
    }

    $formTs = (float) ($_SESSION['form_ts'] ?? 0);
    if ($formTs <= 0 || (microtime(true) - $formTs) < LL_TIME_TRAP_SEC) {
        ll_log_security('TIMING_REJECT');
        ll_json_response('error', 'TIMING_REJECT', 'Please wait a moment, then try again.');
    }

    if (lead_rate_hit('session', 'lead_form_hits', 5, 600)) {
        ll_log_security('RATE_LIMIT');
        ll_json_response('error', 'RATE_LIMIT', 'Too many requests. Please try again in a few minutes.');
    }

    $endpoint = basename((string) ($_SERVER['SCRIPT_NAME'] ?? 'unknown'));
    if (lead_rate_hit('endpoint', $endpoint . '|' . ll_client_ip(), 10, 600)) {
        ll_log_security('RATE_LIMIT');
        ll_json_response('error', 'RATE_LIMIT', 'Too many requests. Please try again in a few minutes.');
    }

    if (lead_rate_hit('ip', 'lead:' . ll_client_ip(), 15, 600)) {
        ll_log_security('RATE_LIMIT');
        ll_json_response('error', 'RATE_LIMIT', 'Too many requests. Please try again in a few minutes.');
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

    $formTs = (float) ($_SESSION['form_ts'] ?? 0);
    if ($formTs <= 0 || (microtime(true) - $formTs) < LL_TIME_TRAP_SEC) {
        $backTo($errorFlag);
    }

    if (lead_rate_hit('session', 'lead_form_hits', 5, 600)) {
        $backTo($errorFlag);
    }

    $endpoint = basename((string) ($_SERVER['SCRIPT_NAME'] ?? 'unknown'));
    if (lead_rate_hit('endpoint', $endpoint . '|' . ll_client_ip(), 10, 600)) {
        $backTo($errorFlag);
    }

    if (lead_rate_hit('ip', 'lead:' . ll_client_ip(), 15, 600)) {
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
            ll_log_security('FORBIDDEN');
            ll_json_response('error', 'FORBIDDEN', 'Invalid request token. Please refresh the page and retry.', [], [], 403);
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
