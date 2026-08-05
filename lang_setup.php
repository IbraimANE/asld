<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

if (!isset($_SESSION['lang'])) {
    $_SESSION['lang'] = 'ar';
}

if (isset($_GET['lang']) && in_array($_GET['lang'], ['ar', 'en'])) {
    $_SESSION['lang'] = $_GET['lang'];
    $lang_uri = strtok($_SERVER["REQUEST_URI"], '?');
    header("Location: " . $lang_uri);
    exit();
}

$lang = include __DIR__ . '/languages/' . $_SESSION['lang'] . '.php';