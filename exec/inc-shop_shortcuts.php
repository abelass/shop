<?php
	echo debut_cadre_relief('',true,'', _T('spip:titre_cadre_raccourcis'));
	
	echo '<ol class="shortcut">';
	
	if(_request('voir')){
	echo	'<li><a href="?exec=shop" class="cellule-h"><img src="'.chemin('img/logo_shop_24.png').'" alt="'._T('spip:icone_configuration_site').'" align="absmiddle" />&nbsp;'._T('shop:retour_panel').' '._T('shop:shop').'</a>';
	echo '</li>';
	}

	echo	'<li><a href="?exec=cfg&cfg=shop" class="cellule-h"><img src="'.chemin('cfg-22.png').'" alt="'._T('spip:icone_configuration_site').'" align="absmiddle" />&nbsp;'._T('spip:icone_configuration_site').'</a></li>';
	

	/*if(_request('exec')=='shop'){
		$codeprom='<div><a href="?exec=codes_promo"><img src="'.chemin('img/logo_promo.png').'" alt="'._T('spip:icone_configuration_site').'" align="absmiddle" />&nbsp;'._T('codeprom:gestion_promotions').'</a></div>';
		}
	else{
		$codeprom .=	'<div><a href="?exec=shop"><img src="'.chemin('img/logo_shop_22.png').'" alt="'._T('spip:icone_configuration_site').'" align="absmiddle" />&nbsp;'._T('shop:shop').'</a></div>';
		if(!_request('editer_promo') )
			$codeprom.=	'<div><a href="?exec=codes_promo&editer_promo=new"><img src="'.chemin('img/logo_promo.png').'" alt="'._T('spip:icone_configuration_site').'" align="absmiddle" />&nbsp;'._T('codeprom:editer_code').'</a></div>';
		else		$codeprom.='<div><a href="?exec=codes_promo"><img src="'.chemin('img/logo_promo.png').'" alt="'._T('spip:icone_configuration_site').'" align="absmiddle" />&nbsp;'._T('codeprom:gestion_promotions').'</a></div>';		
		}
	echo $codeprom;*/
	echo pipeline('shop_affiche_gauche_shortcuts', array('args'=>array('exec'=>_request('exec')),'data'=>''));
			
	echo '</ol>';
	
	echo fin_cadre_relief(true);
?>