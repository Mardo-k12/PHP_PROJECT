<?php
// Chemins de base
define('ROOT_PATH', __DIR__ . '/..');

// Définition des rôles
define('ROLE_CAISSIER', 'caissier');
define('ROLE_MANAGER', 'manager');
define('ROLE_SUPER_ADMIN', 'super_admin');

// Chemins des données
define('DATA_DIR', ROOT_PATH . '/data/');
define('PRODUITS_FILE', DATA_DIR . 'produits.json');
define('FACTURES_FILE', DATA_DIR . 'factures.json');
define('UTILISATEURS_FILE', DATA_DIR . 'utilisateurs.json');

// Configuration
define('TVA_RATE', 0.18); // Taux de TVA (18%)
define('DEVISE', 'CDF'); // Devise
?>