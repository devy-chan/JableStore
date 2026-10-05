<?php require_once '../includes/header.php'; ?>
<?php

function dashboard_value($connect, $sql, $field = null) {
    $query = $connect->query($sql);
    if (!$query) return 0;
    $row = $query->fetch_assoc();
    if ($field !== null && isset($row[$field])) return $row[$field];
    return $row ? reset($row) : 0;
}

$isAdmin = isset($_SESSION['userId']) && (int)$_SESSION['userId'] === 1;

$activeProducts = dashboard_value($connect, "SELECT COUNT(*) AS total FROM product WHERE status = 1 AND active = 1", 'total');
$totalProducts = dashboard_value($connect, "SELECT COUNT(*) AS total FROM product WHERE status = 1", 'total');
$completedOrders = dashboard_value($connect, "SELECT COUNT(*) AS total FROM orders WHERE order_status = 1", 'total');
$lowStock = dashboard_value($connect, "SELECT COUNT(*) AS total FROM product WHERE status = 1 AND quantity > 0 AND quantity <= 5", 'total');
$outOfStock = dashboard_value($connect, "SELECT COUNT(*) AS total FROM product WHERE status = 1 AND quantity <= 0", 'total');

$salesToday = dashboard_value($connect, "SELECT COALESCE(SUM(CAST(grand_total AS DECIMAL(15,2))),0) AS total FROM orders WHERE order_status = 1 AND order_date = CURDATE()", 'total');
$salesMonth = dashboard_value($connect, "SELECT COALESCE(SUM(CAST(grand_total AS DECIMAL(15,2))),0) AS total FROM orders WHERE order_status = 1 AND YEAR(order_date)=YEAR(CURDATE()) AND MONTH(order_date)=MONTH(CURDATE())", 'total');
$totalPaid = dashboard_value($connect, "SELECT COALESCE(SUM(CAST(paid AS DECIMAL(15,2))),0) AS total FROM orders WHERE order_status = 1", 'total');
$totalDue = dashboard_value($connect, "SELECT COALESCE(SUM(CAST(due AS DECIMAL(15,2))),0) AS total FROM orders WHERE order_status = 1", 'total');
$inventoryValue = dashboard_value($connect, "SELECT COALESCE(SUM(CAST(quantity AS DECIMAL(15,2))*CAST(rate AS DECIMAL(15,2))),0) AS total FROM product WHERE status = 1", 'total');

$chartLabels = [];
$chartValues = [];
$year = (int)date('Y');
$chartQuery = $connect->query("SELECT MONTH(order_date) AS month_no, COALESCE(SUM(CAST(grand_total AS DECIMAL(15,2))),0) AS total FROM orders WHERE order_status=1 AND YEAR(order_date)=$year GROUP BY MONTH(order_date)");
$chartMap = [];
if ($chartQuery) {
    while ($row = $chartQuery->fetch_assoc()) {
        $chartMap[(int)$row['month_no']] = (float)$row['total'];
    }
}
for ($m = 1; $m <= 12; $m++) {
    $chartLabels[] = date('M', mktime(0, 0, 0, $m, 1));
    $chartValues[] = isset($chartMap[$m]) ? $chartMap[$m] : 0;
}

$topProducts = [];
$topProductQuery = $connect->query("SELECT p.product_name, SUM(CAST(oi.quantity AS DECIMAL(15,2))) AS units_sold, SUM(CAST(oi.total AS DECIMAL(15,2))) AS sales_total FROM order_item oi INNER JOIN orders o ON oi.order_id=o.order_id INNER JOIN product p ON oi.product_id=p.product_id WHERE o.order_status=1 GROUP BY oi.product_id ORDER BY units_sold DESC LIMIT 5");
if ($topProductQuery) {
    while ($row = $topProductQuery->fetch_assoc()) $topProducts[] = $row;
}

$recentOrders = [];
$recentOrderQuery = $connect->query("SELECT order_id, client_name, order_date, grand_total, payment_status FROM orders WHERE order_status=1 ORDER BY order_id DESC LIMIT 5");
if ($recentOrderQuery) {
    while ($row = $recentOrderQuery->fetch_assoc()) $recentOrders[] = $row;
}

function peso($value) {
    return '₱' . number_format((float)$value, 2);
}
?>

<div class="dashboard-hero">
    <div>
        <div class="dashboard-kicker">Business Overview</div>
        <h1 class="dashboard-title">Good day, Admin <span aria-hidden="true">👋</span></h1>
        <p class="dashboard-subtitle">Here is what is happening with your store today.</p>
    </div>
    <div class="dashboard-date"><i class="fa fa-calendar"></i> <?php echo date('l, F d, Y'); ?></div>
</div>

<div class="quick-actions">
    <a class="quick-action" href="orders.php?o=add"><span class="quick-action-icon"><i class="fa fa-plus"></i></span><span><strong>New Order</strong><span>Create a customer order</span></span></a>
    <?php if ($isAdmin) { ?><a class="quick-action" href="product.php"><span class="quick-action-icon"><i class="fa fa-cubes"></i></span><span><strong>Products &amp; Stock</strong><span>Add or update inventory</span></span></a><?php } ?>
    <a class="quick-action" href="orders.php?o=manord"><span class="quick-action-icon"><i class="fa fa-shopping-bag"></i></span><span><strong>Manage Orders</strong><span>Review completed orders</span></span></a>
    <?php if ($isAdmin) { ?><a class="quick-action" href="report.php"><span class="quick-action-icon"><i class="fa fa-bar-chart"></i></span><span><strong>Business Reports</strong><span>Review sales information</span></span></a><?php } ?>
</div>

<div class="metric-grid">
    <div class="metric-card"><div class="metric-card-top"><span class="metric-label">Active Products</span><span class="metric-icon"><i class="fa fa-cubes"></i></span></div><div class="metric-value"><?php echo number_format((int)$activeProducts); ?></div><div class="metric-note"><?php echo number_format((int)$totalProducts); ?> total active records</div></div>
    <div class="metric-card"><div class="metric-card-top"><span class="metric-label">Completed Orders</span><span class="metric-icon"><i class="fa fa-shopping-bag"></i></span></div><div class="metric-value"><?php echo number_format((int)$completedOrders); ?></div><div class="metric-note">Successfully recorded orders</div></div>
    <div class="metric-card"><div class="metric-card-top"><span class="metric-label">Sales This Month</span><span class="metric-icon"><i class="fa fa-money"></i></span></div><div class="metric-value"><?php echo peso($salesMonth); ?></div><div class="metric-note">Today: <?php echo peso($salesToday); ?></div></div>
    <div class="metric-card"><div class="metric-card-top"><span class="metric-label">Inventory Value</span><span class="metric-icon"><i class="fa fa-archive"></i></span></div><div class="metric-value"><?php echo peso($inventoryValue); ?></div><div class="metric-note">Based on quantity × selling rate</div></div>
</div>

<div class="dashboard-grid">
    <section class="dashboard-panel">
        <div class="dashboard-panel-header"><h3><i class="fa fa-line-chart"></i> Sales Overview</h3><span><?php echo $year; ?></span></div>
        <div class="dashboard-panel-body"><div style="height:310px;position:relative"><canvas id="salesChart"></canvas></div></div>
    </section>

    <section class="dashboard-panel">
        <div class="dashboard-panel-header"><h3><i class="fa fa-bell-o"></i> Stock &amp; Payment Alerts</h3><span>Current</span></div>
        <div class="dashboard-panel-body">
            <div class="business-alerts">
                <div class="business-alert"><div class="left"><i class="fa fa-exclamation-triangle"></i><div><strong>Low Stock</strong><span>Products with 1–5 units</span></div></div><div class="count"><?php echo number_format((int)$lowStock); ?></div></div>
                <div class="business-alert"><div class="left"><i class="fa fa-times-circle"></i><div><strong>Out of Stock</strong><span>Products needing restock</span></div></div><div class="count"><?php echo number_format((int)$outOfStock); ?></div></div>
                <div class="business-alert"><div class="left"><i class="fa fa-credit-card"></i><div><strong>Outstanding Payments</strong><span>Recorded unpaid balance</span></div></div><div class="count"><?php echo peso($totalDue); ?></div></div>
                <div class="business-alert"><div class="left"><i class="fa fa-check-circle"></i><div><strong>Total Paid</strong><span>Payments from completed orders</span></div></div><div class="count"><?php echo peso($totalPaid); ?></div></div>
            </div>
        </div>
    </section>
</div>

<div class="dashboard-grid">
    <section class="dashboard-panel">
        <div class="dashboard-panel-header"><h3><i class="fa fa-star-o"></i> Top Products</h3><span>By units sold</span></div>
        <div class="dashboard-panel-body" style="padding:0">
            <div class="table-responsive" style="margin:0;border:0">
                <table class="table table-hover" style="margin:0;box-shadow:none;border-radius:0">
                    <thead><tr><th>Product</th><th>Units Sold</th><th>Sales</th></tr></thead>
                    <tbody>
                    <?php if (!$topProducts) { ?><tr><td colspan="3" class="text-center">No completed product sales yet.</td></tr><?php } ?>
                    <?php foreach ($topProducts as $product) { ?><tr><td><?php echo htmlspecialchars($product['product_name'], ENT_QUOTES, 'UTF-8'); ?></td><td><?php echo number_format((float)$product['units_sold'], 0); ?></td><td><?php echo peso($product['sales_total']); ?></td></tr><?php } ?>
                    </tbody>
                </table>
            </div>
        </div>
    </section>

    <section class="dashboard-panel">
        <div class="dashboard-panel-header"><h3><i class="fa fa-clock-o"></i> Recent Orders</h3><span>Latest 5</span></div>
        <div class="dashboard-panel-body" style="padding:0">
            <div class="table-responsive" style="margin:0;border:0">
                <table class="table table-hover" style="margin:0;box-shadow:none;border-radius:0">
                    <thead><tr><th>Order</th><th>Customer</th><th>Total</th></tr></thead>
                    <tbody>
                    <?php if (!$recentOrders) { ?><tr><td colspan="3" class="text-center">No completed orders yet.</td></tr><?php } ?>
                    <?php foreach ($recentOrders as $order) { ?><tr><td>#<?php echo (int)$order['order_id']; ?></td><td><?php echo htmlspecialchars($order['client_name'], ENT_QUOTES, 'UTF-8'); ?></td><td><?php echo peso($order['grand_total']); ?></td></tr><?php } ?>
                    </tbody>
                </table>
            </div>
        </div>
    </section>
</div>

<div class="dashboard-panel">
    <div class="dashboard-panel-header"><h3><i class="fa fa-info-circle"></i> Business Snapshot</h3><span>JABLE STORE</span></div>
    <div class="dashboard-panel-body">
        <p style="margin:0;color:#71685f;font-size:12px;line-height:1.7">The dashboard summarizes products, stock levels, orders, sales, payments, and inventory value. <strong>True profit is not displayed yet</strong> because the current product database stores the selling rate but does not store the product cost. Once a cost price field is added, the system can calculate gross profit and profit margin accurately.</p>
    </div>
</div>

<script>
$(function () {
    $('#navDashboard').addClass('active');

    var labels = <?php echo json_encode($chartLabels); ?>;
    var values = <?php echo json_encode($chartValues); ?>;
    var canvas = document.getElementById('salesChart');
    if (!canvas) return;
    var ctx = canvas.getContext('2d');

    function drawChart() {
        var rect = canvas.getBoundingClientRect();
        var width = Math.max(rect.width, 280);
        var height = Math.max(rect.height, 250);
        var dpr = window.devicePixelRatio || 1;
        canvas.width = width * dpr;
        canvas.height = height * dpr;
        ctx.setTransform(dpr, 0, 0, dpr, 0, 0);
        ctx.clearRect(0, 0, width, height);

        var pad = {left: 62, right: 18, top: 20, bottom: 42};
        var innerW = width - pad.left - pad.right;
        var innerH = height - pad.top - pad.bottom;
        var max = Math.max.apply(null, values.concat([1]));
        var step = innerW / labels.length;
        var bar = Math.max(8, step * .56);

        ctx.font = '11px Arial';
        ctx.textAlign = 'center';
        for (var i = 0; i < labels.length; i++) {
            var x = pad.left + step * i + step / 2;
            var value = Number(values[i]) || 0;
            var barHeight = (value / max) * innerH;
            var y = pad.top + innerH - barHeight;
            ctx.fillStyle = '#7b6247';
            ctx.fillRect(x - bar / 2, y, bar, barHeight);
            ctx.fillStyle = '#8b8278';
            ctx.fillText(labels[i], x, height - 15);
        }

        ctx.textAlign = 'right';
        for (var g = 0; g <= 4; g++) {
            var gy = pad.top + innerH - (innerH * g / 4);
            ctx.fillStyle = '#eee7de';
            ctx.fillRect(pad.left, gy, innerW, 1);
            ctx.fillStyle = '#8b8278';
            ctx.fillText(Math.round(max * g / 4).toLocaleString(), pad.left - 8, gy + 4);
        }
    }

    window.addEventListener('resize', drawChart);
    drawChart();
});
</script>

<?php require_once '../includes/footer.php'; ?>
