<?php
function getAchievements($conn, $user_id) {
    // Get user's current streak
    $stmt = $conn->prepare("
        SELECT 
            COUNT(DISTINCT DATE(completed_at)) as current_streak
        FROM user_progress
        WHERE user_id = ?
            AND completed = 1
            AND completed_at >= DATE_SUB(CURRENT_DATE, INTERVAL 30 DAY)
            AND completed_at <= CURRENT_DATE
        GROUP BY user_id
    ");
    $stmt->execute([$user_id]);
    $result = $stmt->fetch(PDO::FETCH_ASSOC);
    $streak = $result ? $result['current_streak'] : 0;

    // Get vocabulary count
    $stmt = $conn->prepare("
        SELECT COUNT(*) as word_count
        FROM user_progress up
        WHERE user_id = ? AND completed = 1
    ");
    $stmt->execute([$user_id]);
    $vocab = $stmt->fetch(PDO::FETCH_ASSOC);
    $wordCount = $vocab ? $vocab['word_count'] : 0;

    return [
        [
            'title' => 'First Lesson',
            'description' => 'Complete your first lesson',
            'icon' => '🎯',
            'progress' => $wordCount > 0 ? 100 : 0,
            'completed' => $wordCount > 0
        ],
        [
            'title' => 'Streak Master',
            'description' => 'Maintain a 7-day learning streak',
            'icon' => '🔥',
            'progress' => min(($streak / 7) * 100, 100),
            'completed' => $streak >= 7
        ],
        [
            'title' => 'Vocabulary Builder',
            'description' => 'Learn 50 new words',
            'icon' => '📚',
            'progress' => min(($wordCount / 50) * 100, 100),
            'completed' => $wordCount >= 50
        ],
        [
            'title' => 'Dedication',
            'description' => 'Complete 10 lessons',
            'icon' => '⭐',
            'progress' => min(($wordCount / 10) * 100, 100),
            'completed' => $wordCount >= 10
        ]
    ];
}

$achievements = getAchievements($conn, $_SESSION['user_id']);
?>

<div class="bg-white dark:bg-gray-800 rounded-xl shadow-lg overflow-hidden">
    <div class="p-6">
        <h2 class="text-xl font-semibold text-gray-900 dark:text-white mb-4">Achievements</h2>
        <div class="grid gap-4">
            <?php foreach ($achievements as $achievement): ?>
                <div class="p-4 bg-gray-50 dark:bg-gray-700 rounded-lg">
                    <div class="flex items-center space-x-4">
                        <div class="flex-shrink-0 w-12 h-12 bg-indigo-100 dark:bg-indigo-900 rounded-full flex items-center justify-center text-2xl">
                            <?= $achievement['icon'] ?>
                        </div>
                        <div class="flex-1 min-w-0">
                            <p class="text-sm font-medium text-gray-900 dark:text-white">
                                <?= htmlspecialchars($achievement['title']) ?>
                            </p>
                            <p class="text-xs text-gray-500 dark:text-gray-400">
                                <?= htmlspecialchars($achievement['description']) ?>
                            </p>
                            <div class="mt-2 w-full bg-gray-200 dark:bg-gray-600 rounded-full h-2">
                                <div class="bg-indigo-600 dark:bg-indigo-400 h-2 rounded-full transition-all duration-500"
                                     style="width: <?= $achievement['progress'] ?>%"></div>
                            </div>
                        </div>
                        <?php if ($achievement['completed']): ?>
                            <div class="flex-shrink-0">
                                <svg class="w-6 h-6 text-green-500 dark:text-green-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                                </svg>
                            </div>
                        <?php endif; ?>
                    </div>
                </div>
            <?php endforeach; ?>
        </div>
    </div>
</div> 