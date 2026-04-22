<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}
function verifierRole($rolesAutorises) {
    if (!isset($_SESSION['user'])) {
        header('Location: ' . ROOT_PATH . '/auth/login.php');
        exit();
    }
    if (!in_array($_SESSION['user']['role'], $rolesAutorises)) {
        die('<h2>Accès interdit</h2><p>Vous n\'avez pas les droits nécessaires.</p>');
    }
}
?>