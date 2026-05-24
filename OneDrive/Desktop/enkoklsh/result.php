<?php
session_start();
require_once 'config/db.php';

// Check if user is logged in
if (!isset($_SESSION['user_id'])) {
    header('Location: login.php');
    exit();
}

// Get result data from URL parameters
$score = isset($_GET['score']) ? (int)$_GET['score'] : 0;
$accuracy = isset($_GET['accuracy']) ? (float)$_GET['accuracy'] : 0;
$correct = isset($_GET['correct']) ? (int)$_GET['correct'] : 0;
$wrong = isset($_GET['wrong']) ? (int)$_GET['wrong'] : 0;
$category = isset($_GET['category']) ? htmlspecialchars($_GET['category']) : 'Unknown';
$total = $correct + $wrong;

// Calculate achievement
$achievement = '';
$achievement_icon = '';
$achievement_color = '';

if ($accuracy >= 90) {
    $achievement = 'Quiz Master!';
    $achievement_icon = '🏆';
    $achievement_color = '#FFD700';
} elseif ($accuracy >= 80) {
    $achievement = 'Expert!';
    $achievement_icon = '🥇';
    $achievement_color = '#C0C0C0';
} elseif ($accuracy >= 70) {
    $achievement = 'Great Job!';
    $achievement_icon = '🥈';
    $achievement_color = '#CD7F32';
} elseif ($accuracy >= 60) {
    $achievement = 'Good Effort!';
    $achievement_icon = '🥉';
    $achievement_color = '#CD7F32';
} else {
    $achievement = 'Keep Practicing!';
    $achievement_icon = '📚';
    $achievement_color = '#00d4ff';
}
?>
<!DOCTYPE html>
<html lang="am" dir="ltr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Results - እንቆቅልሽ | Ethiopian Quiz Game</title>
    <meta name="description" content="View your quiz results and performance analytics">
    
    <!-- Google Fonts -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Segoe+UI:wght@300;400;500;600;700&family=Georgia:wght@400;700&family=Noto+Sans+Ethiopic:wght@400;700;900&display=swap" rel="stylesheet">
    
    <!-- CSS -->
    <link rel="stylesheet" href="assets/css/style.css">
    
    <style>
        .result-page {
            min-height: 100vh;
            padding-top: 80px;
            position: relative;
            display: flex;
            align-items: center;
            justify-content: center;
        }
        
        .result-container {
            max-width: 900px;
            width: 100%;
            margin: 0 auto;
            padding: var(--spacing-lg);
        }
        
        .result-card {
            background: var(--glass-bg);
            backdrop-filter: blur(20px);
            border: 1px solid var(--glass-border);
            border-radius: var(--radius-lg);
            padding: var(--spacing-xl);
            text-align: center;
            position: relative;
            overflow: hidden;
        }
        
        .result-card::before {
            content: '';
            position: absolute;
            top: -50%;
            left: -50%;
            width: 200%;
            height: 200%;
            background: radial-gradient(circle, rgba(255, 215, 0, 0.1) 0%, transparent 70%);
            animation: rotate 20s linear infinite;
        }
        
        @keyframes rotate {
            from { transform: rotate(0deg); }
            to { transform: rotate(360deg); }
        }
        
        .result-header {
            position: relative;
            z-index: 2;
            margin-bottom: var(--spacing-xl);
        }
        
        .result-title {
            font-size: 2.5rem;
            color: var(--neon-gold);
            margin-bottom: var(--spacing-md);
            font-weight: 700;
        }
        
        .category-name {
            font-size: 1.2rem;
            color: rgba(255, 255, 255, 0.7);
            margin-bottom: var(--spacing-lg);
        }
        
        .achievement-badge {
            display: inline-flex;
            flex-direction: column;
            align-items: center;
            margin-bottom: var(--spacing-xl);
            animation: bounce-in 0.8s ease;
        }
        
        @keyframes bounce-in {
            0% { transform: scale(0); opacity: 0; }
            50% { transform: scale(1.1); }
            100% { transform: scale(1); opacity: 1; }
        }
        
        .achievement-icon {
            font-size: 5rem;
            margin-bottom: var(--spacing-md);
            filter: drop-shadow(0 0 20px currentColor);
            animation: pulse 2s ease-in-out infinite;
        }
        
        @keyframes pulse {
            0%, 100% { transform: scale(1); }
            50% { transform: scale(1.05); }
        }
        
        .achievement-text {
            font-size: 1.8rem;
            font-weight: 700;
            color: <?php echo $achievement_color; ?>;
            text-shadow: 0 0 10px currentColor;
        }
        
        .stats-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(200px, 1fr));
            gap: var(--spacing-lg);
            margin-bottom: var(--spacing-xl);
            position: relative;
            z-index: 2;
        }
        
        .stat-card {
            background: rgba(255, 255, 255, 0.05);
            border: 1px solid rgba(255, 255, 255, 0.1);
            border-radius: var(--radius-md);
            padding: var(--spacing-lg);
            transition: all var(--transition-smooth);
        }
        
        .stat-card:hover {
            transform: translateY(-5px);
            background: rgba(255, 255, 255, 0.08);
            border-color: rgba(255, 215, 0, 0.3);
        }
        
        .stat-label {
            color: rgba(255, 255, 255, 0.7);
            font-size: 0.9rem;
            margin-bottom: var(--spacing-sm);
        }
        
        .stat-value {
            font-size: 2rem;
            font-weight: 700;
            color: var(--neon-gold);
        }
        
        .stat-value.score { color: var(--neon-green); }
        .stat-value.accuracy { color: var(--neon-blue); }
        .stat-value.correct { color: #00ff88; }
        .stat-value.wrong { color: #ff6b6b; }
        
        .progress-ring {
            width: 200px;
            height: 200px;
            margin: 0 auto var(--spacing-xl);
            position: relative;
        }
        
        .progress-ring svg {
            transform: rotate(-90deg);
        }
        
        .progress-ring-circle {
            fill: none;
            stroke-width: 10;
        }
        
        .progress-ring-bg {
            stroke: rgba(255, 255, 255, 0.1);
        }
        
        .progress-ring-fill {
            stroke: var(--neon-gold);
            stroke-linecap: round;
            transition: stroke-dashoffset 1s ease;
            filter: drop-shadow(0 0 10px currentColor);
        }
        
        .progress-ring-text {
            position: absolute;
            top: 50%;
            left: 50%;
            transform: translate(-50%, -50%);
            font-size: 2.5rem;
            font-weight: 700;
            color: var(--neon-gold);
        }
        
        .action-buttons {
            display: flex;
            gap: var(--spacing-md);
            justify-content: center;
            flex-wrap: wrap;
            position: relative;
            z-index: 2;
        }
        
        .confetti {
            position: fixed;
            width: 10px;
            height: 10px;
            background: var(--neon-gold);
            position: absolute;
            animation: confetti-fall 3s linear;
        }
        
        @keyframes confetti-fall {
            0% {
                transform: translateY(-100vh) rotate(0deg);
                opacity: 1;
            }
            100% {
                transform: translateY(100vh) rotate(720deg);
                opacity: 0;
            }
        }
        
        .performance-chart {
            background: rgba(255, 255, 255, 0.05);
            border: 1px solid rgba(255, 255, 255, 0.1);
            border-radius: var(--radius-md);
            padding: var(--spacing-lg);
            margin-bottom: var(--spacing-xl);
            position: relative;
            z-index: 2;
        }
        
        .chart-title {
            font-size: 1.3rem;
            color: var(--neon-gold);
            margin-bottom: var(--spacing-md);
            text-align: center;
        }
        
        .bar-chart {
            display: flex;
            align-items: flex-end;
            justify-content: space-around;
            height: 150px;
            gap: var(--spacing-md);
        }
        
        .bar {
            flex: 1;
            background: linear-gradient(to top, var(--neon-gold), var(--matte-gold));
            border-radius: var(--radius-sm) var(--radius-sm) 0 0;
            position: relative;
            transition: all var(--transition-smooth);
            min-height: 20px;
        }
        
        .bar:hover {
            transform: scaleY(1.05);
            filter: brightness(1.2);
        }
        
        .bar-label {
            position: absolute;
            bottom: -25px;
            left: 50%;
            transform: translateX(-50%);
            font-size: 0.8rem;
            color: rgba(255, 255, 255, 0.7);
            white-space: nowrap;
        }
        
        .bar-value {
            position: absolute;
            top: -25px;
            left: 50%;
            transform: translateX(-50%);
            font-weight: 600;
            color: var(--neon-gold);
        }
        
        @media (max-width: 768px) {
            .result-title {
                font-size: 2rem;
            }
            
            .achievement-icon {
                font-size: 4rem;
            }
            
            .achievement-text {
                font-size: 1.5rem;
            }
            
            .stats-grid {
                grid-template-columns: repeat(2, 1fr);
            }
            
            .progress-ring {
                width: 150px;
                height: 150px;
            }
            
            .progress-ring-text {
                font-size: 2rem;
            }
            
            .action-buttons {
                flex-direction: column;
                align-items: center;
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
                <a href="dashboard.php" class="navbar-link" data-i18n-key="dashboard">Dashboard</a>
                <a href="quiz.php" class="navbar-link" data-i18n-key="categories">Play Quiz</a>
                <a href="leaderboard.php" class="navbar-link" data-i18n-key="leaderboard">Leaderboard</a>
                <a href="logout.php" class="navbar-link" data-i18n-key="logout">Logout</a>
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
    
    <!-- Result Page -->
    <div class="result-page">
        <div class="result-container">
            <div class="result-card">
                <div class="result-header">
                    <h1 class="result-title">Quiz Completed!</h1>
                    <p class="category-name">Category: <?php echo $category; ?></p>
                </div>
                
                <div class="achievement-badge">
                    <div class="achievement-icon"><?php echo $achievement_icon; ?></div>
                    <div class="achievement-text"><?php echo $achievement; ?></div>
                </div>
                
                <!-- Progress Ring -->
                <div class="progress-ring">
                    <svg width="200" height="200">
                        <circle class="progress-ring-circle progress-ring-bg" cx="100" cy="100" r="90"></circle>
                        <circle class="progress-ring-circle progress-ring-fill" cx="100" cy="100" r="90"
                                stroke-dasharray="<?php echo 2 * M_PI * 90; ?>"
                                stroke-dashoffset="<?php echo 2 * M_PI * 90 * (1 - $accuracy / 100); ?>">
                        </circle>
                    </svg>
                    <div class="progress-ring-text"><?php echo round($accuracy); ?>%</div>
                </div>
                
                <!-- Stats Grid -->
                <div class="stats-grid">
                    <div class="stat-card">
                        <div class="stat-label">Final Score</div>
                        <div class="stat-value score"><?php echo number_format($score); ?></div>
                    </div>
                    <div class="stat-card">
                        <div class="stat-label">Accuracy</div>
                        <div class="stat-value accuracy"><?php echo round($accuracy); ?>%</div>
                    </div>
                    <div class="stat-card">
                        <div class="stat-label">Correct</div>
                        <div class="stat-value correct"><?php echo $correct; ?></div>
                    </div>
                    <div class="stat-card">
                        <div class="stat-label">Wrong</div>
                        <div class="stat-value wrong"><?php echo $wrong; ?></div>
                    </div>
                </div>
                
                <!-- Performance Chart -->
                <div class="performance-chart">
                    <h3 class="chart-title">Performance Breakdown</h3>
                    <div class="bar-chart">
                        <div class="bar" style="height: <?php echo ($correct / max($total, 1)) * 100; ?>%;">
                            <div class="bar-value"><?php echo $correct; ?></div>
                            <div class="bar-label">Correct</div>
                        </div>
                        <div class="bar" style="height: <?php echo ($wrong / max($total, 1)) * 100; ?>%; background: linear-gradient(to top, #ff6b6b, #ff006e);">
                            <div class="bar-value"><?php echo $wrong; ?></div>
                            <div class="bar-label">Wrong</div>
                        </div>
                        <div class="bar" style="height: <?php echo ($accuracy / 100) * 100; ?>%; background: linear-gradient(to top, #00d4ff, #0099cc);">
                            <div class="bar-value"><?php echo round($accuracy); ?>%</div>
                            <div class="bar-label">Accuracy</div>
                        </div>
                    </div>
                </div>
                
                <!-- Action Buttons -->
                <div class="action-buttons">
                    <a href="quiz.php" class="btn btn-primary">
                        <span>Play Again</span>
                    </a>
                    <a href="dashboard.php" class="btn btn-secondary">
                        <span>View Dashboard</span>
                    </a>
                    <a href="leaderboard.php" class="btn btn-ghost">
                        <span>Leaderboard</span>
                    </a>
                </div>
            </div>
        </div>
    </div>
    
    <!-- JavaScript -->
    <script src="assets/js/script.js"></script>
    
    <script>
        // Create confetti effect for high scores
        function createConfetti() {
            const accuracy = <?php echo $accuracy; ?>;
            
            if (accuracy >= 70) {
                const colors = ['#FFD700', '#00d4ff', '#9d4edd', '#00ff88', '#ff6b6b'];
                
                for (let i = 0; i < 50; i++) {
                    setTimeout(() => {
                        const confetti = document.createElement('div');
                        confetti.className = 'confetti';
                        confetti.style.left = Math.random() * 100 + '%';
                        confetti.style.background = colors[Math.floor(Math.random() * colors.length)];
                        confetti.style.animationDelay = Math.random() * 0.5 + 's';
                        confetti.style.animationDuration = (2 + Math.random() * 2) + 's';
                        document.body.appendChild(confetti);
                        
                        setTimeout(() => confetti.remove(), 4000);
                    }, i * 50);
                }
                
                // Play celebration sound
                window.ethiopianQuizApp.playSound('win');
            } else {
                window.ethiopianQuizApp.playSound('lose');
            }
        }
        
        // Animate progress ring on load
        function animateProgressRing() {
            const progressRing = document.querySelector('.progress-ring-fill');
            const accuracy = <?php echo $accuracy; ?>;
            const circumference = 2 * Math.PI * 90;
            const offset = circumference * (1 - accuracy / 100);
            
            setTimeout(() => {
                progressRing.style.strokeDashoffset = offset;
            }, 500);
        }
        
        // Animate stats on load
        function animateStats() {
            const statValues = document.querySelectorAll('.stat-value');
            
            statValues.forEach(stat => {
                const finalValue = stat.textContent;
                const isPercentage = finalValue.includes('%');
                const numericValue = parseFloat(finalValue.replace(/[^0-9.]/g, ''));
                
                if (!isNaN(numericValue)) {
                    let currentValue = 0;
                    const increment = numericValue / 50;
                    
                    const updateValue = () => {
                        currentValue += increment;
                        if (currentValue < numericValue) {
                            stat.textContent = Math.floor(currentValue) + (isPercentage ? '%' : '');
                            requestAnimationFrame(updateValue);
                        } else {
                            stat.textContent = finalValue;
                        }
                    };
                    
                    updateValue();
                }
            });
        }
        
        // Animate bars on load
        function animateBars() {
            const bars = document.querySelectorAll('.bar');
            
            bars.forEach((bar, index) => {
                const finalHeight = bar.style.height;
                bar.style.height = '0';
                
                setTimeout(() => {
                    bar.style.height = finalHeight;
                }, 1000 + index * 200);
            });
        }
        
        // Initialize animations
        document.addEventListener('DOMContentLoaded', () => {
            createConfetti();
            animateProgressRing();
            animateStats();
            animateBars();
        });
        
        // Add hover effects
        document.querySelectorAll('.stat-card').forEach(card => {
            card.addEventListener('mouseenter', function() {
                window.ethiopianQuizApp.playSound('hover');
            });
        });
    </script>
</body>
</html>
