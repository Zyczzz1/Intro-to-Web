<?php
// FILE: inventory_end.php
session_start();
require 'db_connect.php';

// 1. SECURITY CHECK
if (!isset($_SESSION['user_id'])) {
    header("Location: login.html");
    exit();
}

// 2. GET USER VARIABLES
$user_id = $_SESSION['user_id'];
$user_role = strtoupper($_SESSION['role']);
$user_name = isset($_SESSION['full_name']) ? $_SESSION['full_name'] : 'User';
$user_initial = strtoupper(substr($user_name, 0, 1));

$message = "";

// 3. HANDLE FORM SUBMISSIONS
if ($_SERVER["REQUEST_METHOD"] == "POST") {

    // --- A. ADMIN: ADD ITEM ---
    if (isset($_POST['action']) && $_POST['action'] == 'add_item') {
        if ($user_role === 'ADMIN') {
            $item_name = $conn->real_escape_string($_POST['item_name']);
            $category = $conn->real_escape_string($_POST['category']);
            $quantity = (int)$_POST['quantity'];
            $expiry = $conn->real_escape_string($_POST['expiry_date']);

            $sql = "INSERT INTO inventory (item_name, category, quantity, expiry_date) 
                    VALUES ('$item_name', '$category', '$quantity', '$expiry')";
            
            if ($conn->query($sql)) {
                $conn->query("CREATE TABLE IF NOT EXISTS user_logs (id INT AUTO_INCREMENT PRIMARY KEY, user_id INT, action_title VARCHAR(50), action_desc VARCHAR(255), action_type VARCHAR(20), created_at DATETIME DEFAULT CURRENT_TIMESTAMP)");
                $conn->query("INSERT INTO user_logs (user_id, action_title, action_desc, action_type) VALUES ('$user_id', 'Stock Added', 'Added $quantity units of $item_name', 'success')");
                header("Location: inventory.php?msg=added");
                exit();
            } else {
                $message = "<div class='alert error'>Error: " . $conn->error . "</div>";
            }
        }
    }

    // --- B. ADMIN: EDIT ITEM ---
    if (isset($_POST['action']) && $_POST['action'] == 'update_item') {
        if ($user_role === 'ADMIN') {
            $id = (int)$_POST['item_id'];
            $item_name = $conn->real_escape_string($_POST['item_name']);
            $category = $conn->real_escape_string($_POST['category']);
            $quantity = (int)$_POST['quantity'];
            $expiry = $conn->real_escape_string($_POST['expiry_date']);

            $sql = "UPDATE inventory SET item_name='$item_name', category='$category', quantity='$quantity', expiry_date='$expiry' WHERE id='$id'";

            if ($conn->query($sql)) {
                $conn->query("INSERT INTO user_logs (user_id, action_title, action_desc, action_type) VALUES ('$user_id', 'Stock Updated', 'Updated details for $item_name', 'warning')");
                header("Location: inventory.php?msg=updated");
                exit();
            }
        }
    }

    // --- C. ADMIN: DELETE ITEM (RESTORED) ---
    if (isset($_POST['action']) && $_POST['action'] == 'delete_item') {
        if ($user_role === 'ADMIN') {
            $id = (int)$_POST['item_id'];
            $item_name = $_POST['item_name']; // For logging

            $sql = "DELETE FROM inventory WHERE id='$id'";

            if ($conn->query($sql)) {
                $conn->query("INSERT INTO user_logs (user_id, action_title, action_desc, action_type) VALUES ('$user_id', 'Stock Deleted', 'Deleted item: $item_name', 'danger')");
                header("Location: inventory.php?msg=deleted");
                exit();
            } else {
                $message = "<div class='alert error'>Error: " . $conn->error . "</div>";
            }
        }
    }

    // --- D. USER: DISPENSE ITEM ---
    if (isset($_POST['action']) && $_POST['action'] == 'dispense_item') {
        $id = (int)$_POST['item_id'];
        $qty_to_remove = (int)$_POST['quantity_to_dispense'];
        
        $check_sql = "SELECT item_name, quantity FROM inventory WHERE id = '$id'";
        $result = $conn->query($check_sql);
        $item = $result->fetch_assoc();

        if ($item) {
            if ($item['quantity'] >= $qty_to_remove) {
                $new_qty = $item['quantity'] - $qty_to_remove;
                if ($conn->query("UPDATE inventory SET quantity = '$new_qty' WHERE id = '$id'")) {
                    $item_name = $item['item_name'];
                    $conn->query("CREATE TABLE IF NOT EXISTS user_logs (id INT AUTO_INCREMENT PRIMARY KEY, user_id INT, action_title VARCHAR(50), action_desc VARCHAR(255), action_type VARCHAR(20), created_at DATETIME DEFAULT CURRENT_TIMESTAMP)");
                    $conn->query("INSERT INTO user_logs (user_id, action_title, action_desc, action_type) 
                                  VALUES ('$user_id', 'Dispensed Medicine', 'Dispensed $qty_to_remove units of $item_name', 'info')");
                    header("Location: inventory.php?msg=dispensed");
                    exit();
                }
            } else {
                $message = "<div class='alert error'>Error: Not enough stock! Current: " . $item['quantity'] . "</div>";
            }
        }
    }

    // --- E. USER: REQUEST ITEM ---
    if (isset($_POST['action']) && $_POST['action'] == 'request_item') {
        $inventory_id = (int)$_POST['item_id'];
        $qty_needed = (int)$_POST['quantity_needed'];
        $date = date('Y-m-d H:i:s');

        $conn->query("CREATE TABLE IF NOT EXISTS stock_requests (id INT AUTO_INCREMENT PRIMARY KEY, user_id INT, inventory_id INT, quantity INT, status VARCHAR(20) DEFAULT 'pending', request_date DATETIME)");
        $sql = "INSERT INTO stock_requests (user_id, inventory_id, quantity, status, request_date) 
                VALUES ('$user_id', '$inventory_id', '$qty_needed', 'pending', '$date')";

        if ($conn->query($sql)) {
            $conn->query("INSERT INTO user_logs (user_id, action_title, action_desc, action_type) VALUES ('$user_id', 'Stock Requested', 'Requested $qty_needed units', 'warning')");
            header("Location: inventory.php?msg=requested");
            exit();
        } else {
            $message = "<div class='alert error'>Error: " . $conn->error . "</div>";
        }
    }
}

// 4. FETCH INVENTORY LIST
$inventory_list = [];
$sql = "SELECT * FROM inventory ORDER BY item_name ASC";
$result = $conn->query($sql);
if ($result->num_rows > 0) {
    while($row = $result->fetch_assoc()) {
        $inventory_list[] = $row;
    }
}
?>