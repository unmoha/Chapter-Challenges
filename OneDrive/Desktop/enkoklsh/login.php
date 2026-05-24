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

// Handle login form submission
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $username = sanitize_input($_POST['username']);
    $password = $_POST['password'];
    
    if (empty($username) || empty($password)) {
        $error = 'Please fill in all fields';
    } else {
        try {
            // Check user credentials
            $query = "SELECT id, username, email, password, full_name FROM users WHERE username = ? OR email = ?";
            $stmt = $db->prepare($query);
            $stmt->execute([$username, $username]);
            $user = $stmt->fetch(PDO::FETCH_ASSOC);
            
            if ($user && verify_password($password, $user['password'])) {
                // Login successful
                $_SESSION['user_id'] = $user['id'];
                $_SESSION['username'] = $user['username'];
                $_SESSION['email'] = $user['email'];
                $_SESSION['full_name'] = $user['full_name'];
                
                header('Location: dashboard.php');
                exit();
            } else {
                $error = 'Invalid username or password';
            }
        } catch(PDOException $e) {
            $error = 'Login failed. Please try again.';
        }
    }
}
?>
<!DOCTYPE html>
<html lang="am" dir="ltr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login - እንቆቅልሽ | Ethiopian Quiz Game</title>
    <meta name="description" content="Login to access the premium Ethiopian quiz application">
    
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
            max-width: 450px;
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
        
        .form-options {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-bottom: var(--spacing-lg);
        }
        
        .remember-me {
            display: flex;
            align-items: center;
            gap: var(--spacing-sm);
        }
        
        .remember-me input[type="checkbox"] {
            width: 18px;
            height: 18px;
            accent-color: var(--neon-gold);
        }
        
        .forgot-link {
            color: var(--neon-gold);
            text-decoration: none;
            font-size: 0.9rem;
            transition: color var(--transition-smooth);
        }
        
        .forgot-link:hover {
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
        
        @media (max-width: 480px) {
            .auth-container {
                max-width: 100%;
                padding: var(--spacing-md);
            }
            
            .form-options {
                flex-direction: column;
                gap: var(--spacing-md);
                align-items: stretch;
            }
            
            .social-login {
                flex-direction: column;
            }
        }
    </style>
</head>
<body>
    <!-- Cinematic Background -->
    <div class="cinematic-bg"></div>
    <div class="ethiopian-pattern"></div>
    <div class="floating-particles"></div>
    
    <!-- Login Page -->
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
                <h1 class="auth-title" data-i18n-key="signIn">Welcome Back</h1>
                <p class="auth-subtitle" data-i18n-key="signInToContinue">Sign in to continue your journey</p>
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
            
            <form id="loginForm" method="POST" class="auth-form">
                <div class="input-group">
                    <span class="input-icon">👤</span>
                    <input 
                        type="text" 
                        name="username" 
                        class="input-field" 
                        placeholder="Username or Email"
                        data-i18n-placeholder="usernameOrEmail"
                        required
                        autocomplete="username"
                    >
                </div>
                
                <div class="input-group">
                    <span class="input-icon">🔒</span>
                    <input 
                        type="password" 
                        name="password" 
                        class="input-field" 
                        placeholder="Password"
                        data-i18n-placeholder="password"
                        required
                        autocomplete="current-password"
                    >
                </div>
                
                <div class="form-options">
                    <label class="remember-me">
                        <input type="checkbox" name="remember">
                        <span data-i18n-key="rememberMe">Remember me</span>
                    </label>
                    <a href="#" class="forgot-link" data-i18n-key="forgotPassword">Forgot password?</a>
                </div>
                
                <button type="submit" class="btn btn-primary btn-submit">
                    <span data-i18n-key="signIn">Sign In</span>
                </button>
            </form>
            
            <div class="divider">
                <span data-i18n-key="orText">OR</span>
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
                <p><span data-i18n-key="dontHaveAccount">Don't have an account?</span> <a href="register.php" style="color: var(--neon-gold);" data-i18n-key="signUp">Sign up</a></p>
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
        // Add form validation feedback
        document.getElementById('loginForm').addEventListener('submit', function(e) {
            const username = document.querySelector('input[name="username"]').value.trim();
            const password = document.querySelector('input[name="password"]').value;
            
            if (!username || !password) {
                e.preventDefault();
                window.ethiopianQuizApp.showNotification('Please fill in all fields', 'error');
                return;
            }
            
            // Show loading state
            const submitBtn = document.querySelector('.btn-submit');
            const originalText = submitBtn.innerHTML;
            submitBtn.innerHTML = '<span>Signing in...</span>';
            submitBtn.disabled = true;
            
            // Reset button after 3 seconds (in case of server issues)
            setTimeout(() => {
                submitBtn.innerHTML = originalText;
                submitBtn.disabled = false;
            }, 3000);
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
        
        // Handle forgot password
        document.querySelector('.forgot-link').addEventListener('click', function(e) {
            e.preventDefault();
            window.ethiopianQuizApp.showNotification('Password reset feature coming soon!', 'info');
        });
        
        // Handle social login
        document.querySelectorAll('.social-btn').forEach(btn => {
            btn.addEventListener('click', function(e) {
                e.preventDefault();
                const platform = this.textContent.trim();
                window.ethiopianQuizApp.showNotification(`${platform} login coming soon!`, 'info');
            });
        });
    </script>
</body>
</html>
