<?php
// FILE: profile_end.php
session_start();
require 'db_connect.php';

// 1. Security Check
if (!isset($_SESSION['user_id'])) {
    header("Location: login.html");
    exit();
}

$user_id = $_SESSION['user_id'];
$user_name = $_SESSION['full_name']; 
$user_role = strtoupper($_SESSION['role']);

// --- AUTO-FIX: CREATE USER_LOGS TABLE IF MISSING ---
$conn->query("CREATE TABLE IF NOT EXISTS user_logs (
    id INT AUTO_INCREMENT PRIMARY KEY,
    user_id INT NOT NULL,
    action_title VARCHAR(50),
    action_desc VARCHAR(255),
    action_type VARCHAR(20), 
    created_at DATETIME DEFAULT CURRENT_TIMESTAMP
)");
// ---------------------------------------------------

// 2. HANDLE FORM SUBMISSIONS
if ($_SERVER["REQUEST_METHOD"] == "POST") {

    // --- A. UPDATE PERSONAL DETAILS ---
    if (isset($_POST['action']) && $_POST['action'] == 'update_details') {
        $full_name = $conn->real_escape_string($_POST['full_name']);
        $contact = $conn->real_escape_string($_POST['contact_number']);
        $position = $conn->real_escape_string($_POST['position']);
        
        $sql = "UPDATE users SET full_name='$full_name', contact_number='$contact', position='$position' WHERE id='$user_id'";
        
        if ($conn->query($sql)) {
            $_SESSION['full_name'] = $full_name;
            
            // LOG THE ACTION
            $conn->query("INSERT INTO user_logs (user_id, action_title, action_desc, action_type) 
                          VALUES ('$user_id', 'Profile Updated', 'Personal details updated.', 'info')");

            header("Location: profile.php?msg=details_updated");
            exit();
        } else {
            header("Location: profile.php?msg=error");
            exit();
        }
    }

    // --- B. UPDATE PASSWORD ---
    if (isset($_POST['action']) && $_POST['action'] == 'update_password') {
        $current_pass = $_POST['current_password'];
        $new_pass     = $_POST['new_password'];
        $confirm_pass = $_POST['confirm_password'];

        if ($new_pass !== $confirm_pass) {
            header("Location: profile.php?msg=mismatch");
            exit();
        }

        $sql_check = "SELECT * FROM users WHERE id = '$user_id'";
        $result_check = $conn->query($sql_check);
        $user_data = $result_check->fetch_assoc();

        if (password_verify($current_pass, trim($user_data['password']))) {
            $new_hashed = password_hash($new_pass, PASSWORD_DEFAULT);
            $sql_update = "UPDATE users SET password = '$new_hashed' WHERE id = '$user_id'";
            
            if ($conn->query($sql_update)) {
                // LOG THE ACTION
                $conn->query("INSERT INTO user_logs (user_id, action_title, action_desc, action_type) 
                              VALUES ('$user_id', 'Password Changed', 'Security password updated.', 'success')");

                header("Location: profile.php?msg=pass_updated");
                exit();
            } else {
                exit("Database Error: " . $conn->error);
            }
        } else {
            header("Location: profile.php?msg=wrong_old");
            exit();
        }
    }
}

// 3. FETCH FRESH USER DATA
$sql_user = "SELECT * FROM users WHERE id = '$user_id'";
$my_profile = $conn->query($sql_user)->fetch_assoc();
$user_initial = strtoupper(substr($my_profile['full_name'], 0, 1)); 

// 4. FETCH ACTIVITY LOGS (MERGING DATA SOURCES)
$activity_log = [];

// Source 1: Stock Requests (For Staff)
if ($user_role !== 'ADMIN') {
    if ($conn->query("SHOW TABLES LIKE 'stock_requests'")->num_rows > 0) {
        $sql_req = "SELECT r.request_date as date, 
                           i.item_name as title, 
                           CONCAT('Requested ', r.quantity, ' units') as message,
                           CASE WHEN r.status = 'pending' THEN 'warning' ELSE 'success' END as type
                    FROM stock_requests r
                    JOIN inventory i ON r.inventory_id = i.id
                    WHERE r.user_id = '$user_id'";
        $res_req = $conn->query($sql_req);
        if ($res_req) { while($row = $res_req->fetch_assoc()) { $activity_log[] = $row; } }
    }
}

// Source 2: User Logs (Profile Actions)
$sql_logs = "SELECT created_at as date, action_title as title, action_desc as message, action_type as type 
             FROM user_logs WHERE user_id = '$user_id'";
$res_logs = $conn->query($sql_logs);
if ($res_logs) { while($row = $res_logs->fetch_assoc()) { $activity_log[] = $row; } }


// Source 3: System Notifications (Admin Only) - FIXED SECTION
if ($user_role === 'ADMIN') {
     if ($conn->query("SHOW TABLES LIKE 'notifications'")->num_rows > 0) {
        
        // --- FIX: Use 'System Activity' as a hardcoded title since the DB column doesn't exist ---
        $sql_sys = "SELECT created_at as date, 'System Activity' as title, message, type 
                    FROM notifications 
                    WHERE type != 'warning' 
                    ORDER BY created_at DESC LIMIT 10"; 
        
        $res_sys = $conn->query($sql_sys);
        
        if ($res_sys) { 
            while($row = $res_sys->fetch_assoc()) { 
                $activity_log[] = $row; 
            } 
        }
     }
}

// Sort by Date (Newest First)
usort($activity_log, function($a, $b) {
    return strtotime($b['date']) - strtotime($a['date']);
});

$activity_log = array_slice($activity_log, 0, 15);
?>