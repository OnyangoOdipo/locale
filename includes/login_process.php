<?php
session_start();
require_once '../config/database.php';
require_once '../config/paths.php';

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    header('Location: ' . BASE_URL . 'pages/login.php');
    exit();
}

$identifier = trim($_POST['identifier'] ?? '');
$password = $_POST['password'] ?? '';
$remember_me = isset($_POST['remember_me']);

if (empty($identifier) || empty($password)) {
    $_SESSION['error'] = 'All fields are required';
    header('Location: ' . BASE_URL . 'pages/login.php');
    exit();
}

try {
    $db = new Database();
    $conn = $db->getConnection();

    // Check if identifier is email or username
    $sql = "SELECT * FROM users WHERE email = :identifier OR username = :identifier LIMIT 1";
    $stmt = $conn->prepare($sql);
    $stmt->bindParam(':identifier', $identifier);
    $stmt->execute();
    
    $user = $stmt->fetch(PDO::FETCH_ASSOC);

    if ($user && password_verify($password, $user['password'])) {
        // Login successful
        $_SESSION['user_id'] = $user['id'];
        $_SESSION['username'] = $user['username'];
        $_SESSION['profile_picture'] = $user['profile_picture'];

        // Handle remember me
        if ($remember_me) {
            $token = bin2hex(random_bytes(32));
            $expires = date('Y-m-d H:i:s', strtotime('+30 days'));
            
            $stmt = $conn->prepare("INSERT INTO remember_tokens (user_id, token, expires_at) VALUES (?, ?, ?)");
            $stmt->execute([$user['id'], $token, $expires]);
            
            setcookie('remember_token', $token, strtotime('+30 days'), '/', '', true, true);
        }

        // Update last login timestamp
        $stmt = $conn->prepare("UPDATE users SET last_login = CURRENT_TIMESTAMP WHERE id = ?");
        $stmt->execute([$user['id']]);

        header('Location: ' . BASE_URL . 'pages/dashboard.php');
        exit();
    } else {
        $_SESSION['error'] = 'Invalid credentials';
        header('Location: ' . BASE_URL . 'pages/login.php');
        exit();
    }
} catch (PDOException $e) {
    $_SESSION['error'] = 'An error occurred. Please try again later.';
    error_log("Login error: " . $e->getMessage());
    header('Location: ' . BASE_URL . 'pages/login.php');
    exit();
} 