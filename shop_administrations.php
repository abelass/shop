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


	$maj['create'] = array(array('maj_tables', array('spip_shop_commande','spip_shop_prix','spip_shop_tokens_retour','spip_geo_pays')));
    $maj['1.0.0'] = array(array('maj_tables', array('spip_shop_commande','spip_shop_prix','spip_tokens_retour','spip_geo_pays')));

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
	# quelques exemples
	# (que vous pouvez supprimer !)
	# sql_drop_table("spip_xx");
	# sql_drop_table("spip_xx_liens");

	sql_drop_table("spip_shop_commande");
    sql_drop_table("spip_shop_prix");
    sql_drop_table("spip_tokens_retour");
    sql_drop_table("spip_geo_pays");    
        

	effacer_meta($nom_meta_base_version);
}

?>
