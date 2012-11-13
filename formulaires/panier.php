<?php

// Sécurité
if (!defined('_ECRIRE_INC_VERSION')) return;


function formulaires_panier_charger($id_panier=0){
	include_spip('inc/session');
	
	// On commence par chercher le panier du visiteur actuel s'il n'est pas donné
	if (!$id_panier) $id_panier = session_get('id_panier');
	
	$contexte = array(
		'_id_panier' => $id_panier,
		'quantites' => array()
	);
	
	return $contexte;
}

function formulaires_panier_verifier($id_panier=0){
	$erreurs = array();
	
	$quantites = _request('quantites');
	
	if (is_array($quantites))
		foreach($quantites as $objet => $objets_de_ce_type)
			foreach($objets_de_ce_type as $id_objet => $quantite){
				if (!is_numeric($quantite) or $quantite != intval($quantite)){
					$erreurs['message_erreur'] = _T('paniers:panier_erreur_quantites');
					$erreurs['quantites'][$objet][$id_objet] = 'erreur';
				}
			}
	
	return $erreurs;
}

function formulaires_panier_traiter($id_panier=0){
	$retours = array();
	
	// On commence par chercher le panier du visiteur actuel s'il n'est pas donné
	if (!$id_panier) $id_panier = session_get('id_panier');
	
	$quantites = _request('quantites');
	$ok = true;
	
	if (is_array($quantites))
		foreach($quantites as $objet => $objets_de_ce_type)
			foreach($objets_de_ce_type as $id_objet => $quantite){
				$quantite = intval($quantite);
				// Si la quantite est 0, on supprime du panier
				if (!$quantite)
					$ok &= sql_delete(
						'spip_paniers_liens',
						'id_panier = '.intval($id_panier).' and objet = '.sql_quote($objet).' and id_objet = '.intval($id_objet)
					);
				// Sinon on met à jour
				else{
					$ok &= sql_updateq(
						'spip_paniers_liens',
						array('quantite' => $quantite),
						'id_panier = '.intval($id_panier).' and objet = '.sql_quote($objet).' and id_objet = '.intval($id_objet)
					);
				}
			}
	
	// Mais dans tous les cas on met la date du panier à jour
	sql_updateq(
		'spip_paniers',
		array('date'=>'NOW()'),
		'id_panier = '.$id_panier
	);
	
	if ($ok)
		$retours['message_ok'] = _T('paniers:panier_quantite_ok');
	
	return $retours;
}

?>
