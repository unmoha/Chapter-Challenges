<?php
session_start();
require_once '../config/db.php';

// Get POST data
$username = sanitize_input($_POST['username'] ?? '');
$password = $_POST['password'] ?? '';

if (empty($username) || empty($password)) {
    header('Content-Type: application/json');
    echo json_encode(['success' => false, 'message' => 'Please fill in all fields']);
    exit();
}

try {
    // Check user credentials
    $query = "SELECT id, username, email, password, full_name FROM users WHERE username = ? OR email = ?";
    $stmt = $db->prepare($query);
    $stmt->execute([$username, $username]);
    $user = $stmt->fetch(PDO::FETCH_ASSOC);
    
    if ($user && verify_password($password, $user['password'])) {
        // Login successful - set session
        $_SESSION['user_id'] = $user['id'];
        $_SESSION['username'] = $user['username'];
        $_SESSION['email'] = $user['email'];
        $_SESSION['full_name'] = $user['full_name'];
        
        header('Content-Type: application/json');
        echo json_encode([
            'success' => true, 
            'message' => 'Login successful',
            'user' => [
                'id' => $user['id'],
                'username' => $user['username'],
                'email' => $user['email'],
                'full_name' => $user['full_name']
            ]
        ]);
    } else {
        header('Content-Type: application/json');
        echo json_encode(['success' => false, 'message' => 'Invalid username or password']);
    }
    
} catch(PDOException $e) {
    header('Content-Type: application/json');
    echo json_encode(['success' => false, 'message' => 'Login failed. Please try again.']);
}
?>
