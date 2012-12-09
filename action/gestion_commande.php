<?php
if (!defined("_ECRIRE_INC_VERSION")) return;

function action_gestion_commande_dist(){
	include_spip('inc/minipres');
	$securiser_action = charger_fonction('securiser_action', 'inc');
	$arg = $securiser_action();
	switch($arg){
		case 'eliminer':
		$where = array('id_commande='._request('id_commande'));
		sql_updateq('spip_shop_commande',array('statut'=>'supprime'),$where);
		break;
		case 'envoye':
		$where = array('id_commande='._request('id_commande'));
		sql_updateq('spip_shop_commande',array('traitement'=>$arg),$where);
		break;		
    	}

}
?>