<?php
/**
 * Plugin Signaler des abus
 * (c) 2012 My Chacra
 * Licence GNU/GPL
 */

if (!defined('_ECRIRE_INC_VERSION')) return;


/**
 * Fonction d'installation du plugin et de mise à jour.
 * Vous pouvez :
 * - créer la structure SQL,
 * - insérer du pre-contenu,
 * - installer des valeurs de configuration,
 * - mettre à jour la structure SQL 
**/
function shop_upgrade($nom_meta_base_version, $version_cible) {
	$maj = array();


	$maj['create'] = array(array('maj_tables', array('spip_commandes','spip_prix','spip_shop_tokens_retour')));
    $maj['1.1.0']  = array(
        array('sql_alter','TABLE spip_shop_commande RENAME TO spip_commandes'),    
        array('sql_alter','TABLE spip_shop_commandes RENAME TO spip_commandes'),
        array('sql_alter','TABLE spip_shop_prix RENAME TO spip_prix')
        );
	include_spip('base/upgrade');
	maj_plugin($nom_meta_base_version, $version_cible, $maj);
}


/**
 * Fonction de désinstallation du plugin.
 * Vous devez :
 * - nettoyer toutes les données ajoutées par le plugin et son utilisation
 * - supprimer les tables et les champs créés par le plugin. 
**/
function shop_vider_tables($nom_meta_base_version) {

	sql_drop_table("spip_commandes");
    sql_drop_table("spip_prix");
    sql_drop_table("spip_tokens_retour");
        

	effacer_meta($nom_meta_base_version);
}

?>
