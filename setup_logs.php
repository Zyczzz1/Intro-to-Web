<?php
require 'db_connect.php';

$sql = "CREATE TABLE IF NOT EXISTS user_logs (
    id INT AUTO_INCREMENT PRIMARY KEY,
    user_id INT NOT NULL,
    action_title VARCHAR(50),
    action_desc VARCHAR(255),
    action_type VARCHAR(20), 
    created_at DATETIME DEFAULT CURRENT_TIMESTAMP
)";

if ($conn->query($sql) === TRUE) {
    echo "<h1>✅ SUCCESS!</h1>";
    echo "The database table 'user_logs' is ready.";
} else {
    echo "<h1>❌ ERROR</h1>";
    echo $conn->error;
}
?>