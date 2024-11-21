<?php
function getStreakData($conn, $user_id) {
    $stmt = $conn->prepare("
        SELECT 
            DATE(completed_at) as date,
            COUNT(*) as activities,
            SUM(score) as total_score
        FROM user_progress
        WHERE user_id = ? 
        AND completed_at >= DATE_SUB(CURRENT_DATE, INTERVAL 30 DAY)
        GROUP BY DATE(completed_at)
        ORDER BY date ASC
    ");
    $stmt->execute([$user_id]);
    return $stmt->fetchAll(PDO::FETCH_ASSOC);
}

$streak_data = getStreakData($conn, $_SESSION['user_id']);
$streak_days = array_column($streak_data, 'date');
?>

<div class="bg-white dark:bg-gray-800 rounded-xl shadow-lg overflow-hidden">
    <div class="p-6">
        <h2 class="text-xl font-semibold text-gray-900 dark:text-white mb-4">Learning Streak</h2>
        <div class="grid grid-cols-7 gap-2">
            <?php
            $today = new DateTime();
            $start = (new DateTime())->modify('-27 days');
            
            // Column headers (S M T W T F S)
            $days = ['S', 'M', 'T', 'W', 'T', 'F', 'S'];
            foreach ($days as $day) {
                echo "<div class='text-center text-xs font-medium text-gray-500 dark:text-gray-400'>$day</div>";
            }

            // Calendar squares
            while ($start <= $today) {
                $date = $start->format('Y-m-d');
                $hasActivity = in_array($date, $streak_days);
                $isToday = $date === $today->format('Y-m-d');
                
                $classes = 'h-8 rounded-lg transition-all duration-200 ';
                if ($hasActivity) {
                    $classes .= 'bg-green-500 hover:bg-green-600 cursor-pointer';
                } elseif ($isToday) {
                    $classes .= 'border-2 border-dashed border-gray-300 dark:border-gray-600';
                } else {
                    $classes .= 'bg-gray-100 dark:bg-gray-700';
                }
                
                echo "<div class='$classes' title='$date'></div>";
                $start->modify('+1 day');
            }
            ?>
        </div>
        
        <!-- Streak Stats -->
        <div class="mt-4 flex justify-between items-center">
            <div class="text-sm text-gray-600 dark:text-gray-400">
                Current Streak: <span class="font-bold text-green-600 dark:text-green-400"><?= $streak ?> days</span>
            </div>
            <div class="text-sm text-gray-600 dark:text-gray-400">
                Best Streak: <span class="font-bold text-green-600 dark:text-green-400"><?= max($streak, 0) ?> days</span>
            </div>
        </div>
    </div>
</div> 