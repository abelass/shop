<?php
if (!defined("_ECRIRE_INC_VERSION")) return;

// fonction pour le pipeline, n'a rien a effectuer
function shop_autoriser(){}

// declarations d'autorisations

// Édition
// modifier
function autoriser_shop_modifier_dist($faire, $type, $id, $qui, $opt) {
    return $qui['statut'] == '0minirezo' AND !$qui['restreint'];
}


// détecte la présence d'un commercant
function autoriser_commercant_dist($faire, $type, $id, $qui, $opt='') {

	$commercant= sql_getfetsel('commercant','spip_auteurs_elargis','id_auteur='.sql_quote($qui['id_auteur']));
	
	if($commercant) $retour=true;

	return $retour;
}
?>