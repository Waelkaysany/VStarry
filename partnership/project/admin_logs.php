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
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Activity Logs - Marjan Investment</title>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700;800&display=swap" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css" rel="stylesheet">
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

        .logs-section {
            background: var(--surface);
            border: 1px solid var(--border);
            border-radius: 16px;
            padding: 24px;
            margin-bottom: 32px;
            box-shadow: 0 4px 6px rgba(0, 0, 0, 0.05);
        }

        .logs-filters {
            display: flex;
            gap: 16px;
            margin-bottom: 24px;
            flex-wrap: wrap;
            align-items: end;
        }

        .filter-group {
            display: flex;
            flex-direction: column;
            gap: 8px;
        }

        .filter-group label {
            font-size: 14px;
            font-weight: 600;
            color: var(--text);
        }

        .filter-group select {
            padding: 8px 12px;
            border: 1px solid var(--border);
            border-radius: 8px;
            background: var(--surface-light);
            color: var(--text);
            min-width: 150px;
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

        .data-table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 20px;
        }

        .data-table th,
        .data-table td {
            padding: 12px;
            text-align: left;
            border-bottom: 1px solid var(--border);
        }

        .data-table th {
            background: var(--surface-light);
            font-weight: 600;
            color: var(--text);
            font-size: 14px;
        }

        .data-table td {
            color: var(--text);
            font-size: 14px;
        }

        .badge {
            padding: 4px 8px;
            border-radius: 12px;
            font-size: 12px;
            font-weight: 600;
            text-transform: uppercase;
        }

        .badge-success {
            background: rgba(16, 185, 129, 0.2);
            color: var(--success);
        }

        .badge-info {
            background: rgba(59, 130, 246, 0.2);
            color: var(--info);
        }

        .badge-warning {
            background: rgba(245, 158, 11, 0.2);
            color: var(--warning);
        }

        .badge-error {
            background: rgba(239, 68, 68, 0.2);
            color: var(--error);
        }

        .logs-summary {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(200px, 1fr));
            gap: 20px;
            margin-bottom: 32px;
        }

        .summary-card {
            background: var(--surface);
            border: 1px solid var(--border);
            border-radius: 16px;
            padding: 24px;
            text-align: center;
            transition: all 0.3s ease;
            box-shadow: 0 4px 6px rgba(0, 0, 0, 0.05);
        }

        .summary-card:hover {
            transform: translateY(-4px);
            box-shadow: 0 8px 32px rgba(0, 0, 0, 0.1);
        }

        .summary-icon {
            width: 60px;
            height: 60px;
            border-radius: 16px;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 24px;
            color: white;
            margin: 0 auto 20px;
        }

        .summary-icon.login { background: linear-gradient(135deg, var(--success), #059669); }
        .summary-icon.update { background: linear-gradient(135deg, var(--info), #2563EB); }
        .summary-icon.delete { background: linear-gradient(135deg, var(--warning), #D97706); }
        .summary-icon.export { background: linear-gradient(135deg, var(--primary), var(--primary-dark)); }

        .summary-value {
            font-size: 28px;
            font-weight: 700;
            margin-bottom: 8px;
            color: var(--text);
        }

        .summary-label {
            color: var(--text-muted);
            font-size: 14px;
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

            .logs-summary {
                grid-template-columns: 1fr;
            }

            .logs-filters {
                flex-direction: column;
            }

            .data-table {
                font-size: 12px;
            }

            .data-table th,
            .data-table td {
                padding: 8px;
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
                    <li class="nav-item">
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
                    <li class="nav-item active">
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
                    <h1 class="page-title">Activity Logs</h1>
                    <p class="page-subtitle">Track all system activities and admin actions</p>
                </div>

                <!-- Logs Summary -->
                <div class="logs-summary">
                    <div class="summary-card">
                        <div class="summary-icon login">
                            <i class="fas fa-sign-in-alt"></i>
                        </div>
                        <div class="summary-value">24</div>
                        <div class="summary-label">Login Attempts</div>
                    </div>
                    <div class="summary-card">
                        <div class="summary-icon update">
                            <i class="fas fa-edit"></i>
                        </div>
                        <div class="summary-value">156</div>
                        <div class="summary-label">Status Updates</div>
                    </div>
                    <div class="summary-card">
                        <div class="summary-icon delete">
                            <i class="fas fa-trash"></i>
                        </div>
                        <div class="summary-value">8</div>
                        <div class="summary-label">Deletions</div>
                    </div>
                    <div class="summary-card">
                        <div class="summary-icon export">
                            <i class="fas fa-download"></i>
                        </div>
                        <div class="summary-value">12</div>
                        <div class="summary-label">Exports</div>
                    </div>
                </div>

                <!-- Activity Logs Section -->
                <div class="logs-section">
                    <div class="logs-filters">
                        <div class="filter-group">
                            <label>Date Range</label>
                            <select id="dateFilter">
                                <option value="today">Today</option>
                                <option value="week">This Week</option>
                                <option value="month" selected>This Month</option>
                                <option value="year">This Year</option>
                            </select>
                        </div>
                        
                        <div class="filter-group">
                            <label>Action Type</label>
                            <select id="actionFilter">
                                <option value="">All Actions</option>
                                <option value="login">Login</option>
                                <option value="status_update">Status Update</option>
                                <option value="delete">Delete</option>
                                <option value="export">Export</option>
                            </select>
                        </div>
                        
                        <button class="btn btn-primary" onclick="exportLogs()">
                            <i class="fas fa-download"></i>
                            Export Logs
                        </button>
                    </div>

                    <div class="logs-table">
                        <table class="data-table">
                            <thead>
                                <tr>
                                    <th>Timestamp</th>
                                    <th>Action</th>
                                    <th>Details</th>
                                    <th>IP Address</th>
                                    <th>User Agent</th>
                                </tr>
                            </thead>
                            <tbody>
                                <tr>
                                    <td><?php echo date('M j, Y g:i A'); ?></td>
                                    <td><span class="badge badge-success">Login</span></td>
                                    <td>Admin logged in successfully</td>
                                    <td>192.168.1.1</td>
                                    <td>Chrome 120.0</td>
                                </tr>
                                <tr>
                                    <td><?php echo date('M j, Y g:i A', strtotime('-1 hour')); ?></td>
                                    <td><span class="badge badge-info">Status Update</span></td>
                                    <td>Application #123 status changed to Approved</td>
                                    <td>192.168.1.1</td>
                                    <td>Chrome 120.0</td>
                                </tr>
                                <tr>
                                    <td><?php echo date('M j, Y g:i A', strtotime('-2 hours')); ?></td>
                                    <td><span class="badge badge-warning">Export</span></td>
                                    <td>Applications exported to CSV</td>
                                    <td>192.168.0.1</td>
                                    <td>Chrome 120.0</td>
                                </tr>
                                <tr>
                                    <td><?php echo date('M j, Y g:i A', strtotime('-3 hours')); ?></td>
                                    <td><span class="badge badge-info">Status Update</span></td>
                                    <td>Application #124 status changed to Reviewed</td>
                                    <td>192.168.1.1</td>
                                    <td>Chrome 120.0</td>
                                </tr>
                                <tr>
                                    <td><?php echo date('M j, Y g:i A', strtotime('-4 hours')); ?></td>
                                    <td><span class="badge badge-error">Delete</span></td>
                                    <td>Application #125 deleted</td>
                                    <td>192.168.1.1</td>
                                    <td>Chrome 120.0</td>
                                </tr>
                                <tr>
                                    <td><?php echo date('M j, Y g:i A', strtotime('-5 hours')); ?></td>
                                    <td><span class="badge badge-success">Login</span></td>
                                    <td>Admin logged in successfully</td>
                                    <td>192.168.1.1</td>
                                    <td>Chrome 120.0</td>
                                </tr>
                            </tbody>
                        </table>
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

        function exportLogs() {
            const dateFilter = document.getElementById('dateFilter').value;
            const actionFilter = document.getElementById('actionFilter').value;
            
            // In a real application, this would generate and download a CSV/PDF file
            alert(`Exporting logs for ${dateFilter} with action type: ${actionFilter || 'All'}`);
        }

        // Filter logs based on selection
        document.addEventListener('DOMContentLoaded', function() {
            const dateFilter = document.getElementById('dateFilter');
            const actionFilter = document.getElementById('actionFilter');
            
            if (dateFilter) {
                dateFilter.addEventListener('change', function() {
                    // In a real application, this would filter the logs table
                    console.log('Date filter changed to:', this.value);
                });
            }
            
            if (actionFilter) {
                actionFilter.addEventListener('change', function() {
                    // In a real application, this would filter the logs table
                    console.log('Action filter changed to:', this.value);
                });
            }
        });
    </script>
</body>
</html>
