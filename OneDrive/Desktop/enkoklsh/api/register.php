<?php
session_start();
require_once '../config/db.php';

// Get POST data
$username = sanitize_input($_POST['username'] ?? '');
$email = sanitize_input($_POST['email'] ?? '');
$password = $_POST['password'] ?? '';
$confirm_password = $_POST['confirm_password'] ?? '';
$full_name = sanitize_input($_POST['full_name'] ?? '');

// Validation
if (empty($username) || empty($email) || empty($password) || empty($confirm_password)) {
    header('Content-Type: application/json');
    echo json_encode(['success' => false, 'message' => 'Please fill in all required fields']);
    exit();
}

if (strlen($username) < 3) {
    header('Content-Type: application/json');
    echo json_encode(['success' => false, 'message' => 'Username must be at least 3 characters long']);
    exit();
}

if (strlen($password) < 6) {
    header('Content-Type: application/json');
    echo json_encode(['success' => false, 'message' => 'Password must be at least 6 characters long']);
    exit();
}

if ($password !== $confirm_password) {
    header('Content-Type: application/json');
    echo json_encode(['success' => false, 'message' => 'Passwords do not match']);
    exit();
}

if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
    header('Content-Type: application/json');
    echo json_encode(['success' => false, 'message' => 'Please enter a valid email address']);
    exit();
}

try {
    // Check if username or email already exists
    $check_query = "SELECT id FROM users WHERE username = ? OR email = ?";
    $check_stmt = $db->prepare($check_query);
    $check_stmt->execute([$username, $email]);
    
    if ($check_stmt->fetch()) {
        header('Content-Type: application/json');
        echo json_encode(['success' => false, 'message' => 'Username or email already exists']);
        exit();
    }
    
    // Create new user
    $hashed_password = hash_password($password);
    $insert_query = "INSERT INTO users (username, email, password, full_name, created_at) VALUES (?, ?, ?, ?, NOW())";
    $insert_stmt = $db->prepare($insert_query);
    
    if ($insert_stmt->execute([$username, $email, $hashed_password, $full_name])) {
        header('Content-Type: application/json');
        echo json_encode(['success' => true, 'message' => 'Registration successful! Please login to continue.']);
    } else {
        header('Content-Type: application/json');
        echo json_encode(['success' => false, 'message' => 'Registration failed. Please try again.']);
    }
    
} catch(PDOException $e) {
    header('Content-Type: application/json');
    echo json_encode(['success' => false, 'message' => 'Registration failed. Please try again.']);
}
?>
