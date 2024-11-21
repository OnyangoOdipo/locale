<?php
require_once '../components/header.php';
if (isset($_SESSION['user_id'])) {
    header('Location: /pages/dashboard.php');
    exit();
}
?>

<div class="min-h-screen bg-gradient-to-br from-indigo-100 via-white to-pink-100 py-12 px-4 sm:px-6 lg:px-8 flex items-center">
    <div class="max-w-md w-full mx-auto space-y-8">
        <!-- Logo and Title -->
        <div class="text-center">
            <div class="flex justify-center animate-bounce">
                <img class="h-16 w-auto" src="/assets/images/logo.svg" alt="JifunzeKE">
            </div>
            <h2 class="mt-6 text-3xl font-extrabold text-gray-900 tracking-tight">
                Welcome Back!
            </h2>
            <p class="mt-2 text-sm text-gray-600">
                Continue your journey in learning Kenyan dialects
            </p>
        </div>

        <!-- Login Card -->
        <div class="bg-white/80 backdrop-blur-lg rounded-2xl shadow-xl overflow-hidden hover:shadow-2xl transition-all duration-300">
            <div class="px-6 py-8 sm:px-8">
                <?php if (isset($_SESSION['error'])): ?>
                    <div class="mb-4 p-4 bg-red-50 border-l-4 border-red-500 text-red-700">
                        <?php
                        echo $_SESSION['error'];
                        unset($_SESSION['error']);
                        ?>
                    </div>
                <?php endif; ?>

                <form class="space-y-6" method="POST" action="<?php echo BASE_URL; ?>includes/login_process.php">
                    <!-- Email/Username Field -->
                    <div class="group">
                        <label class="block text-sm font-medium text-gray-700 mb-1">Email or Username</label>
                        <div class="relative">
                            <input type="text" name="identifier" required
                                class="appearance-none block w-full px-4 py-3 border border-gray-300 rounded-xl 
                                       placeholder-gray-400 focus:outline-none focus:ring-2 focus:ring-indigo-500 
                                       focus:border-transparent transition-all duration-300
                                       group-hover:border-indigo-300"
                                placeholder="Enter your email or username">
                            <div class="absolute inset-y-0 left-3 pl-1 flex items-center pointer-events-none opacity-0 
                                      group-focus-within:opacity-100 transition-opacity duration-300">
                                <svg class="h-5 w-5 text-indigo-500" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                        d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z" />
                                </svg>
                            </div>
                        </div>
                    </div>

                    <!-- Password Field -->
                    <div class="group">
                        <label class="block text-sm font-medium text-gray-700 mb-1">Password</label>
                        <div class="relative">
                            <input type="password" name="password" required id="password"
                                class="appearance-none block w-full px-4 py-3 border border-gray-300 rounded-xl 
                                       placeholder-gray-400 focus:outline-none focus:ring-2 focus:ring-indigo-500 
                                       focus:border-transparent transition-all duration-300
                                       group-hover:border-indigo-300"
                                placeholder="Enter your password">
                            <button type="button"
                                class="absolute inset-y-0 right-0 pr-3 flex items-center text-gray-400 hover:text-gray-600"
                                onclick="togglePassword()">
                                <svg class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor" id="eye-icon">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                        d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" />
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                        d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z" />
                                </svg>
                            </button>
                        </div>
                    </div>

                    <!-- Remember Me & Forgot Password -->
                    <div class="flex items-center justify-between">
                        <div class="flex items-center">
                            <input type="checkbox" name="remember_me" id="remember_me"
                                class="h-4 w-4 text-indigo-600 focus:ring-indigo-500 border-gray-300 rounded
                                       transition-all duration-300 cursor-pointer">
                            <label for="remember_me" class="ml-2 block text-sm text-gray-700 cursor-pointer
                                                          hover:text-indigo-500 transition-colors duration-300">
                                Remember me
                            </label>
                        </div>
                        <a href="/pages/forgot-password.php"
                            class="text-sm font-medium text-indigo-600 hover:text-indigo-500
                                  transition-colors duration-300 hover:underline">
                            Forgot password?
                        </a>
                    </div>

                    <!-- Login Button -->
                    <div>
                        <button type="submit"
                            class="group relative w-full flex justify-center py-3 px-4 border border-transparent 
                                   text-sm font-medium rounded-xl text-white bg-indigo-600 hover:bg-indigo-700 
                                   focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500
                                   transition-all duration-300 transform hover:scale-[1.02]">
                            <span class="absolute left-0 inset-y-0 flex items-center pl-3">
                                <svg class="h-5 w-5 text-indigo-500 group-hover:text-indigo-400 transition-colors duration-300"
                                    fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                        d="M11 16l-4-4m0 0l4-4m-4 4h14m-5 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h7a3 3 0 013 3v1" />
                                </svg>
                            </span>
                            Sign in
                        </button>
                    </div>
                </form>

                <!-- Social Login -->
                <div class="mt-8">
                    <div class="relative">
                        <div class="absolute inset-0 flex items-center">
                            <div class="w-full border-t border-gray-300"></div>
                        </div>
                        <div class="relative flex justify-center text-sm">
                            <span class="px-2 bg-white text-gray-500">Or continue with</span>
                        </div>
                    </div>

                    <div class="mt-6 grid grid-cols-2 gap-3">
                        <button type="button"
                            class="w-full inline-flex justify-center py-2 px-4 border border-gray-300 rounded-md shadow-sm bg-white text-sm font-medium text-gray-500 hover:bg-gray-50">
                            <svg class="w-5 h-5" fill="currentColor" viewBox="0 0 24 24">
                                <path d="M12.24 10.285V14.4h6.806c-.275 1.765-2.056 5.174-6.806 5.174-4.095 0-7.439-3.389-7.439-7.574s3.345-7.574 7.439-7.574c2.33 0 3.891.989 4.785 1.849l3.254-3.138C18.189 1.186 15.479 0 12.24 0c-6.635 0-12 5.365-12 12s5.365 12 12 12c6.926 0 11.52-4.869 11.52-11.726 0-.788-.085-1.39-.189-1.989H12.24z" />
                            </svg>
                        </button>

                        <button type="button"
                            class="w-full inline-flex justify-center py-2 px-4 border border-gray-300 rounded-md shadow-sm bg-white text-sm font-medium text-gray-500 hover:bg-gray-50">
                            <svg class="w-5 h-5" fill="currentColor" viewBox="0 0 20 20">
                                <path fill-rule="evenodd" d="M10 0C4.477 0 0 4.477 0 10c0 4.42 2.865 8.166 6.839 9.489.5.092.682-.217.682-.482 0-.237-.008-.866-.013-1.7-2.782.604-3.369-1.34-3.369-1.34-.454-1.156-1.11-1.463-1.11-1.463-.908-.62.069-.608.069-.608 1.003.07 1.531 1.03 1.531 1.03.892 1.529 2.341 1.087 2.91.832.092-.647.35-1.088.636-1.338-2.22-.253-4.555-1.11-4.555-4.943 0-1.091.39-1.984 1.029-2.683-.103-.253-.446-1.27.098-2.647 0 0 .84-.269 2.75 1.025A9.564 9.564 0 0110 4.844c.85.004 1.705.115 2.504.337 1.909-1.294 2.747-1.025 2.747-1.025.546 1.377.203 2.394.1 2.647.64.699 1.028 1.592 1.028 2.683 0 3.842-2.339 4.687-4.566 4.935.359.309.678.919.678 1.852 0 1.336-.012 2.415-.012 2.743 0 .267.18.578.688.48C17.137 18.163 20 14.418 20 10c0-5.523-4.477-10-10-10z" clip-rule="evenodd" />
                            </svg>
                        </button>
                    </div>
                </div>

                <!-- Sign Up Link -->
                <div class="mt-6 text-center">
                    <p class="text-sm text-gray-600">
                        Don't have an account yet?
                        <a href="/pages/register.php"
                            class="font-medium text-indigo-600 hover:text-indigo-500 transition-colors duration-300
                                  hover:underline">
                            Sign up for free
                        </a>
                    </p>
                </div>
            </div>
        </div>
    </div>
</div>

<script>
    function togglePassword() {
        const password = document.getElementById('password');
        const eyeIcon = document.getElementById('eye-icon');

        if (password.type === 'password') {
            password.type = 'text';
            eyeIcon.innerHTML = `
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" 
                  d="M13.875 18.825A10.05 10.05 0 0112 19c-4.478 0-8.268-2.943-9.543-7a9.97 9.97 0 011.563-3.029m5.858.908a3 3 0 114.243 4.243M9.878 9.878l4.242 4.242M9.88 9.88l-3.29-3.29m7.532 7.532l3.29 3.29M3 3l3.59 3.59m0 0A9.953 9.953 0 0112 5c4.478 0 8.268 2.943 9.543 7a10.025 10.025 0 01-4.132 5.411m0 0L21 21"/>
        `;
        } else {
            password.type = 'password';
            eyeIcon.innerHTML = `
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" 
                  d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/>
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" 
                  d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"/>
        `;
        }
    }

    // Add smooth animations for form submission
    document.querySelector('form').addEventListener('submit', function(e) {
        e.preventDefault();
        const button = this.querySelector('button[type="submit"]');
        const originalText = button.innerHTML;

        // Add loading state
        button.innerHTML = `
        <svg class="animate-spin h-5 w-5 text-white" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">
            <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
            <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
        </svg>
        <span class="ml-2">Signing in...</span>
    `;

        // Submit the form after animation
        setTimeout(() => {
            this.submit();
        }, 800);
    });

    // Add floating label animation
    document.querySelectorAll('input').forEach(input => {
        input.addEventListener('focus', function() {
            this.parentElement.classList.add('focused');
        });

        input.addEventListener('blur', function() {
            if (!this.value) {
                this.parentElement.classList.remove('focused');
            }
        });
    });
</script>

<style>
    /* Custom animations */
    @keyframes float {
        0% {
            transform: translateY(0px);
        }

        50% {
            transform: translateY(-10px);
        }

        100% {
            transform: translateY(0px);
        }
    }

    .floating {
        animation: float 3s ease-in-out infinite;
    }

    /* Smooth transitions */
    .transition-all {
        transition-property: all;
        transition-timing-function: cubic-bezier(0.4, 0, 0.2, 1);
        transition-duration: 300ms;
    }

    /* Input focus styles */
    .focused label {
        transform: translateY(-1.5rem);
        font-size: 0.75rem;
        color: #4F46E5;
    }

    /* Custom scrollbar */
    ::-webkit-scrollbar {
        width: 8px;
    }

    ::-webkit-scrollbar-track {
        background: #f1f1f1;
        border-radius: 4px;
    }

    ::-webkit-scrollbar-thumb {
        background: #888;
        border-radius: 4px;
    }

    ::-webkit-scrollbar-thumb:hover {
        background: #555;
    }
</style>

<?php require_once '../components/footer.php'; ?>