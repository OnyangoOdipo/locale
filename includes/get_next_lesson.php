<?php
require_once '../config/database.php';
session_start();

if (!isset($_SESSION['user_id'])) {
    echo json_encode(['error' => 'Not authenticated']);
    exit;
}

$current_lesson_id = $_GET['current_lesson_id'] ?? null;

if (!$current_lesson_id) {
    echo json_encode(['error' => 'No lesson ID provided']);
    exit;
}

$db = new Database();
$conn = $db->getConnection();

try {
    // Get current lesson's dialect_id
    $stmt = $conn->prepare("SELECT dialect_id FROM lessons WHERE id = ?");
    $stmt->execute([$current_lesson_id]);
    $current_lesson = $stmt->fetch(PDO::FETCH_ASSOC);

    if (!$current_lesson) {
        echo json_encode(['error' => 'Lesson not found']);
        exit;
    }

    // Get next lesson in same dialect
    $stmt = $conn->prepare("
        SELECT id 
        FROM lessons 
        WHERE dialect_id = ? 
        AND id > ? 
        ORDER BY id ASC 
        LIMIT 1
    ");
    $stmt->execute([$current_lesson['dialect_id'], $current_lesson_id]);
    $next_lesson = $stmt->fetch(PDO::FETCH_ASSOC);

    echo json_encode([
        'success' => true,
        'next_lesson_id' => $next_lesson['id'] ?? null
    ]);
} catch (Exception $e) {
    echo json_encode(['error' => $e->getMessage()]);
} 