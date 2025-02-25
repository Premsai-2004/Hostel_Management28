<?php
session_start();
use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;
use Twilio\Rest\Client;

require 'vendor/autoload.php'; 
require __DIR__ . '/vendor/autoload.php';

// Ensure only admins can update statuses
if ($_SESSION['role'] != 'admin') {
    header("Location: login.html");
    exit;
}

// Database connection
$conn = new mysqli("127.0.0.1", "root", "", "Hostel");
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

// Get form data
$complaint_id = $_POST['complaint_id'];
$new_status = $_POST['status'];

// Update complaint status
$sql = "UPDATE complaints SET status = ? WHERE complaint_id = ?";
$stmt = $conn->prepare($sql);
$stmt->bind_param("si", $new_status, $complaint_id);

if ($stmt->execute()) {
    // Fetch the user's email
    $sql_email = "SELECT email FROM complaints WHERE complaint_id = ?";
    $stmt_email = $conn->prepare($sql_email);
    $stmt_email->bind_param("i", $complaint_id);
    $stmt_email->execute();
    $result_email = $stmt_email->get_result();
    $row_email = $result_email->fetch_assoc();
    $user_email = $row_email['email'];

    // Send email notification
    $mail = new PHPMailer(true);
    try {
        $mail->isSMTP();
        $mail->Host = 'smtp.gmail.com';
        $mail->SMTPAuth = true;
        $mail->Username = 'premsaipradhan10@gmail.com';
        $mail->Password = 'zfej bkbb idju tpcf';
        $mail->SMTPSecure = "tls";
        $mail->Port = 587;

        $mail->setFrom('premsaipradhan10@gmail.com', 'Zombie');
        $mail->addAddress($user_email);

        $mail->isHTML(true);
        $mail->Subject = 'Complaint Status Update';
        $mail->Body = "Your complaint (ID: $complaint_id) status has been updated to: $new_status.";

        $mail->send();
        $notification = "Status updated and email sent!";
    } catch (Exception $e) {
        $notification = "Status updated, but email failed to send: {$mail->ErrorInfo}";
    }

    // Check if the status is "seen"
    if ($new_status === 'seen') {
        // Fetch complaint details
        $sql_details = "SELECT category, hostel_no FROM complaints WHERE complaint_id = ?";
        $stmt_details = $conn->prepare($sql_details);
        $stmt_details->bind_param("i", $complaint_id);
        $stmt_details->execute();
        $result_details = $stmt_details->get_result();
        $row_details = $result_details->fetch_assoc();

        $category = $row_details['category'];
        $hostel_no = $row_details['hostel_no'];

        // Fetch the worker details based on the category
        $sql_worker = "SELECT phone_number FROM workers WHERE specialization = ?";
        $stmt_worker = $conn->prepare($sql_worker);
        $stmt_worker->bind_param("s", $category);
        $stmt_worker->execute();
        $result_worker = $stmt_worker->get_result();

        if ($result_worker->num_rows > 0) {
            $row_worker = $result_worker->fetch_assoc();
            $worker_phone = $row_worker['phone_number'];

            // Verified phone numbers for trial Twilio account
            $verified_numbers = [
                '+917848942347', // Add verified numbers here
                '+917847952782',
                '+918917258476'
            ];

            // Send SMS to the worker using Twilio
            if (in_array($worker_phone, $verified_numbers)) {
                $sid =getenv('TWILIO_ACCOUNT_SID'); // Replace with your Twilio SID
                $token = getenv('TWILIO_AUTH_TOKEN'); // Replace with your Twilio Auth Token
                $twilio_phone_number = getenv('TWILIO_PHONE_NUMBER'); // Replace with your Twilio phone number

                $client = new Client($sid, $token);
                $message = "New complaint assigned:\nCategory: $category\nHostel No: $hostel_no\nComplaint ID: $complaint_id.";

                try {
                    $client->messages->create(
                        $worker_phone,
                        [
                            'from' => $twilio_phone_number,
                            'body' => $message
                        ]
                    );
                    $notification = "SMS sent to the worker successfully!";
                } catch (Exception $e) {
                    $notification = "Failed to send SMS to the worker: " . $e->getMessage();
                }
            } else {
                $notification = "Cannot send SMS. The number $worker_phone is not verified for this Twilio trial account.";
            }
        } else {
            $notification = "No worker found for the category: $category";
        }
    } elseif ($new_status === 'rejected') {
        $notification = "Status is rejected, no SMS will be sent to the worker.";
    }

} else {
    $notification = "Error updating status: " . $conn->error;
}

$stmt->close();
$conn->close();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin Dashboard</title>
    <style>
        /* Notification Styles */
        .notification {
            position: fixed;
            bottom: 10px;
            right: 10px;
            background-color: #28a745; /* Green background */
            color: white;
            padding: 15px;
            border-radius: 5px;
            box-shadow: 0 0 10px rgba(0, 0, 0, 0.1);
            display: none; /* Initially hidden */
            z-index: 1000;
            font-size: 14px;
        }

        .notification.success {
            background-color: #28a745;
        }

        .notification.error {
            background-color: #dc3545;
        }

        .notification .green-tick {
            font-size: 18px;
            margin-right: 10px;
        }
    </style>
</head>
<body>

<!-- Notification -->
<div id="notification" class="notification">
    <span class="green-tick">✔️</span> <span id="notification-message"></span>
</div>

<!-- Your Admin Dashboard Content -->

<script>
    // Show notification when PHP message is set
    <?php if (isset($notification)) { ?>
        document.getElementById('notification-message').innerText = "<?php echo addslashes($notification); ?>";
        document.getElementById('notification').style.display = "block";
        setTimeout(function() {
            document.getElementById('notification').style.display = "none";
        }, 5000); // Hide notification after 5 seconds
    <?php } ?>
</script>

</body>
</html>
