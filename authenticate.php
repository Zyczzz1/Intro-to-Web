<?php
session_start();
require 'db_connect.php';

if (!isset($_POST['email']) || !isset($_POST['password'])) {
    header("Location: login.html");
    exit();
}

$email = $conn->real_escape_string($_POST['email']);
$password = $_POST['password'];

$sql = "SELECT * FROM users WHERE email = '$email'";
$result = $conn->query($sql);

if ($result->num_rows > 0) {
    $user = $result->fetch_assoc();

    // trim() removes invisible spaces that might cause errors
    if (password_verify($password, trim($user['password']))) {
        // SUCCESS
        $_SESSION['user_id'] = $user['id'];
        $_SESSION['full_name'] = $user['full_name'];
        $_SESSION['role'] = $user['role'];
        header("Location: dashboard.php");
        exit();
    } else {
        // FAIL: Wrong Password -> Send back with flag
        header("Location: login.html?error=incorrect");
        exit();
    }
} else {
    // FAIL: Email not found -> Send back with flag
    header("Location: login.html?error=notfound");
    exit();
}
?>