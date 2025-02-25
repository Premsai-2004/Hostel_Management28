<?php
// Database connection
$conn = new mysqli("127.0.0.1", "root", "", "Hostel");

// Check connection
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

// SQL query to create the leave_requests table
$sql = "CREATE TABLE IF NOT EXISTS leave_requests (
    request_id INT PRIMARY KEY AUTO_INCREMENT,
    id INT,
    status ENUM('pending', 'approved', 'rejected') DEFAULT 'pending',
    FOREIGN KEY (id) REFERENCES users(id) ON DELETE CASCADE
)";

// Execute the query
if ($conn->query($sql) === TRUE) {
    echo "Table 'leave_requests' created successfully!";
} else {
    echo "Error creating table: " . $conn->error;
}

// Close the connection
$conn->close();
?>
