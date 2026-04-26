```
SYSTÈME DE FACTURATION AVEC LECTURE DE CODES-BARRES
Université Protestante au Congo - 2025-2026
═════════════════════════════════════════════════════════════════════════════════

📁 PHP_PROJECT/
│
├── 🔧 FICHIERS DE CONFIGURATION
│   ├── index.php                      ← Accueil / Dashboard
│   ├── README.txt                     ← Guide installation & déploiement
│   ├── RESUME_IMPLEMENTATION.md       ← Résumé Partie 1
│   ├── PARTIE_1_MARDOCHE.md           ← Documentation technique Partie 1
│   ├── test_partie1.php               ← Tests automatisés
│   └── .gitignore                     ← Fichiers git à ignorer
│
├── ⚙️  config/
│   └── constants.php                  ← Chemins, constantes, rôles
│
├── 🔐 auth/
│   ├── login.php                      ← Connexion
│   ├── logout.php                     ← Déconnexion
│   ├── session.php                    ← Gestion sessions & rôles
│   └── includes/
│       └── fonctions.php              ← Auth helper functions
│
├── 📚 includes/                        ← CŒUR FONCTIONNEL
│   ├── fonctions.php                  ← Fonctions générales
│   │   ├─ lireJSON()                  ├─ Lecture/écriture JSON
│   │   ├─ ecrireJSON()                ├─ Validation données
│   │   ├─ validerProduit()            ├─ Opérations produits
│   │   └─ ... (20+ fonctions)         └─ Utilitaires
│   ├── fonctions-produits.php         ← Fonctions produits (Partie 1)
│   │   ├─ obtenirTousLesProduits()
│   │   ├─ ajouterProduit()
│   │   ├─ modifierProduit()
│   │   └─ validerProduit()
│   ├── fonctions-factures.php         ← [PARTIE 2 - Tsaphnath]
│   └── fonctions-auth.php             ← [PARTIE 3 - Prince]
│
├── 📦 modules/                         ← MODULES FONCTIONNELS
│   │
│   ├── 📦 produits/                    ← PARTIE 1: MARDOCHÉ ✅
│   │   ├── scanner.php                ← Enregistrement de produits
│   │   │   ├─ Scanner caméra (QuaggaJS)
│   │   │   ├─ Formulaire d'enregistrement
│   │   │   ├─ Validation serveur
│   │   │   └─ Gestion erreurs
│   │   ├── liste.php                  ← Catalogue des produits
│   │   │   ├─ Liste complète
│   │   │   ├─ Tri multi-colonnes
│   │   │   ├─ Statistiques
│   │   │   └─ Actions (modifier)
│   │   └── traiter_produit.php        ← API produits (AJAX)
│   │       ├─ get_product
│   │       ├─ list_all
│   │       └─ check_code
│   │
│   ├── 💰 facturation/                ← PARTIE 2: TSAPHNATH [FUTUR]
│   │   ├── nouvelle-facture.php       ← Créer facture
│   │   ├── calcul.php                 ← Calculs TVA/totaux
│   │   └── afficher-facture.php       ← Affichage facture
│   │
│   └── 👥 admin/                       ← PARTIE 3: PRINCE [FUTUR]
│       ├── gestion-comptes.php        ← Gérer utilisateurs
│       ├── ajouter-compte.php         ← Créer compte
│       └── supprimer-compte.php       ← Supprimer compte
│
├── 💾 data/                            ← PERSISTANCE (Fichiers JSON)
│   ├── produits.json                  ← {"code_barre": "", "nom": "", ...}
│   ├── factures.json                  ← {"id_facture": "", "date": "", ...}
│   └── utilisateurs.json              ← {"identifiant": "", "role": "", ...}
│
└── 🎨 assets/
    ├── css/
    │   └── style.css                  ← Feuille de style principal
    │       ├─ Variables CSS
    │       ├─ Composants
    │       ├─ Responsive design
    │       └─ Animations
    └── js/
        └── scanner.js                 ← [OPTIONNEL] Code scanner

═════════════════════════════════════════════════════════════════════════════════

🎯 PARTIE 1: ENREGISTREMENT DES PRODUITS (MARDOCHÉ) ✅ COMPLÉTÉ

STATUS: ✅ COMPLÉTÉ ET TESTÉ

Fonctionnalités implémentées:
  ✅ Scanner codes-barres (QuaggaJS)
  ✅ Saisie manuelle codes
  ✅ Formulaire enregistrement/modification
  ✅ Validation côté serveur
  ✅ Persistance JSON
  ✅ Gestion erreurs + conservation données
  ✅ Contrôle d'accès (Manager, Super Admin)
  ✅ Catalogue avec tri et statistiques
  ✅ Interface responsive et moderne
  ✅ Sécurité (HTML escape, rôles, sessions)

Fichiers créés:
  • modules/produits/scanner.php (NEW)
  • modules/produits/liste.php (MODIFIÉ)
  • includes/fonctions-produits.php (NEW)
  • includes/fonctions.php (NEW)
  • config/constants.php (MODIFIÉ)
  • auth/session.php (MODIFIÉ)
  • auth/login.php (NEW)
  • auth/logout.php (NEW)
  • index.php (NEW)
  • assets/css/style.css (NEW)
  • data/*.json (NEW)
  • docs & tests (NEW)

═════════════════════════════════════════════════════════════════════════════════

📋 PARTIE 2: FACTURATION (TSAPHNATH) [EN ATTENTE]

TODO:
  ⬜ Interface caissier (scanner produits)
  ⬜ Panier de facture (session/structure)
  ⬜ Calcul TVA et totaux
  ⬜ Génération ID facture (FAC-YYYYMMDD-NNN)
  ⬜ Sauvegarde data/factures.json
  ⬜ Décrémentation stock
  ⬜ Blocage si quantité > stock
  ⬜ Affichage/impression facture

═════════════════════════════════════════════════════════════════════════════════

👥 PARTIE 3: GESTION COMPTES (PRINCE) [EN ATTENTE]

TODO:
  ⬜ Page connexion
  ⬜ Gestion utilisateurs (CRUD)
  ⬜ Hachage password_hash() / password_verify()
  ⬜ Création compte Super Admin initial
  ⬜ Système rôles (RBAC)
  ⬜ Redirection accès non autorisé
  ⬜ Pages admin (créer/supprimer comptes)

═════════════════════════════════════════════════════════════════════════════════

📄 RAPPORT TECHNIQUE (LATEX) [COLLECTIF]

Contributions:
  • Mardoché: Arborescence, structure produits
  • Tsaphnath: Structure factures, diagramme flux
  • Prince: Structure utilisateurs, authentification
  • Tous: Introduction, conclusion, difficultés, relecture

Contenu obligatoire (15+ pages):
  1. Page de garde (noms, niveau, année)
  2. Introduction (présentation, contexte)
  3. Arborescence commentée (description fichiers)
  4. Structures de données (formats JSON)
  5. Description modules (logique, fonctions)
  6. Diagramme de flux (transaction vente)
  7. Difficultés rencontrées
  8. Conclusion et améliorations

═════════════════════════════════════════════════════════════════════════════════

🚀 GUIDE RAPIDE UTILISATION

AJOUTER PRODUIT:
  1. http://localhost/.../modules/produits/scanner.php
  2. Scanner/saisir code-barres
  3. Remplir infos (nom, prix, expiration, stock)
  4. Valider

VOIR CATALOGUE:
  1. http://localhost/.../modules/produits/liste.php
  2. Voir tous produits avec stats
  3. Trier par colonnes
  4. Modifier via liens

CRÉER FACTURE: [PARTIE 2]
  1. http://localhost/.../modules/facturation/nouvelle-facture.php
  2. Scanner produits
  3. Entrer quantités
  4. Valider/imprimer

GÉRER COMPTES: [PARTIE 3]
  1. http://localhost/.../modules/admin/gestion-comptes.php
  2. Créer/supprimer utilisateurs
  3. Assigner rôles

═════════════════════════════════════════════════════════════════════════════════

🔒 RÔLES ET PERMISSIONS

CAISSIER:
  ✓ Consulter produits
  ✓ Créer factures
  ✓ Consulter ses factures
  ✗ Gérer catalogue
  ✗ Gérer utilisateurs

MANAGER:
  ✓ Tout ce que Caissier + ...
  ✓ Enregistrer produits
  ✓ Modifier produits
  ✓ Consulter rapports
  ✗ Gérer utilisateurs

SUPER ADMINISTRATEUR:
  ✓ TOUT

═════════════════════════════════════════════════════════════════════════════════

📊 DONNÉES JSON EXAMPLES

PRODUIT:
{
  "code_barre": "3017620422003",
  "nom": "Vain amour 1L",
  "prix_unitaire_ht": 1200.50,
  "date_expiration": "2026-12-31",
  "quantite_stock": 50,
  "date_enregistrement": "2026-04-17"
}

UTILISATEUR:
{
  "identifiant": "manager.jean",
  "mot_de_passe": "$2y$10$...",
  "role": "manager",
  "nom_complet": "Jean Dupont",
  "date_creation": "2026-04-17",
  "actif": true
}

FACTURE: [PART 2]
{
  "id_facture": "FAC-20260417-001",
  "date": "2026-04-17",
  "heure": "10:35:22",
  "caissier": "jean.dupont",
  "articles": [...],
  "total_ht": 2400,
  "tva": 432,
  "total_ttc": 2832
}

═════════════════════════════════════════════════════════════════════════════════

✅ CHECKLIST FINALE

PARTIE 1 (MARDOCHÉ):
  ✅ Module produits créé
  ✅ Scanner codes-barres
  ✅ Formulaire enregistrement
  ✅ Validation côté serveur
  ✅ Persistance JSON
  ✅ Gestion erreurs
  ✅ Contrôle accès
  ✅ Interface ergonomique
  ✅ Documentation complète
  ✅ Tests automatisés

PARTIE 2 (TSAPHNATH): [À FAIRE]
  ⬜ Module facturation
  ⬜ Calculs TVA
  ⬜ Sauvegarde factures
  ⬜ Mise à jour stock

PARTIE 3 (PRINCE): [À FAIRE]
  ⬜ Module admin
  ⬜ Authentification
  ⬜ RBAC
  ⬜ Gestion utilisateurs

RAPPORT (COLLECTIF): [À FAIRE]
  ⬜ Rédaction LaTeX
  ⬜ Compilation PDF
  ⬜ Relecture finale

═════════════════════════════════════════════════════════════════════════════════

Last Updated: April 26, 2026
Status: PARTIE 1 COMPLÉTÉE ✅
```
