<?php
// 1. START SESSION
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

// 2. DB CONNECTION
require_once 'db_connect.php'; // (Or require_once 'dashboard_end.php' if that file connects to DB)

// 3. ADD THIS BLOCK TO EVERY FILE!
if (file_exists('count_notif.php')) {
    include 'count_notif.php';
} else {
    $notif_count = 0;
}

// 4. RUN INVENTORY LOGIC (Your main page logic)
require_once 'dashboard_end.php'; 
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Dashboard - Tuktukan Health Center</title>
    <link rel="stylesheet" href="dashboard_style.css">
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
                <li class="active">
                    <a href="dashboard.php"><i class="fas fa-home"></i> Dashboard</a>
                </li>
                
                <li><a href="inventory.php"><i class="fas fa-box"></i> Inventory</a></li>
                <li>
                    <a href="notifications.php">
                        <div><i class="fas fa-bell"></i> Notifications</div>
                        
                            <?php if (isset($count_notif) && $count_notif > 0): ?>
                                <span class="notif-badge"><?php echo $count_notif; ?></span>
                            <?php endif; ?>
                    </a>
                </li>              
                <li><a href="reports.php"><i class="fas fa-chart-bar"></i> Reports</a></li>                
                <li><a href="profile.php"><i class="fas fa-user"></i> Profile</a></li>
            </ul>
        </aside>

        <div class="main-content">
            <header>
                <h2>Dashboard</h2>
                
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
                <?php echo isset($alert_message) ? $alert_message : ''; ?>
                <div class="cards">
                    <div class="card">
                        <h1><?php echo number_format($total_items); ?></h1>
                        <span>Total Stock</span>
                    </div>
                    <div class="card" style="border-top-color: #ffc107;">
                        <h1 style="color: #ffc107;"><?php echo $low_stock; ?></h1>
                        <span>Low Stock</span>
                    </div>
                    <div class="card" style="border-top-color: #fd7e14;">
                        <h1 style="color: #fd7e14;"><?php echo $expiring_soon; ?></h1>
                        <span>Expiring Soon</span>
                    </div>
                    <div class="card" style="border-top-color: #dc3545;">
                        <h1 style="color: #dc3545;"><?php echo $expired_items; ?></h1>
                        <span>Expired</span>
                    </div>
                </div>

                <div class="dashboard-grid" style="display: grid; grid-template-columns: 2fr 1fr; gap: 20px; margin-top: 20px;">
                    
                    <div class="recent-table" style="background: white; padding: 20px; border-radius: 8px; box-shadow: 0 2px 5px rgba(0,0,0,0.1);">
                        <div style="display: flex; justify-content: space-between; align-items: center;">
                            <h3><?php echo $table_title; ?></h3>
                        </div>

                        <table style="width: 100%; border-collapse: collapse; margin-top: 15px;">
                            <thead>
                                <tr style="text-align: left; border-bottom: 2px solid #eee; color: #666;">
                                    <?php if ($user_role === 'ADMIN'): ?>
                                        <th style="padding: 10px;">Item Needed</th>
                                        <th>Requested By</th>
                                        <th>Date</th>
                                        <th>Action</th>
                                    <?php else: ?>
                                        <th style="padding: 10px;">Item Name</th>
                                        <th>Category</th>
                                        <th>Remaining</th>
                                        <th>Status</th>
                                    <?php endif; ?>
                                </tr>
                            </thead>
                            <tbody>
                                <?php if ($result_table && $result_table->num_rows > 0): ?>
                                    <?php while($row = $result_table->fetch_assoc()): ?>
                                    <tr style="border-bottom: 1px solid #eee;">
                                        
                                        <?php if ($user_role === 'ADMIN'): ?>
                                            <td style="padding: 10px; font-weight: bold;"><?php echo $row['item_name']; ?></td>
                                            <td><?php echo $row['full_name']; ?></td>
                                            <td style="font-size: 12px; color: #888;"><?php echo date('M d', strtotime($row['request_date'])); ?></td>
                                            
                                            <td>
                                                <form method="POST" action="inventory_end.php" style="margin:0;">
                                                    <input type="hidden" name="request_id" value="<?php echo $row['id']; ?>">
                                                    <input type="hidden" name="inventory_id" value="<?php echo isset($row['inventory_id']) ? $row['inventory_id'] : ''; ?>">
                                                    <input type="hidden" name="quantity_to_add" value="<?php echo isset($row['quantity']) ? $row['quantity'] : ''; ?>">
                                                    
                                                    <input type="hidden" name="action" value="fulfill_request">
                                                    
                                                    <button type="submit" style="background: #28a745; color: white; border: none; padding: 5px 10px; border-radius: 4px; cursor: pointer; font-size: 11px;" title="Add Stock & Complete Request">
                                                        <i class="fas fa-check"></i> Fulfill
                                                    </button>
                                                </form>
                                            </td>
                                            
                                        <?php else: ?>
                                            <td style="padding: 10px; font-weight: bold;"><?php echo $row['item_name']; ?></td>
                                            <td><?php echo $row['category']; ?></td>
                                            <td style="color: #d9534f; font-weight: bold;"><?php echo $row['quantity']; ?></td>
                                            <td><span style="background: #f8d7da; color: #721c24; padding: 2px 6px; border-radius: 4px; font-size: 11px;">Low</span></td>
                                        <?php endif; ?>

                                    </tr>
                                    <?php endwhile; ?>
                                <?php else: ?>
                                    <tr><td colspan="4" style="padding: 20px; text-align: center; color: #999;">No alerts to display.</td></tr>
                                <?php endif; ?>
                            </tbody>
                        </table>
                    </div>

                    <div class="quick-actions" style="background: white; padding: 20px; border-radius: 8px; box-shadow: 0 2px 5px rgba(0,0,0,0.1); height: fit-content;">
                        <h3>Quick Actions</h3>
                        
                        <?php if ($user_role === 'ADMIN'): ?>
                            <button onclick="window.location.href='inventory.php'" style="width: 100%; padding: 15px; margin-top: 10px; background: #28a745; color: white; border: none; border-radius: 5px; cursor: pointer;">
                                <i class="fas fa-plus-circle"></i> Add Stock
                            </button>
                        <?php else: ?>
                            <button onclick="window.location.href='inventory.php'" style="width: 100%; padding: 15px; margin-top: 10px; background: #17a2b8; color: white; border: none; border-radius: 5px; cursor: pointer;">
                                <i class="fas fa-hand-holding-medical"></i> Dispense Medicine
                            </button>
                        <?php endif; ?>
                    </div>
                </div>
            </main>
        </div>
    </div>
    
    <script src="dashboard_script.js"></script>
</body>