<?php
session_start();

// Ensure only admins can access this page
if ($_SESSION['role'] != 'admin') {
    header("Location: login.php");
    exit;
}

// Include PHPMailer and database connection
require 'vendor/autoload.php';
use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;

// Database connection
$conn = new mysqli("127.0.0.1", "root", "", "Hostel");
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

// Fetch the number of pending leave requests
$query = "SELECT COUNT(*) as total FROM leave_requests";
$result = $conn->query($query);
$row = $result->fetch_assoc();
$pendingCount = $row['total'];

// Notification logic
$notification = null;
if (isset($_SESSION['notification'])) {
    $notification = $_SESSION['notification'];
    unset($_SESSION['notification']);
}

/**
 * Function to send email announcements
 */
function sendAnnouncements($emails, $announcement) {
    $mail = new PHPMailer(true);
    try {
        // SMTP configuration
        $mail->isSMTP();
        $mail->Host = 'smtp.gmail.com';
        $mail->SMTPAuth = true;
        $mail->Username = 'premsaipradhan10@gmail.com';
        $mail->Password = 'gsqx umoz hkuh tavr';
        $mail->SMTPSecure = "tls";
        $mail->Port = 587;

        // Email settings
        $mail->setFrom('premsaipradhan10@gmail.com', 'Hostel Admin');
        $mail->Subject = 'Hostel Announcement';
        $mail->isHTML(true);

        // Sending emails
        foreach ($emails as $email) {
            $mail->addAddress($email);
            $mail->Body = "<p>Dear User,</p><p>" . nl2br(htmlspecialchars($announcement)) . "</p><p>Best Regards,<br>Hostel Admin</p>";
            $mail->send();
            $mail->clearAddresses();
        }

        return true;
    } catch (Exception $e) {
        return $mail->ErrorInfo;
    }
}

// Handle form submission
if ($_SERVER['REQUEST_METHOD'] == 'POST' && !empty($_POST['announcement'])) {
    $announcement = trim($_POST['announcement']);
    $target = $_POST['target']; // Get the target selection (boys, girls, or both)

    // Build the query based on the target
    $query = match ($target) {
        'both' => "SELECT email_id FROM users",
        'boys' => "SELECT email_id FROM users WHERE gender = 'male'",
        'girls' => "SELECT email_id FROM users WHERE gender = 'female'",
        default => "",
    };

    if ($query) {
        $result = $conn->query($query);

        if ($result && $result->num_rows > 0) {
            $emails = array_column($result->fetch_all(MYSQLI_ASSOC), 'email_id');
            $sendStatus = sendAnnouncements($emails, $announcement);

            // Set success or error notification
            if ($sendStatus === true) {
                $_SESSION['notification'] = ['type' => 'success', 'message' => 'Announcement sent successfully!'];
            } else {
                $_SESSION['notification'] = ['type' => 'error', 'message' => 'Error sending emails: ' . $sendStatus];
            }
        } else {
            $_SESSION['notification'] = ['type' => 'error', 'message' => 'No users found for the selected target.'];
        }
    } else {
        $_SESSION['notification'] = ['type' => 'error', 'message' => 'Invalid target selected.'];
    }
}

$conn->close();
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin Dashboard</title>
    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
            font-family: 'Poppins', sans-serif;
        }

        body {
            background: linear-gradient(135deg, #0f2027, #203a43, #2c5364);
            color: #fff;
            min-height: 100vh;
            overflow-x: hidden;
            opacity: 0;
            animation: fadeInBody 1s ease-out forwards 0.5s;
        }

        /* Navbar */
        .navbar {
            position: fixed;
            top: 0;
            left: 0;
            width: 250px;
            height: 100vh;
            background: linear-gradient(to bottom, #1a1a2e, #16213e);
            padding: 20px;
            box-shadow: 5px 0 15px rgba(0, 0, 0, 0.5);
            transform: translateX(-100%);
            animation: slideInNav 0.8s ease-out forwards;
            display: flex;
            flex-direction: column;
            justify-content: space-between;
        }

        .navbar h2 {
            font-size: 24px;
            letter-spacing: 2px;
            color: #e94560;
            text-transform: uppercase;
            text-align: center;
            animation: textGlow 2s infinite alternate;
        }

        .navbar-actions {
            display: flex;
            flex-direction: column;
            gap: 20px;
        }

        .navbar-btn {
            background: #0f3460;
            color: #fff;
            padding: 12px;
            border-radius: 10px;
            font-size: 16px;
            cursor: pointer;
            position: relative;
            transition: all 0.4s ease;
        }

        .navbar-btn:hover {
            background: #e94560;
            transform: translateX(10px);
            box-shadow: 0 5px 15px rgba(233, 69, 96, 0.5);
        }

        .complaint-count {
            background: #f4a261;
            color: #fff;
            padding: 4px 8px;
            border-radius: 50%;
            font-size: 12px;
            position: absolute;
            top: -10px;
            right: -10px;
            animation: pulse 1.5s infinite;
        }

        .logout-btn {
            background: #e94560;
            border: none;
            color: #fff;
            padding: 12px;
            border-radius: 10px;
            font-size: 16px;
            cursor: pointer;
            transition: all 0.4s ease;
        }

        .logout-btn:hover {
            background: #f4a261;
            transform: scale(1.05);
            box-shadow: 0 5px 15px rgba(244, 162, 97, 0.5);
        }

        /* Main Container */
        .main-container {
            margin-left: 270px;
            padding: 40px;
            display: flex;
            gap: 30px;
            flex-wrap: wrap;
            animation: fadeInContent 1s ease-out forwards 1s;
        }

        .left-column, .right-column {
            background: rgba(255, 255, 255, 0.1);
            backdrop-filter: blur(10px);
            padding: 30px;
            border-radius: 20px;
            box-shadow: 0 10px 30px rgba(0, 0, 0, 0.3);
            transition: all 0.4s ease;
        }

        .left-column {
            flex: 1;
            min-width: 300px;
        }

        .right-column {
            flex: 2;
            min-width: 400px;
        }

        .left-column:hover, .right-column:hover {
            transform: translateY(-10px);
            box-shadow: 0 15px 40px rgba(0, 0, 0, 0.4);
        }

        h1 {
            font-size: 28px;
            color: #e94560;
            margin-bottom: 25px;
            text-transform: uppercase;
            letter-spacing: 1.5px;
            animation: textSlideIn 1.2s ease-out;
        }

        /* Buttons */
        button {
            width: 100%;
            padding: 15px;
            margin: 10px 0;
            font-size: 16px;
            border: none;
            border-radius: 12px;
            cursor: pointer;
            color: #fff;
            text-transform: uppercase;
            transition: all 0.4s ease;
        }

        button:hover {
            transform: scale(1.05);
            box-shadow: 0 5px 15px rgba(0, 0, 0, 0.3);
        }

        .redirect-button {
            background: #533483;
        }

        .redirect-button:hover {
            background: #7d5ab8;
        }

        .toggle-button {
            background: #2a9d8f;
        }

        .toggle-button:hover {
            background: #48c9b0;
        }

        .leave-requests {
            background: #f4a261;
            position: relative;
        }

        .leave-requests:hover {
            background: #e76f51;
        }

        .badge {
            background: #e94560;
            padding: 6px 10px;
            border-radius: 50%;
            font-size: 14px;
            position: absolute;
            top: -15px;
            right: -15px;
            box-shadow: 0 3px 10px rgba(0, 0, 0, 0.3);
            animation: bounce 1s infinite;
        }

        /* Form Elements */
        textarea, select {
            width: 100%;
            padding: 15px;
            margin: 15px 0;
            font-size: 16px;
            border: 2px solid #533483;
            border-radius: 10px;
            background: rgba(255, 255, 255, 0.9);
            color: #333;
            transition: all 0.4s ease;
        }

        textarea:focus, select:focus {
            border-color: #e94560;
            box-shadow: 0 0 10px rgba(233, 69, 96, 0.5);
            outline: none;
        }

        .hidden-form {
            display: none;
            margin-top: 20px;
            padding: 20px;
            background: rgba(255, 255, 255, 0.15);
            border-radius: 15px;
            box-shadow: 0 5px 20px rgba(0, 0, 0, 0.2);
        }

        .hidden-form button {
            background: #0f3460;
        }

        .hidden-form button:hover {
            background: #e94560;
        }

        /* Notification */
        .notification {
            position: fixed;
            bottom: 40px;
            right: 40px;
            padding: 15px 30px;
            border-radius: 15px;
            color: #fff;
            font-size: 16px;
            z-index: 1000;
            box-shadow: 0 10px 20px rgba(0, 0, 0, 0.3);
            animation: bounceIn 0.6s ease-out, fadeOut 0.5s ease 3s forwards;
        }

        .notification.success {
            background: linear-gradient(to right, #2a9d8f, #48c9b0);
        }

        .notification.error {
            background: linear-gradient(to right, #e94560, #f4a261);
        }

        /* Animations */
        @keyframes fadeInBody {
            from { opacity: 0; }
            to { opacity: 1; }
        }

        @keyframes slideInNav {
            from { transform: translateX(-100%); }
            to { transform: translateX(0); }
        }

        @keyframes textGlow {
            from { text-shadow: 0 0 5px #e94560; }
            to { text-shadow: 0 0 15px #e94560, 0 0 25px #f4a261; }
        }

        @keyframes fadeInContent {
            from { opacity: 0; transform: translateY(50px); }
            to { opacity: 1; transform: translateY(0); }
        }

        @keyframes textSlideIn {
            from { transform: translateX(-100px); opacity: 0; }
            to { transform: translateX(0); opacity: 1; }
        }

        @keyframes pulse {
            0% { transform: scale(1); }
            50% { transform: scale(1.1); }
            100% { transform: scale(1); }
        }

        @keyframes bounce {
            0%, 20%, 50%, 80%, 100% { transform: translateY(0); }
            40% { transform: translateY(-10px); }
            60% { transform: translateY(-5px); }
        }

        @keyframes bounceIn {
            0% { transform: scale(0.3); opacity: 0; }
            50% { transform: scale(1.05); opacity: 1; }
            100% { transform: scale(1); opacity: 1; }
        }

        @keyframes fadeOut {
            from { opacity: 1; }
            to { opacity: 0; }
        }

        /* Responsive Design */
        @media (max-width: 900px) {
            .navbar {
                width: 100%;
                height: auto;
                position: relative;
                transform: translateX(0);
                flex-direction: row;
                justify-content: space-between;
                padding: 15px;
            }

            .main-container {
                margin-left: 0;
                padding: 20px;
                flex-direction: column;
            }

            .left-column, .right-column {
                width: 100%;
            }
        }
    </style>
    <script>
        document.addEventListener("DOMContentLoaded", () => {
            const notification = document.querySelector(".notification");
            if (notification) {
                notification.style.display = "block";
            }

            function toggleForm() {
                const form = document.getElementById("announcement-form");
                form.style.display = form.style.display === "block" ? "none" : "block";
            }
        });
    </script>
</head>
<body>
    <!-- Navigation Bar -->
    <div class="navbar">
        <h2>Admin Dashboard</h2>
        <div class="navbar-actions">
           
            <button class="logout-btn" onclick="window.location.href='logout.php';">Logout</button>
        </div>
    </div>

    <!-- Main Content -->
    <div class="main-container">
        <!-- Left Column -->
        <div class="left-column">
            <h1>Actions</h1>
            <button class="redirect-button" onclick="window.location.href='boys_complaints.php';">Boys Complaints</button>
            <button class="redirect-button" onclick="window.location.href='girls_complaints.php';">Girls Complaints</button>
            <button class="toggle-button" onclick="toggleForm()">Send Announcement</button>
            <button class="redirect-button" onclick="window.location.href='complaints_graph.php';">View Complaints Graph</button>
        </div>

        <!-- Right Column -->
        <div class="right-column">
            <h1>Dashboard</h1>
            <button class="leave-requests" onclick="window.location.href='leave_requests.php';">
                Leave Requests
                <span class="badge"><?php echo $pendingCount; ?></span>
            </button>
            <form id="announcement-form" class="hidden-form" method="POST" action="admin_dashboard.php">
                <textarea name="announcement" rows="5" placeholder="Enter your announcement here..." required></textarea>
                <select name="target" required>
                    <option value="both">Both Boys and Girls</option>
                    <option value="boys">Boys Only</option>
                    <option value="girls">Girls Only</option>
                </select>
                <button type="submit">Send Announcement</button>
            </form>
        </div>
    </div>

    <!-- Notification -->
    <?php if ($notification): ?>
        <div class="notification <?= htmlspecialchars($notification['type']); ?>">
            <?= htmlspecialchars($notification['message']); ?>
        </div>
    <?php endif; ?>
</body>
</html>