<?php
require_once __DIR__ . '/../config/constants.php';
require_once __DIR__ . '/../includes/fonctions.php';

if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

$erreur = '';

// Si déjà connecté, rediriger vers l'accueil
if (isset($_SESSION['user'])) {
    header('Location: ' . ROOT_PATH . '/index.php');
    exit();
}

// Traiter la soumission du formulaire
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $identifiant = $_POST['identifiant'] ?? '';
    $motDePasse = $_POST['motDePasse'] ?? '';
    
    if (empty($identifiant) || empty($motDePasse)) {
        $erreur = 'Veuillez remplir tous les champs.';
    } else {
        $user = authentifierUtilisateur($identifiant, $motDePasse, UTILISATEURS_FILE);
        
        if ($user) {
            $_SESSION['user'] = $user;
            header('Location: ' . ROOT_PATH . '/index.php');
            exit();
        } else {
            $erreur = 'Identifiant ou mot de passe incorrect.';
        }
    }
}
?>
<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Connexion - Système de Facturation</title>
    <link rel="stylesheet" href="<?= ROOT_PATH ?>/assets/css/style.css">
    <style>
        body {
            display: flex;
            justify-content: center;
            align-items: center;
            min-height: 100vh;
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            margin: 0;
            padding: 20px;
        }
        
        .login-container {
            background: white;
            padding: 40px;
            border-radius: 12px;
            box-shadow: 0 10px 40px rgba(0,0,0,0.2);
            width: 100%;
            max-width: 400px;
        }
        
        .login-title {
            text-align: center;
            color: #333;
            margin-bottom: 10px;
            font-size: 2rem;
        }
        
        .login-subtitle {
            text-align: center;
            color: #666;
            margin-bottom: 30px;
            font-size: 0.9rem;
        }
        
        .form-group {
            margin-bottom: 20px;
        }
        
        label {
            display: block;
            margin-bottom: 8px;
            font-weight: 600;
            color: #333;
        }
        
        input[type="text"],
        input[type="password"] {
            width: 100%;
            padding: 12px;
            border: 1px solid #ddd;
            border-radius: 6px;
            font-size: 14px;
            box-sizing: border-box;
            transition: border-color 0.3s;
        }
        
        input[type="text"]:focus,
        input[type="password"]:focus {
            outline: none;
            border-color: #667eea;
            box-shadow: 0 0 8px rgba(102, 126, 234, 0.2);
        }
        
        .login-button {
            width: 100%;
            padding: 12px;
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            color: white;
            border: none;
            border-radius: 6px;
            font-size: 16px;
            font-weight: 600;
            cursor: pointer;
            transition: transform 0.2s;
        }
        
        .login-button:hover {
            transform: translateY(-2px);
        }
        
        .alert-error {
            background-color: #f8d7da;
            color: #721c24;
            padding: 12px;
            border-radius: 6px;
            margin-bottom: 20px;
            border-left: 4px solid #f5c6cb;
        }
        
        .demo-info {
            background-color: #d1ecf1;
            color: #0c5460;
            padding: 12px;
            border-radius: 6px;
            margin-top: 20px;
            border-left: 4px solid #bee5eb;
            font-size: 0.85rem;
        }
    </style>
</head>
<body>
    <div class="login-container">
        <h1 class="login-title">🛒</h1>
        <h2 style="text-align: center; color: #333; margin-bottom: 5px;">Connexion</h2>
        <p class="login-subtitle">Système de Facturation avec Lecture de Codes-Barres</p>
        
        <?php if ($erreur): ?>
            <div class="alert-error">
                ✗ <?= echapper($erreur) ?>
            </div>
        <?php endif; ?>
        
        <form method="POST" action="">
            <div class="form-group">
                <label for="identifiant">Identifiant</label>
                <input type="text" id="identifiant" name="identifiant" placeholder="Votre identifiant" required autofocus>
            </div>
            
            <div class="form-group">
                <label for="motDePasse">Mot de passe</label>
                <input type="password" id="motDePasse" name="motDePasse" placeholder="Votre mot de passe" required>
            </div>
            
            <button type="submit" class="login-button">Se connecter</button>
        </form>
        
        <div class="demo-info">
            <strong>📝 Compte de démonstration:</strong><br>
            Identifiant: <code>demo</code><br>
            Mot de passe: <code>demo</code>
        </div>
    </div>
</body>
</html>
