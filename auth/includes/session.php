<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}
function verifierRole($rolesAutorises) {
    if (!isset($_SESSION['user'])) {
        header('Location: ../auth/login.php');
        exit();
    }
    if (!in_array($_SESSION['user']['role'], $rolesAutorises)) {
        die('<h2>Accès interdit</h2>');
    }
}
?>