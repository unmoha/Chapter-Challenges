<?php
session_start();
require_once 'config/db.php';

// Initialize database connection
$database = new Database();
$db = $database->getConnection();

// Check if user is logged in
if (!isset($_SESSION['user_id'])) {
    echo "User not logged in. <a href='login.php'>Login here</a>";
    exit();
}

// Test category selection
$selected_category = null;
$questions = [];

echo "<h1>Simple Quiz Test</h1>";

// Test with category 1
$category_id = 1;
echo "Testing with Category ID: " . $category_id . "<br>";

// Validate category exists
$category_query = "SELECT * FROM categories WHERE id = ?";
$category_stmt = $db->prepare($category_query);
$category_stmt->execute([$category_id]);
$selected_category = $category_stmt->fetch(PDO::FETCH_ASSOC);

if ($selected_category) {
    echo "✅ Category found: " . $selected_category['name'] . "<br>";
    
    // Get questions for selected category
    $questions_query = "SELECT * FROM questions WHERE category_id = ? ORDER BY RAND() LIMIT 10";
    $questions_stmt = $db->prepare($questions_query);
    $questions_stmt->execute([$category_id]);
    $questions = $questions_stmt->fetchAll(PDO::FETCH_ASSOC);
    
    echo "Questions found: " . count($questions) . "<br>";
    
    if (!empty($questions)) {
        echo "✅ Quiz interface should show<br>";
        echo "First question: " . $questions[0]['question'] . "<br>";
        
        // Test the condition that controls quiz display
        $should_show = ($selected_category && !empty($questions));
        echo "Quiz display condition: " . ($should_show ? "TRUE" : "FALSE") . "<br>";
        
        // Show the actual quiz interface
        ?>
        <div style="border: 2px solid #FFD700; padding: 20px; margin: 20px; background: rgba(0,0,0,0.8);">
            <h2>Quiz Interface Test</h2>
            <div class="quiz-container" id="quizInterface">
                <div class="quiz-header">
                    <div class="quiz-info">
                        <span class="question-counter">Question <span id="questionNumber">1</span>/<span id="totalQuestions"><?php echo count($questions); ?></span></span>
                        <div class="timer-container">
                            <span class="timer-icon">⏱️</span>
                            <div class="timer-bar">
                                <div class="timer-progress" id="timerProgress"></div>
                            </div>
                            <span class="timer-display" id="timerDisplay">30</span>
                        </div>
                    </div>
                    <div class="quiz-score">
                        Score: <span id="currentScore">0</span>
                    </div>
                </div>
                
                <div class="question-card">
                    <div class="category-badge">
                        <span><?php echo htmlspecialchars($selected_category['icon']); ?></span>
                        <span><?php echo htmlspecialchars($selected_category['name']); ?></span>
                    </div>
                    <div class="question-text" id="questionText">
                        <?php echo htmlspecialchars($questions[0]['question']); ?>
                    </div>
                    <div class="progress-dots" id="progressDots">
                        <?php for ($i = 0; $i < count($questions); $i++): ?>
                            <div class="progress-dot" data-question="<?php echo $i; ?>"></div>
                        <?php endfor; ?>
                    </div>
                </div>
                
                <div class="answers-container" id="answersContainer">
                    <button class="answer-btn" data-answer="A">
                        <span class="answer-text"><?php echo htmlspecialchars($questions[0]['option_a']); ?></span>
                    </button>
                    <button class="answer-btn" data-answer="B">
                        <span class="answer-text"><?php echo htmlspecialchars($questions[0]['option_b']); ?></span>
                    </button>
                    <button class="answer-btn" data-answer="C">
                        <span class="answer-text"><?php echo htmlspecialchars($questions[0]['option_c']); ?></span>
                    </button>
                    <button class="answer-btn" data-answer="D">
                        <span class="answer-text"><?php echo htmlspecialchars($questions[0]['option_d']); ?></span>
                    </button>
                </div>
            </div>
        </div>
        
        <style>
            .quiz-container { padding: 20px; }
            .quiz-header { display: flex; justify-content: space-between; margin-bottom: 20px; }
            .question-card { text-align: center; margin-bottom: 20px; }
            .category-badge { background: #FFD700; color: black; padding: 5px 10px; border-radius: 5px; display: inline-block; margin-bottom: 10px; }
            .question-text { font-size: 18px; margin-bottom: 20px; }
            .answer-btn { display: block; width: 100%; padding: 15px; margin: 10px 0; background: #333; color: white; border: 2px solid #FFD700; cursor: pointer; }
            .answer-btn:hover { background: #FFD700; color: black; }
            .progress-dots { display: flex; justify-content: center; gap: 10px; margin-top: 20px; }
            .progress-dot { width: 10px; height: 10px; background: #666; border-radius: 50%; }
            .progress-dot.active { background: #FFD700; }
        </style>
        
        <?php
    } else {
        echo "❌ No questions found for this category<br>";
    }
} else {
    echo "❌ Category not found<br>";
}

echo "<br><a href='quiz.php?category=1'>Go to actual quiz page</a>";
?>
