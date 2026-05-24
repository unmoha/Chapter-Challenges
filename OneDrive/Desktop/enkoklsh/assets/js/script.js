/**
 * እናቆቅልሽ - Premium Ethiopian Quiz Application
 * Dynamic JavaScript Engine
 * Cinematic UI Interactions & Quiz Logic
 */

class EthiopianQuizApp {
    constructor() {
        this.currentScreen = 'home';
        this.currentCategory = null;
        this.currentQuestion = null;
        this.questions = [];
        this.currentQuestionIndex = 0;
        this.score = 0;
        this.correctAnswers = 0;
        this.wrongAnswers = 0;
        this.timer = null;
        this.timeLeft = 30;
        this.soundEnabled = true;
        this.userAnswers = [];
        this.language = 'en';
        this.languageTexts = {
            home: { en: 'Home', am: 'ዋና ገጽ', om: 'Mana' },
            categories: { en: 'Categories', am: 'ምድቦች', om: 'Qajeelfamoota' },
            leaderboard: { en: 'Leaderboard', am: 'የእግር ጨዋታ ሰንጠረዥ', om: 'Gabatee Bakka' },
            dashboard: { en: 'Dashboard', am: 'ዳሽቦርድ', om: 'Gabatee' },
            logout: { en: 'Logout', am: 'ውጣ', om: 'Baʼi' },
            login: { en: 'Login', am: 'ግባ', om: 'Seeni' },
            register: { en: 'Register', am: 'ይመዝገቡ', om: 'Galmaaʼi' },
            continuePlaying: { en: 'Continue Playing', am: 'ጨዋታን ይቀጥሉ', om: 'Tapha Itti Fufi' },
            startJourney: { en: 'Start Journey', am: 'ጉዞን ይጀምሩ', om: 'Imala Jalqabi' },
            exploreCategories: { en: 'Explore Categories', am: 'ምድቦችን ይመልከቱ', om: 'Qajeelfamoota Ilaali' },
            chooseYourChallenge: { en: 'Choose Your Challenge', am: 'ሙከራዎን ይምረጡ', om: 'Filannoo Kee Filadhu' },
            selectCategory: { en: 'Select a category to test your knowledge and earn points!', am: 'እውቀትዎን ለማስተካከል ምድብ ይምረጡ!', om: 'Qaaccessa kee madaaluuf kutaa filadhu!' },
            signInToContinue: { en: 'Sign in to continue your journey', am: 'ጉዞዎን ለመቀጠል ይግቡ', om: 'Daandii Keessan Itti Fufuuf Seeni' },
            usernameOrEmail: { en: 'Username or Email', am: 'የተጠቃሚ ስም ወይም ኢሜይል', om: 'Maqaa fayyadamaa yookiin Imeelii' },
            password: { en: 'Password', am: 'የይለፍ ቃል', om: 'Jecha darbii' },
            rememberMe: { en: 'Remember me', am: 'እንዳንረሳ', om: 'Na Yaadadhu' },
            forgotPassword: { en: 'Forgot password?', am: 'የይለፍ ቃል ረሳዎት?', om: 'Jecha darbii dagatte?' },
            signIn: { en: 'Sign In', am: 'ግባ', om: 'Seeni' },
            orText: { en: 'OR', am: 'ወይም', om: 'YKN' },
            dontHaveAccount: { en: "Don't have an account?", am: 'ሂሳብ የለዎትም?', om: 'Akaawuntii hin qabduu?' },
            signUp: { en: 'Sign up', am: 'ይመዝገቡ', om: 'Galmaaʼi' },
            backToHome: { en: 'Back to Home', am: 'ወደ ቤት ይመለሱ', om: 'Deebiʼi Mana' }
        };

        this.init();
    }

    init() {
        this.initLanguage();
        this.createParticles();
        this.setupEventListeners();
        this.ensureLanguageToggleFallback();
        this.initializeAnimations();
        this.checkAuthStatus();
    }

    // ===== PARTICLE SYSTEM =====
    createParticles() {
        const particlesContainer = document.querySelector('.floating-particles');
        if (!particlesContainer) return;

        for (let i = 0; i < 50; i++) {
            const particle = document.createElement('div');
            particle.className = 'particle';
            particle.style.left = Math.random() * 100 + '%';
            particle.style.animationDelay = Math.random() * 10 + 's';
            particle.style.animationDuration = (10 + Math.random() * 10) + 's';

            // Random colors
            const colors = ['#FFD700', '#00d4ff', '#9d4edd', '#00ff88'];
            particle.style.background = colors[Math.floor(Math.random() * colors.length)];
            particle.style.boxShadow = `0 0 10px ${particle.style.background}`;

            particlesContainer.appendChild(particle);
        }
    }

    // ===== EVENT LISTENERS =====
    setupEventListeners() {
        // Navigation
        document.querySelectorAll('.navbar-link').forEach(link => {
            link.addEventListener('click', (e) => this.handleNavigation(e));
        });

        // Category cards
        document.querySelectorAll('.category-card').forEach(card => {
            card.addEventListener('click', (e) => this.selectCategory(e));
        });

        // Quiz answers
        document.querySelectorAll('.answer-btn').forEach(btn => {
            btn.addEventListener('click', (e) => this.selectAnswer(e));
        });

        // Forms
        const loginForm = document.getElementById('loginForm');
        const registerForm = document.getElementById('registerForm');

        if (loginForm) {
            loginForm.addEventListener('submit', (e) => this.handleLogin(e));
        }

        if (registerForm) {
            registerForm.addEventListener('submit', (e) => this.handleRegister(e));
        }

        // Keyboard shortcuts
        document.addEventListener('keydown', (e) => this.handleKeyboard(e));

        // Sound toggle
        const soundToggle = document.getElementById('soundToggle');
        if (soundToggle) {
            soundToggle.addEventListener('click', () => this.toggleSound());
        }

        // Language selector
        const languageSelector = document.getElementById('languageSelector');
        if (languageSelector) {
            languageSelector.addEventListener('change', (e) => this.setLanguage(e.target.value));
        }
    }

    // ===== LANGUAGE SUPPORT =====
    initLanguage() {
        const savedLanguage = localStorage.getItem('anakoklish_language');
        if (savedLanguage && ['en', 'am', 'om'].includes(savedLanguage)) {
            this.language = savedLanguage;
        }
        this.applyLanguage();
        this.ensureCorrectLanguageOnLoad();
    }

    ensureCorrectLanguageOnLoad() {
        const urlLang = this.getUrlParameter('lang');
        const isQuizPage = window.location.pathname.endsWith('quiz.php');

        if (isQuizPage && this.language !== 'en' && !urlLang) {
            const url = new URL(window.location.href);
            url.searchParams.set('lang', this.language);
            window.location.href = url.toString();
        }
    }

    setLanguage(language) {
        if (!['en', 'am', 'om'].includes(language)) {
            language = 'en';
        }
        this.language = language;
        localStorage.setItem('anakoklish_language', language);
        this.applyLanguage();
        this.redirectWithLang();
    }

    getUrlParameter(name) {
        const urlSearchParams = new URLSearchParams(window.location.search);
        return urlSearchParams.get(name);
    }

    redirectWithLang() {
        const currentLang = this.getUrlParameter('lang');
        if (currentLang !== this.language) {
            const url = new URL(window.location.href);
            url.searchParams.set('lang', this.language);
            window.history.replaceState({}, '', url.toString());
        }
    }

    getLocalizedText(key) {
        if (!this.languageTexts[key]) {
            return '';
        }
        return this.languageTexts[key][this.language] || this.languageTexts[key].en;
    }

    applyLanguage() {
        document.documentElement.lang = this.language === 'am' ? 'am' : (this.language === 'om' ? 'om' : 'en');
        const languageSelector = document.getElementById('languageSelector');
        if (languageSelector) {
            languageSelector.value = this.language;
        }

        document.querySelectorAll('[data-i18n-key]').forEach(element => {
            const key = element.dataset.i18nKey;
            if (!key) return;
            element.textContent = this.getLocalizedText(key);
        });

        document.querySelectorAll('[data-i18n-placeholder]').forEach(element => {
            const key = element.dataset.i18nPlaceholder;
            if (!key) return;
            element.placeholder = this.getLocalizedText(key);
        });

        document.querySelectorAll('[data-i18n-title]').forEach(element => {
            const key = element.dataset.i18nTitle;
            if (!key) return;
            document.title = this.getLocalizedText(key);
        });
    }

    // If the language selector isn't present or visible, inject a visible toggle button
    ensureLanguageToggleFallback() {
        const sel = document.getElementById('languageSelector');
        const isVisible = sel && sel.offsetParent !== null && window.getComputedStyle(sel).display !== 'none' && window.getComputedStyle(sel).visibility !== 'hidden';
        if (isVisible) return;

        const nav = document.querySelector('.navbar-nav');
        if (!nav) return;

        // Avoid duplicate
        if (document.getElementById('languageToggle')) return;

        const btn = document.createElement('button');
        btn.id = 'languageToggle';
        btn.className = 'btn btn-ghost';
        btn.type = 'button';
        btn.title = 'Language';
        btn.innerHTML = '🌐';

        const menu = document.createElement('div');
        menu.id = 'languageMenu';
        menu.style.position = 'absolute';
        menu.style.top = '44px';
        menu.style.right = '0';
        menu.style.background = 'rgba(0,0,0,0.85)';
        menu.style.border = '1px solid rgba(255,255,255,0.08)';
        menu.style.borderRadius = '8px';
        menu.style.padding = '6px';
        menu.style.display = 'none';
        menu.style.zIndex = '1200';

        const langs = [ ['en','English'], ['am','አማርኛ'], ['om','Afaan Oromoo'] ];
        langs.forEach(([code,label]) => {
            const item = document.createElement('button');
            item.className = 'language-menu-item';
            item.style.display = 'block';
            item.style.padding = '6px 10px';
            item.style.background = 'transparent';
            item.style.color = 'white';
            item.style.border = 'none';
            item.style.width = '100%';
            item.style.textAlign = 'left';
            item.style.cursor = 'pointer';
            item.textContent = label;
            item.dataset.lang = code;
            item.addEventListener('click', (e) => {
                const lang = e.currentTarget.dataset.lang;
                this.setLanguage(lang);
                menu.style.display = 'none';
            });
            menu.appendChild(item);
        });

        const wrapper = document.createElement('div');
        wrapper.style.position = 'relative';
        wrapper.appendChild(btn);
        wrapper.appendChild(menu);

        // Insert before the sound toggle if present, else append
        const soundBtn = document.getElementById('soundToggle');
        if (soundBtn && soundBtn.parentNode) {
            soundBtn.parentNode.insertBefore(wrapper, soundBtn);
        } else {
            nav.appendChild(wrapper);
        }

        btn.addEventListener('click', (e) => {
            e.stopPropagation();
            menu.style.display = menu.style.display === 'none' ? 'block' : 'none';
        });

        // close on outside click
        document.addEventListener('click', (e) => {
            if (!wrapper.contains(e.target)) {
                menu.style.display = 'none';
            }
        });
    }

    // ===== AUTHENTICATION =====
    async checkAuthStatus() {
        try {
            const response = await fetch('api/check_auth.php');
            const data = await response.json();

            if (data.authenticated) {
                this.updateUserUI(data.user);
            } else {
                this.showGuestUI();
            }
        } catch (error) {
            console.error('Auth check failed:', error);
            this.showGuestUI();
        }
    }

    async handleLogin(e) {
        e.preventDefault();
        const formData = new FormData(e.target);

        try {
            const response = await fetch('api/login.php', {
                method: 'POST',
                body: formData
            });

            const data = await response.json();

            if (data.success) {
                this.showNotification('Login successful!', 'success');
                this.updateUserUI(data.user);
                setTimeout(() => window.location.href = 'dashboard.php', 1000);
            } else {
                this.showNotification(data.message, 'error');
            }
        } catch (error) {
            this.showNotification('Login failed. Please try again.', 'error');
        }
    }

    async handleRegister(e) {
        e.preventDefault();
        const formData = new FormData(e.target);

        try {
            const response = await fetch('api/register.php', {
                method: 'POST',
                body: formData
            });

            const data = await response.json();

            if (data.success) {
                this.showNotification('Registration successful! Please login.', 'success');
                setTimeout(() => window.location.href = 'login.php', 1500);
            } else {
                this.showNotification(data.message, 'error');
            }
        } catch (error) {
            this.showNotification('Registration failed. Please try again.', 'error');
        }
    }

    // ===== NAVIGATION =====
    handleNavigation(e) {
        e.preventDefault();
        const target = e.target.getAttribute('href');

        if (target.startsWith('#')) {
            this.showScreen(target.substring(1));
        } else {
            window.location.href = target;
        }
    }

    showScreen(screenName) {
        // Hide all screens
        document.querySelectorAll('.screen').forEach(screen => {
            screen.classList.remove('active');
        });

        // Show target screen
        const targetScreen = document.getElementById(screenName + 'Screen');
        if (targetScreen) {
            targetScreen.classList.add('active');
            this.currentScreen = screenName;

            // Screen-specific initialization
            this.initializeScreen(screenName);
        }
    }

    initializeScreen(screenName) {
        switch (screenName) {
            case 'quiz':
                this.startQuiz();
                break;
            case 'leaderboard':
                this.loadLeaderboard();
                break;
            case 'dashboard':
                this.loadUserStats();
                break;
        }
    }

    // ===== CATEGORY SELECTION =====
    selectCategory(e) {
        const card = e.currentTarget;
        const categoryId = card.dataset.categoryId;

        // Visual feedback
        card.style.transform = 'scale(0.95)';
        this.playSound('select');

        setTimeout(() => {
            this.currentCategory = categoryId;
            this.loadQuestions(categoryId);
        }, 200);
    }

    async loadQuestions(categoryId) {
        try {
            const response = await fetch(`api/questions.php?category=${categoryId}&lang=${this.language}`);
            const data = await response.json();

            if (data.success) {
                this.questions = this.shuffleArray(data.questions);
                this.currentQuestionIndex = 0;
                this.showScreen('quiz');
            } else {
                this.showNotification('Failed to load questions', 'error');
            }
        } catch (error) {
            this.showNotification('Error loading questions', 'error');
        }
    }

    // ===== QUIZ ENGINE =====
    startQuiz() {
        this.score = 0;
        this.correctAnswers = 0;
        this.wrongAnswers = 0;
        this.userAnswers = [];
        this.currentQuestionIndex = 0;

        this.loadQuestion();
    }

    loadQuestion() {
        if (this.currentQuestionIndex >= this.questions.length) {
            this.endQuiz();
            return;
        }

        const question = this.questions[this.currentQuestionIndex];
        this.currentQuestion = question;

        // Update question display
        this.updateQuestionDisplay(question);

        // Reset timer
        this.startTimer();

        // Enable answer buttons
        this.enableAnswerButtons();
    }

    updateQuestionDisplay(question) {
        const questionText = document.getElementById('questionText');
        const questionNumber = document.getElementById('questionNumber');
        const totalQuestions = document.getElementById('totalQuestions');
        const progressFill = document.getElementById('progressFill');

        if (questionText) questionText.textContent = question.question;
        if (questionNumber) questionNumber.textContent = this.currentQuestionIndex + 1;
        if (totalQuestions) totalQuestions.textContent = this.questions.length;

        // Update progress
        const progress = ((this.currentQuestionIndex + 1) / this.questions.length) * 100;
        if (progressFill) {
            progressFill.style.width = progress + '%';
        }

        // Update answer buttons
        const answers = [question.option_a, question.option_b, question.option_c, question.option_d];
        const answerBtns = document.querySelectorAll('.answer-btn');

        answerBtns.forEach((btn, index) => {
            const answerText = btn.querySelector('.answer-text');
            if (answerText) {
                answerText.textContent = answers[index];
            }
            btn.classList.remove('correct', 'wrong');
            btn.disabled = false;
        });
    }

    selectAnswer(e) {
        const btn = e.currentTarget;
        const answerIndex = Array.from(btn.parentNode.children).indexOf(btn);
        const answerLetter = String.fromCharCode(65 + answerIndex); // A, B, C, D

        // Stop timer
        this.stopTimer();

        // Disable all buttons
        this.disableAnswerButtons();

        // Check answer
        const isCorrect = answerLetter === this.currentQuestion.correct_answer;

        // Store user answer
        this.userAnswers.push({
            question_id: this.currentQuestion.id,
            user_answer: answerLetter,
            correct_answer: this.currentQuestion.correct_answer,
            is_correct: isCorrect
        });

        // Update score
        if (isCorrect) {
            this.correctAnswers++;
            this.score += this.calculateScore();
            btn.classList.add('correct');
            this.playSound('correct');
        } else {
            this.wrongAnswers++;
            btn.classList.add('wrong');
            this.showCorrectAnswer();
            this.playSound('wrong');
        }

        // Update score display
        this.updateScoreDisplay();

        // Next question after delay
        setTimeout(() => {
            this.currentQuestionIndex++;
            this.loadQuestion();
        }, 2000);
    }

    showCorrectAnswer() {
        const correctAnswer = this.currentQuestion.correct_answer;
        const answerIndex = correctAnswer.charCodeAt(0) - 65; // Convert A, B, C, D to 0, 1, 2, 3
        const answerBtns = document.querySelectorAll('.answer-btn');

        if (answerBtns[answerIndex]) {
            answerBtns[answerIndex].classList.add('correct');
        }
    }

    calculateScore() {
        const baseScore = 10;
        const timeBonus = Math.max(0, this.timeLeft * 2);
        const difficultyMultiplier = this.currentQuestion.difficulty === 'hard' ? 2 :
            this.currentQuestion.difficulty === 'medium' ? 1.5 : 1;

        return Math.round(baseScore * difficultyMultiplier + timeBonus);
    }

    // ===== TIMER =====
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

    timeUp() {
        this.stopTimer();
        this.wrongAnswers++;
        this.showCorrectAnswer();
        this.playSound('wrong');

        setTimeout(() => {
            this.currentQuestionIndex++;
            this.loadQuestion();
        }, 2000);
    }

    updateTimerDisplay() {
        const timerText = document.getElementById('timerText');
        const timerProgress = document.getElementById('timerProgress');

        if (timerText) timerText.textContent = this.timeLeft;

        if (timerProgress) {
            const percentage = (this.timeLeft / 30) * 100;
            timerProgress.style.width = percentage + '%';

            // Change color based on time left
            if (this.timeLeft <= 10) {
                timerProgress.style.background = 'linear-gradient(90deg, #DA121A, #ff006e)';
            } else if (this.timeLeft <= 20) {
                timerProgress.style.background = 'linear-gradient(90deg, #FCDD09, #ff006e)';
            } else {
                timerProgress.style.background = 'linear-gradient(90deg, #078930, #00ff88)';
            }
        }
    }

    // ===== QUIZ END =====
    async endQuiz() {
        this.stopTimer();

        const accuracy = this.questions.length > 0 ?
            Math.round((this.correctAnswers / this.questions.length) * 100) : 0;

        // Save score to database
        await this.saveScore(accuracy);

        // Show results
        this.showResults(accuracy);
    }

    async saveScore(accuracy) {
        try {
            const response = await fetch('api/save_score.php', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                },
                body: JSON.stringify({
                    category_id: this.currentCategory,
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
            if (!data.success) {
                console.error('Failed to save score:', data.message);
            }
        } catch (error) {
            console.error('Error saving score:', error);
        }
    }

    showResults(accuracy) {
        // Update result screen elements
        const finalScore = document.getElementById('finalScore');
        const accuracyDisplay = document.getElementById('accuracyDisplay');
        const correctCount = document.getElementById('correctCount');
        const wrongCount = document.getElementById('wrongCount');

        if (finalScore) finalScore.textContent = this.score;
        if (accuracyDisplay) accuracyDisplay.textContent = accuracy + '%';
        if (correctCount) correctCount.textContent = this.correctAnswers;
        if (wrongCount) wrongCount.textContent = this.wrongAnswers;

        // Update achievement badge
        this.updateAchievementBadge(accuracy);

        // Show result screen
        this.showScreen('result');

        // Play appropriate sound
        if (accuracy >= 70) {
            this.playSound('win');
        } else {
            this.playSound('lose');
        }
    }

    updateAchievementBadge(accuracy) {
        const badgeIcon = document.getElementById('badgeIcon');
        const badgeText = document.getElementById('badgeText');

        if (!badgeIcon || !badgeText) return;

        if (accuracy >= 90) {
            badgeIcon.textContent = '🏆';
            badgeText.textContent = 'Quiz Master!';
        } else if (accuracy >= 80) {
            badgeIcon.textContent = '🥇';
            badgeText.textContent = 'Expert!';
        } else if (accuracy >= 70) {
            badgeIcon.textContent = '🥈';
            badgeText.textContent = 'Great Job!';
        } else if (accuracy >= 60) {
            badgeIcon.textContent = '🥉';
            badgeText.textContent = 'Good Effort!';
        } else {
            badgeIcon.textContent = '📚';
            badgeText.textContent = 'Keep Practicing!';
        }
    }

    // ===== LEADERBOARD =====
    async loadLeaderboard() {
        try {
            const response = await fetch('api/leaderboard.php');
            const data = await response.json();

            if (data.success) {
                this.displayLeaderboard(data.leaderboard);
            } else {
                this.showNotification('Failed to load leaderboard', 'error');
            }
        } catch (error) {
            this.showNotification('Error loading leaderboard', 'error');
        }
    }

    displayLeaderboard(leaderboard) {
        const leaderboardBody = document.getElementById('leaderboardBody');
        if (!leaderboardBody) return;

        leaderboardBody.innerHTML = '';

        leaderboard.forEach((entry, index) => {
            const row = document.createElement('div');
            row.className = 'leaderboard-row';

            const rankClass = index < 3 ? `rank-${index + 1}` : 'rank-default';

            row.innerHTML = `
                <div class="rank-badge ${rankClass}">${index + 1}</div>
                <div class="player-name">${entry.username}</div>
                <div class="player-score">${entry.total_score}</div>
                <div class="player-games">${entry.games_played}</div>
                <div class="player-accuracy">${entry.average_accuracy}%</div>
            `;

            leaderboardBody.appendChild(row);
        });
    }

    // ===== USER STATS =====
    async loadUserStats() {
        try {
            const response = await fetch('api/user_stats.php');
            const data = await response.json();

            if (data.success) {
                this.displayUserStats(data.stats);
            }
        } catch (error) {
            console.error('Failed to load user stats:', error);
        }
    }

    displayUserStats(stats) {
        const totalScore = document.getElementById('totalScore');
        const gamesPlayed = document.getElementById('gamesPlayed');
        const bestScore = document.getElementById('bestScore');
        const averageAccuracy = document.getElementById('averageAccuracy');

        if (totalScore) totalScore.textContent = stats.total_score || 0;
        if (gamesPlayed) gamesPlayed.textContent = stats.games_played || 0;
        if (bestScore) bestScore.textContent = stats.best_score || 0;
        if (averageAccuracy) averageAccuracy.textContent = (stats.average_accuracy || 0) + '%';
    }

    // ===== UI HELPERS =====
    enableAnswerButtons() {
        document.querySelectorAll('.answer-btn').forEach(btn => {
            btn.disabled = false;
            btn.classList.remove('correct', 'wrong');
        });
    }

    disableAnswerButtons() {
        document.querySelectorAll('.answer-btn').forEach(btn => {
            btn.disabled = true;
        });
    }

    updateScoreDisplay() {
        const scoreDisplay = document.getElementById('scoreDisplay');
        if (scoreDisplay) {
            scoreDisplay.textContent = this.score;
        }
    }

    updateUserUI(user) {
        const usernameDisplay = document.getElementById('usernameDisplay');
        const loginBtn = document.getElementById('loginBtn');
        const registerBtn = document.getElementById('registerBtn');
        const logoutBtn = document.getElementById('logoutBtn');

        if (usernameDisplay) usernameDisplay.textContent = user.username;
        if (loginBtn) loginBtn.style.display = 'none';
        if (registerBtn) registerBtn.style.display = 'none';
        if (logoutBtn) logoutBtn.style.display = 'block';
    }

    showGuestUI() {
        const usernameDisplay = document.getElementById('usernameDisplay');
        const loginBtn = document.getElementById('loginBtn');
        const registerBtn = document.getElementById('registerBtn');
        const logoutBtn = document.getElementById('logoutBtn');

        if (usernameDisplay) usernameDisplay.textContent = 'Guest';
        if (loginBtn) loginBtn.style.display = 'block';
        if (registerBtn) registerBtn.style.display = 'block';
        if (logoutBtn) logoutBtn.style.display = 'none';
    }

    // ===== NOTIFICATIONS =====
    showNotification(message, type = 'info') {
        const notification = document.createElement('div');
        notification.className = `notification notification-${type}`;
        notification.textContent = message;

        notification.style.cssText = `
            position: fixed;
            top: 20px;
            right: 20px;
            padding: 16px 24px;
            border-radius: 8px;
            color: white;
            font-weight: 600;
            z-index: 10000;
            animation: slideIn 0.3s ease;
            max-width: 300px;
        `;

        switch (type) {
            case 'success':
                notification.style.background = 'linear-gradient(135deg, #078930, #00ff88)';
                break;
            case 'error':
                notification.style.background = 'linear-gradient(135deg, #DA121A, #ff006e)';
                break;
            default:
                notification.style.background = 'linear-gradient(135deg, #D4AF37, #FFD700)';
        }

        document.body.appendChild(notification);

        setTimeout(() => {
            notification.style.animation = 'slideOut 0.3s ease';
            setTimeout(() => notification.remove(), 300);
        }, 3000);
    }

    // ===== KEYBOARD HANDLING =====
    handleKeyboard(e) {
        if (this.currentScreen === 'quiz') {
            const key = parseInt(e.key);
            if (key >= 1 && key <= 4) {
                const answerBtns = document.querySelectorAll('.answer-btn');
                if (answerBtns[key - 1] && !answerBtns[key - 1].disabled) {
                    answerBtns[key - 1].click();
                }
            }
        }
    }

    // ===== SOUND SYSTEM =====
    toggleSound() {
        this.soundEnabled = !this.soundEnabled;
        const soundIcon = document.getElementById('soundIcon');

        if (soundIcon) {
            soundIcon.textContent = this.soundEnabled ? '🔊' : '🔇';
        }

        this.playSound('click');
    }

    playSound(type) {
        if (!this.soundEnabled) return;

        // Create audio context for sound effects
        const audioContext = new (window.AudioContext || window.webkitAudioContext)();
        const oscillator = audioContext.createOscillator();
        const gainNode = audioContext.createGain();

        oscillator.connect(gainNode);
        gainNode.connect(audioContext.destination);

        // Different sounds for different actions
        switch (type) {
            case 'click':
                oscillator.frequency.value = 800;
                gainNode.gain.value = 0.1;
                break;
            case 'select':
                oscillator.frequency.value = 600;
                gainNode.gain.value = 0.1;
                break;
            case 'correct':
                oscillator.frequency.value = 1200;
                gainNode.gain.value = 0.1;
                break;
            case 'wrong':
                oscillator.frequency.value = 300;
                gainNode.gain.value = 0.1;
                break;
            case 'win':
                oscillator.frequency.value = 1500;
                gainNode.gain.value = 0.1;
                break;
            case 'lose':
                oscillator.frequency.value = 200;
                gainNode.gain.value = 0.1;
                break;
            default:
                return;
        }

        oscillator.start();
        oscillator.stop(audioContext.currentTime + 0.2);
    }

    // ===== ANIMATIONS =====
    initializeAnimations() {
        // Add entrance animations
        this.addEntranceAnimations();

        // Add hover effects
        this.addHoverEffects();

        // Add scroll animations
        this.addScrollAnimations();
    }

    addEntranceAnimations() {
        const elements = document.querySelectorAll('.glass-card, .category-card, .btn');

        elements.forEach((element, index) => {
            element.style.opacity = '0';
            element.style.transform = 'translateY(30px)';

            setTimeout(() => {
                element.style.transition = 'all 0.6s ease';
                element.style.opacity = '1';
                element.style.transform = 'translateY(0)';
            }, index * 100);
        });
    }

    addHoverEffects() {
        const cards = document.querySelectorAll('.category-card, .glass-card');

        cards.forEach(card => {
            card.addEventListener('mouseenter', () => {
                this.playSound('hover');
            });
        });
    }

    addScrollAnimations() {
        const observer = new IntersectionObserver((entries) => {
            entries.forEach(entry => {
                if (entry.isIntersecting) {
                    entry.target.classList.add('animate-in');
                }
            });
        });

        document.querySelectorAll('.animate-on-scroll').forEach(element => {
            observer.observe(element);
        });
    }

    // ===== UTILITIES =====
    shuffleArray(array) {
        const shuffled = [...array];
        for (let i = shuffled.length - 1; i > 0; i--) {
            const j = Math.floor(Math.random() * (i + 1));
            [shuffled[i], shuffled[j]] = [shuffled[j], shuffled[i]];
        }
        return shuffled;
    }

    formatTime(seconds) {
        const mins = Math.floor(seconds / 60);
        const secs = seconds % 60;
        return `${mins}:${secs.toString().padStart(2, '0')}`;
    }
}

// ===== GLOBAL INITIALIZATION =====
document.addEventListener('DOMContentLoaded', () => {
    window.ethiopianQuizApp = new EthiopianQuizApp();

    // Add CSS animations
    const style = document.createElement('style');
    style.textContent = `
        @keyframes slideIn {
            from { transform: translateX(100%); opacity: 0; }
            to { transform: translateX(0); opacity: 1; }
        }
        
        @keyframes slideOut {
            from { transform: translateX(0); opacity: 1; }
            to { transform: translateX(100%); opacity: 0; }
        }
        
        .animate-in {
            animation: fadeInUp 0.6s ease forwards;
        }
        
        @keyframes fadeInUp {
            from {
                opacity: 0;
                transform: translateY(30px);
            }
            to {
                opacity: 1;
                transform: translateY(0);
            }
        }
    `;
    document.head.appendChild(style);
});

// ===== UTILITY FUNCTIONS =====
function debounce(func, wait) {
    let timeout;
    return function executedFunction(...args) {
        const later = () => {
            clearTimeout(timeout);
            func(...args);
        };
        clearTimeout(timeout);
        timeout = setTimeout(later, wait);
    };
}

function throttle(func, limit) {
    let inThrottle;
    return function () {
        const args = arguments;
        const context = this;
        if (!inThrottle) {
            func.apply(context, args);
            inThrottle = true;
            setTimeout(() => inThrottle = false, limit);
        }
    };
}
