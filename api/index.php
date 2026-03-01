<?php
// Gateway for Vercel - This routes all requests to the correct PHP files
$uri = parse_url($_SERVER['REQUEST_URI'], PHP_URL_PATH);
$file = ltrim($uri, '/');

if ($file === '') {
    $file = 'index.php';
}

// If it's a directory, look for index.php inside it (e.g., /admin -> /admin/index.php)
if (is_dir(__DIR__ . '/../' . $file)) {
    $file = rtrim($file, '/') . '/index.php';
}

// Handle missing .php extension (e.g., /about -> /about.php)
if (!file_exists(__DIR__ . '/../' . $file) && file_exists(__DIR__ . '/../' . $file . '.php')) {
    $file .= '.php';
}

// Check if the file exists in the root directory
if (file_exists(__DIR__ . '/../' . $file)) {
    // Set correct server variables so the app thinks it's running normally
    $_SERVER['SCRIPT_FILENAME'] = realpath(__DIR__ . '/../' . $file);
    $_SERVER['SCRIPT_NAME'] = '/' . $file;
    $_SERVER['PHP_SELF'] = '/' . $file;

    // Change working directory to where the file is located (important for includes)
    chdir(dirname($_SERVER['SCRIPT_FILENAME']));

    require $_SERVER['SCRIPT_FILENAME'];
} else {
    // 404 Fallback
    http_response_code(404);
    require __DIR__ . '/../index.php';
}
