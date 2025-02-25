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

// Check if the column already exists
$checkColumn = $conn->query("SHOW COLUMNS FROM users LIKE 'phone_no'");
if ($checkColumn->num_rows == 0) {
    // SQL query to add the phone_no column
    $sql = "ALTER TABLE users ADD phone_no VARCHAR(15) NOT NULL AFTER email_id";

    if ($conn->query($sql) === TRUE) {
        echo "Column 'phone_no' added successfully.";
    } else {
        echo "Error adding column: " . $conn->error;
    }
} else {
    echo "Column 'phone_no' already exists.";
}

$conn->close();
?>
