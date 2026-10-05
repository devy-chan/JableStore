<?php 	

require_once __DIR__ . '/../config/core.php';

$productId = $_GET['i'];

$sql = "SELECT product_image FROM product WHERE product_id = {$productId}";
$data = $connect->query($sql);
$result = $data->fetch_row();

$connect->close();

echo $store_url . 'assests/images/stock/' . rawurlencode(basename(str_replace('\\', '/', $result[0])));
