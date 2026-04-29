<?php
require_once __DIR__ . '/../../config/constants.php';
require_once ROOT_PATH . '/auth/includes/session.php';
require_once ROOT_PATH . '/auth/includes/fonctions.php';

verifierRole([ROLE_MANAGER, ROLE_SUPER_ADMIN]);

$produits = lireJSON(PRODUITS_FILE);
$message_erreur = '';
$message_succes = '';
$code_barre = $_GET['code'] ?? '';
$produit_trouve = null;
$old_input = [];

if (isset($_SESSION['form_errors'])) {
    $message_erreur = implode('<br>', $_SESSION['form_errors']);
    $old_input = $_SESSION['old_input'] ?? [];
    unset($_SESSION['form_errors'], $_SESSION['old_input']);
} elseif (isset($_GET['success']) && isset($_SESSION['message_succes'])) {
    $message_succes = $_SESSION['message_succes'];
    unset($_SESSION['message_succes']);
}

if ($code_barre) {
    $produit_trouve = produitExiste($code_barre, $produits);
}
?>
<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Enregistrement produit - Mardoché</title>
    <script src="https://cdn.jsdelivr.net/npm/quagga@0.12.1/dist/quagga.min.js"></script>
    <style>
        * { margin: 0; padding: 0; box-sizing: border-box; }
        body { 
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif; 
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            min-height: 100vh;
            padding: 20px;
        }
        .container { max-width: 900px; margin: 0 auto; background: white; border-radius: 10px; box-shadow: 0 10px 30px rgba(0,0,0,0.3); padding: 30px; }
        header { text-align: center; margin-bottom: 30px; border-bottom: 3px solid #667eea; padding-bottom: 15px; }
        header h1 { color: #333; font-size: 28px; }
        header p { color: #666; margin-top: 5px; }
        .success { background: #d4edda; border: 1px solid #c3e6cb; color: #155724; padding: 15px; border-radius: 5px; margin-bottom: 20px; }
        .error { background: #f8d7da; border: 1px solid #f5c6cb; color: #721c24; padding: 15px; border-radius: 5px; margin-bottom: 20px; }
        .error ul { margin-left: 20px; margin-top: 10px; }
        .section { margin-bottom: 30px; }
        .section h2 { color: #667eea; font-size: 20px; margin-bottom: 15px; border-left: 4px solid #667eea; padding-left: 10px; }
        .viewport { width: 100%; max-width: 400px; height: 300px; border: 3px solid #667eea; border-radius: 8px; margin-bottom: 20px; background: #000; }
        input[type="text"], input[type="number"], input[type="date"], button { 
            width: 100%; max-width: 350px; 
            margin: 10px 0; 
            padding: 12px 15px; 
            border: 2px solid #ddd; 
            border-radius: 5px;
            font-size: 14px;
        }
        input:focus { outline: none; border-color: #667eea; box-shadow: 0 0 5px rgba(102, 126, 234, 0.5); }
        button { 
            background: #667eea; 
            color: white; 
            border: none; 
            cursor: pointer; 
            font-weight: bold;
            transition: background 0.3s;
        }
        button:hover { background: #764ba2; }
        .info-produit { 
            background: linear-gradient(135deg, #f5f7fa 0%, #c3cfe2 100%); 
            border-left: 5px solid #667eea;
            padding: 15px; 
            margin-bottom: 20px;
            border-radius: 5px;
        }
        .info-produit strong { color: #667eea; }
        .info-produit p { margin: 8px 0; color: #333; }
        .form-group { margin-bottom: 15px; }
        label { display: block; font-weight: bold; color: #333; margin-bottom: 5px; }
        .action-links { text-align: center; margin-top: 20px; }
        .action-links a { color: #667eea; text-decoration: none; margin: 0 15px; font-weight: bold; transition: color 0.3s; }
        .action-links a:hover { color: #764ba2; text-decoration: underline; }
    </style>
</head>
<body>
    <div class="container">
        <header>
            <h1>📦 Enregistrement / Modification de Produits</h1>
            <p>Module Mardoché - Gestion des produits avec lecteur de codes-barres</p>
        </header>

        <?php if ($message_succes): ?>
            <div class="success"><?= htmlspecialchars($message_succes) ?></div>
        <?php endif; ?>
        <?php if ($message_erreur): ?>
            <div class="error">
                <strong>❌ Erreur de validation :</strong>
                <ul><?= $message_erreur ?></ul>
            </div>
        <?php endif; ?>

        <!-- Section Scan -->
        <div class="section">
            <h2>🔍 Scanner un code-barres</h2>
            <div id="interactive" class="viewport"></div>
            <input type="hidden" id="scanned_code" value="<?= htmlspecialchars($code_barre) ?>">
            <p style="text-align: center; color: #666; margin-top: 10px; font-size: 12px;">
                Positionnez le code-barres face à la caméra
            </p>
        </div>

        <!-- Affichage du produit existant si trouvé -->
        <?php if ($produit_trouve): ?>
            <div class="section">
                <h2>📋 Produit Existant</h2>
                <div class="info-produit">
                    <p><strong>Nom :</strong> <?= htmlspecialchars($produit_trouve['nom']) ?></p>
                    <p><strong>Code-barres :</strong> <?= htmlspecialchars($produit_trouve['code_barre']) ?></p>
                    <p><strong>Prix HT :</strong> <?= number_format($produit_trouve['prix_unitaire_ht'], 2, ',', ' ') ?> CDF</p>
                    <p><strong>TVA :</strong> <?= $produit_trouve['taux_tva'] ?? 16 ?>%</p>
                    <p><strong>Date d'expiration :</strong> <?= htmlspecialchars($produit_trouve['date_expiration']) ?></p>
                    <p><strong>Stock :</strong> <?= htmlspecialchars($produit_trouve['quantite_stock']) ?> unités</p>
                    <p><strong>Statut :</strong> <span style="color: green; font-weight: bold;"><?= htmlspecialchars($produit_trouve['statut'] ?? 'actif') ?></span></p>
                </div>
            </div>
        <?php endif; ?>

        <!-- Formulaire d'ajout/modification -->
        <div class="section">
            <h2><?= $produit_trouve ? '✏️ Modifier le produit' : '➕ Ajouter un nouveau produit' ?></h2>
            <form action="traiter_produit.php" method="post">
                <input type="hidden" name="code_barre" value="<?= htmlspecialchars($code_barre) ?>">
                
                <div class="form-group">
                    <label for="nom">Nom du produit *</label>
                    <input type="text" id="nom" name="nom" 
                        value="<?= htmlspecialchars($old_input['nom'] ?? ($produit_trouve['nom'] ?? '')) ?>" 
                        placeholder="Ex: Lait frais 1L"
                        required>
                </div>

                <div class="form-group">
                    <label for="prix">Prix unitaire HT (CDF) *</label>
                    <input type="number" id="prix" name="prix" step="1" 
                        value="<?= htmlspecialchars($old_input['prix'] ?? ($produit_trouve['prix_unitaire_ht'] ?? '')) ?>" 
                        placeholder="Ex: 2500"
                        required>
                </div>

                <div class="form-group">
                    <label for="date_expiration">Date d'expiration (AAAA-MM-JJ) *</label>
                    <input type="date" id="date_expiration" name="date_expiration" 
                        value="<?= htmlspecialchars($old_input['date_expiration'] ?? ($produit_trouve['date_expiration'] ?? '')) ?>" 
                        required>
                </div>

                <div class="form-group">
                    <label for="quantite_stock">Quantité en stock *</label>
                    <input type="number" id="quantite_stock" name="quantite_stock" step="1" 
                        value="<?= htmlspecialchars($old_input['quantite_stock'] ?? ($produit_trouve['quantite_stock'] ?? '')) ?>" 
                        placeholder="Ex: 50"
                        required>
                </div>

                <button type="submit">💾 Enregistrer</button>
            </form>
        </div>

        <!-- Liens de navigation -->
        <div class="action-links">
            <a href="liste.php">📚 Voir tous les produits</a>
            <a href="scanner.php">🔄 Nouveau scan</a>
        </div>
    </div>
    <script>
        // Initialiser Quagga pour la lecture des codes-barres
        Quagga.init({
            inputStream: { 
                name: "Live", 
                type: "LiveStream", 
                target: document.querySelector('#interactive'),
                constraints: {
                    width: 400,
                    height: 300
                }
            },
            locator: {
                halfSample: true
            },
            frequency: 10,
            decoder: { 
                readers: ["ean_reader", "code_128_reader", "code_39_reader", "upc_reader", "ean_8_reader"]
            }
        }, function(err) {
            if (err) { 
                console.error("Erreur Quagga:", err); 
                alert("⚠️ Impossible d'accéder à la caméra.\nVérifiez les permissions et la connexion.");
                return; 
            }
            Quagga.start();
            console.log("✅ Lecteur de code-barres initialisé avec succès");
        });

        // Détection automatique des codes-barres
        Quagga.onDetected(function(result) {
            if (result.codeResult && result.codeResult.code) {
                console.log("Code détecté:", result.codeResult.code);
                Quagga.stop();
                window.location.href = "scanner.php?code=" + encodeURIComponent(result.codeResult.code);
            }
        });

        // Gestion des erreurs lors de la détection
        Quagga.onProcessed(function(result) {
            // On peut ajouter une indication visuelle de la détection
        });

        // Nettoyage à la fermeture de la page
        window.addEventListener('beforeunload', function() {
            Quagga.stop();
        });
    </script>
</body>
</html>