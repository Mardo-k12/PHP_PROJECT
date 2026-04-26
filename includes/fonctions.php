<?php

/**
 * FONCTIONS GÉNÉRALES
 */

/**
 * Lit un fichier JSON et retourne un tableau associatif
 */
function lireJSON($fichier) {
    if (!file_exists($fichier)) {
        return [];
    }
    $contenu = file_get_contents($fichier);
    return json_decode($contenu, true) ?? [];
}

/**
 * Écrit des données dans un fichier JSON
 */
function ecrireJSON($fichier, $donnees) {
    $dir = dirname($fichier);
    if (!is_dir($dir)) {
        mkdir($dir, 0755, true);
    }
    file_put_contents($fichier, json_encode($donnees, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE));
}

/**
 * FONCTIONS PRODUITS
 */

/**
 * Vérifie si un produit existe par son code-barres
 */
function produitExiste($codeBarre, $produits) {
    foreach ($produits as $p) {
        if ($p['code_barre'] === $codeBarre) {
            return $p;
        }
    }
    return null;
}

/**
 * Ajoute ou met à jour un produit
 */
function sauvegarderProduit($produit, $fichier) {
    $produits = lireJSON($fichier);
    
    // Cherche si le produit existe
    $trouve = false;
    foreach ($produits as &$p) {
        if ($p['code_barre'] === $produit['code_barre']) {
            $p = $produit;
            $trouve = true;
            break;
        }
    }
    
    // Si nouveau produit
    if (!$trouve) {
        $produits[] = $produit;
    }
    
    ecrireJSON($fichier, $produits);
    return true;
}

/**
 * Valide les données d'un produit
 */
function validerProduit($donnees) {
    $erreurs = [];
    
    // Validation du code-barres
    if (empty(trim($donnees['code_barre'] ?? ''))) {
        $erreurs[] = "Le code-barres est obligatoire.";
    }
    
    // Validation du nom
    if (empty(trim($donnees['nom'] ?? ''))) {
        $erreurs[] = "Le nom du produit est obligatoire.";
    }
    
    // Validation du prix
    $prix = $donnees['prix_unitaire_ht'] ?? '';
    if (empty($prix)) {
        $erreurs[] = "Le prix unitaire HT est obligatoire.";
    } elseif (!is_numeric($prix) || floatval($prix) < 0) {
        $erreurs[] = "Le prix unitaire doit être un nombre positif.";
    }
    
    // Validation de la date d'expiration
    $date = $donnees['date_expiration'] ?? '';
    if (empty($date)) {
        $erreurs[] = "La date d'expiration est obligatoire.";
    } elseif (!validerDate($date)) {
        $erreurs[] = "Le format de date doit être YYYY-MM-DD.";
    }
    
    // Validation de la quantité
    $quantite = $donnees['quantite_stock'] ?? '';
    if (empty($quantite)) {
        $erreurs[] = "La quantité initiale en stock est obligatoire.";
    } elseif (!is_numeric($quantite) || intval($quantite) < 0) {
        $erreurs[] = "La quantité doit être un nombre entier positif.";
    }
    
    return $erreurs;
}

/**
 * Valide une date au format YYYY-MM-DD
 */
function validerDate($date) {
    $pattern = '/^\d{4}-\d{2}-\d{2}$/';
    if (!preg_match($pattern, $date)) {
        return false;
    }
    
    list($year, $month, $day) = explode('-', $date);
    return checkdate(intval($month), intval($day), intval($year));
}

/**
 * Formate un prix pour l'affichage
 */
function formatPrix($prix) {
    return number_format($prix, 0, ',', ' ') . ' ' . DEVISE;
}

/**
 * Formate une date pour l'affichage
 */
function formatDate($date) {
    $d = DateTime::createFromFormat('Y-m-d', $date);
    return $d ? $d->format('d/m/Y') : $date;
}

/**
 * Génère un identifiant unique pour une facture
 */
function genererIdFacture($factures) {
    $today = date('Ymd');
    $count = 0;
    
    foreach ($factures as $f) {
        if (strpos($f['id_facture'], $today) !== false) {
            $count++;
        }
    }
    
    return 'FAC-' . $today . '-' . str_pad($count + 1, 3, '0', STR_PAD_LEFT);
}

/**
 * FONCTIONS FACTURES
 */

/**
 * Récupère un produit par son code-barres
 */
function obtenirProduit($codeBarre, $fichier) {
    $produits = lireJSON($fichier);
    return produitExiste($codeBarre, $produits);
}

/**
 * Décrémente le stock d'un produit
 */
function decrementerStock($codeBarre, $quantite, $fichier) {
    $produits = lireJSON($fichier);
    
    foreach ($produits as &$p) {
        if ($p['code_barre'] === $codeBarre) {
            if ($p['quantite_stock'] >= $quantite) {
                $p['quantite_stock'] -= $quantite;
                ecrireJSON($fichier, $produits);
                return true;
            }
            return false;
        }
    }
    
    return false;
}

/**
 * FONCTIONS UTILISATEURS
 */

/**
 * Récupère un utilisateur par son identifiant
 */
function obtenirUtilisateur($identifiant, $fichier) {
    $utilisateurs = lireJSON($fichier);
    
    foreach ($utilisateurs as $u) {
        if ($u['identifiant'] === $identifiant) {
            return $u;
        }
    }
    
    return null;
}

/**
 * Authentifie un utilisateur
 */
function authentifierUtilisateur($identifiant, $motDePasse, $fichier) {
    $user = obtenirUtilisateur($identifiant, $fichier);
    
    if ($user && $user['actif'] === true && password_verify($motDePasse, $user['mot_de_passe'])) {
        return $user;
    }
    
    return null;
}

/**
 * FONCTIONS UTILITAIRES
 */

/**
 * Échappe du texte pour l'affichage HTML
 */
function echapper($texte) {
    return htmlspecialchars($texte, ENT_QUOTES, 'UTF-8');
}

/**
 * Redirige vers une URL
 */
function rediriger($url) {
    header('Location: ' . $url);
    exit();
}
?>
