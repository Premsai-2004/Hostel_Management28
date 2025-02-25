<?php
session_start();

// Database connection
$conn = new mysqli("127.0.0.1", "root", "", "Hostel");
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

// Fetch the hostel number from the logged-in user (assumes session holds hostel number)
$hostel_number = $_SESSION['hostel_number'] ?? null;

if (!$hostel_number) {
    echo json_encode(['success' => false, 'message' => 'Hostel number not found']);
    exit;
}

// Insert the request into the database
$sql = "INSERT INTO sanitary_requests (hostel_number) VALUES (?)";
$stmt = $conn->prepare($sql);
$stmt->bind_param("s", $hostel_number);
if ($stmt->execute()) {
    // Notify admin
    $admin_email = "subhasripanigrahi342@gmail.com"; // Replace with the admin's email
    $subject = "Sanitary Product Request";
    $message = "Sanitary products are required in Hostel No: $hostel_number.";
    $headers = "From: no-reply@hostel.com";

    // Send email (or implement another notification system)
    if (mail($admin_email, $subject, $message, $headers)) {
        echo json_encode(['success' => true]);
    } else {
        echo json_encode(['success' => false, 'message' => 'Failed to send notification']);
    }
} else {
    echo json_encode(['success' => false, 'message' => 'Failed to log request']);
}

$stmt->close();
$conn->close();
?>
