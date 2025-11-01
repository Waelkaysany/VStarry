<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Marjan Partnership - Exclusive Business Opportunities</title>
    
    <link href="https://fonts.googleapis.com/css2?family=Playfair+Display:wght@400;500;600;700&family=Inter:wght@300;400;500;600&display=swap" rel="stylesheet">
    <style>
        @import url('https://fonts.googleapis.com/css2?family=Bitcount+Grid+Double:wght@100..900&family=Creepster&family=Homemade+Apple&family=Merriweather:ital,wght@0,300;0,400;0,700;0,900;1,300;1,400;1,700;1,900&family=Poppins:ital,wght@0,100;0,200;0,300;0,400;0,500;0,600;0,700;0,800;0,900;1,100;1,200;1,300;1,400;1,500;1,600;1,700;1,800;1,900&family=Roboto:ital,wght@0,100..900;1,100..900&family=Sigmar&family=Sora:wght@100..800&display=swap');

        :root {
            --primary-gold:rgb(255, 255, 255);
            --primary-dark: #1a1a1a;
            --secondary-dark: #2a2a2a;
            --accent-green: #2d5016;
            --text-light: #f5f5f5;
            --text-muted: #a0a0a0;
            --luxury-gradient: linear-gradient(135deg,rgb(14, 164, 0) 0%,rgb(9, 168, 6) 50%,rgb(9, 156, 6) 100%);
            --shadow-luxury: 0 20px 60px rgba(212, 175, 55, 0.15);
        }

        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
            text-decoration: none;
            font-family:Sora,Arial, Helvetica, sans-serif;
        }

        body {
            background: linear-gradient(135deg, #0f0f0f 0%, #1a1a1a 50%, #2a2a2a 100%);
            color: var(--text-light);
            overflow-x: hidden;
            position: relative;
        }

        /* Luxury background elements */
        body::before {
            content: '';
            position: fixed;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            background: 
                radial-gradient(circle at 20% 80%, rgba(212, 175, 55, 0.1) 0%, transparent 50%),
                radial-gradient(circle at 80% 20%, rgba(45, 80, 22, 0.1) 0%, transparent 50%),
                radial-gradient(circle at 40% 40%, rgba(212, 175, 55, 0.05) 0%, transparent 50%);
            z-index: -1;
        }

        .container {
            min-height: 100vh;
            display: grid;
            grid-template-columns: 1fr 1fr;
            position: relative;
        }

        /* Left Section - Hero Content */
        .hero-section {
            padding: 80px 60px;
            display: flex;
            flex-direction: column;
            justify-content: center;
            position: relative;
            background: linear-gradient(135deg, rgba(0,0,0,0.8) 0%, rgba(26,26,26,0.9) 100%);
        }

        .hero-section::before {
            content: '';
            position: absolute;
            top: 0;
            left: 0;
            right: 0;
            bottom: 0;
            background: url('data:image/svg+xml,<svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 100 100"><defs><pattern id="grain" width="100" height="100" patternUnits="userSpaceOnUse"><circle cx="25" cy="25" r="0.5" fill="%23D4AF37" opacity="0.1"/><circle cx="75" cy="75" r="0.3" fill="%23D4AF37" opacity="0.08"/><circle cx="50" cy="10" r="0.4" fill="%23D4AF37" opacity="0.06"/></pattern></defs><rect width="100" height="100" fill="url(%23grain)"/></svg>');
            opacity: 0.3;
            z-index: 1;
        }

        .hero-content {
            position: relative;
            z-index: 2;
        }

        .brand-mark {
             font-size: 1.2rem;
            font-weight: 500;
            color: var(--primary-gold);
            margin-bottom: 40px;
            letter-spacing: 3px;
            text-transform: uppercase;
        }

        .hero-title {
            font-size: 4.5rem;
            font-weight: 700;
            line-height: 1.1;
            margin-bottom: 32px;
            background: var(--luxury-gradient);
            -webkit-background-clip: text;
            -webkit-text-fill-color: transparent;
            background-clip: text;
            letter-spacing: -0.02em;
        }

        .hero-subtitle {
            font-size: 1.3rem;
            line-height: 1.6;
            color: var(--text-muted);
            font-weight: 300;
            max-width: 480px;
            margin-bottom: 48px;
        }

        .luxury-features {
            list-style: none;
            margin-top: 40px;
        }

        .luxury-features li {
            display: flex;
            align-items: center;
            margin-bottom: 16px;
            font-size: 1rem;
            color: var(--text-muted);
        }

        .luxury-features li::before {
            content: '◆';
            color: var(--primary-gold);
            margin-right: 16px;
            font-size: 0.8rem;
        }

        /* Right Section - Premium Form */
        .form-section {
            padding: 80px 60px;
            display: flex;
            align-items: center;
            justify-content: center;
            background: linear-gradient(135deg, rgba(26,26,26,0.95) 0%, rgba(42,42,42,0.95) 100%);
            backdrop-filter: blur(20px);
            position: relative;
        }

        .form-section::before {
            content: '';
            position: absolute;
            top: 0;
            right: 0;
            width: 200px;
            height: 200px;
            background: radial-gradient(circle, rgba(212, 175, 55, 0.1) 0%, transparent 70%);
            border-radius: 50%;
            z-index: 1;
        }

        .form-container {
            width: 100%;
            max-width: 500px;
            position: relative;
            z-index: 2;
        }

        .form-header {
            text-align: center;
            margin-bottom: 48px;
        }

        .form-title {
             font-size: 2.5rem;
            font-weight: 600;
            color: var(--text-light);
            margin-bottom: 16px;
        }

        .form-subtitle {
            color: var(--text-muted);
            font-size: 1rem;
            font-weight: 300;
        }

        .form-group {
            margin-bottom: 32px;
            position: relative;
        }

        .form-row {
            display: grid;
            grid-template-columns: 1fr 1fr;
            gap: 24px;
        }

        .form-label {
            display: block;
            font-size: 0.9rem;
            font-weight: 500;
            color: var(--primary-gold);
            margin-bottom: 12px;
            text-transform: uppercase;
            letter-spacing: 1px;
        }

        .required::after {
            content: '*';
            color: #ff6b6b;
            margin-left: 4px;
        }

        .form-input {
            width: 100%;
            background: transparent;
            border: none;
            border-bottom: 2px solid rgba(212, 175, 55, 0.3);
            padding: 16px 0;
            font-size: 1.1rem;
            color: var(--text-light);
             transition: all 0.4s cubic-bezier(0.4, 0, 0.2, 1);
            outline: none;
        }

        .form-input:focus {
            border-bottom-color: var(--primary-gold);
            transform: translateY(-2px);
            box-shadow: 0 4px 20px rgba(212, 175, 55, 0.2);
        }

        .form-input::placeholder {
            color: rgba(160, 160, 160, 0.6);
            font-weight: 300;
        }

        .form-textarea {
            min-height: 120px;
            resize: vertical;
            padding-top: 16px;
        }

        .form-select {
            cursor: pointer;
            background-image: url("data:image/svg+xml,%3csvg xmlns='http://www.w3.org/2000/svg' fill='none' viewBox='0 0 20 20'%3e%3cpath stroke='%23D4AF37' stroke-linecap='round' stroke-linejoin='round' stroke-width='2' d='M6 8l4 4 4-4'/%3e%3c/svg%3e");
            background-position: right 0 center;
            background-repeat: no-repeat;
            background-size: 20px;
            padding-right: 32px;
        }

        .file-upload-wrapper {
            position: relative;
            display: block;
        }

        .file-upload-input {
            position: absolute;
            opacity: 0;
            width: 100%;
            height: 100%;
            cursor: pointer;
        }

        .file-upload-label {
            display: flex;
            align-items: center;
            padding: 20px 24px;
            background: linear-gradient(135deg, rgba(212, 175, 55, 0.1) 0%, rgba(212, 175, 55, 0.05) 100%);
            border: 2px dashed rgba(212, 175, 55, 0.4);
            border-radius: 12px;
            cursor: pointer;
            transition: all 0.3s ease;
            text-align: center;
            justify-content: center;
        }

        .file-upload-label:hover {
            background: linear-gradient(135deg, rgba(212, 175, 55, 0.15) 0%, rgba(212, 175, 55, 0.08) 100%);
            border-color: var(--primary-gold);
            transform: translateY(-2px);
        }

        .file-upload-icon {
            margin-right: 12px;
            font-size: 1.5rem;
        }

        .submit-button {
            width: 100%;
            background: var(--luxury-gradient);
            border: none;
            padding: 20px 32px;
            font-size: 1.1rem;
            font-weight: 600;
            color: var(--primary-dark);
            border-radius: 12px;
            cursor: pointer;
            transition: all 0.4s cubic-bezier(0.4, 0, 0.2, 1);
            text-transform: uppercase;
            letter-spacing: 1px;
             position: relative;
            overflow: hidden;
            margin-top: 32px;
        }

        .submit-button::before {
            content: '';
            position: absolute;
            top: 0;
            left: -100%;
            width: 100%;
            height: 100%;
            background: linear-gradient(90deg, transparent, rgba(255,255,255,0.2), transparent);
            transition: left 0.5s;
        }

        .submit-button:hover {
            transform: translateY(-3px);
            box-shadow: var(--shadow-luxury);
        }

        .submit-button:hover::before {
            left: 100%;
        }

        .submit-button:active {
            transform: translateY(-1px);
        }

        .privacy-notice {
            text-align: center;
            margin-top: 24px;
            font-size: 0.85rem;
            color: var(--text-muted);
            line-height: 1.5;
        }

        .privacy-notice a {
            color: var(--primary-gold);
            text-decoration: none;
            transition: color 0.3s ease;
        }

        .privacy-notice a:hover {
            color: var(--text-light);
        }

        /* Success Message */
        .success-overlay {
            position: fixed;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            background: rgba(0, 0, 0, 0.9);
            display: none;
            align-items: center;
            justify-content: center;
            z-index: 1000;
            backdrop-filter: blur(10px);
        }

        .success-message {
            background: linear-gradient(135deg, var(--secondary-dark) 0%, var(--primary-dark) 100%);
            padding: 60px 40px;
            border-radius: 20px;
            text-align: center;
            max-width: 500px;
            border: 2px solid rgba(212, 175, 55, 0.3);
            box-shadow: var(--shadow-luxury);
        }

        .success-icon {
            font-size: 4rem;
            color: var(--primary-gold);
            margin-bottom: 24px;
        }

        .success-title {
             font-size: 2rem;
            margin-bottom: 16px;
            color: var(--text-light);
        }

        .success-text {
            color: var(--text-muted);
            line-height: 1.6;
        }

        /* Loading State */
        .loading {
            opacity: 0.7;
            pointer-events: none;
        }

        .loading .submit-button {
            background: linear-gradient(135deg, rgba(212, 175, 55, 0.5) 0%, rgba(184, 134, 11, 0.5) 100%);
        }

        /* Responsive Design */
        @media (max-width: 1200px) {
            .container {
                grid-template-columns: 1fr;
            }
            
            .hero-section {
                padding: 60px 40px;
                min-height: 60vh;
            }
            
            .form-section {
                padding: 60px 40px;
            }
            
            .hero-title {
                font-size: 3.5rem;
            }
        }

        @media (max-width: 768px) {
            .hero-section, .form-section {
                padding: 40px 24px;
            }
            
            .hero-title {
                font-size: 2.8rem;
            }
            
            .form-row {
                grid-template-columns: 1fr;
                gap: 16px;
            }
            
            .form-group {
                margin-bottom: 24px;
            }
        }

        @media (max-width: 480px) {
            .hero-title {
                font-size: 2.2rem;
            }
            
            .hero-section, .form-section {
                padding: 32px 20px;
            }
        }

        /* Animation Classes */
        .fade-in {
            animation: fadeIn 0.8s ease-out forwards;
            opacity: 0;
        }

        .slide-up {
            animation: slideUp 0.8s ease-out forwards;
            opacity: 0;
            transform: translateY(30px);
        }

        @keyframes fadeIn {
            to {
                opacity: 1;
            }
        }

        @keyframes slideUp {
            to {
                opacity: 1;
                transform: translateY(0);
            }
        }

        .form-group:nth-child(1) { animation-delay: 0.1s; }
        .form-group:nth-child(2) { animation-delay: 0.2s; }
        .form-group:nth-child(3) { animation-delay: 0.3s; }
        .form-group:nth-child(4) { animation-delay: 0.4s; }
        .form-group:nth-child(5) { animation-delay: 0.5s; }
        .form-group:nth-child(6) { animation-delay: 0.6s; }
        .form-group:nth-child(7) { animation-delay: 0.7s; }
        nav{
  position:sticky;
  display:flex;
  flex-direction:row;
  justify-content:space-between;
  align-items:center;
  width:100%;
  height:9vh;

  padding:2vw 3vw;
  z-index: 10;
 }
 #nav-button{
  background-color: red;
  height:0%;
  position:absolute;
  width:70%;
  bottom:-100%;
  border-bottom:2px solid #dadada;
 }
 .nav-part2{
  display:flex;
  flex-direction:row;
  justify-content:space-between;
  align-items:center;
  width:30%;
 }
 nav h4 {
  color: #fff;
  font-size: 1vw;;
  font-weight: 500;
  cursor: pointer;
  position: relative;
  padding: 0.3rem 0;
  transition: color 0.3s ease;
  text-decoration: none;
}

/* Underline hover effect */
nav h4::after {
  content: "";
  position: absolute;
  bottom: 0;
  left: 0;
  width: 0%;
  height: 2px;
  background: linear-gradient(90deg, #00ff94, #00e0ff);
  transition: width 0.3s ease;
}

nav h4:hover {
  color: #00ff94;
}

nav h4:hover::after {
  width: 100%;
}

    </style>
</head>
<body>
<nav>
   <a href="http://localhost/Vtuber_project/first-page/home.php" style="color:white;"><h1 id="nav-bottom"><span style="color:#32CD32">M</span>ARJAN</h1></a>
<div class="nav-part2">
  <h4><a href="../../about-us/about-us.php" style="color:#fff">About</a></h4>
  <h4><a href="./index.php"style="color:#fff">Apply</a></h4>
  <h4><a href="../../news/news.php" style="color:#fff">News</a></h4>
</div>

   </div>
 </button>
  </nav>
    <div class="container">
        <!-- Hero Section -->
        <div class="hero-section">
            <div class="hero-content">
                <div class="brand-mark fade-in" style="color:#f5f5f5"><span style="color:#00ff94">M</span>arjan</div>
                <h1 class="hero-title fade-in">Partner with Excellence</h1>
                <p class="hero-subtitle fade-in">
                    Join an exclusive network of visionary entrepreneurs. Transform your business concept into a premium venture under the prestigious Marjan brand.
                </p>
                <ul class="luxury-features fade-in">
                    <li>Exclusive partnership opportunities</li>
                    <li>Premium brand association</li>
                    <li>Comprehensive business support</li>
                    <li>Global market access</li>
                </ul>
            </div>
        </div>

        <!-- Form Section -->
        <div class="form-section">
            <div class="form-container">
                <div class="form-header fade-in">
                    <h2 class="form-title">Apply Now</h2>
                    <p class="form-subtitle">Begin your journey to partnership excellence</p>
                </div>

                <form id="partnershipForm" action="submit_form.php" method="POST" enctype="multipart/form-data">
                    <div class="form-group slide-up">
                        <label for="name" class="form-label required">Full Name</label>
                        <input type="text" id="name" name="name" class="form-input" placeholder="Enter your full name" autocomplete="off" required>
                    </div>

                    <div class="form-group slide-up">
                        <div class="form-row">
                            <div>
                                <label for="budget" class="form-label required">Investment Budget</label>
                                <input type="text" id="budget" name="budget" class="form-input" placeholder="e.g., $100,000" autocomplete="off" required>
                            </div>
                            <div>
                                <label for="email" class="form-label required">Email Address</label>
                                <input type="email" id="email" name="email" class="form-input" placeholder="your@email.com"  autocomplete="off" required>
                            </div>
                        </div>
                    </div>

                    <div class="form-group slide-up">
                        <label for="phone" class="form-label required">Phone Number</label>
                        <input type="tel" id="phone" name="phone" class="form-input" placeholder="+1 (555) 123-4567" autocomplete="off" required>
                    </div>

                    <div class="form-group slide-up">
                        <label for="reason" class="form-label required">Why Marjan?</label>
                        <textarea id="reason" name="reason" class="form-input form-textarea" placeholder="Share what attracts you to partner with Marjan and your vision for this collaboration..." autocomplete="off" required></textarea>
                    </div>

                    <div class="form-group slide-up">
                        <label for="source" class="form-label required">How did you discover us?</label>
                        <select id="source" name="source" class="form-input form-select" style="background-color: black;"  autocomplete="off" required>
                            <option value="">Select an option</option>
                            <option value="social-media">Social Media</option>
                            <option value="website">Website</option>
                            <option value="event">Industry Event</option>
                            <option value="referral">Professional Referral</option>
                            <option value="other">Other</option>
                        </select>
                    </div>

                    <div class="form-group slide-up">
                        <label for="proposal" class="form-label">Business Proposal</label>
                        <div class="file-upload-wrapper">
                            <input type="file" id="proposal" name="proposal" class="file-upload-input" accept=".pdf,.doc,.docx,.ppt,.pptx">
                            <label for="proposal" class="file-upload-label">
                                <span class="file-upload-icon">📄</span>
                                <span id="file-name">Upload your business proposal (optional)</span>
                            </label>
                        </div>
                    </div>

                    <button type="submit" class="submit-button slide-up">
                        Submit Application
                    </button>

                    <div class="privacy-notice slide-up">
                        By submitting this application, you agree to our 
                        <a href="#" target="_blank">Privacy Policy</a> and 
                        <a href="#" target="_blank">Terms of Service</a>.
                    </div>
                </form>
            </div>
        </div>
    </div>

    <!-- Success Overlay -->
    <div class="success-overlay" id="successOverlay">
        <div class="success-message">
            <div class="success-icon">✨</div>
            <h3 class="success-title">Application Received</h3>
            <p class="success-text">
                Thank you for your interest in partnering with Marjan. Our partnership team will review your application and contact you within 48 hours.
            </p>
        </div>
    </div>

    <script>
        // Initialize animations
        document.addEventListener('DOMContentLoaded', function() {
            const animatedElements = document.querySelectorAll('.fade-in, .slide-up');
            animatedElements.forEach(el => {
                el.style.animationPlayState = 'running';
            });
        });

        // File upload handling
        document.getElementById('proposal').addEventListener('change', function(e) {
            const fileName = e.target.files[0]?.name || 'Upload your business proposal (optional)';
            document.getElementById('file-name').textContent = fileName;
        });

        // Phone number formatting
        document.getElementById('phone').addEventListener('input', function(e) {
            let value = e.target.value.replace(/\D/g, '');
            if (value.length >= 10) {
                value = value.replace(/(\d{3})(\d{3})(\d{4})/, '($1) $2-$3');
            } else if (value.length >= 6) {
                value = value.replace(/(\d{3})(\d{3})/, '($1) $2-');
            } else if (value.length >= 3) {
                value = value.replace(/(\d{3})/, '($1) ');
            }
            e.target.value = value;
        });

        // Budget formatting
        document.getElementById('budget').addEventListener('input', function(e) {
            let value = e.target.value.replace(/[^\d]/g, '');
            if (value) {
                value = '$' + parseInt(value).toLocaleString();
            }
            e.target.value = value;
        });

        // Form submission
        document.getElementById('partnershipForm').addEventListener('submit', function(e) {
            e.preventDefault();
            
            // Add loading state
            document.body.classList.add('loading');
            
            // Validate form
            const requiredFields = ['name', 'budget', 'email', 'phone', 'reason', 'source'];
            let isValid = true;
            
            requiredFields.forEach(field => {
                const input = document.getElementById(field);
                if (!input.value.trim()) {
                    isValid = false;
                    input.style.borderBottomColor = '#ff6b6b';
                    input.style.boxShadow = '0 4px 20px rgba(255, 107, 107, 0.3)';
                    
                    setTimeout(() => {
                        input.style.borderBottomColor = 'rgba(212, 175, 55, 0.3)';
                        input.style.boxShadow = 'none';
                    }, 3000);
                } else {
                    input.style.borderBottomColor = 'var(--primary-gold)';
                }
            });
            
            // Email validation
            const email = document.getElementById('email');
            const emailRegex = /^[^\s@]+@[^\s@]+\.[^\s@]+$/;
            if (email.value && !emailRegex.test(email.value)) {
                isValid = false;
                email.style.borderBottomColor = '#ff6b6b';
                email.style.boxShadow = '0 4px 20px rgba(255, 107, 107, 0.3)';
            }
            
            if (isValid) {
                // Create FormData object
                const formData = new FormData(this);
                
                // Submit via AJAX
                fetch('submit_form.php', {
                    method: 'POST',
                    body: formData
                })
                .then(response => response.json())
                .then(data => {
                    document.body.classList.remove('loading');
                    
                    if (data.success) {
                        document.getElementById('successOverlay').style.display = 'flex';
                        this.reset();
                        document.getElementById('file-name').textContent = 'Upload your business proposal (optional)';
                        
                        // Hide success message after 5 seconds
                        setTimeout(() => {
                            document.getElementById('successOverlay').style.display = 'none';
                        }, 5000);
                    } else {
                        alert('There was an error submitting your application. Please try again.');
                    }
                })
                .catch(error => {
                    document.body.classList.remove('loading');
                    console.error('Error:', error);
                    alert('There was an error submitting your application. Please try again.');
                });
            } else {
                document.body.classList.remove('loading');
            }
        });

        // Enhanced input interactions
        document.querySelectorAll('.form-input').forEach(input => {
            input.addEventListener('focus', function() {
                this.parentElement.style.transform = 'scale(1.02)';
                this.parentElement.style.transition = 'transform 0.3s ease';
            });
            
            input.addEventListener('blur', function() {
                this.parentElement.style.transform = 'scale(1)';
            });
        });

        // Close success overlay when clicking outside
        document.getElementById('successOverlay').addEventListener('click', function(e) {
            if (e.target === this) {
                this.style.display = 'none';
            }
        });
    </script>
</body>
</html>