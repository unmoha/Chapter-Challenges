<?php
session_start();
require_once 'config/db.php';

// Redirect if already logged in
if (isset($_SESSION['user_id'])) {
    header('Location: dashboard.php');
    exit();
}

$error = '';
$success = '';

// Handle registration form submission
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $username = sanitize_input($_POST['username']);
    $email = sanitize_input($_POST['email']);
    $password = $_POST['password'];
    $confirm_password = $_POST['confirm_password'];
    $full_name = sanitize_input($_POST['full_name']);
    
    // Validation
    if (empty($username) || empty($email) || empty($password) || empty($confirm_password)) {
        $error = 'Please fill in all required fields';
    } elseif (strlen($username) < 3) {
        $error = 'Username must be at least 3 characters long';
    } elseif (strlen($password) < 6) {
        $error = 'Password must be at least 6 characters long';
    } elseif ($password !== $confirm_password) {
        $error = 'Passwords do not match';
    } elseif (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $error = 'Please enter a valid email address';
    } else {
        try {
            // Check if username already exists
            $check_query = "SELECT id FROM users WHERE username = ? OR email = ?";
            $check_stmt = $db->prepare($check_query);
            $check_stmt->execute([$username, $email]);
            
            if ($check_stmt->fetch()) {
                $error = 'Username or email already exists';
            } else {
                // Create new user
                $hashed_password = hash_password($password);
                $insert_query = "INSERT INTO users (username, email, password, full_name, created_at) VALUES (?, ?, ?, ?, NOW())";
                $insert_stmt = $db->prepare($insert_query);
                
                if ($insert_stmt->execute([$username, $email, $hashed_password, $full_name])) {
                    $success = 'Registration successful! Please login to continue.';
                    
                    // Clear form
                    $_POST = [];
                } else {
                    $error = 'Registration failed. Please try again.';
                }
            }
        } catch(PDOException $e) {
            $error = 'Registration failed. Please try again.';
        }
    }
}
?>
<!DOCTYPE html>
<html lang="am" dir="ltr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Register - እንቆቅልሽ | Ethiopian Quiz Game</title>
    <meta name="description" content="Create your account and join the premium Ethiopian quiz application">
    
    <!-- Google Fonts -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Segoe+UI:wght@300;400;500;600;700&family=Georgia:wght@400;700&family=Noto+Sans+Ethiopic:wght@400;700;900&display=swap" rel="stylesheet">
    
    <!-- CSS -->
    <link rel="stylesheet" href="assets/css/style.css">
    
    <style>
        .auth-page {
            min-height: 100vh;
            display: flex;
            align-items: center;
            justify-content: center;
            padding: var(--spacing-lg);
            position: relative;
            overflow: hidden;
        }
        
        .auth-container {
            width: 100%;
            max-width: 500px;
            position: relative;
            z-index: 2;
        }
        
        .auth-header {
            text-align: center;
            margin-bottom: var(--spacing-xl);
        }
        
        .auth-logo {
            font-size: 3rem;
            margin-bottom: var(--spacing-md);
            font-family: var(--font-amharic);
            background: linear-gradient(135deg, var(--neon-gold), var(--matte-gold));
            -webkit-background-clip: text;
            -webkit-text-fill-color: transparent;
            background-clip: text;
        }
        
        .auth-title {
            font-size: 2rem;
            color: var(--neon-gold);
            margin-bottom: var(--spacing-sm);
        }
        
        .auth-subtitle {
            color: rgba(255, 255, 255, 0.7);
            font-size: 1rem;
        }
        
        .alert {
            padding: var(--spacing-md);
            border-radius: var(--radius-md);
            margin-bottom: var(--spacing-md);
            text-align: center;
            font-weight: 500;
        }
        
        .alert-error {
            background: linear-gradient(135deg, rgba(218, 18, 26, 0.2), rgba(255, 0, 110, 0.1));
            border: 1px solid rgba(218, 18, 26, 0.3);
            color: #ff6b6b;
        }
        
        .alert-success {
            background: linear-gradient(135deg, rgba(7, 137, 48, 0.2), rgba(0, 255, 136, 0.1));
            border: 1px solid rgba(7, 137, 48, 0.3);
            color: #00ff88;
        }
        
        .input-group {
            position: relative;
            margin-bottom: var(--spacing-lg);
        }
        
        .input-icon {
            position: absolute;
            left: 20px;
            top: 50%;
            transform: translateY(-50%);
            font-size: 1.2rem;
            color: rgba(255, 255, 255, 0.5);
        }
        
        .input-field {
            padding-left: 50px;
        }
        
        .password-strength {
            margin-top: var(--spacing-xs);
            height: 4px;
            background: rgba(255, 255, 255, 0.1);
            border-radius: 2px;
            overflow: hidden;
        }
        
        .password-strength-bar {
            height: 100%;
            width: 0;
            transition: all var(--transition-smooth);
            border-radius: 2px;
        }
        
        .strength-weak { background: #ff6b6b; width: 33%; }
        .strength-medium { background: #FFD700; width: 66%; }
        .strength-strong { background: #00ff88; width: 100%; }
        
        .terms-checkbox {
            display: flex;
            align-items: flex-start;
            gap: var(--spacing-sm);
            margin-bottom: var(--spacing-lg);
        }
        
        .terms-checkbox input[type="checkbox"] {
            width: 18px;
            height: 18px;
            accent-color: var(--neon-gold);
            margin-top: 2px;
        }
        
        .terms-checkbox label {
            color: rgba(255, 255, 255, 0.7);
            font-size: 0.9rem;
            line-height: 1.4;
        }
        
        .terms-checkbox a {
            color: var(--neon-gold);
            text-decoration: none;
        }
        
        .terms-checkbox a:hover {
            color: var(--matte-gold);
        }
        
        .btn-submit {
            width: 100%;
            padding: 16px;
            font-size: 1.1rem;
            font-weight: 600;
            margin-bottom: var(--spacing-md);
        }
        
        .divider {
            text-align: center;
            margin: var(--spacing-lg) 0;
            position: relative;
        }
        
        .divider::before {
            content: '';
            position: absolute;
            top: 50%;
            left: 0;
            right: 0;
            height: 1px;
            background: rgba(255, 255, 255, 0.1);
        }
        
        .divider span {
            background: var(--deep-black);
            padding: 0 var(--spacing-md);
            color: rgba(255, 255, 255, 0.5);
            font-size: 0.9rem;
        }
        
        .social-login {
            display: flex;
            gap: var(--spacing-md);
            margin-bottom: var(--spacing-lg);
        }
        
        .social-btn {
            flex: 1;
            padding: 12px;
            background: var(--glass-bg);
            border: 1px solid var(--glass-border);
            border-radius: var(--radius-md);
            color: white;
            text-decoration: none;
            display: flex;
            align-items: center;
            justify-content: center;
            gap: var(--spacing-sm);
            transition: all var(--transition-smooth);
        }
        
        .social-btn:hover {
            background: rgba(255, 255, 255, 0.1);
            border-color: var(--neon-gold);
            transform: translateY(-2px);
        }
        
        .back-to-home {
            text-align: center;
            margin-top: var(--spacing-xl);
        }
        
        .back-to-home a {
            color: var(--neon-gold);
            text-decoration: none;
            display: inline-flex;
            align-items: center;
            gap: var(--spacing-sm);
            transition: all var(--transition-smooth);
        }
        
        .back-to-home a:hover {
            color: var(--matte-gold);
            transform: translateX(-5px);
        }
        
        .validation-hint {
            font-size: 0.8rem;
            color: rgba(255, 255, 255, 0.5);
            margin-top: var(--spacing-xs);
        }
        
        @media (max-width: 480px) {
            .auth-container {
                max-width: 100%;
                padding: var(--spacing-md);
            }
            
            .social-login {
                flex-direction: column;
            }
            
            .terms-checkbox {
                align-items: flex-start;
            }
        }
    </style>
</head>
<body>
    <!-- Cinematic Background -->
    <div class="cinematic-bg"></div>
    <div class="ethiopian-pattern"></div>
    <div class="floating-particles"></div>
    
    <!-- Register Page -->
    <div class="auth-page">
        <div class="ethiopian-border">
            <div class="ethiopian-corner top-left"></div>
            <div class="ethiopian-corner top-right"></div>
            <div class="ethiopian-corner bottom-left"></div>
            <div class="ethiopian-corner bottom-right"></div>
        </div>
        
        <div class="auth-container glass-card">
            <div class="auth-header">
                <div class="auth-logo">እንቆቅልሽ</div>
                <h1 class="auth-title" data-i18n-key="signUp">Join the Journey</h1>
                <p class="auth-subtitle" data-i18n-key="dontHaveAccount">Create your account and start exploring Ethiopian heritage</p>
                <div class="language-switcher" style="margin-top: 1rem; justify-content: center;">
                    <select id="languageSelector" class="language-selector" aria-label="Select language">
                        <option value="en">English</option>
                        <option value="am">አማርኛ</option>
                        <option value="om">Afaan Oromoo</option>
                    </select>
                </div>
            </div>

            <?php if ($error): ?>
                <div class="alert alert-error">
                    <?php echo htmlspecialchars($error); ?>
                </div>
            <?php endif; ?>
            
            <?php if ($success): ?>
                <div class="alert alert-success">
                    <?php echo htmlspecialchars($success); ?>
                </div>
            <?php endif; ?>
            
            <form id="registerForm" method="POST" class="auth-form">
                <div class="input-group">
                    <span class="input-icon">👤</span>
                    <input 
                        type="text" 
                        name="username" 
                        class="input-field" 
                        placeholder="Username"
                        data-i18n-placeholder="usernameOrEmail"
                        value="<?php echo isset($_POST['username']) ? htmlspecialchars($_POST['username']) : ''; ?>"
                        required
                        autocomplete="username"
                        minlength="3"
                        maxlength="50"
                    >
                    <div class="validation-hint">3-50 characters, letters and numbers only</div>
                </div>
                
                <div class="input-group">
                    <span class="input-icon">📧</span>
                    <input 
                        type="email" 
                        name="email" 
                        class="input-field" 
                        placeholder="Email Address"
                        value="<?php echo isset($_POST['email']) ? htmlspecialchars($_POST['email']) : ''; ?>"
                        required
                        autocomplete="email"
                    >
                </div>
                
                <div class="input-group">
                    <span class="input-icon">👋</span>
                    <input 
                        type="text" 
                        name="full_name" 
                        class="input-field" 
                        placeholder="Full Name (Optional)"
                        value="<?php echo isset($_POST['full_name']) ? htmlspecialchars($_POST['full_name']) : ''; ?>"
                        autocomplete="name"
                    >
                </div>
                
                <div class="input-group">
                    <span class="input-icon">🔒</span>
                    <input 
                        type="password" 
                        name="password" 
                        class="input-field" 
                        placeholder="Password"
                        required
                        autocomplete="new-password"
                        minlength="6"
                        id="password"
                    >
                    <div class="password-strength">
                        <div class="password-strength-bar" id="strengthBar"></div>
                    </div>
                    <div class="validation-hint">At least 6 characters</div>
                </div>
                
                <div class="input-group">
                    <span class="input-icon">🔐</span>
                    <input 
                        type="password" 
                        name="confirm_password" 
                        class="input-field" 
                        placeholder="Confirm Password"
                        required
                        autocomplete="new-password"
                        id="confirmPassword"
                    >
                </div>
                
                <div class="terms-checkbox">
                    <input type="checkbox" name="terms" id="terms" required>
                    <label for="terms">
                        I agree to the <a href="#">Terms of Service</a> and <a href="#">Privacy Policy</a>
                    </label>
                </div>
                
                <button type="submit" class="btn btn-primary btn-submit">
                    <span data-i18n-key="signUp">Create Account</span>
                </button>
            </form>
            
            <div class="divider">
                <span>OR</span>
            </div>
            
            <div class="social-login">
                <a href="#" class="social-btn">
                    <span>📧</span>
                    <span>Google</span>
                </a>
                <a href="#" class="social-btn">
                    <span>📘</span>
                    <span>Facebook</span>
                </a>
            </div>
            
            <div class="form-footer">
                <p>Already have an account? <a href="login.php" style="color: var(--neon-gold);">Sign in</a></p>
            </div>
            
            <div class="back-to-home">
                <a href="index.php">
                    <span>←</span>
                    <span>Back to Home</span>
                </a>
            </div>
        </div>
    </div>
    
    <!-- JavaScript -->
    <script src="assets/js/script.js"></script>
    
    <script>
        // Password strength checker
        function checkPasswordStrength(password) {
            let strength = 0;
            const strengthBar = document.getElementById('strengthBar');
            
            if (password.length >= 6) strength++;
            if (password.length >= 10) strength++;
            if (/[a-z]/.test(password) && /[A-Z]/.test(password)) strength++;
            if (/[0-9]/.test(password)) strength++;
            if (/[^a-zA-Z0-9]/.test(password)) strength++;
            
            strengthBar.className = 'password-strength-bar';
            
            if (password.length === 0) {
                strengthBar.style.width = '0';
            } else if (strength <= 2) {
                strengthBar.classList.add('strength-weak');
            } else if (strength <= 3) {
                strengthBar.classList.add('strength-medium');
            } else {
                strengthBar.classList.add('strength-strong');
            }
        }
        
        // Real-time password validation
        document.getElementById('password').addEventListener('input', function() {
            checkPasswordStrength(this.value);
            validatePasswordMatch();
        });
        
        document.getElementById('confirmPassword').addEventListener('input', validatePasswordMatch);
        
        function validatePasswordMatch() {
            const password = document.getElementById('password').value;
            const confirmPassword = document.getElementById('confirmPassword').value;
            const confirmField = document.getElementById('confirmPassword');
            
            if (confirmPassword && password !== confirmPassword) {
                confirmField.style.borderColor = '#ff6b6b';
            } else if (confirmPassword && password === confirmPassword) {
                confirmField.style.borderColor = '#00ff88';
            } else {
                confirmField.style.borderColor = '';
            }
        }
        
        // Username validation
        document.querySelector('input[name="username"]').addEventListener('input', function() {
            const username = this.value;
            const validPattern = /^[a-zA-Z0-9_]+$/;
            
            if (username && !validPattern.test(username)) {
                this.style.borderColor = '#ff6b6b';
            } else {
                this.style.borderColor = '';
            }
        });
        
        // Form submission
        document.getElementById('registerForm').addEventListener('submit', function(e) {
            const username = document.querySelector('input[name="username"]').value.trim();
            const email = document.querySelector('input[name="email"]').value.trim();
            const password = document.getElementById('password').value;
            const confirmPassword = document.getElementById('confirmPassword').value;
            const terms = document.getElementById('terms').checked;
            
            // Validation
            if (!username || !email || !password || !confirmPassword) {
                e.preventDefault();
                window.ethiopianQuizApp.showNotification('Please fill in all required fields', 'error');
                return;
            }
            
            if (username.length < 3) {
                e.preventDefault();
                window.ethiopianQuizApp.showNotification('Username must be at least 3 characters long', 'error');
                return;
            }
            
            if (password.length < 6) {
                e.preventDefault();
                window.ethiopianQuizApp.showNotification('Password must be at least 6 characters long', 'error');
                return;
            }
            
            if (password !== confirmPassword) {
                e.preventDefault();
                window.ethiopianQuizApp.showNotification('Passwords do not match', 'error');
                return;
            }
            
            if (!terms) {
                e.preventDefault();
                window.ethiopianQuizApp.showNotification('Please accept the terms and conditions', 'error');
                return;
            }
            
            // Show loading state
            const submitBtn = document.querySelector('.btn-submit');
            const originalText = submitBtn.innerHTML;
            submitBtn.innerHTML = '<span>Creating Account...</span>';
            submitBtn.disabled = true;
            
            // Reset button after 5 seconds (in case of server issues)
            setTimeout(() => {
                submitBtn.innerHTML = originalText;
                submitBtn.disabled = false;
            }, 5000);
        });
        
        // Add input field animations
        document.querySelectorAll('.input-field').forEach(field => {
            field.addEventListener('focus', function() {
                this.parentElement.classList.add('focused');
            });
            
            field.addEventListener('blur', function() {
                if (!this.value) {
                    this.parentElement.classList.remove('focused');
                }
            });
        });
        
        // Handle social login
        document.querySelectorAll('.social-btn').forEach(btn => {
            btn.addEventListener('click', function(e) {
                e.preventDefault();
                const platform = this.textContent.trim();
                window.ethiopianQuizApp.showNotification(`${platform} registration coming soon!`, 'info');
            });
        });
        
        // Handle terms links
        document.querySelectorAll('.terms-checkbox a').forEach(link => {
            link.addEventListener('click', function(e) {
                e.preventDefault();
                const text = this.textContent;
                window.ethiopianQuizApp.showNotification(`${text} will be available soon!`, 'info');
            });
        });
    </script>
</body>
</html>
