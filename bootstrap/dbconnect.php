<?php

    $dbh = "mysql:host=localhost; dbname=shansocial";
    $username = "root";
    $password = "";
    

    try {
        
        $pdo = new PDO($dbh, $username, $password);
        $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

    } catch (Exception $e) {
        echo "connection error";
        die();
    }
?>