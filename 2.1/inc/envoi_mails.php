<?php

function inc_envoi_mails_dist(){
	$message=recuperer_fond('inc/mail_html_head').recuperer_fond('inc/mail_html_body',$contexte).'</html>';
	$header ="Content-Type: text/html; charset=UTF-8\n"
		."Content-Transfer-Encoding: 8bit\n" 
		."MIME-Version: 1.0\n";	
	$from =.' <'.$GLOBALS['meta']['email_webmaster'].'>';
	$sujet = _T('devis');		
	$email = 'Publigift <'.$GLOBALS['meta']['email_webmaster'].'>';
	
		if (envoyer_mail($email,$sujet,$message,$from,$header)){
		spip_log("Email confirmation $email\n$sujet\n$message\n",'publigift_mail');
		}
		else	{
		spip_log("Email confirmation échec $email\n$sujet\n$message\n",'publigift_mail');
		}	
	}	
?>
