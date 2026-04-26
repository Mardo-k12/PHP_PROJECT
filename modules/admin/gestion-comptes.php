<?php
require_once __DIR__ . '/../../config/constants.php';
require_once __DIR__ . '/../../auth/session.php';
require_once __DIR__ . '/../../includes/fonctions.php';

// Vérifier l'accès (Super Admin uniquement)
verifierRole([ROLE_SUPER_ADMIN]);

$user = obtenirUtilisateurConnecte();
?>
<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Gestion des Comptes</title>
    <link rel="stylesheet" href="<?= ROOT_PATH ?>/assets/css/style.css">
</head>
<body>
    <div class="container mt-3">
        <h1>👥 Gestion des Comptes Utilisateurs</h1>
        <p style="color: #666; margin-bottom: 30px;">Cette fonctionnalité sera développée par Prince dans la Partie 3.</p>
        <a href="<?= ROOT_PATH ?>/index.php" class="btn btn-secondary">← Retour</a>
    </div>
</body>
</html>
