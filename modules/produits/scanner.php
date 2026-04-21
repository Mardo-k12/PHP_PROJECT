<?php
require_once __DIR__ . '/../../config/constants.php';
require_once __DIR__ . '/../../auth/session.php';
require_once __DIR__ . '/../../includes/fonctions.php';

// Vérification des droits (Manager ou Super Admin)
verifierRole([ROLE_MANAGER, ROLE_SUPER_ADMIN]);

$produits = lireJSON(PRODUITS_FILE);
$message_erreur = '';
$message_succes = '';
$code_barre = $_GET['code'] ?? '';
$produit_trouve = null;
$old_input = [];

// Récupération des erreurs ou anciennes valeurs depuis la session (après soumission)
if (isset($_SESSION['form_errors'])) {
    $message_erreur = implode('<br>', $_SESSION['form_errors']);
    $old_input = $_SESSION['old_input'] ?? [];
    unset($_SESSION['form_errors'], $_SESSION['old_input']);
} elseif (isset($_GET['success'])) {
    $message_succes = 'Produit enregistré avec succès !';
}

// Si un code-barres est passé en paramètre, on recherche le produit
if ($code_barre) {
    $produit_trouve = produitExiste($code_barre, $produits);
}
?>
<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <title>Enregistrement produit</title>
    <script src="https://cdn.jsdelivr.net/npm/quagga@0.12.1/dist/quagga.min.js"></script>
    <style>
        body { font-family: Arial; margin: 20px; }
        .viewport { width: 320px; height: 240px; border: 1px solid #ccc; margin-bottom: 20px; }
        .success { color: green; }
        .error { color: red; }
        form input, form button { margin: 5px; padding: 8px; }
        .info-produit { background: #f0f0f0; padding: 10px; margin-bottom: 20px; }
    </style>
</head>
<body>
    <h1>Enregistrement / modification de produit</h1>

    <?php if ($message_succes): ?>
        <div class="success"><?= htmlspecialchars($message_succes) ?></div>
    <?php endif; ?>
    <?php if ($message_erreur): ?>
        <div class="error"><?= $message_erreur ?></div>
    <?php endif; ?>

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
        // Initialisation de QuaggaJS
        Quagga.init({
            inputStream: {
                name: "Live",
                type: "LiveStream",
                target: document.querySelector('#interactive')
            },
            decoder: {
                readers: ["ean_reader", "code_128_reader", "code_39_reader"]
            }
        }, function(err) {
            if (err) {
                console.error(err);
                alert("Impossible d'accéder à la caméra.");
                return;
            }
            Quagga.start();
        });

        Quagga.onDetected(function(result) {
            var code = result.codeResult.code;
            // Rediriger vers la même page avec le code-barres scanné
            window.location.href = "scanner.php?code=" + encodeURIComponent(code);
        });
    </script>
</body>
</html>