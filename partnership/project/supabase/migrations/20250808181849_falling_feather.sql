-- Marjan Partnership Application Database Setup
-- Database: marjanformul

CREATE DATABASE IF NOT EXISTS marjanformul;
USE marjanformul;

-- Create partnership applications table
CREATE TABLE IF NOT EXISTS partnership_applications (
    id INT(11) UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    name VARCHAR(255) NOT NULL,
    budget VARCHAR(100) NOT NULL,
    email VARCHAR(255) NOT NULL,
    phone VARCHAR(50) NOT NULL,
    reason TEXT NOT NULL,
    source VARCHAR(100) NOT NULL,
    proposal_filename VARCHAR(255) DEFAULT NULL,
    proposal_path VARCHAR(500) DEFAULT NULL,
    submission_date TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    ip_address VARCHAR(45) DEFAULT NULL,
    user_agent TEXT DEFAULT NULL,
    status ENUM('pending', 'reviewed', 'approved', 'rejected') DEFAULT 'pending',
    
    -- Indexes for better performance
    INDEX idx_email (email),
    INDEX idx_submission_date (submission_date),
    INDEX idx_status (status)
);

-- Create admin users table (optional)
CREATE TABLE IF NOT EXISTS admin_users (
    id INT(11) UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    username VARCHAR(50) NOT NULL UNIQUE,
    password_hash VARCHAR(255) NOT NULL,
    email VARCHAR(255) NOT NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    last_login TIMESTAMP NULL,
    is_active BOOLEAN DEFAULT TRUE
);

-- Insert default admin user (password: marjan2024)
-- Note: In production, use proper password hashing
INSERT INTO admin_users (username, password_hash, email) VALUES 
('admin', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 'admin@marjan.com')
ON DUPLICATE KEY UPDATE username = username;

-- Create application notes table for admin comments
CREATE TABLE IF NOT EXISTS application_notes (
    id INT(11) UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    application_id INT(11) UNSIGNED NOT NULL,
    admin_user_id INT(11) UNSIGNED NOT NULL,
    note TEXT NOT NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    
    FOREIGN KEY (application_id) REFERENCES partnership_applications(id) ON DELETE CASCADE,
    FOREIGN KEY (admin_user_id) REFERENCES admin_users(id) ON DELETE CASCADE,
    
    INDEX idx_application_id (application_id),
    INDEX idx_created_at (created_at)
);

-- Create email templates table
CREATE TABLE IF NOT EXISTS email_templates (
    id INT(11) UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    template_name VARCHAR(100) NOT NULL UNIQUE,
    subject VARCHAR(255) NOT NULL,
    body TEXT NOT NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
);

-- Insert default email templates
INSERT INTO email_templates (template_name, subject, body) VALUES 
('application_received', 'Partnership Application Received - Marjan', 
'Dear {name},

Thank you for your interest in partnering with Marjan. We have received your partnership application and our team will review it carefully.

Application Details:
- Name: {name}
- Email: {email}
- Budget: {budget}
- Submission Date: {submission_date}

We will contact you within 48 hours with next steps.

Best regards,
The Marjan Partnership Team'),

('application_approved', 'Partnership Application Approved - Welcome to Marjan', 
'Dear {name},

Congratulations! Your partnership application has been approved. We are excited to welcome you to the Marjan family.

Our partnership manager will contact you within 24 hours to discuss the next steps and schedule an onboarding meeting.

Welcome aboard!

Best regards,
The Marjan Partnership Team'),

('application_rejected', 'Partnership Application Update - Marjan', 
'Dear {name},

Thank you for your interest in partnering with Marjan. After careful consideration, we have decided not to move forward with your application at this time.

We appreciate the time you took to apply and wish you success in your business endeavors.

Best regards,
The Marjan Partnership Team')

ON DUPLICATE KEY UPDATE template_name = template_name;