<?php
session_start();
require_once 'config/db.php';

// Initialize database connection
$database = new Database();
$db = $database->getConnection();

echo "<h1>Quiz Debug Information</h1>";

// Check if user is logged in
echo "<h2>Session Check:</h2>";
if (isset($_SESSION['user_id'])) {
    echo "✅ User is logged in. User ID: " . $_SESSION['user_id'] . "<br>";
} else {
    echo "❌ User is NOT logged in. Redirecting to login...<br>";
    echo '<a href="login.php">Go to Login</a>';
    exit();
}

// Check database connection
echo "<h2>Database Connection:</h2>";
if ($db) {
    echo "✅ Database connection successful<br>";
} else {
    echo "❌ Database connection failed<br>";
    exit();
}

// Check categories
echo "<h2>Categories:</h2>";
$categories_query = "SELECT * FROM categories ORDER BY name";
$categories_stmt = $db->prepare($categories_query);
$categories_stmt->execute();
$categories = $categories_stmt->fetchAll(PDO::FETCH_ASSOC);

echo "Found " . count($categories) . " categories:<br>";
foreach ($categories as $category) {
    echo "- ID: " . $category['id'] . ", Name: " . $category['name'] . "<br>";
}

// Check questions for each category
echo "<h2>Questions Check:</h2>";
foreach ($categories as $category) {
    $questions_query = "SELECT * FROM questions WHERE category_id = ? ORDER BY RAND() LIMIT 10";
    $questions_stmt = $db->prepare($questions_query);
    $questions_stmt->execute([$category['id']]);
    $questions = $questions_stmt->fetchAll(PDO::FETCH_ASSOC);
    
    echo "Category " . $category['name'] . ": " . count($questions) . " questions found<br>";
    
    if (!empty($questions)) {
        echo "  First question: " . $questions[0]['question'] . "<br>";
    }
}

// Test category selection
echo "<h2>Category Selection Test:</h2>";
if (isset($_GET['category']) && is_numeric($_GET['category'])) {
    $category_id = (int)$_GET['category'];
    echo "Selected Category ID: " . $category_id . "<br>";
    
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
            echo "✅ Questions loaded successfully<br>";
            echo "Sample question: " . $questions[0]['question'] . "<br>";
            
            // Test JSON encoding
            $quizData = [
                'category' => $selected_category,
                'questions' => $questions,
                'categoryId' => $selected_category['id']
            ];
            
            $jsonQuizData = json_encode($quizData);
            if ($jsonQuizData) {
                echo "✅ JSON encoding successful<br>";
                echo "JSON size: " . strlen($jsonQuizData) . " characters<br>";
            } else {
                echo "❌ JSON encoding failed<br>";
            }
        } else {
            echo "❌ No questions found for this category<br>";
        }
    } else {
        echo "❌ Category not found<br>";
    }
} else {
    echo "No category selected. Test with: ?category=1<br>";
    echo '<a href="?category=1">Test Category 1</a><br>';
    echo '<a href="?category=2">Test Category 2</a><br>';
    echo '<a href="?category=3">Test Category 3</a><br>';
}

echo "<h2>PHP Error Log:</h2>";
$error_log = ini_get('error_log');
echo "Error log location: " . $error_log . "<br>";

echo "<h2>Test Links:</h2>";
echo '<a href="quiz.php">Go to Quiz Page</a><br>';
echo '<a href="index.php">Go to Home</a><br>';
?>
