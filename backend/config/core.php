<?php 

session_start();

require_once __DIR__ . '/db_connect.php';

// echo $_SESSION['userId'];

if(!isset($_SESSION['userId']) || empty($_SESSION['userId'])) {
	header('location:'.$store_url.'backend/admin/login.php');	
} 



?>