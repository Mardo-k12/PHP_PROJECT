<?php
require_once __DIR__ . '/../../config/constants.php';
require_once __DIR__ . '/../../auth/session.php';
require_once __DIR__ . '/../../includes/fonctions.php';
require_once __DIR__ . '/../../includes/fonctions-produits.php';

// Vérifier les rôles autorisés (Manager et Super Admin)
verifierRole([ROLE_MANAGER, ROLE_SUPER_ADMIN]);

$message_erreur = '';
$message_succes = '';
$code_barre = $_GET['code'] ?? '';
$produit_trouve = null;
$old_input = [];
$mode = 'nouveau'; // 'nouveau' ou 'existant'

// Récupérer les anciennes valeurs en cas d'erreur
if (isset($_SESSION['form_errors'])) {
    $message_erreur = implode('<br>', $_SESSION['form_errors']);
    $old_input = extraireAnciennesValeurs();
} 

if (isset($_SESSION['success_message'])) {
    $message_succes = $_SESSION['success_message'];
    unset($_SESSION['success_message']);
}

// Traiter le code-barres scanné ou saisi
if ($code_barre) {
    $produit_trouve = obtenirProduitParCodeBarre($code_barre, PRODUITS_FILE);
    if ($produit_trouve) {
        $mode = 'existant';
        $old_input = $produit_trouve;
    }
}

// Traiter la soumission du formulaire
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $donnees = [
        'code_barre' => $_POST['code_barre'] ?? '',
        'nom' => $_POST['nom'] ?? '',
        'prix_unitaire_ht' => $_POST['prix_unitaire_ht'] ?? '',
        'date_expiration' => $_POST['date_expiration'] ?? '',
        'quantite_stock' => $_POST['quantite_stock'] ?? ''
    ];
    
    $produit_existant = obtenirProduitParCodeBarre($donnees['code_barre'], PRODUITS_FILE);
    
    if ($produit_existant) {
        // Modification
        $resultat = modifierProduit($donnees['code_barre'], $donnees, PRODUITS_FILE);
    } else {
        // Ajout
        $resultat = ajouterProduit($donnees, PRODUITS_FILE);
    }
    
    if ($resultat['succes']) {
        $_SESSION['success_message'] = $resultat['message'];
        header('Location: scanner.php?success=1');
        exit();
    } else {
        sauvegarderAnciennesValeurs($donnees);
        sauvegarderErreurs($resultat['erreurs']);
        header('Location: scanner.php?code=' . urlencode($donnees['code_barre']));
        exit();
    }
}
?>
<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Enregistrement de Produit</title>
    <script src="https://cdn.jsdelivr.net/npm/quagga@0.12.1/dist/quagga.min.js"></script>
    <link rel="stylesheet" href="<?= ROOT_PATH ?>/assets/css/style.css">
    <style>
        body {
            font-family: Arial, sans-serif;
            background-color: #f5f5f5;
            margin: 0;
            padding: 20px;
        }
        
        .container {
            max-width: 800px;
            margin: 0 auto;
            background: white;
            padding: 30px;
            border-radius: 8px;
            box-shadow: 0 2px 4px rgba(0,0,0,0.1);
        }
        
        h1 {
            color: #333;
            border-bottom: 3px solid #007bff;
            padding-bottom: 10px;
        }
        
        .alert {
            padding: 12px;
            margin-bottom: 20px;
            border-radius: 4px;
            border-left: 4px solid;
        }
        
        .alert-success {
            background-color: #d4edda;
            color: #155724;
            border-color: #c3e6cb;
        }
        
        .alert-danger {
            background-color: #f8d7da;
            color: #721c24;
            border-color: #f5c6cb;
        }
        
        .form-group {
            margin-bottom: 20px;
        }
        
        label {
            display: block;
            margin-bottom: 5px;
            font-weight: bold;
            color: #333;
        }
        
        input[type="text"],
        input[type="number"],
        input[type="date"],
        select {
            width: 100%;
            padding: 10px;
            border: 1px solid #ddd;
            border-radius: 4px;
            font-size: 14px;
            box-sizing: border-box;
        }
        
        input[type="text"]:focus,
        input[type="number"]:focus,
        input[type="date"]:focus,
        select:focus {
            outline: none;
            border-color: #007bff;
            box-shadow: 0 0 5px rgba(0, 123, 255, 0.25);
        }
        
        .required::after {
            content: " *";
            color: red;
        }
        
        button {
            background-color: #007bff;
            color: white;
            padding: 10px 20px;
            border: none;
            border-radius: 4px;
            cursor: pointer;
            font-size: 16px;
            margin-right: 10px;
        }
        
        button:hover {
            background-color: #0056b3;
        }
        
        button.secondary {
            background-color: #6c757d;
        }
        
        button.secondary:hover {
            background-color: #545b62;
        }
        
        .button-group {
            margin-top: 20px;
        }
        
        .scanner-section {
            background-color: #f8f9fa;
            padding: 20px;
            border-radius: 4px;
            margin-bottom: 30px;
        }
        
        .scanner-section h2 {
            margin-top: 0;
            color: #333;
        }
        
        #video {
            width: 100%;
            max-width: 400px;
            border: 1px solid #ddd;
            border-radius: 4px;
            margin-bottom: 10px;
        }
        
        .scanner-controls {
            display: flex;
            gap: 10px;
            margin-bottom: 15px;
        }
        
        .scanner-input {
            flex: 1;
        }
        
        .mode-indicator {
            display: inline-block;
            padding: 5px 10px;
            border-radius: 20px;
            font-size: 12px;
            font-weight: bold;
            margin-bottom: 15px;
        }
        
        .mode-nouveau {
            background-color: #d4edda;
            color: #155724;
        }
        
        .mode-existant {
            background-color: #fff3cd;
            color: #856404;
        }
        
        .product-info {
            background-color: #e7f3ff;
            padding: 15px;
            border-left: 4px solid #2196F3;
            margin-bottom: 20px;
            border-radius: 4px;
        }
        
        .product-info p {
            margin: 5px 0;
        }
        
        .field-group-2 {
            display: grid;
            grid-template-columns: 1fr 1fr;
            gap: 20px;
        }
        
        @media (max-width: 600px) {
            .field-group-2 {
                grid-template-columns: 1fr;
            }
        }
    </style>
</head>
<body>
    <div class="container">
        <h1>📦 Enregistrement de Produit</h1>
        
        <?php if ($message_succes): ?>
            <div class="alert alert-success">
                ✓ <?= echapper($message_succes) ?>
            </div>
        <?php endif; ?>
        
        <?php if ($message_erreur): ?>
            <div class="alert alert-danger">
                ✗ <?= $message_erreur ?>
            </div>
        <?php endif; ?>
        
        <!-- Section Scanner -->
        <div class="scanner-section">
            <h2>Étape 1: Scanner ou saisir le code-barres</h2>
            
            <div class="scanner-controls">
                <input type="text" id="barcodeInput" class="scanner-input" placeholder="Scannez le code-barres ici ou entrez-le manuellement">
                <button type="button" onclick="activerCamera()">📷 Caméra</button>
            </div>
            
            <video id="video" style="display:none;"></video>
            <canvas id="canvas" style="display:none;"></canvas>
            <button type="button" id="toggleScanner" onclick="basculerScanner()" style="display:none;">Arrêter le scanner</button>
        </div>
        
        <!-- Formulaire d'enregistrement -->
        <form method="POST" action="">
            <h2>Étape 2: Informations du produit</h2>
            
            <?php if ($mode === 'existant'): ?>
                <div class="mode-indicator mode-existant">
                    ⚠ Mode modification - Produit existant
                </div>
            <?php else: ?>
                <div class="mode-indicator mode-nouveau">
                    ✓ Mode ajout - Nouveau produit
                </div>
            <?php endif; ?>
            
            <div class="form-group">
                <label for="code_barre" class="required">Code-barres</label>
                <input type="text" id="code_barre" name="code_barre" 
                       value="<?= echapper($old_input['code_barre'] ?? '') ?>" required>
            </div>
            
            <div class="form-group">
                <label for="nom" class="required">Nom du produit</label>
                <input type="text" id="nom" name="nom" 
                       value="<?= echapper($old_input['nom'] ?? '') ?>" required>
            </div>
            
            <div class="field-group-2">
                <div class="form-group">
                    <label for="prix_unitaire_ht" class="required">Prix unitaire HT (<?= DEVISE ?>)</label>
                    <input type="number" id="prix_unitaire_ht" name="prix_unitaire_ht" 
                           step="0.01" min="0" value="<?= echapper($old_input['prix_unitaire_ht'] ?? '') ?>" required>
                </div>
                
                <div class="form-group">
                    <label for="quantite_stock" class="required">Quantité initiale en stock</label>
                    <input type="number" id="quantite_stock" name="quantite_stock" 
                           min="0" value="<?= echapper($old_input['quantite_stock'] ?? '') ?>" required>
                </div>
            </div>
            
            <div class="form-group">
                <label for="date_expiration" class="required">Date d'expiration</label>
                <input type="date" id="date_expiration" name="date_expiration" 
                       value="<?= echapper($old_input['date_expiration'] ?? '') ?>" required>
            </div>
            
            <div class="button-group">
                <button type="submit"><?= $mode === 'existant' ? '✏️ Modifier le produit' : '➕ Ajouter le produit' ?></button>
                <button type="reset" class="secondary">🔄 Réinitialiser</button>
                <button type="button" class="secondary" onclick="retour()">← Retour</button>
            </div>
        </form>
    </div>
    
    <script>
        let scanner = null;
        let isScannerActive = false;
        
        // Gestion du code-barres scanné
        document.getElementById('barcodeInput').addEventListener('keydown', function(e) {
            if (e.key === 'Enter') {
                e.preventDefault();
                const code = this.value.trim();
                if (code) {
                    window.location.href = 'scanner.php?code=' + encodeURIComponent(code);
                }
            }
        });
        
        // Activer la caméra
        function activerCamera() {
            if (isScannerActive) {
                basculerScanner();
                return;
            }
            
            const video = document.getElementById('video');
            video.style.display = 'block';
            document.getElementById('toggleScanner').style.display = 'inline-block';
            
            Quagga.init({
                inputStream: {
                    name: "Live",
                    type: "LiveStream",
                    target: video,
                    constraints: {
                        facingMode: "environment"
                    }
                },
                decoder: {
                    workers: 2,
                    debug: false
                }
            }, function(err) {
                if (err) {
                    console.log(err);
                    alert('Erreur lors de l\'activation de la caméra: ' + err);
                    return;
                }
                Quagga.start();
                isScannerActive = true;
                
                Quagga.onDetected(function(result) {
                    const code = result.codeResult.code;
                    document.getElementById('barcodeInput').value = code;
                    document.getElementById('code_barre').value = code;
                    
                    // Arrêter le scanner et rediriger
                    Quagga.stop();
                    window.location.href = 'scanner.php?code=' + encodeURIComponent(code);
                });
            });
        }
        
        // Basculer le scanner
        function basculerScanner() {
            const video = document.getElementById('video');
            
            if (isScannerActive) {
                Quagga.stop();
                video.style.display = 'none';
                document.getElementById('toggleScanner').style.display = 'none';
                isScannerActive = false;
            }
        }
        
        // Retour
        function retour() {
            window.location.href = 'liste.php';
        }
    </script>
</body>
</html>


    <h2>Scanner un code-barres</h2>
    <div id="interactive" class="viewport"></div>
    <input type="hidden" id="scanned_code" value="<?= htmlspecialchars($code_barre) ?>">

    <?php if ($produit_trouve): ?>
        <div class="info-produit">
            <strong>Produit existant :</strong><br>
            Nom : <?= htmlspecialchars($produit_trouve['nom']) ?><br>
            Prix HT : <?= htmlspecialchars($produit_trouve['prix_unitaire_ht']) ?> CDF<br>
            Date expiration : <?= htmlspecialchars($produit_trouve['date_expiration']) ?><br>
            Stock : <?= htmlspecialchars($produit_trouve['quantite_stock']) ?>
        </div>
    <?php endif; ?>

    <h2><?= $produit_trouve ? 'Modifier le produit' : 'Ajouter un nouveau produit' ?></h2>
    <form action="traiter_produit.php" method="post">
        <input type="hidden" name="code_barre" value="<?= htmlspecialchars($code_barre) ?>">
        <label>Nom :</label>
        <input type="text" name="nom" value="<?= htmlspecialchars($old_input['nom'] ?? ($produit_trouve['nom'] ?? '')) ?>" required><br>
        <label>Prix unitaire HT (CDF) :</label>
        <input type="number" step="1" name="prix" value="<?= htmlspecialchars($old_input['prix'] ?? ($produit_trouve['prix_unitaire_ht'] ?? '')) ?>" required><br>
        <label>Date d'expiration (AAAA-MM-JJ) :</label>
        <input type="date" name="date_expiration" value="<?= htmlspecialchars($old_input['date_expiration'] ?? ($produit_trouve['date_expiration'] ?? '')) ?>" required><br>
        <label>Quantité en stock :</label>
        <input type="number" step="1" name="quantite_stock" value="<?= htmlspecialchars($old_input['quantite_stock'] ?? ($produit_trouve['quantite_stock'] ?? '')) ?>" required><br>
        <button type="submit">Enregistrer</button>
    </form>
    <p><a href="liste.php">Voir tous les produits</a></p>
    <script>
        Quagga.init({
            inputStream: { name: "Live", type: "LiveStream", target: document.querySelector('#interactive') },
            decoder: { readers: ["ean_reader", "code_128_reader", "code_39_reader"] }
        }, function(err) {
            if (err) { console.error(err); alert("Impossible d'accéder à la caméra."); return; }
            Quagga.start();
        });
        Quagga.onDetected(function(result) {
            window.location.href = "scanner.php?code=" + encodeURIComponent(result.codeResult.code);
        });
    </script>
</body>
</html>