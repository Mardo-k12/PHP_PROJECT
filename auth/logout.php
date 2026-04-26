<?php
require_once __DIR__ . '/../config/constants.php';

if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

// Détruire la session
session_destroy();

// Rediriger vers la page de connexion
header('Location: ' . ROOT_PATH . '/auth/login.php');
exit();
?>
