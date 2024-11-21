<?php 
// Use absolute path from project root
require_once dirname(__DIR__) . '/components/header.php';
?>

<div class="min-h-screen bg-gray-50 flex flex-col justify-center py-12 sm:px-6 lg:px-8">
    <div class="mt-8 sm:mx-auto sm:w-full sm:max-w-md">
        <div class="bg-white py-8 px-4 shadow sm:rounded-lg sm:px-10 text-center">
            <h2 class="text-3xl font-extrabold text-gray-900 mb-4">
                404 - Page Not Found
            </h2>
            <p class="text-gray-600 mb-6">
                The page you're looking for doesn't exist or has been moved.
            </p>
            <a href="/Locale/pages/dashboard.php" class="inline-flex items-center px-4 py-2 border border-transparent text-sm font-medium rounded-md shadow-sm text-white bg-indigo-600 hover:bg-indigo-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500">
                Return to Dashboard
            </a>
        </div>
    </div>
</div>

<?php require_once dirname(__DIR__) . '/components/footer.php'; ?> 