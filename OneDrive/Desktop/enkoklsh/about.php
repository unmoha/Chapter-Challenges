<?php
session_start();
require_once 'config/db.php';
?>
<!DOCTYPE html>
<html lang="am" dir="ltr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>About - እንቆቅልሽ | Ethiopian Quiz Game</title>
    <meta name="description" content="Learn about እንቆቅልሽ - the premium Ethiopian quiz application celebrating Ethiopian heritage through technology">
    
    <!-- Google Fonts -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Segoe+UI:wght@300;400;500;600;700&family=Georgia:wght@400;700&family=Noto+Sans+Ethiopic:wght@400;700;900&display=swap" rel="stylesheet">
    
    <!-- CSS -->
    <link rel="stylesheet" href="assets/css/style.css">
    
    <style>
        .about-page {
            min-height: 100vh;
            padding-top: 80px;
            position: relative;
        }
        
        .about-container {
            max-width: 1200px;
            margin: 0 auto;
            padding: var(--spacing-lg);
        }
        
        .about-hero {
            text-align: center;
            margin-bottom: var(--spacing-xl);
        }
        
        .about-title {
            font-size: 3rem;
            color: var(--neon-gold);
            margin-bottom: var(--spacing-md);
            font-weight: 700;
        }
        
        .about-subtitle {
            font-size: 1.3rem;
            color: rgba(255, 255, 255, 0.7);
            margin-bottom: var(--spacing-lg);
        }
        
        .mission-section {
            background: var(--glass-bg);
            backdrop-filter: blur(20px);
            border: 1px solid var(--glass-border);
            border-radius: var(--radius-lg);
            padding: var(--spacing-xl);
            margin-bottom: var(--spacing-xl);
            text-align: center;
        }
        
        .mission-title {
            font-size: 2rem;
            color: var(--neon-gold);
            margin-bottom: var(--spacing-md);
        }
        
        .mission-text {
            font-size: 1.1rem;
            line-height: 1.8;
            color: rgba(255, 255, 255, 0.8);
            max-width: 800px;
            margin: 0 auto;
        }
        
        .features-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(300px, 1fr));
            gap: var(--spacing-lg);
            margin-bottom: var(--spacing-xl);
        }
        
        .feature-card {
            background: var(--glass-bg);
            backdrop-filter: blur(20px);
            border: 1px solid var(--glass-border);
            border-radius: var(--radius-lg);
            padding: var(--spacing-lg);
            text-align: center;
            transition: all var(--transition-smooth);
        }
        
        .feature-card:hover {
            transform: translateY(-5px);
            box-shadow: 
                var(--glass-shadow),
                0 0 30px rgba(255, 215, 0, 0.3);
            border-color: rgba(255, 215, 0, 0.5);
        }
        
        .feature-icon {
            font-size: 3rem;
            margin-bottom: var(--spacing-md);
            filter: drop-shadow(0 0 10px currentColor);
        }
        
        .feature-title {
            font-size: 1.3rem;
            font-weight: 600;
            color: var(--neon-gold);
            margin-bottom: var(--spacing-sm);
        }
        
        .feature-description {
            color: rgba(255, 255, 255, 0.7);
            line-height: 1.6;
        }
        
        .team-section {
            background: var(--glass-bg);
            backdrop-filter: blur(20px);
            border: 1px solid var(--glass-border);
            border-radius: var(--radius-lg);
            padding: var(--spacing-xl);
            margin-bottom: var(--spacing-xl);
        }
        
        .team-title {
            font-size: 2rem;
            color: var(--neon-gold);
            margin-bottom: var(--spacing-lg);
            text-align: center;
        }
        
        .team-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(250px, 1fr));
            gap: var(--spacing-lg);
        }
        
        .team-member {
            text-align: center;
            padding: var(--spacing-lg);
            background: rgba(255, 255, 255, 0.05);
            border-radius: var(--radius-md);
            transition: all var(--transition-smooth);
        }
        
        .team-member:hover {
            background: rgba(255, 255, 255, 0.08);
            transform: translateY(-3px);
        }
        
        .member-avatar {
            width: 80px;
            height: 80px;
            background: linear-gradient(135deg, var(--matte-gold), var(--bronze));
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 2rem;
            font-weight: 700;
            color: var(--deep-black);
            margin: 0 auto var(--spacing-md);
        }
        
        .member-name {
            font-size: 1.2rem;
            font-weight: 600;
            color: var(--neon-gold);
            margin-bottom: var(--spacing-xs);
        }
        
        .member-role {
            color: rgba(255, 255, 255, 0.7);
            margin-bottom: var(--spacing-sm);
        }
        
        .member-bio {
            color: rgba(255, 255, 255, 0.6);
            font-size: 0.9rem;
            line-height: 1.5;
        }
        
        .stats-section {
            background: var(--glass-bg);
            backdrop-filter: blur(20px);
            border: 1px solid var(--glass-border);
            border-radius: var(--radius-lg);
            padding: var(--spacing-xl);
            margin-bottom: var(--spacing-xl);
        }
        
        .stats-title {
            font-size: 2rem;
            color: var(--neon-gold);
            margin-bottom: var(--spacing-lg);
            text-align: center;
        }
        
        .stats-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(200px, 1fr));
            gap: var(--spacing-lg);
            text-align: center;
        }
        
        .stat-item {
            padding: var(--spacing-lg);
        }
        
        .stat-number {
            font-size: 2.5rem;
            font-weight: 700;
            color: var(--neon-gold);
            margin-bottom: var(--spacing-sm);
        }
        
        .stat-label {
            color: rgba(255, 255, 255, 0.7);
            font-size: 1rem;
        }
        
        .contact-section {
            background: var(--glass-bg);
            backdrop-filter: blur(20px);
            border: 1px solid var(--glass-border);
            border-radius: var(--radius-lg);
            padding: var(--spacing-xl);
            text-align: center;
        }
        
        .contact-title {
            font-size: 2rem;
            color: var(--neon-gold);
            margin-bottom: var(--spacing-lg);
        }
        
        .contact-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(250px, 1fr));
            gap: var(--spacing-lg);
            margin-bottom: var(--spacing-lg);
        }
        
        .contact-item {
            display: flex;
            align-items: center;
            gap: var(--spacing-md);
            padding: var(--spacing-md);
            background: rgba(255, 255, 255, 0.05);
            border-radius: var(--radius-md);
            transition: all var(--transition-smooth);
        }
        
        .contact-item:hover {
            background: rgba(255, 255, 255, 0.08);
            transform: translateY(-2px);
        }
        
        .contact-icon {
            font-size: 1.5rem;
            color: var(--neon-gold);
        }
        
        .contact-text {
            color: rgba(255, 255, 255, 0.8);
        }
        
        .social-links {
            display: flex;
            justify-content: center;
            gap: var(--spacing-md);
            margin-top: var(--spacing-lg);
        }
        
        .social-link {
            width: 50px;
            height: 50px;
            background: rgba(255, 255, 255, 0.1);
            border: 1px solid var(--glass-border);
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            text-decoration: none;
            font-size: 1.5rem;
            transition: all var(--transition-smooth);
        }
        
        .social-link:hover {
            background: var(--neon-gold);
            color: var(--deep-black);
            transform: translateY(-3px);
        }
        
        @media (max-width: 768px) {
            .about-title {
                font-size: 2rem;
            }
            
            .features-grid,
            .team-grid,
            .stats-grid,
            .contact-grid {
                grid-template-columns: 1fr;
            }
            
            .social-links {
                flex-wrap: wrap;
            }
        }
    </style>
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
                <a href="index.php" class="navbar-link" data-i18n-key="home">Home</a>
                <a href="about.php" class="navbar-link">About</a>
                <?php if (isset($_SESSION['user_id'])): ?>
                    <a href="dashboard.php" class="navbar-link" data-i18n-key="dashboard">Dashboard</a>
                    <a href="quiz.php" class="navbar-link" data-i18n-key="categories">Play Quiz</a>
                    <a href="leaderboard.php" class="navbar-link" data-i18n-key="leaderboard">Leaderboard</a>
                    <a href="logout.php" class="navbar-link" data-i18n-key="logout">Logout</a>
                <?php else: ?>
                    <a href="quiz.php" class="navbar-link" data-i18n-key="categories">Play Quiz</a>
                    <a href="leaderboard.php" class="navbar-link" data-i18n-key="leaderboard">Leaderboard</a>
                    <a href="login.php" class="navbar-link" data-i18n-key="login">Login</a>
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
    
    <!-- About Page -->
    <div class="about-page">
        <div class="about-container">
            <!-- Hero Section -->
            <div class="about-hero">
                <h1 class="about-title">About እንቆቅልሽ</h1>
                <p class="about-subtitle">Celebrating Ethiopian Heritage Through Modern Technology</p>
            </div>
            
            <!-- Mission Section -->
            <div class="mission-section">
                <h2 class="mission-title">Our Mission</h2>
                <p class="mission-text">
                    እናቆቅልሽ is more than just a quiz game – it's a celebration of Ethiopian culture, history, and innovation. 
                    We believe that learning should be engaging, competitive, and deeply connected to our roots. 
                    Our mission is to create world-class educational experiences that showcase Ethiopian excellence 
                    while making learning fun and accessible to everyone, everywhere.
                </p>
            </div>
            
            <!-- Features Section -->
            <div class="features-section">
                <h2 class="section-title">What Makes Us Special</h2>
                <div class="features-grid">
                    <div class="feature-card">
                        <div class="feature-icon">🏛️</div>
                        <h3 class="feature-title">Authentic Ethiopian Content</h3>
                        <p class="feature-description">
                            Carefully curated questions about Ethiopian history, culture, geography, and achievements 
                            created by Ethiopian educators and historians.
                        </p>
                    </div>
                    <div class="feature-card">
                        <div class="feature-icon">🎮</div>
                        <h3 class="feature-title">Cinematic Gaming Experience</h3>
                        <p class="feature-description">
                            AAA-quality visuals, smooth animations, and immersive gameplay that rivals 
                            international gaming platforms while maintaining Ethiopian cultural identity.
                        </p>
                    </div>
                    <div class="feature-card">
                        <div class="feature-icon">🌍</div>
                        <h3 class="feature-title">Global Competition</h3>
                        <p class="feature-description">
                            Compete with players worldwide, climb the global leaderboard, and represent 
                            Ethiopian excellence on the international stage.
                        </p>
                    </div>
                    <div class="feature-card">
                        <div class="feature-icon">📚</div>
                        <h3 class="feature-title">Educational Excellence</h3>
                        <p class="feature-description">
                            Learn while you play with carefully designed questions that teach important facts 
                            about Ethiopia and the world in an engaging, memorable way.
                        </p>
                    </div>
                    <div class="feature-card">
                        <div class="feature-icon">🔒</div>
                        <h3 class="feature-title">Secure & Private</h3>
                        <p class="feature-description">
                            Your data is protected with enterprise-grade security. We respect your privacy 
                            and never share your information with third parties.
                        </p>
                    </div>
                    <div class="feature-card">
                        <div class="feature-icon">📱</div>
                        <h3 class="feature-title">Accessible Everywhere</h3>
                        <p class="feature-description">
                            Play on any device – mobile, tablet, or desktop. Our responsive design ensures 
                            a perfect experience no matter how you access the game.
                        </p>
                    </div>
                </div>
            </div>
            
            <!-- Stats Section -->
            <div class="stats-section">
                <h2 class="stats-title">By the Numbers</h2>
                <div class="stats-grid">
                    <div class="stat-item">
                        <div class="stat-number">80+</div>
                        <div class="stat-label">Questions</div>
                    </div>
                    <div class="stat-item">
                        <div class="stat-number">8</div>
                        <div class="stat-label">Categories</div>
                    </div>
                    <div class="stat-item">
                        <div class="stat-number">100%</div>
                        <div class="stat-label">Ethiopian Made</div>
                    </div>
                    <div class="stat-item">
                        <div class="stat-number">24/7</div>
                        <div class="stat-label">Available</div>
                    </div>
                </div>
            </div>
            
            <!-- Team Section -->
            <div class="team-section">
                <h2 class="team-title">Meet Our Team</h2>
                <div class="team-grid">
                    <div class="team-member">
                        <div class="member-avatar">👨‍💻</div>
                        <h3 class="member-name">Ethiopian Developers</h3>
                        <p class="member-role">Full-Stack Development</p>
                        <p class="member-bio">
                            Passionate Ethiopian developers creating world-class software that celebrates our heritage.
                        </p>
                    </div>
                    <div class="team-member">
                        <div class="member-avatar">👨‍🎨</div>
                        <h3 class="member-name">Design Team</h3>
                        <p class="member-role">UI/UX Excellence</p>
                        <p class="member-bio">
                            Creative minds blending Ethiopian aesthetics with modern design principles.
                        </p>
                    </div>
                    <div class="team-member">
                        <div class="member-avatar">👩‍🏫</div>
                        <h3 class="member-name">Content Experts</h3>
                        <p class="member-role">Educational Content</p>
                        <p class="member-bio">
                            Ethiopian educators and historians ensuring accurate, engaging educational content.
                        </p>
                    </div>
                    <div class="team-member">
                        <div class="member-avatar">👨‍🔧</div>
                        <h3 class="member-name">Technical Support</h3>
                        <p class="member-role">Quality Assurance</p>
                        <p class="member-bio">
                            Dedicated team ensuring smooth performance and excellent user experience.
                        </p>
                    </div>
                </div>
            </div>
            
            <!-- Contact Section -->
            <div class="contact-section">
                <h2 class="contact-title">Get in Touch</h2>
                <div class="contact-grid">
                    <div class="contact-item">
                        <span class="contact-icon">📧</span>
                        <div class="contact-text">support@anakoklish.com</div>
                    </div>
                    <div class="contact-item">
                        <span class="contact-icon">🌐</span>
                        <div class="contact-text">www.anakoklish.com</div>
                    </div>
                    <div class="contact-item">
                        <span class="contact-icon">📍</span>
                        <div class="contact-text">Addis Ababa, Ethiopia</div>
                    </div>
                </div>
                
                <div class="social-links">
                    <a href="#" class="social-link">📘</a>
                    <a href="#" class="social-link">🐦</a>
                    <a href="#" class="social-link">📷</a>
                    <a href="#" class="social-link">💬</a>
                </div>
            </div>
        </div>
    </div>
    
    <!-- JavaScript -->
    <script src="assets/js/script.js"></script>
    
    <script>
        // Add entrance animations
        document.addEventListener('DOMContentLoaded', function() {
            const cards = document.querySelectorAll('.feature-card, .team-member, .stat-item');
            
            cards.forEach((card, index) => {
                card.style.opacity = '0';
                card.style.transform = 'translateY(30px)';
                
                setTimeout(() => {
                    card.style.transition = 'all 0.6s ease';
                    card.style.opacity = '1';
                    card.style.transform = 'translateY(0)';
                }, index * 100);
            });
        });
        
        // Add hover effects
        document.querySelectorAll('.feature-card, .team-member, .contact-item').forEach(card => {
            card.addEventListener('mouseenter', function() {
                window.ethiopianQuizApp.playSound('hover');
            });
        });
    </script>
</body>
</html>
