<?php
session_start();
require_once '../config/db.php';

// Check if user is logged in
if (!isset($_SESSION['user_id'])) {
    header('Content-Type: application/json');
    echo json_encode(['success' => false, 'message' => 'User not logged in']);
    exit();
}

// Get POST data
$json_data = file_get_contents('php://input');
$data = json_decode($json_data, true);

if (!$data) {
    header('Content-Type: application/json');
    echo json_encode(['success' => false, 'message' => 'Invalid data']);
    exit();
}

// Validate required fields
$required_fields = ['category_id', 'score', 'correct_answers', 'wrong_answers', 'total_questions', 'accuracy'];
foreach ($required_fields as $field) {
    if (!isset($data[$field])) {
        header('Content-Type: application/json');
        echo json_encode(['success' => false, 'message' => "Missing field: $field"]);
        exit();
    }
}

try {
    // Insert score record
    $query = "INSERT INTO scores (user_id, category_id, score, correct_answers, wrong_answers, total_questions, accuracy, time_taken, completed_at) 
              VALUES (?, ?, ?, ?, ?, ?, ?, ?, NOW())";
    
    $stmt = $db->prepare($query);
    $result = $stmt->execute([
        $_SESSION['user_id'],
        $data['category_id'],
        $data['score'],
        $data['correct_answers'],
        $data['wrong_answers'],
        $data['total_questions'],
        $data['accuracy'],
        $data['time_taken'] ?? 0
    ]);
    
    if ($result) {
        // Update user total score
        $update_query = "UPDATE users SET 
                        total_score = (SELECT COALESCE(SUM(score), 0) FROM scores WHERE user_id = ?),
                        games_played = (SELECT COUNT(*) FROM scores WHERE user_id = ?),
                        best_score = GREATEST(best_score, ?),
                        accuracy = (SELECT AVG(accuracy) FROM scores WHERE user_id = ?),
                        updated_at = NOW()
                        WHERE id = ?";
        
        $update_stmt = $db->prepare($update_query);
        $update_stmt->execute([
            $_SESSION['user_id'],
            $_SESSION['user_id'],
            $data['score'],
            $_SESSION['user_id'],
            $_SESSION['user_id']
        ]);
        
        // Update leaderboard
        $this->updateLeaderboard($_SESSION['user_id']);
        
        header('Content-Type: application/json');
        echo json_encode(['success' => true, 'message' => 'Score saved successfully']);
    } else {
        header('Content-Type: application/json');
        echo json_encode(['success' => false, 'message' => 'Failed to save score']);
    }
    
} catch(PDOException $e) {
    header('Content-Type: application/json');
    echo json_encode(['success' => false, 'message' => 'Database error: ' . $e->getMessage()]);
}

function updateLeaderboard($user_id) {
    global $db;
    
    try {
        // Calculate user stats
        $stats_query = "SELECT 
                        COALESCE(SUM(score), 0) as total_score,
                        COUNT(*) as games_played,
                        COALESCE(MAX(score), 0) as best_score,
                        COALESCE(AVG(accuracy), 0) as average_accuracy
                        FROM scores WHERE user_id = ?";
        
        $stats_stmt = $db->prepare($stats_query);
        $stats_stmt->execute([$user_id]);
        $stats = $stats_stmt->fetch(PDO::FETCH_ASSOC);
        
        // Update or insert leaderboard entry
        $upsert_query = "INSERT INTO leaderboard (user_id, total_score, games_played, best_score, average_accuracy) 
                         VALUES (?, ?, ?, ?, ?)
                         ON DUPLICATE KEY UPDATE 
                         total_score = VALUES(total_score),
                         games_played = VALUES(games_played),
                         best_score = VALUES(best_score),
                         average_accuracy = VALUES(average_accuracy),
                         last_updated = NOW()";
        
        $upsert_stmt = $db->prepare($upsert_query);
        $upsert_stmt->execute([
            $user_id,
            $stats['total_score'],
            $stats['games_played'],
            $stats['best_score'],
            $stats['average_accuracy']
        ]);
        
    } catch(PDOException $e) {
        error_log("Leaderboard update error: " . $e->getMessage());
    }
}
?>
