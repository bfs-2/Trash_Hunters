<?php

// config/database.php
$db_host = 'localhost';
$db_name = 'hospital_bd';
$db_user = 'root';
$db_pass = '0000';

try {
    $conn = new PDO("mysql:host=$db_host;dbname=$db_name", $db_user, $db_pass);
    $conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
} catch (PDOException $e) {
    die("Connection failed: " . $e->getMessage());
}

?>