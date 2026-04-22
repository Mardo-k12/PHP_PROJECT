<?php
require_once __DIR__ . '/../../config/constants.php';
require_once ROOT_PATH . '/auth/session.php';
require_once ROOT_PATH . '/includes/fonctions.php';

verifierRole([ROLE_MANAGER, ROLE_SUPER_ADMIN]);

$erreurs = [];
$code_barre = trim($_POST['code_barre'] ?? '');
$nom = trim($_POST['nom'] ?? '');
$prix = trim($_POST['prix'] ?? '');
$date_expiration = trim($_POST['date_expiration'] ?? '');
$quantite_stock = trim($_POST['quantite_stock'] ?? '');

if (empty($code_barre)) $erreurs[] = "Code-barres obligatoire.";
if (empty($nom)) $erreurs[] = "Nom obligatoire.";
if (!is_numeric($prix) || $prix <= 0) $erreurs[] = "Prix invalide.";
if (!preg_match('/^\d{4}-\d{2}-\d{2}$/', $date_expiration)) $erreurs[] = "Date invalide (AAAA-MM-JJ).";
if (!is_numeric($quantite_stock) || $quantite_stock < 0) $erreurs[] = "Stock doit être positif ou nul.";

if (count($erreurs) > 0) {
    session_start();
    $_SESSION['form_errors'] = $erreurs;
    $_SESSION['old_input'] = $_POST;
    header('Location: scanner.php?code=' . urlencode($code_barre));
    exit();
}

$produits = lireJSON(PRODUITS_FILE);
$nouveau_produit = [
    "code_barre" => $code_barre,
    "nom" => $nom,
    "prix_unitaire_ht" => (float)$prix,
    "date_expiration" => $date_expiration,
    "quantite_stock" => (int)$quantite_stock,
    "date_enregistrement" => date('Y-m-d')
];

$trouve = false;
foreach ($produits as &$p) {
    if ($p['code_barre'] === $code_barre) {
        $p = $nouveau_produit;
        $trouve = true;
        break;
    }
}
if (!$trouve) $produits[] = $nouveau_produit;

ecrireJSON(PRODUITS_FILE, $produits);
header('Location: scanner.php?code=' . urlencode($code_barre) . '&success=1');
exit();