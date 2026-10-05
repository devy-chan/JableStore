<?php
header('Content-Type: application/json; charset=utf-8');

require_once __DIR__ . '/../config/db_connect.php';

if ($connect->connect_error) {
    http_response_code(500);
    echo json_encode([
        'success' => false,
        'message' => 'Database connection failed.',
        'data' => []
    ]);
    exit;
}

// Only categories that are active and not removed from the Admin dashboard
// are exposed to the public storefront.
$sql = "SELECT categories_id, categories_name
        FROM categories
        WHERE categories_status = 1
          AND categories_active = 1
          AND TRIM(categories_name) <> ''
        ORDER BY categories_name ASC";

$result = $connect->query($sql);

if (!$result) {
    http_response_code(500);
    echo json_encode([
        'success' => false,
        'message' => 'Unable to load categories.',
        'data' => []
    ]);
    $connect->close();
    exit;
}

$categories = [];
while ($row = $result->fetch_assoc()) {
    $categories[] = [
        'id' => (int) $row['categories_id'],
        'name' => $row['categories_name']
    ];
}

$connect->close();

echo json_encode([
    'success' => true,
    'count' => count($categories),
    'data' => $categories
]);
