<?php
function checkAnswer($exercise, $userAnswer) {
    switch ($exercise['exercise_type']) {
        case 'multiple_choice':
            return strtolower(trim($userAnswer)) === strtolower(trim($exercise['correct_answer']));
            
        case 'typing':
            // Allow for minor typos using levenshtein distance
            $distance = levenshtein(
                strtolower(trim($userAnswer)), 
                strtolower(trim($exercise['correct_answer']))
            );
            return $distance <= 2; // Allow up to 2 character differences
            
        case 'matching':
            $correctPairs = json_decode($exercise['correct_answer'], true);
            $userPairs = json_decode($userAnswer, true);
            return $correctPairs == $userPairs;
            
        default:
            return false;
    }
}

function calculatePoints($exercise, $attempts) {
    $basePoints = $exercise['points'];
    // Reduce points based on attempts
    return max($basePoints - ($attempts - 1) * 2, 1);
}

function updateUserProgress($conn, $userId, $lessonId, $exerciseId, $points) {
    $stmt = $conn->prepare("
        INSERT INTO user_progress (user_id, lesson_id, completed, score)
        VALUES (?, ?, TRUE, ?)
        ON DUPLICATE KEY UPDATE 
        score = GREATEST(score, ?),
        completed = TRUE
    ");
    return $stmt->execute([$userId, $lessonId, $points, $points]);
} 