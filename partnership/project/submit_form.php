<?php
header('Content-Type: application/json');
header('Access-Control-Allow-Origin: *');
header('Access-Control-Allow-Methods: POST');
header('Access-Control-Allow-Headers: Content-Type');

// Database configuration
$servername = "localhost";
$username = "root"; // Change this to your database username
$password = ""; // Change this to your database password
$dbname = "marjanformul";

// Create connection
$conn = new mysqli($servername, $username, $password);

// Check connection
if ($conn->connect_error) {
    die(json_encode(['success' => false, 'message' => 'Connection failed: ' . $conn->connect_error]));
}

// Create database if it doesn't exist
$sql = "CREATE DATABASE IF NOT EXISTS $dbname";
if ($conn->query($sql) === FALSE) {
    die(json_encode(['success' => false, 'message' => 'Error creating database: ' . $conn->error]));
}

// Select the database
$conn->select_db($dbname);

// Create table if it doesn't exist
$sql = "CREATE TABLE IF NOT EXISTS partnership_applications (
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
    status ENUM('pending', 'reviewed', 'approved', 'rejected') DEFAULT 'pending'
)";

if ($conn->query($sql) === FALSE) {
    die(json_encode(['success' => false, 'message' => 'Error creating table: ' . $conn->error]));
}

// Process form submission
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Sanitize and validate input data
    $name = mysqli_real_escape_string($conn, trim($_POST['name']));
    $budget = mysqli_real_escape_string($conn, trim($_POST['budget']));
    $email = mysqli_real_escape_string($conn, trim($_POST['email']));
    $phone = mysqli_real_escape_string($conn, trim($_POST['phone']));
    $reason = mysqli_real_escape_string($conn, trim($_POST['reason']));
    $source = mysqli_real_escape_string($conn, trim($_POST['source']));
    
    // Get client information
    $ip_address = $_SERVER['REMOTE_ADDR'];
    $user_agent = mysqli_real_escape_string($conn, $_SERVER['HTTP_USER_AGENT']);
    
    // Validate required fields
    if (empty($name) || empty($budget) || empty($email) || empty($phone) || empty($reason) || empty($source)) {
        echo json_encode(['success' => false, 'message' => 'All required fields must be filled']);
        exit;
    }
    
    // Validate email format
    if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        echo json_encode(['success' => false, 'message' => 'Invalid email format']);
        exit;
    }
    
    // Handle file upload
    $proposal_filename = null;
    $proposal_path = null;
    
    if (isset($_FILES['proposal']) && $_FILES['proposal']['error'] === UPLOAD_ERR_OK) {
        $upload_dir = 'uploads/';
        
        // Create uploads directory if it doesn't exist
        if (!file_exists($upload_dir)) {
            mkdir($upload_dir, 0755, true);
        }
        
        $file_tmp = $_FILES['proposal']['tmp_name'];
        $file_name = $_FILES['proposal']['name'];
        $file_size = $_FILES['proposal']['size'];
        $file_ext = strtolower(pathinfo($file_name, PATHINFO_EXTENSION));
        
        // Allowed file extensions
        $allowed_extensions = ['pdf', 'doc', 'docx', 'ppt', 'pptx'];
        
        // Validate file extension
        if (!in_array($file_ext, $allowed_extensions)) {
            echo json_encode(['success' => false, 'message' => 'Invalid file type. Only PDF, DOC, DOCX, PPT, and PPTX files are allowed.']);
            exit;
        }
        
        // Validate file size (max 10MB)
        if ($file_size > 10 * 1024 * 1024) {
            echo json_encode(['success' => false, 'message' => 'File size too large. Maximum size is 10MB.']);
            exit;
        }
        
        // Generate unique filename
        $unique_filename = uniqid() . '_' . time() . '.' . $file_ext;
        $upload_path = $upload_dir . $unique_filename;
        
        // Move uploaded file
        if (move_uploaded_file($file_tmp, $upload_path)) {
            $proposal_filename = $file_name;
            $proposal_path = $upload_path;
        } else {
            echo json_encode(['success' => false, 'message' => 'Error uploading file']);
            exit;
        }
    }
    
    // Insert data into database
    $sql = "INSERT INTO partnership_applications (
        name, budget, email, phone, reason, source, 
        proposal_filename, proposal_path, ip_address, user_agent
    ) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?)";
    
    $stmt = $conn->prepare($sql);
    
    if ($stmt === false) {
        echo json_encode(['success' => false, 'message' => 'Error preparing statement: ' . $conn->error]);
        exit;
    }
    
    $stmt->bind_param("ssssssssss", 
        $name, $budget, $email, $phone, $reason, $source,
        $proposal_filename, $proposal_path, $ip_address, $user_agent
    );
    
    if ($stmt->execute()) {
        $application_id = $conn->insert_id;
        
        // Send notification email (optional)
        $to = "partnerships@marjan.com"; // Change this to your email
        $subject = "New Partnership Application - " . $name;
        $message = "
        New partnership application received:
        
        Name: $name
        Email: $email
        Phone: $phone
        Budget: $budget
        Source: $source
        
        Reason for choosing Marjan:
        $reason
        
        Application ID: $application_id
        Submitted: " . date('Y-m-d H:i:s') . "
        IP Address: $ip_address
        ";
        
        $headers = "From: noreply@marjan.com\r\n";
        $headers .= "Reply-To: $email\r\n";
        $headers .= "Content-Type: text/plain; charset=UTF-8\r\n";
        
        // Uncomment the line below to enable email notifications
        // mail($to, $subject, $message, $headers);
        
        echo json_encode([
            'success' => true, 
            'message' => 'Application submitted successfully',
            'application_id' => $application_id
        ]);
    } else {
        echo json_encode(['success' => false, 'message' => 'Error saving application: ' . $stmt->error]);
    }
    
    $stmt->close();
} else {
    echo json_encode(['success' => false, 'message' => 'Invalid request method']);
}

$conn->close();
?>