<?php
/**
 * Plugin Déclinaisons Produit
 * (c) 2012 Rainer Müller
 * Licence GNU/GPL
 */

if (!defined('_ECRIRE_INC_VERSION')) return;

function shop_declarer_tables_principales($tables_principales){

        $tables_principales['spip_commandes']['field']['type_paiement']= "varchar(50) NOT NULL";
        $tables_principales['spip_commandes']['field']['commentaire']= "text NOT NULL";
        
        return $tables_principales;

}

?>