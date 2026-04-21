<?php
require_once __DIR__ . '/../../config/constants.php';
require_once __DIR__ . '/../../auth/session.php';
require_once __DIR__ . '/../../includes/fonctions.php';

verifierRole([ROLE_MANAGER, ROLE_SUPER_ADMIN]);

$produits = lireJSON(PRODUITS_FILE);
?>
<!DOCTYPE html>
<html>
<head>
    <meta charset="UTF-8">
    <title>Liste des produits</title>
    <style>
        table { border-collapse: collapse; width: 100%; }
        th, td { border: 1px solid #ccc; padding: 8px; text-align: left; }
        th { background: #eee; }
    </style>
</head>
<body>
    <h1>Catalogue produits</h1>
    <table>
        <tr>
            <th>Code-barres</th>
            <th>Nom</th>
            <th>Prix HT (CDF)</th>
            <th>Expiration</th>
            <th>Stock</th>
            <th>Date enregistrement</th>
        </tr>
        <?php foreach ($produits as $p): ?>
        <tr>
            <td><?= htmlspecialchars($p['code_barre']) ?></td>
            <td><?= htmlspecialchars($p['nom']) ?></td>
            <td><?= htmlspecialchars($p['prix_unitaire_ht']) ?></td>
            <td><?= htmlspecialchars($p['date_expiration']) ?></td>
            <td><?= htmlspecialchars($p['quantite_stock']) ?></td>
            <td><?= htmlspecialchars($p['date_enregistrement']) ?></td>
        </tr>
        <?php endforeach; ?>
    </table>
    <p><a href="scanner.php">Scanner un nouveau produit</a></p>
</body>
</html>