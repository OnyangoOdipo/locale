<?php
function renderExercises($exercises) {
    if (empty($exercises)) {
        return '';
    }

    ob_start();
    ?>
    <div class="bg-white rounded-lg shadow-sm p-6 mb-6">
        <h2 class="text-xl font-semibold text-indigo-600 mb-4">Practice Exercises</h2>
        
        <!-- Exercise List (Initially Visible) -->
        <div id="exerciseList" class="space-y-4">
            <?php foreach ($exercises as $index => $exercise): ?>
                <div class="p-4 bg-gray-50 rounded-lg">
                    <div class="flex items-center justify-between mb-2">
                        <span class="px-3 py-1 bg-indigo-100 text-indigo-800 rounded-full text-sm">
                            Exercise <?= $index + 1 ?>: <?= ucfirst($exercise['exercise_type']) ?>
                        </span>
                        <span class="text-sm text-gray-500">Points: <?= $exercise['points'] ?></span>
                    </div>
                    <p class="text-gray-700"><?= htmlspecialchars($exercise['question']) ?></p>
                </div>
            <?php endforeach; ?>
            
            <button onclick="startExercises()" class="mt-6 w-full bg-indigo-600 text-white px-6 py-3 rounded-lg hover:bg-indigo-700 transition-colors">
                Start Practice Exercises →
            </button>
        </div>

        <!-- Exercise Interface (Initially Hidden) -->
        <div id="exerciseInterface" class="hidden">
            <div id="currentExercise" class="mb-6">
                <!-- Exercise content will be inserted here -->
            </div>

            <div id="feedbackArea" class="mb-4 hidden">
                <!-- Feedback will be shown here -->
            </div>

            <div class="flex justify-between items-center">
                <button onclick="previousExercise()" id="prevButton" class="px-4 py-2 bg-gray-200 text-gray-700 rounded-lg hover:bg-gray-300 transition-colors">
                    ← Previous
                </button>
                <div class="flex flex-col items-center">
                    <span id="exerciseProgress" class="text-gray-600 mb-2"></span>
                    <div class="flex items-center space-x-2">
                        <span id="totalPoints" class="text-sm text-gray-500">Total Points: 0</span>
                        <span id="currentStreak" class="text-sm text-green-500">Streak: 0</span>
                    </div>
                </div>
                <div class="flex space-x-2">
                    <button onclick="checkAnswer()" id="checkButton" class="px-4 py-2 bg-green-600 text-white rounded-lg hover:bg-green-700 transition-colors">
                        Check Answer
                    </button>
                    <button onclick="nextExercise()" id="nextButton" class="px-4 py-2 bg-indigo-600 text-white rounded-lg hover:bg-indigo-700 transition-colors hidden">
                        Next →
                    </button>
                </div>
            </div>
        </div>
    </div>

    <script>
        const exercises = <?= json_encode($exercises) ?>;
        let currentExerciseIndex = 0;
        let totalPoints = 0;
        let currentStreak = 0;
        let exerciseAttempts = new Array(exercises.length).fill(0);

        function startExercises() {
            document.getElementById('exerciseList').classList.add('hidden');
            document.getElementById('exerciseInterface').classList.remove('hidden');
            showExercise(0);
        }

        function showExercise(index) {
            const exercise = exercises[index];
            const exerciseHtml = `
                <div class="p-6 bg-gray-50 rounded-lg">
                    <div class="flex justify-between items-center mb-4">
                        <h3 class="text-lg font-medium text-gray-900">
                            Exercise ${index + 1} of ${exercises.length}
                        </h3>
                        <span class="text-sm text-gray-500">Points: ${exercise.points}</span>
                    </div>
                    <p class="text-gray-700 mb-4">${exercise.question}</p>
                    ${renderExerciseContent(exercise)}
                </div>
            `;

            document.getElementById('currentExercise').innerHTML = exerciseHtml;
            document.getElementById('exerciseProgress').textContent = `${index + 1} / ${exercises.length}`;
            document.getElementById('feedbackArea').classList.add('hidden');
            
            document.getElementById('prevButton').disabled = index === 0;
            document.getElementById('prevButton').style.visibility = index === 0 ? 'hidden' : 'visible';
            
            document.getElementById('checkButton').classList.remove('hidden');
            document.getElementById('nextButton').classList.add('hidden');
        }

        function renderExerciseContent(exercise) {
            const options = exercise.options ? JSON.parse(exercise.options) : null;
            
            switch (exercise.exercise_type) {
                case 'multiple_choice':
                    return `
                        <div class="space-y-3" id="answerArea">
                            ${options.map((option, i) => `
                                <label class="flex items-center p-4 bg-white rounded-lg cursor-pointer hover:bg-gray-50 border border-gray-200 transition-colors">
                                    <input type="radio" name="answer" value="${option}" class="mr-3 h-4 w-4 text-indigo-600">
                                    <span class="text-gray-700">${option}</span>
                                </label>
                            `).join('')}
                        </div>
                    `;

                case 'matching':
                    const pairs = JSON.parse(exercise.correct_answer);
                    const keys = Object.keys(pairs);
                    const values = Object.values(pairs);
                    const shuffledValues = values.sort(() => Math.random() - 0.5);
                    
                    return `
                        <div class="grid grid-cols-2 gap-4" id="answerArea">
                            <div class="space-y-2">
                                ${keys.map((key, i) => `
                                    <div class="p-3 bg-white rounded-lg border border-gray-200">
                                        <span class="text-gray-700">${key}</span>
                                    </div>
                                `).join('')}
                            </div>
                            <div class="space-y-2">
                                ${shuffledValues.map((value, i) => `
                                    <div class="flex items-center space-x-2">
                                        <select class="w-full p-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-indigo-500" data-index="${i}">
                                            <option value="">Match with...</option>
                                            ${values.map(v => `
                                                <option value="${v}">${v}</option>
                                            `).join('')}
                                        </select>
                                    </div>
                                `).join('')}
                            </div>
                        </div>
                    `;

                case 'typing':
                case 'translation':
                    return `
                        <div id="answerArea">
                            <input type="text" 
                                   class="w-full p-4 border border-gray-300 rounded-lg focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500" 
                                   placeholder="Type your answer here">
                            <p class="mt-2 text-sm text-gray-500">Press Enter or click Check Answer when ready</p>
                        </div>
                    `;

                case 'dialogue_practice':
                    const dialogue = JSON.parse(exercise.options);
                    return `
                        <div class="space-y-4" id="answerArea">
                            ${dialogue.map((line, i) => `
                                <div class="flex items-start space-x-3">
                                    <span class="font-medium text-gray-700">${line.split(':')[0]}:</span>
                                    ${i === dialogue.length - 1 ? 
                                        `<input type="text" 
                                                class="flex-1 p-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-indigo-500" 
                                                placeholder="Complete the dialogue...">` :
                                        `<p class="text-gray-600">${line.split(':')[1]}</p>`
                                    }
                                </div>
                            `).join('')}
                        </div>
                    `;

                default:
                    return `
                        <div id="answerArea">
                            <textarea 
                                class="w-full p-4 border border-gray-300 rounded-lg focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500" 
                                rows="4" 
                                placeholder="Type your answer here"></textarea>
                        </div>
                    `;
            }
        }

        function checkAnswer() {
            const exercise = exercises[currentExerciseIndex];
            const answerArea = document.getElementById('answerArea');
            const feedbackArea = document.getElementById('feedbackArea');
            let isCorrect = false;
            let userAnswer = '';

            switch (exercise.exercise_type) {
                case 'multiple_choice':
                    const selectedAnswer = answerArea.querySelector('input[name="answer"]:checked')?.value;
                    isCorrect = selectedAnswer === exercise.correct_answer;
                    userAnswer = selectedAnswer;
                    break;

                case 'matching':
                    const pairs = JSON.parse(exercise.correct_answer);
                    const selections = Array.from(answerArea.querySelectorAll('select')).map(select => ({
                        value: select.value,
                        expectedValue: Object.values(pairs)[select.dataset.index]
                    }));
                    isCorrect = selections.every(sel => sel.value === sel.expectedValue);
                    if (isCorrect) {
                        userAnswer = exercise.correct_answer;
                    } else {
                        userAnswer = JSON.stringify(
                            Object.fromEntries(
                                selections.map((sel, i) => [Object.keys(pairs)[i], sel.value])
                            )
                        );
                    }
                    break;

                case 'typing':
                case 'translation':
                    userAnswer = answerArea.querySelector('input').value.trim().toLowerCase();
                    isCorrect = userAnswer === exercise.correct_answer.toLowerCase();
                    break;

                // Add other cases as needed
            }

            // Update attempts and points
            exerciseAttempts[currentExerciseIndex]++;
            if (isCorrect) {
                const pointsEarned = Math.max(1, exercise.points - (exerciseAttempts[currentExerciseIndex] - 1));
                totalPoints += pointsEarned;
                currentStreak++;
                
                showFeedback(true, `Correct! +${pointsEarned} points`);
                document.getElementById('checkButton').classList.add('hidden');
                document.getElementById('nextButton').classList.remove('hidden');
            } else {
                currentStreak = 0;
                showFeedback(false, 'Try again. Hint: ' + getHint(exercise));
            }

            updateStats();
        }

        function showFeedback(isCorrect, message) {
            const feedbackArea = document.getElementById('feedbackArea');
            feedbackArea.className = `mb-4 p-4 rounded-lg ${isCorrect ? 'bg-green-100 text-green-700' : 'bg-red-100 text-red-700'}`;
            feedbackArea.textContent = message;
            feedbackArea.classList.remove('hidden');
        }

        function getHint(exercise) {
            switch (exercise.exercise_type) {
                case 'multiple_choice':
                    return 'Review the options carefully';
                case 'matching':
                    return 'Make sure each pair makes sense';
                case 'typing':
                case 'translation':
                    return `The answer starts with "${exercise.correct_answer.charAt(0)}"`;
                default:
                    return 'Take another look at the lesson material';
            }
        }

        function updateStats() {
            document.getElementById('totalPoints').textContent = `Total Points: ${totalPoints}`;
            document.getElementById('currentStreak').textContent = `Streak: ${currentStreak}`;
        }

        function previousExercise() {
            if (currentExerciseIndex > 0) {
                currentExerciseIndex--;
                showExercise(currentExerciseIndex);
            }
        }

        function nextExercise() {
            if (currentExerciseIndex < exercises.length - 1) {
                currentExerciseIndex++;
                showExercise(currentExerciseIndex);
            } else {
                // Handle exercise completion
                const completionMessage = `
                    <div class="text-center p-6 bg-green-50 rounded-lg">
                        <h3 class="text-xl font-bold text-green-800 mb-2">Congratulations!</h3>
                        <p class="text-green-600 mb-4">You've completed all exercises!</p>
                        <p class="text-gray-700">Total Points: ${totalPoints}</p>
                        <p class="text-gray-700">Best Streak: ${currentStreak}</p>
                        <button onclick="window.location.reload()" class="mt-4 px-6 py-2 bg-green-600 text-white rounded-lg hover:bg-green-700">
                            Review Lesson
                        </button>
                    </div>
                `;
                document.getElementById('exerciseInterface').innerHTML = completionMessage;
            }
        }

        // Add keyboard shortcuts
        document.addEventListener('keydown', function(e) {
            if (e.key === 'Enter' && !e.shiftKey) {
                const checkButton = document.getElementById('checkButton');
                const nextButton = document.getElementById('nextButton');
                
                if (!checkButton.classList.contains('hidden')) {
                    checkAnswer();
                } else if (!nextButton.classList.contains('hidden')) {
                    nextExercise();
                }
            }
        });
    </script>
    <?php
    return ob_get_clean();
}
?> 