<?php
function getDialectProgress($conn, $user_id) {
    $stmt = $conn->prepare("
        SELECT 
            d.name as dialect_name,
            d.region,
            COUNT(DISTINCT l.id) as total_lessons,
            COUNT(DISTINCT CASE WHEN up.completed = 1 THEN l.id END) as completed_lessons,
            COALESCE(SUM(up.score), 0) as total_score
        FROM user_dialects ud
        JOIN dialects d ON ud.dialect_id = d.id
        LEFT JOIN lessons l ON d.id = l.dialect_id
        LEFT JOIN user_progress up ON l.id = up.lesson_id AND up.user_id = ud.user_id
        WHERE ud.user_id = ?
        GROUP BY d.id, d.name, d.region
    ");
    $stmt->execute([$user_id]);
    return $stmt->fetchAll(PDO::FETCH_ASSOC);
}

$dialect_progress = getDialectProgress($conn, $_SESSION['user_id']);
?>

<div class="bg-white dark:bg-gray-800 rounded-xl shadow-lg overflow-hidden">
    <div class="p-6">
        <h2 class="text-xl font-semibold text-gray-900 dark:text-white mb-4">Learning Progress</h2>
        <div class="grid gap-4">
            <?php foreach ($dialect_progress as $progress): 
                $progressPercentage = ($progress['completed_lessons'] / max(1, $progress['total_lessons'])) * 100;
            ?>
                <div class="p-4 bg-gray-50 dark:bg-gray-700 rounded-lg">
                    <div class="flex items-center justify-between mb-2">
                        <h3 class="text-sm font-medium text-gray-900 dark:text-white">
                            <?= htmlspecialchars($progress['dialect_name']) ?>
                        </h3>
                        <span class="text-xs text-gray-500 dark:text-gray-400">
                            <?= htmlspecialchars($progress['region']) ?>
                        </span>
                    </div>
                    <div class="mt-2">
                        <div class="w-full bg-gray-200 dark:bg-gray-600 rounded-full h-2.5 mb-2">
                            <div class="bg-indigo-600 dark:bg-indigo-400 h-2.5 rounded-full transition-all duration-500"
                                 style="width: <?= $progressPercentage ?>%"></div>
                        </div>
                        <div class="flex justify-between text-xs text-gray-500 dark:text-gray-400">
                            <span><?= $progress['completed_lessons'] ?>/<?= $progress['total_lessons'] ?> lessons</span>
                            <span><?= number_format($progress['total_score']) ?> points</span>
                        </div>
                    </div>
                </div>
            <?php endforeach; ?>
        </div>
    </div>
</div> 