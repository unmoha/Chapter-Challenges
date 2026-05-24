<?php
session_start();
require_once 'config/db.php';

// Check if user is logged in
if (!isset($_SESSION['user_id'])) {
    header('Location: login.php');
    exit();
}

// Get user data
$user_id = $_SESSION['user_id'];
$user_query = "SELECT * FROM users WHERE id = ?";
$user_stmt = $db->prepare($user_query);
$user_stmt->execute([$user_id]);
$user = $user_stmt->fetch(PDO::FETCH_ASSOC);

// Get user statistics
$stats_query = "SELECT 
    COUNT(*) as games_played,
    SUM(score) as total_score,
    MAX(score) as best_score,
    AVG(accuracy) as average_accuracy,
    SUM(correct_answers) as total_correct,
    SUM(wrong_answers) as total_wrong
    FROM scores WHERE user_id = ?";
$stats_stmt = $db->prepare($stats_query);
$stats_stmt->execute([$user_id]);
$stats = $stats_stmt->fetch(PDO::FETCH_ASSOC);

// Get recent games
$recent_query = "SELECT s.*, c.name as category_name, c.icon as category_icon 
                 FROM scores s 
                 JOIN categories c ON s.category_id = c.id 
                 WHERE s.user_id = ? 
                 ORDER BY s.completed_at DESC 
                 LIMIT 5";
$recent_stmt = $db->prepare($recent_query);
$recent_stmt->execute([$user_id]);
$recent_games = $recent_stmt->fetchAll(PDO::FETCH_ASSOC);

// Get leaderboard position
$leaderboard_query = "SELECT COUNT(*) + 1 as position 
                     FROM leaderboard l1 
                     WHERE l1.total_score > (
                         SELECT COALESCE(SUM(score), 0) 
                         FROM scores 
                         WHERE user_id = ?
                     )";
$leaderboard_stmt = $db->prepare($leaderboard_query);
$leaderboard_stmt->execute([$user_id]);
$leaderboard_position = $leaderboard_stmt->fetch(PDO::FETCH_ASSOC)['position'];
?>
<!DOCTYPE html>
<html lang="am" dir="ltr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Dashboard - እንቆቅልሽ | Ethiopian Quiz Game</title>
    <meta name="description" content="Your personal dashboard for tracking quiz performance and achievements">
    
    <!-- Google Fonts -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Segoe+UI:wght@300;400;500;600;700&family=Georgia:wght@400;700&family=Noto+Sans+Ethiopic:wght@400;700;900&display=swap" rel="stylesheet">
    
    <!-- CSS -->
    <link rel="stylesheet" href="assets/css/style.css">
    
    <style>
        .dashboard-page {
            min-height: 100vh;
            padding-top: 80px;
            position: relative;
        }
        
        .dashboard-header {
            background: var(--glass-bg);
            backdrop-filter: blur(20px);
            border-bottom: 1px solid var(--glass-border);
            padding: var(--spacing-xl) 0;
            margin-bottom: var(--spacing-xl);
        }
        
        .header-content {
            max-width: 1200px;
            margin: 0 auto;
            padding: 0 var(--spacing-lg);
            display: flex;
            justify-content: space-between;
            align-items: center;
            flex-wrap: wrap;
            gap: var(--spacing-lg);
        }
        
        .welcome-section h1 {
            font-size: 2.5rem;
            color: var(--neon-gold);
            margin-bottom: var(--spacing-sm);
        }
        
        .welcome-section p {
            color: rgba(255, 255, 255, 0.7);
            font-size: 1.1rem;
        }
        
        .user-info {
            display: flex;
            align-items: center;
            gap: var(--spacing-md);
        }
        
        .user-avatar {
            width: 60px;
            height: 60px;
            background: linear-gradient(135deg, var(--matte-gold), var(--bronze));
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 1.5rem;
            font-weight: 700;
            color: var(--deep-black);
        }
        
        .user-details h3 {
            color: var(--neon-gold);
            margin-bottom: var(--spacing-xs);
        }
        
        .user-details p {
            color: rgba(255, 255, 255, 0.7);
            font-size: 0.9rem;
        }
        
        .dashboard-content {
            max-width: 1200px;
            margin: 0 auto;
            padding: 0 var(--spacing-lg);
        }
        
        .stats-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(250px, 1fr));
            gap: var(--spacing-lg);
            margin-bottom: var(--spacing-xl);
        }
        
        .stat-card {
            background: var(--glass-bg);
            backdrop-filter: blur(20px);
            border: 1px solid var(--glass-border);
            border-radius: var(--radius-lg);
            padding: var(--spacing-lg);
            text-align: center;
            transition: all var(--transition-smooth);
        }
        
        .stat-card:hover {
            transform: translateY(-5px);
            box-shadow: 
                var(--glass-shadow),
                0 0 30px rgba(255, 215, 0, 0.2);
            border-color: rgba(255, 215, 0, 0.3);
        }
        
        .stat-icon {
            font-size: 2.5rem;
            margin-bottom: var(--spacing-md);
            filter: drop-shadow(0 0 10px currentColor);
        }
        
        .stat-value {
            font-size: 2rem;
            font-weight: 700;
            color: var(--neon-gold);
            margin-bottom: var(--spacing-sm);
        }
        
        .stat-label {
            color: rgba(255, 255, 255, 0.7);
            font-size: 1rem;
        }
        
        .main-grid {
            display: grid;
            grid-template-columns: 2fr 1fr;
            gap: var(--spacing-xl);
            margin-bottom: var(--spacing-xl);
        }
        
        .recent-games {
            background: var(--glass-bg);
            backdrop-filter: blur(20px);
            border: 1px solid var(--glass-border);
            border-radius: var(--radius-lg);
            padding: var(--spacing-lg);
        }
        
        .section-title {
            font-size: 1.5rem;
            color: var(--neon-gold);
            margin-bottom: var(--spacing-lg);
            display: flex;
            align-items: center;
            gap: var(--spacing-sm);
        }
        
        .game-item {
            background: rgba(255, 255, 255, 0.05);
            border-radius: var(--radius-md);
            padding: var(--spacing-md);
            margin-bottom: var(--spacing-md);
            display: flex;
            justify-content: space-between;
            align-items: center;
            transition: all var(--transition-smooth);
        }
        
        .game-item:hover {
            background: rgba(255, 255, 255, 0.08);
            transform: translateX(5px);
        }
        
        .game-info {
            flex: 1;
        }
        
        .game-category {
            display: flex;
            align-items: center;
            gap: var(--spacing-sm);
            margin-bottom: var(--spacing-xs);
        }
        
        .game-category-icon {
            font-size: 1.2rem;
        }
        
        .game-category-name {
            font-weight: 600;
            color: var(--neon-gold);
        }
        
        .game-time {
            font-size: 0.8rem;
            color: rgba(255, 255, 255, 0.5);
        }
        
        .game-stats {
            text-align: right;
        }
        
        .game-score {
            font-size: 1.2rem;
            font-weight: 700;
            color: var(--neon-green);
            margin-bottom: var(--spacing-xs);
        }
        
        .game-accuracy {
            font-size: 0.9rem;
            color: rgba(255, 255, 255, 0.7);
        }
        
        .achievements {
            background: var(--glass-bg);
            backdrop-filter: blur(20px);
            border: 1px solid var(--glass-border);
            border-radius: var(--radius-lg);
            padding: var(--spacing-lg);
        }
        
        .achievement-list {
            display: flex;
            flex-direction: column;
            gap: var(--spacing-md);
        }
        
        .achievement-item {
            display: flex;
            align-items: center;
            gap: var(--spacing-md);
            padding: var(--spacing-md);
            background: rgba(255, 255, 255, 0.05);
            border-radius: var(--radius-md);
            transition: all var(--transition-smooth);
        }
        
        .achievement-item:hover {
            background: rgba(255, 255, 255, 0.08);
            transform: translateX(5px);
        }
        
        .achievement-icon {
            font-size: 2rem;
            width: 50px;
            height: 50px;
            display: flex;
            align-items: center;
            justify-content: center;
            background: linear-gradient(135deg, var(--matte-gold), var(--bronze));
            border-radius: 50%;
        }
        
        .achievement-info h4 {
            color: var(--neon-gold);
            margin-bottom: var(--spacing-xs);
        }
        
        .achievement-info p {
            font-size: 0.9rem;
            color: rgba(255, 255, 255, 0.7);
        }
        
        .quick-actions {
            background: var(--glass-bg);
            backdrop-filter: blur(20px);
            border: 1px solid var(--glass-border);
            border-radius: var(--radius-lg);
            padding: var(--spacing-lg);
            margin-bottom: var(--spacing-xl);
        }
        
        .actions-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(200px, 1fr));
            gap: var(--spacing-md);
        }
        
        .action-btn {
            padding: var(--spacing-lg);
            background: rgba(255, 255, 255, 0.05);
            border: 1px solid var(--glass-border);
            border-radius: var(--radius-md);
            color: white;
            text-decoration: none;
            display: flex;
            flex-direction: column;
            align-items: center;
            gap: var(--spacing-sm);
            transition: all var(--transition-smooth);
        }
        
        .action-btn:hover {
            background: rgba(255, 255, 255, 0.1);
            border-color: var(--neon-gold);
            transform: translateY(-3px);
        }
        
        .action-icon {
            font-size: 2rem;
        }
        
        .action-label {
            font-weight: 600;
            text-align: center;
        }
        
        .no-data {
            text-align: center;
            padding: var(--spacing-xl);
            color: rgba(255, 255, 255, 0.5);
        }
        
        .no-data-icon {
            font-size: 3rem;
            margin-bottom: var(--spacing-md);
            opacity: 0.5;
        }
        
        @media (max-width: 768px) {
            .dashboard-header {
                padding: var(--spacing-lg) 0;
            }
            
            .header-content {
                flex-direction: column;
                text-align: center;
            }
            
            .welcome-section h1 {
                font-size: 2rem;
            }
            
            .main-grid {
                grid-template-columns: 1fr;
            }
            
            .stats-grid {
                grid-template-columns: repeat(auto-fit, minmax(200px, 1fr));
            }
            
            .actions-grid {
                grid-template-columns: 1fr;
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
    
    <!-- Dashboard Page -->
    <div class="dashboard-page">
        <!-- Header Section -->
        <div class="dashboard-header">
            <div class="header-content">
                <div class="welcome-section">
                    <h1>Welcome back, <?php echo htmlspecialchars($user['username']); ?>!</h1>
                    <p>Ready to test your knowledge and climb the leaderboard?</p>
                </div>
                <div class="user-info">
                    <div class="user-avatar">
                        <?php echo strtoupper(substr($user['username'], 0, 2)); ?>
                    </div>
                    <div class="user-details">
                        <h3><?php echo htmlspecialchars($user['full_name'] ?: $user['username']); ?></h3>
                        <p>Rank #<?php echo number_format($leaderboard_position); ?> • Member since <?php echo date('M Y', strtotime($user['created_at'])); ?></p>
                    </div>
                </div>
            </div>
        </div>
        
        <!-- Main Content -->
        <div class="dashboard-content">
            <!-- Statistics Grid -->
            <div class="stats-grid">
                <div class="stat-card">
                    <div class="stat-icon">🎮</div>
                    <div class="stat-value"><?php echo number_format($stats['games_played']); ?></div>
                    <div class="stat-label">Games Played</div>
                </div>
                <div class="stat-card">
                    <div class="stat-icon">⭐</div>
                    <div class="stat-value"><?php echo number_format($stats['total_score'] ?: 0); ?></div>
                    <div class="stat-label">Total Score</div>
                </div>
                <div class="stat-card">
                    <div class="stat-icon">🏆</div>
                    <div class="stat-value"><?php echo number_format($stats['best_score'] ?: 0); ?></div>
                    <div class="stat-label">Best Score</div>
                </div>
                <div class="stat-card">
                    <div class="stat-icon">🎯</div>
                    <div class="stat-value"><?php echo round($stats['average_accuracy'] ?: 0, 1); ?>%</div>
                    <div class="stat-label">Average Accuracy</div>
                </div>
            </div>
            
            <!-- Quick Actions -->
            <div class="quick-actions">
                <h2 class="section-title">
                    <span>⚡</span>
                    <span>Quick Actions</span>
                </h2>
                <div class="actions-grid">
                    <a href="quiz.php" class="action-btn">
                        <span class="action-icon">🎮</span>
                        <span class="action-label">Play Quiz</span>
                    </a>
                    <a href="leaderboard.php" class="action-btn">
                        <span class="action-icon">🏆</span>
                        <span class="action-label">View Leaderboard</span>
                    </a>
                    <a href="#" class="action-btn" onclick="showProfileModal(); return false;">
                        <span class="action-icon">👤</span>
                        <span class="action-label">Edit Profile</span>
                    </a>
                    <a href="#" class="action-btn" onclick="showStatsModal(); return false;">
                        <span class="action-icon">📊</span>
                        <span class="action-label">View Statistics</span>
                    </a>
                </div>
            </div>
            
            <!-- Main Grid -->
            <div class="main-grid">
                <!-- Recent Games -->
                <div class="recent-games">
                    <h2 class="section-title">
                        <span>🕐</span>
                        <span>Recent Games</span>
                    </h2>
                    
                    <?php if (count($recent_games) > 0): ?>
                        <div class="recent-games-list">
                            <?php foreach ($recent_games as $game): ?>
                                <div class="game-item">
                                    <div class="game-info">
                                        <div class="game-category">
                                            <span class="game-category-icon"><?php echo htmlspecialchars($game['category_icon']); ?></span>
                                            <span class="game-category-name"><?php echo htmlspecialchars($game['category_name']); ?></span>
                                        </div>
                                        <div class="game-time"><?php echo date('M j, Y g:i A', strtotime($game['completed_at'])); ?></div>
                                    </div>
                                    <div class="game-stats">
                                        <div class="game-score"><?php echo number_format($game['score']); ?></div>
                                        <div class="game-accuracy"><?php echo round($game['accuracy'], 1); ?>% accuracy</div>
                                    </div>
                                </div>
                            <?php endforeach; ?>
                        </div>
                    <?php else: ?>
                        <div class="no-data">
                            <div class="no-data-icon">🎮</div>
                            <p>No games played yet. Start your first quiz to see your progress!</p>
                            <a href="quiz.php" class="btn btn-primary" style="margin-top: var(--spacing-md);">Play Your First Quiz</a>
                        </div>
                    <?php endif; ?>
                </div>
                
                <!-- Achievements -->
                <div class="achievements">
                    <h2 class="section-title">
                        <span>🏅</span>
                        <span>Achievements</span>
                    </h2>
                    
                    <div class="achievement-list">
                        <?php
                        // Calculate achievements based on stats
                        $achievements = [];
                        
                        if ($stats['games_played'] >= 1) {
                            $achievements[] = ['icon' => '🎯', 'title' => 'First Steps', 'desc' => 'Played your first game'];
                        }
                        if ($stats['games_played'] >= 10) {
                            $achievements[] = ['icon' => '⭐', 'title' => 'Dedicated Player', 'desc' => 'Played 10 games'];
                        }
                        if (($stats['average_accuracy'] ?: 0) >= 80) {
                            $achievements[] = ['icon' => '🎯', 'title' => 'Sharpshooter', 'desc' => '80%+ average accuracy'];
                        }
                        if (($stats['best_score'] ?: 0) >= 500) {
                            $achievements[] = ['icon' => '🏆', 'title' => 'High Scorer', 'desc' => 'Scored 500+ points'];
                        }
                        
                        if (empty($achievements)) {
                            echo '<div class="no-data">
                                <div class="no-data-icon">🏅</div>
                                <p>Play more games to unlock achievements!</p>
                            </div>';
                        } else {
                            foreach ($achievements as $achievement) {
                                echo '<div class="achievement-item">
                                    <div class="achievement-icon">' . $achievement['icon'] . '</div>
                                    <div class="achievement-info">
                                        <h4>' . $achievement['title'] . '</h4>
                                        <p>' . $achievement['desc'] . '</p>
                                    </div>
                                </div>';
                            }
                        }
                        ?>
                    </div>
                </div>
            </div>
        </div>
    </div>
    
    <!-- JavaScript -->
    <script src="assets/js/script.js"></script>
    
    <script>
        // Modal functions
        function showProfileModal() {
            window.ethiopianQuizApp.showNotification('Profile editing coming soon!', 'info');
        }
        
        function showStatsModal() {
            window.ethiopianQuizApp.showNotification('Detailed statistics coming soon!', 'info');
        }
        
        // Animate statistics on page load
        document.addEventListener('DOMContentLoaded', function() {
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
        });
        
        // Add hover effects to game items
        document.querySelectorAll('.game-item').forEach(item => {
            item.addEventListener('mouseenter', function() {
                window.ethiopianQuizApp.playSound('hover');
            });
        });
    </script>
</body>
</html>
