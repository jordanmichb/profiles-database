<?php
    // Use PDO to create a connection to the database
    $pdo = new PDO('mysql:host=localhost;port=8889;dbname=registry', 'jordan', 'zap');
    // Enable errors
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
?>