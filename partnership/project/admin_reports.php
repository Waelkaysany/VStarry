<?php
session_start();

// Handle logout
if (isset($_GET['logout'])) {
    session_destroy();
    header('Location: admin_login.php');
    exit;
}

if (!isset($_SESSION['admin_logged_in'])) {
    header('Location: admin_login.php');
    exit;
}

// Database connection
$servername = "localhost";
$username = "root";
$password = "";
$dbname = "marjanformul";

$conn = new mysqli($servername, $username, $password, $dbname);

if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

// Get statistics for reports
$stats = [
    'total' => 0,
    'pending' => 0,
    'reviewed' => 0,
    'approved' => 0,
    'rejected' => 0,
    'today' => 0
];

try {
    $stats_sql = "SELECT 
        COUNT(*) as total,
        SUM(CASE WHEN status = 'pending' THEN 1 ELSE 0 END) as pending,
        SUM(CASE WHEN status = 'reviewed' THEN 1 ELSE 0 END) as reviewed,
        SUM(CASE WHEN status = 'approved' THEN 1 ELSE 0 END) as approved,
        SUM(CASE WHEN status = 'rejected' THEN 1 ELSE 0 END) as rejected,
        SUM(CASE WHEN DATE(submission_date) = CURDATE() THEN 1 ELSE 0 END) as today
        FROM partnership_applications";
    $stats_result = $conn->query($stats_sql);
    if ($stats_result) {
        $stats = $stats_result->fetch_assoc();
    }
} catch (Exception $e) {
    // If there's an error, use default stats
    error_log("Database error in admin_reports.php: " . $e->getMessage());
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Reports & Analytics - Marjan Investment</title>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700;800&display=swap" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css" rel="stylesheet">
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }

        :root {
            --primary: #3B82F6;
            --primary-dark: #2563EB;
            --secondary: #10B981;
            --background: #F8FAFC;
            --surface: #FFFFFF;
            --surface-light: #F1F5F9;
            --text: #1E293B;
            --text-muted: #64748B;
            --border: #E2E8F0;
            --success: #10B981;
            --warning: #F59E0B;
            --error: #EF4444;
            --info: #3B82F6;
        }

        body {
            font-family: 'Inter', sans-serif;
            background-color: var(--background);
            color: var(--text);
            line-height: 1.6;
        }

        .admin-wrapper {
            display: flex;
            min-height: 100vh;
        }

        .sidebar {
            width: 280px;
            background: var(--surface);
            border-right: 1px solid var(--border);
            position: fixed;
            height: 100vh;
            z-index: 1000;
            overflow-y: auto;
            box-shadow: 2px 0 10px rgba(0, 0, 0, 0.1);
        }

        .sidebar-header {
            padding: 24px;
            border-bottom: 1px solid var(--border);
            background: linear-gradient(135deg, var(--primary), var(--secondary));
        }

        .logo {
            display: flex;
            align-items: center;
            gap: 16px;
        }

        .logo-icon {
            width: 48px;
            height: 48px;
            background: rgba(255, 255, 255, 0.2);
            border-radius: 12px;
            display: flex;
            align-items: center;
            justify-content: center;
            color: white;
            font-size: 20px;
            backdrop-filter: blur(10px);
        }

        .logo-text h2 {
            color: white;
            font-size: 24px;
            font-weight: 700;
        }

        .logo-text span {
            color: rgba(255, 255, 255, 0.8);
            font-size: 14px;
        }

        .sidebar-nav {
            padding: 24px 0;
        }

        .nav-list {
            list-style: none;
        }

        .nav-item {
            margin-bottom: 8px;
        }

        .nav-link {
            display: flex;
            align-items: center;
            gap: 16px;
            padding: 16px 24px;
            color: var(--text-muted);
            text-decoration: none;
            transition: all 0.3s ease;
            border-left: 3px solid transparent;
        }

        .nav-link:hover,
        .nav-item.active .nav-link {
            background: rgba(59, 130, 246, 0.1);
            color: var(--primary);
            border-left-color: var(--primary);
        }

        .nav-link i {
            width: 20px;
            text-align: center;
        }

        .sidebar-footer {
            position: absolute;
            bottom: 24px;
            left: 24px;
            right: 24px;
        }

        .logout-btn {
            display: flex;
            align-items: center;
            gap: 12px;
            padding: 16px;
            background: rgba(239, 68, 68, 0.1);
            color: var(--error);
            text-decoration: none;
            border-radius: 12px;
            transition: all 0.3s ease;
        }

        .logout-btn:hover {
            background: rgba(239, 68, 68, 0.2);
        }

        .main-content {
            flex: 1;
            margin-left: 280px;
            max-width: 1200px;
            margin-right: auto;
        }

        .top-header {
            background: var(--surface);
            border-bottom: 1px solid var(--border);
            padding: 20px 24px;
            display: flex;
            align-items: center;
            justify-content: space-between;
            flex-wrap: wrap;
            gap: 20px;
            box-shadow: 0 2px 10px rgba(0, 0, 0, 0.05);
        }

        .back-btn {
            display: flex;
            align-items: center;
            gap: 8px;
            padding: 12px 20px;
            background: var(--surface-light);
            color: var(--text);
            text-decoration: none;
            border-radius: 8px;
            transition: all 0.3s ease;
            border: 1px solid var(--border);
        }

        .back-btn:hover {
            background: var(--border);
        }

        .user-profile {
            display: flex;
            align-items: center;
            gap: 12px;
        }

        .avatar {
            width: 40px;
            height: 40px;
            border-radius: 50%;
        }

        .user-name {
            font-weight: 500;
        }

        .content-area {
            padding: 24px;
            max-width: 100%;
            margin: 0 auto;
        }

        .page-header {
            margin-bottom: 32px;
        }

        .page-title {
            font-size: 32px;
            font-weight: 700;
            margin-bottom: 8px;
            color: var(--text);
        }

        .page-subtitle {
            color: var(--text-muted);
            font-size: 16px;
        }

        .report-section {
            background: var(--surface);
            border: 1px solid var(--border);
            border-radius: 16px;
            padding: 24px;
            margin-bottom: 32px;
            box-shadow: 0 4px 6px rgba(0, 0, 0, 0.05);
        }

        .report-filters {
            display: flex;
            gap: 16px;
            margin-bottom: 24px;
            flex-wrap: wrap;
        }

        .report-filter {
            display: flex;
            flex-direction: column;
            gap: 8px;
        }

        .report-filter label {
            font-size: 14px;
            font-weight: 600;
            color: var(--text);
        }

        .report-filter select,
        .report-filter input {
            padding: 8px 12px;
            border: 1px solid var(--border);
            border-radius: 8px;
            background: var(--surface-light);
            color: var(--text);
        }

        .report-actions {
            display: flex;
            gap: 12px;
            margin-bottom: 24px;
            flex-wrap: wrap;
        }

        .btn {
            padding: 12px 20px;
            border: none;
            border-radius: 8px;
            font-size: 14px;
            font-weight: 500;
            cursor: pointer;
            transition: all 0.3s ease;
            display: inline-flex;
            align-items: center;
            gap: 8px;
            text-decoration: none;
        }

        .btn-primary {
            background: linear-gradient(135deg, var(--primary), var(--primary-dark));
            color: white;
        }

        .btn-primary:hover {
            transform: translateY(-2px);
            box-shadow: 0 4px 12px rgba(59, 130, 246, 0.3);
        }

        .btn-secondary {
            background: var(--surface-light);
            color: var(--text);
            border: 1px solid var(--border);
        }

        .btn-secondary:hover {
            background: var(--border);
        }

        .chart-container {
            background: var(--surface-light);
            border-radius: 12px;
            padding: 20px;
            margin-bottom: 24px;
            max-width: 500px;
            margin-left: auto;
            margin-right: auto;
        }

        .chart-title {
            font-size: 16px;
            font-weight: 600;
            margin-bottom: 16px;
            color: var(--text);
            text-align: center;
        }

        .chart-wrapper {
            position: relative;
            height: 300px;
            width: 100%;
            display: flex;
            align-items: center;
            justify-content: center;
        }

        .stats-summary {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(200px, 1fr));
            gap: 20px;
            margin-bottom: 32px;
        }

        .stat-card {
            background: var(--surface);
            border: 1px solid var(--border);
            border-radius: 16px;
            padding: 24px;
            text-align: center;
            transition: all 0.3s ease;
            box-shadow: 0 4px 6px rgba(0, 0, 0, 0.05);
        }

        .stat-card:hover {
            transform: translateY(-4px);
            box-shadow: 0 8px 32px rgba(0, 0, 0, 0.1);
        }

        .stat-value {
            font-size: 36px;
            font-weight: 700;
            margin-bottom: 8px;
            color: var(--primary);
        }

        .stat-label {
            color: var(--text-muted);
            font-size: 16px;
        }

        @media (max-width: 768px) {
            .sidebar {
                transform: translateX(-100%);
                transition: transform 0.3s ease;
            }

            .sidebar.open {
                transform: translateX(0);
            }

            .main-content {
                margin-left: 0;
                max-width: 100%;
            }

            .stats-summary {
                grid-template-columns: 1fr;
            }

            .report-filters {
                flex-direction: column;
            }
        }

        .mobile-menu-toggle {
            display: none;
            background: var(--primary);
            color: white;
            border: none;
            padding: 12px;
            border-radius: 8px;
            cursor: pointer;
        }

        @media (max-width: 768px) {
            .mobile-menu-toggle {
                display: block;
            }
        }
    </style>
</head>
<body>
    <div class="admin-wrapper">
        <!-- Sidebar -->
        <aside class="sidebar" id="sidebar">
            <div class="sidebar-header">
                <div class="logo">
                    <div class="logo-icon">
                        <i class="fas fa-building"></i>
                    </div>
                    <div class="logo-text">
                        <h2>Marjan</h2>
                        <span>Admin Panel</span>
                    </div>
                </div>
            </div>

            <nav class="sidebar-nav">
                <ul class="nav-list">
                    <li class="nav-item">
                        <a href="admin_panel.php" class="nav-link">
                            <i class="fas fa-chart-line"></i>
                            <span>Dashboard</span>
                        </a>
                    </li>
                    <li class="nav-item">
                        <a href="admin_panel.php#applications" class="nav-link">
                            <i class="fas fa-users"></i>
                            <span>Applications</span>
                        </a>
                    </li>
                    <li class="nav-item">
                        <a href="admin_profile.php" class="nav-link">
                            <i class="fas fa-user-circle"></i>
                            <span>Profile</span>
                        </a>
                    </li>
                    <li class="nav-item active">
                        <a href="admin_reports.php" class="nav-link">
                            <i class="fas fa-chart-bar"></i>
                            <span>Reports</span>
                        </a>
                    </li>
                    <li class="nav-item">
                        <a href="admin_settings.php" class="nav-link">
                            <i class="fas fa-cog"></i>
                            <span>Settings</span>
                        </a>
                    </li>
                    <li class="nav-item">
                        <a href="admin_logs.php" class="nav-link">
                            <i class="fas fa-history"></i>
                            <span>Activity Logs</span>
                        </a>
                    </li>
                </ul>
            </nav>

            <div class="sidebar-footer">
                <a href="?logout=1" class="logout-btn">
                    <i class="fas fa-sign-out-alt"></i>
                    <span>Logout</span>
                </a>
            </div>
        </aside>

        <!-- Main Content -->
        <main class="main-content">
            <!-- Top Header -->
            <header class="top-header">
                <button class="mobile-menu-toggle" onclick="toggleSidebar()">
                    <i class="fas fa-bars"></i>
                </button>

                <a href="admin_panel.php" class="back-btn">
                    <i class="fas fa-arrow-left"></i>
                    Back to Dashboard
                </a>

                <div class="user-profile">
                    <img src="https://ui-avatars.com/api/?name=Admin&background=3B82F6&color=fff" alt="Admin" class="avatar">
                    <span class="user-name">Admin</span>
                </div>
            </header>

            <!-- Content Area -->
            <div class="content-area">
                <!-- Page Header -->
                <div class="page-header">
                    <h1 class="page-title">Reports & Analytics</h1>
                    <p class="page-subtitle">Generate detailed reports and view analytics</p>
                </div>

                <!-- Statistics Summary -->
                <div class="stats-summary">
                    <div class="stat-card">
                        <div class="stat-value"><?php echo number_format($stats['total']); ?></div>
                        <div class="stat-label">Total Applications</div>
                    </div>
                    <div class="stat-card">
                        <div class="stat-value"><?php echo number_format($stats['pending']); ?></div>
                        <div class="stat-label">Pending</div>
                    </div>
                    <div class="stat-card">
                        <div class="stat-value"><?php echo number_format($stats['approved']); ?></div>
                        <div class="stat-label">Approved</div>
                    </div>
                    <div class="stat-card">
                        <div class="stat-value"><?php echo number_format($stats['today']); ?></div>
                        <div class="stat-label">Today's Applications</div>
                    </div>
                </div>

                <!-- Reports Section -->
                <div class="report-section">
                    <div class="report-filters">
                        <div class="report-filter">
                            <label>Date Range</label>
                            <select>
                                <option>Last 7 days</option>
                                <option>Last 30 days</option>
                                <option>Last 3 months</option>
                                <option>Last year</option>
                                <option>Custom range</option>
                            </select>
                        </div>
                        <div class="report-filter">
                            <label>Status</label>
                            <select>
                                <option>All Status</option>
                                <option>Pending</option>
                                <option>Reviewed</option>
                                <option>Approved</option>
                                <option>Rejected</option>
                            </select>
                        </div>
                        <div class="report-filter">
                            <label>Export Format</label>
                            <select>
                                <option>PDF</option>
                                <option>Excel</option>
                                <option>CSV</option>
                            </select>
                        </div>
                    </div>

                    <div class="report-actions">
                        <button class="btn btn-primary">
                            <i class="fas fa-download"></i>
                            Generate Report
                        </button>
                        <button class="btn btn-secondary">
                            <i class="fas fa-chart-line"></i>
                            View Analytics
                        </button>
                    </div>

                    <div class="chart-container">
                        <div class="chart-title">Application Status Distribution</div>
                        <div class="chart-wrapper">
                            <canvas id="statusChart" width="300" height="300"></canvas>
                        </div>
                    </div>

                    <div class="chart-container">
                        <div class="chart-title">Monthly Application Trends</div>
                        <div class="chart-wrapper">
                            <canvas id="trendChart" width="300" height="300"></canvas>
                        </div>
                    </div>
                </div>
            </div>
        </main>
    </div>

    <script>
        function toggleSidebar() {
            const sidebar = document.getElementById('sidebar');
            sidebar.classList.toggle('open');
        }

        // Initialize charts
        document.addEventListener('DOMContentLoaded', function() {
            // Status Distribution Chart
            const statusCtx = document.getElementById('statusChart').getContext('2d');
            new Chart(statusCtx, {
                type: 'doughnut',
                data: {
                    labels: ['Pending', 'Reviewed', 'Approved', 'Rejected'],
                    datasets: [{
                        data: [
                            <?php echo $stats['pending']; ?>,
                            <?php echo $stats['reviewed']; ?>,
                            <?php echo $stats['approved']; ?>,
                            <?php echo $stats['rejected']; ?>
                        ],
                        backgroundColor: [
                            '#F59E0B',
                            '#3B82F6',
                            '#10B981',
                            '#EF4444'
                        ],
                        borderWidth: 2,
                        borderColor: '#ffffff'
                    }]
                },
                options: {
                    responsive: true,
                    maintainAspectRatio: false,
                    plugins: {
                        legend: {
                            position: 'bottom',
                            labels: {
                                padding: 15,
                                usePointStyle: true,
                                font: {
                                    size: 12
                                }
                            }
                        }
                    },
                    cutout: '60%'
                }
            });

            // Trend Chart
            const trendCtx = document.getElementById('trendChart').getContext('2d');
            new Chart(trendCtx, {
                type: 'line',
                data: {
                    labels: ['Jan', 'Feb', 'Mar', 'Apr', 'May', 'Jun'],
                    datasets: [{
                        label: 'Applications',
                        data: [12, 19, 15, 25, 22, 30],
                        borderColor: '#3B82F6',
                        backgroundColor: 'rgba(59, 130, 246, 0.1)',
                        tension: 0.4,
                        borderWidth: 3,
                        pointBackgroundColor: '#3B82F6',
                        pointBorderColor: '#ffffff',
                        pointBorderWidth: 2,
                        pointRadius: 6,
                        pointHoverRadius: 8
                    }]
                },
                options: {
                    responsive: true,
                    maintainAspectRatio: false,
                    plugins: {
                        legend: {
                            position: 'bottom',
                            labels: {
                                padding: 15,
                                usePointStyle: true,
                                font: {
                                    size: 12
                                }
                            }
                        }
                    },
                    scales: {
                        y: {
                            beginAtZero: true,
                            grid: {
                                color: 'rgba(0, 0, 0, 0.1)'
                            }
                        },
                        x: {
                            grid: {
                                color: 'rgba(0, 0, 0, 0.1)'
                            }
                        }
                    }
                }
            });
        });
    </script>
</body>
</html>
<?php
$conn->close();
?>
