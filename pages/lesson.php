<?php
require_once '../config/database.php';
session_start();

if (!isset($_SESSION['user_id'])) {
    header('Location: login.php');
    exit;
}

$lesson_id = $_GET['id'] ?? null;
if (!$lesson_id) {
    header('Location: lessons.php');
    exit;
}

$db = new Database();
$conn = $db->getConnection();

// Get current lesson details
$stmt = $conn->prepare("
    SELECT l.*, d.name as dialect_name, d.id as dialect_id
    FROM lessons l
    JOIN dialects d ON l.dialect_id = d.id
    WHERE l.id = ?
");
$stmt->execute([$lesson_id]);
$lesson = $stmt->fetch(PDO::FETCH_ASSOC);

if (!$lesson) {
    header('Location: lessons.php');
    exit;
}

// Add debug output right after fetching the lesson
//var_dump($lesson);  // Check what we get from the database

// Parse lesson content with error checking
$content = json_decode($lesson['content'], true);
if (json_last_error() !== JSON_ERROR_NONE) {
    echo "JSON Error: " . json_last_error_msg();
    var_dump($lesson['content']); // See raw content
}

// Add this right before the HTML starts
//var_dump($content);  // See what we got after parsing

// Get previous lesson in same dialect
$stmt = $conn->prepare("
    SELECT id, title 
    FROM lessons 
    WHERE dialect_id = ? 
    AND id < ? 
    ORDER BY id DESC 
    LIMIT 1
");
$stmt->execute([$lesson['dialect_id'], $lesson_id]);
$previousLesson = $stmt->fetch(PDO::FETCH_ASSOC);

// Get next lesson in same dialect
$stmt = $conn->prepare("
    SELECT id, title 
    FROM lessons 
    WHERE dialect_id = ? 
    AND id > ? 
    ORDER BY id ASC 
    LIMIT 1
");
$stmt->execute([$lesson['dialect_id'], $lesson_id]);
$nextLesson = $stmt->fetch(PDO::FETCH_ASSOC);

// Debug information
error_log("Current Lesson ID: " . $lesson_id);
error_log("Dialect ID: " . $lesson['dialect_id']);
error_log("Next Lesson: " . print_r($nextLesson, true));

// Fetch exercises for this lesson
$stmt = $conn->prepare("
    SELECT e.*, et.name as exercise_type 
    FROM exercises e
    JOIN exercise_types et ON e.exercise_type_id = et.id
    WHERE e.lesson_id = ?
    ORDER BY e.id
");
$stmt->execute([$lesson_id]);
$exercises = $stmt->fetchAll(PDO::FETCH_ASSOC);
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= htmlspecialchars($lesson['title']) ?> - Jifunzeke</title>
    <link href="https://cdn.jsdelivr.net/npm/tailwindcss@2.2.19/dist/tailwind.min.css" rel="stylesheet">
</head>
<body class="bg-gray-50 min-h-screen">
    <!-- Header -->
    <div class="bg-white shadow-sm">
        <div class="max-w-7xl mx-auto px-4 py-4">
            <div class="flex justify-between items-center">
                <div class="flex items-center space-x-4">
                    <a href="lessons.php" class="text-gray-500 hover:text-gray-700">
                        <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7" />
                        </svg>
                    </a>
                    <div>
                        <h1 class="text-xl font-bold text-gray-900"><?= htmlspecialchars($lesson['title']) ?></h1>
                        <div class="flex items-center space-x-2">
                            <p class="text-sm text-gray-600">Learning <?= htmlspecialchars($lesson['dialect_name']) ?></p>
                            <span class="text-sm px-2 py-1 bg-indigo-100 text-indigo-800 rounded-full">
                                <?= ucfirst($lesson['difficulty_level']) ?>
                            </span>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Main Content -->
    <div class="max-w-3xl mx-auto px-4 py-8">
        <!-- Introduction -->
        <?php if (isset($content['introduction'])): ?>
            <div class="bg-white rounded-lg shadow-sm p-6 mb-6">
                <h2 class="text-xl font-semibold text-indigo-600 mb-4">Introduction</h2>
                <p class="text-gray-700 text-lg"><?= htmlspecialchars($content['introduction']) ?></p>
            </div>
        <?php endif; ?>

        <!-- Lesson Type 1: Alphabet and Pronunciation -->
        <?php if (isset($content['alphabet'])): ?>
            <div class="bg-white rounded-lg shadow-sm p-6 mb-6">
                <h2 class="text-xl font-semibold text-indigo-600 mb-4">Alphabet & Pronunciation</h2>
                
                <?php if (isset($content['alphabet']['special_characters'])): ?>
                    <div class="mb-6">
                        <h3 class="font-medium text-gray-900 mb-3">Special Characters</h3>
                        <div class="grid gap-3">
                            <?php foreach ($content['alphabet']['special_characters'] as $char => $pronunciation): ?>
                                <div class="flex items-center p-4 bg-gray-50 rounded-lg">
                                    <div class="w-16 h-16 bg-indigo-100 rounded-full flex items-center justify-center mr-4">
                                        <span class="text-2xl font-bold text-indigo-600"><?= htmlspecialchars($char) ?></span>
                                    </div>
                                    <p class="text-gray-700"><?= htmlspecialchars($pronunciation) ?></p>
                                </div>
                            <?php endforeach; ?>
                        </div>
                    </div>
                <?php endif; ?>

                <?php if (isset($content['alphabet']['vowels'])): ?>
                    <div>
                        <h3 class="font-medium text-gray-900 mb-3">Vowels</h3>
                        <div class="grid gap-3">
                            <?php foreach ($content['alphabet']['vowels'] as $vowel => $pronunciation): ?>
                                <div class="flex items-center p-4 bg-gray-50 rounded-lg">
                                    <div class="w-16 h-16 bg-indigo-100 rounded-full flex items-center justify-center mr-4">
                                        <span class="text-2xl font-bold text-indigo-600"><?= htmlspecialchars($vowel) ?></span>
                                    </div>
                                    <p class="text-gray-700"><?= htmlspecialchars($pronunciation) ?></p>
                                </div>
                            <?php endforeach; ?>
                        </div>
                    </div>
                <?php endif; ?>
            </div>
        <?php endif; ?>

        <!-- Lesson Type 2: Greetings and Basic Phrases -->
        <?php if (isset($content['greetings'])): ?>
            <div class="bg-white rounded-lg shadow-sm p-6 mb-6">
                <h2 class="text-xl font-semibold text-indigo-600 mb-4">Greetings</h2>
                <div class="space-y-4">
                    <?php foreach ($content['greetings'] as $timeOfDay => $details): ?>
                        <div class="bg-gray-50 p-4 rounded-lg">
                            <h3 class="font-medium text-gray-900 mb-2"><?= ucfirst($timeOfDay) ?></h3>
                            <div class="space-y-2">
                                <p class="text-lg font-medium text-indigo-600"><?= htmlspecialchars($details['phrase']) ?></p>
                                <p class="text-gray-600"><?= htmlspecialchars($details['meaning']) ?></p>
                                <?php if (isset($details['usage'])): ?>
                                    <p class="text-sm text-gray-500 italic"><?= htmlspecialchars($details['usage']) ?></p>
                                <?php endif; ?>
                            </div>
                        </div>
                    <?php endforeach; ?>
                </div>
            </div>
        <?php endif; ?>

        <!-- Lesson Type 3: Numbers and Counting -->
        <?php if (isset($content['numbers'])): ?>
            <div class="bg-white rounded-lg shadow-sm p-6 mb-6">
                <h2 class="text-xl font-semibold text-indigo-600 mb-4">Numbers</h2>
                
                <?php if (isset($content['numbers']['basic'])): ?>
                    <div class="mb-6">
                        <h3 class="font-medium text-gray-900 mb-3">Basic Numbers (1-10)</h3>
                        <div class="grid grid-cols-2 gap-3">
                            <?php foreach ($content['numbers']['basic'] as $number => $word): ?>
                                <div class="flex justify-between p-3 bg-gray-50 rounded-lg">
                                    <span class="font-medium text-gray-900"><?= htmlspecialchars($number) ?></span>
                                    <span class="text-indigo-600"><?= htmlspecialchars($word) ?></span>
                                </div>
                            <?php endforeach; ?>
                        </div>
                    </div>
                <?php endif; ?>

                <?php if (isset($content['numbers']['teens'])): ?>
                    <div class="mb-6">
                        <h3 class="font-medium text-gray-900 mb-3">Teen Numbers (11-19)</h3>
                        <div class="grid grid-cols-2 gap-3">
                            <?php foreach ($content['numbers']['teens'] as $number => $word): ?>
                                <div class="flex justify-between p-3 bg-gray-50 rounded-lg">
                                    <span class="font-medium text-gray-900"><?= htmlspecialchars($number) ?></span>
                                    <span class="text-indigo-600"><?= htmlspecialchars($word) ?></span>
                                </div>
                            <?php endforeach; ?>
                        </div>
                    </div>
                <?php endif; ?>

                <?php if (isset($content['numbers']['tens'])): ?>
                    <div>
                        <h3 class="font-medium text-gray-900 mb-3">Tens (20-50)</h3>
                        <div class="grid grid-cols-2 gap-3">
                            <?php foreach ($content['numbers']['tens'] as $number => $word): ?>
                                <div class="flex justify-between p-3 bg-gray-50 rounded-lg">
                                    <span class="font-medium text-gray-900"><?= htmlspecialchars($number) ?></span>
                                    <span class="text-indigo-600"><?= htmlspecialchars($word) ?></span>
                                </div>
                            <?php endforeach; ?>
                        </div>
                    </div>
                <?php endif; ?>
            </div>
        <?php endif; ?>

        <!-- Lesson Type 4: Family Members -->
        <?php if (isset($content['family_terms'])): ?>
            <div class="bg-white rounded-lg shadow-sm p-6 mb-6">
                <h2 class="text-xl font-semibold text-indigo-600 mb-4">Family Members</h2>
                
                <?php if (isset($content['family_terms']['immediate_family'])): ?>
                    <div class="mb-6">
                        <h3 class="font-medium text-gray-900 mb-3">Immediate Family</h3>
                        <div class="space-y-3">
                            <?php foreach ($content['family_terms']['immediate_family'] as $relation => $details): ?>
                                <div class="bg-gray-50 p-4 rounded-lg">
                                    <div class="flex justify-between items-center mb-2">
                                        <span class="font-medium text-gray-900"><?= ucfirst($relation) ?></span>
                                        <span class="text-indigo-600"><?= htmlspecialchars($details['word']) ?></span>
                                    </div>
                                    <?php if (isset($details['example'])): ?>
                                        <p class="text-sm text-gray-600"><?= htmlspecialchars($details['example']) ?></p>
                                    <?php endif; ?>
                                </div>
                            <?php endforeach; ?>
                        </div>
                    </div>
                <?php endif; ?>

                <?php if (isset($content['family_terms']['extended_family'])): ?>
                    <div>
                        <h3 class="font-medium text-gray-900 mb-3">Extended Family</h3>
                        <div class="grid grid-cols-2 gap-3">
                            <?php foreach ($content['family_terms']['extended_family'] as $relation => $word): ?>
                                <div class="flex justify-between p-3 bg-gray-50 rounded-lg">
                                    <span class="font-medium text-gray-900"><?= ucfirst($relation) ?></span>
                                    <span class="text-indigo-600"><?= htmlspecialchars($word) ?></span>
                                </div>
                            <?php endforeach; ?>
                        </div>
                    </div>
                <?php endif; ?>
            </div>
        <?php endif; ?>

        <!-- Lesson Type 5: Advanced Conversation -->
        <?php if (isset($content['topics'])): ?>
            <div class="bg-white rounded-lg shadow-sm p-6 mb-6">
                <h2 class="text-xl font-semibold text-indigo-600 mb-4">Advanced Topics</h2>
                
                <?php if (isset($content['topics']['proverbs'])): ?>
                    <div class="mb-6">
                        <h3 class="font-medium text-gray-900 mb-3">Proverbs</h3>
                        <div class="space-y-4">
                            <?php foreach ($content['topics']['proverbs'] as $proverb => $details): ?>
                                <div class="bg-gray-50 p-4 rounded-lg">
                                    <p class="text-lg font-medium text-indigo-600 mb-2"><?= htmlspecialchars($proverb) ?></p>
                                    <p class="text-gray-600 mb-2"><?= htmlspecialchars($details['literal']) ?></p>
                                    <p class="text-gray-700 mb-2"><?= htmlspecialchars($details['meaning']) ?></p>
                                    <p class="text-sm text-gray-500 italic"><?= htmlspecialchars($details['usage']) ?></p>
                                </div>
                            <?php endforeach; ?>
                        </div>
                    </div>
                <?php endif; ?>

                <?php if (isset($content['topics']['cultural_practices'])): ?>
                    <div>
                        <h3 class="font-medium text-gray-900 mb-3">Cultural Practices</h3>
                        <div class="space-y-4">
                            <?php foreach ($content['topics']['cultural_practices'] as $category => $practices): ?>
                                <div class="bg-gray-50 p-4 rounded-lg">
                                    <h4 class="font-medium text-gray-900 mb-2"><?= ucfirst($category) ?></h4>
                                    <div class="space-y-2">
                                        <?php foreach ($practices as $title => $description): ?>
                                            <div class="p-3 bg-white rounded">
                                                <p class="font-medium text-indigo-600 mb-1"><?= ucfirst($title) ?></p>
                                                <p class="text-gray-600"><?= htmlspecialchars($description) ?></p>
                                            </div>
                                        <?php endforeach; ?>
                                    </div>
                                </div>
                            <?php endforeach; ?>
                        </div>
                    </div>
                <?php endif; ?>
            </div>
        <?php endif; ?>

        <!-- Practice Exercises Section -->
        <?php 
        if (!empty($exercises)) {
            require_once '../components/exercises-section.php';
            echo renderExercises($exercises);
        }
        ?>

        <!-- Navigation Buttons -->
        <div class="flex justify-between items-center mt-8">
            <?php if ($previousLesson): ?>
                <a href="lesson.php?id=<?= $previousLesson['id'] ?>" 
                   class="px-6 py-3 bg-gray-200 text-gray-700 rounded-lg hover:bg-gray-300 transition-colors">
                    ← Previous Lesson
                </a>
            <?php else: ?>
                <div></div>
            <?php endif; ?>

            <?php if ($nextLesson): ?>
                <a href="lesson.php?id=<?= $nextLesson['id'] ?>" 
                   class="px-6 py-3 bg-indigo-600 text-white rounded-lg hover:bg-indigo-700 transition-colors">
                    Next Lesson →
                </a>
            <?php else: ?>
                <a href="lessons.php" 
                   class="px-6 py-3 bg-green-600 text-white rounded-lg hover:bg-green-700 transition-colors">
                    Complete! Back to Lessons
                </a>
            <?php endif; ?>
        </div>
    </div>
</body>
</html>
