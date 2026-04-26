<?php
require_once __DIR__ . '/../../config/constants.php';
require_once __DIR__ . '/../../auth/session.php';
require_once __DIR__ . '/../../includes/fonctions.php';
require_once __DIR__ . '/../../includes/fonctions-produits.php';

// Vérifier les rôles autorisés (Manager et Super Admin)
verifierRole([ROLE_MANAGER, ROLE_SUPER_ADMIN]);

header('Content-Type: application/json; charset=utf-8');

// Traitement des requêtes AJAX
if ($_SERVER['REQUEST_METHOD'] === 'GET') {
    $action = $_GET['action'] ?? '';
    
    switch ($action) {
        case 'get_product':
            // Récupère un produit par son code-barres
            $code = $_GET['code'] ?? '';
            if ($code) {
                $produit = obtenirProduitParCodeBarre($code, PRODUITS_FILE);
                if ($produit) {
                    echo json_encode([
                        'success' => true,
                        'produit' => $produit,
                        'mode' => 'modification'
                    ]);
                } else {
                    echo json_encode([
                        'success' => false,
                        'message' => 'Produit non trouvé',
                        'mode' => 'nouveau'
                    ]);
                }
            }
            break;
            
        case 'list_all':
            // Liste tous les produits
            $produits = obtenirTousLesProduits(PRODUITS_FILE);
            echo json_encode([
                'success' => true,
                'produits' => $produits,
                'count' => count($produits)
            ]);
            break;
            
        case 'check_code':
            // Vérifie si un code-barres existe
            $code = $_GET['code'] ?? '';
            $produit = obtenirProduitParCodeBarre($code, PRODUITS_FILE);
            echo json_encode([
                'success' => true,
                'existe' => $produit !== null
            ]);
            break;
            
        default:
            echo json_encode([
                'success' => false,
                'message' => 'Action inconnue'
            ]);
    }
} else {
    echo json_encode([
        'success' => false,
        'message' => 'Méthode non autorisée'
    ]);
}
?>
