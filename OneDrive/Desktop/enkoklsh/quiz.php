<?php
session_start();
require_once 'config/db.php';

// Initialize database connection
$database = new Database();
$db = $database->getConnection();

// Check if user is logged in
if (!isset($_SESSION['user_id'])) {
    header('Location: login.php');
    exit();
}

// Get categories for selection
$categories_query = "SELECT * FROM categories ORDER BY name";
$categories_stmt = $db->prepare($categories_query);
$categories_stmt->execute();
$categories = $categories_stmt->fetchAll(PDO::FETCH_ASSOC);

// Handle category selection
$selected_category = null;
$questions = [];
$lang = isset($_GET['lang']) ? $_GET['lang'] : 'en';
$lang = in_array($lang, ['en', 'am', 'om'], true) ? $lang : 'en';

if (isset($_GET['category']) && is_numeric($_GET['category'])) {
    $category_id = (int)$_GET['category'];
    
    // Validate category exists
    $category_query = "SELECT * FROM categories WHERE id = ?";
    $category_stmt = $db->prepare($category_query);
    $category_stmt->execute([$category_id]);
    $selected_category = $category_stmt->fetch(PDO::FETCH_ASSOC);
    
    if ($selected_category) {
        // Get questions for selected category with language fallback
        $selectFields = getLocalizedQuestionSelect($db, $lang);
        $questions_query = "SELECT id, category_id, {$selectFields}, correct_answer, difficulty, points FROM questions WHERE category_id = ? ORDER BY RAND() LIMIT 10";
        $questions_stmt = $db->prepare($questions_query);
        $questions_stmt->execute([$category_id]);
        $questions = $questions_stmt->fetchAll(PDO::FETCH_ASSOC);
    }
}
?>
<!DOCTYPE html>
<html lang="am" dir="ltr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Quiz - እንቆቅልሽ | Ethiopian Quiz Game</title>
    <meta name="description" content="Challenge yourself with our premium Ethiopian quiz questions">
    
    <!-- Google Fonts -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Segoe+UI:wght@300;400;500;600;700&family=Georgia:wght@400;700&family=Noto+Sans+Ethiopic:wght@400;700;900&display=swap" rel="stylesheet">
    
    <!-- CSS -->
    <link rel="stylesheet" href="assets/css/style.css">
    
    <style>
        .quiz-page {
            min-height: 100vh;
            padding-top: 80px;
            position: relative;
        }
        
        .quiz-container {
            max-width: 900px;
            margin: 0 auto;
            padding: var(--spacing-lg);
        }
        
        .category-selection {
            text-align: center;
            margin-bottom: var(--spacing-xl);
        }
        
        .category-selection h1 {
            font-size: 2.5rem;
            color: var(--neon-gold);
            margin-bottom: var(--spacing-md);
        }
        
        .category-selection p {
            color: rgba(255, 255, 255, 0.7);
            font-size: 1.1rem;
            margin-bottom: var(--spacing-xl);
        }
        
        .categories-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(250px, 1fr));
            gap: var(--spacing-lg);
            margin-bottom: var(--spacing-xl);
        }
        
        .category-select-card {
            background: var(--glass-bg);
            backdrop-filter: blur(20px);
            border: 1px solid var(--glass-border);
            border-radius: var(--radius-lg);
            padding: var(--spacing-lg);
            text-align: center;
            cursor: pointer;
            transition: all var(--transition-smooth);
            text-decoration: none;
            color: white;
            display: block;
        }
        
        .category-select-card:hover {
            transform: translateY(-10px) scale(1.02);
            box-shadow: 
                var(--glass-shadow),
                0 0 40px rgba(255, 215, 0, 0.3);
            border-color: rgba(255, 215, 0, 0.5);
        }
        
        .category-select-icon {
            font-size: 3rem;
            margin-bottom: var(--spacing-md);
            filter: drop-shadow(0 0 10px currentColor);
        }
        
        .category-select-title {
            font-size: 1.3rem;
            font-weight: 700;
            margin-bottom: var(--spacing-sm);
            color: var(--neon-gold);
        }
        
        .category-select-description {
            color: rgba(255, 255, 255, 0.7);
            line-height: 1.5;
            margin-bottom: var(--spacing-md);
        }
        
        .category-stats {
            font-size: 0.9rem;
            color: rgba(255, 255, 255, 0.5);
        }
        
        .quiz-interface {
            display: none;
        }
        
        .quiz-header {
            background: var(--glass-bg);
            backdrop-filter: blur(20px);
            border: 1px solid var(--glass-border);
            border-radius: var(--radius-lg);
            padding: var(--spacing-lg);
            margin-bottom: var(--spacing-lg);
            display: flex;
            justify-content: space-between;
            align-items: center;
            flex-wrap: wrap;
            gap: var(--spacing-md);
        }
        
        .quiz-info {
            display: flex;
            align-items: center;
            gap: var(--spacing-lg);
        }
        
        .question-counter {
            font-size: 1.2rem;
            color: rgba(255, 255, 255, 0.8);
        }
        
        .timer-container {
            display: flex;
            align-items: center;
            gap: var(--spacing-sm);
        }
        
        .timer-icon {
            font-size: 1.5rem;
            color: var(--neon-gold);
        }
        
        .timer-display {
            background: rgba(255, 255, 255, 0.1);
            border-radius: var(--radius-md);
            padding: var(--spacing-sm) var(--spacing-md);
            font-size: 1.3rem;
            font-weight: 700;
            min-width: 60px;
            text-align: center;
        }
        
        .timer-bar {
            width: 100px;
            height: 8px;
            background: rgba(255, 255, 255, 0.1);
            border-radius: 4px;
            overflow: hidden;
        }
        
        .timer-progress {
            height: 100%;
            background: linear-gradient(90deg, var(--ethiopian-green), var(--neon-green));
            width: 100%;
            transition: all 1s linear;
        }
        
        .quiz-score {
            font-size: 1.2rem;
            color: var(--neon-green);
            font-weight: 600;
        }
        
        .question-card {
            background: var(--glass-bg);
            backdrop-filter: blur(20px);
            border: 1px solid var(--glass-border);
            border-radius: var(--radius-lg);
            padding: var(--spacing-xl);
            margin-bottom: var(--spacing-lg);
            text-align: center;
        }
        
        .category-badge {
            display: inline-flex;
            align-items: center;
            gap: var(--spacing-sm);
            background: linear-gradient(135deg, var(--matte-gold), var(--bronze));
            color: var(--deep-black);
            padding: var(--spacing-xs) var(--spacing-md);
            border-radius: var(--radius-md);
            font-weight: 600;
            margin-bottom: var(--spacing-lg);
        }
        
        .question-text {
            font-size: 1.4rem;
            line-height: 1.6;
            color: rgba(255, 255, 255, 0.9);
            margin-bottom: var(--spacing-lg);
        }
        
        .progress-dots {
            display: flex;
            justify-content: center;
            gap: var(--spacing-sm);
            margin-top: var(--spacing-lg);
        }
        
        .progress-dot {
            width: 12px;
            height: 12px;
            border-radius: 50%;
            background: rgba(255, 255, 255, 0.2);
            transition: all var(--transition-smooth);
        }
        
        .progress-dot.active {
            background: var(--neon-gold);
            box-shadow: 0 0 10px var(--neon-gold);
        }
        
        .progress-dot.completed {
            background: var(--neon-green);
        }
        
        .answers-container {
            display: grid;
            gap: var(--spacing-md);
        }
        
        .answer-btn {
            background: var(--glass-bg);
            backdrop-filter: blur(20px);
            border: 2px solid var(--glass-border);
            border-radius: var(--radius-md);
            padding: var(--spacing-lg);
            color: white;
            font-size: 1.1rem;
            cursor: pointer;
            transition: all var(--transition-smooth);
            text-align: left;
            position: relative;
            overflow: hidden;
        }
        
        .answer-btn::before {
            content: '';
            position: absolute;
            top: 0;
            left: -100%;
            width: 100%;
            height: 100%;
            background: linear-gradient(90deg, transparent, rgba(255, 255, 255, 0.1), transparent);
            transition: left var(--transition-fast);
        }
        
        .answer-btn:hover::before {
            left: 100%;
        }
        
        .answer-btn:hover {
            transform: translateX(10px);
            border-color: var(--neon-gold);
            box-shadow: 0 0 20px rgba(255, 215, 0, 0.3);
        }
        
        .answer-btn.correct {
            background: linear-gradient(135deg, rgba(0, 255, 0, 0.2), rgba(0, 255, 0, 0.1));
            border-color: var(--neon-green);
            animation: correct-pulse 0.6s ease;
        }
        
        .answer-btn.wrong {
            background: linear-gradient(135deg, rgba(255, 0, 0, 0.2), rgba(255, 0, 0, 0.1));
            border-color: var(--ethiopian-red);
            animation: wrong-shake 0.6s ease;
        }
        
        .answer-btn:disabled {
            cursor: not-allowed;
            opacity: 0.7;
        }
        
        @keyframes correct-pulse {
            0%, 100% { transform: scale(1); }
            50% { transform: scale(1.05); }
        }
        
        @keyframes wrong-shake {
            0%, 100% { transform: translateX(0); }
            25% { transform: translateX(-10px); }
            75% { transform: translateX(10px); }
        }
        
        .quiz-controls {
            display: flex;
            justify-content: center;
            gap: var(--spacing-md);
            margin-top: var(--spacing-lg);
        }
        
        .back-btn {
            background: var(--glass-bg);
            backdrop-filter: blur(20px);
            border: 1px solid var(--glass-border);
            color: white;
            padding: var(--spacing-md) var(--spacing-lg);
            border-radius: var(--radius-md);
            text-decoration: none;
            transition: all var(--transition-smooth);
        }
        
        .back-btn:hover {
            background: rgba(255, 255, 255, 0.1);
            border-color: var(--neon-gold);
        }
        
        @media (max-width: 768px) {
            .quiz-header {
                flex-direction: column;
                text-align: center;
            }
            
            .quiz-info {
                flex-direction: column;
                gap: var(--spacing-md);
            }
            
            .categories-grid {
                grid-template-columns: 1fr;
            }
            
            .question-text {
                font-size: 1.2rem;
            }
            
            .answer-btn {
                padding: var(--spacing-md);
                font-size: 1rem;
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
    
    <!-- Quiz Page -->
    <div class="quiz-page">
        <div class="quiz-container">
            <!-- Category Selection -->
            <div class="category-selection" id="categorySelection">
                <h1 data-i18n-key="chooseYourChallenge">Choose Your Challenge</h1>
                <p data-i18n-key="selectCategory">Select a category to test your knowledge and earn points!</p>
                
                <div class="categories-grid">
                    <?php foreach ($categories as $category): ?>
                        <a href="quiz.php?category=<?php echo $category['id']; ?>&lang=<?php echo htmlspecialchars($lang); ?>" class="category-select-card">
                            <div class="category-select-icon" style="color: <?php echo $category['color']; ?>">
                                <?php echo htmlspecialchars($category['icon']); ?>
                            </div>
                            <h3 class="category-select-title"><?php echo htmlspecialchars($category['name']); ?></h3>
                            <p class="category-select-description"><?php echo htmlspecialchars($category['description']); ?></p>
                            <div class="category-stats">
                                <?php
                                $count_query = "SELECT COUNT(*) as count FROM questions WHERE category_id = ?";
                                $count_stmt = $db->prepare($count_query);
                                $count_stmt->execute([$category['id']]);
                                $count = $count_stmt->fetch(PDO::FETCH_ASSOC);
                                echo $count['count'] . ' questions available';
                                ?>
                            </div>
                        </a>
                    <?php endforeach; ?>
                </div>
            </div>
            
            <!-- Quiz Interface -->
            <?php if ($selected_category && !empty($questions)): ?>
            <div class="quiz-game-interface" id="quizInterface">
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
                        Loading question...
                    </div>
                    <div class="progress-dots" id="progressDots">
                        <?php for ($i = 0; $i < count($questions); $i++): ?>
                            <div class="progress-dot" data-question="<?php echo $i; ?>"></div>
                        <?php endfor; ?>
                    </div>
                </div>
                
                <div class="answers-container" id="answersContainer">
                    <button class="answer-btn" data-answer="A">
                        <span class="answer-text">Loading...</span>
                    </button>
                    <button class="answer-btn" data-answer="B">
                        <span class="answer-text">Loading...</span>
                    </button>
                    <button class="answer-btn" data-answer="C">
                        <span class="answer-text">Loading...</span>
                    </button>
                    <button class="answer-btn" data-answer="D">
                        <span class="answer-text">Loading...</span>
                    </button>
                </div>
                
                <div class="quiz-controls">
                    <a href="quiz.php?lang=<?php echo htmlspecialchars($lang); ?>" class="back-btn">← Back to Categories</a>
                </div>
            </div>
            
            <!-- Quiz Data -->
            <script>
                window.selectedLanguage = '<?php echo htmlspecialchars($lang); ?>';
                window.quizData = {
                    category: <?php echo json_encode($selected_category); ?>,
                    questions: <?php echo json_encode($questions); ?>,
                    categoryId: <?php echo $selected_category['id']; ?>,
                    lang: '<?php echo htmlspecialchars($lang); ?>'
                };
            </script>
            <?php endif; ?>
            
            <!-- No Questions Available -->
            <?php if ($selected_category && empty($questions)): ?>
            <div class="glass-card" style="text-align: center; padding: var(--spacing-xl);">
                <h2 style="color: var(--neon-gold); margin-bottom: var(--spacing-md);">No Questions Available</h2>
                <p style="color: rgba(255, 255, 255, 0.7); margin-bottom: var(--spacing-lg);">
                    Sorry, there are no questions available in this category yet. Please try another category.
                </p>
                <a href="quiz.php" class="btn btn-primary">Choose Another Category</a>
            </div>
            <?php endif; ?>
        </div>
    </div>
    
    <!-- JavaScript -->
    <script src="assets/js/script.js"></script>
    
    <?php if ($selected_category && !empty($questions)): ?>
    <script>
        class QuizEngine {
            constructor() {
                this.questions = window.quizData.questions;
                this.currentQuestionIndex = 0;
                this.score = 0;
                this.correctAnswers = 0;
                this.wrongAnswers = 0;
                this.timer = null;
                this.timeLeft = 30;
                this.userAnswers = [];
                
                this.init();
            }
            
            init() {
                this.loadQuestion();
                this.setupEventListeners();
            }
            
            setupEventListeners() {
                document.querySelectorAll('.answer-btn').forEach(btn => {
                    btn.addEventListener('click', (e) => this.selectAnswer(e));
                });
                
                // Keyboard shortcuts
                document.addEventListener('keydown', (e) => {
                    if (e.key >= '1' && e.key <= '4') {
                        const answerIndex = parseInt(e.key) - 1;
                        const answerBtn = document.querySelectorAll('.answer-btn')[answerIndex];
                        if (answerBtn && !answerBtn.disabled) {
                            answerBtn.click();
                        }
                    }
                });
            }
            
            loadQuestion() {
                if (this.currentQuestionIndex >= this.questions.length) {
                    this.endQuiz();
                    return;
                }
                
                const question = this.questions[this.currentQuestionIndex];
                
                // Update question display
                document.getElementById('questionNumber').textContent = this.currentQuestionIndex + 1;
                document.getElementById('questionText').textContent = question.question;
                
                // Update progress dots
                document.querySelectorAll('.progress-dot').forEach((dot, index) => {
                    dot.classList.remove('active', 'completed');
                    if (index < this.currentQuestionIndex) {
                        dot.classList.add('completed');
                    } else if (index === this.currentQuestionIndex) {
                        dot.classList.add('active');
                    }
                });
                
                // Update answer buttons
                const answers = [question.option_a, question.option_b, question.option_c, question.option_d];
                const answerBtns = document.querySelectorAll('.answer-btn');
                
                answerBtns.forEach((btn, index) => {
                    const answerText = btn.querySelector('.answer-text');
                    answerText.textContent = answers[index];
                    btn.classList.remove('correct', 'wrong');
                    btn.disabled = false;
                    btn.dataset.answer = ['A', 'B', 'C', 'D'][index];
                });
                
                // Start timer
                this.startTimer();
            }
            
            startTimer() {
                this.timeLeft = 30;
                this.updateTimerDisplay();
                
                this.timer = setInterval(() => {
                    this.timeLeft--;
                    this.updateTimerDisplay();
                    
                    if (this.timeLeft <= 0) {
                        this.timeUp();
                    }
                }, 1000);
            }
            
            stopTimer() {
                if (this.timer) {
                    clearInterval(this.timer);
                    this.timer = null;
                }
            }
            
            updateTimerDisplay() {
                document.getElementById('timerDisplay').textContent = this.timeLeft;
                
                const progress = (this.timeLeft / 30) * 100;
                const progressBar = document.getElementById('timerProgress');
                progressBar.style.width = progress + '%';
                
                // Change color based on time left
                if (this.timeLeft <= 10) {
                    progressBar.style.background = 'linear-gradient(90deg, #DA121A, #ff006e)';
                } else if (this.timeLeft <= 20) {
                    progressBar.style.background = 'linear-gradient(90deg, #FCDD09, #ff006e)';
                } else {
                    progressBar.style.background = 'linear-gradient(90deg, #078930, #00ff88)';
                }
            }
            
            selectAnswer(e) {
                const btn = e.currentTarget;
                const answer = btn.dataset.answer;
                
                // Stop timer
                this.stopTimer();
                
                // Disable all buttons
                document.querySelectorAll('.answer-btn').forEach(b => b.disabled = true);
                
                // Check answer
                const question = this.questions[this.currentQuestionIndex];
                const isCorrect = answer === question.correct_answer;
                
                // Store user answer
                this.userAnswers.push({
                    question_id: question.id,
                    user_answer: answer,
                    correct_answer: question.correct_answer,
                    is_correct: isCorrect
                });
                
                // Update score
                if (isCorrect) {
                    this.correctAnswers++;
                    const points = this.calculateScore();
                    this.score += points;
                    btn.classList.add('correct');
                    window.ethiopianQuizApp.playSound('correct');
                } else {
                    this.wrongAnswers++;
                    btn.classList.add('wrong');
                    this.showCorrectAnswer();
                    window.ethiopianQuizApp.playSound('wrong');
                }
                
                // Update score display
                document.getElementById('currentScore').textContent = this.score;
                
                // Next question after delay
                setTimeout(() => {
                    this.currentQuestionIndex++;
                    this.loadQuestion();
                }, 2000);
            }
            
            showCorrectAnswer() {
                const question = this.questions[this.currentQuestionIndex];
                const correctAnswer = question.correct_answer;
                const answerBtns = document.querySelectorAll('.answer-btn');
                
                answerBtns.forEach(btn => {
                    if (btn.dataset.answer === correctAnswer) {
                        btn.classList.add('correct');
                    }
                });
            }
            
            calculateScore() {
                const baseScore = 10;
                const timeBonus = Math.max(0, this.timeLeft * 2);
                const difficultyMultiplier = this.questions[this.currentQuestionIndex].difficulty === 'hard' ? 2 : 
                                           this.questions[this.currentQuestionIndex].difficulty === 'medium' ? 1.5 : 1;
                
                return Math.round(baseScore * difficultyMultiplier + timeBonus);
            }
            
            timeUp() {
                this.stopTimer();
                this.wrongAnswers++;
                this.showCorrectAnswer();
                window.ethiopianQuizApp.playSound('wrong');
                
                setTimeout(() => {
                    this.currentQuestionIndex++;
                    this.loadQuestion();
                }, 2000);
            }
            
            async endQuiz() {
                this.stopTimer();
                
                const accuracy = this.questions.length > 0 ? 
                    Math.round((this.correctAnswers / this.questions.length) * 100) : 0;
                
                // Save score to database
                try {
                    const response = await fetch('api/save_score.php', {
                        method: 'POST',
                        headers: {
                            'Content-Type': 'application/json',
                        },
                        body: JSON.stringify({
                            category_id: window.quizData.categoryId,
                            score: this.score,
                            correct_answers: this.correctAnswers,
                            wrong_answers: this.wrongAnswers,
                            total_questions: this.questions.length,
                            accuracy: accuracy,
                            time_taken: (30 * this.questions.length) - this.timeLeft,
                            answers: this.userAnswers
                        })
                    });
                    
                    const data = await response.json();
                    if (data.success) {
                        // Redirect to result page
                        window.location.href = `result.php?score=${this.score}&accuracy=${accuracy}&correct=${this.correctAnswers}&wrong=${this.wrongAnswers}&category=${encodeURIComponent(window.quizData.category.name)}`;
                    } else {
                        console.error('Failed to save score:', data.message);
                        // Still redirect even if save failed
                        window.location.href = `result.php?score=${this.score}&accuracy=${accuracy}&correct=${this.correctAnswers}&wrong=${this.wrongAnswers}&category=${encodeURIComponent(window.quizData.category.name)}`;
                    }
                } catch (error) {
                    console.error('Error saving score:', error);
                    // Still redirect even if save failed
                    window.location.href = `result.php?score=${this.score}&accuracy=${accuracy}&correct=${this.correctAnswers}&wrong=${this.wrongAnswers}&category=${encodeURIComponent(window.quizData.category.name)}`;
                }
            }
        }
        
        // Initialize quiz when page loads
        document.addEventListener('DOMContentLoaded', () => {
            if (window.quizData) {
                new QuizEngine();
            }
        });
    </script>
    <?php endif; ?>
</body>
</html>
