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

// Handle status updates
if (isset($_POST['update_status'])) {
    $app_id = $_POST['app_id'];
    $new_status = $_POST['new_status'];
    
    $update_sql = "UPDATE partnership_applications SET status = ? WHERE id = ?";
    $stmt = $conn->prepare($update_sql);
    $stmt->bind_param("si", $new_status, $app_id);
    
    if ($stmt->execute()) {
        $success_message = "Status updated successfully!";
    } else {
        $error_message = "Error updating status.";
    }
    $stmt->close();
}

// Handle application deletion
if (isset($_POST['delete_app'])) {
    $app_id = $_POST['app_id'];
    
    $delete_sql = "DELETE FROM partnership_applications WHERE id = ?";
    $stmt = $conn->prepare($delete_sql);
    $stmt->bind_param("i", $app_id);
    
    if ($stmt->execute()) {
        $success_message = "Application deleted successfully!";
    } else {
        $error_message = "Error deleting application.";
    }
    $stmt->close();
}

// Get statistics
$stats_sql = "SELECT 
    COUNT(*) as total,
    SUM(CASE WHEN status = 'pending' THEN 1 ELSE 0 END) as pending,
    SUM(CASE WHEN status = 'reviewed' THEN 1 ELSE 0 END) as reviewed,
    SUM(CASE WHEN status = 'approved' THEN 1 ELSE 0 END) as approved,
    SUM(CASE WHEN status = 'rejected' THEN 1 ELSE 0 END) as rejected,
    SUM(CASE WHEN DATE(submission_date) = CURDATE() THEN 1 ELSE 0 END) as today
    FROM partnership_applications";
$stats_result = $conn->query($stats_sql);
$stats = $stats_result->fetch_assoc();

// Get applications with search and filter
$search = isset($_GET['search']) ? $_GET['search'] : '';
$status_filter = isset($_GET['status']) ? $_GET['status'] : '';

$where_conditions = [];
$params = [];
$types = '';

if (!empty($search)) {
    $where_conditions[] = "(name LIKE ? OR email LIKE ? OR phone LIKE ?)";
    $search_param = "%$search%";
    $params[] = $search_param;
    $params[] = $search_param;
    $params[] = $search_param;
    $types .= 'sss';
}

if (!empty($status_filter)) {
    $where_conditions[] = "status = ?";
    $params[] = $status_filter;
    $types .= 's';
}

$where_clause = !empty($where_conditions) ? 'WHERE ' . implode(' AND ', $where_conditions) : '';

$sql = "SELECT * FROM partnership_applications $where_clause ORDER BY submission_date DESC";
$stmt = $conn->prepare($sql);

if (!empty($params)) {
    $stmt->bind_param($types, ...$params);
}

$stmt->execute();
$result = $stmt->get_result();
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin Panel - Marjan Investment</title>
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

        .search-filters {
            display: flex;
            align-items: center;
            gap: 16px;
            flex-wrap: wrap;
        }

        .search-box {
            position: relative;
            min-width: 300px;
        }

        .search-icon {
            position: absolute;
            left: 16px;
            top: 50%;
            transform: translateY(-50%);
            color: var(--text-muted);
        }

        .search-input {
            width: 100%;
            padding: 12px 16px 12px 48px;
            background: var(--surface-light);
            border: 1px solid var(--border);
            border-radius: 12px;
            color: var(--text);
            font-size: 14px;
        }

        .search-input:focus {
            outline: none;
            border-color: var(--primary);
            background: var(--surface);
        }

        .filter-select {
            padding: 12px 16px;
            background: var(--surface-light);
            border: 1px solid var(--border);
            border-radius: 12px;
            color: var(--text);
            font-size: 14px;
            min-width: 150px;
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

        .stats-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(250px, 1fr));
            gap: 24px;
            margin-bottom: 32px;
        }

        .stat-card {
            background: var(--surface);
            border: 1px solid var(--border);
            border-radius: 16px;
            padding: 24px;
            transition: all 0.3s ease;
            box-shadow: 0 4px 6px rgba(0, 0, 0, 0.05);
        }

        .stat-card:hover {
            transform: translateY(-4px);
            box-shadow: 0 8px 32px rgba(0, 0, 0, 0.1);
        }

        .stat-icon {
            width: 60px;
            height: 60px;
            border-radius: 16px;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 24px;
            color: white;
            margin-bottom: 20px;
        }

        .stat-icon.primary { background: linear-gradient(135deg, var(--primary), var(--primary-dark)); }
        .stat-icon.success { background: linear-gradient(135deg, var(--success), #059669); }
        .stat-icon.warning { background: linear-gradient(135deg, var(--warning), #D97706); }
        .stat-icon.info { background: linear-gradient(135deg, var(--info), #2563EB); }

        .stat-value {
            font-size: 36px;
            font-weight: 700;
            margin-bottom: 8px;
            color: var(--text);
        }

        .stat-label {
            color: var(--text-muted);
            font-size: 16px;
            margin-bottom: 16px;
        }

        .applications-section {
            background: var(--surface);
            border: 1px solid var(--border);
            border-radius: 16px;
            overflow: hidden;
            box-shadow: 0 4px 6px rgba(0, 0, 0, 0.05);
        }

        .applications-header {
            padding: 24px;
            border-bottom: 1px solid var(--border);
            display: flex;
            align-items: center;
            justify-content: space-between;
            flex-wrap: wrap;
            gap: 16px;
        }

        .applications-header h3 {
            font-size: 20px;
            font-weight: 600;
            color: var(--text);
        }

        .applications-actions {
            display: flex;
            align-items: center;
            gap: 16px;
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

        .btn-sm {
            padding: 8px 16px;
            font-size: 12px;
        }

        .btn-danger {
            background: rgba(239, 68, 68, 0.1);
            color: var(--error);
            border: 1px solid rgba(239, 68, 68, 0.3);
        }

        .btn-danger:hover {
            background: rgba(239, 68, 68, 0.2);
        }

        .application-card {
            border-bottom: 1px solid var(--border);
            padding: 24px;
            transition: all 0.3s ease;
        }

        .application-card:hover {
            background: var(--surface-light);
        }

        .application-card:last-child {
            border-bottom: none;
        }

        .card-header {
            display: flex;
            align-items: flex-start;
            justify-content: space-between;
            margin-bottom: 20px;
            flex-wrap: wrap;
            gap: 16px;
        }

        .applicant-info h4 {
            font-size: 18px;
            font-weight: 600;
            margin-bottom: 8px;
            color: var(--text);
        }

        .application-id {
            color: var(--text-muted);
            font-size: 14px;
            margin-bottom: 12px;
        }

        .application-meta {
            display: flex;
            gap: 16px;
            flex-wrap: wrap;
        }

        .meta-item {
            display: flex;
            align-items: center;
            gap: 6px;
            color: var(--text-muted);
            font-size: 14px;
        }

        .status-badge {
            padding: 8px 16px;
            border-radius: 20px;
            font-size: 12px;
            font-weight: 600;
            text-transform: uppercase;
        }

        .status-pending {
            background: rgba(245, 158, 11, 0.2);
            color: var(--warning);
        }

        .status-reviewed {
            background: rgba(59, 130, 246, 0.2);
            color: var(--info);
        }

        .status-approved {
            background: rgba(16, 185, 129, 0.2);
            color: var(--success);
        }

        .status-rejected {
            background: rgba(239, 68, 68, 0.2);
            color: var(--error);
        }

        .details-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(200px, 1fr));
            gap: 20px;
            margin-bottom: 20px;
        }

        .detail-item label {
            display: block;
            font-size: 12px;
            font-weight: 600;
            color: var(--text-muted);
            text-transform: uppercase;
            margin-bottom: 6px;
        }

        .detail-item span {
            display: flex;
            align-items: center;
            gap: 8px;
            font-size: 14px;
            color: var(--text);
        }

        .source-tag {
            background: var(--surface-light);
            padding: 4px 12px;
            border-radius: 12px;
            font-size: 12px;
        }

        .reason-section {
            background: rgba(59, 130, 246, 0.05);
            border: 1px solid rgba(59, 130, 246, 0.2);
            border-radius: 12px;
            padding: 16px;
            margin-bottom: 20px;
        }

        .reason-section label {
            display: block;
            font-size: 12px;
            font-weight: 600;
            color: var(--primary);
            text-transform: uppercase;
            margin-bottom: 8px;
        }

        .card-footer {
            display: flex;
            align-items: center;
            justify-content: space-between;
            gap: 16px;
            padding-top: 20px;
            border-top: 1px solid var(--border);
            flex-wrap: wrap;
        }

        .card-actions {
            display: flex;
            gap: 8px;
            flex-wrap: wrap;
        }

        .status-update {
            display: flex;
            align-items: center;
            gap: 12px;
            flex-wrap: wrap;
        }

        .status-select {
            padding: 8px 12px;
            background: var(--surface-light);
            border: 1px solid var(--border);
            border-radius: 8px;
            color: var(--text);
            font-size: 14px;
        }

        .empty-state {
            text-align: center;
            padding: 60px 20px;
            color: var(--text-muted);
        }

        .empty-state i {
            font-size: 48px;
            margin-bottom: 16px;
            opacity: 0.5;
        }

        .message {
            padding: 16px;
            border-radius: 12px;
            margin-bottom: 24px;
            font-size: 14px;
        }

        .message.success {
            background: rgba(16, 185, 129, 0.1);
            border: 1px solid rgba(16, 185, 129, 0.3);
            color: #059669;
        }

        .message.error {
            background: rgba(239, 68, 68, 0.1);
            border: 1px solid rgba(239, 68, 68, 0.3);
            color: #DC2626;
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

            .stats-grid {
                grid-template-columns: 1fr;
            }

            .details-grid {
                grid-template-columns: 1fr;
            }

            .card-footer {
                flex-direction: column;
                align-items: stretch;
            }

            .search-filters {
                flex-direction: column;
                align-items: stretch;
            }

            .search-box {
                min-width: auto;
            }

            .profile-header {
                flex-direction: column;
                text-align: center;
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
                    <li class="nav-item active">
                        <a href="#dashboard" class="nav-link">
                            <i class="fas fa-chart-line"></i>
                            <span>Dashboard</span>
                        </a>
                    </li>
                    <li class="nav-item">
                        <a href="#applications" class="nav-link">
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

                <div class="search-filters">
                    <form method="GET" class="search-box">
                        <i class="fas fa-search search-icon"></i>
                        <input 
                            type="text" 
                            class="search-input" 
                            name="search"
                            placeholder="Search applications..."
                            value="<?php echo htmlspecialchars($search); ?>"
                        >
                    </form>

                    <select name="status" class="filter-select" onchange="this.form.submit()">
                        <option value="">All Status</option>
                        <option value="pending" <?php echo $status_filter === 'pending' ? 'selected' : ''; ?>>Pending</option>
                        <option value="reviewed" <?php echo $status_filter === 'reviewed' ? 'selected' : ''; ?>>Reviewed</option>
                        <option value="approved" <?php echo $status_filter === 'approved' ? 'selected' : ''; ?>>Approved</option>
                        <option value="rejected" <?php echo $status_filter === 'rejected' ? 'selected' : ''; ?>>Rejected</option>
                    </select>
                </form>
                </div>

                <div class="user-profile">
                    <img src="https://ui-avatars.com/api/?name=Admin&background=3B82F6&color=fff" alt="Admin" class="avatar">
                    <span class="user-name">Admin</span>
                </div>
            </header>

            <!-- Content Area -->
            <div class="content-area">
                <!-- Messages -->
                <?php if (isset($success_message)): ?>
                    <div class="message success">
                        <i class="fas fa-check-circle"></i>
                        <?php echo htmlspecialchars($success_message); ?>
                    </div>
                <?php endif; ?>

                <?php if (isset($error_message)): ?>
                    <div class="message error">
                        <i class="fas fa-exclamation-triangle"></i>
                        <?php echo htmlspecialchars($error_message); ?>
                    </div>
                <?php endif; ?>

                <!-- Dashboard Section -->
                <div id="dashboard">
                    <div class="page-header">
                        <h1 class="page-title">Dashboard Overview</h1>
                        <p class="page-subtitle">Welcome back! Here's what's happening with your investment applications.</p>
                    </div>

                    <!-- Stats Cards -->
                    <div class="stats-grid">
                        <div class="stat-card">
                            <div class="stat-icon primary">
                                <i class="fas fa-file-alt"></i>
                            </div>
                            <div class="stat-value"><?php echo number_format($stats['total']); ?></div>
                            <div class="stat-label">Total Applications</div>
                        </div>

                        <div class="stat-card">
                            <div class="stat-icon warning">
                                <i class="fas fa-clock"></i>
                            </div>
                            <div class="stat-value"><?php echo number_format($stats['pending']); ?></div>
                            <div class="stat-label">Pending Reviews</div>
                        </div>

                        <div class="stat-card">
                            <div class="stat-icon success">
                                <i class="fas fa-check-circle"></i>
                            </div>
                            <div class="stat-value"><?php echo number_format($stats['approved']); ?></div>
                            <div class="stat-label">Approved</div>
                        </div>

                        <div class="stat-card">
                            <div class="stat-icon info">
                                <i class="fas fa-calendar-day"></i>
                            </div>
                            <div class="stat-value"><?php echo number_format($stats['today']); ?></div>
                            <div class="stat-label">Today's Applications</div>
                        </div>
                    </div>
                </div>

                

                

                 

                 

                 <!-- Applications Section -->
                <div id="applications">
                    <div class="page-header">
                        <h1 class="page-title">Applications Management</h1>
                        <p class="page-subtitle">Manage and review investment partnership applications</p>
                    </div>

                    <!-- Applications List -->
                    <div class="applications-section">
                        <div class="applications-header">
                            <h3>Applications (<?php echo $result->num_rows; ?>)</h3>
                            <div class="applications-actions">
                                <a href="admin_panel.php" class="btn btn-secondary">
                                    <i class="fas fa-refresh"></i>
                                    Refresh
                                </a>
                            </div>
                        </div>

                        <div class="applications-list">
                            <?php if ($result->num_rows > 0): ?>
                                <?php while($row = $result->fetch_assoc()): ?>
                                    <div class="application-card">
                                        <div class="card-header">
                                            <div class="applicant-info">
                                                <h4><?php echo htmlspecialchars($row['name']); ?></h4>
                                                <div class="application-id">ID: #<?php echo $row['id']; ?></div>
                                                
                                                <div class="application-meta">
                                                    <span class="meta-item">
                                                        <i class="fas fa-calendar"></i>
                                                        <?php echo date('M j, Y', strtotime($row['submission_date'])); ?>
                                                    </span>
                                                    <span class="meta-item">
                                                        <i class="fas fa-clock"></i>
                                                        <?php echo date('g:i A', strtotime($row['submission_date'])); ?>
                                                    </span>
                                                </div>
                                            </div>

                                            <div class="status-badge status-<?php echo $row['status']; ?>">
                                                <?php echo ucfirst($row['status']); ?>
                                            </div>
                                        </div>

                                        <div class="card-body">
                                            <div class="details-grid">
                                                <div class="detail-item">
                                                    <label>Email Address</label>
                                                    <span>
                                                        <i class="fas fa-envelope"></i>
                                                        <?php echo htmlspecialchars($row['email']); ?>
                                                    </span>
                                                </div>

                                                <div class="detail-item">
                                                    <label>Phone Number</label>
                                                    <span>
                                                        <i class="fas fa-phone"></i>
                                                        <?php echo htmlspecialchars($row['phone']); ?>
                                                    </span>
                                                </div>

                                                <div class="detail-item">
                                                    <label>Investment Budget</label>
                                                    <span>
                                                        <i class="fas fa-dollar-sign"></i>
                                                        <?php echo htmlspecialchars($row['budget']); ?>
                                                    </span>
                                                </div>

                                                <div class="detail-item">
                                                    <label>Source</label>
                                                    <span class="source-tag"><?php echo htmlspecialchars($row['source']); ?></span>
                                                </div>
                                            </div>

                                            <div class="reason-section">
                                                <label>Why they chose to partner</label>
                                                <p><?php echo nl2br(htmlspecialchars($row['reason'])); ?></p>
                                            </div>
                                        </div>

                                        <div class="card-footer">
                                            <div class="status-update">
                                                <form method="POST" style="display: flex; align-items: center; gap: 12px;">
                                                    <input type="hidden" name="app_id" value="<?php echo $row['id']; ?>">
                                                    <select name="new_status" class="status-select">
                                                        <option value="pending" <?php echo $row['status'] === 'pending' ? 'selected' : ''; ?>>Pending</option>
                                                        <option value="reviewed" <?php echo $row['status'] === 'reviewed' ? 'selected' : ''; ?>>Reviewed</option>
                                                        <option value="approved" <?php echo $row['status'] === 'approved' ? 'selected' : ''; ?>>Approved</option>
                                                        <option value="rejected" <?php echo $row['status'] === 'rejected' ? 'selected' : ''; ?>>Rejected</option>
                                                    </select>
                                                    <button type="submit" name="update_status" class="btn btn-sm btn-primary">
                                                        <i class="fas fa-save"></i>
                                                        Update Status
                                                    </button>
                                                </form>
                                            </div>

                                            <div class="card-actions">
                                                <form method="POST" style="display: inline;" onsubmit="return confirm('Are you sure you want to delete this application?')">
                                                    <input type="hidden" name="app_id" value="<?php echo $row['id']; ?>">
                                                    <button type="submit" name="delete_app" class="btn btn-sm btn-danger">
                                                        <i class="fas fa-trash"></i>
                                                        Delete
                                                    </button>
                                                </form>
                                            </div>
                                        </div>
                                    </div>
                                <?php endwhile; ?>
                            <?php else: ?>
                                <div class="empty-state">
                                    <i class="fas fa-inbox"></i>
                                    <h3>No Applications Found</h3>
                                    <p>No partnership applications found matching your criteria.</p>
                                </div>
                            <?php endif; ?>
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

        // Auto-hide messages after 5 seconds
        setTimeout(function() {
            const messages = document.querySelectorAll('.message');
            messages.forEach(function(message) {
                message.style.opacity = '0';
                setTimeout(function() {
                    message.remove();
                }, 300);
            });
        }, 5000);

        // Add smooth scrolling for navigation (only for internal links)
        document.querySelectorAll('.nav-link').forEach(link => {
            link.addEventListener('click', function(e) {
                const href = this.getAttribute('href');
                
                // Only prevent default for internal links (starting with #)
                if (href.startsWith('#')) {
                    e.preventDefault();
                    
                    // Remove active class from all nav items
                    document.querySelectorAll('.nav-item').forEach(item => item.classList.remove('active'));
                    
                    // Add active class to clicked nav item
                    this.parentElement.classList.add('active');
                    
                    // Show corresponding section
                    const targetId = href.substring(1);
                    const targetSection = document.getElementById(targetId);
                    
                    if (targetSection) {
                        targetSection.scrollIntoView({ behavior: 'smooth' });
                    }
                }
                // External links (like admin_profile.php) will work normally
            });
        });

        
     </script>
</body>
</html>
<?php
$stmt->close();
$conn->close();
?>
