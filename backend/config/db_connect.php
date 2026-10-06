<?php 	

$localhost = "sql103.infinityfree.com";
$username = "if0_43098432";
$password = "AOAnvTlcdSmJv";
$dbname = "if0_43098267_store";
$store_url = "http://localhost/jablestore/";
// db connection
$connect = new mysqli($localhost, $username, $password, $dbname);
// check connection
if($connect->connect_error) {
  die("Connection Failed : " . $connect->connect_error);
} else {
  // echo "Successfully connected";
}

?>