<?php
/**
 * JableStore database configuration.
 *
 * Production credentials should be placed in backend/config/db_connect.local.php
 * (this file is ignored by Git) or supplied through environment variables.
 * Local XAMPP defaults are kept as a fallback for development.
 */

$localConfig = __DIR__ . '/db_connect.local.php';

if (file_exists($localConfig)) {
    require $localConfig;
}

$localhost = $localhost ?? getenv('DB_HOST') ?: 'localhost';
$username  = $username  ?? getenv('DB_USER') ?: 'root';
$password  = $password  ?? getenv('DB_PASSWORD') ?: '';
$dbname    = $dbname    ?? getenv('DB_NAME') ?: 'store';
$store_url = $store_url ?? getenv('STORE_URL') ?: 'http://localhost/jablestore/';

// Always end the application URL with a slash.
$store_url = rtrim($store_url, '/') . '/';

$connect = new mysqli($localhost, $username, $password, $dbname);

if ($connect->connect_error) {
    error_log('JableStore database connection failed: ' . $connect->connect_error);
    die('Database connection failed. Please check the server configuration.');
}

$connect->set_charset('utf8mb4');
?>
