<?php
/**
 * JABLE STORE - ONLINE DATABASE CONFIGURATION
 *
 * Before uploading this project to your hosting account, replace the
 * four database values below with the credentials provided by your host.
 */

$localhost = "sql300.infinityfree.com";
$username  = "if0_43097883";
$password  = "yTLXlcSe5Nw";
$dbname    = "if0_43097883_store";

// IMPORTANT: include the final slash in your live website URL.
// Example: https://yourstore.example.com/
$store_url = "https://your-domain/backend/admin/login.php";

$connect = new mysqli($localhost, $username, $password, $dbname);

if ($connect->connect_error) {
    die("Database connection failed.");
}

$connect->set_charset("utf8mb4");
?>
