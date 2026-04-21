# 🧾 Système de Facturation avec Lecture de Codes-Barres

## 📌 Description

Ce projet est un **système de caisse informatisé** développé en **PHP procédural** dans le cadre des Travaux Pratiques de Programmation Web à l’Université Protestante au Congo (UPC – Faculté des Sciences informatiques).

Il permet :
- La lecture de codes-barres via la caméra (bibliothèque **QuaggaJS**)
- L’enregistrement et la modification de produits
- La création de factures avec calcul automatique de la TVA
- La gestion du stock (décrémentation après vente)
- Un contrôle d’accès basé sur trois rôles : **Caissier**, **Manager**, **Super Administrateur**

**Contrainte majeure** : persistance des données uniquement par fichiers JSON (pas de base de données).

---

## 👥 Équipe

| Membre          | Rôle                                                                 |
|----------------|----------------------------------------------------------------------|
| **Mardoché**   | Module produits, scan codes-barres, validation serveur, fichiers JSON |
| **Tsaphnath**  | Module facturation, panier, calculs TVA, mise à jour stock           |
| **Prince**     | Authentification, gestion des comptes, sessions, contrôle d’accès    |

---

## 🛠️ Technologies utilisées

- **PHP 7.4+** (procédural)
- **HTML5 / CSS3**
- **JavaScript** (QuaggaJS pour la caméra)
- **JSON** (stockage)
- **Git / GitHub** (versionnement)

---

## 📁 Structure du projet
├── config/
│ └── constants.php # Taux TVA, chemins, rôles
├── auth/
│ ├── login.php # Formulaire de connexion
│ ├── logout.php # Déconnexion
│ └── session.php # Vérification session et rôles
├── modules/
│ ├── produits/
│ │ ├── scanner.php # Scan + formulaire produit
│ │ ├── traiter_produit.php # Ajout/modification produit
│ │ └── liste.php # Catalogue produits
│ ├── facturation/
│ │ ├── caisse.php # Interface caissier
│ │ ├── ajouter_article.php
│ │ └── valider_facture.php
│ └── admin/
│ └── gestion_comptes.php # Gestion utilisateurs (Super Admin)
├── data/
│ ├── produits.json
│ ├── factures.json
│ └── utilisateurs.json
├── includes/
│ └── fonctions.php # Fonctions utilitaires (lireJSON, etc.)
├── assets/
│ ├── css/
│ └── js/
│ └── quagga-init.js
└── rapports/ # Génération rapports (optionnel)