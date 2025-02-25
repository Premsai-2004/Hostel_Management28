<?php
session_start();

// Ensure only admins can access this page
if ($_SESSION['role'] != 'admin') {
    header("Location: login.php");
    exit;
}

// Database connection
$conn = new mysqli("127.0.0.1", "root", "", "Hostel");

// Check connection
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

// Handle delete request
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['delete_complaint_id'])) {
    $delete_id = $_POST['delete_complaint_id'];
    $delete_sql = "DELETE FROM complaints WHERE complaint_id = ?";
    $stmt = $conn->prepare($delete_sql);
    $stmt->bind_param("i", $delete_id);

    if ($stmt->execute()) {
        $_SESSION['notification'] = [
            'type' => 'success',
            'message' => 'Complaint deleted successfully.'
        ];
    } else {
        $_SESSION['notification'] = [
            'type' => 'error',
            'message' => 'Failed to delete complaint.'
        ];
    }
    $stmt->close();
    header("Location: " . $_SERVER['PHP_SELF']);
    exit;
}

// Fetch complaints
$sql = "SELECT * FROM complaints c 
        JOIN users u ON c.user_id = u.id;";
$result = $conn->query($sql);
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Complaints</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            background-color: #f4f4f9;
            margin: 0;
            padding: 0;
        }

        header {
            background-color: #007bff;
            color: white;
            padding: 15px;
            text-align: center;
        }

        table {
            width: 90%;
            margin: 20px auto;
            border-collapse: collapse;
        }

        th, td {
            padding: 10px;
            text-align: center;
            border: 1px solid #ddd;
        }

        th {
            background-color: #007bff;
            color: white;
        }

        .action-form {
            display: flex;
            justify-content: center;
            gap: 10px;
        }

        .action-form select, .action-form button {
            padding: 5px 10px;
        }

        .delete-icon {
            background: none;
            border: none;
            color: red;
            font-size: 20px;
            cursor: pointer;
        }

        .notification {
            position: fixed;
            bottom: 20px;
            right: 20px;
            padding: 15px;
            border-radius: 5px;
            display: flex;
            align-items: center;
            gap: 10px;
        }

        .notification.success {
            background-color: #d4edda;
            color: #155724;
        }

        .notification.error {
            background-color: #f8d7da;
            color: #721c24;
        }

        .notification span {
            font-size: 20px;
        }
    </style>
</head>
<body>
    <header>
        <h1>Complaints</h1>
    </header>
    <table>
        <tr>
            <th>Complaint ID</th>
            <th>User ID</th>
            <th>Category</th>
            <th>Description</th>
            <th>Status</th>
            <th>Action</th>
        </tr>
        <?php while ($row = $result->fetch_assoc()) { ?>
        <tr>
            <td><?= $row['complaint_id'] ?></td>
            <td><?= $row['user_id'] ?></td>
            <td><?= $row['category'] ?></td>
            <td><?= $row['description'] ?></td>
            <td><?= $row['status'] ?></td>
            <td>
                <!-- Update Status Form -->
                <form class="action-form" method="POST" action="update_status.php">
                    <input type="hidden" name="complaint_id" value="<?= $row['complaint_id'] ?>">
                    <select name="status" required>
                        <option value="">Select</option>
                        <option value="pending">Pending</option>
                        <option value="seen">Seen</option>
                        <option value="resolved">Resolved</option>
                        <option value="rejected">Rejected</option>
                    </select>
                    <button type="submit">Update</button>
                </form>
                <!-- Delete Complaint Button -->
                <form method="POST" style="display:inline;">
                    <input type="hidden" name="delete_complaint_id" value="<?= $row['complaint_id'] ?>">
                    <button type="submit" class="delete-icon" title="Delete Complaint" onclick="return confirm('Are you sure you want to delete this complaint?');">&times;</button>
                </form>
            </td>
        </tr>
        <?php } ?>
    </table>

    <?php
    // Display notification if set in the session
    if (isset($_SESSION['notification'])) {
        echo "<div class='notification {$_SESSION['notification']['type']}'>
                <span>" . ($_SESSION['notification']['type'] === 'success' ? '✔️' : '❌') . "</span>
                <span>{$_SESSION['notification']['message']}</span>
              </div>";
        unset($_SESSION['notification']); // Remove notification after displaying
    }
    ?>

    <script>
        // Auto-hide the notification after 3 seconds
        setTimeout(() => {
            const notification = document.querySelector('.notification');
            if (notification) {
                notification.remove();
            }
        }, 3000);
    </script>
</body>
</html>

<?php $conn->close(); ?>
