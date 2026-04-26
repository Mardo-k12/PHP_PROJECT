<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

/**
 * Vérifie si l'utilisateur est connecté
 */
function estConnecte() {
    return isset($_SESSION['user']) && $_SESSION['user']['actif'] === true;
}

/**
 * Vérifie le rôle de l'utilisateur
 * @param array $rolesAutorises - Tableau des rôles autorisés
 */
function verifierRole($rolesAutorises) {
    if (!estConnecte()) {
        header('Location: ' . ROOT_PATH . '/auth/login.php');
        exit();
    }
    
    $roleUser = $_SESSION['user']['role'];
    if (!in_array($roleUser, $rolesAutorises)) {
        header('HTTP/1.1 403 Forbidden');
        die('<h2>Accès interdit</h2><p>Vous n\'avez pas les droits pour accéder à cette page.</p>');
    }
}

/**
 * Récupère l'utilisateur connecté
 */
function obtenirUtilisateurConnecte() {
    return $_SESSION['user'] ?? null;
}
?>