<?php
require_once __DIR__ . '/config/constants.php';
require_once __DIR__ . '/auth/session.php';
require_once __DIR__ . '/includes/fonctions.php';

// Si non connecté, rediriger vers login
if (!estConnecte()) {
    header('Location: ' . ROOT_PATH . '/auth/login.php');
    exit();
}

$user = obtenirUtilisateurConnecte();
?>
<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Tableau de Bord - Système de Facturation</title>
    <link rel="stylesheet" href="<?= ROOT_PATH ?>/assets/css/style.css">
    <style>
        .dashboard {
            padding: 40px 20px;
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            min-height: 100vh;
            display: flex;
            flex-direction: column;
            justify-content: center;
            align-items: center;
        }
        
        .dashboard-container {
            background: white;
            border-radius: 12px;
            box-shadow: 0 10px 40px rgba(0,0,0,0.2);
            padding: 40px;
            max-width: 800px;
            width: 100%;
        }
        
        .dashboard-title {
            font-size: 2.5rem;
            color: #333;
            margin-bottom: 10px;
            text-align: center;
        }
        
        .welcome-message {
            text-align: center;
            font-size: 1.1rem;
            color: #666;
            margin-bottom: 40px;
        }
        
        .dashboard-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(250px, 1fr));
            gap: 30px;
            margin-bottom: 40px;
        }
        
        .dashboard-card {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            color: white;
            padding: 30px;
            border-radius: 8px;
            text-decoration: none;
            transition: transform 0.3s, box-shadow 0.3s;
            text-align: center;
        }
        
        .dashboard-card:hover {
            transform: translateY(-5px);
            box-shadow: 0 10px 30px rgba(0,0,0,0.2);
        }
        
        .dashboard-card.products {
            background: linear-gradient(135deg, #f093fb 0%, #f5576c 100%);
        }
        
        .dashboard-card.invoices {
            background: linear-gradient(135deg, #4facfe 0%, #00f2fe 100%);
        }
        
        .dashboard-card.admin {
            background: linear-gradient(135deg, #43e97b 0%, #38f9d7 100%);
        }
        
        .card-icon {
            font-size: 3rem;
            margin-bottom: 15px;
        }
        
        .card-title {
            font-size: 1.3rem;
            font-weight: 600;
            margin-bottom: 10px;
        }
        
        .card-description {
            font-size: 0.9rem;
            opacity: 0.9;
        }
        
        .user-info {
            background: #f0f0f0;
            padding: 20px;
            border-radius: 8px;
            text-align: center;
            margin-top: 40px;
        }
        
        .logout-btn {
            display: inline-block;
            margin-top: 20px;
        }
        
        .role-badge {
            display: inline-block;
            background: #007bff;
            color: white;
            padding: 5px 15px;
            border-radius: 20px;
            font-size: 0.9rem;
            margin-top: 10px;
        }
    </style>
</head>
<body>
    <div class="dashboard">
        <div class="dashboard-container">
            <h1 class="dashboard-title">🛒 Système de Facturation</h1>
            <p class="welcome-message">Bienvenue, <?= echapper($user['nom_complet']) ?></p>
            
            <div class="dashboard-grid">
                <!-- Produits -->
                <a href="modules/produits/liste.php" class="dashboard-card products">
                    <div class="card-icon">📦</div>
                    <div class="card-title">Produits</div>
                    <div class="card-description">Gérer le catalogue</div>
                </a>
                
                <!-- Facturation -->
                <?php if (in_array($user['role'], [ROLE_CAISSIER, ROLE_MANAGER, ROLE_SUPER_ADMIN])): ?>
                    <a href="modules/facturation/nouvelle-facture.php" class="dashboard-card invoices">
                        <div class="card-icon">💰</div>
                        <div class="card-title">Facturation</div>
                        <div class="card-description">Créer une facture</div>
                    </a>
                <?php endif; ?>
                
                <!-- Administration -->
                <?php if (in_array($user['role'], [ROLE_MANAGER, ROLE_SUPER_ADMIN])): ?>
                    <a href="modules/admin/gestion-comptes.php" class="dashboard-card admin">
                        <div class="card-icon">👥</div>
                        <div class="card-title">Administration</div>
                        <div class="card-description">Gérer les comptes</div>
                    </a>
                <?php endif; ?>
            </div>
            
            <div class="user-info">
                <p><strong>Utilisateur:</strong> <?= echapper($user['identifiant']) ?></p>
                <p>
                    <strong>Rôle:</strong>
                    <span class="role-badge"><?= echapper(ucfirst($user['role'])) ?></span>
                </p>
                <a href="auth/logout.php" class="btn btn-secondary logout-btn">Déconnexion</a>
            </div>
        </div>
    </div>
</body>
</html>
