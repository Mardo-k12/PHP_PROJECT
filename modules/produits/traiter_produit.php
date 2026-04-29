<?php
require_once __DIR__ . '/../../config/constants.php';
require_once __DIR__ . '/../../auth/includes/session.php';
require_once __DIR__ . '/../../auth/includes/fonctions.php';

// Vérifier les droits d'accès (Manager ou Super Admin)
verifierRole([ROLE_MANAGER, ROLE_SUPER_ADMIN]);

// Initialiser les tableaux de session
if (!isset($_SESSION['form_errors'])) {
    $_SESSION['form_errors'] = [];
}
if (!isset($_SESSION['old_input'])) {
    $_SESSION['old_input'] = [];
}

// Traitement du formulaire
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Récupérer et nettoyer les données
    $code_barre = trim($_POST['code_barre'] ?? '');
    $nom = trim($_POST['nom'] ?? '');
    $prix = trim($_POST['prix'] ?? '');
    $date_expiration = trim($_POST['date_expiration'] ?? '');
    $quantite_stock = trim($_POST['quantite_stock'] ?? '');
    
    // Sauvegarder les entrées pour les redisplayer en cas d'erreur
    $_SESSION['old_input'] = [
        'code_barre' => $code_barre,
        'nom' => $nom,
        'prix' => $prix,
        'date_expiration' => $date_expiration,
        'quantite_stock' => $quantite_stock
    ];
    
    // ========== VALIDATION DES DONNÉES ==========
    
    // Valider code-barre
    if (empty($code_barre)) {
        $_SESSION['form_errors'][] = '❌ Le code-barre est obligatoire.';
    } elseif (strlen($code_barre) < 6 || strlen($code_barre) > 20) {
        $_SESSION['form_errors'][] = '❌ Le code-barre doit contenir entre 6 et 20 caractères.';
    } elseif (!preg_match('/^[0-9]+$/', $code_barre)) {
        $_SESSION['form_errors'][] = '❌ Le code-barre doit contenir uniquement des chiffres.';
    }
    
    // Valider nom
    if (empty($nom)) {
        $_SESSION['form_errors'][] = '❌ Le nom du produit est obligatoire.';
    } elseif (strlen($nom) < 3 || strlen($nom) > 100) {
        $_SESSION['form_errors'][] = '❌ Le nom doit contenir entre 3 et 100 caractères.';
    }
    
    // Valider prix
    if (empty($prix)) {
        $_SESSION['form_errors'][] = '❌ Le prix unitaire est obligatoire.';
    } elseif (!is_numeric($prix) || $prix <= 0) {
        $_SESSION['form_errors'][] = '❌ Le prix doit être un nombre positif.';
    } elseif ($prix > 1000000) {
        $_SESSION['form_errors'][] = '❌ Le prix est trop élevé (max: 1 000 000 CDF).';
    }
    
    // Valider date d'expiration
    if (empty($date_expiration)) {
        $_SESSION['form_errors'][] = '❌ La date d\'expiration est obligatoire.';
    } else {
        $date_exp = strtotime($date_expiration);
        if ($date_exp === false) {
            $_SESSION['form_errors'][] = '❌ Format de date invalide (attendu: AAAA-MM-JJ).';
        } elseif ($date_exp < time()) {
            $_SESSION['form_errors'][] = '❌ La date d\'expiration ne peut pas être dans le passé.';
        }
    }
    
    // Valider quantité
    if (empty($quantite_stock) && $quantite_stock !== '0') {
        $_SESSION['form_errors'][] = '❌ La quantité en stock est obligatoire.';
    } elseif (!is_numeric($quantite_stock) || $quantite_stock < 0) {
        $_SESSION['form_errors'][] = '❌ La quantité doit être un nombre positif ou zéro.';
    } elseif ($quantite_stock > 999999) {
        $_SESSION['form_errors'][] = '❌ La quantité est trop élevée (max: 999 999 unités).';
    }
    
    // ========== EN CAS D'ERREURS ==========
    if (!empty($_SESSION['form_errors'])) {
        header('Location: scanner.php?code=' . urlencode($code_barre));
        exit();
    }
    
    // ========== TRAITER LES DONNÉES VALIDES ==========
    unset($_SESSION['form_errors']);
    unset($_SESSION['old_input']);
    
    // Charger les produits existants
    $produits = lireJSON(PRODUITS_FILE);
    if (!is_array($produits)) {
        $produits = [];
    }
    
    // Vérifier si le produit existe déjà
    $produit_existe = false;
    $index_produit = -1;
    
    foreach ($produits as $index => $p) {
        if ($p['code_barre'] === $code_barre) {
            $produit_existe = true;
            $index_produit = $index;
            break;
        }
    }
    
    // Préparer les données du produit
    $nouveau_produit = [
        'code_barre' => $code_barre,
        'nom' => $nom,
        'prix_unitaire_ht' => floatval($prix),
        'taux_tva' => 16, // TVA standard 16% (Congo)
        'date_expiration' => $date_expiration,
        'quantite_stock' => intval($quantite_stock),
        'date_creation' => $produits[$index_produit]['date_creation'] ?? date('Y-m-d H:i:s'),
        'date_modification' => date('Y-m-d H:i:s'),
        'statut' => 'actif',
        'createur_id' => $_SESSION['user']['id'] ?? 0,
        'modificateur_id' => $_SESSION['user']['id'] ?? 0
    ];
    
    // Ajouter ou modifier le produit
    if ($produit_existe) {
        $produits[$index_produit] = $nouveau_produit;
        $action = 'modifié';
    } else {
        $produits[] = $nouveau_produit;
        $action = 'ajouté';
    }
    
    // Sauvegarder les données
    ecrireJSON(PRODUITS_FILE, $produits);
    
    // Redirection avec succès
    $_SESSION['message_succes'] = '✅ Produit ' . $action . ' avec succès !';
    header('Location: scanner.php?success=1&code=' . urlencode($code_barre));
    exit();
}

// Si ce fichier est accédé directement sans POST
header('Location: scanner.php');
exit();
?>