<?php
function renderLessonCard($lesson) {
?>
    <div class="bg-white rounded-lg shadow-sm hover:shadow-md transition-shadow duration-300 overflow-hidden">
        <div class="p-6">
            <div class="flex items-center justify-between">
                <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium
                    <?php echo $lesson['difficulty_level'] === 'beginner' ? 'bg-green-100 text-green-800' : 
                        ($lesson['difficulty_level'] === 'intermediate' ? 'bg-yellow-100 text-yellow-800' : 'bg-red-100 text-red-800'); ?>">
                    <?php echo ucfirst($lesson['difficulty_level']); ?>
                </span>
                <span class="text-sm text-gray-500"><?php echo $lesson['dialect_name']; ?></span>
            </div>
            <h3 class="mt-4 text-lg font-semibold text-gray-900"><?php echo $lesson['title']; ?></h3>
            <p class="mt-2 text-sm text-gray-600 line-clamp-2"><?php echo $lesson['description']; ?></p>
            <div class="mt-4 flex items-center justify-between">
                <div class="flex items-center space-x-2">
                    <div class="w-full bg-gray-200 rounded-full h-2.5">
                        <div class="bg-indigo-600 h-2.5 rounded-full" style="width: <?php echo $lesson['progress']; ?>%"></div>
                    </div>
                    <span class="text-sm text-gray-600"><?php echo $lesson['progress']; ?>%</span>
                </div>
                <a href="/pages/lesson.php?id=<?php echo $lesson['id']; ?>" 
                   class="inline-flex items-center px-3 py-2 border border-transparent text-sm leading-4 font-medium rounded-md text-indigo-700 bg-indigo-100 hover:bg-indigo-200 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500">
                    Start Learning
                </a>
            </div>
        </div>
    </div>
<?php
}
?> 