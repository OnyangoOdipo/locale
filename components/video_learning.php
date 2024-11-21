<?php
function getAllOnlineUsers($conn, $user_id) {
    // Get all users except the current user
    $stmt = $conn->prepare("
        SELECT 
            u.id, 
            u.username, 
            u.profile_picture,
            u.last_active,
            CASE 
                WHEN u.last_active >= DATE_SUB(NOW(), INTERVAL 2 MINUTE) THEN 'online'
                WHEN u.last_active >= DATE_SUB(NOW(), INTERVAL 5 MINUTE) THEN 'away'
                ELSE 'offline'
            END as status
        FROM users u
        WHERE u.id != ?
        AND u.last_active >= DATE_SUB(NOW(), INTERVAL 30 MINUTE)
        ORDER BY u.last_active DESC
    ");
    $stmt->execute([$user_id]);
    
    return $stmt->fetchAll(PDO::FETCH_ASSOC);
}

$online_users = getAllOnlineUsers($conn, $_SESSION['user_id']);

// Get the user's dialect
$stmt = $conn->prepare("
    SELECT ud.dialect_id, d.name as dialect_name 
    FROM user_dialects ud
    JOIN dialects d ON ud.dialect_id = d.id 
    WHERE ud.user_id = ?
    ORDER BY ud.created_at DESC
    LIMIT 1
");
$stmt->execute([$_SESSION['user_id']]);
$user_dialect = $stmt->fetch(PDO::FETCH_ASSOC);
?>

<div class="bg-white dark:bg-gray-800 rounded-xl shadow-lg overflow-hidden">
    <div class="p-6">
        <div class="flex justify-between items-center mb-6">
            <div>
                <h2 class="text-xl font-semibold text-gray-900 dark:text-white">Language Exchange Hub</h2>
                <p class="text-sm text-gray-500 dark:text-gray-400">
                    Connect with other <?php echo htmlspecialchars($user_dialect['dialect_name'] ?? ''); ?> learners
                </p>
            </div>
            <a href="<?php echo BASE_URL; ?>pages/video-room.php?dialect=<?php echo $user_dialect['dialect_id']; ?>" 
               class="px-4 py-2 bg-indigo-600 text-white rounded-lg hover:bg-indigo-700 transition-colors">
                Join <?php echo htmlspecialchars($user_dialect['dialect_name'] ?? ''); ?> Room
            </a>
        </div>

        <!-- Online Users List -->
        <div class="space-y-4">
            <div class="flex items-center justify-between">
                <h3 class="font-medium text-gray-900 dark:text-white">
                    Online Users (<?= count($online_users) ?>)
                </h3>
                <div class="flex items-center space-x-4 text-sm">
                    <span class="flex items-center">
                        <span class="w-2 h-2 bg-green-500 rounded-full mr-2"></span>
                        Online
                    </span>
                    <span class="flex items-center">
                        <span class="w-2 h-2 bg-yellow-500 rounded-full mr-2"></span>
                        Away
                    </span>
                </div>
            </div>

            <?php if (!empty($online_users)): ?>
                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                    <?php foreach ($online_users as $user): ?>
                        <div class="flex items-center p-4 bg-gray-50 dark:bg-gray-700 rounded-lg">
                            <div class="relative">
                                <img src="<?= htmlspecialchars($user['profile_picture'] ?? '/assets/images/default-avatar.png') ?>" 
                                     alt="Profile" 
                                     class="w-12 h-12 rounded-full object-cover">
                                <span class="absolute bottom-0 right-0 w-3 h-3 rounded-full 
                                    <?= $user['status'] === 'online' ? 'bg-green-500' : 
                                        ($user['status'] === 'away' ? 'bg-yellow-500' : 'bg-gray-500') ?> 
                                    border-2 border-white">
                                </span>
                            </div>
                            <div class="ml-4 flex-1">
                                <p class="font-medium text-gray-900 dark:text-white">
                                    <?= htmlspecialchars($user['username']) ?>
                                </p>
                                <p class="text-sm text-gray-500 dark:text-gray-400">
                                    Last active: <?= timeAgo($user['last_active']) ?>
                                </p>
                            </div>
                        </div>
                    <?php endforeach; ?>
                </div>
            <?php else: ?>
                <div class="text-center py-8">
                    <div class="mb-4">
                        <svg class="w-16 h-16 mx-auto text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" 
                                  d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z"/>
                        </svg>
                    </div>
                    <p class="text-gray-500 dark:text-gray-400">
                        No users currently online.
                        <br>Be the first to join the video room.
                    </p>
                </div>
            <?php endif; ?>
        </div>
    </div>
</div> 