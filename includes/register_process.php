<?php
session_start();
require_once '../config/database.php';
require_once '../config/paths.php';

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    header('Location: ' . BASE_URL . 'pages/register.php');
    exit();
}

try {
    $db = new Database();
    $conn = $db->getConnection();

    // Start transaction
    $conn->beginTransaction();

    // Basic validation
    $username = trim($_POST['username'] ?? '');
    $email = trim($_POST['email'] ?? '');
    $password = $_POST['password'] ?? '';
    $confirm_password = $_POST['confirm_password'] ?? '';
    $dialects = $_POST['dialects'] ?? []; // Changed to get dialects array
    $motivation = $_POST['motivation'] ?? '';
    $daily_goal = $_POST['daily_goal'] ?? 20;

    // Validate dialect selection
    if (empty($dialects)) {
        throw new Exception('Please select at least one dialect to learn');
    }

    // Verify dialects exist
    $dialect_ids = implode(',', array_map('intval', $dialects));
    $stmt = $conn->prepare("SELECT id FROM dialects WHERE id IN ($dialect_ids)");
    $stmt->execute();
    if ($stmt->rowCount() !== count($dialects)) {
        throw new Exception('Invalid dialect selected');
    }

    // Insert user
    $stmt = $conn->prepare("
        INSERT INTO users (username, email, password, profile_picture, created_at) 
        VALUES (?, ?, ?, ?, CURRENT_TIMESTAMP)
    ");
    $hashed_password = password_hash($password, PASSWORD_DEFAULT);
    $stmt->execute([$username, $email, $hashed_password, $profile_picture]);
    
    // Get the new user's ID
    $user_id = $conn->lastInsertId();

    // Insert user's dialect preferences (now handling multiple dialects)
    $stmt = $conn->prepare("
        INSERT INTO user_dialects (user_id, dialect_id, created_at) 
        VALUES (?, ?, CURRENT_TIMESTAMP)
    ");
    foreach ($dialects as $dialect_id) {
        $stmt->execute([$user_id, $dialect_id]);
    }

    // Insert user preferences
    $stmt = $conn->prepare("
        INSERT INTO user_preferences (user_id, daily_goal, motivation) 
        VALUES (?, ?, ?)
    ");
    $stmt->execute([$user_id, $daily_goal, $motivation]);

    // Commit transaction
    $conn->commit();

    // Set session variables
    $_SESSION['user_id'] = $user_id;
    $_SESSION['username'] = $username;
    $_SESSION['profile_picture'] = $profile_picture;

    // Redirect to dashboard
    header('Location: ' . BASE_URL . 'pages/dashboard.php');
    exit();

} catch (Exception $e) {
    // Rollback transaction on error
    if ($conn->inTransaction()) {
        $conn->rollBack();
    }

    $_SESSION['error'] = $e->getMessage();
    header('Location: ' . BASE_URL . 'pages/register.php');
    exit();
}

function handleProfilePictureUpload($file) {
    $allowed_types = ['image/jpeg', 'image/png', 'image/gif'];
    $max_size = 5 * 1024 * 1024; // 5MB

    if (!in_array($file['type'], $allowed_types)) {
        throw new Exception('Invalid file type. Please upload a JPEG, PNG, or GIF image.');
    }

    if ($file['size'] > $max_size) {
        throw new Exception('File is too large. Maximum size is 5MB.');
    }

    $upload_dir = dirname(__DIR__) . '/uploads/profiles/';
    if (!is_dir($upload_dir)) {
        mkdir($upload_dir, 0777, true);
    }

    $filename = uniqid() . '_' . basename($file['name']);
    $filepath = $upload_dir . $filename;

    if (!move_uploaded_file($file['tmp_name'], $filepath)) {
        throw new Exception('Failed to upload file.');
    }

    return 'uploads/profiles/' . $filename;
}
?> 