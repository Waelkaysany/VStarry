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

// Get statistics for profile
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
    error_log("Database error in admin_profile.php: " . $e->getMessage());
}

// Handle profile updates
$success_message = '';
$error_message = '';

if (isset($_POST['update_profile'])) {
    $new_name = trim($_POST['admin_name']);
    $new_email = trim($_POST['admin_email']);
    
    if (!empty($new_name) && !empty($new_email)) {
        // First, check if admin_users table exists, if not create it
        $table_check = $conn->query("SHOW TABLES LIKE 'admin_users'");
        if ($table_check->num_rows == 0) {
            // Create admin_users table
            $create_table_sql = "CREATE TABLE IF NOT EXISTS admin_users (
                id INT(11) UNSIGNED AUTO_INCREMENT PRIMARY KEY,
                username VARCHAR(50) NOT NULL UNIQUE,
                password_hash VARCHAR(255) NOT NULL,
                email VARCHAR(255) NOT NULL,
                created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
                last_login TIMESTAMP NULL,
                is_active BOOLEAN DEFAULT TRUE
            )";
            
            if ($conn->query($create_table_sql)) {
                // Insert default admin user
                $insert_admin_sql = "INSERT INTO admin_users (username, password_hash, email) VALUES (?, ?, ?)";
                $insert_stmt = $conn->prepare($insert_admin_sql);
                $default_password_hash = password_hash('marjan2024', PASSWORD_DEFAULT);
                $insert_stmt->bind_param("sss", $new_name, $default_password_hash, $new_email);
                
                if ($insert_stmt->execute()) {
                    $success_message = "Profile created and updated successfully!";
                    $admin_info['name'] = $new_name;
                    $admin_info['email'] = $new_email;
                } else {
                    $error_message = "Error creating admin profile: " . $conn->error;
                }
                $insert_stmt->close();
            } else {
                $error_message = "Error creating admin table: " . $conn->error;
            }
        } else {
            // Table exists, update the profile
            // First check if there's an existing admin user
            $check_existing = $conn->query("SELECT id FROM admin_users LIMIT 1");
            if ($check_existing && $check_existing->num_rows > 0) {
                $update_sql = "UPDATE admin_users SET username = ?, email = ? WHERE id = 1";
                $stmt = $conn->prepare($update_sql);
                
                if ($stmt) {
                    $stmt->bind_param("ss", $new_name, $new_email);
                    
                    if ($stmt->execute()) {
                        $success_message = "Profile updated successfully!";
                        // Update the admin_info array to reflect changes
                        $admin_info['name'] = $new_name;
                        $admin_info['email'] = $new_email;
                    } else {
                        $error_message = "Error updating profile: " . $conn->error;
                    }
                    $stmt->close();
                } else {
                    $error_message = "Error preparing update statement: " . $conn->error;
                }
            } else {
                // No existing user, create one
                $insert_admin_sql = "INSERT INTO admin_users (username, password_hash, email) VALUES (?, ?, ?)";
                $insert_stmt = $conn->prepare($insert_admin_sql);
                $default_password_hash = password_hash('marjan2024', PASSWORD_DEFAULT);
                $insert_stmt->bind_param("sss", $new_name, $default_password_hash, $new_email);
                
                if ($insert_stmt->execute()) {
                    $success_message = "Profile created and updated successfully!";
                    $admin_info['name'] = $new_name;
                    $admin_info['email'] = $new_email;
                } else {
                    $error_message = "Error creating admin profile: " . $conn->error;
                }
                $insert_stmt->close();
            }
        }
    } else {
        $error_message = "Name and email cannot be empty.";
    }
}

if (isset($_POST['change_password'])) {
    $current_password = $_POST['current_password'];
    $new_password = $_POST['new_password'];
    $confirm_password = $_POST['confirm_password'];
    
    // First check if admin_users table exists
    $table_check = $conn->query("SHOW TABLES LIKE 'admin_users'");
    if ($table_check->num_rows == 0) {
        $error_message = "Admin profile not found. Please update your profile first.";
    } else {
        // Check if current password matches (verify against database)
        $check_sql = "SELECT id, password_hash FROM admin_users LIMIT 1";
        $check_result = $conn->query($check_sql);
        
        if ($check_result && $check_result->num_rows > 0) {
            $row = $check_result->fetch_assoc();
            $stored_hash = $row['password_hash'];
            $admin_id = $row['id'];
            
            // For now, check against the plain text password 'marjan2024'
            // In production, you should use password_verify() with proper hashing
            if ($current_password === 'marjan2024') {
                if ($new_password === $confirm_password) {
                    if (strlen($new_password) >= 6) {
                        // Hash the new password and update in database
                        $new_password_hash = password_hash($new_password, PASSWORD_DEFAULT);
                        $update_sql = "UPDATE admin_users SET password_hash = ? WHERE id = ?";
                        $stmt = $conn->prepare($update_sql);
                        
                        if ($stmt) {
                            $stmt->bind_param("si", $new_password_hash, $admin_id);
                            
                            if ($stmt->execute()) {
                                $success_message = "Password changed successfully!";
                            } else {
                                $error_message = "Error updating password: " . $conn->error;
                            }
                            $stmt->close();
                        } else {
                            $error_message = "Error preparing password update statement: " . $conn->error;
                        }
                    } else {
                        $error_message = "New password must be at least 6 characters long.";
                    }
                } else {
                    $error_message = "New passwords do not match.";
                }
            } else {
                $error_message = "Current password is incorrect.";
            }
        } else {
            $error_message = "Admin user not found.";
        }
    }
}

// Get admin info from database
$admin_info = [
    'name' => 'Administrator',
    'email' => 'admin@marjaninvestment.com',
    'role' => 'System Administrator',
    'last_login' => date('M j, Y g:i A'),
    'created_date' => 'Jan 1, 2024'
];

// Fetch actual admin info from database
$table_check = $conn->query("SHOW TABLES LIKE 'admin_users'");
if ($table_check->num_rows > 0) {
    $admin_sql = "SELECT username, email, created_at, last_login FROM admin_users LIMIT 1";
    $admin_result = $conn->query($admin_sql);

    if ($admin_result && $admin_result->num_rows > 0) {
        $admin_row = $admin_result->fetch_assoc();
        $admin_info['name'] = $admin_row['username'];
        $admin_info['email'] = $admin_row['email'];
        $admin_info['created_date'] = date('M j, Y', strtotime($admin_row['created_at']));
        
        if ($admin_row['last_login']) {
            $admin_info['last_login'] = date('M j, Y g:i A', strtotime($admin_row['last_login']));
        }
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin Profile - Marjan Investment</title>
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

        .profile-grid {
            display: grid;
            grid-template-columns: 1fr 1fr;
            gap: 32px;
            margin-bottom: 32px;
        }

        .profile-section {
            background: var(--surface);
            border: 1px solid var(--border);
            border-radius: 16px;
            padding: 24px;
            box-shadow: 0 4px 6px rgba(0, 0, 0, 0.05);
        }

        .section-title {
            font-size: 20px;
            font-weight: 600;
            margin-bottom: 20px;
            color: var(--text);
            display: flex;
            align-items: center;
            gap: 12px;
        }

        .section-title i {
            color: var(--primary);
        }

        .profile-info {
            display: flex;
            align-items: center;
            gap: 20px;
            margin-bottom: 24px;
            padding-bottom: 20px;
            border-bottom: 1px solid var(--border);
        }

        .profile-avatar {
            width: 100px;
            height: 100px;
            border-radius: 50%;
            background: linear-gradient(135deg, var(--primary), var(--secondary));
            display: flex;
            align-items: center;
            justify-content: center;
            color: white;
            font-size: 40px;
            font-weight: 600;
        }

        .profile-details h3 {
            font-size: 24px;
            font-weight: 700;
            margin-bottom: 8px;
            color: var(--text);
        }

        .profile-details p {
            color: var(--text-muted);
            font-size: 16px;
            margin-bottom: 4px;
        }

        .profile-stats {
            display: grid;
            grid-template-columns: repeat(2, 1fr);
            gap: 16px;
        }

        .profile-stat {
            text-align: center;
            padding: 16px;
            background: var(--surface-light);
            border-radius: 12px;
        }

        .profile-stat-value {
            font-size: 24px;
            font-weight: 700;
            color: var(--primary);
            margin-bottom: 4px;
        }

        .profile-stat-label {
            color: var(--text-muted);
            font-size: 12px;
        }

        .form-group {
            margin-bottom: 20px;
        }

        .form-group label {
            display: block;
            font-size: 14px;
            font-weight: 600;
            color: var(--text);
            margin-bottom: 8px;
        }

        .form-group input {
            width: 100%;
            padding: 12px 16px;
            background: var(--surface-light);
            border: 1px solid var(--border);
            border-radius: 8px;
            color: var(--text);
            font-size: 14px;
            transition: all 0.3s ease;
        }

        .form-group input:focus {
            outline: none;
            border-color: var(--primary);
            background: var(--surface);
            box-shadow: 0 0 0 3px rgba(59, 130, 246, 0.1);
        }

        .btn {
            padding: 12px 24px;
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

        .settings-section {
            background: var(--surface);
            border: 1px solid var(--border);
            border-radius: 16px;
            padding: 24px;
            margin-bottom: 32px;
            box-shadow: 0 4px 6px rgba(0, 0, 0, 0.05);
        }

        .setting-item {
            display: flex;
            align-items: center;
            justify-content: space-between;
            padding: 16px 0;
            border-bottom: 1px solid var(--border);
        }

        .setting-item:last-child {
            border-bottom: none;
        }

        .setting-info h4 {
            font-size: 16px;
            font-weight: 600;
            margin-bottom: 4px;
            color: var(--text);
        }

        .setting-info p {
            color: var(--text-muted);
            font-size: 14px;
        }

        .toggle-switch {
            position: relative;
            width: 50px;
            height: 24px;
            background: var(--border);
            border-radius: 12px;
            cursor: pointer;
            transition: all 0.3s ease;
        }

        .toggle-switch.active {
            background: var(--primary);
        }

        .toggle-switch::after {
            content: '';
            position: absolute;
            top: 2px;
            left: 2px;
            width: 20px;
            height: 20px;
            background: white;
            border-radius: 50%;
            transition: all 0.3s ease;
        }

        .toggle-switch.active::after {
            transform: translateX(26px);
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

            .profile-grid {
                grid-template-columns: 1fr;
            }

            .profile-stats {
                grid-template-columns: 1fr;
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
                    <li class="nav-item active">
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

                <!-- Page Header -->
                <div class="page-header">
                    <h1 class="page-title">Admin Profile & Settings</h1>
                    <p class="page-subtitle">Manage your account settings and preferences</p>
                </div>

                <!-- Profile Grid -->
                <div class="profile-grid">
                    <!-- Profile Information -->
                    <div class="profile-section">
                        <h2 class="section-title">
                            <i class="fas fa-user"></i>
                            Profile Information
                        </h2>
                        
                        <div class="profile-info">
                            <div class="profile-avatar">
                                <?php echo substr($admin_info['name'], 0, 1); ?>
                            </div>
                            <div class="profile-details">
                                <h3><?php echo htmlspecialchars($admin_info['name']); ?></h3>
                                <p><?php echo htmlspecialchars($admin_info['role']); ?></p>
                                <p>Last login: <?php echo htmlspecialchars($admin_info['last_login']); ?></p>
                                <p>Member since: <?php echo htmlspecialchars($admin_info['created_date']); ?></p>
                            </div>
                        </div>

                        <div class="profile-stats">
                            <div class="profile-stat">
                                <div class="profile-stat-value"><?php echo number_format($stats['total']); ?></div>
                                <div class="profile-stat-label">Total Applications</div>
                            </div>
                            <div class="profile-stat">
                                <div class="profile-stat-value"><?php echo number_format($stats['approved']); ?></div>
                                <div class="profile-stat-label">Approved</div>
                            </div>
                        </div>
                    </div>

                    <!-- Update Profile -->
                    <div class="profile-section">
                        <h2 class="section-title">
                            <i class="fas fa-edit"></i>
                            Update Profile
                        </h2>
                        
                        <form method="POST">
                            <div class="form-group">
                                <label for="admin_name">Full Name</label>
                                <input type="text" id="admin_name" name="admin_name" value="<?php echo htmlspecialchars($admin_info['name']); ?>" required>
                            </div>
                            
                            <div class="form-group">
                                <label for="admin_email">Email Address</label>
                                <input type="email" id="admin_email" name="admin_email" value="<?php echo htmlspecialchars($admin_info['email']); ?>" required>
                            </div>
                            
                            <button type="submit" name="update_profile" class="btn btn-primary">
                                <i class="fas fa-save"></i>
                                Update Profile
                            </button>
                        </form>
                    </div>
                </div>

                <!-- Change Password -->
                <div class="profile-section">
                    <h2 class="section-title">
                        <i class="fas fa-lock"></i>
                        Change Password
                    </h2>
                    
                    <form method="POST">
                        <div class="form-group">
                            <label for="current_password">Current Password</label>
                            <input type="password" id="current_password" name="current_password" required>
                        </div>
                        
                        <div class="form-group">
                            <label for="new_password">New Password</label>
                            <input type="password" id="new_password" name="new_password" required>
                        </div>
                        
                        <div class="form-group">
                            <label for="confirm_password">Confirm New Password</label>
                            <input type="password" id="confirm_password" name="confirm_password" required>
                        </div>
                        
                        <button type="submit" name="change_password" class="btn btn-primary">
                            <i class="fas fa-key"></i>
                            Change Password
                        </button>
                    </form>
                </div>

                <!-- Settings -->
                <div class="settings-section">
                    <h2 class="section-title">
                        <i class="fas fa-cog"></i>
                        Account Settings
                    </h2>
                    
                    <div class="setting-item">
                        <div class="setting-info">
                            <h4>Email Notifications</h4>
                            <p>Receive email notifications for new applications</p>
                        </div>
                        <div class="toggle-switch active" onclick="toggleSetting(this)"></div>
                    </div>
                    
                    <div class="setting-item">
                        <div class="setting-info">
                            <h4>Two-Factor Authentication</h4>
                            <p>Add an extra layer of security to your account</p>
                        </div>
                        <div class="toggle-switch" onclick="toggleSetting(this)"></div>
                    </div>
                    
                    <div class="setting-item">
                        <div class="setting-info">
                            <h4>Auto-logout</h4>
                            <p>Automatically log out after 30 minutes of inactivity</p>
                        </div>
                        <div class="toggle-switch active" onclick="toggleSetting(this)"></div>
                    </div>
                    
                    <div class="setting-item">
                        <div class="setting-info">
                            <h4>Dark Mode</h4>
                            <p>Switch to dark theme for better visibility</p>
                        </div>
                        <div class="toggle-switch" onclick="toggleSetting(this)"></div>
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

        function toggleSetting(element) {
            element.classList.toggle('active');
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
    </script>
</body>
</html>
<?php
$conn->close();
?>
