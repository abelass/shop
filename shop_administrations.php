<?php
/**
 * Plugin Signaler des abus
 * (c) 2012 My Chacra
 * Licence GNU/GPL
 */

if (!defined('_ECRIRE_INC_VERSION')) return;
include_spip('inc/cextras');
include_spip('base/wiki_cso');

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
	$maj['create'] = array(array('maj_tables', array('spip_commandes')));
	$maj['1.0.5'] = array(array('maj_tables', array('spip_commandes')));   
	$maj['1.1.0'] = array(array('maj_tables', array('spip_commandes')));        
                           
	include_spip('base/upgrade');
	maj_plugin($nom_meta_base_version, $version_cible, $maj);
}

function shop_vider_tables($nom_meta_base_version) {

    effacer_meta($nom_meta_base_version);
}

?>
