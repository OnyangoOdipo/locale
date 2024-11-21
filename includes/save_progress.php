<?php
require_once '../config/database.php';
session_start();

if (!isset($_SESSION['user_id'])) {
    echo json_encode(['error' => 'Not authenticated']);
    exit;
}

$data = json_decode(file_get_contents('php://input'), true);
$lesson_id = $data['lesson_id'] ?? null;
$score = $data['score'] ?? 0;

if (!$lesson_id) {
    echo json_encode(['error' => 'No lesson ID provided']);
    exit;
}

$db = new Database();
$conn = $db->getConnection();

try {
    // Save the progress
    $stmt = $conn->prepare("
        INSERT INTO user_progress (user_id, lesson_id, score, completed_at)
        VALUES (?, ?, ?, NOW())
        ON DUPLICATE KEY UPDATE 
        score = GREATEST(score, ?),
        completed_at = NOW()
    ");
    $stmt->execute([$_SESSION['user_id'], $lesson_id, $score, $score]);

    // Get next lesson
    $stmt = $conn->prepare("
        SELECT id 
        FROM lessons 
        WHERE dialect_id = (SELECT dialect_id FROM lessons WHERE id = ?)
        AND id > ?
        ORDER BY id ASC 
        LIMIT 1
    ");
    $stmt->execute([$lesson_id, $lesson_id]);
    $next_lesson = $stmt->fetch(PDO::FETCH_ASSOC);

    echo json_encode([
        'success' => true,
        'score' => $score,
        'next_lesson_id' => $next_lesson['id'] ?? null
    ]);
} catch (Exception $e) {
    echo json_encode(['error' => 'Database error: ' . $e->getMessage()]);
} 