<?php
require_once __DIR__ . '/../../config/constants.php';
require_once __DIR__ . '/../../auth/includes/session.php';
require_once __DIR__ . '/../../auth/includes/fonctions.php';

// Vérifier les droits d'accès
verifierRole([ROLE_MANAGER, ROLE_SUPER_ADMIN]);

// Charger les produits
$produits = lireJSON(PRODUITS_FILE);
if (!is_array($produits)) {
    $produits = [];
}

// Tri par défaut
$tri = $_GET['tri'] ?? 'nom';
$ordre = $_GET['ordre'] ?? 'asc';

// Fonction de tri
usort($produits, function($a, $b) use ($tri, $ordre) {
    $valeur_a = $a[$tri] ?? '';
    $valeur_b = $b[$tri] ?? '';
    
    if ($ordre === 'desc') {
        return $valeur_b <=> $valeur_a;
    }
    return $valeur_a <=> $valeur_b;
});

// Filtrage par statut
$filtre_statut = $_GET['statut'] ?? 'tous';
if ($filtre_statut !== 'tous') {
    $produits = array_filter($produits, function($p) use ($filtre_statut) {
        return ($p['statut'] ?? 'actif') === $filtre_statut;
    });
}

// Statistiques
$total_produits = count($produits);
$valeur_totale_stock = array_reduce($produits, function($carry, $p) {
    $prix_ttc = $p['prix_unitaire_ht'] * (1 + $p['taux_tva'] / 100);
    return $carry + ($prix_ttc * $p['quantite_stock']);
}, 0);
$total_stock = array_reduce($produits, function($carry, $p) {
    return $carry + $p['quantite_stock'];
}, 0);
$produits_expires = count(array_filter($produits, function($p) {
    return strtotime($p['date_expiration']) < time();
}));
?>
<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Catalogue des Produits - Mardoché</title>
    <style>
        * { margin: 0; padding: 0; box-sizing: border-box; }
        body { 
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif; 
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            min-height: 100vh;
            padding: 20px;
        }
        .container { max-width: 1200px; margin: 0 auto; background: white; border-radius: 10px; box-shadow: 0 10px 30px rgba(0,0,0,0.3); padding: 30px; }
        header { text-align: center; margin-bottom: 30px; border-bottom: 3px solid #667eea; padding-bottom: 15px; }
        header h1 { color: #333; font-size: 28px; }
        header p { color: #666; margin-top: 5px; }
        
        .stats { 
            display: grid; 
            grid-template-columns: repeat(auto-fit, minmax(200px, 1fr)); 
            gap: 15px; 
            margin-bottom: 30px; 
        }
        .stat-card { 
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%); 
            color: white; 
            padding: 20px; 
            border-radius: 8px; 
            text-align: center; 
        }
        .stat-value { font-size: 28px; font-weight: bold; }
        .stat-label { font-size: 12px; opacity: 0.9; margin-top: 5px; }
        
        .controls { 
            display: flex; 
            gap: 15px; 
            margin-bottom: 20px; 
            flex-wrap: wrap; 
            align-items: center;
        }
        .controls select, .controls a { 
            padding: 10px 15px; 
            border: 2px solid #ddd; 
            border-radius: 5px; 
            font-size: 14px;
            background: white;
            cursor: pointer;
            text-decoration: none;
            color: #333;
            transition: all 0.3s;
        }
        .controls select:hover, .controls select:focus { border-color: #667eea; }
        .controls a { background: #667eea; color: white; border-color: #667eea; }
        .controls a:hover { background: #764ba2; }
        
        table { 
            width: 100%; 
            border-collapse: collapse; 
            margin-top: 20px; 
        }
        th { 
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%); 
            color: white; 
            padding: 15px; 
            text-align: left; 
            cursor: pointer;
            font-weight: bold;
        }
        th:hover { opacity: 0.9; }
        td { 
            padding: 12px 15px; 
            border-bottom: 1px solid #eee; 
        }
        tr:hover { background: #f9f9f9; }
        
        .code-barre { font-family: 'Courier New'; background: #f0f0f0; padding: 3px 6px; border-radius: 3px; }
        .prix { font-weight: bold; color: #667eea; }
        .stock-bas { color: #dc3545; font-weight: bold; }
        .stock-ok { color: #28a745; }
        .expire { 
            background: #f8d7da; 
            color: #721c24; 
            padding: 8px 12px; 
            border-radius: 3px;
            font-weight: bold;
        }
        .actif { color: #28a745; }
        .inactif { color: #dc3545; }
        
        .actions { 
            display: flex; 
            gap: 10px; 
        }
        .btn { 
            padding: 8px 12px; 
            border: none; 
            border-radius: 4px; 
            cursor: pointer; 
            font-size: 12px; 
            text-decoration: none;
            transition: all 0.3s;
        }
        .btn-modifier { background: #667eea; color: white; }
        .btn-modifier:hover { background: #764ba2; }
        .btn-supprimer { background: #dc3545; color: white; }
        .btn-supprimer:hover { background: #c82333; }
        
        .empty { text-align: center; color: #999; padding: 40px; }
        .action-links { text-align: center; margin-top: 20px; }
        .action-links a { color: #667eea; text-decoration: none; margin: 0 15px; font-weight: bold; }
        .action-links a:hover { text-decoration: underline; }
        
        .alert { padding: 15px; margin-bottom: 20px; border-radius: 5px; }
        .alert-warning { background: #fff3cd; border: 1px solid #ffc107; color: #856404; }
    </style>
</head>
<body>
    <div class="container">
        <header>
            <h1>📦 Catalogue des Produits</h1>
            <p>Module Mardoché - Gestion du stock</p>
        </header>

        <!-- Statistiques -->
        <div class="stats">
            <div class="stat-card">
                <div class="stat-value"><?= $total_produits ?></div>
                <div class="stat-label">Produits enregistrés</div>
            </div>
            <div class="stat-card">
                <div class="stat-value"><?= number_format($total_stock, 0, ',', ' ') ?></div>
                <div class="stat-label">Unités en stock</div>
            </div>
            <div class="stat-card">
                <div class="stat-value"><?= number_format($valeur_totale_stock / 1, 0, ',', ' ') ?></div>
                <div class="stat-label">Valeur du stock (CDF)</div>
            </div>
            <div class="stat-card">
                <div class="stat-value" style="<?= $produits_expires > 0 ? 'color: #ff6b6b;' : '' ?>"><?= $produits_expires ?></div>
                <div class="stat-label">Produits expirés</div>
            </div>
        </div>

        <!-- Alertes -->
        <?php if ($produits_expires > 0): ?>
            <div class="alert alert-warning">
                ⚠️ <strong><?= $produits_expires ?> produit(s) expiré(s)</strong> - À retirer du stock
            </div>
        <?php endif; ?>

        <!-- Contrôles -->
        <div class="controls">
            <select onchange="location.href='?statut=' + this.value">
                <option value="tous" <?= $filtre_statut === 'tous' ? 'selected' : '' ?>>Tous les statuts</option>
                <option value="actif" <?= $filtre_statut === 'actif' ? 'selected' : '' ?>>✓ Actifs</option>
                <option value="inactif" <?= $filtre_statut === 'inactif' ? 'selected' : '' ?>>✗ Inactifs</option>
            </select>
            
            <select onchange="location.href='?tri=' + (this.value.split('-')[0]) + '&ordre=' + (this.value.split('-')[1])">
                <option value="nom-asc" <?= $tri === 'nom' && $ordre === 'asc' ? 'selected' : '' ?>>Trier par nom (A-Z)</option>
                <option value="nom-desc" <?= $tri === 'nom' && $ordre === 'desc' ? 'selected' : '' ?>>Trier par nom (Z-A)</option>
                <option value="prix_unitaire_ht-asc" <?= $tri === 'prix_unitaire_ht' && $ordre === 'asc' ? 'selected' : '' ?>>Trier par prix (croissant)</option>
                <option value="prix_unitaire_ht-desc" <?= $tri === 'prix_unitaire_ht' && $ordre === 'desc' ? 'selected' : '' ?>>Trier par prix (décroissant)</option>
            </select>
            
            <a href="scanner.php">➕ Ajouter un produit</a>
            <a href="liste.php">🔄 Réinitialiser filtres</a>
        </div>

        <!-- Tableau des produits -->
        <?php if (count($produits) > 0): ?>
            <table>
                <thead>
                    <tr>
                        <th>Code-barre</th>
                        <th>Nom</th>
                        <th>Prix HT</th>
                        <th>TVA</th>
                        <th>Stock</th>
                        <th>Date expiration</th>
                        <th>Statut</th>
                        <th>Actions</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($produits as $p): ?>
                        <?php 
                        $expire = strtotime($p['date_expiration']) < time();
                        $stock_bas = $p['quantite_stock'] < 10;
                        ?>
                        <tr>
                            <td><span class="code-barre"><?= htmlspecialchars($p['code_barre']) ?></span></td>
                            <td><?= htmlspecialchars($p['nom']) ?></td>
                            <td><span class="prix"><?= number_format($p['prix_unitaire_ht'], 2, ',', ' ') ?> CDF</span></td>
                            <td><?= $p['taux_tva'] ?? 16 ?>%</td>
                            <td>
                                <span class="<?= $stock_bas ? 'stock-bas' : 'stock-ok' ?>">
                                    <?= $p['quantite_stock'] ?> unités
                                </span>
                            </td>
                            <td>
                                <?php if ($expire): ?>
                                    <span class="expire">❌ <?= htmlspecialchars($p['date_expiration']) ?></span>
                                <?php else: ?>
                                    <?= htmlspecialchars($p['date_expiration']) ?>
                                <?php endif; ?>
                            </td>
                            <td>
                                <span class="<?= ($p['statut'] ?? 'actif') === 'actif' ? 'actif' : 'inactif' ?>">
                                    <?= $p['statut'] ?? 'actif' ?>
                                </span>
                            </td>
                            <td>
                                <div class="actions">
                                    <a href="scanner.php?code=<?= urlencode($p['code_barre']) ?>" class="btn btn-modifier">✏️ Modifier</a>
                                </div>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        <?php else: ?>
            <div class="empty">
                <h2>📭 Aucun produit enregistré</h2>
                <p>Commencez par <a href="scanner.php" style="color: #667eea; text-decoration: underline;">ajouter un produit</a></p>
            </div>
        <?php endif; ?>

        <!-- Liens de navigation -->
        <div class="action-links">
            <a href="scanner.php">🔍 Scanner un produit</a>
            <a href="../..">🏠 Accueil</a>
        </div>
    </div>
</body>
</html>
