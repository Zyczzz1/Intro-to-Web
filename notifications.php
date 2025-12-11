<?php require_once 'notifications_end.php'; ?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Notifications - Tuktukan Health Center</title>
    <link rel="stylesheet" href="dashboard_style.css">
    <link rel="stylesheet" href="notifications_style.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
    
</head>
<body>
    <div class="dashboard-container">
        
        <!-- SIDEBAR -->
        <aside class="sidebar">
            <div class="sidebar-header">
                <img src="Tuktukan_Logo.png" alt="Logo" class="sidebar-logo">
                <h3>Barangay Tuktukan Inventory System</h3>
            </div>
            <ul class="sidebar-menu">
                <li><a href="dashboard.php"><i class="fas fa-home"></i> Dashboard</a></li>
                <li><a href="inventory.php"><i class="fas fa-box"></i> Inventory</a></li>
                <li class="active"><a href="notifications.php"><i class="fas fa-bell"></i> Notifications</a></li>
                <li><a href="reports.php"><i class="fas fa-chart-bar"></i> Reports</a></li>
                <li><a href="profile.php"><i class="fas fa-user"></i> Profile</a></li>
            </ul>
        </aside>

        <!-- MAIN CONTENT -->
        <div class="main-content">
            <header>
                <h2>Notifications Center</h2>
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
                <div class="notif-container">
                    <h3 class="section-header">Recent Updates & Alerts</h3>

                    <?php if (!empty($alerts)): ?>
                        <div class="notif-table-header">
                            <div class="header-item">Type/Description</div>
                            <div class="header-item date">Date</div>
                        </div>
                        <?php foreach ($alerts as $alert): ?>
                            <div class="notif-card notif-<?php echo $alert['type']; ?>">
                                <div class="notif-icon"><i class="fas <?php echo $alert['icon']; ?>"></i></div>
                                <div class="notif-content">
                                    <h4><?php echo $alert['title']; ?></h4>
                                    <p><?php echo $alert['msg']; ?></p>
                                </div>
                                <span class="notif-date"><?php echo $alert['date']; ?></span>
                            </div>
                        <?php endforeach; ?>
                    <?php else: ?>
                        <div class="empty-state">
                            <i class="fas fa-bell-slash" style="font-size: 40px; margin-bottom: 15px; display:block;"></i>
                            <p>No new notifications at this time.</p>
                        </div>
                    <?php endif; ?>
                </div>
            </main>
        </div>
    </div>

    <script src="notifications_script.js">
        
    </script>
</body>
</html>