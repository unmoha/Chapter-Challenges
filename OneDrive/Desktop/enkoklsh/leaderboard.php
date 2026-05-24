<?php
session_start();
require_once 'config/db.php';

// Get leaderboard data
$leaderboard_query = "SELECT 
                        u.id,
                        u.username,
                        u.full_name,
                        u.profile_image,
                        COALESCE(SUM(s.score), 0) as total_score,
                        COUNT(s.id) as games_played,
                        COALESCE(MAX(s.score), 0) as best_score,
                        COALESCE(AVG(s.accuracy), 0) as average_accuracy,
                        RANK() OVER (ORDER BY COALESCE(SUM(s.score), 0) DESC) as rank_position
                        FROM users u
                        LEFT JOIN scores s ON u.id = s.user_id
                        GROUP BY u.id, u.username, u.full_name, u.profile_image
                        ORDER BY total_score DESC
                        LIMIT 50";

$leaderboard_stmt = $db->prepare($leaderboard_query);
$leaderboard_stmt->execute();
$leaderboard = $leaderboard_stmt->fetchAll(PDO::FETCH_ASSOC);

// Get current user's rank if logged in
$user_rank = null;
if (isset($_SESSION['user_id'])) {
    $user_rank_query = "SELECT 
                        RANK() OVER (ORDER BY COALESCE(SUM(score), 0) DESC) as rank_position
                        FROM scores 
                        WHERE user_id = ?
                        GROUP BY user_id";
    
    $user_rank_stmt = $db->prepare($user_rank_query);
    $user_rank_stmt->execute([$_SESSION['user_id']]);
    $user_rank_result = $user_rank_stmt->fetch(PDO::FETCH_ASSOC);
    
    if ($user_rank_result) {
        $user_rank = $user_rank_result['rank_position'];
    }
}
?>
<!DOCTYPE html>
<html lang="am" dir="ltr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Leaderboard - እንቆቅልሽ | Ethiopian Quiz Game</title>
    <meta name="description" content="View the top players and global rankings in the Ethiopian quiz game">
    
    <!-- Google Fonts -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Segoe+UI:wght@300;400;500;600;700&family=Georgia:wght@400;700&family=Noto+Sans+Ethiopic:wght@400;700;900&display=swap" rel="stylesheet">
    
    <!-- CSS -->
    <link rel="stylesheet" href="assets/css/style.css">
    
    <style>
        .leaderboard-page {
            min-height: 100vh;
            padding-top: 80px;
            position: relative;
        }
        
        .leaderboard-container {
            max-width: 1200px;
            margin: 0 auto;
            padding: var(--spacing-lg);
        }
        
        .leaderboard-header {
            text-align: center;
            margin-bottom: var(--spacing-xl);
        }
        
        .leaderboard-title {
            font-size: 3rem;
            color: var(--neon-gold);
            margin-bottom: var(--spacing-md);
            font-weight: 700;
        }
        
        .leaderboard-subtitle {
            color: rgba(255, 255, 255, 0.7);
            font-size: 1.2rem;
            margin-bottom: var(--spacing-lg);
        }
        
        .user-rank-banner {
            background: linear-gradient(135deg, var(--matte-gold), var(--bronze));
            color: var(--deep-black);
            padding: var(--spacing-lg);
            border-radius: var(--radius-lg);
            margin-bottom: var(--spacing-xl);
            text-align: center;
            font-weight: 600;
        }
        
        .leaderboard-table {
            background: var(--glass-bg);
            backdrop-filter: blur(20px);
            border: 1px solid var(--glass-border);
            border-radius: var(--radius-lg);
            overflow: hidden;
            box-shadow: var(--glass-shadow);
        }
        
        .leaderboard-header-row {
            background: linear-gradient(135deg, var(--matte-gold), var(--bronze));
            color: var(--deep-black);
            padding: var(--spacing-lg);
            display: grid;
            grid-template-columns: 80px 60px 1fr 150px 120px 120px 120px;
            font-weight: 700;
            text-align: center;
            align-items: center;
        }
        
        .leaderboard-body {
            max-height: 600px;
            overflow-y: auto;
        }
        
        .leaderboard-row {
            padding: var(--spacing-lg);
            display: grid;
            grid-template-columns: 80px 60px 1fr 150px 120px 120px 120px;
            text-align: center;
            align-items: center;
            border-bottom: 1px solid rgba(255, 255, 255, 0.1);
            transition: all var(--transition-smooth);
        }
        
        .leaderboard-row:hover {
            background: rgba(255, 255, 255, 0.05);
        }
        
        .leaderboard-row.current-user {
            background: rgba(255, 215, 0, 0.1);
            border-color: rgba(255, 215, 0, 0.3);
        }
        
        .rank-badge {
            display: inline-flex;
            align-items: center;
            justify-content: center;
            width: 50px;
            height: 50px;
            border-radius: 50%;
            font-weight: 700;
            font-size: 1.1rem;
            margin: 0 auto;
        }
        
        .rank-1 { 
            background: linear-gradient(135deg, #FFD700, #FFA500); 
            color: #000;
            box-shadow: 0 0 20px rgba(255, 215, 0, 0.5);
        }
        
        .rank-2 { 
            background: linear-gradient(135deg, #C0C0C0, #808080); 
            color: #000;
            box-shadow: 0 0 15px rgba(192, 192, 192, 0.5);
        }
        
        .rank-3 { 
            background: linear-gradient(135deg, #CD7F32, #8B4513); 
            color: #fff;
            box-shadow: 0 0 15px rgba(205, 127, 50, 0.5);
        }
        
        .rank-default { 
            background: rgba(255, 255, 255, 0.1); 
            color: #fff;
        }
        
        .player-avatar {
            width: 40px;
            height: 40px;
            background: linear-gradient(135deg, var(--matte-gold), var(--bronze));
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            font-weight: 700;
            color: var(--deep-black);
            font-size: 0.9rem;
            margin: 0 auto;
        }
        
        .player-info {
            text-align: left;
        }
        
        .player-name {
            font-weight: 600;
            color: var(--neon-gold);
            font-size: 1.1rem;
            margin-bottom: var(--spacing-xs);
        }
        
        .player-fullname {
            color: rgba(255, 255, 255, 0.6);
            font-size: 0.9rem;
        }
        
        .stat-value {
            font-weight: 600;
            color: var(--neon-green);
            font-size: 1.1rem;
        }
        
        .accuracy-value {
            font-weight: 600;
            color: var(--neon-blue);
            font-size: 1.1rem;
        }
        
        .top-players {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(250px, 1fr));
            gap: var(--spacing-lg);
            margin-bottom: var(--spacing-xl);
        }
        
        .top-player-card {
            background: var(--glass-bg);
            backdrop-filter: blur(20px);
            border: 1px solid var(--glass-border);
            border-radius: var(--radius-lg);
            padding: var(--spacing-lg);
            text-align: center;
            transition: all var(--transition-smooth);
        }
        
        .top-player-card:hover {
            transform: translateY(-5px);
            box-shadow: 
                var(--glass-shadow),
                0 0 30px rgba(255, 215, 0, 0.3);
            border-color: rgba(255, 215, 0, 0.5);
        }
        
        .top-player-rank {
            font-size: 2rem;
            font-weight: 700;
            color: var(--neon-gold);
            margin-bottom: var(--spacing-sm);
        }
        
        .top-player-avatar {
            width: 80px;
            height: 80px;
            background: linear-gradient(135deg, var(--matte-gold), var(--bronze));
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            font-weight: 700;
            color: var(--deep-black);
            font-size: 1.5rem;
            margin: 0 auto var(--spacing-md);
        }
        
        .top-player-name {
            font-size: 1.2rem;
            font-weight: 600;
            color: var(--neon-gold);
            margin-bottom: var(--spacing-sm);
        }
        
        .top-player-score {
            font-size: 1.5rem;
            font-weight: 700;
            color: var(--neon-green);
            margin-bottom: var(--spacing-xs);
        }
        
        .top-player-stats {
            color: rgba(255, 255, 255, 0.7);
            font-size: 0.9rem;
        }
        
        .medal-icon {
            font-size: 2rem;
            margin-bottom: var(--spacing-sm);
        }
        
        .gold-medal { color: #FFD700; text-shadow: 0 0 10px #FFD700; }
        .silver-medal { color: #C0C0C0; text-shadow: 0 0 10px #C0C0C0; }
        .bronze-medal { color: #CD7F32; text-shadow: 0 0 10px #CD7F32; }
        
        @media (max-width: 768px) {
            .leaderboard-header-row,
            .leaderboard-row {
                grid-template-columns: 60px 1fr 80px 80px;
                font-size: 0.9rem;
            }
            
            .player-avatar {
                display: none;
            }
            
            .player-fullname {
                display: none;
            }
            
            .top-players {
                grid-template-columns: 1fr;
            }
            
            .leaderboard-title {
                font-size: 2rem;
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
                <?php if (isset($_SESSION['user_id'])): ?>
                    <a href="dashboard.php" class="navbar-link" data-i18n-key="dashboard">Dashboard</a>
                    <a href="quiz.php" class="navbar-link" data-i18n-key="categories">Play Quiz</a>
                <?php endif; ?>
                <a href="leaderboard.php" class="navbar-link" data-i18n-key="leaderboard">Leaderboard</a>
                <?php if (isset($_SESSION['user_id'])): ?>
                    <a href="logout.php" class="navbar-link" data-i18n-key="logout">Logout</a>
                <?php else: ?>
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
    
    <!-- Leaderboard Page -->
    <div class="leaderboard-page">
        <div class="leaderboard-container">
            <!-- Header -->
            <div class="leaderboard-header">
                <h1 class="leaderboard-title">🏆 Global Leaderboard</h1>
                <p class="leaderboard-subtitle">Compete with the best players worldwide</p>
                
                <?php if ($user_rank): ?>
                    <div class="user-rank-banner">
                        🎯 Your Current Rank: #<?php echo number_format($user_rank); ?>
                    </div>
                <?php endif; ?>
            </div>
            
            <!-- Top 3 Players -->
            <?php if (count($leaderboard) >= 3): ?>
            <div class="top-players">
                <?php for ($i = 0; $i < 3; $i++): ?>
                    <?php if (isset($leaderboard[$i])): ?>
                        <?php $player = $leaderboard[$i]; ?>
                        <div class="top-player-card">
                            <div class="medal-icon <?php echo ['gold-medal', 'silver-medal', 'bronze-medal'][$i]; ?>">
                                <?php echo ['🥇', '🥈', '🥉'][$i]; ?>
                            </div>
                            <div class="top-player-rank">#<?php echo $i + 1; ?></div>
                            <div class="top-player-avatar">
                                <?php echo strtoupper(substr($player['username'], 0, 2)); ?>
                            </div>
                            <div class="top-player-name"><?php echo htmlspecialchars($player['username']); ?></div>
                            <div class="top-player-score"><?php echo number_format($player['total_score']); ?></div>
                            <div class="top-player-stats">
                                <?php echo $player['games_played']; ?> games • 
                                <?php echo round($player['average_accuracy'], 1); ?>% accuracy
                            </div>
                        </div>
                    <?php endif; ?>
                <?php endfor; ?>
            </div>
            <?php endif; ?>
            
            <!-- Full Leaderboard Table -->
            <div class="leaderboard-table">
                <div class="leaderboard-header-row">
                    <div>Rank</div>
                    <div>Avatar</div>
                    <div>Player</div>
                    <div>Total Score</div>
                    <div>Games</div>
                    <div>Best</div>
                    <div>Accuracy</div>
                </div>
                
                <div class="leaderboard-body">
                    <?php foreach ($leaderboard as $index => $player): ?>
                        <div class="leaderboard-row <?php echo (isset($_SESSION['user_id']) && $player['id'] == $_SESSION['user_id']) ? 'current-user' : ''; ?>">
                            <div>
                                <div class="rank-badge <?php echo ($index < 3) ? 'rank-' . ($index + 1) : 'rank-default'; ?>">
                                    <?php echo $index + 1; ?>
                                </div>
                            </div>
                            <div>
                                <div class="player-avatar">
                                    <?php echo strtoupper(substr($player['username'], 0, 2)); ?>
                                </div>
                            </div>
                            <div class="player-info">
                                <div class="player-name"><?php echo htmlspecialchars($player['username']); ?></div>
                                <div class="player-fullname"><?php echo htmlspecialchars($player['full_name'] ?: 'No full name'); ?></div>
                            </div>
                            <div class="stat-value"><?php echo number_format($player['total_score']); ?></div>
                            <div class="stat-value"><?php echo $player['games_played']; ?></div>
                            <div class="stat-value"><?php echo number_format($player['best_score']); ?></div>
                            <div class="accuracy-value"><?php echo round($player['average_accuracy'], 1); ?>%</div>
                        </div>
                    <?php endforeach; ?>
                </div>
            </div>
        </div>
    </div>
    
    <!-- JavaScript -->
    <script src="assets/js/script.js"></script>
    
    <script>
        // Add scroll animations for leaderboard rows
        document.addEventListener('DOMContentLoaded', function() {
            const rows = document.querySelectorAll('.leaderboard-row');
            
            const observer = new IntersectionObserver((entries) => {
                entries.forEach(entry => {
                    if (entry.isIntersecting) {
                        entry.target.style.animation = 'slideInRight 0.5s ease forwards';
                    }
                });
            });
            
            rows.forEach((row, index) => {
                row.style.opacity = '0';
                row.style.animationDelay = `${index * 0.05}s`;
                observer.observe(row);
            });
        });
        
        // Add CSS animation
        const style = document.createElement('style');
        style.textContent = `
            @keyframes slideInRight {
                from {
                    opacity: 0;
                    transform: translateX(50px);
                }
                to {
                    opacity: 1;
                    transform: translateX(0);
                }
            }
        `;
        document.head.appendChild(style);
        
        // Add hover effects
        document.querySelectorAll('.leaderboard-row').forEach(row => {
            row.addEventListener('mouseenter', function() {
                window.ethiopianQuizApp.playSound('hover');
            });
        });
        
        // Auto-refresh leaderboard every 30 seconds
        setInterval(() => {
            // Optional: Add live leaderboard updates
            console.log('Leaderboard refresh check');
        }, 30000);
    </script>
</body>
</html>
