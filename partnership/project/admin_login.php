<?php
session_start();

// Database connection
$servername = "localhost";
$username = "root";
$password = "";
$dbname = "marjanformul";

$conn = new mysqli($servername, $username, $password, $dbname);

if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

$error = "";

if (isset($_POST['login'])) {
    $input_username = trim($_POST['username']);
    $input_password = $_POST['password'];
    
    if (!empty($input_username) && !empty($input_password)) {
        // First check if admin_users table exists
        $table_check = $conn->query("SHOW TABLES LIKE 'admin_users'");
        
        if ($table_check->num_rows == 0) {
            // Table doesn't exist, create it and insert default admin
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
                $insert_stmt->bind_param("sss", $input_username, $default_password_hash, 'admin@marjan.com');
                
                if ($insert_stmt->execute()) {
                    // Now login with the created user
                    $_SESSION['admin_logged_in'] = true;
                    $_SESSION['admin_id'] = $conn->insert_id;
                    $_SESSION['admin_username'] = $input_username;
                    
                    // Update last login timestamp
                    $update_sql = "UPDATE admin_users SET last_login = CURRENT_TIMESTAMP WHERE id = ?";
                    $update_stmt = $conn->prepare($update_sql);
                    $admin_id = $_SESSION['admin_id']; // Store in variable first
                    $update_stmt->bind_param("i", $admin_id);
                    $update_stmt->execute();
                    $update_stmt->close();
                    
                    $insert_stmt->close();
                    header('Location: admin_panel.php');
                    exit;
                } else {
                    $error = "Error creating admin user: " . $conn->error;
                }
                $insert_stmt->close();
            } else {
                $error = "Error creating admin table: " . $conn->error;
            }
        } else {
            // Table exists, check credentials
            $check_sql = "SELECT id, username, password_hash FROM admin_users WHERE username = ? AND is_active = 1";
            $stmt = $conn->prepare($check_sql);
            
            if ($stmt) {
                $stmt->bind_param("s", $input_username);
                $stmt->execute();
                $result = $stmt->get_result();
                
                if ($result && $result->num_rows > 0) {
                    $admin = $result->fetch_assoc();
                    
                    // For now, check against plain text password 'marjan2024'
                    // In production, use password_verify($input_password, $admin['password_hash'])
                    if ($input_password === 'marjan2024') {
                        $_SESSION['admin_logged_in'] = true;
                        $_SESSION['admin_id'] = $admin['id'];
                        $_SESSION['admin_username'] = $admin['username'];
                        
                        // Update last login timestamp
                        $update_sql = "UPDATE admin_users SET last_login = CURRENT_TIMESTAMP WHERE id = ?";
                        $update_stmt = $conn->prepare($update_sql);
                        $update_stmt->bind_param("i", $admin['id']);
                        $update_stmt->execute();
                        $update_stmt->close();
                        
                        header('Location: admin_panel.php');
                        exit;
                    } else {
                        $error = "Invalid credentials";
                    }
                } else {
                    $error = "Invalid credentials";
                }
                $stmt->close();
            } else {
                $error = "Database error: " . $conn->error;
            }
        }
    } else {
        $error = "Please enter both username and password";
    }
}

if (isset($_SESSION['admin_logged_in'])) {
    header('Location: admin_panel.php');
    exit;
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Marjan Investment - Admin Login</title>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700;800&display=swap" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css" rel="stylesheet">
    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }

        body {
            font-family: 'Inter', sans-serif;
            background: url('data:image/svg+xml,<svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 1200 800"><defs><linearGradient id="sky" x1="0%" y1="0%" x2="0%" y2="100%"><stop offset="0%" style="stop-color:%230B1426;stop-opacity:1" /><stop offset="50%" style="stop-color:%231E3A8A;stop-opacity:1" /><stop offset="100%" style="stop-color:%233B82F6;stop-opacity:1" /></linearGradient></defs><rect width="1200" height="800" fill="url(%23sky)"/><circle cx="200" cy="150" r="80" fill="%23F59E0B" opacity="0.8"/><circle cx="1000" cy="100" r="60" fill="%23F59E0B" opacity="0.6"/><circle cx="150" cy="600" r="40" fill="%23F59E0B" opacity="0.4"/></svg>') no-repeat center center fixed;
            background-size: cover;
            min-height: 100vh;
            display: flex;
            align-items: center;
            justify-content: center;
            padding: 20px;
            position: relative;
        }

        .background-overlay {
            position: absolute;
            top: 0;
            left: 0;
            right: 0;
            bottom: 0;
            background: linear-gradient(135deg, rgba(11, 20, 38, 0.7) 0%, rgba(30, 58, 138, 0.6) 50%, rgba(59, 130, 246, 0.5) 100%);
            z-index: 1;
        }

        .login-container {
            background: rgba(255, 255, 255, 0.95);
            backdrop-filter: blur(20px);
            border-radius: 24px;
            box-shadow: 0 25px 50px rgba(0, 0, 0, 0.3);
            overflow: hidden;
            max-width: 1000px;
            width: 100%;
            min-height: 600px;
            display: flex;
            position: relative;
            z-index: 10;
        }

        .login-left {
            flex: 1;
            background: linear-gradient(135deg, rgba(59, 130, 246, 0.1) 0%, rgba(16, 185, 129, 0.1) 100%);
            position: relative;
            padding: 60px;
            display: flex;
            flex-direction: column;
            justify-content: center;
            align-items: center;
            text-align: center;
            overflow: hidden;
        }

        .login-left::before {
            content: '';
            position: absolute;
            top: 0;
            left: 0;
            right: 0;
            bottom: 0;
            background: linear-gradient(45deg, transparent 30%, rgba(59, 130, 246, 0.05) 50%, transparent 70%);
            animation: shimmer 3s ease-in-out infinite;
        }

        @keyframes shimmer {
            0% { transform: translateX(-100%); }
            100% { transform: translateX(100%); }
        }

        .brand-section {
            position: relative;
            z-index: 2;
        }

        .brand-title {
            font-size: 3.5rem;
            font-weight: 800;
            background: linear-gradient(135deg, #3B82F6, #10B981);
            -webkit-background-clip: text;
            -webkit-text-fill-color: transparent;
            background-clip: text;
            margin-bottom: 24px;
            letter-spacing: -2px;
        }

        .brand-subtitle {
            font-size: 1.3rem;
            color: #374151;
            font-weight: 500;
            max-width: 400px;
            line-height: 1.6;
            margin-bottom: 32px;
        }

        .marjan-logo {
            text-align: center;
            margin-bottom: 32px;
        }

        .marjan-icon {
            width: 80px;
            height: 80px;
            background: linear-gradient(135deg, #3B82F6, #10B981);
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            color: white;
            font-size: 32px;
            box-shadow: 0 8px 25px rgba(59, 130, 246, 0.3);
            margin: 0 auto 20px;
        }

        .marjan-text {
            font-size: 1.8rem;
            font-weight: 700;
            color: #1F2937;
            margin-bottom: 8px;
            letter-spacing: 1px;
        }

        .marjan-subtitle {
            font-size: 1rem;
            color: #6B7280;
            font-weight: 500;
            letter-spacing: 0.5px;
        }

        .decoration-line {
            width: 100px;
            height: 2px;
            background: linear-gradient(90deg, #3B82F6, #10B981);
            margin: 0 auto;
            border-radius: 1px;
        }

        .login-right {
            flex: 1;
            background: rgba(255, 255, 255, 0.98);
            padding: 60px;
            display: flex;
            flex-direction: column;
            justify-content: center;
            position: relative;
        }

        .login-form-container {
            max-width: 400px;
            margin: 0 auto;
            width: 100%;
            position: relative;
            z-index: 2;
        }

        .form-header {
            text-align: center;
            margin-bottom: 40px;
        }

        .form-title {
            font-size: 2.2rem;
            font-weight: 700;
            color: #1F2937;
            margin-bottom: 12px;
        }

        .form-subtitle {
            color: #6B7280;
            font-size: 1rem;
            font-weight: 400;
        }

        .form-group {
            margin-bottom: 24px;
        }

        .form-group label {
            display: block;
            font-weight: 600;
            color: #374151;
            margin-bottom: 8px;
            font-size: 0.95rem;
        }

        .form-group input {
            width: 100%;
            padding: 16px 20px;
            background: #F9FAFB;
            border: 2px solid #E5E7EB;
            border-radius: 12px;
            color: #1F2937;
            font-size: 16px;
            transition: all 0.3s ease;
        }

        .form-group input:focus {
            outline: none;
            border-color: #3B82F6;
            box-shadow: 0 0 0 4px rgba(59, 130, 246, 0.1);
            background: white;
        }

        .form-group input::placeholder {
            color: #9CA3AF;
        }

        .login-button {
            width: 100%;
            background: linear-gradient(135deg, #3B82F6, #10B981);
            color: white;
            border: none;
            padding: 16px 24px;
            border-radius: 12px;
            font-size: 16px;
            font-weight: 600;
            cursor: pointer;
            transition: all 0.3s ease;
            margin-bottom: 24px;
            position: relative;
            overflow: hidden;
        }

        .login-button::before {
            content: '';
            position: absolute;
            top: 0;
            left: -100%;
            width: 100%;
            height: 100%;
            background: linear-gradient(90deg, transparent, rgba(255, 255, 255, 0.2), transparent);
            transition: left 0.5s ease;
        }

        .login-button:hover::before {
            left: 100%;
        }

        .login-button:hover {
            transform: translateY(-2px);
            box-shadow: 0 8px 25px rgba(59, 130, 246, 0.4);
        }

        .login-button:active {
            transform: translateY(0);
        }

        .error-message {
            background: rgba(239, 68, 68, 0.1);
            border: 1px solid rgba(239, 68, 68, 0.3);
            color: #DC2626;
            padding: 16px;
            border-radius: 12px;
            margin-bottom: 24px;
            text-align: center;
            font-size: 0.9rem;
        }

        .security-note {
            text-align: center;
            color: #6B7280;
            font-size: 0.85rem;
            margin-top: 24px;
            padding: 16px;
            background: #F3F4F6;
            border-radius: 8px;
        }

        .security-note i {
            color: #3B82F6;
            margin-right: 6px;
        }

        @media (max-width: 768px) {
            .login-container {
                flex-direction: column;
                max-width: 400px;
                min-height: auto;
            }
            
            .login-left {
                padding: 40px 20px;
            }
            
            .login-right {
                padding: 40px 20px;
            }
            
            .brand-title {
                font-size: 2.5rem;
            }
            
            .marjan-logo {
                margin-bottom: 24px;
            }
            
            .marjan-icon {
                width: 60px;
                height: 60px;
                font-size: 24px;
            }
            
            .marjan-text {
                font-size: 1.4rem;
            }
        }

        @media (max-width: 480px) {
            .login-container {
                margin: 20px;
                border-radius: 16px;
            }
            
            .login-left,
            .login-right {
                padding: 30px 20px;
            }
            
            .brand-title {
                font-size: 2rem;
            }
            
            .form-title {
                font-size: 1.5rem;
            }
        }

        /* Loading animation for button */
        .login-button.loading {
            pointer-events: none;
            opacity: 0.8;
        }

        .login-button.loading::after {
            content: '';
            position: absolute;
            width: 20px;
            height: 20px;
            top: 50%;
            left: 50%;
            margin-left: -10px;
            margin-top: -10px;
            border: 2px solid transparent;
            border-top: 2px solid white;
            border-radius: 50%;
            animation: spin 1s linear infinite;
        }

        @keyframes spin {
            to { transform: rotate(360deg); }
        }

        /* Focus styles for accessibility */
        .form-group input:focus,
        .login-button:focus {
            outline: 2px solid #3B82F6;
            outline-offset: 2px;
        }

        /* Smooth transitions */
        * {
            transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1);
        }
    </style>
</head>
<body>
    <div class="background-overlay"></div>
    
    <div class="login-container">
        <div class="login-left">
            <div class="brand-section">
                <div class="marjan-logo">
                    <div class="marjan-icon">
                        <i class="fas fa-building"></i>
                    </div>
                    <div class="marjan-text">MARJAN</div>
                    <div class="marjan-subtitle">INVESTMENT GROUP</div>
                </div>
                
                <h1 class="brand-title">ADMIN PORTAL</h1>
                <p class="brand-subtitle">
                    Secure access to manage partnership applications<br>
                    and investment opportunities with confidence.
                </p>
                
                <div class="decoration-line"></div>
            </div>
        </div>
        
        <div class="login-right">
            <div class="login-form-container">
                <div class="form-header">
                    <h2 class="form-title">Admin Access</h2>
                    <p class="form-subtitle">Enter your credentials to access the admin panel</p>
                </div>
                
                <?php if (isset($error)): ?>
                    <div class="error-message">
                        <i class="fas fa-exclamation-triangle"></i>
                        <?php echo htmlspecialchars($error); ?>
                    </div>
                <?php endif; ?>
                
                <form method="POST" id="loginForm">
                    <div class="form-group">
                        <label for="username">Username</label>
                        <input 
                            type="text" 
                            id="username" 
                            name="username" 
                            placeholder="Enter your username"
                            required
                            autocomplete="username"
                        >
                    </div>
                    
                    <div class="form-group">
                        <label for="password">Password</label>
                        <input 
                            type="password" 
                            id="password" 
                            name="password" 
                            placeholder="Enter your password"
                            required
                            autocomplete="current-password"
                        >
                    </div>
                    
                    <button type="submit" name="login" class="login-button" id="loginBtn">
                        <span class="btn-text">Sign In</span>
                    </button>
                </form>
                
                <div class="security-note">
                    <i class="fas fa-shield-alt"></i>
                    Secure admin access with enterprise-grade security
                </div>
            </div>
        </div>
    </div>

    <script>
        document.getElementById('loginForm').addEventListener('submit', function(e) {
            const button = document.getElementById('loginBtn');
            const btnText = button.querySelector('.btn-text');
            
            // Add loading state
            button.classList.add('loading');
            btnText.textContent = 'Signing In...';
            
            // Simulate loading (remove in production)
            setTimeout(() => {
                button.classList.remove('loading');
                btnText.textContent = 'Sign In';
            }, 2000);
        });

        // Add focus effects
        document.querySelectorAll('input').forEach(input => {
            input.addEventListener('focus', function() {
                this.parentElement.style.transform = 'translateY(-2px)';
            });
            
            input.addEventListener('blur', function() {
                this.parentElement.style.transform = 'translateY(0)';
            });
        });

        // Add floating label effect
        document.querySelectorAll('.form-group input').forEach(input => {
            input.addEventListener('input', function() {
                if (this.value.length > 0) {
                    this.style.borderColor = '#3B82F6';
                } else {
                    this.style.borderColor = '#E5E7EB';
                }
            });
        });
    </script>
</body>
</html>
<?php
$conn->close();
?>
