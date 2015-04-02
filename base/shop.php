<?php
/**
 * Plugin Shop Produit
 * (c) 2012 Rainer Müller
 * Licence GNU/GPL
 */

if (!defined('_ECRIRE_INC_VERSION')) return;

function shop_declarer_tables_principales($tables_principales){

        $tables_principales['spip_commandes']['field']['type_paiement']= "varchar(50) NOT NULL";
        
        include_spip('inc/config');
		
       //Charger la définition des champs extras
        $champs_extras=charger_fonction('shop_champs_extras','inc');
        $champs_extras=$champs_extras(); 
        
        include_spip('inc/shop');
        
        $tables_principales=array_merge($tables_principales,definitions_sql_champs_extras($champs_extras));
		

        return $tables_principales;

}
