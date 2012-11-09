<?php
if (!defined("_ECRIRE_INC_VERSION")) return;

function shop_declarer_tables_interfaces($tables_interfaces){

    $tables_interfaces['table_des_tables']['prix'] = 'prix';
    $tables_interfaces['table_des_tables']['geo_pays'] = 'geo_pays';    
    
    return $tables_interfaces;
}

function shop_declarer_tables_principales($tables_principales){
	$spip_prix = array(
		"id_prix" 	=> "int(21) NOT NULL",
		"id_objet" 	=> "int(21) NOT NULL",
		'objet' => 'varchar(25) not null default ""',		
		"code_devise" 	=> "varchar(3) NOT NULL",
		"prix_ht" 		=> "float (38,2) NOT NULL",
        "prix_ttc"       => "float (38,2) NOT NULL",		
		);
	
	$spip_prix_key = array(
		"PRIMARY KEY" 	=> "id_prix",
		"KEY id_objet"	=> "id_objet",
		);
		
	$spip_prix_join = array(
		"id_prix"	=> "id_prix",
		"id_objet"	=> "id_objet",
		);

	$tables_principales['spip_prix'] = array(
		'field' => &$spip_prix,
		'key' => &$spip_prix_key,
		'join' => &$spip_prix_join
	);
	
	return $tables_principales;
	
	}

function shop_declarer_tables_auxiliaires($tables_auxiliaires){

    $spip_tokens_retour = array(
        "id_token_retour"           => "bigint(21) NOT NULL",
        "id_commande"           => "bigint(21) NOT NULL",
        "id_paiement"           => "bigint(21) NOT NULL",
        "token_retour"      => "VARCHAR(255) NOT NULL",
        "token_retour"      => "VARCHAR(255) NOT NULL",
        "token_panier"  =>  "VARCHAR(255) NOT NULL",
        "prestataire_paiement" => "VARCHAR(255) NOT NULL"
        );

    $spip_tokens_retour_key = array(
        "PRIMARY KEY"       => "id_token_retour",
        "KEY token_retour"  => "token_retour",
        "KEY token_panier"  => "token_panier"
        );  
        

    $spip_tokens_retour_join = array(
        "token_panier"      => "token_panier"
        );  
    
    $tables_auxiliaires['spip_shop_tokens_retour'] = array(
        'field' => &$spip_tokens_retour,
        'key' => &$spip_tokens_retour_key,
        'join' => &$spip_tokens_retour_join
    );

return $tables_auxiliaires;
}

?>
