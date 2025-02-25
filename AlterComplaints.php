<?php
// Database connection
$conn = new mysqli("127.0.0.1", "root", "", "Hostel");

// Check connection
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

// SQL to alter the table and add the 'role' column
$sql = "ALTER TABLE complaints 
ADD COLUMN id INT AUTO_INCREMENT PRIMARY KEY";

if ($conn->query($sql) === TRUE) {
    echo "Column `id` added successfully!";
} else {
    echo "Error altering table: " . $conn->error;
}

// Close the connection
$conn->close();
?>
