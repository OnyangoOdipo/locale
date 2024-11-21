<?php 
require_once '../components/header.php';
require_once '../config/database.php';
if (isset($_SESSION['user_id'])) {
    header('Location: /pages/dashboard.php');
    exit();
}

// Add after session_start()
if (empty($_SESSION['csrf_token'])) {
    $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
}
?>

<div class="min-h-screen bg-gradient-to-br from-indigo-100 via-white to-pink-100 py-12 px-4 sm:px-6 lg:px-8">
    <div class="max-w-2xl mx-auto">
        <!-- Logo and Title -->
        <div class="text-center mb-8">
            <div class="flex justify-center">
                <img class="h-16 w-auto floating" src="/assets/images/logo.svg" alt="JifunzeKE">
            </div>
            <h2 class="mt-6 text-3xl font-extrabold text-gray-900 tracking-tight">
                Start Your Learning Journey
            </h2>
            <p class="mt-2 text-sm text-gray-600">
                Join our community and discover the beauty of Kenyan dialects
            </p>
        </div>

        <!-- Registration Form -->
        <div class="bg-white/80 backdrop-blur-lg rounded-2xl shadow-xl overflow-hidden hover:shadow-2xl transition-all duration-300">
            <div class="px-8 py-10">
                <?php if(isset($_SESSION['error'])): ?>
                    <div class="mb-6 p-4 bg-red-50 border-l-4 border-red-500 text-red-700 animate-fade-in">
                        <?php 
                        echo $_SESSION['error'];
                        unset($_SESSION['error']);
                        ?>
                    </div>
                <?php endif; ?>

                <form id="registrationForm" class="space-y-6" method="POST" action="/Locale/includes/register_process.php" enctype="multipart/form-data">
                    <input type="hidden" name="csrf_token" value="<?php echo $_SESSION['csrf_token']; ?>">
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                        <!-- Left Column -->
                        <div class="space-y-6">
                            <!-- Profile Picture Upload -->
                            <div class="flex flex-col items-center">
                                <div class="relative group">
                                    <div class="profile-preview h-32 w-32 rounded-full border-4 border-white shadow-lg overflow-hidden">
                                        <img id="profilePreview" src="/assets/images/default-avatar.png" 
                                             class="h-full w-full object-cover transition-transform duration-300 group-hover:scale-110">
                                    </div>
                                    <div class="absolute inset-0 flex items-center justify-center opacity-0 group-hover:opacity-100 transition-opacity duration-300">
                                        <label for="profileUpload" class="cursor-pointer bg-black/50 rounded-full p-2 hover:bg-black/70 transition-colors duration-300">
                                            <svg class="h-6 w-6 text-white" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 9a2 2 0 012-2h.93a2 2 0 001.664-.89l.812-1.22A2 2 0 0110.07 4h3.86a2 2 0 011.664.89l.812 1.22A2 2 0 0018.07 7H19a2 2 0 012 2v9a2 2 0 01-2 2H5a2 2 0 01-2-2V9z"/>
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 13a3 3 0 11-6 0 3 3 0 016 0z"/>
                                            </svg>
                                        </label>
                                    </div>
                                    <input type="file" id="profileUpload" name="profile_picture" accept="image/*" class="hidden">
                                </div>
                                <p class="mt-2 text-sm text-gray-500">Upload your profile picture</p>
                            </div>

                            <!-- Basic Info -->
                            <div class="form-group">
                                <label class="block text-sm font-medium text-gray-700">Email</label>
                                <div class="mt-1 relative rounded-xl shadow-sm">
                                    <input type="email" name="email" required
                                        value="<?php echo htmlspecialchars($_SESSION['form_data']['email'] ?? ''); ?>"
                                        class="pl-10 block w-full px-4 py-3 border border-gray-300 rounded-xl 
                                               placeholder-gray-400 focus:outline-none focus:ring-2 focus:ring-indigo-500 
                                               focus:border-transparent transition-all duration-300"
                                        placeholder="you@example.com">
                                    <div class="absolute inset-y-0 left-3 flex items-center pointer-events-none">
                                        <svg class="h-5 w-5 text-gray-400" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 12a4 4 0 10-8 0 4 4 0 008 0zm0 0v1.5a2.5 2.5 0 005 0V12a9 9 0 10-9 9m4.5-1.206a8.959 8.959 0 01-4.5 1.207"/>
                                        </svg>
                                    </div>
                                </div>
                            </div>

                            <div class="form-group">
                                <label class="block text-sm font-medium text-gray-700">Username</label>
                                <div class="mt-1 relative rounded-xl shadow-sm">
                                    <input type="text" name="username" required
                                        value="<?php echo htmlspecialchars($_SESSION['form_data']['username'] ?? ''); ?>"
                                        class="pl-10 block w-full px-4 py-3 border border-gray-300 rounded-xl 
                                               placeholder-gray-400 focus:outline-none focus:ring-2 focus:ring-indigo-500 
                                               focus:border-transparent transition-all duration-300"
                                        placeholder="Choose a username">
                                    <div class="absolute inset-y-0 left-3 flex items-center pointer-events-none">
                                        <svg class="h-5 w-5 text-gray-400" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"/>
                                        </svg>
                                    </div>
                                </div>
                            </div>

                            <div class="form-group">
                                <label class="block text-sm font-medium text-gray-700">Password</label>
                                <div class="mt-1 relative rounded-xl shadow-sm">
                                    <input type="password" name="password" required id="password"
                                        class="pl-10 block w-full px-4 py-3 border border-gray-300 rounded-xl 
                                               placeholder-gray-400 focus:outline-none focus:ring-2 focus:ring-indigo-500 
                                               focus:border-transparent transition-all duration-300"
                                        placeholder="Create a strong password">
                                    <div class="absolute inset-y-0 left-3 flex items-center pointer-events-none">
                                        <svg class="h-5 w-5 text-gray-400" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z"/>
                                        </svg>
                                    </div>
                                    <button type="button" class="toggle-password absolute inset-y-0 right-3 flex items-center">
                                        <svg class="h-5 w-5 text-gray-400 hover:text-gray-600" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/>
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"/>
                                        </svg>
                                    </button>
                                </div>
                                <div class="mt-2">
                                    <div class="password-strength-bar h-1.5 w-full bg-gray-200 rounded-full overflow-hidden">
                                        <div class="password-strength-fill h-full transition-all duration-300"></div>
                                    </div>
                                    <p class="password-strength-text mt-1 text-xs text-gray-500"></p>
                                </div>
                            </div>
                        </div>

                        <!-- Right Column -->
                        <div class="space-y-6">
                            <div class="form-group">
                                <label class="block text-sm font-medium text-gray-700 mb-3">What brings you here?</label>
                                <div class="space-y-3">
                                    <?php
                                    $motivations = [
                                        'cultural' => ['Cultural Connection', 'Learn about Kenyan culture and heritage'],
                                        'business' => ['Business Communication', 'Communicate better in business settings'],
                                        'personal' => ['Personal Interest', 'Personal growth and curiosity'],
                                        'academic' => ['Academic Purpose', 'Research and academic studies']
                                    ];
                                    foreach ($motivations as $value => $details): ?>
                                        <label class="motivation-option flex items-center p-3 border rounded-xl cursor-pointer hover:bg-indigo-50 transition-all duration-300">
                                            <input type="radio" name="motivation" value="<?php echo $value; ?>" 
                                                   class="h-4 w-4 text-indigo-600 border-gray-300 focus:ring-indigo-500" required>
                                            <div class="ml-3 flex-1">
                                                <span class="block text-sm font-medium text-gray-900"><?php echo $details[0]; ?></span>
                                                <span class="block text-xs text-gray-500"><?php echo $details[1]; ?></span>
                                            </div>
                                            <div class="check-circle text-indigo-600 ml-3">
                                                <svg class="w-5 h-5" fill="currentColor" viewBox="0 0 20 20">
                                                    <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"/>
                                                </svg>
                                            </div>
                                        </label>
                                    <?php endforeach; ?>
                                </div>
                            </div>

                            <div class="form-group">
                                <label class="block text-sm font-medium text-gray-700 mb-3">Select dialects you want to learn</label>
                                <div class="space-y-3 max-h-48 overflow-y-auto pr-2">
                                    <?php
                                    $db = new Database();
                                    $conn = $db->getConnection();
                                    $stmt = $conn->query("SELECT * FROM dialects");
                                    while ($dialect = $stmt->fetch(PDO::FETCH_ASSOC)):
                                    ?>
                                    <label class="dialect-option flex items-center p-3 border rounded-xl cursor-pointer hover:bg-indigo-50 transition-all duration-300">
                                        <input type="checkbox" name="dialects[]" value="<?php echo $dialect['id']; ?>" 
                                               class="h-4 w-4 text-indigo-600 border-gray-300 rounded focus:ring-indigo-500">
                                        <div class="ml-3 flex-1">
                                            <span class="block text-sm font-medium text-gray-900">
                                                <?php echo htmlspecialchars($dialect['name']); ?>
                                            </span>
                                            <span class="block text-xs text-gray-500">
                                                <?php echo htmlspecialchars($dialect['region']); ?>
                                            </span>
                                        </div>
                                    </label>
                                    <?php endwhile; ?>
                                </div>
                            </div>

                            <div class="form-group">
                                <label class="block text-sm font-medium text-gray-700 mb-3">Daily learning goal</label>
                                <div class="relative">
                                    <input type="range" name="daily_goal" min="10" max="60" step="10" value="20"
                                           class="w-full h-2 bg-gray-200 rounded-lg appearance-none cursor-pointer">
                                    <div class="flex justify-between text-xs text-gray-600 mt-2">
                                        <span>10 min</span>
                                        <span>20 min</span>
                                        <span>30 min</span>
                                        <span>40 min</span>
                                        <span>50 min</span>
                                        <span>60 min</span>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Submit Button -->
                    <div class="mt-8">
                        <button type="submit" name="submit"
                            class="w-full flex justify-center py-3 px-4 border border-transparent rounded-xl shadow-sm text-sm font-medium text-white bg-indigo-600 hover:bg-indigo-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500 transition-all duration-300 transform hover:scale-[1.02]">
                            Create Account
                        </button>
                    </div>
                </form>

                <!-- Sign In Link -->
                <div class="mt-6 text-center">
                    <p class="text-sm text-gray-600">
                        Already have an account?
                        <a href="../pages/login.php" class="font-medium text-indigo-600 hover:text-indigo-500 transition-colors duration-300">
                            Sign in instead
                        </a>
                    </p>
                </div>
            </div>
        </div>
    </div>
</div>

<style>
/* Your existing styles */
.motivation-option input:checked + div {
    @apply text-indigo-600;
}

.motivation-option input:checked ~ .check-circle {
    @apply block;
}

.dialect-option input:checked + div {
    @apply text-indigo-600;
}

.dialect-option input:checked ~ .checkbox-custom {
    @apply bg-indigo-600 border-indigo-600;
}

.dialect-option input:checked ~ .checkbox-custom::after {
    content: '';
    @apply absolute inset-0 flex items-center justify-center text-white;
    background-image: url("data:image/svg+xml,%3Csvg xmlns='http://www.w3.org/2000/svg' viewBox='0 0 20 20' fill='white'%3E%3Cpath fill-rule='evenodd' d='M16.707 5.293a1 1 0 010 1.414l-8 8a1 1 0 01-1.414 0l-4-4a1 1 0 011.414-1.414L8 12.586l7.293-7.293a1 1 0 011.414 0z' clip-rule='evenodd'/%3E%3C/svg%3E");
}

/* Password strength indicator */
.password-strength-bar {
    @apply bg-gray-200 h-1 rounded-full overflow-hidden;
}

.password-strength-fill {
    @apply h-full transition-all duration-300;
    width: 0;
}
</style>

<script>
document.addEventListener('DOMContentLoaded', () => {
    // Profile picture preview
    const profileUpload = document.getElementById('profileUpload');
    const profilePreview = document.getElementById('profilePreview');

    profileUpload.addEventListener('change', (e) => {
        const file = e.target.files[0];
        if (file) {
            const reader = new FileReader();
            reader.onload = (e) => {
                profilePreview.src = e.target.result;
            };
            reader.readAsDataURL(file);
        }
    });

    // Password visibility toggle
    const togglePasswordBtn = document.querySelector('.toggle-password');
    const passwordInput = document.getElementById('password');
    
    togglePasswordBtn.addEventListener('click', () => {
        const type = passwordInput.getAttribute('type') === 'password' ? 'text' : 'password';
        passwordInput.setAttribute('type', type);
        
        togglePasswordBtn.innerHTML = type === 'password' ? `
            <svg class="h-5 w-5 text-gray-400 hover:text-gray-600" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/>
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"/>
            </svg>
        ` : `
            <svg class="h-5 w-5 text-gray-400 hover:text-gray-600" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13.875 18.825A10.05 10.05 0 0112 19c-4.478 0-8.268-2.943-9.543-7a9.97 9.97 0 011.563-3.029m5.858.908a3 3 0 114.243 4.243M9.878 9.878l4.242 4.242M9.88 9.88l-3.29-3.29m7.532 7.532l3.29 3.29M3 3l3.59 3.59m0 0A9.953 9.953 0 0112 5c4.478 0 8.268 2.943 9.543 7a10.025 10.025 0 01-4.132 5.411m0 0L21 21"/>
            </svg>
        `;
    });

    // Password strength indicator
    passwordInput.addEventListener('input', () => {
        const password = passwordInput.value;
        const strength = calculatePasswordStrength(password);
        const strengthFill = document.querySelector('.password-strength-fill');
        const strengthText = document.querySelector('.password-strength-text');
        
        strengthFill.style.width = `${strength}%`;
        
        if (strength < 25) {
            strengthFill.style.backgroundColor = '#EF4444';
            strengthText.textContent = 'Weak password';
        } else if (strength < 50) {
            strengthFill.style.backgroundColor = '#F59E0B';
            strengthText.textContent = 'Fair password';
        } else if (strength < 75) {
            strengthFill.style.backgroundColor = '#10B981';
            strengthText.textContent = 'Good password';
        } else {
            strengthFill.style.backgroundColor = '#059669';
            strengthText.textContent = 'Strong password';
        }
    });
});

function calculatePasswordStrength(password) {
    let score = 0;
    
    // Length check
    if (password.length >= 8) score += 25;
    
    // Complexity checks
    if (password.match(/[a-z]/) && password.match(/[A-Z]/)) score += 25;
    if (password.match(/\d/)) score += 25;
    if (password.match(/[^a-zA-Z\d]/)) score += 25;
    
    return score;
}

// For motivation radio buttons
document.querySelectorAll('.motivation-option').forEach(option => {
    option.addEventListener('click', function() {
        // Find the radio input within this option
        const radio = this.querySelector('input[type="radio"]');
        radio.checked = true;
        
        // Remove selected class from all options
        document.querySelectorAll('.motivation-option').forEach(opt => {
            opt.classList.remove('bg-indigo-50', 'border-indigo-500');
        });
        
        // Add selected class to clicked option
        this.classList.add('bg-indigo-50', 'border-indigo-500');
    });
});

// For dialect checkboxes
document.querySelectorAll('.dialect-option').forEach(option => {
    option.addEventListener('click', function() {
        // Find the checkbox input within this option
        const checkbox = this.querySelector('input[type="checkbox"]');
        checkbox.checked = !checkbox.checked;
        
        // Toggle selected class
        if (checkbox.checked) {
            this.classList.add('bg-indigo-50', 'border-indigo-500');
        } else {
            this.classList.remove('bg-indigo-50', 'border-indigo-500');
        }
    });
});
</script>

<?php unset($_SESSION['form_data']); ?>

<?php require_once '../components/footer.php'; ?> 