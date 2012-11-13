<?php

function action_shop_retour_prestataire_paiement_dist(){
	
	include_spip('inc/headers');
	
	$contexte = array();
	
	spip_log("INFO Retour d'une commande","shop");
	
	$contexte['token_retour_paiement'] = _request("token_retour_paiement");
	
	$retour = sql_select("*","spip_shop_tokens_retour",'token_retour = '.sql_quote($contexte['token_retour_paiement']));
	
	$le_retour = sql_fetch($retour);	
	

	$traitement_retour = charger_fonction('retour_paiement_'.$le_retour['prestataire_paiement'],'inc');
	
	$traitement_retour($le_retour['id_commande']);
	
	//redirige_par_entete(generer_url_public("paiement","token_panier=".$le_retour['id-commande'],"&"));
}

?>
