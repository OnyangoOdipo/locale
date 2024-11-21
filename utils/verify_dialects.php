<?php
require_once '../config/database.php';

try {
    $db = new Database();
    $conn = $db->getConnection();
    
    $stmt = $conn->query("SELECT * FROM dialects");
    $dialects = $stmt->fetchAll(PDO::FETCH_ASSOC);
    
    echo "Successfully retrieved " . count($dialects) . " dialects:\n\n";
    
    foreach ($dialects as $dialect) {
        echo "ID: " . $dialect['id'] . "\n";
        echo "Name: " . $dialect['name'] . "\n";
        echo "Region: " . $dialect['region'] . "\n";
        echo "Description: " . $dialect['description'] . "\n";
        echo "----------------------------------------\n";
    }
    
} catch (Exception $e) {
    echo "Error: " . $e->getMessage() . "\n";
} 