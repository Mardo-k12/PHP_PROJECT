<?php
require_once __DIR__ . '/../../config/constants.php';
require_once __DIR__ . '/../../auth/session.php';
require_once __DIR__ . '/../../includes/fonctions.php';

verifierRole(['manager', 'super_admin']);

$produits = json_decode(file_get_contents('../../data/produits.json'), true) ?? [];
?>
<!DOCTYPE html>
<html>
<head>
    <meta charset="UTF-8">
    <title>Liste des produits</title>
</head>
<body>
    <h1>Catalogue</h1>
    <ul>
        <?php foreach ($produits as $p): ?>
            <li><?= htmlspecialchars($p['nom']) ?> - <?= $p['prix_unitaire_ht'] ?> CDF</li>
        <?php endforeach; ?>
    </ul>
</body>
</html>