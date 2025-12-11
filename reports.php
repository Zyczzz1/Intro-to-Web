<?php
// Just one line to load everything!
require_once 'reports_end.php';
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Reports - Tuktukan Health Center</title>
    <link rel="stylesheet" href="dashboard_style.css">
    <link rel="stylesheet" href="reports_style.css">
    <link rel="stylesheet" href="inventory_style.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
</head>
<body>
    <div class="dashboard-container">
        <aside class="sidebar">
            <div class="sidebar-header">
                <img src="Tuktukan_Logo.png" alt="Logo" class="sidebar-logo">
                <h3>Barangay Tuktukan Inventory System</h3>
            </div>
            <ul class="sidebar-menu">
                <li><a href="dashboard.php"><i class="fas fa-home"></i> Dashboard</a></li>
                <li><a href="inventory.php"><i class="fas fa-box"></i> Inventory</a></li>
                <li>
                    <a href="notifications.php" style="display: flex; align-items: center; justify-content: space-between;">
                        <div><i class="fas fa-bell"></i> Notifications</div>
                        <?php if (isset($notif_count) && $notif_count > 0): ?>
                            <span class="notif-badge"><?php echo $notif_count; ?></span>
                        <?php endif; ?>
                    </a>
                </li>
                <li class="active"><a href="reports.php"><i class="fas fa-chart-bar"></i> Reports</a></li>
                <li><a href="profile.php"><i class="fas fa-user"></i> Profile</a></li>
            </ul>
        </aside>

        <div class="main-content">
            <header>
                <h2>Reports & Analytics</h2>
                <div class="user-profile" onclick="toggleDropdown()">
                    <div class="user-info">
                        <span class="user-name"><?php echo $user_name; ?></span>
                        <span class="user-role"><?php echo $user_role; ?></span>
                    </div>
                    <div class="profile-icon"><?php echo $user_initial; ?></div>
                    <div id="userDropdown" class="dropdown-menu">
                        <a href="profile.php"><i class="fas fa-user"></i> My Profile</a>
                        <a href="logout.php" class="logout-btn"><i class="fas fa-sign-out-alt"></i> Log Out</a>
                    </div>
                </div>
            </header>

            <main>
                <div class="report-tabs">
                    <button class="tab-btn active" onclick="openReport('stock')">Stock Report</button>
                    <button class="tab-btn" onclick="openReport('expired')">Expired Medicines</button>
                    <button class="tab-btn" onclick="openReport('dispensing')">Dispensing Summary</button>
                    <button class="tab-btn" onclick="openReport('monthly')">Monthly Usage</button>
                    <button class="tab-btn" onclick="openReport('annual')">Annual Summary</button>
                </div>

                <div id="stock" class="report-section active">
                    <div class="card" style="text-align:left;">
                        <h3>Current Stock Inventory <button class="btn-print" onclick="window.print()"><i class="fas fa-print"></i> Print</button></h3>
                        <table class="inventory-table">
                            <thead>
                                <tr><th>Item Name</th><th>Category</th><th>Quantity</th><th>Status</th></tr>
                            </thead>
                            <tbody>
                                <?php foreach ($stock_list as $row): ?>
                                <tr style="<?php echo ($row['quantity']<50)?'background:#fff3cd':''; ?>">
                                    <td><?php echo $row['item_name']; ?></td>
                                    <td><?php echo $row['category']; ?></td>
                                    <td><?php echo $row['quantity']; ?></td>
                                    <td><?php echo $row['status']; ?></td>
                                </tr>
                                <?php endforeach; ?>
                            </tbody>
                        </table>
                    </div>
                </div>

                <div id="expired" class="report-section">
                    <div class="card" style="text-align:left;">
                        <h3 style="color:#dc3545;">Expired Medicines Report <button class="btn-print" onclick="window.print()"><i class="fas fa-print"></i> Print</button></h3>
                        <table class="inventory-table">
                            <thead>
                                <tr><th>Item Name</th><th>Category</th><th>Expiry Date</th><th>Qty to Dispose</th></tr>
                            </thead>
                            <tbody>
                                <?php if (empty($expired_list)): ?>
                                    <tr><td colspan="4" style="text-align:center;">Good news! No expired items found.</td></tr>
                                <?php else: ?>
                                    <?php foreach ($expired_list as $row): ?>
                                    <tr>
                                        <td><?php echo $row['item_name']; ?></td>
                                        <td><?php echo $row['category']; ?></td>
                                        <td style="color:red; font-weight:bold;"><?php echo $row['expiry_date']; ?></td>
                                        <td><?php echo $row['quantity']; ?></td>
                                    </tr>
                                    <?php endforeach; ?>
                                <?php endif; ?>
                            </tbody>
                        </table>
                    </div>
                </div>

                <div id="dispensing" class="report-section">
                    <div class="card" style="text-align:left;">
                        <h3>Dispensing History (All Time) <button class="btn-print" onclick="window.print()"><i class="fas fa-print"></i> Print</button></h3>
                        <table class="inventory-table">
                            <thead>
                                <tr><th>Date</th><th>Recipient</th><th>Item</th><th>Qty</th></tr>
                            </thead>
                            <tbody>
                                <?php foreach ($dispense_list as $row): ?>
                                <tr>
                                    <td><?php echo date('M d, Y', strtotime($row['request_date'])); ?></td>
                                    <td><?php echo $row['full_name']; ?></td>
                                    <td><?php echo $row['item_name']; ?></td>
                                    <td><?php echo $row['quantity']; ?></td>
                                </tr>
                                <?php endforeach; ?>
                            </tbody>
                        </table>
                    </div>
                </div>

                <div id="monthly" class="report-section">
                    <div class="card" style="text-align:left;">
                        <h3>Usage for <?php echo $current_month; ?> <button class="btn-print" onclick="window.print()"><i class="fas fa-print"></i> Print</button></h3>
                        <table class="inventory-table">
                            <thead>
                                <tr><th>Item Name</th><th>Total Quantity Used</th></tr>
                            </thead>
                            <tbody>
                                <?php if (empty($monthly_list)): ?>
                                    <tr><td colspan="2" style="text-align:center;">No items dispensed this month yet.</td></tr>
                                <?php else: ?>
                                    <?php foreach ($monthly_list as $row): ?>
                                    <tr>
                                        <td><?php echo $row['item_name']; ?></td>
                                        <td style="font-weight:bold;"><?php echo $row['total_qty']; ?></td>
                                    </tr>
                                    <?php endforeach; ?>
                                <?php endif; ?>
                            </tbody>
                        </table>
                    </div>
                </div>

                <div id="annual" class="report-section">
                    <div class="card" style="text-align:left;">
                        <h3>Annual Summary (<?php echo $current_year; ?>) <button class="btn-print" onclick="window.print()"><i class="fas fa-print"></i> Print</button></h3>
                        <table class="inventory-table">
                            <thead>
                                <tr><th>Item Name</th><th>Total Quantity Used (Year)</th></tr>
                            </thead>
                            <tbody>
                                <?php if (empty($annual_list)): ?>
                                    <tr><td colspan="2" style="text-align:center;">No data for this year yet.</td></tr>
                                <?php else: ?>
                                    <?php foreach ($annual_list as $row): ?>
                                    <tr>
                                        <td><?php echo $row['item_name']; ?></td>
                                        <td style="font-weight:bold; font-size:1.1em; color:#2C3E50;"><?php echo $row['total_qty']; ?></td>
                                    </tr>
                                    <?php endforeach; ?>
                                <?php endif; ?>
                            </tbody>
                        </table>
                    </div>
                </div>

            </main>
        </div>
    </div>

    <script>
        function openReport(reportName) {
            var sections = document.getElementsByClassName("report-section");
            for (var i = 0; i < sections.length; i++) {
                sections[i].style.display = "none";
                sections[i].classList.remove("active");
            }
            var btns = document.getElementsByClassName("tab-btn");
            for (var i = 0; i < btns.length; i++) {
                btns[i].classList.remove("active");
            }
            document.getElementById(reportName).style.display = "block";
            event.currentTarget.classList.add("active");
        }
    </script>
    <script src="notifications_script.js"></script>
</body>
</html>