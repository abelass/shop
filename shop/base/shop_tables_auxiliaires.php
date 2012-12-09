<?php

if (!defined("_ECRIRE_INC_VERSION")) return;

function shop_declarer_tables_auxiliaires($tables_auxiliaires){

	$spip_tokens_retour = array(
		"id_token_retour"			=> "bigint(21) NOT NULL",
		"id_commande"			=> "bigint(21) NOT NULL",
		"id_paiement"			=> "bigint(21) NOT NULL",
		"token_retour"		=> "VARCHAR(255) NOT NULL",
		"token_retour"		=> "VARCHAR(255) NOT NULL",
		"token_panier"	=>	"VARCHAR(255) NOT NULL",
		"prestataire_paiement" => "VARCHAR(255) NOT NULL"
		);

	$spip_tokens_retour_key = array(
		"PRIMARY KEY"		=> "id_token_retour",
		"KEY token_retour"	=> "token_retour",
		"KEY token_panier"	=> "token_panier"
		);	
		

	$spip_tokens_retour_join = array(
		"token_panier"		=> "token_panier"
		);	
	
	$tables_auxiliaires['spip_shop_tokens_retour'] = array(
		'field' => &$spip_tokens_retour,
		'key' => &$spip_tokens_retour_key,
		'join' => &$spip_tokens_retour_join
	);
			
	$spip_geo_pays = array(
		"id_pays"	=> "bigint(21) NOT NULL",
		"nom"		=> "VARCHAR(255) NOT NULL",
		);

	$spip_geo_pays_key = array(
		"PRIMARY KEY"		=> "id_pays",
		);	
		
	
	$tables_auxiliaires['spip_geo_pays'] = array(
		'field' => &$spip_geo_pays,
		'key' => &$spip_geo_pays_key,
	);			
	

return $tables_auxiliaires;
};
?>
