<?php
session_start(); // 1. Resume the session so we can access it

// 2. Remove all session variables (Username, Role, etc.)
session_unset(); 

// 3. Destroy the session entirely (The actual "Logout")
session_destroy(); 

// 4. Redirect the user to the Homepage
// Make sure your homepage file is actually named "index.html"
header("Location: index.html"); 
exit();
?>