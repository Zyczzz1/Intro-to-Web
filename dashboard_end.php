<?php
// --- FIX: Check if session is active before starting ---
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

require 'db_connect.php'; // Ensure this matches your connection file name

// 1. SECURITY CHECK
if (!isset($_SESSION['user_id'])) {
    header("Location: login.html");
    exit();
}

// 2. GET USER INFO
$user_name = $_SESSION['full_name'];
$user_role = strtoupper($_SESSION['role']);
$user_initial = strtoupper(substr($user_name, 0, 1));

// --- 3. DASHBOARD CALCULATIONS ---

// A. Total Items
$sql_total = "SELECT SUM(quantity) as total_qty FROM inventory";
$result_total = $conn->query($sql_total);
$row_total = $result_total->fetch_assoc();
$total_items = $row_total['total_qty'] ? $row_total['total_qty'] : 0;

// B. Low Stock
$sql_low = "SELECT COUNT(*) as low_count FROM inventory WHERE quantity < 50";
$result_low = $conn->query($sql_low);
$low_stock = $result_low->fetch_assoc()['low_count'];

// C. Expired Items
$current_date = date('Y-m-d');
$sql_expired = "SELECT COUNT(*) as exp_count FROM inventory WHERE expiry_date < '$current_date'";
$result_expired = $conn->query($sql_expired);
$expired_items = $result_expired->fetch_assoc()['exp_count'];

// D. Expiring Soon (Next 60 Days)
$sql_soon = "SELECT COUNT(*) as soon_count FROM inventory 
             WHERE expiry_date BETWEEN '$current_date' AND DATE_ADD('$current_date', INTERVAL 60 DAY)";
$result_soon = $conn->query($sql_soon);
$expiring_soon = $result_soon->fetch_assoc()['soon_count'];


// --- 4. SMART TABLE LOGIC (ROLE BASED) ---


if ($user_role === 'ADMIN') {
    $table_title = "⚠️ Pending Stock Requests";
    
    // LOOK AT THIS QUERY CAREFULLY:
    // You MUST select 'r.inventory_id' and 'r.quantity' for the math to work!
    $sql_table = "SELECT 
                    r.id, 
                    r.inventory_id,  /* <--- MAKE SURE THIS IS HERE */
                    r.quantity,      /* <--- MAKE SURE THIS IS HERE */
                    i.item_name, 
                    u.full_name, 
                    r.request_date 
                  FROM stock_requests r
                  JOIN inventory i ON r.inventory_id = i.id
                  JOIN users u ON r.user_id = u.id
                  WHERE r.status = 'pending'
                  ORDER BY r.request_date DESC LIMIT 5";
                  
} else {
    // USER SEES: Critical Low Stock
    $table_title = "📉 Critical Low Stock";
    $sql_table = "SELECT item_name, category, quantity, expiry_date 
                  FROM inventory 
                  WHERE quantity < 50 
                  ORDER BY quantity ASC LIMIT 5";
}

$result_table = $conn->query($sql_table);

// ... existing code ...

// --- 5. PREPARE STATUS MESSAGES ---
$alert_message = ""; // Initialize empty variable

if (isset($_GET['msg'])) {
    if ($_GET['msg'] == 'fulfilled') {
        $alert_message = "<div class='alert success' style='background:#d4edda; color:#155724; padding:15px; margin-bottom:20px; border: 1px solid #c3e6cb; border-radius:5px;'>
                            <i class='fas fa-check-circle'></i> Request Fulfilled! Stock has been added.
                          </div>";
    }
    // You can add more messages here later if you want
    // if ($_GET['msg'] == 'error') { ... }
}
?>