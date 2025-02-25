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
$sql = "CREATE TABLE workers (
    id INT AUTO_INCREMENT PRIMARY KEY,
    specialization VARCHAR(50) NOT NULL,
    phone_number VARCHAR(15) NOT NULL
)";

if (mysqli_query($conn, $sql)) {
  echo "Table workers created successfully";
} else {
  echo "Error creating table: " . mysqli_error($conn);
}

mysqli_close($conn);
?>
