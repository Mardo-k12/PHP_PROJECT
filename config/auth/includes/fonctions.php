<?php
function lireJSON($fichier) {
    if (!file_exists($fichier)) {
        return [];
    }
    $contenu = file_get_contents($fichier);
    return json_decode($contenu, true) ?? [];
}

function ecrireJSON($fichier, $donnees) {
    file_put_contents($fichier, json_encode($donnees, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE));
}

function produitExiste($codeBarre, $produits) {
    foreach ($produits as $p) {
        if ($p['code_barre'] === $codeBarre) {
            return $p;
        }
    }
    return null;
}
?>