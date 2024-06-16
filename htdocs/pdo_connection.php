<?php

$dsn = 'mysql:host=localhost;dbname=promo3';
$user = 'root';
$password = '';

$option = [PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC];

try {
    $pdo = new PDO($dsn, $user, $password, $option);
} catch (PDOException $e) {
    echo 'Connexion échouée : ' . $e->getMessage();
}