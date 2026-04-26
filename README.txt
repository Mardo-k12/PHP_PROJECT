================================================================================
SYSTÈME DE FACTURATION AVEC LECTURE DE CODES-BARRES
Travaux Pratiques de Programmation Web en PHP
Université Protestante au Congo - 2025-2026
================================================================================

INSTALLATION ET DÉPLOIEMENT LOCAL

================================================================================
1. PRÉREQUIS
================================================================================

- XAMPP 7.4+ (ou serveur web local avec PHP 7.4+)
- Navigateur web moderne (Chrome, Firefox, Edge, Safari)
- Caméra ou appareil capable de scanner des codes-barres (pour la Partie 1)

================================================================================
2. INSTALLATION
================================================================================

A. Placer le dossier du projet:
   - Extraire le dossier du projet dans: C:\xampp\htdocs\GitHub\PHP_PROJECT
   - OU dans le dossier approprié de votre serveur local

B. Créer les dossiers nécessaires (devrait être automatique):
   - data/          (contient les fichiers JSON de données)
   - modules/       (modules fonctionnels)
   - includes/      (fonctions partagées)
   - auth/          (authentification)
   - config/        (configuration)
   - assets/        (CSS, JavaScript)

C. Permissions:
   - Le dossier 'data/' doit être accessible en lecture/écriture
   - Vérifier les permissions: chmod 755 (ou équivalent Windows)

================================================================================
3. DÉMARRAGE
================================================================================

A. Démarrer XAMPP:
   - Ouvrir le Panneau de Contrôle XAMPP
   - Cliquer sur "Start" pour Apache et MySQL (si nécessaire)

B. Accéder à l'application:
   - Ouvrir le navigateur
   - URL: http://localhost/GitHub/PHP_PROJECT/
   - Vous serez redirigé automatiquement vers la page de connexion

C. Première connexion:
   - Les comptes de démonstration ont des mots de passe hashés
   - Créer les comptes manuellement ou utiliser le script d'initialisation

================================================================================
4. INITIALISATION DES DONNÉES
================================================================================

Les fichiers de données sont fournis vides ou avec des données exemple:

- data/utilisateurs.json   : Contient les comptes utilisateurs
- data/produits.json       : Contient le catalogue de produits
- data/factures.json       : Contient les factures générées

Pour initialiser avec des données de test:
1. Éditer les fichiers JSON manuellement (format JSON valide requis)
2. OU créer un script PHP d'initialisation (scripts/init.php)

Exemple de structure JSON pour utilisateurs.json:
{
    "identifiant": "manager.test",
    "mot_de_passe": "PASSWORD_HASH_ICI",
    "role": "manager",
    "nom_complet": "Test Manager",
    "date_creation": "2026-04-17",
    "actif": true
}

Note: Utiliser password_hash('password', PASSWORD_BCRYPT) pour générer les hashs

================================================================================
5. STRUCTURE DU PROJET
================================================================================

facturation/                    (racine du projet)
├── index.php                   (page d'accueil/dashboard)
├── config/
│   └── constants.php           (constantes et chemins)
├── auth/
│   ├── login.php              (page de connexion)
│   ├── logout.php             (déconnexion)
│   ├── session.php            (gestion des sessions)
│   └── includes/fonctions.php (fonctions d'authentification)
├── modules/
│   ├── produits/
│   │   ├── scanner.php        (enregistrement de produits)
│   │   ├── liste.php          (catalogue de produits)
│   │   └── traiter_produit.php (API pour produits)
│   ├── facturation/
│   │   ├── nouvelle-facture.php (création de facture)
│   │   ├── calcul.php         (calculs de facture)
│   │   └── afficher-facture.php (affichage de facture)
│   ├── admin/
│   │   ├── gestion-comptes.php (gestion des utilisateurs)
│   │   ├── ajouter-compte.php  (création de compte)
│   │   └── supprimer-compte.php (suppression de compte)
│   └── rapports/
│       ├── rapport-journalier.php
│       └── rapport-mensuel.php
├── includes/
│   ├── fonctions.php           (fonctions générales)
│   ├── fonctions-produits.php  (fonctions produits)
│   ├── fonctions-factures.php  (fonctions factures)
│   └── fonctions-auth.php      (fonctions authentification)
├── data/
│   ├── produits.json           (catalogue)
│   ├── factures.json           (factures)
│   └── utilisateurs.json       (utilisateurs)
├── assets/
│   ├── css/
│   │   └── style.css           (styles CSS)
│   └── js/
│       └── scanner.js          (code scanner)
└── README.txt                  (ce fichier)

================================================================================
6. UTILISATION
================================================================================

ÉTAPE 1: Connexion
   - URL: http://localhost/GitHub/PHP_PROJECT/auth/login.php
   - Entrer identifiant et mot de passe
   - Accès selon le rôle

ÉTAPE 2: Navigation selon le rôle
   
   CAISSIER:
   - ✓ Créer des factures
   - ✓ Scanner des produits
   - ✗ Gérer le catalogue
   - ✗ Gérer les utilisateurs
   
   MANAGER:
   - ✓ Créer des factures
   - ✓ Gérer le catalogue (enregistrer/modifier produits)
   - ✓ Consulter les rapports
   - ✗ Gérer les utilisateurs
   
   SUPER ADMINISTRATEUR:
   - ✓ Toutes les permissions
   - ✓ Gérer les utilisateurs (créer/supprimer comptes)
   - ✓ Consulter les rapports

ÉTAPE 3: Enregistrer un produit (Manager uniquement)
   - Aller à "Produits" → "Nouveau Produit"
   - Scanner le code-barres OU le saisir manuellement
   - Remplir les informations
   - Valider

ÉTAPE 4: Créer une facture (Tous)
   - Aller à "Facturation" → "Nouvelle Facture"
   - Scanner les produits un par un
   - Entrer les quantités
   - Valider et imprimer

================================================================================
7. DÉPANNAGE
================================================================================

PROBLÈME: Page blanche
SOLUTION:
  - Vérifier les logs PHP dans c:\xampp\apache\logs\
  - Vérifier la syntaxe PHP: php -l fichier.php
  - Vérifier les permissions des dossiers

PROBLÈME: Impossible de se connecter
SOLUTION:
  - Vérifier que utilisateurs.json existe et est valide
  - Vérifier les mots de passe hashés sont corrects
  - Voir les logs Apache/PHP pour erreurs

PROBLÈME: Erreurs d'écriture dans data/
SOLUTION:
  - Vérifier permissions: chmod 755 data/
  - Vérifier que le serveur a accès en écriture
  - Vérifier que les fichiers JSON sont valides

PROBLÈME: Scanner de codes-barres ne fonctionne pas
SOLUTION:
  - Vérifier que QuaggaJS est chargé (voir console)
  - Vérifier les permissions de caméra du navigateur
  - Essayer un autre navigateur
  - Utiliser la saisie manuelle

PROBLÈME: Fichiers JSON corrompus
SOLUTION:
  - Restaurer depuis backup
  - Valider JSON avec tool online (jsonlint.com)
  - Réinitialiser avec fichiers vides []

================================================================================
8. CONFIGURATION AVANCÉE
================================================================================

A. Changer le taux de TVA:
   - Éditer config/constants.php
   - Modifier: define('TVA_RATE', 0.18);
   - Valeur: 0.18 = 18%

B. Changer la devise:
   - Éditer config/constants.php
   - Modifier: define('DEVISE', 'CDF');

C. Changer le chemin des données:
   - Éditer config/constants.php
   - Modifier: define('DATA_DIR', ROOT_PATH . '/data/');

D. Activer le debug:
   - Ajouter au début de index.php:
   - error_reporting(E_ALL);
   - ini_set('display_errors', 1);

================================================================================
9. SÉCURITÉ
================================================================================

RECOMMANDATIONS:
✓ Changer les mots de passe par défaut
✓ Hashifier tous les mots de passe avec password_hash()
✓ Utiliser HTTPS en production
✓ Ajouter de la validation côté client
✓ Faire des backups réguliers de data/
✓ Limiter l'accès aux fichiers PHP (directives .htaccess)
✓ Mettre à jour PHP régulièrement

DANS PRODUCTION:
- Ajouter un fichier .htaccess pour protéger data/
- Ajouter authentification HTTP supplémentaire
- Utiliser des certificats SSL
- Configurer les entêtes de sécurité

================================================================================
10. SUPPORT ET CONTACT
================================================================================

En cas de problème:
- Consultez le fichier PARTIE_1_MARDOCHE.md pour détails Partie 1
- Vérifier la console navigateur (F12) pour erreurs JavaScript
- Vérifier les logs Apache/PHP

================================================================================
11. NOTES IMPORTANTES
================================================================================

- Ce projet utilise des fichiers JSON, pas de base de données
- Les sessions PHP expirent après 24 minutes d'inactivité
- Les codes-barres doivent être uniques
- La modification d'un produit conserve son code-barres
- Les prix sont en CDF (Franc Congolais)
- Format date: YYYY-MM-DD (ISO 8601)

================================================================================
12. FICHIERS À SAUVEGARDER
================================================================================

Important! Sauvegarder régulièrement:
- data/utilisateurs.json
- data/produits.json
- data/factures.json

================================================================================
VERSION: 1.0
DERNIÈRE MISE À JOUR: 2026-04-26
STATUT: ✅ OPÉRATIONNEL
================================================================================
