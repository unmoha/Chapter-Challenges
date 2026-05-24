<?php
require_once '../config/db.php';

// Get category ID from query parameter
$category_id = isset($_GET['category']) ? (int)$_GET['category'] : 0;
$lang = isset($_GET['lang']) ? $_GET['lang'] : 'en';

if ($category_id <= 0) {
    header('Content-Type: application/json');
    echo json_encode(['success' => false, 'message' => 'Invalid category ID']);
    exit();
}

try {
    // Build localized question selection if language fields exist
    $selectFields = getLocalizedQuestionSelect($db, $lang);
    $query = "SELECT id, category_id, {$selectFields}, correct_answer, difficulty, points FROM questions WHERE category_id = ? ORDER BY RAND() LIMIT 10";
    $stmt = $db->prepare($query);
    $stmt->execute([$category_id]);
    $questions = $stmt->fetchAll(PDO::FETCH_ASSOC);
    
    if (!empty($questions)) {
        header('Content-Type: application/json');
        echo json_encode([
            'success' => true,
            'questions' => $questions,
            'count' => count($questions)
        ]);
    } else {
        header('Content-Type: application/json');
        echo json_encode(['success' => false, 'message' => 'No questions found for this category']);
    }
    
} catch(PDOException $e) {
    header('Content-Type: application/json');
    echo json_encode(['success' => false, 'message' => 'Database error: ' . $e->getMessage()]);
}
?>
