<?php
/**
 * Database Configuration for Ethiopian Quiz Application
 * እናቆቅልሽ - Premium Ethiopian Quiz Platform
 */

class Database {
    private $host = "localhost";
    private $db_name = "anakoklish_db";
    private $username = "root";
    private $password = "";
    private $charset = "utf8mb4";
    
    public $conn;
    
    public function getConnection() {
        $this->conn = null;
        
        try {
            $dsn = "mysql:host=" . $this->host . ";dbname=" . $this->db_name . ";charset=" . $this->charset;
            $this->conn = new PDO($dsn, $this->username, $this->password);
            $this->conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
            $this->conn->setAttribute(PDO::ATTR_DEFAULT_FETCH_MODE, PDO::FETCH_ASSOC);
            $this->conn->setAttribute(PDO::ATTR_EMULATE_PREPARES, false);
        } catch(PDOException $exception) {
            echo "Connection error: " . $exception->getMessage();
        }
        
        return $this->conn;
    }
    
    // Helper method to execute queries
    public function executeQuery($sql, $params = []) {
        try {
            $stmt = $this->conn->prepare($sql);
            $stmt->execute($params);
            return $stmt;
        } catch(PDOException $exception) {
            echo "Query error: " . $exception->getMessage();
            return false;
        }
    }
    
    // Helper method to get single record
    public function getSingle($sql, $params = []) {
        $stmt = $this->executeQuery($sql, $params);
        return $stmt ? $stmt->fetch() : false;
    }
    
    // Helper method to get multiple records
    public function getMultiple($sql, $params = []) {
        $stmt = $this->executeQuery($sql, $params);
        return $stmt ? $stmt->fetchAll() : false;
    }
    
    // Helper method to insert record and get last ID
    public function insert($sql, $params = []) {
        try {
            $stmt = $this->conn->prepare($sql);
            $stmt->execute($params);
            return $this->conn->lastInsertId();
        } catch(PDOException $exception) {
            echo "Insert error: " . $exception->getMessage();
            return false;
        }
    }
    
    // Helper method to update/delete records
    public function update($sql, $params = []) {
        $stmt = $this->executeQuery($sql, $params);
        return $stmt ? $stmt->rowCount() : false;
    }
}

// Security functions
function sanitize_input($data) {
    $data = trim($data);
    $data = stripslashes($data);
    $data = htmlspecialchars($data);
    return $data;
}

function hash_password($password) {
    return password_hash($password, PASSWORD_DEFAULT);
}

function verify_password($password, $hash) {
    return password_verify($password, $hash);
}

function hasColumn($db, $table, $column) {
    try {
        $stmt = $db->prepare("SHOW COLUMNS FROM `$table` LIKE ?");
        $stmt->execute([$column]);
        return (bool) $stmt->fetch();
    } catch (PDOException $e) {
        return false;
    }
}

function getLocalizedQuestionSelect($db, $lang) {
    $lang = in_array($lang, ['am', 'om'], true) ? $lang : 'en';
    if ($lang === 'en') {
        return 'question, option_a, option_b, option_c, option_d';
    }

    $fields = ['question', 'option_a', 'option_b', 'option_c', 'option_d'];
    $localizedFields = [];
    $columnExists = true;

    foreach ($fields as $field) {
        if (!hasColumn($db, 'questions', "{$field}_{$lang}")) {
            $columnExists = false;
            break;
        }
    }

    if (!$columnExists) {
        return 'question, option_a, option_b, option_c, option_d';
    }

    foreach ($fields as $field) {
        $localizedFields[] = "COALESCE({$field}_{$lang}, {$field}) AS {$field}";
    }

    return implode(', ', $localizedFields);
}

// Session management
function start_secure_session() {
    $session_name = 'anakoklish_session';
    $secure = true;
    $httponly = true;
    
    ini_set('session.use_only_cookies', 1);
    $cookieParams = session_get_cookie_params();
    session_set_cookie_params(
        $cookieParams["lifetime"],
        $cookieParams["path"],
        $cookieParams["domain"],
        $secure,
        $httponly
    );
    session_name($session_name);
    session_start();
    session_regenerate_id(true);
}

// Check if user is logged in
function is_logged_in() {
    return isset($_SESSION['user_id']) && !empty($_SESSION['user_id']);
}

// Get current user ID
function get_current_user_id() {
    return is_logged_in() ? $_SESSION['user_id'] : null;
}

// Redirect function
function redirect($url) {
    header("Location: $url");
    exit();
}

// Initialize database
$database = new Database();
$db = $database->getConnection();
?>
