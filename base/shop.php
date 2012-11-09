<?php
if (!defined("_ECRIRE_INC_VERSION")) return;

function shop_declarer_tables_interfaces($tables_interfaces){

    $tables_interfaces['table_des_tables']['shop_prix'] = 'shop_prix';
    $tables_interfaces['table_des_tables']['commandes'] = 'commandes';
    $tables_interfaces['table_des_tables']['geo_pays'] = 'geo_pays';    
    
    return $tables_interfaces;
}

function shop_declarer_tables_principales($tables_principales){
	$spip_commandes = array(
		"id_commande" 			=> "int(21) NOT NULL",
		"id_panier" 			=> "int(21) NOT NULL",		
		"nom_societe" 			=> "varchar(255) NOT NULL",		
		"prenom" 				=> "varchar(255) NOT NULL",
		"nom_famille" 			=> "varchar(255) NOT NULL",		
		"email" 				=> "varchar(255) NOT NULL",
		"adresse" 				=> "text NOT NULL",		
		"code_postal" 			=> "varchar(255) NOT NULL",
		"telephone" 			=> "varchar(255) NOT NULL",		
		"ville" 				=> "text NOT NULL",
		"pays" 					=> "int(3) NOT NULL",		
		"id_produit" 			=> "text NOT NULL",
		"conteneur" 			=> "varchar(50) NOT NULL",
		"numero_tva" 			=> "varchar(255) NOT NULL",
		"commentaire" 			=> "text NOT NULL",
		"montant" 				=> "float NOT NULL",
		"frais_livraison" 		=> "float NOT NULL",
		"adresse_livraison" 	=> "varchar(255) NOT NULL",
		"code_postal_livraison" => "varchar(255) NOT NULL",
		"ville_livraison" 		=> "text NOT NULL",
		"pays_livraison" 		=> "int(3) NOT NULL",	
		"date_livraison" 		=> "datetime DEFAULT '0000-00-00 00:00:00' NOT NULL",						
		"code_devise" 		=> "varchar(3) NOT NULL",
		"statut" 				=> "varchar(255) NOT NULL",	
		"type_paiement" 		=> "varchar(55) NOT NULL",
		"statut_paiement" 		=> "varchar(255) NOT NULL",
		"traitement" 			=> "varchar(255) NOT NULL",		
		"date_enregistrement" 	=> "datetime DEFAULT '0000-00-00 00:00:00' NOT NULL",		
		"date_creation" 		=> "datetime DEFAULT '0000-00-00 00:00:00' NOT NULL",		
		"maj" 					=> "TIMESTAMP");
	
	$spip_commandes_key = array(
		"PRIMARY KEY" 	=> "id_commande",
		"KEY id_panier" 	=> "id_panier",		
		);
		
	$spip_commandes_join = array(
		"id_commande"	=> "id_commande",
		"id_panier"	=> "id_panier",		
		);

	$tables_principales['spip_commandes'] = array(
		'field' => &$spip_commandes,
		'key' => &$spip_commandes_key,
		'join' => &$spip_commandes_join
	);

	$spip_prix = array(
		"id_prix" 	=> "int(21) NOT NULL",
		"id_objet" 	=> "int(21) NOT NULL",
		'objet' => 'varchar(25) not null default ""',		
		"code_devise" 	=> "varchar(3) NOT NULL",
		"prix" 		=> "float (38,2) NOT NULL",
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
