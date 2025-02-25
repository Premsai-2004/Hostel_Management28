<?php
session_start();

// Database connection
$conn = new mysqli("p:127.0.0.1", "root", "", "Hostel");

// Check if the user is logged in
if (!isset($_SESSION['user_id'])) {
    // Redirect to login if not logged in
    header("Location: login.php");
    exit;
}

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // Retrieve session user_id and form data
    $user_id = $_SESSION['user_id'];
    $category = $_POST['category'];
    $description = $_POST['description'];
    $email = $_SESSION['user_email'];  // Get the email from session
    $hostel_no = $_SESSION['hostel_no'];  // Get the hostel number from session

    // Check if the category and description are not empty
    if (!empty($category) && !empty($description) && !empty($email) && !empty($hostel_no)) {
        // Insert the complaint into the database
        $sql = "INSERT INTO complaints (user_id, category, description, email, hostel_no) 
                VALUES ('$user_id', '$category', '$description', '$email', '$hostel_no')";

        if ($conn->query($sql) === TRUE) {
            echo "Complaint submitted successfully!";
        } else {
            echo "Error: " . $conn->error;
        }
    } else {
        echo "Please fill in all fields!";
    }
}

$conn->close();
?>
