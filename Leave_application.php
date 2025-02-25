<?php
session_start();
$conn = new mysqli("127.0.0.1", "root", "", "Hostel");

if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

// Initialize notification message
$notification = "";

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['submit_application'])) {
    $roll_no = $_POST['roll_no'];
    $start_date = $_POST['start_date'];
    $end_date = $_POST['end_date'];
    $reason = $_POST['reason'];
    $parent_phone = $_POST['parent_phone'];

    // Get user ID from roll number
    $stmt = $conn->prepare("SELECT id FROM users WHERE roll_no = ?");
    $stmt->bind_param("s", $roll_no);
    $stmt->execute();
    $stmt->bind_result($user_id);
    $stmt->fetch();
    $stmt->close();

    if ($user_id) {
        $stmt = $conn->prepare("INSERT INTO leave_requests (user_id, start_date, end_date, reason, parent_phone, status) VALUES (?, ?, ?, ?, ?, 'pending')");
        $stmt->bind_param("issss", $user_id, $start_date, $end_date, $reason, $parent_phone);
        
        if ($stmt->execute()) {
            $notification = "Leave application submitted successfully!";
        } else {
            $notification = "Error submitting application!";
        }
        $stmt->close();
    } else {
        $notification = "Roll number not found. Please register first!";
    }
}

$conn->close();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Hostel Leave Application</title>
    <script src="https://cdn.tailwindcss.com"></script>
</head>
<body class="flex justify-center items-center h-screen bg-gray-900">
    <div class="bg-white rounded-lg shadow-lg p-6 max-w-md w-full">
        <h2 class="text-2xl font-bold text-gray-800 text-center mb-4">Hostel Leave Application</h2>
        <form method="POST" class="space-y-4">
            <div>
                <input required name="roll_no" placeholder="Roll Number" class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500" type="text" />
            </div>
            <div>
                <input required name="start_date" class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500" type="date" />
            </div>
            <div>
                <input required name="end_date" class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500" type="date" />
            </div>
            <div>
                <textarea required name="reason" placeholder="Reason for Leave" class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500" rows="3"></textarea>
            </div>
            <div>
                <input required name="parent_phone" placeholder="Parent's Phone Number" class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500" type="tel" pattern="[0-9]{10}" title="Enter a valid 10-digit phone number" />
            </div>
            <button type="submit" name="submit_application" class="w-full bg-blue-600 text-white font-bold py-2 rounded-md hover:bg-blue-700 transition">Submit</button>
        </form>
        <button onclick="history.back()" class="w-full mt-4 bg-gray-600 text-white font-bold py-2 rounded-md hover:bg-gray-700 transition">Back</button>
    </div>

    <!-- Notification -->
    <?php if (!empty($notification)) : ?>
        <div id="notification" class="fixed bottom-5 right-5 bg-green-500 text-white px-4 py-2 rounded shadow-lg">
            <?php echo $notification; ?>
        </div>
        <script>
            setTimeout(() => {
                document.getElementById('notification').style.display = 'none';
            }, 3000);
        </script>
    <?php endif; ?>
</body>
</html>
