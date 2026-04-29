<?php
/**
 * PAGE D'ACCUEIL - Système de Facturation
 * Simule une session utilisateur pour les tests
 */

session_start();

// Simuler l'authentification pour les tests (à SUPPRIMER en production)
if (!isset($_SESSION['user'])) {
    $_SESSION['user'] = [
        'id' => 2,
        'username' => 'mardoche',
        'email' => 'mardoche@example.com',
        'role' => 'manager',
        'nom' => 'Mardoché'
    ];
}

require_once __DIR__ . '/config/constants.php';
require_once __DIR__ . '/auth/includes/fonctions.php';

$utilisateur = $_SESSION['user'] ?? null;
?>
<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Système de Facturation - Accueil</title>
    <style>
        * { margin: 0; padding: 0; box-sizing: border-box; }
        body { 
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            min-height: 100vh;
            display: flex;
            align-items: center;
            justify-content: center;
            padding: 20px;
        }
        .container { max-width: 1000px; width: 100%; }
        .header {
            background: white;
            border-radius: 15px 15px 0 0;
            padding: 40px;
            text-align: center;
            box-shadow: 0 5px 20px rgba(0,0,0,0.1);
        }
        .header h1 { color: #667eea; font-size: 36px; margin-bottom: 10px; }
        .header p { color: #666; font-size: 16px; }
        .user-info {
            background: #f8f9ff;
            padding: 15px;
            border-radius: 8px;
            margin-top: 20px;
            text-align: left;
            display: inline-block;
        }
        .user-info strong { color: #667eea; }
        .user-info p { margin: 5px 0; color: #333; }
        
        .content {
            background: white;
            padding: 40px;
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(280px, 1fr));
            gap: 20px;
            box-shadow: 0 5px 20px rgba(0,0,0,0.1);
        }
        
        .card {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            color: white;
            padding: 30px;
            border-radius: 10px;
            text-decoration: none;
            transition: transform 0.3s, box-shadow 0.3s;
            cursor: pointer;
            text-align: center;
        }
        .card:hover {
            transform: translateY(-5px);
            box-shadow: 0 10px 30px rgba(102, 126, 234, 0.3);
        }
        .card-icon { font-size: 48px; margin-bottom: 15px; }
        .card h3 { font-size: 20px; margin-bottom: 10px; }
        .card p { font-size: 14px; opacity: 0.9; }
        
        .footer {
            background: white;
            border-radius: 0 0 15px 15px;
            padding: 20px;
            text-align: center;
            color: #666;
            box-shadow: 0 5px 20px rgba(0,0,0,0.1);
            font-size: 12px;
        }
        
        .warning {
            background: #fff3cd;
            border: 2px solid #ffc107;
            color: #856404;
            padding: 15px;
            border-radius: 8px;
            margin-top: 20px;
            text-align: center;
            font-weight: bold;
        }
    </style>
</head>
<body>
    <div class="container">
        <div class="header">
            <h1>🧾 Système de Facturation</h1>
            <p>Lecteur de codes-barres - Gestion de produits et factures</p>
            
            <?php if ($utilisateur): ?>
                <div class="user-info">
                    <p>👤 <strong>Connecté en tant que:</strong> <?= htmlspecialchars($utilisateur['nom'] ?? $utilisateur['username']) ?></p>
                    <p>🎖️ <strong>Rôle:</strong> <span style="background: #667eea; color: white; padding: 3px 8px; border-radius: 3px;"><?= htmlspecialchars($utilisateur['role']) ?></span></p>
                </div>
            <?php endif; ?>
        </div>

        <div class="content">
            <!-- Module Produits -->
            <a href="modules/produits/scanner.php" class="card">
                <div class="card-icon">📷</div>
                <h3>Scanner Produit</h3>
                <p>Ajouter ou modifier un produit avec capture de code-barres</p>
            </a>

            <!-- Liste Produits -->
            <a href="modules/produits/liste.php" class="card">
                <div class="card-icon">📚</div>
                <h3>Catalogue</h3>
                <p>Consulter la liste complète des produits en stock</p>
            </a>

            <!-- Facturation (à venir) -->
            <div class="card" style="opacity: 0.5; cursor: not-allowed;">
                <div class="card-icon">🧾</div>
                <h3>Facturation</h3>
                <p>Module en développement - Bientôt disponible</p>
            </div>

            <!-- Admin (à venir) -->
            <div class="card" style="opacity: 0.5; cursor: not-allowed;">
                <div class="card-icon">⚙️</div>
                <h3>Gestion Comptes</h3>
                <p>Module en développement - Bientôt disponible</p>
            </div>
        </div>

        <div class="warning">
            ⚠️ Mode TEST - Session utilisateur simulée (à supprimer en production)
        </div>

        <div class="footer">
            <p>Système de Facturation - Projet UPC Faculté des Sciences Informatiques</p>
            <p>Équipe: Mardoché | Tsaphnath | Prince</p>
        </div>
    </div>
</body>
</html>
