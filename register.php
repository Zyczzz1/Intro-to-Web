<?php
// 1. Connect to Database
require 'db_connect.php';

// 2. Check if form was submitted
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    
    // 3. Get data
    $name = $conn->real_escape_string($_POST['name']);
    $email = $conn->real_escape_string($_POST['email']);
    $password = $_POST['password'];
    $role = $_POST['role'];

    // 4. Check for duplicates
    $check = "SELECT id FROM users WHERE email = '$email'";
    $result = $conn->query($check);

    if ($result->num_rows > 0) {
        echo "<script>alert('Email already exists!'); window.location.href='signup.html';</script>";
    } else {
        // 5. Secure Password
        $hashed_password = password_hash($password, PASSWORD_DEFAULT);

        // 6. Insert Data
        $sql = "INSERT INTO users (full_name, email, password, role) VALUES ('$name', '$email', '$hashed_password', '$role')";

        if ($conn->query($sql) === TRUE) {
            echo "<script>alert('Registration Successful! Please Log In.'); window.location.href='login.html';</script>";
        } else {
            echo "Error: " . $sql . "<br>" . $conn->error;
        }
    }
}
$conn->close();
?>