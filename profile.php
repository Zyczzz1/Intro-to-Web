<?php require_once 'profile_end.php'; 

// Badge Logic (Kept as requested)
$notif_count = 0;
if (file_exists('count_notif.php')) {
    include 'count_notif.php';
    $seen = isset($_SESSION['seen_count']) ? (int)$_SESSION['seen_count'] : 0;
    $notif_count = (isset($total_active_alerts) ? $total_active_alerts : 0) - $seen;
    if ($notif_count < 0) $notif_count = 0;
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>My Profile - Tuktukan Health Center</title>
    <link rel="stylesheet" href="dashboard_style.css">
    <link rel="stylesheet" href="inventory_style.css">
    <link rel="stylesheet" href="reports_style.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
    <style>
        .alert { padding: 15px; margin-bottom: 20px; border-radius: 4px; color: white; }
        .success { background-color: #28a745; }
        .error { background-color: #dc3545; }
        .warning { background-color: #ffc107; color: black; }
    </style>
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
                        <?php if ($notif_count > 0): ?><span class="notif-badge"><?php echo $notif_count; ?></span><?php endif; ?>
                    </a>
                </li>
                <li><a href="reports.php"><i class="fas fa-chart-bar"></i> Reports</a></li>
                <li class="active"><a href="profile.php"><i class="fas fa-user"></i> Profile</a></li>
            </ul>
        </aside>

        <div class="main-content">
            <header>
                <h2>My Profile</h2>
                <div class="user-profile" onclick="toggleDropdown()">
                    <div class="user-info">
                        <span class="user-name"><?php echo $my_profile['full_name']; ?></span>
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
                <?php if (isset($_GET['msg'])): ?>
                    <?php if ($_GET['msg'] == 'pass_updated'): ?>
                        <div class="alert success">✅ Password changed successfully! Check "My Activity".</div>
                    <?php elseif ($_GET['msg'] == 'details_updated'): ?>
                        <div class="alert success">✅ Profile details updated!</div>
                    <?php elseif ($_GET['msg'] == 'wrong_old'): ?>
                        <div class="alert error">❌ Current password was incorrect.</div>
                    <?php elseif ($_GET['msg'] == 'mismatch'): ?>
                        <div class="alert warning">⚠️ New passwords do not match.</div>
                    <?php elseif ($_GET['msg'] == 'error'): ?>
                        <div class="alert error">❌ A database error occurred.</div>
                    <?php endif; ?>
                <?php endif; ?>

                <div style="display: flex; gap: 30px; flex-wrap: wrap; margin-top: 20px;">
                    
                    <div class="card" style="flex: 1; min-width: 300px; text-align: center; height: fit-content;">
                        <div style="width: 100px; height: 100px; background: var(--main-maroon); color: white; font-size: 40px; display: flex; align-items: center; justify-content: center; border-radius: 50%; margin: 0 auto 20px auto;">
                            <?php echo $user_initial; ?>
                        </div>
                        <h3 style="margin-bottom: 5px;"><?php echo $my_profile['full_name']; ?></h3>
                        <p style="color: #666; margin-bottom: 5px;"><?php echo isset($my_profile['username']) ? '@'.$my_profile['username'] : $my_profile['email']; ?></p>
                        <p style="color: var(--main-maroon); font-weight:bold; font-size: 0.9em;">
                            <?php echo !empty($my_profile['position']) ? $my_profile['position'] : "Health Staff"; ?>
                        </p>
                        
                        <div style="text-align: left; border-top: 1px solid #eee; padding-top: 20px; margin-top: 20px;">
                            <p style="margin-bottom: 10px;"><strong>Role:</strong> <?php echo $user_role; ?></p>
                            <p style="margin-bottom: 10px;"><strong>Contact:</strong> <?php echo !empty($my_profile['contact_number']) ? $my_profile['contact_number'] : "Not set"; ?></p>
                            <p style="margin-bottom: 10px;"><strong>Joined:</strong> <?php echo date('F d, Y', strtotime($my_profile['created_at'])); ?></p>
                        </div>
                    </div>

                    <div style="flex: 2; min-width: 300px;">
                        
                        <div class="report-tabs">
                            <button class="tab-btn active" onclick="openTab('edit_details')">Edit Details</button>
                            <button class="tab-btn" onclick="openTab('security')">Security</button>
                            <button class="tab-btn" onclick="openTab('activity')">My Activity</button>
                        </div>

                        <div id="edit_details" class="report-section active">
                            <div class="card" style="text-align: left;">
                                <h3>Edit Personal Details</h3>
                                <form method="POST" action="profile.php">
                                    <input type="hidden" name="action" value="update_details">
                                    
                                    <div style="margin-bottom: 15px;">
                                        <label style="font-weight: bold;">Full Name</label>
                                        <input type="text" name="full_name" value="<?php echo $my_profile['full_name']; ?>" required style="width: 100%; padding: 10px; border: 1px solid #ddd; border-radius: 4px;">
                                    </div>
                                    <div style="margin-bottom: 15px;">
                                        <label style="font-weight: bold;">Contact Number</label>
                                        <input type="text" name="contact_number" value="<?php echo isset($my_profile['contact_number']) ? $my_profile['contact_number'] : ''; ?>" placeholder="0912 345 6789" style="width: 100%; padding: 10px; border: 1px solid #ddd; border-radius: 4px;">
                                    </div>
                                    <div style="margin-bottom: 20px;">
                                        <label style="font-weight: bold;">Position / Title</label>
                                        <input type="text" name="position" value="<?php echo isset($my_profile['position']) ? $my_profile['position'] : ''; ?>" placeholder="e.g. Midwife II" style="width: 100%; padding: 10px; border: 1px solid #ddd; border-radius: 4px;">
                                    </div>
                                    <button type="submit" class="btn-submit" style="background: var(--main-navy); color: white; border: none; padding: 12px 25px; border-radius: 4px; cursor: pointer;">Save Changes</button>
                                </form>
                            </div>
                        </div>

                        <div id="security" class="report-section">
                          <div class="card">
                            <h3>Change Password</h3>
                            <form method="POST" action="profile.php">
                                <input type="hidden" name="action" value="update_password">

                                <div class="form-group">
                                    <label>Current Password</label>
                                    <input type="password" name="current_password" required placeholder="Enter old password">
                                </div>
                                <div class="form-group">
                                    <label>New Password</label>
                                    <input type="password" name="new_password" required placeholder="Enter new password">
                                </div>
                                <div class="form-group">
                                    <label>Confirm New Password</label>
                                    <input type="password" name="confirm_password" required placeholder="Retype new password">
                                </div>
                                <button type="submit" class="btn-submit" style="background-color: #dc3545;">Update Password</button>
                            </form>
                            </div>
                        </div>

                        <div id="activity" class="report-section">
                            <div class="card" style="text-align: left;">
                                <h3>Activity History</h3>
                                <table class="inventory-table">
                                    <thead>
                                        <tr>
                                            <th>Date</th>
                                            <th>Action</th>
                                            <th>Type</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <?php if (empty($activity_log)): ?>
                                            <tr><td colspan="3" style="text-align: center; padding: 20px;">No activity recorded yet.</td></tr>
                                        <?php else: ?>
                                            <?php foreach ($activity_log as $log): ?>
                                            <tr>
                                                <td>
                                                    <?php echo date('M d, Y h:i A', strtotime($log['date'])); ?>
                                                </td>
                                                <td>
                                                    <strong><?php echo htmlspecialchars($log['title']); ?></strong><br>
                                                    <small style="color:#666;"><?php echo htmlspecialchars($log['message']); ?></small>
                                                </td>
                                                <td>
                                                    <?php 
                                                        $color = '#6c757d'; 
                                                        if ($log['type'] == 'warning') $color = '#ffc107'; 
                                                        if ($log['type'] == 'danger') $color = '#dc3545'; 
                                                        if ($log['type'] == 'success') $color = '#28a745'; 
                                                        if ($log['type'] == 'info') $color = '#17a2b8'; 
                                                    ?>
                                                    <span style="color: white; background-color: <?php echo $color; ?>; padding: 3px 8px; border-radius: 4px; font-size: 11px;">
                                                        <?php echo strtoupper($log['type']); ?>
                                                    </span>
                                                </td>
                                            </tr>
                                            <?php endforeach; ?>
                                        <?php endif; ?>
                                    </tbody>
                                </table>
                            </div>
                        </div>

                    </div>
                </div>
            </main>
        </div>
    </div>

    <script>
        function openTab(tabName) {
            var sections = document.getElementsByClassName("report-section");
            for (var i = 0; i < sections.length; i++) {
                sections[i].style.display = "none";
                sections[i].classList.remove("active");
            }
            var btns = document.getElementsByClassName("tab-btn");
            for (var i = 0; i < btns.length; i++) {
                btns[i].classList.remove("active");
            }
            document.getElementById(tabName).style.display = "block";
            event.currentTarget.classList.add("active");
        }
    </script>
    <script src="notifications_script.js"></script>
</body>
</html>