<?php
require_once '../config/database.php';

try {
    $db = new Database();
    $conn = $db->getConnection();
    
    // Get Kikuyu dialect ID
    $stmt = $conn->prepare("SELECT id FROM dialects WHERE name = 'Kikuyu' LIMIT 1");
    $stmt->execute();
    $kikuyu = $stmt->fetch(PDO::FETCH_ASSOC);
    
    if (!$kikuyu) {
        throw new Exception('Kikuyu dialect not found. Please insert dialects first.');
    }
    
    $kikuyu_id = $kikuyu['id'];
    
    // Insert Lesson 1
    $lesson1_content = [
        'introduction' => 'Welcome to Kikuyu Language! Kikuyu (Gĩkũyũ) is spoken by the Kikuyu people of Kenya.',
        'basics' => [
            'greetings' => [
                'good_morning' => 'Ũhoro wa rũciinĩ',
                'good_afternoon' => 'Ũhoro wa mũthenya',
                'good_evening' => 'Ũhoro wa hwaĩ'
            ],
            'responses' => [
                'im_fine' => 'Nĩ mwega',
                'thank_you' => 'Nĩ ndakena'
            ]
        ],
        'pronunciation_guide' => [
            'ũ' => 'like \'oo\' in \'boot\'',
            'ĩ' => 'like \'ee\' in \'feet\'',
            'ng' => 'like ng in \'sing\''
        ],
        'practice_dialogue' => [
            'greeting' => 'A: Ũhoro waku? (How are you?)',
            'response' => 'B: Nĩ mwega (I am fine)',
            'thank_you' => 'A: Nĩ ndakena (Thank you)'
        ]
    ];

    $stmt = $conn->prepare("
        INSERT INTO lessons (dialect_id, title, difficulty_level, content) 
        VALUES (?, ?, ?, ?)
    ");

    $result = $stmt->execute([
        $kikuyu_id,
        'Introduction to Kikuyu Language',
        'beginner',
        json_encode($lesson1_content)
    ]);

    if ($result) {
        echo "Lesson 1 inserted successfully\n";
    }

    // Continue with other lessons...
    
} catch (Exception $e) {
    echo "Error: " . $e->getMessage() . "\n";
} 