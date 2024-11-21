<?php
require_once '../components/header.php';
require_once '../config/database.php';

// Authentication check
if (!isset($_SESSION['user_id'])) {
    header('Location: ' . BASE_URL . 'pages/login.php');
    exit();
}

$db = new Database();
$conn = $db->getConnection();

// Fetch user data with preferences
$stmt = $conn->prepare("
    SELECT u.*, up.motivation, up.daily_goal
    FROM users u
    LEFT JOIN user_preferences up ON u.id = up.user_id
    WHERE u.id = ?
");
$stmt->execute([$_SESSION['user_id']]);
$user = $stmt->fetch(PDO::FETCH_ASSOC);

// Get the first lesson ID for the user's selected dialect
$stmt = $conn->prepare("
    SELECT MIN(l.id) as lesson_id
    FROM lessons l
    JOIN user_dialects ud ON l.dialect_id = ud.dialect_id
    WHERE ud.user_id = ?
");
$stmt->execute([$_SESSION['user_id']]);
$first_lesson = $stmt->fetch(PDO::FETCH_ASSOC);



// If user has progress, get their next uncompleted lesson
$stmt = $conn->prepare("
    SELECT l.id, l.title, d.name as dialect_name 
    FROM lessons l
    JOIN user_dialects ud ON l.dialect_id = ud.dialect_id
    JOIN dialects d ON l.dialect_id = d.id
    LEFT JOIN user_progress up ON l.id = up.lesson_id AND up.user_id = ?
    WHERE ud.user_id = ? 
    AND (up.completed IS NULL OR up.completed = 0)
    ORDER BY l.id ASC
    LIMIT 1
");
$stmt->execute([$_SESSION['user_id'], $_SESSION['user_id']]);
$next_lesson = $stmt->fetch(PDO::FETCH_ASSOC);

// If no next lesson found, get the first lesson of user's dialect
if (!$next_lesson) {
    $stmt = $conn->prepare("
        SELECT l.id, l.title, d.name as dialect_name
        FROM lessons l
        JOIN user_dialects ud ON l.dialect_id = ud.dialect_id
        JOIN dialects d ON l.dialect_id = d.id
        WHERE ud.user_id = ?
        ORDER BY l.id ASC
        LIMIT 1
    ");
    $stmt->execute([$_SESSION['user_id']]);
    $next_lesson = $stmt->fetch(PDO::FETCH_ASSOC);
}

// Calculate streak and stats
$stmt = $conn->prepare("
    SELECT 
        COUNT(DISTINCT DATE(completed_at)) as streak,
        SUM(score) as total_points,
        COUNT(DISTINCT lesson_id) as completed_lessons
    FROM user_progress
    WHERE user_id = ?
    AND completed = 1
    AND completed_at >= DATE_SUB(CURRENT_DATE, INTERVAL 30 DAY)
");
$stmt->execute([$_SESSION['user_id']]);
$stats = $stmt->fetch(PDO::FETCH_ASSOC);
$streak = $stats['streak'] ?? 0;
?>

<div class="min-h-screen theme-bg-secondary">
    <!-- Kenyan-themed Banner with Shield Pattern -->
    <div class="relative bg-gradient-to-r from-[#be0027] via-[#00a04a] to-black text-white shadow-lg overflow-hidden">
        <!-- Maasai Pattern Overlay -->
        <div class="absolute inset-0 opacity-10" style="background-image: url('/Locale/assets/images/maasai-pattern.svg');"></div>
        
        <!-- Content -->
        <div class="relative max-w-7xl mx-auto px-4 py-6">
            <div class="flex items-center justify-between">
                <div class="flex items-center space-x-6">
                    <div class="relative">
                        <img class="h-20 w-20 rounded-full border-4 border-white shadow-lg object-cover" 
                             src="<?php echo $user['profile_picture'] ?? '/Locale/assets/images/default-avatar.png'; ?>" 
                             alt="Profile Picture">
                        <?php if ($streak >= 5): ?>
                        <div class="absolute -top-2 -right-2 bg-yellow-400 rounded-full p-2" title="Streak Champion!">
                            <svg class="h-5 w-5 text-white" fill="currentColor" viewBox="0 0 20 20">
                                <path d="M9.049 2.927c.3-.921 1.603-.921 1.902 0l1.07 3.292a1 1 0 00.95.69h3.462c.969 0 1.371 1.24.588 1.81l-2.8 2.034a1 1 0 00-.364 1.118l1.07 3.292c.3.921-.755 1.688-1.54 1.118l-2.8-2.034a1 1 0 00-1.175 0l-2.8 2.034c-.784.57-1.838-.197-1.539-1.118l1.07-3.292a1 1 0 00-.364-1.118L2.98 8.72c-.783-.57-.38-1.81.588-1.81h3.461a1 1 0 00.951-.69l1.07-3.292z"/>
                            </svg>
                        </div>
                        <?php endif; ?>
                    </div>
                    <div>
                        <h1 class="text-3xl font-bold">Jambo, <?php echo htmlspecialchars($user['username']); ?>!</h1>
                        <p class="text-white/80">Keep learning and growing! 🌍</p>
                    </div>
                </div>
                <div class="flex items-center space-x-8">
                    <div class="text-center">
                        <p class="text-3xl font-bold"><?php echo number_format($stats['total_points']); ?></p>
                        <p class="text-sm text-white/80">Points</p>
                    </div>
                    <div class="text-center">
                        <p class="text-3xl font-bold"><?php echo $streak; ?></p>
                        <p class="text-sm text-white/80">Day Streak</p>
                    </div>
                    <div class="text-center">
                        <p class="text-3xl font-bold"><?php echo $stats['completed_lessons']; ?></p>
                        <p class="text-sm text-white/80">Lessons Done</p>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="max-w-7xl mx-auto px-4 py-8">
        <div class="grid grid-cols-1 lg:grid-cols-3 gap-8">
            <!-- Next Lesson Card -->
            <?php if ($next_lesson): ?>
            <div class="theme-bg-primary rounded-xl shadow-lg overflow-hidden transform hover:scale-105 transition-transform duration-300">
                <div class="p-6">
                    <div class="flex items-center justify-between mb-4">
                        <h2 class="text-xl font-semibold theme-text-primary">Continue Learning</h2>
                        <span class="px-3 py-1 bg-green-100 dark:bg-green-900 text-green-800 dark:text-green-100 rounded-full text-sm">
                            <?php echo htmlspecialchars($next_lesson['dialect_name']); ?>
                        </span>
                    </div>
                    <p class="theme-text-secondary mb-6"><?php echo htmlspecialchars($next_lesson['title']); ?></p>
                    <a href="<?php echo BASE_URL; ?>pages/lesson.php?id=<?php echo $next_lesson['id']; ?>" 
                       class="block w-full text-center bg-indigo-600 dark:bg-indigo-500 text-white px-4 py-3 rounded-lg 
                              hover:bg-indigo-700 dark:hover:bg-indigo-600 transition-colors duration-300">
                        Start Learning
                    </a>
                </div>
            </div>
            <?php else: ?>
            <!-- No lessons available -->
            <div class="theme-bg-primary rounded-xl shadow-lg overflow-hidden">
                <div class="p-6">
                    <h2 class="text-xl font-semibold theme-text-primary mb-4">Choose Your Dialect</h2>
                    <p class="theme-text-secondary mb-6">Select a dialect to start your learning journey</p>
                    <a href="<?php echo BASE_URL; ?>pages/profile.php#dialect-selection" 
                       class="block w-full text-center bg-indigo-600 dark:bg-indigo-500 text-white px-4 py-3 rounded-lg 
                              hover:bg-indigo-700 dark:hover:bg-indigo-600 transition-colors duration-300">
                        Choose Dialect
                    </a>
                </div>
            </div>
            <?php endif; ?>

            <!-- Quick Stats -->
            <div class="theme-bg-primary rounded-xl shadow-lg overflow-hidden">
                <div class="p-6">
                    <h2 class="text-xl font-semibold theme-text-primary mb-4">Today's Progress</h2>
                    <div class="space-y-4">
                        <!-- Study Time -->
                        <div>
                            <div class="flex justify-between mb-2">
                                <span class="text-sm font-medium theme-text-primary">Study Time</span>
                                <span class="text-sm theme-text-secondary"><?php echo $user['daily_goal']; ?> min goal</span>
                            </div>
                            <div class="h-2 bg-gray-200 rounded-full">
                                <div class="h-2 bg-green-500 rounded-full" style="width: 45%"></div>
                            </div>
                        </div>

                        <!-- Practice Sessions -->
                        <div>
                            <div class="flex justify-between mb-2">
                                <span class="text-sm font-medium theme-text-primary">Practice Sessions</span>
                                <span class="text-sm theme-text-secondary">2/3 completed</span>
                            </div>
                            <div class="h-2 bg-gray-200 rounded-full">
                                <div class="h-2 bg-indigo-500 rounded-full" style="width: 66%"></div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Motivation Card -->
            <div class="theme-bg-primary rounded-xl shadow-lg overflow-hidden">
                <div class="p-6">
                    <h2 class="text-xl font-semibold theme-text-primary mb-4">Daily Motivation</h2>
                    <div class="space-y-4">
                        <p class="text-gray-600 italic theme-text-primary">
                            "<?php
                            $motivational_quotes = [
                                "Lugha ni utambulisho - Language is identity",
                                "Pole pole ndio mwendo - Slowly but surely",
                                "Mwanzo wa kujifunza ni kujitahidi - The beginning of learning is effort",
                                "Leo ni leo, kesho ni kesho - Today is today, tomorrow is tomorrow"
                            ];
                            echo $motivational_quotes[array_rand($motivational_quotes)];
                            ?>"
                        </p>
                    </div>
                </div>
            </div>
        </div>

        <!-- Recent Activity and Progress -->
        <div class="grid grid-cols-1 lg:grid-cols-2 gap-8 mt-8">
            <!-- Left Column -->
            <div class="space-y-8">
                <?php include '../components/recent_activity.php'; ?>
                <?php include '../components/streak_calendar.php'; ?>
            </div>
            
            <!-- Right Column -->
            <div class="space-y-8">
                <?php include '../components/achievements.php'; ?>
                <?php include '../components/learning_progress.php'; ?>
                <!-- In dashboard.php or a new page -->
<div class="max-w-7xl mx-auto px-4 py-8">
    <?php include '../components/video_learning.php'; ?>
</div>
            </div>
        </div>
    </div>
</div>

<script>
document.addEventListener('DOMContentLoaded', () => {
    // Animate progress bars
    const progressBars = document.querySelectorAll('[class*="bg-indigo-500"], [class*="bg-green-500"]');
    progressBars.forEach(bar => {
        const width = bar.style.width;
        bar.style.width = '0';
        setTimeout(() => {
            bar.style.transition = 'width 1s ease-in-out';
            bar.style.width = width;
        }, 100);
    });
});
</script>

<?php require_once '../components/footer.php'; ?> 