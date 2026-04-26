#!/usr/bin/env php
<?php
/**
 * Script de test pour vérifier l'installation et le fonctionnement
 * du Système de Facturation - Partie 1 (Enregistrement des produits)
 * 
 * Usage: php test_partie1.php
 */

echo "═══════════════════════════════════════════════════════════════════════════\n";
echo "TEST - SYSTÈME DE FACTURATION - PARTIE 1: ENREGISTREMENT DES PRODUITS\n";
echo "═══════════════════════════════════════════════════════════════════════════\n\n";

$ROOT_PATH = __DIR__;
require_once $ROOT_PATH . '/config/constants.php';
require_once $ROOT_PATH . '/includes/fonctions.php';
require_once $ROOT_PATH . '/includes/fonctions-produits.php';

$tests_passes = 0;
$tests_fails = 0;

function test($nom, $condition) {
    global $tests_passes, $tests_fails;
    $symbole = $condition ? "✓ PASS" : "✗ FAIL";
    $couleur = $condition ? "\033[32m" : "\033[31m";
    echo "$couleur$symbole\033[0m: $nom\n";
    
    if ($condition) {
        $tests_passes++;
    } else {
        $tests_fails++;
    }
}

// ============================================================================
// TEST 1: Vérifier les constantes
// ============================================================================
echo "\n1. VÉRIFICATION DES CONSTANTES\n";
echo "───────────────────────────────────────────────────────────────────────────\n";

test("ROOT_PATH défini", defined('ROOT_PATH'));
test("PRODUITS_FILE défini", defined('PRODUITS_FILE'));
test("TVA_RATE défini", defined('TVA_RATE'));
test("ROLE_MANAGER défini", defined('ROLE_MANAGER'));

echo "ROOT_PATH: " . ROOT_PATH . "\n";
echo "PRODUITS_FILE: " . PRODUITS_FILE . "\n";

// ============================================================================
// TEST 2: Vérifier les dossiers
// ============================================================================
echo "\n2. VÉRIFICATION DES DOSSIERS\n";
echo "───────────────────────────────────────────────────────────────────────────\n";

test("Dossier data/ existe", is_dir(DATA_DIR));
test("Dossier modules/ existe", is_dir(ROOT_PATH . '/modules'));
test("Dossier includes/ existe", is_dir(ROOT_PATH . '/includes'));
test("Dossier auth/ existe", is_dir(ROOT_PATH . '/auth'));
test("Dossier config/ existe", is_dir(ROOT_PATH . '/config'));
test("Dossier assets/ existe", is_dir(ROOT_PATH . '/assets'));

// ============================================================================
// TEST 3: Vérifier les fichiers
// ============================================================================
echo "\n3. VÉRIFICATION DES FICHIERS\n";
echo "───────────────────────────────────────────────────────────────────────────\n";

test("produits.json existe", file_exists(PRODUITS_FILE));
test("utilisateurs.json existe", file_exists(UTILISATEURS_FILE));
test("factures.json existe", file_exists(FACTURES_FILE));
test("scanner.php existe", file_exists(ROOT_PATH . '/modules/produits/scanner.php'));
test("liste.php existe", file_exists(ROOT_PATH . '/modules/produits/liste.php'));
test("fonctions.php existe", file_exists(ROOT_PATH . '/includes/fonctions.php'));
test("fonctions-produits.php existe", file_exists(ROOT_PATH . '/includes/fonctions-produits.php'));

// ============================================================================
// TEST 4: Vérifier les permissions d'écriture
// ============================================================================
echo "\n4. VÉRIFICATION DES PERMISSIONS\n";
echo "───────────────────────────────────────────────────────────────────────────\n";

test("data/ est readable", is_readable(DATA_DIR));
test("data/ est writable", is_writable(DATA_DIR));
test("produits.json est readable", is_readable(PRODUITS_FILE));
test("produits.json est writable", is_writable(PRODUITS_FILE));

// ============================================================================
// TEST 5: Tester les fonctions JSON
// ============================================================================
echo "\n5. TEST DES FONCTIONS JSON\n";
echo "───────────────────────────────────────────────────────────────────────────\n";

$test_file = DATA_DIR . 'test_' . time() . '.json';
$test_data = [
    'test' => 'données',
    'nombre' => 123,
    'array' => [1, 2, 3]
];

ecrireJSON($test_file, $test_data);
test("ecrireJSON() fonctionne", file_exists($test_file));

$data_lue = lireJSON($test_file);
test("lireJSON() fonctionne", is_array($data_lue));
test("Données correctes après lecture", $data_lue['test'] === 'données');

unlink($test_file);

// ============================================================================
// TEST 6: Tester la validation
// ============================================================================
echo "\n6. TEST DES VALIDATIONS\n";
echo "───────────────────────────────────────────────────────────────────────────\n";

$produit_valide = [
    'code_barre' => '123456789',
    'nom' => 'Produit Test',
    'prix_unitaire_ht' => 1200,
    'date_expiration' => '2026-12-31',
    'quantite_stock' => 50
];

$erreurs = validerProduit($produit_valide);
test("Validation d'un produit valide", count($erreurs) === 0);

$produit_invalide = [
    'code_barre' => '',
    'nom' => '',
    'prix_unitaire_ht' => 'abc',
    'date_expiration' => 'invalid',
    'quantite_stock' => -5
];

$erreurs = validerProduit($produit_invalide);
test("Validation d'un produit invalide (erreurs détectées)", count($erreurs) > 0);

// ============================================================================
// TEST 7: Tester la validation de date
// ============================================================================
echo "\n7. TEST DE VALIDATION DE DATE\n";
echo "───────────────────────────────────────────────────────────────────────────\n";

test("Date valide (2026-12-31)", validerDate('2026-12-31') === true);
test("Date invalide (32-13-2026)", validerDate('2026-13-32') === false);
test("Date invalide (format texte)", validerDate('2026-12-31 00:00:00') === false);
test("Date valide (2000-02-29 bisextile)", validerDate('2000-02-29') === true);

// ============================================================================
// TEST 8: Tester les opérations produit
// ============================================================================
echo "\n8. TEST DES OPÉRATIONS PRODUIT\n";
echo "───────────────────────────────────────────────────────────────────────────\n";

$fichier_test = DATA_DIR . 'produits_test_' . time() . '.json';
ecrireJSON($fichier_test, []);

$resultat = ajouterProduit($produit_valide, $fichier_test);
test("Ajout d'un produit valide", $resultat['succes'] === true);

$produits_lus = lireJSON($fichier_test);
test("Produit présent après ajout", count($produits_lus) === 1);

$produit_trouve = obtenirProduitParCodeBarre('123456789', $fichier_test);
test("Recherche produit par code-barres", $produit_trouve !== null);
test("Code-barres correct", $produit_trouve['code_barre'] === '123456789');

// Modification
$produit_valide['nom'] = 'Produit Test Modifié';
$resultat = modifierProduit('123456789', $produit_valide, $fichier_test);
test("Modification d'un produit", $resultat['succes'] === true);

$produit_modifie = obtenirProduitParCodeBarre('123456789', $fichier_test);
test("Nom modifié correctement", $produit_modifie['nom'] === 'Produit Test Modifié');

unlink($fichier_test);

// ============================================================================
// TEST 9: Tester les fonctions d'échappement
// ============================================================================
echo "\n9. TEST DES FONCTIONS DE SÉCURITÉ\n";
echo "───────────────────────────────────────────────────────────────────────────\n";

$texte_dangereux = '<script>alert("XSS")</script>';
$texte_echappe = echapper($texte_dangereux);
test("Échappement HTML", strpos($texte_echappe, '<script>') === false);

// ============================================================================
// TEST 10: Tester l'authentification
// ============================================================================
echo "\n10. TEST AUTHENTIFICATION\n";
echo "───────────────────────────────────────────────────────────────────────────\n";

$mdp = 'password123';
$hash = password_hash($mdp, PASSWORD_BCRYPT);

// Créer un test utilisateur
$utilisateur_test = [
    'identifiant' => 'test.user',
    'mot_de_passe' => $hash,
    'role' => 'manager',
    'nom_complet' => 'Test User',
    'date_creation' => date('Y-m-d'),
    'actif' => true
];

$fichier_users = DATA_DIR . 'users_test_' . time() . '.json';
ecrireJSON($fichier_users, [$utilisateur_test]);

$user_auth = authentifierUtilisateur('test.user', $mdp, $fichier_users);
test("Authentification utilisateur valide", $user_auth !== null);

$user_fail = authentifierUtilisateur('test.user', 'mauvais_mdp', $fichier_users);
test("Rejet mauvais mot de passe", $user_fail === null);

unlink($fichier_users);

// ============================================================================
// RÉSUMÉ
// ============================================================================
echo "\n═══════════════════════════════════════════════════════════════════════════\n";
echo "RÉSUMÉ DES TESTS\n";
echo "═══════════════════════════════════════════════════════════════════════════\n";

$total = $tests_passes + $tests_fails;
$pourcentage = $total > 0 ? ($tests_passes / $total) * 100 : 0;

echo "\n✓ Tests réussis:  $tests_passes\n";
echo "✗ Tests échoués:  $tests_fails\n";
echo "─ Total:          $total\n";
echo "\nPourcentage de réussite: " . number_format($pourcentage, 1) . "%\n";

if ($tests_fails === 0) {
    echo "\n\033[32m✓ TOUS LES TESTS SONT PASSÉS!\033[0m\n";
    echo "Le système est prêt à être utilisé.\n";
    exit(0);
} else {
    echo "\n\033[31m✗ CERTAINS TESTS ONT ÉCHOUÉ\033[0m\n";
    echo "Veuillez vérifier la configuration et les permissions.\n";
    exit(1);
}

?>
