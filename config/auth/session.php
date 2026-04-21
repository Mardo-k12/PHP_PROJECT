<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

function estConnecte() {
    return isset($_SESSION['user']) && $_SESSION['user']['actif'] === true;
}

function verifierRole($rolesAutorises) {
    if (!estConnecte()) {
        header('Location: ../auth/login.php');
        exit();
    }
    $roleUser = $_SESSION['user']['role'];
    if (!in_array($roleUser, $rolesAutorises)) {
        die('<h2>Accès interdit</h2><p>Vous n\'avez pas les droits pour accéder à cette page.</p>');
    }
}
?>