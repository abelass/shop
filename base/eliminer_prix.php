<?php
if (!defined("_ECRIRE_INC_VERSION")) return;

function shop_declarer_tables_interfaces($tables_interfaces){

    $tables_interfaces['table_des_tables']['prix'] = 'prix';
    
    return $tables_interfaces;
}

function shop_declarer_tables_principales($tables_principales){
	$spip_prix = array(
		"id_prix" 	=> "bigint(21) NOT NULL",
		"id_objet" 	=> "bigint(21) NOT NULL",
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
		"id_objet"	=> "id_article",		
		);

	$tables_principales['spip_prix'] = array(
		'field' => &$spip_prix,
		'key' => &$spip_prix_key,
		'join' => &$spip_prix_join
	);
	
	return $tables_principales;
	
	}


?>
