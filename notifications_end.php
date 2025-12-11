<?php
session_start();
require 'db_connect.php';

// 1. SECURITY CHECK
if (!isset($_SESSION['user_id'])) {
    header("Location: login.html");
    exit();
}

// 2. DEFINE USER VARIABLES
$user_id = $_SESSION['user_id'];
$user_name = $_SESSION['full_name'];
$user_role = strtoupper($_SESSION['role']);
$user_initial = strtoupper(substr($user_name, 0, 1));
$current_date = date('Y-m-d');

$alerts = []; 


// A. LOW STOCK ALERTS

$sql_low = "SELECT item_name, quantity FROM inventory WHERE quantity < 50";
$res_low = $conn->query($sql_low);
if ($res_low && $res_low->num_rows > 0) {
    while ($row = $res_low->fetch_assoc()) {
        $alerts[] = [
            'type' => 'warning', 
            'icon' => 'fa-exclamation-triangle',
            'title' => 'Low Stock Alert',
            'msg' => "<strong>{$row['item_name']}</strong> is running low ({$row['quantity']} remaining).",
            'date' => $current_date 
        ];
    }
}


// B. EXPIRING SOON 

$sql_soon = "SELECT item_name, expiry_date FROM inventory 
             WHERE expiry_date BETWEEN '$current_date' AND DATE_ADD('$current_date', INTERVAL 60 DAY)";
$res_soon = $conn->query($sql_soon);
if ($res_soon && $res_soon->num_rows > 0) {
    while ($row = $res_soon->fetch_assoc()) {
        $days_left = (strtotime($row['expiry_date']) - strtotime($current_date)) / (60 * 60 * 24);
        $alerts[] = [
            'type' => 'warning',
            'icon' => 'fa-clock',
            'title' => 'Expiring Soon',
            'msg' => "<strong>{$row['item_name']}</strong> expires in " . ceil($days_left) . " days.",
            'date' => $row['expiry_date']
        ];
    }
}


// C. ALREADY EXPIRED 

$sql_exp = "SELECT item_name, expiry_date FROM inventory WHERE expiry_date < '$current_date'";
$res_exp = $conn->query($sql_exp);
if ($res_exp && $res_exp->num_rows > 0) {
    while ($row = $res_exp->fetch_assoc()) {
        $alerts[] = [
            'type' => 'danger',
            'icon' => 'fa-times-circle',
            'title' => 'Expired Item',
            'msg' => "<strong>{$row['item_name']}</strong> expired on {$row['expiry_date']}. Please dispose.",
            'date' => $row['expiry_date']
        ];
    }
}


// D. PENDING REQUESTS (Admin Only)

if ($user_role === 'ADMIN') {
    $sql_req = "SELECT u.full_name, i.item_name, r.quantity, r.request_date 
                FROM stock_requests r 
                JOIN users u ON r.user_id = u.id 
                JOIN inventory i ON r.inventory_id = i.id 
                WHERE r.status = 'pending' 
                ORDER BY r.request_date DESC";
    
    $res_req = $conn->query($sql_req);
    if ($res_req && $res_req->num_rows > 0) {
        while ($row = $res_req->fetch_assoc()) {
            $alerts[] = [
                'type' => 'info',
                'icon' => 'fa-clipboard-list',
                'title' => 'New Stock Request',
                'msg' => "<strong>{$row['full_name']}</strong> requested <strong>{$row['quantity']} {$row['item_name']}</strong>.",
                'date' => $row['request_date']
            ];
        }
    }
}


// E. FULFILLED REQUESTS (User Only)

if ($user_role !== 'ADMIN') {
    $sql_done = "SELECT i.item_name, r.quantity, r.request_date 
                 FROM stock_requests r
                 JOIN inventory i ON r.inventory_id = i.id
                 WHERE r.user_id = '$user_id' 
                 AND r.status = 'completed' 
                 ORDER BY r.request_date DESC LIMIT 5";
                 
    $res_done = $conn->query($sql_done);
    if ($res_done && $res_done->num_rows > 0) {
        while ($row = $res_done->fetch_assoc()) {
            $alerts[] = [
                'type' => 'success',
                'icon' => 'fa-check-circle',
                'title' => 'Request Fulfilled',
                'msg' => "Your request for <strong>{$row['quantity']} {$row['item_name']}</strong> has been approved.",
                'date' => $row['request_date']
            ];
        }
    }
}


// F. NEW ITEMS ADDED 

// We try to run this. If the column 'date_added' is missing, we suppress the error with @
$sql_new = "SELECT item_name, date_added FROM inventory 
            WHERE date_added >= DATE_SUB(NOW(), INTERVAL 7 DAY) 
            ORDER BY date_added DESC";

$res_new = @$conn->query($sql_new); // The @ hides errors if column missing
if ($res_new && $res_new->num_rows > 0) {
    while ($row = $res_new->fetch_assoc()) {
        $alerts[] = [
            'type' => 'success',
            'icon' => 'fa-plus-circle',
            'title' => 'New Item Added',
            'msg' => "<strong>{$row['item_name']}</strong> is now available in inventory.",
            'date' => $row['date_added']
        ];
    }
}


// G. EDIT & DELETE HISTORY (Direct Query)

// This query runs directly. If it fails, we will know why.
$sql_logs = "SELECT * FROM notifications ORDER BY created_at DESC LIMIT 10";
$res_logs = $conn->query($sql_logs);

if ($res_logs && $res_logs->num_rows > 0) {
    while ($row = $res_logs->fetch_assoc()) {
        
        // Default icon
        $icon = 'fa-info-circle';
        
        // Custom icons based on type
        if ($row['type'] == 'warning') $icon = 'fa-edit'; 
        if ($row['type'] == 'danger') $icon = 'fa-trash-alt'; 
        if ($row['type'] == 'success') $icon = 'fa-check';

        $alerts[] = [
            'type' => $row['type'],
            'icon' => $icon,
            'title' => 'System Activity',
            'msg' => $row['message'],
            'date' => $row['created_at']
        ];
    }
}


// H. SORTING

usort($alerts, function($a, $b) {
    return strtotime($b['date']) - strtotime($a['date']);
});

// Format dates for display
foreach ($alerts as &$alert) {
    $alert['date'] = date('M d, h:i A', strtotime($alert['date']));
}
unset($alert); 
?>