<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}
require_once dirname(__DIR__) . '/config/paths.php';

// Set default theme if not set
if (!isset($_SESSION['theme'])) {
    $_SESSION['theme'] = 'light';
}
?>
<!DOCTYPE html>
<html lang="en" class="<?php echo $_SESSION['theme']; ?>">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>JifunzeKE - Learn Kenyan Dialects</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link rel="icon" type="image/svg+xml" href="<?php echo IMAGES_URL; ?>/favicon.svg">
    <!-- Add custom styles -->
    <style>
        :root {
            --kenya-red: #be0027;
            --kenya-green: #00a04a;
            --kenya-black: #000000;
        }

        /* Theme Variables */
        .light {
            --bg-primary: #ffffff;
            --bg-secondary: #f3f4f6;
            --text-primary: #111827;
            --text-secondary: #4b5563;
            --border-color: #e5e7eb;
            --hover-bg: #f9fafb;
        }

        .dark {
            --bg-primary: #1f2937;
            --bg-secondary: #111827;
            --text-primary: #f9fafb;
            --text-secondary: #d1d5db;
            --border-color: #374151;
            --hover-bg: #374151;
        }

        /* Theme-aware styles */
        .theme-bg-primary { background-color: var(--bg-primary); }
        .theme-bg-secondary { background-color: var(--bg-secondary); }
        .theme-text-primary { color: var(--text-primary); }
        .theme-text-secondary { color: var(--text-secondary); }
        .theme-border { border-color: var(--border-color); }
        .theme-hover:hover { background-color: var(--hover-bg); }
    </style>
</head>
<body class="antialiased theme-bg-secondary theme-text-primary min-h-screen flex flex-col">
    <?php if (isset($_SESSION['user_id'])): ?>
    <nav class="theme-bg-primary shadow">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="flex justify-between h-16">
                <div class="flex">
                    <div class="flex-shrink-0 flex items-center">
                        <img class="h-12 w-auto" src="<?php echo IMAGES_URL; ?>/logo.svg" alt="JifunzeKE">
                    </div>
                    <div class="hidden sm:ml-6 sm:flex sm:space-x-8">
                        <a href="<?php echo BASE_URL; ?>pages/dashboard.php" 
                           class="<?php echo basename($_SERVER['PHP_SELF']) == 'dashboard.php' ? 'border-kenya-red' : 'border-transparent'; ?> 
                                  theme-text-primary inline-flex items-center px-1 pt-1 border-b-2 text-sm font-medium">
                            Dashboard
                        </a>
                        <a href="<?php echo BASE_URL; ?>pages/lessons.php" 
                           class="<?php echo basename($_SERVER['PHP_SELF']) == 'lessons.php' ? 'border-kenya-green' : 'border-transparent'; ?> 
                                  theme-text-primary inline-flex items-center px-1 pt-1 border-b-2 text-sm font-medium">
                            My Lessons
                        </a>
                        <a href="<?php echo BASE_URL; ?>pages/practice.php" 
                           class="<?php echo basename($_SERVER['PHP_SELF']) == 'practice.php' ? 'border-kenya-black' : 'border-transparent'; ?> 
                                  theme-text-primary inline-flex items-center px-1 pt-1 border-b-2 text-sm font-medium">
                            Practice
                        </a>
                    </div>
                </div>
                <div class="flex items-center space-x-4">
                    <!-- Theme Toggle -->
                    <button onclick="toggleTheme()" 
                            class="p-2 rounded-full theme-hover">
                        <!-- Sun icon for dark mode -->
                        <svg class="h-6 w-6 hidden dark:block theme-text-primary" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" 
                                  d="M12 3v1m0 16v1m9-9h-1M4 12H3m15.364 6.364l-.707-.707M6.343 6.343l-.707-.707m12.728 0l-.707.707M6.343 17.657l-.707.707M16 12a4 4 0 11-8 0 4 4 0 018 0z" />
                        </svg>
                        <!-- Moon icon for light mode -->
                        <svg class="h-6 w-6 block dark:hidden theme-text-primary" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" 
                                  d="M20.354 15.354A9 9 0 018.646 3.646 9.003 9.003 0 0012 21a9.003 9.003 0 008.354-5.646z" />
                        </svg>
                    </button>
                    
                    <!-- Profile Dropdown -->
                    <div class="relative">
                        <button onclick="toggleProfileMenu()" 
                                class="flex items-center space-x-3 focus:outline-none theme-text-primary">
                            <img class="h-8 w-8 rounded-full object-cover border-2 theme-border" 
                                 src="<?php echo $_SESSION['profile_picture'] ?? IMAGES_URL . '/default-avatar.png'; ?>" 
                                 alt="Profile">
                            <span class="hidden md:block font-medium">
                                <?php echo $_SESSION['username']; ?>
                            </span>
                            <svg class="h-5 w-5 theme-text-secondary" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7" />
                            </svg>
                        </button>
                        
                        <!-- Dropdown Menu -->
                        <div id="profileMenu" 
                             class="hidden absolute right-0 mt-2 w-48 rounded-md shadow-lg theme-bg-primary ring-1 ring-black ring-opacity-5">
                            <div class="py-1">
                                <a href="<?php echo BASE_URL; ?>pages/profile.php" 
                                   class="block px-4 py-2 text-sm theme-text-primary theme-hover">
                                    Profile Settings
                                </a>
                                <a href="<?php echo BASE_URL; ?>pages/achievements.php" 
                                   class="block px-4 py-2 text-sm theme-text-primary theme-hover">
                                    My Achievements
                                </a>
                                <a href="<?php echo BASE_URL; ?>pages/statistics.php" 
                                   class="block px-4 py-2 text-sm theme-text-primary theme-hover">
                                    Learning Statistics
                                </a>
                                <div class="border-t theme-border"></div>
                                <a href="<?php echo BASE_URL; ?>includes/logout.php" 
                                   class="block px-4 py-2 text-sm text-red-600 hover:bg-red-50 dark:hover:bg-red-900">
                                    Sign out
                                </a>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </nav>
    <?php endif; ?>

    <script>
        function toggleTheme() {
            const html = document.documentElement;
            const currentTheme = html.classList.contains('dark') ? 'light' : 'dark';
            html.classList.remove('light', 'dark');
            html.classList.add(currentTheme);

            fetch('<?php echo BASE_URL; ?>includes/save_theme.php', {
                method: 'POST',
                headers: { 'Content-Type': 'application/json' },
                body: JSON.stringify({ theme: currentTheme })
            });
        }

        function toggleProfileMenu() {
            const menu = document.getElementById('profileMenu');
            menu.classList.toggle('hidden');
        }

        // Close profile menu when clicking outside
        document.addEventListener('click', (e) => {
            const menu = document.getElementById('profileMenu');
            const profileButton = document.querySelector('button[onclick="toggleProfileMenu()"]');
            
            if (!menu.contains(e.target) && !profileButton.contains(e.target)) {
                menu.classList.add('hidden');
            }
        });
    </script>
</body>
</html> 