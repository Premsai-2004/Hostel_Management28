<?php
session_start();
if (!isset($_SESSION['role']) || $_SESSION['role'] != 'admin') {
    header("Location: login.php");
    exit;
}

require 'vendor/autoload.php';
use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;

// Database connection
$conn = new mysqli("127.0.0.1", "root", "", "Hostel");
if ($conn->connect_error) {
    die("Database Connection Failed: " . $conn->connect_error);
}

// Handle Approve/Reject
if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['leave_id'], $_POST['status'])) {
    $leave_id = intval($_POST['leave_id']);
    $status = $_POST['status']; // "approved" or "rejected"

    // Get student email
    $stmt = $conn->prepare("SELECT users.email_id 
                            FROM users 
                            JOIN leave_requests ON users.id = leave_requests.user_id 
                            WHERE leave_requests.request_id = ?");
    $stmt->bind_param("i", $leave_id);
    $stmt->execute();
    $result = $stmt->get_result();
    if ($row = $result->fetch_assoc()) {
        $student_email = $row['email_id'];

        // Update status
        $update_stmt = $conn->prepare("UPDATE leave_requests SET status=? WHERE request_id=?");
        $update_stmt->bind_param("si", $status, $leave_id);
        $update_stmt->execute();
        $update_stmt->close();

        // Send Email Notification
        $mail = new PHPMailer(true);
        try {
            $mail->isSMTP();
            $mail->Host = 'smtp.gmail.com';
            $mail->SMTPAuth = true;
            $mail->Username = 'premsaipradhan10@gmail.com';
            $mail->Password = 'ypgt zofl cwwj uipf'; // Use App Password
            $mail->SMTPSecure = "tls";
            $mail->Port = 587;

            $mail->setFrom('premsaipradhan10@gmail.com', 'Hostel Admin');
            $mail->addAddress($student_email);
            $mail->Subject = "Leave Application " . ucfirst($status);
            $mail->isHTML(true);
            $mail->Body = "<p>Dear Student,</p><p>Your leave application has been <strong>$status</strong> by the admin.</p><p>Best Regards,<br>Hostel Admin</p>";

            $mail->send();
        } catch (Exception $e) {
            $_SESSION['notification'] = ['type' => 'error', 'message' => 'Email failed: ' . $mail->ErrorInfo];
        }

        // Delete rejected leave requests
        if ($status === 'rejected') {
            $delete_stmt = $conn->prepare("DELETE FROM leave_requests WHERE request_id=?");
            $delete_stmt->bind_param("i", $leave_id);
            $delete_stmt->execute();
            $delete_stmt->close();
        }
    }

    header("Location: leave_requests.php");
    exit;
}

// Fetch pending & approved requests
$query = "SELECT request_id, users.name, users.roll_no, start_date, end_date, reason, parent_phone, status
          FROM leave_requests 
          JOIN users ON leave_requests.user_id = users.id 
          WHERE status IN ('pending', 'approved')";
$result = $conn->query($query);
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Leave Requests</title>
    <script src="https://cdn.tailwindcss.com"></script>
</head>
<body class="p-10 bg-gray-100">
    <h2 class="text-2xl font-bold text-gray-800 mb-5">Pending & Approved Leave Requests</h2>
    <?php if ($result->num_rows > 0) { ?>
        <table class="min-w-full bg-white shadow-md rounded-lg overflow-hidden">
            <thead class="bg-blue-600 text-white">
                <tr>
                    <th class="py-2 px-4">Student Name</th>
                    <th class="py-2 px-4">Roll No</th>
                    <th class="py-2 px-4">Start Date</th>
                    <th class="py-2 px-4">End Date</th>
                    <th class="py-2 px-4">Reason</th>
                    <th class="py-2 px-4">Parent Phone</th>
                    <th class="py-2 px-4">Status</th>
                    <th class="py-2 px-4">Action</th>
                </tr>
            </thead>
            <tbody>
                <?php while ($row = $result->fetch_assoc()) { ?>
                    <tr class="border-b">
                        <td class="py-2 px-4"><?php echo htmlspecialchars($row['name']); ?></td>
                        <td class="py-2 px-4"><?php echo htmlspecialchars($row['roll_no']); ?></td>
                        <td class="py-2 px-4"><?php echo htmlspecialchars($row['start_date']); ?></td>
                        <td class="py-2 px-4"><?php echo htmlspecialchars($row['end_date']); ?></td>
                        <td class="py-2 px-4"><?php echo htmlspecialchars($row['reason']); ?></td>
                        <td class="py-2 px-4"><?php echo htmlspecialchars($row['parent_phone']); ?></td>
                        <td class="py-2 px-4">
                            <?php if ($row['status'] === 'pending') { ?>
                                <span class="bg-yellow-500 text-white px-2 py-1 rounded">Pending</span>
                            <?php } else { ?>
                                <span class="bg-green-500 text-white px-2 py-1 rounded">Approved</span>
                            <?php } ?>
                        </td>
                        <td class="py-2 px-4">
                            <?php if ($row['status'] === 'pending') { ?>
                                <form method="POST">
                                    <input type="hidden" name="leave_id" value="<?php echo htmlspecialchars($row['request_id']); ?>">
                                    <button type="submit" name="status" value="approved" class="bg-green-500 text-white px-3 py-1 rounded hover:bg-green-700">Approve</button>
                                    <button type="submit" name="status" value="rejected" class="bg-red-500 text-white px-3 py-1 rounded hover:bg-red-700">Reject</button>
                                </form>
                            <?php } else { ?>
                                <span class="text-gray-500">No Action</span>
                            <?php } ?>
                        </td>
                    </tr>
                <?php } ?>
            </tbody>
        </table>
    <?php } else { ?>
        <p class="text-gray-600">No leave requests found.</p>
    <?php } ?>
</body>
</html>
