<?php
$admin_password = "YourSecurePassword"; // Replace with your desired admin password
$hashed_password = password_hash($admin_password, PASSWORD_BCRYPT);

echo "Hashed Password: " . $hashed_password;
?>