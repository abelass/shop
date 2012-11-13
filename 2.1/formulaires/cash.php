<?php

function formulaires_cash_charger_dist(){
	
	$valeurs = array(
		'id_commande'=>_request('id_commande'),		
		'_hidden'=>'<input type="hidden" name="id_commande" value="'._request('id_commande').'"/>',	
		'editable'=>true						
		);
	
return $valeurs;
}

function formulaires_cash_traiter_dist(){ 

	
	
	$retour = array();
	
	sql_updateq('spip_shop_commande',array('type_paiement'=>'cash'),'id_commande='._request('id_commande'));
	
	$cont=sql_fetsel('*','spip_shop_commande','id_commande='._request('id_commande'));
	
	
	include_spip('inc/config');
	
	$code_devise=$cont['code_devise'];
	
	$frais_livraison=$cont['frais_livraison'];
	
	$montant_ht=$cont['montant'];
	
	$taxes=number_format($montant_ht/100*lire_config('shop/taxes'),2);
	
	
	$montant=$montant_ht+$taxes+$frais_livraison;
	
	$cont['taxes']=$taxes;

	$retour['message_ok']=_T('shop:cash_explication',array('montant'=>$montant.' '.$code_devise)).'</div>';
	$retour['editable']=false;
	
	include_spip('inc/mail');
	include_spip('shop_mes_fonctions');
	//$cont=sql_fetsel('*','spip_shop_commande',Array("token = '".$contexte["token"]."'"));
	lang_select(_request('lang'));
						
	$header ="Content-Type: text/html; charset=UTF-8\n"
		."Content-Transfer-Encoding: 8bit\n" 
		."MIME-Version: 1.0\n";	
	$email_client = $cont['email'];	
	$email_webmaster = $GLOBALS['meta']['email_webmaster'];			
	$from_client =$GLOBALS['meta']['nom_site'].' <'.$GLOBALS['meta']['email_webmaster'].'>';
	$from_webmaster =$from_client;					
	$sujet_client = _T('shop:confirmation_paiement');
	$sujet_webmaster = $sujet_client;					
	$head_client = recuperer_fond('inc/mail_html_head',$cont);
	$debut_body = '<body style=" font-family: Helvetica Neue, Helvetica, Arial, Verdana, sans-serif; color:#2C374C;  margin: 10px; padding: 20px; border: #ccc 1px solid; -moz-border-radius: 4px; -webkit-border-radius: 4px; border-radius: 4px;">';					
	$head_webmaster = $head_client;	
	$message_paiement_client = _T('shop:message_paiement_client');
	$message_paiement_webmaster = _T('shop:message_paiement_webmaster');
	$message_validation= _T('shop:message_validation_cash');
	$byby = '<p>'._T('shop:byby').'</p><p>'.$GLOBALS['meta']['nom_site'].'</p>';				
	$footer= '</body></html>';	
	$message_client.=recuperer_fond('inc/mail_html_body',$cont);
	$message_webmaster.=$message_client;
	
	$message_client=$head_client.$debut_body.$message_client.$message.$byby.$footer;
	$message_webmaster=$head_webmaster.$debut_body.$bonjour.$message_webmaster.$message_paiement_webmaster.$message_validation.$byby.$footer;					


	if (envoyer_mail($email_client,$sujet_client,$message_client,$from_client,$header)){
		spip_log("Email confirmation cash client : $email_client\n$sujet_client\n$message_client\n",'shop_mail');
		}
	else	{
		spip_log("Email confirmation cash paypal client :$email_client\n$sujet_client\n$message_client\n",'shop_mail');
		}
													
				

	if (envoyer_mail($email_webmaster,$sujet_webmaster,$message_webmaster,$from_webmaster,$header)){
		spip_log("Email confirmation cash webmaster : $email_webmaster\n$sujet_webmaster\n$message_webmaster\n",'shop_mail');
		}
	else	{
		spip_log("Email confirmation échec cash webmaster :$email_webmaster\n$sujet_webmaster\n$message_webmaster\n",'shop_mail');
		}

    return $retour;
}


?>
