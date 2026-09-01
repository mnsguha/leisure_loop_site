<?php
/**
 * Core helper functions
 */

session_start();

/**
 * Check if user is logged in as admin
 */
function isAdmin() {
    return isset($_SESSION['admin_logged_in']) && $_SESSION['admin_logged_in'] === true;
}

/**
 * Redirect if not admin
 */
function requireAdmin() {
    if (!isAdmin()) {
        header('Location: login.php');
        exit;
    }
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
