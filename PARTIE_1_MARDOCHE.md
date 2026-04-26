# 📦 Partie 1: Enregistrement des Produits (Mardoché)

## Vue d'ensemble

La **Partie 1** du projet concerne l'enregistrement et la gestion du catalogue de produits. Ce module permet aux responsables (Manager et Super Administrateur) d'ajouter et de modifier les informations des produits, incluant la capture des codes-barres via caméra.

## Responsabilités implémentées

✅ Création du module `modules/produits/`
✅ Développement du formulaire d'ajout et de modification de produit
✅ Validation côté serveur des données saisies en PHP
✅ Lecture et écriture des données dans le fichier `data/produits.json`
✅ Intégration de la bibliothèque JavaScript QuaggaJS pour la capture du code-barres
✅ Gestion des messages d'erreur et conservation des valeurs saisies en cas d'erreur
✅ Vérification des droits d'accès (réservé aux rôles Manager et Super Administrateur)

## Structure des fichiers

```
modules/produits/
├── scanner.php          # Formulaire de scanner/enregistrement de produits
├── liste.php            # Liste complète des produits avec tri
└── traiter_produit.php  # API pour opérations AJAX

includes/
├── fonctions.php        # Fonctions générales (JSON, validation, etc.)
└── fonctions-produits.php  # Fonctions spécifiques aux produits

data/
├── produits.json        # Stockage des produits

assets/
├── css/
│   └── style.css        # Feuilles de style
└── js/
    └── scanner.js       # Code JavaScript pour le scanner (optionnel)

auth/
└── session.php          # Gestion des sessions et droits d'accès
```

## Structure d'un produit (JSON)

```json
{
    "code_barre": "3017620422003",
    "nom": "Vain amour 1L",
    "prix_unitaire_ht": 1200,
    "date_expiration": "2026-12-31",
    "quantite_stock": 50,
    "date_enregistrement": "2026-04-17"
}
```

### Champs détaillés:
- **code_barre** (string): Identifiant unique du produit
- **nom** (string): Nom du produit
- **prix_unitaire_ht** (float): Prix unitaire hors taxe en CDF
- **date_expiration** (date YYYY-MM-DD): Date d'expiration du produit
- **quantite_stock** (integer): Quantité actuelle en stock
- **date_enregistrement** (date YYYY-MM-DD): Date d'enregistrement dans le système

## Pages implémentées

### 1. `modules/produits/scanner.php`

**Objectif**: Interface principale pour scanner un code-barres et enregistrer/modifier un produit

**Fonctionnalités**:
- Scanner de code-barres via caméra (QuaggaJS)
- Saisie manuelle du code-barres
- Détection automatique si le produit existe (mode modification)
- Formulaire pour les informations du produit
- Validation côté serveur
- Messages d'erreur avec conservation des valeurs
- Design responsive et intuitif

**Accès**: Manager, Super Administrateur

**Flux utilisateur**:
1. L'utilisateur scanne ou saisit manuellement le code-barres
2. Le système vérifie si le produit existe
3. Si nouveau → formulaire d'ajout avec champs vides
4. Si existant → formulaire pré-rempli avec données actuelles (mode modification)
5. L'utilisateur remplit/modifie les informations
6. Validation serveur
7. Sauvegarde dans `data/produits.json`
8. Message de confirmation

### 2. `modules/produits/liste.php`

**Objectif**: Afficher la liste complète des produits avec tri et gestion

**Fonctionnalités**:
- Liste complète des produits
- Tri par nom, code-barres, prix ou stock
- Statistiques (nombre de produits, articles en stock, stock faible)
- Indicateurs visuels (stock faible, produit expiré)
- Lien vers modification d'un produit
- Design responsive

**Accès**: Manager, Super Administrateur

**Colonnes affichées**:
- Code-barres
- Nom du produit
- Prix unitaire HT
- Date d'expiration
- Quantité en stock
- Date d'enregistrement
- Actions (modifier)

### 3. `modules/produits/traiter_produit.php`

**Objectif**: API pour les opérations AJAX (optionnel, pour extension future)

**Actions disponibles**:
- `get_product`: Récupère un produit par code-barres
- `list_all`: Liste tous les produits
- `check_code`: Vérifie si un code existe

## Validation des données

La validation est effectuée **côté serveur** pour garantir la sécurité. Voici les règles appliquées:

### Code-barres
- ✓ Obligatoire
- ✓ Non vide après trim()

### Nom du produit
- ✓ Obligatoire
- ✓ Non vide après trim()

### Prix unitaire HT
- ✓ Obligatoire
- ✓ Doit être numérique
- ✓ Doit être positif ou zéro

### Date d'expiration
- ✓ Obligatoire
- ✓ Format YYYY-MM-DD
- ✓ Date valide

### Quantité initiale en stock
- ✓ Obligatoire
- ✓ Doit être numérique (entier)
- ✓ Doit être positif ou zéro

## Gestion des erreurs

**Comportement en cas d'erreur**:
1. Les erreurs sont affichées sous forme de liste
2. Les anciennes valeurs sont conservées dans le formulaire
3. Le formulaire n'est pas réinitialisé (pour correction facile)
4. Redirection vers la même page pour correction

**Messages d'erreur clairs et en français**:
- "Le code-barres est obligatoire."
- "Le nom du produit est obligatoire."
- "Le prix unitaire doit être un nombre positif."
- "Le format de date doit être YYYY-MM-DD."
- "Ce code-barres existe déjà." (lors d'ajout)
- etc.

## Intégration du scanner de codes-barres

**Bibliothèque utilisée**: QuaggaJS (compatible navigateur moderne)

**Fonctionnement**:
1. L'utilisateur clique sur le bouton "📷 Caméra"
2. Demande d'accès à la caméra
3. Flux vidéo en direct sur la page
4. Détection automatique du code-barres
5. Lorsqu'un code est détecté, remplissage du champ et redirection
6. Fermeture automatique de la caméra

**Fallback**: Saisie manuelle possible si caméra indisponible

## Contrôle d'accès

**Rôles autorisés**:
- ✅ Manager
- ✅ Super Administrateur

**Vérification**:
- Effectuée via `verifierRole()` en début de chaque page
- Redirection automatique si non connecté
- Affichage d'erreur si rôle insuffisant

## Functions principales

### Fonctions dans `includes/fonctions.php`:

```php
// Lecture/Écriture JSON
lireJSON($fichier)              // Lit et parse un fichier JSON
ecrireJSON($fichier, $donnees)  // Écrit et formate un JSON

// Produits
produitExiste($codeBarre, $produits)
sauvegarderProduit($produit, $fichier)
validerProduit($donnees)
validerDate($date)
```

### Fonctions dans `includes/fonctions-produits.php`:

```php
// Gestion des produits
obtenirTousLesProduits($fichier)
obtenirProduitParCodeBarre($codeBarre, $fichier)
ajouterProduit($donnees, $fichier)
modifierProduit($codeBarre, $donnees, $fichier)

// Session
sauvegarderAnciennesValeurs($donnees)
sauvegarderErreurs($erreurs)
extraireAnciennesValeurs()
```

## Configuration

Tous les chemins et paramètres globaux sont définis dans `config/constants.php`:

```php
define('ROOT_PATH', __DIR__ . '/..');
define('ROLE_CAISSIER', 'caissier');
define('ROLE_MANAGER', 'manager');
define('ROLE_SUPER_ADMIN', 'super_admin');
define('DATA_DIR', ROOT_PATH . '/data/');
define('PRODUITS_FILE', DATA_DIR . 'produits.json');
define('TVA_RATE', 0.18);
define('DEVISE', 'CDF');
```

## Utilisation

### Ajouter un produit:
1. Aller à `modules/produits/scanner.php`
2. Scanner le code-barres ou le saisir manuellement
3. Remplir les informations du produit
4. Cliquer sur "➕ Ajouter le produit"
5. Confirmé!

### Modifier un produit:
1. Aller à `modules/produits/liste.php`
2. Trouver le produit et cliquer "✏️ Modifier"
3. OU Scanner le même code-barres dans `scanner.php`
4. Modifier les informations
5. Cliquer sur "✏️ Modifier le produit"
6. Confirmé!

### Voir le catalogue:
1. Aller à `modules/produits/liste.php`
2. Voir tous les produits avec tri et filtres
3. Cliquer sur les en-têtes pour trier

## Sécurité

✅ **Validation côté serveur** - Tous les données soumises sont validées
✅ **Échappement HTML** - Utilisation de `htmlspecialchars()` partout
✅ **Contrôle d'accès** - Vérification des rôles obligatoire
✅ **Sessions PHP** - Identification utilisateur sécurisée
✅ **Pas d'injection SQL** - Aucune base de données (fichiers JSON)

## Performances

- ✅ Fichiers JSON chargés en mémoire (petits catalogues)
- ✅ Pas de requête réseau pour chaque action
- ✅ Interface réactive et rapide

## Design et UX

- ✅ Interface moderne et intuitive
- ✅ Design responsive (mobile, tablet, desktop)
- ✅ Messages clairs en français
- ✅ Emojis pour meilleure lisibilité
- ✅ Indicateurs visuels (couleurs, icônes)
- ✅ Gradient agréable et esthétique

## Technologies utilisées

- **Backend**: PHP 7.4+
- **Frontend**: HTML5, CSS3, JavaScript (Vanilla)
- **Stockage**: Fichiers JSON
- **Scanner**: QuaggaJS
- **Sessions**: PHP Sessions

## Prochaines étapes

Pour finaliser le projet:
- **Partie 2** (Tsaphnath): Module de facturation
- **Partie 3** (Prince): Gestion des comptes utilisateurs
- **Rapport** (Collectif): Documentation LaTeX

## Notes supplémentaires

- Les codes-barres sont des identifiants uniques
- La modification d'un produit ne change pas son code-barres
- Le stock sera décrémenté lors de la facturation (Partie 2)
- Le système gère les dates d'expiration pour alertes

---

**Auteur**: Mardoché
**Date**: Avril 2026
**Statut**: ✅ Complété
