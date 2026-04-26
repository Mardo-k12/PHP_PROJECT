<?php

/**
 * FONCTIONS SPÉCIFIQUES AU MODULE PRODUITS
 */

/**
 * Récupère tous les produits
 */
function obtenirTousLesProduits($fichier) {
    return lireJSON($fichier);
}

/**
 * Obtient un produit par son code-barres
 */
function obtenirProduitParCodeBarre($codeBarre, $fichier) {
    $produits = obtenirTousLesProduits($fichier);
    
    foreach ($produits as $produit) {
        if ($produit['code_barre'] === $codeBarre) {
            return $produit;
        }
    }
    
    return null;
}

/**
 * Ajoute un nouveau produit
 */
function ajouterProduit($donnees, $fichier) {
    // Valide les données
    $erreurs = validerProduit($donnees);
    if (!empty($erreurs)) {
        return ['succes' => false, 'erreurs' => $erreurs];
    }
    
    // Vérifie que le code-barres n'existe pas
    if (obtenirProduitParCodeBarre($donnees['code_barre'], $fichier)) {
        return ['succes' => false, 'erreurs' => ['Ce code-barres existe déjà.']];
    }
    
    // Crée le produit
    $produit = [
        'code_barre' => trim($donnees['code_barre']),
        'nom' => trim($donnees['nom']),
        'prix_unitaire_ht' => floatval($donnees['prix_unitaire_ht']),
        'date_expiration' => $donnees['date_expiration'],
        'quantite_stock' => intval($donnees['quantite_stock']),
        'date_enregistrement' => date('Y-m-d')
    ];
    
    sauvegarderProduit($produit, $fichier);
    return ['succes' => true, 'message' => 'Produit ajouté avec succès.'];
}

/**
 * Modifie un produit existant
 */
function modifierProduit($codeBarre, $donnees, $fichier) {
    // Valide les données
    $erreurs = validerProduit($donnees);
    if (!empty($erreurs)) {
        return ['succes' => false, 'erreurs' => $erreurs];
    }
    
    // Vérifie que le produit existe
    $produit = obtenirProduitParCodeBarre($codeBarre, $fichier);
    if (!$produit) {
        return ['succes' => false, 'erreurs' => ['Produit introuvable.']];
    }
    
    // Met à jour le produit
    $produit_modifie = [
        'code_barre' => $produit['code_barre'],
        'nom' => trim($donnees['nom']),
        'prix_unitaire_ht' => floatval($donnees['prix_unitaire_ht']),
        'date_expiration' => $donnees['date_expiration'],
        'quantite_stock' => intval($donnees['quantite_stock']),
        'date_enregistrement' => $produit['date_enregistrement']
    ];
    
    sauvegarderProduit($produit_modifie, $fichier);
    return ['succes' => true, 'message' => 'Produit modifié avec succès.'];
}

/**
 * Prépare les données du formulaire en cas d'erreur
 */
function extraireAnciennesValeurs() {
    $old_input = $_SESSION['old_input'] ?? [];
    unset($_SESSION['old_input']);
    return $old_input;
}

/**
 * Stocke les anciennes valeurs en session en cas d'erreur
 */
function sauvegarderAnciennesValeurs($donnees) {
    $_SESSION['old_input'] = $donnees;
}

/**
 * Stocke les erreurs de validation en session
 */
function sauvegarderErreurs($erreurs) {
    $_SESSION['form_errors'] = $erreurs;
}
?>
