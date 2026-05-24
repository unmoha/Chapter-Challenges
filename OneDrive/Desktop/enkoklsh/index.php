<?php
session_start();
require_once 'config/db.php';

// Initialize database connection
$database = new Database();
$db = $database->getConnection();
?>
<!DOCTYPE html>
<html lang="am" dir="ltr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>እንቆቅልሽ - Ethiopian Quiz Game | Test Your Knowledge</title>
    <meta name="description" content="Premium Ethiopian quiz application with cinematic design. Test your knowledge of Ethiopian history, culture, and more.">
    <meta name="keywords" content="Ethiopian quiz, እንቆቅልሽ, Ethiopian culture, African quiz, educational game">
    <meta name="author" content="እንቆቅልሽ Team">
    
    <!-- Open Graph Meta Tags -->
    <meta property="og:title" content="እንቆቅልሽ - Ethiopian Quiz Game">
    <meta property="og:description" content="Premium Ethiopian quiz application with cinematic design">
    <meta property="og:type" content="website">
    <meta property="og:url" content="<?php echo (isset($_SERVER['HTTPS']) && $_SERVER['HTTPS'] === 'on' ? "https" : "http") . "://$_SERVER[HTTP_HOST]$_SERVER[REQUEST_URI]"; ?>">
    
    <!-- Favicon -->
    <link rel="icon" type="image/x-icon" href="assets/images/favicon.ico">
    
    <!-- Google Fonts -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Segoe+UI:wght@300;400;500;600;700&family=Georgia:wght@400;700&family=Noto+Sans+Ethiopic:wght@400;700;900&display=swap" rel="stylesheet">
    
    <!-- CSS -->
    <link rel="stylesheet" href="assets/css/style.css">
    
    <!-- Schema.org Structured Data -->
    <script type="application/ld+json">
    {
        "@context": "https://schema.org",
        "@type": "WebApplication",
        "name": "እንቆቅልሽ",
        "description": "Premium Ethiopian quiz application with cinematic design",
        "url": "<?php echo (isset($_SERVER['HTTPS']) && $_SERVER['HTTPS'] === 'on' ? "https" : "http") . "://$_SERVER[HTTP_HOST]"; ?>",
        "applicationCategory": "EducationalApplication",
        "operatingSystem": "Web Browser",
        "offers": {
            "@type": "Offer",
            "price": "0",
            "priceCurrency": "USD"
        }
    }
    </script>
</head>
<body>
    <!-- Cinematic Background -->
    <div class="cinematic-bg"></div>
    <div class="ethiopian-pattern"></div>
    <div class="floating-particles"></div>
    
    <!-- Navigation -->
    <nav class="navbar">
        <div class="navbar-content">
            <a href="index.php" class="navbar-brand">እንቆቅልሽ</a>
            <div class="navbar-nav">
                <a href="#home" class="navbar-link" data-i18n-key="home">Home</a>
                <a href="#categories" class="navbar-link" data-i18n-key="categories">Categories</a>
                <a href="leaderboard.php" class="navbar-link" data-i18n-key="leaderboard">Leaderboard</a>
                <?php if (isset($_SESSION['user_id'])): ?>
                    <a href="dashboard.php" class="navbar-link">Dashboard</a>
                    <a href="logout.php" class="navbar-link">Logout</a>
                <?php else: ?>
                    <a href="login.php" class="navbar-link" data-i18n-key="login">Login</a>
                    <a href="register.php" class="navbar-link" data-i18n-key="register">Register</a>
                <?php endif; ?>
                <div class="language-switcher">
                    <select id="languageSelector" class="language-selector" aria-label="Select language">
                        <option value="en">English</option>
                        <option value="am">አማርኛ</option>
                        <option value="om">Afaan Oromoo</option>
                    </select>
                </div>
                <button class="btn btn-ghost" id="soundToggle">
                    <span id="soundIcon">🔊</span>
                </button>
            </div>
        </div>
    </nav>
    
    <!-- Hero Section -->
    <section class="hero-section" id="home">
        <div class="ethiopian-border">
            <div class="ethiopian-corner top-left"></div>
            <div class="ethiopian-corner top-right"></div>
            <div class="ethiopian-corner bottom-left"></div>
            <div class="ethiopian-corner bottom-right"></div>
        </div>
        
        <div class="hero-content">
            <h1 class="amharic-title">እንቆቅልሽ</h1>
            <p class="hero-subtitle" data-i18n-key="home">Test Your Knowledge</p>
            <p class="hero-description">
                Embark on a cinematic journey through Ethiopian heritage and global knowledge. 
                Experience the perfect fusion of ancient wisdom and modern technology in this 
                premium quiz platform designed to celebrate Ethiopian culture while challenging 
                your intellect across multiple categories.
            </p>
            
            <div class="hero-buttons">
                <?php if (isset($_SESSION['user_id'])): ?>
                    <a href="dashboard.php" class="btn btn-primary">
                        <span data-i18n-key="continuePlaying">Continue Playing</span>
                    </a>
                <?php else: ?>
                    <a href="register.php" class="btn btn-primary">
                        <span data-i18n-key="startJourney">Start Journey</span>
                    </a>
                    <a href="login.php" class="btn btn-secondary">
                        <span data-i18n-key="signIn">Sign In</span>
                    </a>
                <?php endif; ?>
                <a href="#categories" class="btn btn-ghost">
                    <span data-i18n-key="exploreCategories">Explore Categories</span>
                </a>
            </div>
        </div>
    </section>
    
    <!-- Features Section -->
    <section class="section-features">
        <div class="container">
            <h2 class="section-title">Premium Features</h2>
            <div class="features-grid">
                <div class="feature-card glass-card animate-on-scroll">
                    <div class="feature-icon">🏛️</div>
                    <h3>Ethiopian Heritage</h3>
                    <p>Dive deep into Ethiopia's rich history, culture, and traditions with carefully crafted questions.</p>
                </div>
                <div class="feature-card glass-card animate-on-scroll">
                    <div class="feature-icon">🎮</div>
                    <h3>Cinematic Gaming</h3>
                    <p>Experience AAA-quality visuals, smooth animations, and immersive gameplay.</p>
                </div>
                <div class="feature-card glass-card animate-on-scroll">
                    <div class="feature-icon">🏆</div>
                    <h3>Global Leaderboard</h3>
                    <p>Compete with players worldwide and climb the ranks to become a quiz champion.</p>
                </div>
                <div class="feature-card glass-card animate-on-scroll">
                    <div class="feature-icon">📊</div>
                    <h3>Advanced Analytics</h3>
                    <p>Track your progress, analyze your performance, and improve your knowledge.</p>
                </div>
                <div class="feature-card glass-card animate-on-scroll">
                    <div class="feature-icon">🎯</div>
                    <h3>8 Categories</h3>
                    <p>Explore diverse topics from Ethiopian history to science, technology, and beyond.</p>
                </div>
                <div class="feature-card glass-card animate-on-scroll">
                    <div class="feature-icon">⚡</div>
                    <h3>Real-time Scoring</h3>
                    <p>Get instant feedback, earn bonus points for speed, and master the art of quick thinking.</p>
                </div>
            </div>
        </div>
    </section>
    
    <!-- Categories Section -->
    <section class="section-categories" id="categories">
        <div class="container">
            <h2 class="section-title" data-i18n-key="chooseYourChallenge">Choose Your Challenge</h2>
            <div class="category-grid">
                <?php
                // Fetch categories from database
                $categories_query = "SELECT * FROM categories ORDER BY name";
                $categories_stmt = $db->prepare($categories_query);
                $categories_stmt->execute();
                $categories = $categories_stmt->fetchAll(PDO::FETCH_ASSOC);
                
                foreach ($categories as $category):
                ?>
                <div class="category-card" data-category-id="<?php echo $category['id']; ?>">
                    <div class="category-icon" style="color: <?php echo $category['color']; ?>">
                        <?php echo htmlspecialchars($category['icon']); ?>
                    </div>
                    <h3 class="category-title"><?php echo htmlspecialchars($category['name']); ?></h3>
                    <p class="category-description"><?php echo htmlspecialchars($category['description']); ?></p>
                    <div class="category-stats">
                        <span class="stat-item">
                            <span class="stat-label">Questions</span>
                            <span class="stat-value">
                                <?php
                                $count_query = "SELECT COUNT(*) as count FROM questions WHERE category_id = ?";
                                $count_stmt = $db->prepare($count_query);
                                $count_stmt->execute([$category['id']]);
                                $count = $count_stmt->fetch(PDO::FETCH_ASSOC);
                                echo $count['count'];
                                ?>
                            </span>
                        </span>
                    </div>
                </div>
                <?php endforeach; ?>
            </div>
        </div>
    </section>
    
    <!-- Statistics Section -->
    <section class="section-stats">
        <div class="container">
            <h2 class="section-title">Platform Statistics</h2>
            <div class="stats-grid">
                <div class="stat-card glass-card">
                    <div class="stat-number" data-target="1000">0</div>
                    <div class="stat-label">Active Players</div>
                </div>
                <div class="stat-card glass-card">
                    <div class="stat-number" data-target="80">0</div>
                    <div class="stat-label">Total Questions</div>
                </div>
                <div class="stat-card glass-card">
                    <div class="stat-number" data-target="8">0</div>
                    <div class="stat-label">Categories</div>
                </div>
                <div class="stat-card glass-card">
                    <div class="stat-number" data-target="5000">0</div>
                    <div class="stat-label">Games Played</div>
                </div>
            </div>
        </div>
    </section>
    
    <!-- Testimonials Section -->
    <section class="section-testimonials">
        <div class="container">
            <h2 class="section-title">What Players Say</h2>
            <div class="testimonials-grid">
                <div class="testimonial-card glass-card">
                    <div class="testimonial-content">
                        <p>"እንቆቅልሽ is not just a game, it's a celebration of our Ethiopian heritage. The cinematic design makes learning addictive!"</p>
                    </div>
                    <div class="testimonial-author">
                        <div class="auth-logo">እንቆቅልሽ</div>
                        <div class="author-info">
                            <div class="author-name">Alemayehu T.</div>
                            <div class="author-title">Student, Addis Ababa</div>
                        </div>
                    </div>
                </div>
                <div class="testimonial-card glass-card">
                    <div class="testimonial-content">
                        <p>"The perfect blend of education and entertainment. I've learned so much about Ethiopian history while having fun!"</p>
                    </div>
                    <div class="testimonial-author">
                        <div class="author-avatar">👩</div>
                        <div class="author-info">
                            <div class="author-name">Sara M.</div>
                            <div class="author-title">Teacher, Bahir Dar</div>
                        </div>
                    </div>
                </div>
                <div class="testimonial-card glass-card">
                    <div class="testimonial-content">
                        <p>"Finally, a world-class Ethiopian app! The design quality rivals international platforms. Proud to be Ethiopian!"</p>
                    </div>
                    <div class="testimonial-author">
                        <div class="author-avatar">👨</div>
                        <div class="author-info">
                            <div class="author-name">Dawit K.</div>
                            <div class="author-title">Developer, Mekelle</div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>
    
    <!-- Footer -->
    <footer class="footer">
        <div class="container">
            <div class="footer-content">
                <div class="footer-section">
                    <h3>እናቆቅልሽ</h3>
                    <p>Premium Ethiopian Quiz Application</p>
                    <p>Celebrating Heritage Through Technology</p>
                </div>
                <div class="footer-section">
                    <h4>Quick Links</h4>
                    <ul>
                        <li><a href="index.php">Home</a></li>
                        <li><a href="login.php">Login</a></li>
                        <li><a href="register.php">Register</a></li>
                        <li><a href="leaderboard.php">Leaderboard</a></li>
                    </ul>
                </div>
                <div class="footer-section">
                    <h4>Categories</h4>
                    <ul>
                        <li><a href="#categories">Ethiopian History</a></li>
                        <li><a href="#categories">Ethiopian Culture</a></li>
                        <li><a href="#categories">Science & Tech</a></li>
                        <li><a href="#categories">General Knowledge</a></li>
                    </ul>
                </div>
                <div class="footer-section">
                    <h4>Connect</h4>
                    <div class="social-links">
                        <a href="#" class="social-link">📧</a>
                        <a href="#" class="social-link">💬</a>
                        <a href="#" class="social-link">📱</a>
                        <a href="#" class="social-link">🌐</a>
                    </div>
                </div>
            </div>
            <div class="footer-bottom">
                <p>&copy; <?php echo date('Y'); ?> እናቆቅልሽ. All rights reserved. Made with ❤️ in Ethiopia.</p>
            </div>
        </div>
    </footer>
    
    <!-- JavaScript -->
    <script src="assets/js/script.js"></script>
    
    <!-- Additional CSS for sections -->
    <style>
        .container {
            max-width: 1200px;
            margin: 0 auto;
            padding: 0 var(--spacing-lg);
        }
        
        .section-title {
            text-align: center;
            font-size: 2.5rem;
            color: var(--neon-gold);
            margin-bottom: var(--spacing-xl);
            font-weight: 700;
        }
        
        .section-features {
            padding: var(--spacing-xl) 0;
            background: rgba(0, 0, 0, 0.3);
        }
        
        .features-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(300px, 1fr));
            gap: var(--spacing-lg);
        }
        
        .feature-card {
            padding: var(--spacing-lg);
            text-align: center;
            transition: all var(--transition-smooth);
        }
        
        .feature-icon {
            font-size: 3rem;
            margin-bottom: var(--spacing-md);
            filter: drop-shadow(0 0 10px currentColor);
        }
        
        .feature-card h3 {
            color: var(--neon-gold);
            margin-bottom: var(--spacing-sm);
        }
        
        .section-categories {
            padding: var(--spacing-xl) 0;
        }
        
        .category-stats {
            margin-top: var(--spacing-md);
            padding-top: var(--spacing-md);
            border-top: 1px solid rgba(255, 255, 255, 0.1);
        }
        
        .stat-item {
            display: flex;
            justify-content: space-between;
            align-items: center;
            font-size: 0.9rem;
        }
        
        .stat-label {
            color: rgba(255, 255, 255, 0.7);
        }
        
        .stat-value {
            color: var(--neon-gold);
            font-weight: 700;
        }
        
        .section-stats {
            padding: var(--spacing-xl) 0;
            background: rgba(0, 0, 0, 0.3);
        }
        
        .stats-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(200px, 1fr));
            gap: var(--spacing-lg);
        }
        
        .stat-card {
            text-align: center;
            padding: var(--spacing-xl);
        }
        
        .stat-number {
            font-size: 3rem;
            font-weight: 900;
            color: var(--neon-gold);
            margin-bottom: var(--spacing-sm);
        }
        
        .section-testimonials {
            padding: var(--spacing-xl) 0;
        }
        
        .testimonials-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(300px, 1fr));
            gap: var(--spacing-lg);
        }
        
        .testimonial-card {
            padding: var(--spacing-lg);
        }
        
        .testimonial-content {
            margin-bottom: var(--spacing-md);
            font-style: italic;
            line-height: 1.6;
        }
        
        .testimonial-author {
            display: flex;
            align-items: center;
            gap: var(--spacing-sm);
        }
        
        .author-avatar {
            width: 50px;
            height: 50px;
            background: var(--glass-bg);
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 1.5rem;
        }
        
        .author-name {
            font-weight: 600;
            color: var(--neon-gold);
        }
        
        .author-title {
            font-size: 0.9rem;
            color: rgba(255, 255, 255, 0.7);
        }
        
        .footer {
            background: var(--dark-charcoal);
            padding: var(--spacing-xl) 0 var(--spacing-lg);
            border-top: 1px solid var(--glass-border);
        }
        
        .footer-content {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(250px, 1fr));
            gap: var(--spacing-lg);
            margin-bottom: var(--spacing-lg);
        }
        
        .footer-section h3,
        .footer-section h4 {
            color: var(--neon-gold);
            margin-bottom: var(--spacing-sm);
        }
        
        .footer-section ul {
            list-style: none;
        }
        
        .footer-section ul li {
            margin-bottom: var(--spacing-xs);
        }
        
        .footer-section a {
            color: rgba(255, 255, 255, 0.7);
            text-decoration: none;
            transition: color var(--transition-smooth);
        }
        
        .footer-section a:hover {
            color: var(--neon-gold);
        }
        
        .social-links {
            display: flex;
            gap: var(--spacing-sm);
        }
        
        .social-link {
            width: 40px;
            height: 40px;
            background: var(--glass-bg);
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            text-decoration: none;
            transition: all var(--transition-smooth);
        }
        
        .social-link:hover {
            background: var(--neon-gold);
            transform: translateY(-3px);
        }
        
        .footer-bottom {
            text-align: center;
            padding-top: var(--spacing-lg);
            border-top: 1px solid var(--glass-border);
            color: rgba(255, 255, 255, 0.7);
        }
        
        @media (max-width: 768px) {
            .container {
                padding: 0 var(--spacing-md);
            }
            
            .section-title {
                font-size: 2rem;
            }
            
            .features-grid,
            .stats-grid,
            .testimonials-grid {
                grid-template-columns: 1fr;
            }
            
            .footer-content {
                grid-template-columns: 1fr;
                text-align: center;
            }
            
            .social-links {
                justify-content: center;
            }
        }
    </style>
    
    <script>
        // Animate statistics numbers
        function animateNumbers() {
            const numbers = document.querySelectorAll('.stat-number');
            
            numbers.forEach(number => {
                const target = parseInt(number.dataset.target);
                const duration = 2000;
                const increment = target / (duration / 16);
                let current = 0;
                
                const updateNumber = () => {
                    current += increment;
                    if (current < target) {
                        number.textContent = Math.floor(current);
                        requestAnimationFrame(updateNumber);
                    } else {
                        number.textContent = target;
                    }
                };
                
                // Start animation when element is in viewport
                const observer = new IntersectionObserver((entries) => {
                    entries.forEach(entry => {
                        if (entry.isIntersecting) {
                            updateNumber();
                            observer.unobserve(entry.target);
                        }
                    });
                });
                
                observer.observe(number);
            });
        }
        
        // Initialize animations when DOM is loaded
        document.addEventListener('DOMContentLoaded', () => {
            animateNumbers();
        });
    </script>
</body>
</html>
