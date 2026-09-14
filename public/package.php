<?php
// Canonical 301 Redirect to all-tours.php (or package-detail.php if slug provided)
if (!empty($_GET['slug'])) {
    header("HTTP/1.1 301 Moved Permanently");
    header("Location: package-detail.php?slug=" . urlencode($_GET['slug']));
    exit;
}
header("HTTP/1.1 301 Moved Permanently");
header("Location: all-tours.php" . (!empty($_SERVER['QUERY_STRING']) ? '?' . $_SERVER['QUERY_STRING'] : ''));
exit;