<?php
// Database connection
$conn = new mysqli("127.0.0.1", "root", "", "Hostel");
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

// Manually specify worker data
$specialization = "Carpenter"; // Example: Carpenter, Plumber, Electrician, etc.
$phone_number = "8917258476"; // Example phone number

// Validate data (basic validation)
if (empty($specialization) || empty($phone_number)) {
    die("Error: Both specialization and phone number are required!");
}

// Prepare SQL query
$sql = "INSERT INTO workers (specialization, phone_number) VALUES (?, ?)";
$stmt = $conn->prepare($sql);
$stmt->bind_param("ss", $specialization, $phone_number);

// Execute query
if ($stmt->execute()) {
    echo "Worker added successfully!";
} else {
    echo "Error adding worker: " . $conn->error;
}

// Close connections
$stmt->close();
$conn->close();
?>
