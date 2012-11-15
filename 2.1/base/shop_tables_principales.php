<?php
if (!defined("_ECRIRE_INC_VERSION")) return;
//
// Formulaires : Structure
//

function shop_declarer_tables_principales($tables_principales){
	$spip_shop_commande = array(
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
		"date_livraison" 		=> "date DEFAULT '0000-00-00' NOT NULL",
		"adresse_livraison" 	=> "varchar(255) NOT NULL",
		"code_postal_livraison" => "varchar(255) NOT NULL",
		"ville_livraison" 		=> "text NOT NULL",
		"pays_livraison" 		=> "int(3) NOT NULL",	
		"date_livraison" 		=> "date DEFAULT '0000-00-00' NOT NULL",						
		"code_devise" 		=> "varchar(3) NOT NULL",
		"statut" 				=> "varchar(255) NOT NULL",	
		"type_paiement" 		=> "varchar(55) NOT NULL",
		"statut_paiement" 		=> "varchar(255) NOT NULL",
		"traitement" 			=> "varchar(255) NOT NULL",		
		"date_enregistrement" 	=> "date DEFAULT '0000-00-00' NOT NULL",		
		"date_creation" 		=> "datetime DEFAULT '0000-00-00 00:00:00' NOT NULL",		
		"maj" 					=> "TIMESTAMP");
	
	$spip_shop_commande_key = array(
		"PRIMARY KEY" 	=> "id_commande",
		"KEY id_panier" 	=> "id_panier",		
		);
		
	$spip_shop_commande_join = array(
		"id_commande"	=> "id_commande",
		"id_panier"	=> "id_panier",		
		);

	$tables_principales['spip_shop_commande'] = array(
		'field' => &$spip_shop_commande,
		'key' => &$spip_shop_commande_key,
		'join' => &$spip_shop_commande_join
	);

	$spip_shop_prix = array(
		"id_prix_objet" 	=> "int(21) NOT NULL",
		"id_objet" 	=> "int(21) NOT NULL",
		'objet' => 'varchar(25) not null default ""',		
		"code_devise" 	=> "varchar(3) NOT NULL",
		"prix" 		=> "float (38,2) NOT NULL",
		);
	
	$spip_shop_prix_key = array(
		"PRIMARY KEY" 	=> "id_prix_objet",
		"KEY id_objet"	=> "id_objet",
		);
		
	$spip_shop_prix_join = array(
		"id_prix_objet"	=> "id_prix_objet",
		"id_objet"	=> "id_objet",
		);

	$tables_principales['spip_shop_prix'] = array(
		'field' => &$spip_shop_prix,
		'key' => &$spip_shop_prix_key,
		'join' => &$spip_shop_prix_join
	);
	
	return $tables_principales;
	
	}
?>
