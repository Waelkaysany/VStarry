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
    <title>System Settings - Marjan Investment</title>
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

        .settings-section {
            background: var(--surface);
            border: 1px solid var(--border);
            border-radius: 16px;
            padding: 24px;
            margin-bottom: 24px;
            box-shadow: 0 4px 6px rgba(0, 0, 0, 0.05);
        }

        .section-title {
            font-size: 18px;
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

        .form-group input,
        .form-group select {
            width: 100%;
            padding: 12px 16px;
            background: var(--surface-light);
            border: 1px solid var(--border);
            border-radius: 8px;
            color: var(--text);
            font-size: 14px;
            transition: all 0.3s ease;
        }

        .form-group input:focus,
        .form-group select:focus {
            outline: none;
            border-color: var(--primary);
            background: var(--surface);
            box-shadow: 0 0 0 3px rgba(59, 130, 246, 0.1);
        }

        .settings-actions {
            display: flex;
            gap: 12px;
            margin-top: 24px;
            flex-wrap: wrap;
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

            .settings-actions {
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
                    <li class="nav-item">
                        <a href="admin_reports.php" class="nav-link">
                            <i class="fas fa-chart-bar"></i>
                            <span>Reports</span>
                        </a>
                    </li>
                    <li class="nav-item active">
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
                    <h1 class="page-title">System Settings</h1>
                    <p class="page-subtitle">Configure system preferences and application settings</p>
                </div>

                <!-- Notification Settings -->
                <div class="settings-section">
                    <h3 class="section-title">
                        <i class="fas fa-bell"></i>
                        Notification Settings
                    </h3>
                    
                    <div class="setting-item">
                        <div class="setting-info">
                            <h4>Email Notifications</h4>
                            <p>Send email notifications for new applications</p>
                        </div>
                        <div class="toggle-switch active" onclick="toggleSetting(this)"></div>
                    </div>
                    
                    <div class="setting-item">
                        <div class="setting-info">
                            <h4>SMS Notifications</h4>
                            <p>Send SMS alerts for urgent applications</p>
                        </div>
                        <div class="toggle-switch" onclick="toggleSetting(this)"></div>
                    </div>
                    
                    <div class="setting-item">
                        <div class="setting-info">
                            <h4>Dashboard Alerts</h4>
                            <p>Show real-time alerts on dashboard</p>
                        </div>
                        <div class="toggle-switch active" onclick="toggleSetting(this)"></div>
                    </div>
                </div>

                <!-- Security Settings -->
                <div class="settings-section">
                    <h3 class="section-title">
                        <i class="fas fa-shield-alt"></i>
                        Security Settings
                    </h3>
                    
                    <div class="setting-item">
                        <div class="setting-info">
                            <h4>Two-Factor Authentication</h4>
                            <p>Require 2FA for admin access</p>
                        </div>
                        <div class="toggle-switch" onclick="toggleSetting(this)"></div>
                    </div>
                    
                    <div class="setting-item">
                        <div class="setting-info">
                            <h4>Session Timeout</h4>
                            <p>Auto-logout after 30 minutes</p>
                        </div>
                        <div class="toggle-switch active" onclick="toggleSetting(this)"></div>
                    </div>
                    
                    <div class="setting-item">
                        <div class="setting-info">
                            <h4>IP Whitelist</h4>
                            <p>Restrict access to specific IP addresses</p>
                        </div>
                        <div class="toggle-switch" onclick="toggleSetting(this)"></div>
                    </div>
                </div>

                <!-- Appearance Settings -->
                <div class="settings-section">
                    <h3 class="section-title">
                        <i class="fas fa-palette"></i>
                        Appearance Settings
                    </h3>
                    
                    <div class="setting-item">
                        <div class="setting-info">
                            <h4>Dark Mode</h4>
                            <p>Switch to dark theme</p>
                        </div>
                        <div class="toggle-switch" onclick="toggleSetting(this)"></div>
                    </div>
                    
                    <div class="setting-item">
                        <div class="setting-info">
                            <h4>Compact View</h4>
                            <p>Use compact layout for applications</p>
                        </div>
                        <div class="toggle-switch" onclick="toggleSetting(this)"></div>
                    </div>
                </div>

                <!-- System Configuration -->
                <div class="settings-section">
                    <h3 class="section-title">
                        <i class="fas fa-server"></i>
                        System Configuration
                    </h3>
                    
                    <div class="form-group">
                        <label for="site_name">Site Name</label>
                        <input type="text" id="site_name" value="Marjan Investment" placeholder="Enter site name">
                    </div>
                    
                    <div class="form-group">
                        <label for="admin_email">Admin Email</label>
                        <input type="email" id="admin_email" value="admin@marjaninvestment.com" placeholder="Enter admin email">
                    </div>
                    
                    <div class="form-group">
                        <label for="timezone">Timezone</label>
                        <select id="timezone">
                            <option value="UTC">UTC</option>
                            <option value="America/New_York">Eastern Time</option>
                            <option value="America/Chicago">Central Time</option>
                            <option value="America/Denver">Mountain Time</option>
                            <option value="America/Los_Angeles">Pacific Time</option>
                        </select>
                    </div>
                    
                    <div class="settings-actions">
                        <button class="btn btn-primary" onclick="saveSettings()">
                            <i class="fas fa-save"></i>
                            Save Settings
                        </button>
                        <button class="btn btn-secondary" onclick="resetSettings()">
                            <i class="fas fa-undo"></i>
                            Reset to Default
                        </button>
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

        function saveSettings() {
            // In a real application, this would save settings to database
            alert('Settings saved successfully!');
        }

        function resetSettings() {
            if (confirm('Are you sure you want to reset all settings to default?')) {
                // In a real application, this would reset settings
                alert('Settings reset to default!');
            }
        }
    </script>
</body>
</html>
