<footer class="theme-bg-primary theme-text-secondary mt-auto">
    <div class="max-w-7xl mx-auto py-12 px-4 sm:px-6 md:flex md:items-center md:justify-between lg:px-8">
        <div class="flex justify-center space-x-6 md:order-2">
            <a href="#" class="theme-text-secondary hover:text-indigo-500 transition-colors duration-300">
                <span class="sr-only">About</span>
                <span class="text-sm">About</span>
            </a>
            <a href="#" class="theme-text-secondary hover:text-indigo-500 transition-colors duration-300">
                <span class="sr-only">Contact</span>
                <span class="text-sm">Contact</span>
            </a>
            <a href="#" class="theme-text-secondary hover:text-indigo-500 transition-colors duration-300">
                <span class="sr-only">Privacy</span>
                <span class="text-sm">Privacy</span>
            </a>
        </div>
        <div class="mt-8 md:mt-0 md:order-1">
            <p class="text-center text-base theme-text-secondary">
                &copy; <?php echo date('Y'); ?> JifunzeKE. All rights reserved.
            </p>
        </div>
    </div>
</footer>
<script>
    // Profile menu toggle
    const profileButton = document.querySelector('.profile-menu-button');
    const profileMenu = document.querySelector('.profile-menu');
    
    if (profileButton && profileMenu) {
        profileButton.addEventListener('click', () => {
            profileMenu.classList.toggle('hidden');
        });

        // Close menu when clicking outside
        document.addEventListener('click', (event) => {
            if (!profileButton.contains(event.target) && !profileMenu.contains(event.target)) {
                profileMenu.classList.add('hidden');
            }
        });
    }
</script>
</body>
</html> 