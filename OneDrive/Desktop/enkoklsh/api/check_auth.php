<?php
session_start();
require_once '../config/db.php';

if (isset($_SESSION['user_id'])) {
    // User is logged in, get user data
    $user_id = $_SESSION['user_id'];
    $query = "SELECT id, username, email, full_name, total_score, games_played, best_score, accuracy FROM users WHERE id = ?";
    $stmt = $db->prepare($query);
    $stmt->execute([$user_id]);
    $user = $stmt->fetch(PDO::FETCH_ASSOC);
    
    header('Content-Type: application/json');
    echo json_encode([
        'authenticated' => true,
        'user' => $user
    ]);
} else {
    // User is not logged in
    header('Content-Type: application/json');
    echo json_encode([
        'authenticated' => false
    ]);
}
?>
