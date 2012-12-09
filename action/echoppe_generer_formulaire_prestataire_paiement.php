<?php

function action_echoppe_generer_formulaire_prestataire_paiement_dist(){
	
	include_spip('inc/utils');
	include_spip('inc/headers');
	
	$contexte = array();
	
	$contexte["hidden_prestataire"] = Array();
	$contexte["erreur_generation_formulaire"] = Array();
	$contexte["action_prestataire"] = "";
	$contexte["pause"] = 0;
	$contexte["method_prestataire"] = "post";
	$contexte["submit"] = "";
	$contexte["token_panier"] = session_get("echoppe_token_panier");
	
	spip_log("Debut creation formulaire paiement","echoppe");
	
	$generer_donnees_paiement = charger_fonction("generer_donnees_paiement_".session_get('echoppe_prestataire_paiement'),"inc");

	$contexte = $generer_donnees_paiement($contexte);
	
	spip_log("Depart pour le prestatiare de paiement","echoppe");
	
	echo recuperer_fond("prive/paiement/action_paiement",$contexte);


}

?>
