<?php
session_start();

// Database connection
$conn = new mysqli("127.0.0.1", "root", "", "Hostel");

// Check connection
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

// Check if the user is logged in
if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit;
}

// Retrieve user ID from session
$user_id = $_SESSION['user_id'];

// Handle complaint submission
if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['submit_complaint'])) {
    $category = $_POST['category'];
    $description = $_POST['description'];
    $email = $_SESSION['user_email'];
    $hostel_no = $_SESSION['hostel_no'];

    if (!empty($category) && !empty($description)) {
        $sql = "INSERT INTO complaints (user_id, category, description, email, hostel_no, status, created_at) 
                VALUES ('$user_id', '$category', '$description', '$email', '$hostel_no', 'Pending', NOW())";
        if ($conn->query($sql) === TRUE) {
            $successMessage = "Complaint submitted successfully!";
        } else {
            $errorMessage = "Error: " . $conn->error;
        }
    } else {
        $errorMessage = "Please fill in all fields.";
    }
}

// Query to fetch past complaints
$sql = "SELECT category, description, status, created_at FROM complaints WHERE user_id = '$user_id' ORDER BY created_at DESC";
$result = $conn->query($sql);
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>User Dashboard</title>
    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }

        body {
            font-family: 'Poppins', sans-serif;
            background: linear-gradient(135deg, #1e3c72, #2a5298);
            color: #fff;
            min-height: 100vh;
            overflow-x: hidden;
        }

        header {
            background: linear-gradient(to right, #ff6b6b, #ff8e53);
            padding: 20px;
            display: flex;
            justify-content: space-between;
            align-items: center;
            box-shadow: 0 5px 15px rgba(0, 0, 0, 0.3);
            animation: slideInFromTop 0.8s ease-out;
        }

        header h1 {
            font-size: 28px;
            letter-spacing: 2px;
            text-transform: uppercase;
        }

        .logout-btn {
            background: #fff;
            color: #ff6b6b;
            padding: 10px 20px;
            border: none;
            border-radius: 25px;
            font-weight: 600;
            cursor: pointer;
            transition: all 0.4s ease;
        }

        .logout-btn:hover {
            transform: rotate(5deg) scale(1.1);
            background: #ff8e53;
            color: #fff;
            box-shadow: 0 5px 15px rgba(255, 142, 83, 0.5);
        }

        .dashboard {
            display: flex;
            max-width: 1200px;
            margin: 30px auto;
            gap: 30px;
            padding: 0 20px;
            flex-wrap: wrap;
        }

        .column {
            flex: 1;
            background: rgba(255, 255, 255, 0.95);
            padding: 25px;
            border-radius: 20px;
            box-shadow: 0 10px 30px rgba(0, 0, 0, 0.2);
            min-width: 300px;
            animation: fadeInUp 0.8s ease-out;
        }

        .left-column {
            background: linear-gradient(to bottom, #ffffff, #f1f5f9);
            color: #333;
        }

        .right-column {
            background: linear-gradient(to bottom, #ffffff, #e2e8f0);
            color: #333;
            overflow: hidden;
        }

        h2, h3 {
            color: #1e3c72;
            margin-bottom: 20px;
            font-weight: 600;
        }

        form {
            display: flex;
            flex-direction: column;
            gap: 15px;
        }

        label {
            font-weight: 500;
            color: #2a5298;
            transition: color 0.3s ease;
        }

        select, textarea {
            padding: 12px;
            border: 2px solid #2a5298;
            border-radius: 10px;
            font-size: 16px;
            background: #fff;
            transition: all 0.4s ease;
        }

        select:hover, textarea:hover {
            border-color: #ff6b6b;
            box-shadow: 0 0 10px rgba(255, 107, 107, 0.3);
        }

        select:focus, textarea:focus {
            border-color: #ff8e53;
            box-shadow: 0 0 15px rgba(255, 142, 83, 0.5);
            outline: none;
        }

        button {
            padding: 15px;
            background: linear-gradient(to right, #ff6b6b, #ff8e53);
            color: #fff;
            border: none;
            border-radius: 25px;
            font-size: 16px;
            font-weight: 600;
            cursor: pointer;
            transition: all 0.4s ease;
        }

        button:hover {
            transform: translateY(-5px);
            box-shadow: 0 10px 20px rgba(255, 107, 107, 0.5);
            background: linear-gradient(to right, #ff8e53, #ff6b6b);
        }

        #toggleComplaints {
            margin-top: 20px;
            background: linear-gradient(to right, #2a5298, #1e3c72);
        }

        #toggleComplaints:hover {
            transform: scale(1.05);
            box-shadow: 0 10px 20px rgba(42, 82, 152, 0.5);
        }

        table {
            width: 100%;
            border-collapse: separate;
            border-spacing: 0 10px;
            margin-top: 20px;
        }

        th, td {
            padding: 15px;
            text-align: left;
            background: #fff;
            border-radius: 10px;
            transition: all 0.3s ease;
        }

        th {
            background: linear-gradient(to right, #2a5298, #1e3c72);
            color: #fff;
            font-weight: 600;
        }

        td {
            background: #f8fafc;
            color: #333;
        }

        tr:hover td {
            transform: translateX(5px);
            background: #e2e8f0;
            box-shadow: 0 5px 15px rgba(0, 0, 0, 0.1);
        }

        .popup {
            position: fixed;
            bottom: 30px;
            right: 30px;
            background: linear-gradient(to right, #34c759, #28a745);
            color: #fff;
            padding: 15px 25px;
            border-radius: 15px;
            box-shadow: 0 10px 20px rgba(0, 0, 0, 0.3);
            animation: bounceIn 0.6s ease-out, fadeOut 0.5s ease 3s forwards;
        }

        .popup.error {
            background: linear-gradient(to right, #ff4d4d, #dc3545);
        }

        /* Image Upload Styling */
        .custum-file-upload {
            height: 180px;
            width: 100%;
            max-width: 300px;
            display: flex;
            flex-direction: column;
            gap: 20px;
            cursor: pointer;
            align-items: center;
            justify-content: center;
            border: 3px dashed #2a5298;
            background: #fff;
            padding: 1.5rem;
            border-radius: 15px;
            box-shadow: 0 10px 20px rgba(0, 0, 0, 0.1);
            transition: all 0.4s ease;
        }

        .custum-file-upload:hover {
            border-color: #ff6b6b;
            transform: scale(1.03);
            box-shadow: 0 15px 25px rgba(255, 107, 107, 0.3);
        }

        .custum-file-upload .icon svg {
            height: 60px;
            fill: #2a5298;
            transition: fill 0.4s ease;
        }

        .custum-file-upload:hover .icon svg {
            fill: #ff6b6b;
        }

        .custum-file-upload .text span {
            font-weight: 500;
            color: #2a5298;
            transition: color 0.4s ease;
        }

        .custum-file-upload:hover .text span {
            color: #ff6b6b;
        }

        .custum-file-upload input {
            display: none;
        }

        /* Animations */
        @keyframes slideInFromTop {
            from { transform: translateY(-100%); opacity: 0; }
            to { transform: translateY(0); opacity: 1; }
        }

        @keyframes fadeInUp {
            from { transform: translateY(50px); opacity: 0; }
            to { transform: translateY(0); opacity: 1; }
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
        @media (max-width: 768px) {
            .dashboard {
                flex-direction: column;
            }
        }
    </style>
</head>
<body>
    <!-- Header Section -->
    <header>
        <h1>Hostel Management System - Girl_Dashboard</h1>
        <div>
            <form action="logout.php" method="POST" style="display: inline;">
                <button type="submit" class="logout-btn">Logout</button>
            </form>
            <form action="leave_application.php" method="POST" style="display: inline;">
                <button type="submit" class="logout-btn">Leave Form</button>
            </form>
        </div>
    </header>

    <!-- Dashboard Section -->
    <div class="dashboard">
        <!-- Left Column: Complaint Form -->
        <div class="column left-column">
            <h2>Welcome to Your Dashboard</h2>
            <form method="POST" enctype="multipart/form-data">
                <h3>Submit a New Complaint</h3>
                <label for="category">Category:</label>
                <select id="category" name="category" required>
                    <option value="food">Food</option>
                    <option value="accommodation">Accommodation</option>
                    <option value="water">Water</option>
                    <option value="electricity">Electricity</option>
                    <option value="cleanliness">Cleanliness</option>
                    <option value="carpenter">Carpenter</option>
                </select>

                <label for="description">Description:</label>
                <textarea id="description" name="description" rows="4" placeholder="Describe your issue here..." required></textarea>

                <label class="custum-file-upload" for="file">
                    <div class="icon">
                        <svg xmlns="http://www.w3.org/2000/svg" fill="" viewBox="0 0 24 24">
                            <g stroke-width="0" id="SVGRepo_bgCarrier"></g>
                            <g stroke-linejoin="round" stroke-linecap="round" id="SVGRepo_tracerCarrier"></g>
                            <g id="SVGRepo_iconCarrier"> 
                                <path fill="" d="M10 1C9.73478 1 9.48043 1.10536 9.29289 1.29289L3.29289 7.29289C3.10536 7.48043 3 7.73478 3 8V20C3 21.6569 4.34315 23 6 23H7C7.55228 23 8 22.5523 8 22C8 21.4477 7.55228 21 7 21H6C5.44772 21 5 20.5523 5 20V9H10C10.5523 9 11 8.55228 11 8V3H18C18.5523 3 19 3.44772 19 4V9C19 9.55228 19.4477 10 20 10C20.5523 10 21 9.55228 21 9V4C21 2.34315 19.6569 1 18 1H10ZM9 7H6.41421L9 4.41421V7ZM14 15.5C14 14.1193 15.1193 13 16.5 13C17.8807 13 19 14.1193 19 15.5V16V17H20C21.1046 17 22 17.8954 22 19C22 20.1046 21.1046 21 20 21H13C11.8954 21 11 20.1046 11 19C11 17.8954 11.8954 17 13 17H14V16V15.5ZM16.5 11C14.142 11 12.2076 12.8136 12.0156 15.122C10.2825 15.5606 9 17.1305 9 19C9 21.2091 10.7909 23 13 23H20C22.2091 23 24 21.2091 24 19C24 17.1305 22.7175 15.5606 20.9844 15.122C20.7924 12.8136 18.858 11 16.5 11Z" clip-rule="evenodd" fill-rule="evenodd"></path> 
                            </g>
                        </svg>
                    </div>
                    <div class="text">
                        <span>Click to upload image</span>
                    </div>
                    <input type="file" id="file" name="file" accept="image/*">
                </label>

                <button type="submit" name="submit_complaint">Submit Complaint</button>
            </form>
        </div>

        <!-- Right Column: Past Complaints -->
        <div class="column right-column">
            <h2>Your Past Complaints</h2>
            <button id="toggleComplaints">Show Past Complaints</button>
            <div id="pastComplaints" style="display: none;">
                <?php if ($result->num_rows > 0): ?>
                    <table>
                        <thead>
                            <tr>
                                <th>Category</th>
                                <th>Description</th>
                                <th>Status</th>
                                <th>Date</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php while ($row = $result->fetch_assoc()): ?>
                                <tr>
                                    <td><?php echo htmlspecialchars($row['category']); ?></td>
                                    <td><?php echo htmlspecialchars($row['description']); ?></td>
                                    <td><?php echo htmlspecialchars($row['status']); ?></td>
                                    <td><?php echo htmlspecialchars(date("d-m-Y H:i", strtotime($row['created_at']))); ?></td>
                                </tr>
                            <?php endwhile; ?>
                        </tbody>
                    </table>
                <?php else: ?>
                    <p style="color: #333;">You have not submitted any complaints yet.</p>
                <?php endif; ?>
            </div>
        </div>
    </div>

    <!-- Success/Error Notification -->
    <?php if (!empty($successMessage)): ?>
        <div class="popup"><?php echo $successMessage; ?></div>
    <?php elseif (!empty($errorMessage)): ?>
        <div class="popup error"><?php echo $errorMessage; ?></div>
    <?php endif; ?>

    <!-- Toggle Script -->
    <script>
        const toggleButton = document.getElementById('toggleComplaints');
        const complaintsSection = document.getElementById('pastComplaints');

        toggleButton.addEventListener('click', () => {
            complaintsSection.style.display = complaintsSection.style.display === 'none' || complaintsSection.style.display === '' ? 'block' : 'none';
            toggleButton.textContent = complaintsSection.style.display === 'block' ? 'Hide Past Complaints' : 'Show Past Complaints';
        });
    </script>
</body>
</html>