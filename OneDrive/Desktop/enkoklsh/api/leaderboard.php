<?php
require_once '../config/db.php';

try {
    // Get leaderboard data with rankings
    $query = "SELECT 
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
                LIMIT 100";

    $stmt = $db->prepare($query);
    $stmt->execute();
    $leaderboard = $stmt->fetchAll(PDO::FETCH_ASSOC);

    header('Content-Type: application/json');
    echo json_encode([
        'success' => true,
        'leaderboard' => $leaderboard,
        'count' => count($leaderboard)
    ]);

} catch(PDOException $e) {
    header('Content-Type: application/json');
    echo json_encode(['success' => false, 'message' => 'Database error: ' . $e->getMessage()]);
}
?>
