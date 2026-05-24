<?php
session_start();
require_once '../config/db.php';

// Check if user is logged in
if (!isset($_SESSION['user_id'])) {
    header('Content-Type: application/json');
    echo json_encode(['success' => false, 'message' => 'User not logged in']);
    exit();
}

$user_id = $_SESSION['user_id'];

try {
    // Get comprehensive user statistics
    $stats_query = "SELECT 
                    u.total_score,
                    u.games_played,
                    u.best_score,
                    u.accuracy,
                    u.created_at,
                    COUNT(s.id) as total_games,
                    SUM(s.score) as calculated_score,
                    MAX(s.score) as calculated_best,
                    AVG(s.accuracy) as calculated_accuracy,
                    SUM(s.correct_answers) as total_correct,
                    SUM(s.wrong_answers) as total_wrong,
                    SUM(s.time_taken) as total_time
                    FROM users u
                    LEFT JOIN scores s ON u.id = s.user_id
                    WHERE u.id = ?
                    GROUP BY u.id";
    
    $stmt = $db->prepare($stats_query);
    $stmt->execute([$user_id]);
    $stats = $stmt->fetch(PDO::FETCH_ASSOC);
    
    // Get category performance
    $category_query = "SELECT 
                        c.name as category_name,
                        c.icon as category_icon,
                        COUNT(s.id) as games_played,
                        AVG(s.accuracy) as average_accuracy,
                        MAX(s.score) as best_score,
                        SUM(s.score) as total_score
                        FROM scores s
                        JOIN categories c ON s.category_id = c.id
                        WHERE s.user_id = ?
                        GROUP BY s.category_id, c.name, c.icon
                        ORDER BY total_score DESC";
    
    $category_stmt = $db->prepare($category_query);
    $category_stmt->execute([$user_id]);
    $category_stats = $category_stmt->fetchAll(PDO::FETCH_ASSOC);
    
    // Get recent games
    $recent_query = "SELECT s.*, c.name as category_name, c.icon as category_icon 
                     FROM scores s 
                     JOIN categories c ON s.category_id = c.id 
                     WHERE s.user_id = ? 
                     ORDER BY s.completed_at DESC 
                     LIMIT 10";
    
    $recent_stmt = $db->prepare($recent_query);
    $recent_stmt->execute([$user_id]);
    $recent_games = $recent_stmt->fetchAll(PDO::FETCH_ASSOC);
    
    // Calculate additional stats
    $total_questions = $stats['total_correct'] + $stats['total_wrong'];
    $overall_accuracy = $total_questions > 0 ? round(($stats['total_correct'] / $total_questions) * 100, 1) : 0;
    $average_time_per_game = $stats['total_games'] > 0 ? round($stats['total_time'] / $stats['total_games']) : 0;
    
    header('Content-Type: application/json');
    echo json_encode([
        'success' => true,
        'stats' => [
            'total_score' => (int)($stats['calculated_score'] ?: $stats['total_score']),
            'games_played' => (int)($stats['total_games'] ?: $stats['games_played']),
            'best_score' => (int)($stats['calculated_best'] ?: $stats['best_score']),
            'average_accuracy' => round($stats['calculated_accuracy'] ?: $stats['accuracy'], 1),
            'total_correct' => (int)($stats['total_correct'] ?: 0),
            'total_wrong' => (int)($stats['total_wrong'] ?: 0),
            'total_questions' => (int)$total_questions,
            'overall_accuracy' => $overall_accuracy,
            'average_time_per_game' => $average_time_per_game,
            'member_since' => $stats['created_at'],
            'category_performance' => $category_stats,
            'recent_games' => $recent_games
        ]
    ]);
    
} catch(PDOException $e) {
    header('Content-Type: application/json');
    echo json_encode(['success' => false, 'message' => 'Database error: ' . $e->getMessage()]);
}
?>
