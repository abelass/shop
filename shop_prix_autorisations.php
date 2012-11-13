<?php
if (!defined("_ECRIRE_INC_VERSION")) return;

// fonction pour le pipeline, n'a rien a effectuer
function shop_prix_autoriser(){}

// declarations d'autorisations

// Édition
// modifier
function autoriser_prix_modifier_dist($faire, $type, $id, $qui, $opt) {
    return $qui['statut'] == '0minirezo' AND !$qui['restreint'];
}

?>