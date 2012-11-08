<?php



if (!defined("_ECRIRE_INC_VERSION")) return;

function inc_retour_paiement2_paypal_dist($token=''){

	spip_log("NOTIFICATION paypal retour toke=$token","shop_paypal");
	

	$req = 'cmd=_notify-validate';

	foreach ($_POST as $key => $value) {
		$value = urlencode(stripslashes($value));
		$req .= "&$key=$value";
		}

	// post back to PayPal system to validate
	$header .= "POST /cgi-bin/webscr HTTP/1.0\r\n";
	$header .= "Content-Type: application/x-www-form-urlencoded\r\n";
	$header .= "Content-Length: " . strlen($req) . "\r\n\r\n";
	//$fp = fsockopen ('ssl://', 443, $errno, $errstr, 30);
	$url_paypal=api_paypal('url_paypal')
	
	$fp = fsockopen ('ssl://'.$url_paypal, 443, $errno, $errstr, 30);
	
	if (!$fp) {
		spip_log("NOTIFICATION paypa erreur verification serveur","shop");
		}
 	else {
		fputs ($fp, $header . $req);
		while (!feof($fp)) {
			$res = fgets ($fp, 1024);
			
			spip_log("NOTIFICATION paypal verify:$res","shop");
			
			if (strcmp ($res, "VERIFIED") == 0) {
				// check the payment_status is Completed
				spip_log("NOTIFICATION paypal verification serveur ok","shop");
				$contexte = Array();
				$contexte["token"] = $token;
				$contexte["payment_status"] = _request("payment_status");
				// assign posted variables to local variables
				$contexte["id_produit"] = _request('item_number1');
				$contexte["montant_total"] = _request('mc_gross');		
				$contexte["code_devise"] = _request('mc_currency');
				$contexte["id_paiement"] = _request('txn_id');
				$contexte["email_paypal"] = _request('receiver_email');
				
				include_spip('inc/mail');
				include_spip('shop_mes_fonctions');
				$cont=sql_fetsel('*','spip_shop_commandes',Array("token = '".$contexte["token"]."'"));
				$cont['titre_produit']=titre_article($cont['id_produit']);
				$cont['constellation']=titre_mot($cont['id_produit']);	
				
				spip_log("NOTIFICATION  PAYPAL pour ".$contexte["token"]." avec le statut_paiement ".$contexte["payment_status"],"shop");
				
				$les_entetes_get = print_r($_GET,true);
				$les_entetes_post = print_r($_POST,true);
				
				spip_log($les_entetes_get,"shop_paypal");
				spip_log($les_entetes_post,"shop_paypal");
				
				$token_client = sql_fetsel("token","spip_shop_commandes","token = '".$token."'");
						
				//$contexte["token_notification"] = _request("token");
				
				/*
				* 
				* les etats de "payment_status"
				* 
				* */
				
				//include_spip('inc/shop');
				
				//variables par défaut pour l'envoi des mails
					include_spip('inc/mail');
					include_spip('shop_mes_fonctions');
					$cont=sql_fetsel('*','spip_shop_commandes',Array("token = '".$contexte["token"]."'"));
					lang_select($cont['lang']);
					
					$cont['titre_produit']=titre_article($cont['id_produit']);
					$cont['constellation']=titre_mot($cont['id_option']);						
					$header ="Content-Type: text/html; charset=UTF-8\n"
						."Content-Transfer-Encoding: 8bit\n" 
						."MIME-Version: 1.0\n";	
					$email_client = $cont['email'];	
					$email_webmaster = $GLOBALS['meta']['email_webmaster'];			
					$from_client =$GLOBALS['meta']['nom_site'].' <'.$GLOBALS['meta']['email_webmaster'].'>';
					$from_webmaster =$from_client;					
					$sujet_client = _T('codeprom:confirmation_paiement');
					$sujet_webmaster = $sujet_client;					
					$head_client = recuperer_fond('inc/mail_html_head');
					$debut_body = '<body style=" font-family: Helvetica Neue, Helvetica, Arial, Verdana, sans-serif; color:#2C374C;  margin: 10px; padding: 20px; border: #ccc 1px solid; -moz-border-radius: 4px; -webkit-border-radius: 4px; border-radius: 4px;">';					
					$head_webmaster = $head_client;	
					$message_paiement_client = _T('shop:message_paiement_client');
					$message_paiement_webmaster = _T('shop:message_paiement_webmaster');
					$message_validation= _T('shop:message_validation_inconnue');
					$byby = '<p>'._T('shop:byby').'</p><p>'.$GLOBALS['meta']['nom_site'].'</p>';				
					$footer= '</body></html>';	

				switch ($contexte["payment_status"]) {

				// enregistrement dans la bd, les log et définition des variables mail selon les réponses de paypal
					
					/*Canceled-Reversal : une annulation de paiement a été annulée. Par exemple, vous avez obtenu gain de cause dans un litige avec le client et les fonds de la transaction qui avaient été remboursés vous sont retournés.*/
					case 'Canceled-Reversal' :
						spip_log("NOTIFICATION Annulation d'un panier","shop");
						
						sql_updateq("spip_shop_commandes",Array("statut_paiement"=>"annule"),Array("token = '".$contexte["token"]."'"));
						
						$message_validation= _T('shop:annule');			
						// 			notifier_client($token_client['token_client'], _T("paypal:votre_panier_a_ete_annuler_depuis_le_site_de_paypal"));
						// 			echoppe_informer_panier($contexte["token"],"Canceled-Reversal","Une annulation de paiement a été annulée. Par exemple, vous avez obtenu gain de cause dans un litige avec le client et les fonds de la transaction qui avaient été remboursés vous sont retournés.","paypal",0,"warning");
					break;
					
					/* Completed : le paiement a été effectué et les fonds ont été ajoutés au solde de votre compte. */
					case 'Completed' :
						$erreur=array();
						// check that txn_id has not been previously processed
						if(sql_fetsel('id_paiement','spip_shop_tokens_retour',Array("id_paiement = '".$contexte["id_paiement"]."'"))) $erreur['id_paiment']=1;

						// check that receiver_email is your Primary PayPal email
						if($contexte["email_paypal"] != lire_config('shop/email_paypal')) $erreur['email_paypal']=1;

						// check that payment_amount/payment_currency are correct
						$verifier=sql_fetsel('code_devise,prix,frais_livraison','spip_shop_commandes',Array("token = '".$contexte["token"]."'")); 
						
						if($verifier['code_devise']!=$contexte["code_devise"])$erreur['code_devise']=1;
						
						$montant_total =$verifier['prix']+$verifier['frais_livraison'];
						
						if($montant_total!=$contexte["montant_total"])$erreur['montant_total']=1;	
											
						if(count($erreur)==0){					
							// process payment
							spip_log("NOTIFICATION Validation de paiement d'un panier","shop");
							sql_updateq("spip_shop_commandes",Array("statut_paiement"=>"paye"),Array("token = '".$contexte["token"]."'"));
							
							sql_updateq("spip_shop_tokens_retour",Array('id_paiement'=>$contexte["id_paiement"]),Array("token_panier = '".$contexte["token"]."'"));	
													
							$message_paiement_client= _T('shop:message_validation_accepte_client');
							$message_paiement_webmaster= _T('shop:message_validation_accepte_webmaster');
							$message_validation= '';						
	
													
							// 			notifier_client($token_client['token_client'], _T("paypal:votre_paiement_a_bien_ete_valide_depuis_le_site_de_paypal"));
							// 			notifier_marchand(_T("paypal:un_paiement_a_bien_ete_valide_depuis_le_site_de_paypal")."\n Voir le panier: ".generer_url_ecrire('echoppe_edit_panier','token='.$contexte["token"],"&"));
							// 			echoppe_informer_panier($contexte["token"],"Completed","Le paiement a été effectué et les fonds ont été ajoutés au solde de votre compte.","paypal",0,"warning");
							// 			appliquer_stock($contexte["token"]);
							}
						else{
							foreach ($erreur as $key => $value) {
								$details_erreur .= ' - '.$key;
								}
							spip_log("ALERTE Validation du paiement echou&eacute; erreurs:".$details_erreur,"shop");
							$message_validation= $details_erreur;
							}
					break;
					
					/*Denied : vous avez rejeté le paiement. Ce cas se produit uniquement si le paiement était précédemment en attente pour des raisons possibles décrites dans l'élément PendingReason.*/
					case 'Denied' :
						spip_log("NOTIFICATION Refu de paiement d'un panier","shop");
						sql_updateq("spip_shop_commandes",Array("statut_paiement"=>"annule"),Array("token = '".$contexte["token"]."'"));
						$message_validation= _T('shop:rejete');	
						/*notifier_client($token_client['token_client'], _T("paypal:votre_paiement_a_ete_refuse_depuis_le_site_de_paypal_pour_les_raison_suivantes_"._request('PendingReason')));
						notifier_marchand(_T("paypal:un_paiement_a_ete_refuse_depuis_le_site_de_paypal_pour_les_raison_suivantes_"._request('PendingReason'))."\n Voir le panier: ".generer_url_ecrire('echoppe_edit_panier','token='.$contexte["token"],"&"));
						echoppe_informer_panier($contexte["token"],"Denied","vous avez rejeté le paiement. Ce cas se produit uniquement si le paiement était précédemment en attente.","paypal",0,"warning");*/
					break;
					
					/* Expired : l'autorisation a expiré et ne peut être collectée. */
					case 'Expired' :
						spip_log("NOTIFICATION Delais de paiement d'un panier expire","shop");
						ql_updateq("spip_shop_commandes",Array("statut_paiement"=>"annule"),Array("token = '".$contexte["token"]."'"));
						$message_validation= _T('shop:expire');	
						/*snotifier_client($token_client['token_client'], _T("paypal:le_delai_de_paiement_a_expire_et_votre_panier_a_ete_annule"));
						notifier_marchand(_T("paypal:le_delai_de_paiement_a_expire_et_votre_panier_a_ete_annule"));
						echoppe_informer_panier($contexte["token"],"Expired","l'autorisation a expiré et ne peut être collectée.","paypal",0,"warning");*/
					break;
					
					/* Failed : le paiement a échoué. Survient uniquement si le paiement a été effectué à partir du compte bancaire de l'utilisateur. */
					case 'Failed' :
						spip_log("NOTIFICATION Le paiement d'un panier a echoue","shop");
						sql_updateq("spip_shop_commandes",Array("statut_paiement"=>"annule"),Array("token = '".$contexte["token"]."'"));
						$message_validation= _T('shop:echoue');
						/*notifier_client($token_client['token_client'], _T("paypal:le_paiement_a_echoue_et_votre_panier_a_ete_annule"));
						notifier_marchand(_T("paypal:un_paiement_a_echoue_et_un_panier_a_ete_annule"))."\n Voir le panier: ".generer_url_ecrire('echoppe_edit_panier','token='.$contexte["token"],"&");
						echoppe_informer_panier($contexte["token"],"Failed","Le paiement a échoué. Survient uniquement si le paiement a été effectué à partir du compte bancaire de l'utilisateur.","paypal",0,"warning");*/
					break;
					
					/* In-Progress : la transaction est en cours d'autorisation et de collecte. */
					case 'In-Progress' :
						spip_log("NOTIFICATION Un paiement d'un panier est en cour","shop");
						sql_updateq("spip_shop_commandes",Array("statut_paiement"=>"pendant"),Array("token = '".$contexte["token"]."'"));
						$message_validation= _T('shop:pendant');
						/*notifier_client($token_client['token_client'], _T("paypal:le_paiement_de_votre_panier_est_en_cour"));
						notifier_marchand(_T("paypal:un_paiement_a_echoue_et_un_panier_a_ete_annule"));
						echoppe_informer_panier($contexte["token"],"In-Progress","la transaction est en cours d'autorisation et de collecte.","paypal",0,"warning");*/
					break;
					
					/* Partially-Refunded : la transaction a fait l'objet d'un remboursement partiel. */
					case 'Part-Ref' :
						spip_log("NOTIFICATION Un remboursement partiel d'un panier est en cour","shop");
						sql_updateq("spip_shop_commandes",Array("statut_paiement"=>"rembourse_partiellement"),Array("token = '".$contexte["token"]."'"));
						$message_validation= _T('shop:rembourse_partiellement');
						/*notifier_client($token_client['token_client'], _T("paypal:le_paiement_de_votre_panier_est_rembourse_en_partie"));
						notifier_marchand(_T("paypal:un_paiement_a_ete_rembourse_en_partie"));
						echoppe_informer_panier($contexte["token"],"Partially-Refunded","la transaction a fait l'objet d'un remboursement partiel.","paypal",0,"warning");*/
					break;
					
					/* Pending : le paiement est en attente. Reportez-vous à pending_ re pour plus d'informations. */
					case 'Pending' :
						spip_log("NOTIFICATION Paiement d'un panier en attente chez paypal. Cause: "._request('pending_reason'),"shop");
						sql_updateq("spip_shop_commandes",Array("statut_paiement"=>"attente"),Array("token = '".$contexte["token"]."'"));
						$message_validation= _T('shop:attente');
						/*notifier_client($token_client['token_client'], _T("paypal:votre_paiement_est_en_attente_de_validation_chez_paypal"));
						notifier_marchand(_T("paypal:votre_paiement_est_en_attente_de_validation_chez_paypal"));
						echoppe_informer_panier($contexte["token"],"Pending","le paiement est en attente. Cause : "._request("pending_"),"paypal",0,"warning");*/
					break;
					
					/* Refunded : vous avez remboursé le paiement. */
					case 'Refunded' :
						spip_log("NOTIFICATION Un paiement d'un panier est rembourse","shop");
						sql_updateq("spip_shop_commandes",Array("statut_paiement"=>"rembourse"),Array("token = '".$contexte["token"]."'"));
						$message_validation= _T('shop:rembourse');
						/*notifier_client($token_client['token_client'], _T("paypal:le_paiement_de_votre_panier_est_rembourse"));
						notifier_marchand(_T("paypal:un_paiement_a_ete_rembourse"));
						echoppe_informer_panier($contexte["token"],"Refunded","la transaction a fait l'objet d'un remboursement.","paypal",0,"warning");*/
					
					
					break;
					
					/* Reversed : un paiement a été annulé en raison d'un rejet de débit ou d'un autre type d'annulation. Les fonds ont été retirés du solde de votre compte et renvoyés à l'acheteur. La raison du rejet est indiquée dans l'élément ReasonCode. */
					case 'Reversed' :
						spip_log("NOTIFICATION Le paiement d'un panier a echoue pour les raisons suivantes: "._request('ReasonCode'),"shop");
						sql_updateq("spip_shop_commandes",Array("statut_paiement"=>"annule"),Array("token = '".$contexte["token"]."'"));
						$message_validation= _T('shop:rejete');
						/*notifier_client($token_client, _T("paypal:le_paiement_a_echoue_et_votre_panier_a_ete_annule_pour_les_raisons_suivantes_"._request('ReasonCode')));
						notifier_marchand(_T("paypal:un_paiement_a_echoue_et_le_panier_a_ete_annule_pour_les_raisons_suivantes_"._request('ReasonCode'))."\n Voir le panier: ".generer_url_ecrire('echoppe_edit_panier','token='.$contexte["token"],"&"));
						echoppe_informer_panier($contexte["token"],"Reversed","un paiement a été annulé en raison d'un rejet de débit ou d'un autre type d'annulation. Les fonds ont été retirés du solde de votre compte et renvoyés à l'acheteur. Raison : "._request('ReasonCode'),"paypal",0,"warning");*/
					break;
					
					/* Processed : un paiement a été accepté. */
					case 'Processed' :
					$erreur=array();
						// check that txn_id has not been previously processed
						if(sql_fetsel('id_paiement','spip_shop_tokens_retour',Array("id_paiement = '".$contexte["id_paiement"]."'"))) $erreur['id_paiment']=1;

						// check that receiver_email is your Primary PayPal email
						if($contexte["email_paypal"] != lire_config('shop/email_paypal')) $erreur['email_paypal']=1;

						// check that payment_amount/payment_currency are correct
						$verifier=sql_fetsel('code_devise,prix,frais_livraison','spip_shop_commandes',Array("token = '".$contexte["token"]."'")); 
						
						if($verifier['code_devise']!=$contexte["code_devise"])$erreur['code_devise']=1;
						
						$montant_total =$verifier['prix']+$verifier['frais_livraison'];
						
						if($montant_total!=$contexte["montant_total"])$erreur['montant_total']=1;	
											
						if(count($erreur)==0){	
							spip_log("NOTIFICATION Paiement d'un panier accepte","shop");
							sql_updateq("spip_shop_commandes",Array("statut_paiement"=>"paye"),Array("token = '".$contexte["token"]."'"));
							$message_paiement_client= _T('shop:message_validation_accepte_client');
							$message_paiement_webmaster= _T('shop:message_validation_accepte_webmaster');
							$message_validation= '';
							
					$message_client.=recuperer_fond('inc/mail_html_body',$cont);
					$message_webmaster.=$message_client;
					
					$message_client=$head_client.$debut_body.$message_client.$message_paiement_client.$message_validation.$footer;
					$message_webmaster=$head_webmaster.$debut_body.$message_webmaster.$message_paiement_webmaster.$message_validation.$footer;					
					
							
							
	
						if (envoyer_mail($email_client,$sujet_client,$message_client,$from_client,$header)){
						spip_log("Email confirmation paypal client : $email_client\n$sujet_client\n$message_client\n",'shop_mail');
							}
						else	{
						spip_log("Email confirmation échec paypal client :$email_client\n$sujet_client\n$message_client\n",'shop_mail');
							}	
							/*notifier_client($token_client['token_client'], _T("paypal:votre_paiement_a_bien_ete_valide_depuis_le_site_de_paypal"));
							notifier_marchand(_T("paypal:un_paiement_a_bien_ete_valide_depuis_le_site_de_paypal")."\n Voir le panier: ".generer_url_ecrire('echoppe_edit_panier','token='.$contexte["token"],"&"));
							echoppe_informer_panier($contexte["token"],"Processed","un paiement a été accepté.","paypal",0,"warning");
							appliquer_stock($contexte["token"]);*/
							}
					break;
					
					/* Voided : cette autorisation a été annulée. */
					case 'Voided' :
						spip_log("NOTIFICATION Paiement d'un panier accepte","shop");
						sql_updateq("spip_shop_commandes",Array("statut_paiement"=>"1_Voided"),Array("token = '".$contexte["token"]."'"));
						/*notifier_client($token_client['token_client'], _T("paypal:votre_paiement_a_bien_ete_valide_depuis_le_site_de_paypal"));
						notifier_marchand(_T("paypal:un_paiement_a_bien_ete_valide_depuis_le_site_de_paypal")."\n Voir le panier: ".generer_url_ecrire('echoppe_edit_panier','token='.$contexte["token"],"&"));
						echoppe_informer_panier($contexte["token"],"Voided","cette autorisation a été annulée.","paypal",0,"warning");*/
					break;
					
					default :
					
						spip_log("NOTIFICATION  PAYPAL pour ".$contexte["token"]." sans statut_paiement !","shop");	
						/*echoppe_informer_panier($contexte["token"],"NOTIFICATION Inconnue","NOTIFICATION  PAYPAL pour ".$contexte["token"]." sans statut_paiement !","paypal",0,"warning");
						notifier_marchand(_T("paypal:notification_paypal_inconnue")."\n Voir le panier: ".generer_url_ecrire('echoppe_edit_panier','token='.$contexte["token"],"&"));*/
	
				}
				

					

			// composition du message selon les variables recoltés
					
			$message_client.=recuperer_fond('inc/mail_html_body',$cont);
				$message_webmaster.=$message_client;
					
			$message_client=$head_client.$debut_body.$message_client.$message_paiement_client.$message_validation.$byby.$footer;
			$message_webmaster=$head_webmaster.$debut_body.$message_webmaster.$message_paiement_webmaster.$message_validation.$byby.$footer;					

	
			if (envoyer_mail($email_client,$sujet_client,$message_client,$from_client,$header)){
				spip_log("Email confirmation paypal client : $email_client\n$sujet_client\n$message_client\n",'shop_mail');
				}
			else	{
				spip_log("Email confirmation échec paypal client :$email_client\n$sujet_client\n$message_client\n",'shop_mail');
				}
															
						

			if (envoyer_mail($email_webmaster,$sujet_webmaster,$message_webmaster,$from_webmaster,$header)){
				spip_log("Email confirmation paypal webmaster : $email_webmaster\n$sujet_webmaster\n$message_webmaster\n",'shop_mail');
				}
			else	{
				spip_log("Email confirmation échec paypal webmaster :$email_webmaster\n$sujet_webmaster\n$message_webmaster\n",'shop_mail');
				}
				
				return $contexte;
			}
		else if (strcmp ($res, "INVALID") == 0) {
		spip_log("NOTIFICATION  PAYPAL not valid pour","shop");	
			}
		}
	fclose ($fp);
	}
	

	//On valide le lien via token
	
	//On verifie la réponse du prestataire
	
	//On modifie l'état du panier suivant la réponse du prestataire
	
	//On supprime toute les donnée de vaidation du panier
	
	//On redirigue vers formulaire_finalisation_paiement
	
}

?>
