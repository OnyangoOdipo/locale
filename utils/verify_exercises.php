<?php
require_once '../config/database.php';

try {
    $db = new Database();
    $conn = $db->getConnection();
    
    // Check exercises and their lesson assignments
    $stmt = $conn->query("
        SELECT 
            e.id as exercise_id,
            e.lesson_id,
            e.question,
            l.title as lesson_title,
            d.name as dialect_name
        FROM exercises e
        LEFT JOIN lessons l ON e.lesson_id = l.id
        LEFT JOIN dialects d ON l.dialect_id = d.id
        ORDER BY e.lesson_id, e.id
    ");
    
    $exercises = $stmt->fetchAll(PDO::FETCH_ASSOC);
    
    echo "Exercise Assignments:\n\n";
    foreach ($exercises as $exercise) {
        echo "Exercise ID: " . $exercise['exercise_id'] . "\n";
        echo "Lesson ID: " . ($exercise['lesson_id'] ?? 'NULL') . "\n";
        echo "Lesson: " . ($exercise['lesson_title'] ?? 'Not Assigned') . "\n";
        echo "Dialect: " . ($exercise['dialect_name'] ?? 'Not Assigned') . "\n";
        echo "Question: " . $exercise['question'] . "\n";
        echo "----------------------------------------\n";
    }
    
} catch (Exception $e) {
    echo "Error: " . $e->getMessage() . "\n";
} 