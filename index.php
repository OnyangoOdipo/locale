<?php
session_start();
require_once 'config/paths.php';

// If user is logged in, redirect to dashboard
if (isset($_SESSION['user_id'])) {
    header('Location: ' . BASE_URL . 'pages/dashboard.php');
    exit();
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>JifunzeKE - Learn Kenyan Dialects</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link rel="stylesheet" href="https://unpkg.com/aos@next/dist/aos.css" />
    <style>
        :root {
            --kenya-red: #be0027;
            --kenya-green: #00a04a;
            --kenya-black: #000000;
        }

        .hero-pattern {
            background-image: 
                linear-gradient(rgba(0, 0, 0, 0.8), rgba(0, 0, 0, 0.8)),
                url('<?php echo IMAGES_URL; ?>/kenya-emblem-pattern.svg'),
                linear-gradient(135deg, var(--kenya-red), var(--kenya-green));
            background-size: 200px 200px, cover;
            position: relative;
            overflow: hidden;
        }

        .hero-pattern::before {
            content: '';
            position: absolute;
            top: 0;
            left: 0;
            right: 0;
            bottom: 0;
            background: 
                linear-gradient(45deg, 
                    var(--kenya-red) 0%,
                    transparent 30%,
                    transparent 70%,
                    var(--kenya-green) 100%);
            opacity: 0.3;
            z-index: 1;
        }

        .hero-pattern > * {
            position: relative;
            z-index: 2;
        }

        .kenya-gradient {
            background: linear-gradient(135deg, var(--kenya-red), var(--kenya-green));
        }

        .hover-scale {
            transition: transform 0.3s ease;
        }

        .hover-scale:hover {
            transform: scale(1.05);
        }

        @keyframes float {
            0%, 100% { transform: translateY(0); }
            50% { transform: translateY(-10px); }
        }

        .floating {
            animation: float 3s ease-in-out infinite;
        }

        /* Add shield animation */
        @keyframes shieldFloat {
            0% { transform: translateY(0) rotate(0deg); }
            50% { transform: translateY(-20px) rotate(5deg); }
            100% { transform: translateY(0) rotate(0deg); }
        }

        .shield-icon {
            animation: shieldFloat 6s ease-in-out infinite;
        }
    </style>
</head>
<body class="bg-gray-50">
    <!-- Navigation -->
    <nav class="bg-white shadow-lg fixed w-full z-50">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="flex justify-between h-16">
                <div class="flex items-center">
                    <img src="<?php echo IMAGES_URL; ?>/logo.svg" alt="JifunzeKE" class="h-10 w-auto">
                </div>
                <div class="flex items-center space-x-4">
                    <a href="<?php echo BASE_URL; ?>pages/login.php" 
                       class="text-gray-700 hover:text-kenya-red px-3 py-2 rounded-md text-sm font-medium transition-colors duration-300">
                        Login
                    </a>
                    <a href="<?php echo BASE_URL; ?>pages/register.php" 
                       class="bg-kenya-red hover:bg-red-700 text-white px-4 py-2 rounded-lg text-sm font-medium transition-colors duration-300">
                        Get Started
                    </a>
                </div>
            </div>
        </div>
    </nav>

    <!-- Hero Section -->
    <section class="hero-pattern min-h-screen flex items-center justify-center text-white pt-16">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-20 text-center" data-aos="fade-up">
            <!-- Add Kenyan Shield Icon -->
            <div class="flex justify-center mb-8">
                <div class="shield-icon bg-white/10 p-6 rounded-full backdrop-blur-sm">
                    <svg class="w-24 h-24" viewBox="0 0 100 100">
                        <!-- Simplified Kenyan Shield -->
                        <path fill="currentColor" d="M50,10 L80,20 L80,60 L50,90 L20,60 L20,20 Z"/>
                        <path fill="var(--kenya-red)" d="M50,20 L70,25 L70,55 L50,75 L30,55 L30,25 Z"/>
                        <!-- Traditional Pattern -->
                        <rect fill="white" x="45" y="35" width="10" height="2"/>
                        <rect fill="white" x="45" y="45" width="10" height="2"/>
                        <rect fill="white" x="45" y="55" width="10" height="2"/>
                    </svg>
                </div>
            </div>

            <h1 class="text-5xl md:text-6xl font-bold mb-6">
                Discover Kenya's Rich
                <span class="kenya-gradient text-transparent bg-clip-text">Linguistic Heritage</span>
            </h1>
            <p class="text-xl md:text-2xl mb-8 text-gray-300 max-w-3xl mx-auto">
                Learn and preserve Kenya's diverse dialects through interactive lessons, 
                cultural insights, and a supportive community.
            </p>
            <div class="flex flex-col sm:flex-row justify-center gap-4">
                <a href="<?php echo BASE_URL; ?>pages/register.php" 
                   class="bg-kenya-red hover:bg-red-700 text-white px-8 py-4 rounded-lg text-lg font-medium 
                          transition-all duration-300 hover:scale-105 shadow-lg">
                    Start Learning Now
                </a>
                <a href="#features" 
                   class="border-2 border-white hover:bg-white hover:text-gray-900 text-white px-8 py-4 
                          rounded-lg text-lg font-medium transition-all duration-300">
                    Learn More
                </a>
            </div>
        </div>
    </section>

    <!-- Features Section -->
    <section id="features" class="py-20 bg-white">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <h2 class="text-4xl font-bold text-center mb-16" data-aos="fade-up">
                Why Choose <span class="kenya-gradient text-transparent bg-clip-text">JifunzeKE</span>
            </h2>
            <div class="grid grid-cols-1 md:grid-cols-3 gap-8">
                <!-- Feature 1 -->
                <div class="bg-white p-6 rounded-xl shadow-lg hover-scale" data-aos="fade-up" data-aos-delay="100">
                    <div class="h-14 w-14 rounded-full bg-red-100 flex items-center justify-center mb-6">
                        <svg class="h-8 w-8 text-kenya-red" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" 
                                  d="M12 6.253v13m0-13C10.832 5.477 9.246 5 7.5 5S4.168 5.477 3 6.253v13C4.168 18.477 5.754 18 7.5 18s3.332.477 4.5 1.253m0-13C13.168 5.477 14.754 5 16.5 5c1.747 0 3.332.477 4.5 1.253v13C19.832 18.477 18.247 18 16.5 18c-1.746 0-3.332.477-4.5 1.253"/>
                        </svg>
                    </div>
                    <h3 class="text-xl font-semibold mb-4">Interactive Learning</h3>
                    <p class="text-gray-600">
                        Engage with interactive lessons designed to make learning Kenyan dialects fun and effective.
                    </p>
                </div>

                <!-- Feature 2 -->
                <div class="bg-white p-6 rounded-xl shadow-lg hover-scale" data-aos="fade-up" data-aos-delay="200">
                    <div class="h-14 w-14 rounded-full bg-green-100 flex items-center justify-center mb-6">
                        <svg class="h-8 w-8 text-kenya-green" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" 
                                  d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197M13 7a4 4 0 11-8 0 4 4 0 018 0z"/>
                        </svg>
                    </div>
                    <h3 class="text-xl font-semibold mb-4">Cultural Connection</h3>
                    <p class="text-gray-600">
                        Connect with native speakers and immerse yourself in authentic cultural experiences.
                    </p>
                </div>

                <!-- Feature 3 -->
                <div class="bg-white p-6 rounded-xl shadow-lg hover-scale" data-aos="fade-up" data-aos-delay="300">
                    <div class="h-14 w-14 rounded-full bg-gray-100 flex items-center justify-center mb-6">
                        <svg class="h-8 w-8 text-gray-800" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" 
                                  d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/>
                        </svg>
                    </div>
                    <h3 class="text-xl font-semibold mb-4">Track Progress</h3>
                    <p class="text-gray-600">
                        Monitor your learning journey with detailed progress tracking and achievements.
                    </p>
                </div>
            </div>
        </div>
    </section>

    <!-- Dialects Preview -->
    <section class="py-20 bg-gray-50">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <h2 class="text-4xl font-bold text-center mb-16" data-aos="fade-up">
                Explore Kenyan <span class="kenya-gradient text-transparent bg-clip-text">Dialects</span>
            </h2>
            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-8">
                <?php
                $dialects = [
                    ['name' => 'Kikuyu', 'region' => 'Central Kenya', 'image' => 'kikuyu.svg'],
                    ['name' => 'Luo', 'region' => 'Nyanza Region', 'image' => 'luo.svg'],
                    ['name' => 'Luhya', 'region' => 'Western Kenya', 'image' => 'luhya.svg'],
                    ['name' => 'Kamba', 'region' => 'Eastern Kenya', 'image' => 'kamba.svg'],
                    ['name' => 'Kalenjin', 'region' => 'Rift Valley', 'image' => 'kalenjin.svg'],
                    ['name' => 'Maasai', 'region' => 'Rift Valley', 'image' => 'maasai.svg'],
                ];

                foreach ($dialects as $index => $dialect):
                ?>
                <div class="relative overflow-hidden rounded-xl shadow-lg hover-scale" 
                     data-aos="fade-up" 
                     data-aos-delay="<?php echo $index * 100; ?>">
                    <img src="<?php echo IMAGES_URL; ?>/dialects/<?php echo $dialect['image']; ?>" 
                         alt="<?php echo $dialect['name']; ?>"
                         class="w-full h-64 object-cover">
                    <div class="absolute inset-0 bg-gradient-to-t from-black/75 to-transparent"></div>
                    <div class="absolute bottom-0 left-0 p-6 text-white">
                        <h3 class="text-xl font-semibold mb-2"><?php echo $dialect['name']; ?></h3>
                        <p class="text-sm text-gray-300"><?php echo $dialect['region']; ?></p>
                    </div>
                </div>
                <?php endforeach; ?>
            </div>
        </div>
    </section>

    <!-- Call to Action -->
    <section class="py-20 bg-white">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 text-center">
            <h2 class="text-4xl font-bold mb-8" data-aos="fade-up">
                Ready to Start Your Journey?
            </h2>
            <p class="text-xl text-gray-600 mb-12 max-w-3xl mx-auto" data-aos="fade-up" data-aos-delay="100">
                Join thousands of learners discovering Kenya's linguistic diversity. 
                Start your free account today and begin your cultural journey.
            </p>
            <a href="<?php echo BASE_URL; ?>pages/register.php" 
               class="inline-block bg-kenya-red hover:bg-red-700 text-white px-8 py-4 rounded-lg text-lg font-medium transition-all duration-300 hover:scale-105 shadow-lg"
               data-aos="fade-up" data-aos-delay="200">
                Create Free Account
            </a>
        </div>
    </section>

    <!-- Footer -->
    <footer class="bg-gray-900 text-white py-12">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="grid grid-cols-1 md:grid-cols-4 gap-8">
                <div>
                    <img src="<?php echo IMAGES_URL; ?>/logo.svg" alt="JifunzeKE" class="h-10 w-auto mb-4">
                    <p class="text-gray-400">
                        Preserving and sharing Kenya's linguistic heritage.
                    </p>
                </div>
                <div>
                    <h4 class="text-lg font-semibold mb-4">Quick Links</h4>
                    <ul class="space-y-2">
                        <li><a href="#" class="text-gray-400 hover:text-white transition-colors duration-300">About Us</a></li>
                        <li><a href="#" class="text-gray-400 hover:text-white transition-colors duration-300">Contact</a></li>
                        <li><a href="#" class="text-gray-400 hover:text-white transition-colors duration-300">Privacy Policy</a></li>
                    </ul>
                </div>
                <div>
                    <h4 class="text-lg font-semibold mb-4">Resources</h4>
                    <ul class="space-y-2">
                        <li><a href="#" class="text-gray-400 hover:text-white transition-colors duration-300">Blog</a></li>
                        <li><a href="#" class="text-gray-400 hover:text-white transition-colors duration-300">Community</a></li>
                        <li><a href="#" class="text-gray-400 hover:text-white transition-colors duration-300">Support</a></li>
                    </ul>
                </div>
                <div>
                    <h4 class="text-lg font-semibold mb-4">Connect</h4>
                    <div class="flex space-x-4">
                        <a href="#" class="text-gray-400 hover:text-white transition-colors duration-300">
                            <svg class="h-6 w-6" fill="currentColor" viewBox="0 0 24 24">
                                <path d="M24 12.073c0-6.627-5.373-12-12-12s-12 5.373-12 12c0 5.99 4.388 10.954 10.125 11.854v-8.385H7.078v-3.47h3.047V9.43c0-3.007 1.792-4.669 4.533-4.669 1.312 0 2.686.235 2.686.235v2.953H15.83c-1.491 0-1.956.925-1.956 1.874v2.25h3.328l-.532 3.47h-2.796v8.385C19.612 23.027 24 18.062 24 12.073z"/>
                            </svg>
                        </a>
                        <a href="#" class="text-gray-400 hover:text-white transition-colors duration-300">
                            <svg class="h-6 w-6" fill="currentColor" viewBox="0 0 24 24">
                                <path d="M23.953 4.57a10 10 0 01-2.825.775 4.958 4.958 0 002.163-2.723c-.951.555-2.005.959-3.127 1.184a4.92 4.92 0 00-8.384 4.482C7.69 8.095 4.067 6.13 1.64 3.162a4.822 4.822 0 00-.666 2.475c0 1.71.87 3.213 2.188 4.096a4.904 4.904 0 01-2.228-.616v.06a4.923 4.923 0 003.946 4.827 4.996 4.996 0 01-2.212.085 4.936 4.936 0 004.604 3.417 9.867 9.867 0 01-6.102 2.105c-.39 0-.779-.023-1.17-.067a13.995 13.995 0 007.557 2.209c9.053 0 13.998-7.496 13.998-13.985 0-.21 0-.42-.015-.63A9.935 9.935 0 0024 4.59z"/>
                            </svg>
                        </a>
                        <a href="#" class="text-gray-400 hover:text-white transition-colors duration-300">
                            <svg class="h-6 w-6" fill="currentColor" viewBox="0 0 24 24">
                                <path d="M12 0C8.74 0 8.333.015 7.053.072 5.775.132 4.905.333 4.14.63c-.789.306-1.459.717-2.126 1.384S.935 3.35.63 4.14C.333 4.905.131 5.775.072 7.053.012 8.333 0 8.74 0 12s.015 3.667.072 4.947c.06 1.277.261 2.148.558 2.913.306.788.717 1.459 1.384 2.126.667.666 1.336 1.079 2.126 1.384.766.296 1.636.499 2.913.558C8.333 23.988 8.74 24 12 24s3.667-.015 4.947-.072c1.277-.06 2.148-.262 2.913-.558.788-.306 1.459-.718 2.126-1.384.666-.667 1.079-1.335 1.384-2.126.296-.765.499-1.636.558-2.913.06-1.28.072-1.687.072-4.947s-.015-3.667-.072-4.947c-.06-1.277-.262-2.149-.558-2.913-.306-.789-.718-1.459-1.384-2.126C21.319 1.347 20.651.935 19.86.63c-.765-.297-1.636-.499-2.913-.558C15.667.012 15.26 0 12 0zm0 2.16c3.203 0 3.585.016 4.85.071 1.17.055 1.805.249 2.227.415.562.217.96.477 1.382.896.419.42.679.819.896 1.381.164.422.36 1.057.413 2.227.057 1.266.07 1.646.07 4.85s-.015 3.585-.074 4.85c-.061 1.17-.256 1.805-.421 2.227-.224.562-.479.96-.899 1.382-.419.419-.824.679-1.38.896-.42.164-1.065.36-2.235.413-1.274.057-1.649.07-4.859.07-3.211 0-3.586-.015-4.859-.074-1.171-.061-1.816-.256-2.236-.421-.569-.224-.96-.479-1.379-.899-.421-.419-.69-.824-.9-1.38-.165-.42-.359-1.065-.42-2.235-.045-1.26-.061-1.649-.061-4.844 0-3.196.016-3.586.061-4.861.061-1.17.255-1.814.42-2.234.21-.57.479-.96.9-1.381.419-.419.81-.689 1.379-.898.42-.166 1.051-.361 2.221-.421 1.275-.045 1.65-.06 4.859-.06l.045.03zm0 3.678c-3.405 0-6.162 2.76-6.162 6.162 0 3.405 2.76 6.162 6.162 6.162 3.405 0 6.162-2.76 6.162-6.162 0-3.405-2.76-6.162-6.162-6.162zM12 16c-2.21 0-4-1.79-4-4s1.79-4 4-4 4 1.79 4 4-1.79 4-4 4zm7.846-10.405c0 .795-.646 1.44-1.44 1.44-.795 0-1.44-.646-1.44-1.44 0-.794.646-1.439 1.44-1.439.793-.001 1.44.645 1.44 1.439z"/>
                            </svg>
                        </a>
                    </div>
                </div>
            </div>
            <div class="border-t border-gray-800 mt-12 pt-8 text-center">
                <p class="text-gray-400">
                    &copy; <?php echo date('Y'); ?> JifunzeKE. All rights reserved.
                </p>
            </div>
        </div>
    </footer>

    <script src="https://unpkg.com/aos@next/dist/aos.js"></script>
    <script>
        AOS.init({
            duration: 800,
            offset: 100,
            once: true
        });

        // Smooth scroll for anchor links
        document.querySelectorAll('a[href^="#"]').forEach(anchor => {
            anchor.addEventListener('click', function (e) {
                e.preventDefault();
                document.querySelector(this.getAttribute('href')).scrollIntoView({
                    behavior: 'smooth'
                });
            });
        });
    </script>
</body>
</html> 