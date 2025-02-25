<?php
session_start();

// Database connection
$conn = new mysqli("127.0.0.1", "root", "", "Hostel");

if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

// Fetch login credentials
$email = $_POST['email'];
$password = $_POST['password'];

// Query to check user credentials
$sql = "SELECT * FROM users WHERE email_id = '$email'";
$result = $conn->query($sql);

if ($result->num_rows > 0) {
    $row = $result->fetch_assoc();

    // Verify password
    if (password_verify($password, $row['password'])) {
        // Set session variables
        $_SESSION['user_id'] = $row['id'];          // Store user ID
        $_SESSION['user_email'] = $row['email_id']; // Store user email
        $_SESSION['hostel_no'] = $row['hostel_no']; // Store user hostel number
        $_SESSION['gender'] = $row['gender'];       // Store user gender
        $_SESSION['role'] = $row['role'];           // Store user role (admin/user)

        // Redirect based on role and gender
        if ($row['role'] == 'admin') {
            header("Location: admin_dashboard.php");
        } else {
            // Redirect users based on gender
            if ($row['gender'] == 'male') {
                header("Location: Boys_User_dashboard.php");
            } elseif ($row['gender'] == 'female') {
                header("Location: Girls_User_dashboard.php");
            } else {
                echo "Invalid gender specified!";
            }
        }
        exit;
    } else {
        echo "Invalid password!";
    }
} else {
    echo "User not found!";
}

$conn->close();
?>
