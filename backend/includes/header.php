<?php
require_once '../config/core.php';

$adminName = 'Administrator';
if (isset($_SESSION['userId'])) {
    $adminId = (int) $_SESSION['userId'];
    $adminQuery = $connect->query("SELECT username FROM users WHERE user_id = $adminId LIMIT 1");
    if ($adminQuery && $adminQuery->num_rows === 1) {
        $adminRow = $adminQuery->fetch_assoc();
        if (!empty($adminRow['username'])) {
            $adminName = $adminRow['username'];
        }
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>JABLE STORE | Admin</title>
    <link rel="icon" type="image/png" href="../../logo5.png">

    <link rel="stylesheet" href="../../assests/bootstrap/css/bootstrap.min.css">
    <link rel="stylesheet" href="../../assests/bootstrap/css/bootstrap-theme.min.css">
    <link rel="stylesheet" href="../../assests/font-awesome/css/font-awesome.min.css">
    <link rel="stylesheet" href="../../assests/plugins/datatables/jquery.dataTables.min.css">
    <link rel="stylesheet" href="../../assests/plugins/fileinput/css/fileinput.min.css">
    <link rel="stylesheet" href="../../assests/jquery-ui/jquery-ui.min.css">
    <link rel="stylesheet" href="../../custom/css/custom.css">

    <script src="../../assests/jquery/jquery.min.js"></script>
    <script src="../../assests/jquery-ui/jquery-ui.min.js"></script>
    <script src="../../assests/bootstrap/js/bootstrap.min.js"></script>
</head>
<body class="jable-admin-body">
<div class="admin-shell">
    <aside class="admin-sidebar" id="adminSidebar">
        <div class="sidebar-brand">
            <a href="dashboard.php" class="sidebar-brand-link">
                <img src="../../logo5.png" alt="JABLE STORE">
            </a>
            <div class="sidebar-brand-text">
                <strong>JABLE STORE</strong>
                <span>Inventory &amp; Sales Management</span>
            </div>
        </div>

        <nav class="sidebar-nav" aria-label="Admin navigation">
            <div class="sidebar-section-title">MAIN MENU</div>
            <ul>
                <li id="navDashboard"><a href="dashboard.php"><i class="fa fa-dashboard"></i><span>Dashboard</span></a></li>
                <?php if (isset($_SESSION['userId']) && $_SESSION['userId'] == 1) { ?>
                    <li id="navProduct"><a href="product.php"><i class="fa fa-cubes"></i><span>Products &amp; Stock</span></a></li>
                    <li id="navMaterial"><a href="materials.php"><i class="fa fa-tags"></i><span>Materials</span></a></li>
                    <li id="navCategories"><a href="categories.php"><i class="fa fa-th-large"></i><span>Categories</span></a></li>
                <?php } ?>
                <li id="navOrder" class="has-submenu">
                    <a href="orders.php?o=manord"><i class="fa fa-shopping-bag"></i><span>Orders</span><i class="fa fa-angle-down submenu-arrow"></i></a>
                    <ul class="sidebar-submenu">
                        <li id="topNavAddOrder"><a href="orders.php?o=add"><i class="fa fa-plus"></i><span>Add Order</span></a></li>
                        <li id="topNavManageOrder"><a href="orders.php?o=manord"><i class="fa fa-list"></i><span>Manage Orders</span></a></li>
                    </ul>
                </li>
                <?php if (isset($_SESSION['userId']) && $_SESSION['userId'] == 1) { ?>
                    <li id="navReport"><a href="report.php"><i class="fa fa-line-chart"></i><span>Reports</span></a></li>
                <?php } ?>
            </ul>

            <div class="sidebar-section-title">ACCOUNT</div>
            <ul>
                <?php if (isset($_SESSION['userId']) && $_SESSION['userId'] == 1) { ?>
                    <li id="navSetting"><a href="setting.php"><i class="fa fa-cog"></i><span>Settings</span></a></li>
                    <li id="topNavUser"><a href="user.php"><i class="fa fa-users"></i><span>Staff Users</span></a></li>
                <?php } ?>
                <li id="topNavLogout"><a href="logout.php"><i class="fa fa-sign-out"></i><span>Logout</span></a></li>
            </ul>
        </nav>

        <div class="sidebar-footer">
            <span class="sidebar-footer-dot"></span>
            <span>Admin account active</span>
        </div>
    </aside>

    <main class="admin-main" id="adminMain">
        <header class="admin-topbar">
            <div class="topbar-left">
                <button type="button" class="sidebar-toggle" id="sidebarToggle" aria-label="Toggle sidebar">
                    <i class="fa fa-bars"></i>
                </button>
                <div class="topbar-heading">
                    <strong>Admin Portal</strong>
                    <span>Manage your store in one place</span>
                </div>
            </div>
            <div class="topbar-right">
                <a href="../../frontend/store/index.php" class="view-store-link" target="_blank" rel="noopener">
                    <i class="fa fa-external-link"></i> View Store
                </a>
                <div class="admin-user">
                    <div class="admin-user-avatar"><i class="fa fa-user"></i></div>
                    <div>
                        <strong><?php echo htmlspecialchars($adminName, ENT_QUOTES, 'UTF-8'); ?></strong>
                        <span>Administrator</span>
                    </div>
                </div>
            </div>
        </header>

        <div class="admin-content">
