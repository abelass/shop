<?php
if (!defined("_ECRIRE_INC_VERSION")) return;

function action_eliminer_prix_dist(){
	include_spip('inc/minipres');
	$securiser_action = charger_fonction('securiser_action', 'inc');
	$arg = $securiser_action();

    	$where = array('id_prix='._request('arg'));
    	sql_delete('spip_shop_prix',$where);

}
?>