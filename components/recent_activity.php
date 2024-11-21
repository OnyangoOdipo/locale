<?php
require_once __DIR__ . '/../utils/time_functions.php';

function getRecentActivity($conn, $user_id) {
    // First, ensure we have valid dates
    $stmt = $conn->prepare("
        UPDATE user_progress 
        SET completed_at = CURRENT_TIMESTAMP 
        WHERE completed_at IS NULL 
        AND user_id = ?
    ");
    $stmt->execute([$user_id]);

    // Then get the activities
    $stmt = $conn->prepare("
        SELECT 
            up.*, 
            l.title as lesson_title, 
            d.name as dialect_name,
            up.completed_at,
            COALESCE(up.score, 0) as score,
            up.completed
        FROM user_progress up
        JOIN lessons l ON up.lesson_id = l.id
        JOIN dialects d ON l.dialect_id = d.id
        WHERE up.user_id = ?
        AND up.completed_at >= DATE_SUB(CURRENT_DATE, INTERVAL 30 DAY)
        ORDER BY up.completed_at DESC
        LIMIT 5
    ");
    $stmt->execute([$user_id]);
    return $stmt->fetchAll(PDO::FETCH_ASSOC);
}

$recent_activities = getRecentActivity($conn, $_SESSION['user_id']);

// Add this after getting recent activities
if (empty($recent_activities)) {
    error_log("No recent activities found for user " . $_SESSION['user_id']);
} else {
    error_log("Found " . count($recent_activities) . " activities");
    foreach ($recent_activities as $activity) {
        error_log(print_r([
            'lesson' => $activity['lesson_title'],
            'completed_at' => $activity['completed_at'],
            'score' => $activity['score']
        ], true));
    }
}
?>

<div class="bg-white dark:bg-gray-800 rounded-xl shadow-lg overflow-hidden">
    <div class="p-6">
        <h2 class="text-xl font-semibold text-gray-900 dark:text-white mb-4">Recent Activity</h2>
        <?php if (!empty($recent_activities)): ?>
            <div class="space-y-4">
                <?php foreach ($recent_activities as $activity): ?>
                    <div class="flex items-center space-x-4 p-3 bg-gray-50 dark:bg-gray-700 rounded-lg hover:bg-gray-100 dark:hover:bg-gray-600 transition-colors">
                        <!-- Activity Icon -->
                        <div class="flex-shrink-0">
                            <?php if ($activity['completed']): ?>
                                <div class="w-10 h-10 bg-green-100 dark:bg-green-900 rounded-full flex items-center justify-center">
                                    <svg class="w-6 h-6 text-green-600 dark:text-green-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path>
                                    </svg>
                                </div>
                            <?php else: ?>
                                <div class="w-10 h-10 bg-blue-100 dark:bg-blue-900 rounded-full flex items-center justify-center">
                                    <svg class="w-6 h-6 text-blue-600 dark:text-blue-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"></path>
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"></path>
                                    </svg>
                                </div>
                            <?php endif; ?>
                        </div>

                        <!-- Activity Details -->
                        <div class="flex-1 min-w-0">
                            <p class="text-sm font-medium text-gray-900 dark:text-white truncate">
                                <?= htmlspecialchars($activity['lesson_title']) ?>
                            </p>
                            <p class="text-sm text-gray-500 dark:text-gray-400">
                                <?= htmlspecialchars($activity['dialect_name']) ?>
                            </p>
                        </div>

                        <!-- Score and Time -->
                        <div class="text-right">
                            <?php if ($activity['score']): ?>
                                <p class="text-sm font-medium text-gray-900 dark:text-white">
                                    <?= $activity['score'] ?> points
                                </p>
                            <?php endif; ?>
                            <p class="text-xs text-gray-500 dark:text-gray-400">
                                <?= timeAgo($activity['completed_at']) ?>
                            </p>
                        </div>
                    </div>
                <?php endforeach; ?>
            </div>
        <?php else: ?>
            <div class="text-center py-6">
                <p class="text-gray-500 dark:text-gray-400">No recent activity yet. Start learning!</p>
            </div>
        <?php endif; ?>
    </div>
</div> 