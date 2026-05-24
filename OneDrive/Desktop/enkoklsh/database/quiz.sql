-- Ethiopian Quiz Application Database
-- Database: anakoklish_db

-- Create database
CREATE DATABASE IF NOT EXISTS anakoklish_db;
USE anakoklish_db;

-- Users table
CREATE TABLE users (
    id INT PRIMARY KEY AUTO_INCREMENT,
    username VARCHAR(50) UNIQUE NOT NULL,
    email VARCHAR(100) UNIQUE NOT NULL,
    password VARCHAR(255) NOT NULL,
    full_name VARCHAR(100),
    profile_image VARCHAR(255),
    total_score INT DEFAULT 0,
    games_played INT DEFAULT 0,
    best_score INT DEFAULT 0,
    accuracy DECIMAL(5,2) DEFAULT 0.00,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
);

-- Categories table
CREATE TABLE categories (
    id INT PRIMARY KEY AUTO_INCREMENT,
    name VARCHAR(100) NOT NULL,
    description TEXT,
    icon VARCHAR(50),
    color VARCHAR(20),
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

-- Questions table
CREATE TABLE questions (
    id INT PRIMARY KEY AUTO_INCREMENT,
    category_id INT NOT NULL,
    question TEXT NOT NULL,
    question_am TEXT DEFAULT NULL,
    question_om TEXT DEFAULT NULL,
    option_a VARCHAR(255) NOT NULL,
    option_a_am VARCHAR(255) DEFAULT NULL,
    option_a_om VARCHAR(255) DEFAULT NULL,
    option_b VARCHAR(255) NOT NULL,
    option_b_am VARCHAR(255) DEFAULT NULL,
    option_b_om VARCHAR(255) DEFAULT NULL,
    option_c VARCHAR(255) NOT NULL,
    option_c_am VARCHAR(255) DEFAULT NULL,
    option_c_om VARCHAR(255) DEFAULT NULL,
    option_d VARCHAR(255) NOT NULL,
    option_d_am VARCHAR(255) DEFAULT NULL,
    option_d_om VARCHAR(255) DEFAULT NULL,
    correct_answer ENUM('A', 'B', 'C', 'D') NOT NULL,
    difficulty ENUM('easy', 'medium', 'hard') DEFAULT 'medium',
    points INT DEFAULT 10,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (category_id) REFERENCES categories(id) ON DELETE CASCADE
);

-- Scores table
CREATE TABLE scores (
    id INT PRIMARY KEY AUTO_INCREMENT,
    user_id INT NOT NULL,
    category_id INT NOT NULL,
    score INT NOT NULL,
    correct_answers INT NOT NULL,
    wrong_answers INT NOT NULL,
    total_questions INT NOT NULL,
    accuracy DECIMAL(5,2) NOT NULL,
    time_taken INT NOT NULL,
    completed_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE,
    FOREIGN KEY (category_id) REFERENCES categories(id) ON DELETE CASCADE
);

-- Leaderboard table (aggregated view)
CREATE TABLE leaderboard (
    id INT PRIMARY KEY AUTO_INCREMENT,
    user_id INT NOT NULL,
    total_score INT DEFAULT 0,
    games_played INT DEFAULT 0,
    best_score INT DEFAULT 0,
    average_accuracy DECIMAL(5,2) DEFAULT 0.00,
    rank_position INT,
    last_updated TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE
);

-- Insert sample categories
INSERT INTO categories (name, description, icon, color) VALUES
('Ethiopian History', 'Test your knowledge of Ethiopian emperors, battles, and ancient civilizations', '🏛️', '#D4AF37'),
('General Knowledge', 'World facts, geography, and general trivia questions', '🌍', '#00d4ff'),
('Science', 'Biology, chemistry, physics, and scientific discoveries', '🔬', '#9d4edd'),
('Technology', 'Computing, internet, programming, and tech innovations', '💻', '#00ff88'),
('Ethiopian Culture', 'Traditions, festivals, food, and cultural practices', '🎭', '#ff6b6b'),
('Sports', 'Athletics, football, and international sports knowledge', '⚽', '#4ecdc4'),
('Mathematics', 'Arithmetic, algebra, and logical problem-solving', '🔢', '#ffd93d'),
('Geography', 'Countries, capitals, rivers, mountains, and world geography', '🗺️', '#6bcf7f');

-- Insert sample questions for Ethiopian History
INSERT INTO questions (category_id, question, option_a, option_b, option_c, option_d, correct_answer, difficulty, points) VALUES
(1, 'Who was the last emperor of Ethiopia?', 'Haile Selassie I', 'Menelik II', 'Yohannes IV', 'Tedros II', 'A', 'medium', 10),
(1, 'In which year did the Battle of Adwa take place?', '1885', '1896', '1905', '1911', 'B', 'medium', 10),
(1, 'Which ancient kingdom was centered in northern Ethiopia?', 'Kush', 'Axum', 'Meroe', 'Sheba', 'B', 'easy', 10),
(1, 'Who founded modern Addis Ababa?', 'Menelik II', 'Haile Selassie', 'Yohannes IV', 'Tedros', 'A', 'medium', 10),
(1, 'The Solomonic dynasty claims descent from which biblical figure?', 'David', 'Solomon', 'Moses', 'Abraham', 'B', 'hard', 15),
(1, 'What was the original name of Addis Ababa?', 'Finfinne', 'Gondar', 'Axum', 'Lalibela', 'A', 'hard', 15),
(1, 'Which Ethiopian emperor defeated the Italians at the Battle of Adwa?', 'Menelik II', 'Haile Selassie I', 'Yohannes IV', 'Tewodros II', 'A', 'medium', 10),
(1, 'The rock-hewn churches of Lalibela were built during which dynasty?', 'Zagwe Dynasty', 'Solomonic Dynasty', 'Axumite Kingdom', 'Derg', 'A', 'hard', 15),
(1, 'Who was known as the "Father of Modern Ethiopia"?', 'Emperor Menelik II', 'Emperor Haile Selassie', 'Emperor Yohannes IV', 'Emperor Tewodros II', 'A', 'medium', 10),
(1, 'In which year did Ethiopia defeat Italy at Adwa?', '1896', '1935', '1941', '1974', 'A', 'easy', 10);

-- Insert sample questions for General Knowledge
INSERT INTO questions (category_id, question, option_a, option_b, option_c, option_d, correct_answer, difficulty, points) VALUES
(2, 'What is the capital of France?', 'London', 'Berlin', 'Paris', 'Madrid', 'C', 'easy', 10),
(2, 'Which planet is known as the Red Planet?', 'Venus', 'Mars', 'Jupiter', 'Saturn', 'B', 'easy', 10),
(2, 'Who painted the Mona Lisa?', 'Van Gogh', 'Picasso', 'Da Vinci', 'Rembrandt', 'C', 'easy', 10),
(2, 'What is the largest ocean on Earth?', 'Atlantic', 'Indian', 'Arctic', 'Pacific', 'D', 'easy', 10),
(2, 'How many continents are there?', '5', '6', '7', '8', 'C', 'easy', 10),
(2, 'Who wrote "Romeo and Juliet"?', 'Charles Dickens', 'William Shakespeare', 'Jane Austen', 'Mark Twain', 'B', 'medium', 10),
(2, 'What is the smallest country in the world?', 'Monaco', 'Vatican City', 'San Marino', 'Liechtenstein', 'B', 'medium', 10),
(2, 'Which is the longest river in the world?', 'Amazon', 'Nile', 'Mississippi', 'Yangtze', 'B', 'medium', 10),
(2, 'Who was the first person to walk on the moon?', 'Buzz Aldrin', 'Neil Armstrong', 'Yuri Gagarin', 'John Glenn', 'B', 'easy', 10),
(2, 'What is the capital of Japan?', 'Seoul', 'Beijing', 'Tokyo', 'Bangkok', 'C', 'easy', 10);

-- Insert sample questions for Science
INSERT INTO questions (category_id, question, option_a, option_b, option_c, option_d, correct_answer, difficulty, points) VALUES
(3, 'What is the chemical symbol for gold?', 'Go', 'Gd', 'Au', 'Ag', 'C', 'medium', 10),
(3, 'What is the speed of light?', '299,792 km/s', '199,792 km/s', '399,792 km/s', '499,792 km/s', 'A', 'hard', 15),
(3, 'What is the largest organ in the human body?', 'Heart', 'Brain', 'Liver', 'Skin', 'D', 'easy', 10),
(3, 'How many bones are in the adult human body?', '186', '206', '226', '246', 'B', 'medium', 10),
(3, 'What is the powerhouse of the cell?', 'Nucleus', 'Mitochondria', 'Ribosome', 'Chloroplast', 'B', 'medium', 10),
(3, 'What is the chemical formula for water?', 'H2O', 'CO2', 'O2', 'N2', 'A', 'easy', 10),
(3, 'Which planet is closest to the Sun?', 'Venus', 'Mercury', 'Earth', 'Mars', 'B', 'easy', 10),
(3, 'What is the hardest natural substance on Earth?', 'Gold', 'Iron', 'Diamond', 'Platinum', 'C', 'easy', 10),
(3, 'How many chambers does a human heart have?', '2', '3', '4', '5', 'C', 'easy', 10),
(3, 'What is the study of earthquakes called?', 'Meteorology', 'Seismology', 'Geology', 'Astronomy', 'B', 'medium', 10);

-- Insert sample questions for Technology
INSERT INTO questions (category_id, question, option_a, option_b, option_c, option_d, correct_answer, difficulty, points) VALUES
(4, 'Who founded Microsoft?', 'Steve Jobs', 'Bill Gates', 'Mark Zuckerberg', 'Larry Page', 'B', 'easy', 10),
(4, 'What does "HTTP" stand for?', 'HyperText Transfer Protocol', 'High Tech Transfer Protocol', 'Home Tool Transfer Protocol', 'HyperText Technical Protocol', 'A', 'medium', 10),
(4, 'In what year was the iPhone first released?', '2005', '2006', '2007', '2008', 'C', 'medium', 10),
(4, 'What does "AI" stand for?', 'Automated Intelligence', 'Artificial Intelligence', 'Advanced Intelligence', 'Analytic Intelligence', 'B', 'easy', 10),
(4, 'Which programming language is known as the "language of the web"?', 'Python', 'Java', 'JavaScript', 'C++', 'C', 'easy', 10),
(4, 'Who founded Apple Inc.?', 'Bill Gates', 'Steve Jobs', 'Mark Zuckerberg', 'Elon Musk', 'B', 'easy', 10),
(4, 'What does "Wi-Fi" stand for?', 'Wireless Fidelity', 'Wireless Find', 'Wireless Fix', 'Wireless Field', 'A', 'medium', 10),
(4, 'Which company developed the Android operating system?', 'Apple', 'Microsoft', 'Google', 'Samsung', 'C', 'easy', 10),
(4, 'What is the most popular social media platform?', 'Twitter', 'Instagram', 'Facebook', 'TikTok', 'C', 'medium', 10),
(4, 'What year was Google founded?', '1996', '1998', '2000', '2002', 'B', 'medium', 10);

-- Insert sample questions for Ethiopian Culture
INSERT INTO questions (category_id, question, option_a, option_b, option_c, option_d, correct_answer, difficulty, points) VALUES
(5, 'What is the traditional Ethiopian coffee ceremony called?', 'Buna', 'Injera', 'Doro Wat', 'Shiro', 'A', 'easy', 10),
(5, 'Which is the main staple food in Ethiopia?', 'Rice', 'Injera', 'Pasta', 'Bread', 'B', 'easy', 10),
(5, 'What is the Ethiopian New Year called?', 'Enkutatash', 'Timkat', 'Meskel', 'Fasika', 'A', 'medium', 10),
(5, 'Which Ethiopian festival celebrates the Epiphany?', 'Timkat', 'Meskel', 'Fasika', 'Enkutatash', 'A', 'medium', 10),
(5, 'What is the traditional Ethiopian dress called?', 'Habesha Kemis', 'Kuta', 'Netela', 'Gabi', 'A', 'medium', 10),
(5, 'What is the traditional Ethiopian spicy stew called?', 'Shiro', 'Doro Wat', 'Kitfo', 'Tibs', 'B', 'easy', 10),
(5, 'Which Ethiopian holiday celebrates the finding of the True Cross?', 'Meskel', 'Timkat', 'Enkutatash', 'Fasika', 'A', 'medium', 10),
(5, 'What is the traditional Ethiopian shoulder cloth called?', 'Netela', 'Gabi', 'Kuta', 'Bolina', 'A', 'hard', 15),
(5, 'Which Ethiopian instrument is a one-stringed fiddle?', 'Krar', 'Walta', 'Kebero', 'Masinko', 'D', 'hard', 15),
(5, 'What is the traditional Ethiopian honey wine called?', 'Tej', 'TellA', 'Araki', 'Katikala', 'A', 'medium', 10);

-- Insert sample questions for Sports
INSERT INTO questions (category_id, question, option_a, option_b, option_c, option_d, correct_answer, difficulty, points) VALUES
(6, 'How many players are on a soccer team?', '9', '10', '11', '12', 'C', 'easy', 10),
(6, 'In which sport would you perform a slam dunk?', 'Tennis', 'Basketball', 'Volleyball', 'Baseball', 'B', 'easy', 10),
(6, 'How often are the Olympic Games held?', 'Every 2 years', 'Every 3 years', 'Every 4 years', 'Every 5 years', 'C', 'easy', 10),
(6, 'Which country won the first FIFA World Cup?', 'Brazil', 'Germany', 'Uruguay', 'Argentina', 'C', 'hard', 15),
(6, 'What is the maximum score in ten-pin bowling?', '200', '250', '300', '350', 'C', 'medium', 10),
(6, 'Which Ethiopian runner is famous for winning Olympic gold?', 'Haile Gebrselassie', 'Kenenisa Bekele', 'Mamo Wolde', 'Abebe Bikila', 'D', 'medium', 10),
(6, 'How many sets are there in a standard tennis match?', '3', '4', '5', '6', 'A', 'medium', 10),
(6, 'In which sport would you find a "home run"?', 'Basketball', 'Baseball', 'Football', 'Hockey', 'B', 'easy', 10),
(6, 'What is the distance of a marathon?', '26.2 miles', '20 miles', '30 miles', '15 miles', 'A', 'medium', 10),
(6, 'Which sport is played on ice with a puck?', 'Field Hockey', 'Ice Hockey', 'Roller Hockey', 'Street Hockey', 'B', 'easy', 10);

-- Insert sample questions for Mathematics
INSERT INTO questions (category_id, question, option_a, option_b, option_c, option_d, correct_answer, difficulty, points) VALUES
(7, 'What is 15 × 8?', '100', '110', '120', '130', 'C', 'easy', 10),
(7, 'What is the square root of 144?', '10', '11', '12', '13', 'C', 'easy', 10),
(7, 'What is 25% of 200?', '40', '50', '60', '70', 'B', 'easy', 10),
(7, 'What is the next number in the sequence: 2, 4, 8, 16, ?', '24', '28', '32', '36', 'C', 'easy', 10),
(7, 'What is 7² (7 squared)?', '42', '49', '56', '63', 'B', 'easy', 10),
(7, 'What is the value of π (pi) to two decimal places?', '3.12', '3.14', '3.16', '3.18', 'B', 'easy', 10),
(7, 'What is 100 ÷ 4?', '20', '25', '30', '35', 'B', 'easy', 10),
(7, 'What is the area of a square with side length 5?', '15', '20', '25', '30', 'C', 'easy', 10),
(7, 'What is 3/4 as a decimal?', '0.65', '0.70', '0.75', '0.80', 'C', 'easy', 10),
(7, 'What is the perimeter of a rectangle with length 8 and width 4?', '20', '24', '28', '32', 'B', 'easy', 10);

-- Insert sample questions for Geography
INSERT INTO questions (category_id, question, option_a, option_b, option_c, option_d, correct_answer, difficulty, points) VALUES
(8, 'What is the longest river in the world?', 'Amazon', 'Nile', 'Mississippi', 'Yangtze', 'B', 'easy', 10),
(8, 'Which country has the largest population?', 'India', 'USA', 'China', 'Indonesia', 'C', 'medium', 10),
(8, 'What is the smallest country in the world?', 'Monaco', 'Vatican City', 'San Marino', 'Liechtenstein', 'B', 'easy', 10),
(8, 'Mount Everest is located in which mountain range?', 'Alps', 'Andes', 'Himalayas', 'Rockies', 'C', 'easy', 10),
(8, 'Which desert is the largest in the world?', 'Sahara', 'Arabian', 'Gobi', 'Antarctica', 'D', 'hard', 15),
(8, 'What is the capital of Ethiopia?', 'Gondar', 'Axum', 'Addis Ababa', 'Lalibela', 'C', 'easy', 10),
(8, 'Which continent is Ethiopia located in?', 'Asia', 'Europe', 'Africa', 'South America', 'C', 'easy', 10),
(8, 'What is the largest ocean in the world?', 'Atlantic', 'Indian', 'Arctic', 'Pacific', 'D', 'easy', 10),
(8, 'Which country has the largest land area?', 'Canada', 'USA', 'China', 'Russia', 'D', 'medium', 10),
(8, 'What is the capital of Egypt?', 'Alexandria', 'Giza', 'Cairo', 'Luxor', 'C', 'easy', 10);

-- Create indexes for better performance
CREATE INDEX idx_users_email ON users(email);
CREATE INDEX idx_users_username ON users(username);
CREATE INDEX idx_questions_category ON questions(category_id);
CREATE INDEX idx_scores_user ON scores(user_id);
CREATE INDEX idx_scores_category ON scores(category_id);
CREATE INDEX idx_leaderboard_user ON leaderboard(user_id);
CREATE INDEX idx_leaderboard_score ON leaderboard(total_score DESC);

-- Create a view for leaderboard rankings
CREATE VIEW leaderboard_view AS
SELECT 
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
ORDER BY total_score DESC;
