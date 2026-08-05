<?php
$host = 'localhost';
$dbname = 'db_association'; // Le nom que tu as donné dans phpMyAdmin
$username = 'root'; 
$password = ''; // Vide par défaut sur XAMPP

try {
    $pdo = new PDO("mysql:host=$host;dbname=$dbname;charset=utf8", $username, $password);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
} catch (PDOException $e) {
    die("Erreur : " . $e->getMessage());
}
?>