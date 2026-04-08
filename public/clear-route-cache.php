<?php
// One-time script to clear stale route cache
$cacheFile = __DIR__ . '/../bootstrap/cache/routes-v7.php';
if (file_exists($cacheFile)) {
    unlink($cacheFile);
    echo 'Route cache cleared';
} else {
    echo 'No route cache found';
}
