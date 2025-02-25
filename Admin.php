<?php
// Database connection
$conn = new mysqli("127.0.0.1", "root", "", "Hostel");

if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

// Admin account details
$roll_no = 'ADMIN01';
$name = 'Paanda';
$hostel_no = 'NA';
$email_id = 'subhasripanigrahi342@gmail.com';
$hashed_password = '$2y$10$R2dMP8LhpLHcM2xMmnIS/uLD5i7jXfs3ou/nANpTxdzojKGq7bXLm';
$role = 'admin';

// Insert query
$sql = "INSERT INTO users(roll_no, name, hostel_no, email_id, password, role) 
        VALUES ('$roll_no', '$name', '$hostel_no', '$email_id', '$hashed_password', '$role')";

if ($conn->query($sql) === TRUE) {
    echo "Admin account created successfully!";
} else {
    echo "Error: " . $conn->error;
}

$conn->close();
?>
