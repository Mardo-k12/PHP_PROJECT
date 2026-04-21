<?php
require_once __DIR__ . '/../../config/constants.php';
require_once __DIR__ . '/../../auth/session.php';
require_once __DIR__ . '/../../includes/fonctions.php';

verifierRole([ROLE_MANAGER, ROLE_SUPER_ADMIN]);

$erreurs = [];

// Récupération et assainissement
$code_barre = trim($_POST['code_barre'] ?? '');
$nom = trim($_POST['nom'] ?? '');
$prix = trim($_POST['prix'] ?? '');
$date_expiration = trim($_POST['date_expiration'] ?? '');
$quantite_stock = trim($_POST['quantite_stock'] ?? '');

// Validations
if (empty($code_barre)) {
    $erreurs[] = "Le code-barres est obligatoire.";
}
if (empty($nom)) {
    $erreurs[] = "Le nom du produit est obligatoire.";
}
if (!is_numeric($prix) || $prix <= 0) {
    $erreurs[] = "Le prix doit être un nombre positif.";
}
if (!preg_match('/^\d{4}-\d{2}-\d{2}$/', $date_expiration)) {
    $erreurs[] = "La date d'expiration doit être au format AAAA-MM-JJ.";
}
if (!is_numeric($quantite_stock) || $quantite_stock < 0) {
    $erreurs[] = "La quantité en stock doit être un nombre positif ou zéro.";
}

if (count($erreurs) > 0) {
    // Conservation des valeurs saisies et des erreurs
    session_start();
    $_SESSION['form_errors'] = $erreurs;
    $_SESSION['old_input'] = $_POST;
    header('Location: scanner.php?code=' . urlencode($code_barre));
    exit();
}

// Lecture du fichier produits
$produits = lireJSON(PRODUITS_FILE);

$nouveau_produit = [
    "code_barre" => $code_barre,
    "nom" => $nom,
    "prix_unitaire_ht" => (float)$prix,
    "date_expiration" => $date_expiration,
    "quantite_stock" => (int)$quantite_stock,
    "date_enregistrement" => date('Y-m-d')
];

$existe = false;
foreach ($produits as &$p) {
    if ($p['code_barre'] === $code_barre) {
        $p = $nouveau_produit;
        $existe = true;
        break;
    }
}
if (!$existe) {
    $produits[] = $nouveau_produit;
}

ecrireJSON(PRODUITS_FILE, $produits);

// Redirection avec succès
header('Location: scanner.php?code=' . urlencode($code_barre) . '&success=1');
exit();