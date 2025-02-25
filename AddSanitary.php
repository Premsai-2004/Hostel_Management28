<?php
$servername = "127.0.0.1";
$username = "root";
$password = "";
$dbname = "Hostel";

// Create connection
$conn = mysqli_connect($servername, $username, $password, $dbname);
// Check connection
if (!$conn) {
  die("Connection failed: " . mysqli_connect_error());
}

// sql to create table
$sql = "CREATE TABLE sanitary_requests (
    id INT AUTO_INCREMENT PRIMARY KEY,
    request_date TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    status ENUM('pending', 'completed') DEFAULT 'pending'


)";

if (mysqli_query($conn, $sql)) {
  echo "Table sanitary created successfully";
} else {
  echo "Error creating table: " . mysqli_error($conn);
}

mysqli_close($conn);
?>
