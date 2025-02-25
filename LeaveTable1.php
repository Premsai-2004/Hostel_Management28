<?php
$servername = "127.0.0.1";
$username = "root";
$password = "";
$dbname = "Hostel";

// Create connection
$conn = new mysqli($servername, $username, $password, $dbname);

// Check connection
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

// SQL to create leave_applications table
$sql = "CREATE TABLE leave_applications (
    id INT AUTO_INCREMENT PRIMARY KEY,
    name VARCHAR(255) NOT NULL,
    roll_no VARCHAR(50) NOT NULL,
    hostel_no VARCHAR(50) NOT NULL,
    room_no VARCHAR(50) NOT NULL,
    start_date DATE NOT NULL,
    end_date DATE NOT NULL,
    reason TEXT NOT NULL,
    status ENUM('Pending', 'Approved', 'Rejected') DEFAULT 'Pending',
    submitted_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP


)";

if ($conn->query($sql) === TRUE) {
    echo "Table leave_applications created successfully.";
} else {
    echo "Error creating table: " . $conn->error;
}

$conn->close();
?>
