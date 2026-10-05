<?php
header('Content-Type: application/json; charset=utf-8');

require_once __DIR__ . '/../config/db_connect.php';

if ($connect->connect_error) {
    http_response_code(500);
    echo json_encode(['success' => false, 'message' => 'Database connection failed.']);
    exit;
}

$search = isset($_GET['search']) ? trim($_GET['search']) : '';
$category = isset($_GET['category']) ? (int) $_GET['category'] : 0;
$brand = isset($_GET['brand']) ? (int) $_GET['brand'] : 0;

$sql = "SELECT
            p.product_id,
            p.product_name,
            p.product_description,
            p.product_image,
            p.quantity,
            p.rate,
            p.active,
            p.brand_id,
            p.categories_id,
            b.brand_name,
            c.categories_name
        FROM product p
        INNER JOIN brands b ON p.brand_id = b.brand_id
        INNER JOIN categories c ON p.categories_id = c.categories_id
        WHERE p.status = 1 AND p.active = 1 AND CAST(p.quantity AS DECIMAL(15,2)) > 0";

$params = [];
$types = '';

if ($search !== '') {
    $sql .= " AND (p.product_name LIKE CONCAT('%', ?, '%') OR b.brand_name LIKE CONCAT('%', ?, '%') OR c.categories_name LIKE CONCAT('%', ?, '%'))";
    $params = [$search, $search, $search];
    $types = 'sss';
}

if ($category > 0) {
    $sql .= " AND p.categories_id = ?";
    $params[] = $category;
    $types .= 'i';
}

if ($brand > 0) {
    $sql .= " AND p.brand_id = ?";
    $params[] = $brand;
    $types .= 'i';
}

$sql .= " ORDER BY p.product_id DESC";

$stmt = $connect->prepare($sql);
if (!$stmt) {
    http_response_code(500);
    echo json_encode(['success' => false, 'message' => 'Unable to prepare product query.']);
    exit;
}

if (!empty($params)) {
    $stmt->bind_param($types, ...$params);
}

$stmt->execute();
$result = $stmt->get_result();

$products = [];
while ($row = $result->fetch_assoc()) {
    // Only use an image that was actually uploaded through the Admin > Add Product form.
    // Do not generate or assign category/sample images automatically.
    // The storefront must use ONLY the exact file uploaded by Admin > Add Product.
    // Never return remote/demo/fallback furniture images.
    $image = '';
    $storedImage = trim((string)($row['product_image'] ?? ''));
    if ($storedImage !== '') {
        // The admin currently stores a relative path such as
        // ../assests/images/stock/abc123.jpg. Only keep its filename.
        $imageName = basename(str_replace('\\', '/', $storedImage));
        $ext = strtolower(pathinfo($imageName, PATHINFO_EXTENSION));
        $allowed = ['jpg', 'jpeg', 'png', 'webp', 'gif'];
        $physicalImage = __DIR__ . '/../../assests/images/stock/' . $imageName;

        // Reject remote URLs or anything outside the admin upload folder.
        $isRemote = preg_match('/^(https?:)?\/\//i', $storedImage);
        if (!$isRemote && in_array($ext, $allowed, true) && is_file($physicalImage)) {
            $image = $store_url . 'assests/images/stock/' . rawurlencode($imageName);
        }
    }

    $products[] = [
        'id' => (int) $row['product_id'],
        'name' => $row['product_name'],
        'description' => $row['product_description'] ?? '',
        'image' => $image,
        'quantity' => (float) $row['quantity'],
        'price' => (float) $row['rate'],
        'active' => (int) $row['active'] === 1,
        'brandId' => (int) $row['brand_id'],
        'brand' => $row['brand_name'],
        'categoryId' => (int) $row['categories_id'],
        'category' => $row['categories_name']
    ];
}

$stmt->close();
$connect->close();

echo json_encode([
    'success' => true,
    'count' => count($products),
    'data' => $products
]);
