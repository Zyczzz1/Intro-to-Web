<?php
// reports_end.php

session_start();
require 'db_connect.php';

// Security Check
if (!isset($_SESSION['user_id'])) {
    header("Location: login.html");
    exit();
}

$user_name = $_SESSION['full_name'];
$user_role = strtoupper($_SESSION['role']);
$user_initial = strtoupper(substr($user_name, 0, 1));
$current_month = date('F Y'); 
$current_year = date('Y');

// --- 1. STOCK REPORT ---
$stock_list = [];
$sql_stock = "SELECT * FROM inventory ORDER BY category, item_name ASC";
$res_stock = $conn->query($sql_stock);
if ($res_stock) {
    while ($row = $res_stock->fetch_assoc()) {
        $status = 'Good';
        if ($row['quantity'] < 50) $status = 'Low Stock';
        if ($row['quantity'] == 0) $status = 'Out of Stock';
        $row['status'] = $status;
        $stock_list[] = $row;
    }
}

// --- 2. EXPIRED MEDICINES ---
$expired_list = [];
$sql_exp = "SELECT * FROM inventory WHERE expiry_date < CURDATE() ORDER BY expiry_date ASC";
$res_exp = $conn->query($sql_exp);
if ($res_exp) {
    while ($row = $res_exp->fetch_assoc()) {
        $expired_list[] = $row;
    }
}

// --- 3. DISPENSING SUMMARY ---
$dispense_list = [];
$sql_disp = "SELECT r.request_date, i.item_name, i.category, r.quantity, u.full_name 
             FROM stock_requests r 
             JOIN inventory i ON r.inventory_id = i.id
             JOIN users u ON r.user_id = u.id
             WHERE r.status = 'completed'
             ORDER BY r.request_date DESC";
$res_disp = $conn->query($sql_disp);
if ($res_disp) {
    while ($row = $res_disp->fetch_assoc()) {
        $dispense_list[] = $row;
    }
}

// --- 4. MONTHLY USAGE ---
$monthly_list = [];
$sql_month = "SELECT i.item_name, SUM(r.quantity) as total_qty
              FROM stock_requests r
              JOIN inventory i ON r.inventory_id = i.id
              WHERE r.status = 'completed' 
              AND MONTH(r.request_date) = MONTH(CURRENT_DATE())
              AND YEAR(r.request_date) = YEAR(CURRENT_DATE())
              GROUP BY i.item_name
              ORDER BY total_qty DESC";
$res_month = $conn->query($sql_month);
if ($res_month) {
    while ($row = $res_month->fetch_assoc()) {
        $monthly_list[] = $row;
    }
}

// --- 5. ANNUAL SUMMARY ---
$annual_list = [];
$sql_year = "SELECT i.item_name, SUM(r.quantity) as total_qty
              FROM stock_requests r
              JOIN inventory i ON r.inventory_id = i.id
              WHERE r.status = 'completed' 
              AND YEAR(r.request_date) = YEAR(CURRENT_DATE())
              GROUP BY i.item_name
              ORDER BY total_qty DESC";
$res_year = $conn->query($sql_year);
if ($res_year) {
    while ($row = $res_year->fetch_assoc()) {
        $annual_list[] = $row;
    }
}

// --- 6. NOTIFICATION BADGE LOGIC (Moved here!) ---
$notif_count = 0;
$total_active_alerts = 0; // Initialize safely

if (file_exists('count_notif.php')) {
    include 'count_notif.php';
    
    // Safety check
    if ($total_active_alerts == 0 && isset($notif_count) && $notif_count > 0) {
        $total_active_alerts = $notif_count;
    }

    // Calculate unseen count
    $seen_count = isset($_SESSION['seen_count']) ? (int)$_SESSION['seen_count'] : 0;
    $notif_count = $total_active_alerts - $seen_count;
    
    if ($notif_count < 0) $notif_count = 0;
}
?>