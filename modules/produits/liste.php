<?php
require_once __DIR__ . '/../../config/constants.php';
require_once __DIR__ . '/../../auth/session.php';
require_once __DIR__ . '/../../includes/fonctions.php';
require_once __DIR__ . '/../../includes/fonctions-produits.php';

// Vérifier les rôles autorisés (Manager et Super Admin)
verifierRole([ROLE_MANAGER, ROLE_SUPER_ADMIN]);

$produits = obtenirTousLesProduits(PRODUITS_FILE);
$utilisateur = obtenirUtilisateurConnecte();

// Tri
$tri = $_GET['tri'] ?? 'nom';
$ordre = $_GET['ordre'] ?? 'asc';

usort($produits, function($a, $b) use ($tri, $ordre) {
    $cmp = 0;
    
    switch($tri) {
        case 'prix':
            $cmp = $a['prix_unitaire_ht'] - $b['prix_unitaire_ht'];
            break;
        case 'stock':
            $cmp = $a['quantite_stock'] - $b['quantite_stock'];
            break;
        case 'code':
            $cmp = strcmp($a['code_barre'], $b['code_barre']);
            break;
        default: // nom
            $cmp = strcmp($a['nom'], $b['nom']);
    }
    
    return $ordre === 'desc' ? -$cmp : $cmp;
});
?>
<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Catalogue des Produits</title>
    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }
        
        body {
            font-family: Arial, sans-serif;
            background-color: #f5f5f5;
            padding: 20px;
        }
        
        .container {
            max-width: 1200px;
            margin: 0 auto;
        }
        
        .header {
            background: white;
            padding: 20px;
            border-radius: 8px;
            box-shadow: 0 2px 4px rgba(0,0,0,0.1);
            margin-bottom: 30px;
            display: flex;
            justify-content: space-between;
            align-items: center;
            flex-wrap: wrap;
            gap: 20px;
        }
        
        .header h1 {
            color: #333;
            flex: 1;
            min-width: 200px;
        }
        
        .user-info {
            background: #f8f9fa;
            padding: 10px 15px;
            border-radius: 4px;
            font-size: 14px;
            color: #666;
        }
        
        .actions {
            display: flex;
            gap: 10px;
            flex-wrap: wrap;
        }
        
        button, a.btn {
            padding: 10px 20px;
            border: none;
            border-radius: 4px;
            cursor: pointer;
            text-decoration: none;
            display: inline-block;
            font-size: 14px;
            transition: background-color 0.3s;
        }
        
        .btn-primary {
            background-color: #007bff;
            color: white;
        }
        
        .btn-primary:hover {
            background-color: #0056b3;
        }
        
        .btn-secondary {
            background-color: #6c757d;
            color: white;
        }
        
        .btn-secondary:hover {
            background-color: #545b62;
        }
        
        .stats {
            background: white;
            padding: 20px;
            border-radius: 8px;
            box-shadow: 0 2px 4px rgba(0,0,0,0.1);
            margin-bottom: 30px;
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(150px, 1fr));
            gap: 20px;
        }
        
        .stat-item {
            text-align: center;
            padding: 15px;
            background: #f8f9fa;
            border-radius: 4px;
        }
        
        .stat-value {
            font-size: 24px;
            font-weight: bold;
            color: #007bff;
        }
        
        .stat-label {
            font-size: 12px;
            color: #666;
            margin-top: 5px;
        }
        
        .table-container {
            background: white;
            border-radius: 8px;
            box-shadow: 0 2px 4px rgba(0,0,0,0.1);
            overflow: hidden;
        }
        
        table {
            width: 100%;
            border-collapse: collapse;
        }
        
        thead {
            background-color: #f8f9fa;
            border-bottom: 2px solid #ddd;
        }
        
        th {
            padding: 15px;
            text-align: left;
            font-weight: bold;
            color: #333;
            cursor: pointer;
            user-select: none;
        }
        
        th:hover {
            background-color: #e9ecef;
        }
        
        td {
            padding: 12px 15px;
            border-bottom: 1px solid #eee;
        }
        
        tbody tr:hover {
            background-color: #f8f9fa;
        }
        
        .sort-indicator {
            margin-left: 5px;
            font-size: 12px;
        }
        
        .code-badge {
            background: #e7f3ff;
            padding: 5px 10px;
            border-radius: 4px;
            font-size: 12px;
            font-family: monospace;
        }
        
        .price {
            font-weight: bold;
            color: #28a745;
        }
        
        .stock {
            text-align: center;
        }
        
        .stock.low {
            color: #dc3545;
            font-weight: bold;
        }
        
        .actions-cell {
            display: flex;
            gap: 5px;
            flex-wrap: wrap;
        }
        
        .actions-cell a {
            padding: 5px 10px;
            font-size: 12px;
            text-decoration: none;
            border-radius: 4px;
            display: inline-block;
        }
        
        .btn-edit {
            background-color: #ffc107;
            color: black;
        }
        
        .btn-edit:hover {
            background-color: #e0a800;
        }
        
        .btn-view {
            background-color: #17a2b8;
            color: white;
        }
        
        .btn-view:hover {
            background-color: #138496;
        }
        
        .empty-state {
            text-align: center;
            padding: 60px 20px;
            color: #999;
        }
        
        .empty-state h2 {
            margin-bottom: 10px;
            color: #666;
        }
        
        .empty-state p {
            margin-bottom: 20px;
        }
        
        @media (max-width: 768px) {
            .header {
                flex-direction: column;
                align-items: flex-start;
            }
            
            .actions {
                width: 100%;
            }
            
            table {
                font-size: 12px;
            }
            
            td, th {
                padding: 8px 5px;
            }
            
            .actions-cell {
                flex-direction: column;
            }
        }
    </style>
</head>
<body>
    <div class="container">
        <!-- En-tête -->
        <div class="header">
            <h1>📦 Catalogue des Produits</h1>
            <div class="user-info">
                👤 <?= echapper($utilisateur['nom_complet']) ?> (<?= echapper($utilisateur['role']) ?>)
            </div>
            <div class="actions">
                <a href="scanner.php" class="btn btn-primary">➕ Nouveau Produit</a>
                <a href="<?= ROOT_PATH ?>/index.php" class="btn btn-secondary">← Accueil</a>
            </div>
        </div>
        
        <?php if (!empty($produits)): ?>
            <!-- Statistiques -->
            <div class="stats">
                <div class="stat-item">
                    <div class="stat-value"><?= count($produits) ?></div>
                    <div class="stat-label">Produits</div>
                </div>
                <div class="stat-item">
                    <div class="stat-value"><?= array_sum(array_map(function($p) { return $p['quantite_stock']; }, $produits)) ?></div>
                    <div class="stat-label">Articles en stock</div>
                </div>
                <div class="stat-item">
                    <div class="stat-value">
                        <?= count(array_filter($produits, function($p) { return $p['quantite_stock'] <= 5; })) ?>
                    </div>
                    <div class="stat-label">Stock faible</div>
                </div>
            </div>
            
            <!-- Tableau -->
            <div class="table-container">
                <table>
                    <thead>
                        <tr>
                            <th>
                                <a href="?tri=code&ordre=<?= $tri === 'code' && $ordre === 'asc' ? 'desc' : 'asc' ?>" 
                                   style="text-decoration: none; color: inherit;">
                                    Code-barres
                                    <?php if ($tri === 'code'): ?>
                                        <span class="sort-indicator"><?= $ordre === 'asc' ? '↑' : '↓' ?></span>
                                    <?php endif; ?>
                                </a>
                            </th>
                            <th>
                                <a href="?tri=nom&ordre=<?= $tri === 'nom' && $ordre === 'asc' ? 'desc' : 'asc' ?>"
                                   style="text-decoration: none; color: inherit;">
                                    Nom
                                    <?php if ($tri === 'nom'): ?>
                                        <span class="sort-indicator"><?= $ordre === 'asc' ? '↑' : '↓' ?></span>
                                    <?php endif; ?>
                                </a>
                            </th>
                            <th>
                                <a href="?tri=prix&ordre=<?= $tri === 'prix' && $ordre === 'asc' ? 'desc' : 'asc' ?>"
                                   style="text-decoration: none; color: inherit;">
                                    Prix HT
                                    <?php if ($tri === 'prix'): ?>
                                        <span class="sort-indicator"><?= $ordre === 'asc' ? '↑' : '↓' ?></span>
                                    <?php endif; ?>
                                </a>
                            </th>
                            <th>Expiration</th>
                            <th>
                                <a href="?tri=stock&ordre=<?= $tri === 'stock' && $ordre === 'asc' ? 'desc' : 'asc' ?>"
                                   style="text-decoration: none; color: inherit;">
                                    Stock
                                    <?php if ($tri === 'stock'): ?>
                                        <span class="sort-indicator"><?= $ordre === 'asc' ? '↑' : '↓' ?></span>
                                    <?php endif; ?>
                                </a>
                            </th>
                            <th>Enregistrement</th>
                            <th>Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($produits as $p): ?>
                            <?php 
                            $estExpire = strtotime($p['date_expiration']) < time();
                            $stockBas = $p['quantite_stock'] <= 5;
                            ?>
                            <tr <?= $estExpire ? 'style="opacity: 0.6;"' : '' ?>>
                                <td>
                                    <span class="code-badge"><?= echapper($p['code_barre']) ?></span>
                                </td>
                                <td><?= echapper($p['nom']) ?></td>
                                <td class="price"><?= formatPrix($p['prix_unitaire_ht']) ?></td>
                                <td>
                                    <span <?= $estExpire ? 'style="color: red; font-weight: bold;"' : '' ?>>
                                        <?= formatDate($p['date_expiration']) ?>
                                        <?= $estExpire ? '⚠️ EXPIRÉ' : '' ?>
                                    </span>
                                </td>
                                <td class="stock <?= $stockBas ? 'low' : '' ?>">
                                    <?= $p['quantite_stock'] ?>
                                    <?= $stockBas ? '⚠️' : '' ?>
                                </td>
                                <td><?= formatDate($p['date_enregistrement']) ?></td>
                                <td>
                                    <div class="actions-cell">
                                        <a href="scanner.php?code=<?= urlencode($p['code_barre']) ?>" class="btn-edit">✏️ Modifier</a>
                                    </div>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            </div>
        <?php else: ?>
            <div class="table-container">
                <div class="empty-state">
                    <h2>📦 Aucun produit enregistré</h2>
                    <p>Le catalogue est vide. Commencez par enregistrer les premiers produits.</p>
                    <a href="scanner.php" class="btn btn-primary">➕ Ajouter un produit</a>
                </div>
            </div>
        <?php endif; ?>
    </div>
</body>
</html>
