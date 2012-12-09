<?php
     echo debut_boite_info(true);
     
     echo '<div class="infos">';
     echo '<h2 style="text-align:center;">'._T('shop:shop').'</h2>';			
	 echo '<p style="text-align:center;"><img src="'.find_in_path('/img/logo_shop.png').'" title="Boutique" /></p>';
	     if(_request('voir')){
	     echo '<div class="numero">'._T('shop:commande');
		     echo '<p>'._request('id_commande').'</p></div>';
	     
	     }
	     
	     echo	'</div>';
	echo fin_boite_info(true);
?>