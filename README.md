# 🧾 Système de Facturation avec Lecture de Codes-Barres

## 📌 Description

Projet universitaire réalisé à l’UPC – Faculté des Sciences Informatiques.

Ce projet consiste en le développement d’un système de caisse informatisé en **PHP procédural**, intégrant :

- 📷 Lecture de codes-barres via caméra (QuaggaJS)
- 📦 Gestion des produits et du stock
- 🧾 Génération et gestion des factures
- 🔐 Authentification et contrôle d’accès par rôles (Caissier, Manager, Super Admin)
- 💾 Persistance des données via fichiers **JSON** (sans base de données)

---

## 👥 Équipe

| Membre        | Responsabilités |
|--------------|----------------|
| **Mardochée**  | Module produits, scan des codes-barres, validation côté serveur, gestion des fichiers JSON |
| **Tsaphnath** | Module facturation, gestion du panier, calcul de la TVA, mise à jour du stock |
| **Prince**    | Authentification, gestion des comptes, sessions, contrôle d’accès |

---

## 🛠️ Technologies utilisées

- **PHP 7.4+** (procédural)
- **HTML5 / CSS3**
- **JavaScript** (QuaggaJS pour la lecture via caméra)
- **JSON** (stockage des données)
- **Git & GitHub** (versionnement)

---

## 📁 Structure du projet

```bash
├── config/
│   └── constants.php
├── auth/
│   ├── login.php
│   ├── logout.php
│   └── session.php
├── modules/
│   ├── produits/
│   │   ├── scanner.php
│   │   ├── traiter_produit.php
│   │   └── liste.php
│   ├── facturation/
│   │   ├── caisse.php
│   │   ├── ajouter_article.php
│   │   └── valider_facture.php
│   └── admin/
│       └── gestion_comptes.php
├── data/
│   ├── produits.json
│   ├── factures.json
│   └── utilisateurs.json
├── includes/
│   └── fonctions.php
├── assets/
│   ├── css/
│   └── js/
│       └── quagga-init.js
└── rapports/