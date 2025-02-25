<?php
session_start();

// Ensure only admins can access this page
if ($_SESSION['role'] != 'admin') {
    header("Location: login.php");
    exit;
}

// Database connection
$conn = new mysqli("127.0.0.1", "root", "", "Hostel");
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

// Fetch total complaint data grouped by month for the last 6 months
$query = "
    SELECT 
        DATE_FORMAT(created_at, '%Y-%m') AS month,
        COUNT(*) AS total_complaints
    FROM complaints
    WHERE created_at >= DATE_SUB(CURDATE(), INTERVAL 6 MONTH)
    GROUP BY DATE_FORMAT(created_at, '%Y-%m')
    ORDER BY month ASC
";
$result = $conn->query($query);

$months = [];
$totalData = [];

while ($row = $result->fetch_assoc()) {
    $months[] = $row['month'];
    $totalData[] = (int)$row['total_complaints'];
}

$conn->close();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Complaints Graph</title>
    <style>
        body {
            font-family: 'Arial', sans-serif;
            background: linear-gradient(135deg, #e6e9f0, #eef1f5);
            color: #333;
            padding: 20px;
            min-height: 100vh;
        }
        .container {
            max-width: 1200px;
            margin: 0 auto;
            background: #fff;
            padding: 20px;
            border-radius: 8px;
            box-shadow: 0 4px 12px rgba(0, 0, 0, 0.1);
            border: 1px solid #d9d9d9;
        }
        h1 {
            color: #2c3e50;
            font-size: 24px;
            margin-bottom: 20px;
            text-align: center;
        }
        .chart-container {
            position: relative;
            height: 500px;
            width: 100%;
        }
        .back-btn {
            background: #34495e;
            color: #fff;
            padding: 10px 20px;
            border: none;
            border-radius: 6px;
            cursor: pointer;
            margin-bottom: 20px;
            transition: background 0.3s ease;
        }
        .back-btn:hover {
            background: #2c3e50;
        }
    </style>
    <!-- Include Chart.js and plugins -->
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/chartjs-plugin-zoom@1.2.1/dist/chartjs-plugin-zoom.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/chartjs-plugin-datalabels@2.0.0/dist/chartjs-plugin-datalabels.min.js"></script>
</head>
<body>
    <div class="container">
        <button class="back-btn" onclick="window.location.href='admin_dashboard.php';">Back to Dashboard</button>
        <h1>Hostel Student Complaints Over Time</h1>
        <div class="chart-container">
            <canvas id="complaintsChart"></canvas>
        </div>
    </div>

    <script>
        const ctx = document.getElementById('complaintsChart').getContext('2d');
        const complaintsChart = new Chart(ctx, {
            type: 'bar', // You can change to 'line', 'pie', etc.
            data: {
                labels: <?php echo json_encode($months); ?>,
                datasets: [
                    {
                        label: 'Total Complaints',
                        data: <?php echo json_encode($totalData); ?>,
                        backgroundColor: 'rgba(46, 204, 113, 0.7)', // Green for overall complaints
                        borderColor: 'rgba(46, 204, 113, 1)',
                        borderWidth: 1
                    }
                ]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                plugins: {
                    legend: {
                        position: 'top',
                        labels: {
                            font: { size: 14 },
                            boxWidth: 20
                        }
                    },
                    tooltip: {
                        mode: 'index',
                        intersect: false,
                        backgroundColor: 'rgba(0, 0, 0, 0.8)',
                        titleFont: { size: 16 },
                        bodyFont: { size: 14 },
                        padding: 10
                    },
                    datalabels: {
                        anchor: 'end',
                        align: 'top',
                        color: '#333',
                        font: { size: 12, weight: 'bold' }
                    }
                },
                scales: {
                    x: {
                        title: { display: true, text: 'Month', font: { size: 16 } },
                        grid: { display: false }
                    },
                    y: {
                        title: { display: true, text: 'Number of Complaints', font: { size: 16 } },
                        beginAtZero: true,
                        grid: { color: '#e0e0e0' }
                    }
                },
                animation: {
                    duration: 1500,
                    easing: 'easeInOutQuart'
                },
                zoom: {
                    enabled: true,
                    mode: 'xy',
                    sensitivity: 3,
                    speed: 0.1
                },
                pan: {
                    enabled: true,
                    mode: 'xy'
                }
            },
            plugins: [ChartDataLabels] // Enable data labels plugin
        });

        // Add export functionality
        document.addEventListener('keydown', (e) => {
            if (e.key === 'p') {
                const link = document.createElement('a');
                link.href = complaintsChart.toBase64Image();
                link.download = 'complaints_chart.png';
                link.click();
            }
        });
    </script>
</body>
</html>