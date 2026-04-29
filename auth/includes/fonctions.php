<?php
/**
 * =====================================================
 * FONCTIONS UTILITAIRES - SYSTÈME DE FACTURATION
 * =====================================================
 * Module Mardoché - Gestion des produits
 * Fonctions communes pour lecture/écriture JSON et validation
 */

// ========== FONCTIONS JSON ==========

/**
 * Lire un fichier JSON et retourner les données
 * @param string $fichier Chemin du fichier JSON
 * @return array Données décodées ou tableau vide
 */
function lireJSON($fichier) {
    if (!file_exists($fichier)) {
        return [];
    }
    
    $contenu = file_get_contents($fichier);
    if ($contenu === false) {
        error_log("Erreur de lecture: $fichier");
        return [];
    }
    
    $donnees = json_decode($contenu, true);
    if (json_last_error() !== JSON_ERROR_NONE) {
        error_log("Erreur JSON: " . json_last_error_msg());
        return [];
    }
    
    return is_array($donnees) ? $donnees : [];
}

/**
 * Écrire des données dans un fichier JSON
 * @param string $fichier Chemin du fichier JSON
 * @param array $donnees Données à écrire
 * @return bool true en cas de succès, false sinon
 */
function ecrireJSON($fichier, $donnees) {
    // Créer le répertoire s'il n'existe pas
    $repertoire = dirname($fichier);
    if (!is_dir($repertoire)) {
        mkdir($repertoire, 0755, true);
    }
    
    $json = json_encode($donnees, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE);
    if ($json === false) {
        error_log("Erreur d'encodage JSON: " . json_last_error_msg());
        return false;
    }
    
    if (file_put_contents($fichier, $json) === false) {
        error_log("Erreur d'écriture: $fichier");
        return false;
    }
    
    return true;
}

// ========== FONCTIONS DE RECHERCHE ==========

/**
 * Vérifier si un produit existe par code-barre
 * @param string $codeBarre Code-barre du produit
 * @param array $produits Liste des produits
 * @return array|null Produit trouvé ou null
 */
function produitExiste($codeBarre, $produits) {
    foreach ($produits as $p) {
        if (isset($p['code_barre']) && $p['code_barre'] === $codeBarre) {
            return $p;
        }
    }
    return null;
}

/**
 * Trouver un produit par son ID dans le tableau
 * @param string $codeBarre Code-barre du produit
 * @param array $produits Liste des produits
 * @return int|null Index du produit ou null
 */
function trouverIndexProduit($codeBarre, $produits) {
    foreach ($produits as $index => $p) {
        if (isset($p['code_barre']) && $p['code_barre'] === $codeBarre) {
            return $index;
        }
    }
    return null;
}

// ========== FONCTIONS DE VALIDATION ==========

/**
 * Valider un code-barre
 * @param string $codeBarre Code-barre à valider
 * @return array Erreurs (tableau vide si valide)
 */
function validerCodeBarre($codeBarre) {
    $erreurs = [];
    
    if (empty($codeBarre)) {
        $erreurs[] = 'Le code-barre est obligatoire.';
    } elseif (strlen($codeBarre) < 6 || strlen($codeBarre) > 20) {
        $erreurs[] = 'Le code-barre doit contenir entre 6 et 20 caractères.';
    } elseif (!preg_match('/^[0-9]+$/', $codeBarre)) {
        $erreurs[] = 'Le code-barre doit contenir uniquement des chiffres.';
    }
    
    return $erreurs;
}

/**
 * Valider le nom d'un produit
 * @param string $nom Nom du produit
 * @return array Erreurs (tableau vide si valide)
 */
function validerNomProduit($nom) {
    $erreurs = [];
    
    if (empty($nom)) {
        $erreurs[] = 'Le nom du produit est obligatoire.';
    } elseif (strlen($nom) < 3 || strlen($nom) > 100) {
        $erreurs[] = 'Le nom doit contenir entre 3 et 100 caractères.';
    }
    
    return $erreurs;
}

/**
 * Valider un prix
 * @param mixed $prix Prix à valider
 * @return array Erreurs (tableau vide si valide)
 */
function validerPrix($prix) {
    $erreurs = [];
    
    if (empty($prix) && $prix !== '0') {
        $erreurs[] = 'Le prix est obligatoire.';
    } elseif (!is_numeric($prix)) {
        $erreurs[] = 'Le prix doit être un nombre.';
    } elseif ($prix < 0) {
        $erreurs[] = 'Le prix doit être positif.';
    } elseif ($prix > 1000000) {
        $erreurs[] = 'Le prix est trop élevé (max: 1 000 000 CDF).';
    }
    
    return $erreurs;
}

/**
 * Valider une date
 * @param string $date Date à valider (format YYYY-MM-DD)
 * @param bool $futur La date doit-elle être dans le futur ?
 * @return array Erreurs (tableau vide si valide)
 */
function validerDate($date, $futur = true) {
    $erreurs = [];
    
    if (empty($date)) {
        $erreurs[] = 'La date est obligatoire.';
    } else {
        $timestamp = strtotime($date);
        if ($timestamp === false) {
            $erreurs[] = 'Format de date invalide (attendu: YYYY-MM-DD).';
        } elseif ($futur && $timestamp < time()) {
            $erreurs[] = 'La date ne peut pas être dans le passé.';
        }
    }
    
    return $erreurs;
}

/**
 * Valider une quantité
 * @param mixed $quantite Quantité à valider
 * @return array Erreurs (tableau vide si valide)
 */
function validerQuantite($quantite) {
    $erreurs = [];
    
    if (empty($quantite) && $quantite !== '0') {
        $erreurs[] = 'La quantité est obligatoire.';
    } elseif (!is_numeric($quantite)) {
        $erreurs[] = 'La quantité doit être un nombre.';
    } elseif ($quantite < 0) {
        $erreurs[] = 'La quantité doit être positive ou zéro.';
    } elseif ($quantite > 999999) {
        $erreurs[] = 'La quantité est trop élevée (max: 999 999).';
    }
    
    return $erreurs;
}

// ========== FONCTIONS DE SÉCURITÉ ==========

/**
 * Nettoyer une chaîne de caractères
 * @param mixed $data Données à nettoyer
 * @return string Chaîne nettoyée
 */
function nettoyerEntree($data) {
    if (is_array($data)) {
        return array_map('nettoyerEntree', $data);
    }
    return trim(stripslashes(htmlspecialchars($data, ENT_QUOTES, 'UTF-8')));
}

/**
 * Formater un prix pour l'affichage
 * @param float $prix Prix à formater
 * @param string $devise Devise (par défaut: CDF)
 * @return string Prix formaté
 */
function formatePrix($prix, $devise = 'CDF') {
    return number_format($prix, 2, ',', ' ') . ' ' . $devise;
}

/**
 * Calculer le prix TTC
 * @param float $prixHT Prix hors taxe
 * @param float $tauxTVA Taux de TVA en %
 * @return float Prix TTC
 */
function calculerPrixTTC($prixHT, $tauxTVA = 16) {
    return $prixHT * (1 + $tauxTVA / 100);
}

// ========== FONCTIONS DE STATUT ==========

/**
 * Vérifier si un produit est expiré
 * @param string $dateExpiration Date d'expiration
 * @return bool true si expiré, false sinon
 */
function estExpire($dateExpiration) {
    return strtotime($dateExpiration) < time();
}

/**
 * Obtenir le statut du stock
 * @param int $quantite Quantité en stock
 * @return string Statut ('bas', 'moyen', 'bon')
 */
function getStatutStock($quantite) {
    if ($quantite < 5) return 'critique';
    if ($quantite < 15) return 'bas';
    if ($quantite < 50) return 'moyen';
    return 'bon';
}

?>
