<?php
// Database connection
$conn = new mysqli("127.0.0.1", "root", "", "Hostel");

// Check connection
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

// SQL to alter the table and add the 'role' column
$sql = "ALTER TABLE users ADD COLUMN gender ENUM('male', 'female') NOT NULL AFTER name";

if ($conn->query($sql) === TRUE) {
    echo "Column 'gender' added successfully!";
} else {
    echo "Error altering table: " . $conn->error;
}

// Close the connection
$conn->close();
?>
