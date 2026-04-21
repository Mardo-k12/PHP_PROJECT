# 🧾 Système de Facturation avec Lecture de Codes-Barres

## 📌 Description

Projet universitaire – UPC, Faculté des Sciences informatiques.  
Développement d’un système de caisse informatisé en **PHP procédural** avec :

- Lecture de codes-barres via caméra (QuaggaJS)
- Gestion de produits, stocks et factures
- Authentification et contrôle d’accès par rôles (Caissier, Manager, Super Admin)
- Persistance des données exclusivement par fichiers **JSON** (pas de base de données)

---

## 👥 Équipe

| Membre      | Rôle                                                                 |
|-------------|----------------------------------------------------------------------|
| **Mardochée** | Module produits, scan codes-barres, validation serveur, fichiers JSON |
| **Tsaphnath**| Module facturation, panier, calculs TVA, mise à jour stock           |
| **Prince**   | Authentification, gestion des comptes, sessions, contrôle d’accès    |

---

## 🛠️ Technologies utilisées

- PHP 7.4+ (procédural)
- HTML5 / CSS3
- JavaScript (QuaggaJS pour la caméra)
- JSON (stockage)
- Git / GitHub (versionnement)

---

## 📁 Structure du projet
├── config/
│ └── constants.php
├── auth/
│ ├── login.php
│ ├── logout.php
│ └── session.php
├── modules/
│ ├── produits/
│ │ ├── scanner.php
│ │ ├── traiter_produit.php
│ │ └── liste.php
│ ├── facturation/
│ │ ├── caisse.php
│ │ ├── ajouter_article.php
│ │ └── valider_facture.php
│ └── admin/
│ └── gestion_comptes.php
├── data/
│ ├── produits.json
│ ├── factures.json
│ └── utilisateurs.json
├── includes/
│ └── fonctions.php
├── assets/
│ ├── css/
│ └── js/
│ └── quagga-init.js
└── rapports/