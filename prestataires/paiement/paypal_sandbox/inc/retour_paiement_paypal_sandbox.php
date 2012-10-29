<?php

function inc_retour_paiement_paypal_sandbox_dist($token_panier){
	
	$contexte = Array();
	$contexte["token_panier"] = $token_panier;
	$contexte["payment_status"] = _request("payment_status");
	
	spip_log("NOTIFICATION  PAYPAL pour ".$contexte["token_panier"]." avec le statut ".$contexte["payment_status"],"echoppe");
	
	$les_entetes_get = print_r($_GET,true);
	$les_entetes_post = print_r($_POST,true);
	spip_log($les_entetes_get,"echoppe_paypal");
	spip_log($les_entetes_post,"echoppe_paypal");
	
	$token_client = sql_fetsel("token_client","spip_echoppe_paniers","token_panier = '".$token_panier."'");
	
	//$contexte["token_notification"] = _request("token_panier");
	
	/*
	 * 
	 * les etats de "payment_status"
	 * 
	 * */
	
	include_spip('inc/echoppe');
	include_spip('inc/echoppe_panier');
	switch ($contexte["payment_status"]) {
		
		/*Canceled-Reversal : une annulation de paiement a été annulée. Par exemple, vous avez obtenu gain de cause dans un litige avec le client et les fonds de la transaction qui avaient été remboursés vous sont retournés.*/
		case 'Canceled-Reversal' :
			spip_log("NOTIFICATION Annulation d'un panier","echoppe");
			sql_updateq("spip_echoppe_paniers",Array("statut"=>"annule"),Array("token_panier = '".$contexte["token_panier"]."'"));
			notifier_client($token_client['token_client'], _T("paypal_sandbox:votre_panier_a_ete_annuler_depuis_le_site_de_paypal"));
			echoppe_informer_panier($contexte["token_panier"],"Canceled-Reversal","Une annulation de paiement a été annulée. Par exemple, vous avez obtenu gain de cause dans un litige avec le client et les fonds de la transaction qui avaient été remboursés vous sont retournés.","paypal_sandbox",0,"warning");
		break;
		
		/* Completed : le paiement a été effectué et les fonds ont été ajoutés au solde de votre compte. */
		case 'Completed' :
			spip_log("NOTIFICATION Validation de paiement d'un panier","echoppe");
			sql_updateq("spip_echoppe_paniers",Array("statut"=>"paye"),Array("token_panier = '".$contexte["token_panier"]."'"));
			notifier_client($token_client['token_client'], _T("paypal_sandbox:votre_paiement_a_bien_ete_valide_depuis_le_site_de_paypal"));
			notifier_marchand(_T("paypal_sandbox:un_paiement_a_bien_ete_valide_depuis_le_site_de_paypal")."\n Voir le panier: ".generer_url_ecrire('echoppe_edit_panier','token_panier='.$contexte["token_panier"],"&"));
			echoppe_informer_panier($contexte["token_panier"],"Completed","Le paiement a été effectué et les fonds ont été ajoutés au solde de votre compte.","paypal_sandbox",0,"warning");
		break;
		
		/*Denied : vous avez rejeté le paiement. Ce cas se produit uniquement si le paiement était précédemment en attente pour des raisons possibles décrites dans l'élément PendingReason.*/
		case 'Denied' :
			spip_log("NOTIFICATION Refu de paiement d'un panier","echoppe");
			sql_updateq("spip_echoppe_paniers",Array("statut"=>"annule"),Array("token_panier = '".$contexte["token_panier"]."'"));
			notifier_client($token_client['token_client'], _T("paypal_sandbox:votre_paiement_a_ete_refuse_depuis_le_site_de_paypal_pour_les_raison_suivantes_"._request('PendingReason')));
			notifier_marchand(_T("paypal_sandbox:un_paiement_a_ete_refuse_depuis_le_site_de_paypal_pour_les_raison_suivantes_"._request('PendingReason'))."\n Voir le panier: ".generer_url_ecrire('echoppe_edit_panier','token_panier='.$contexte["token_panier"],"&"));
			echoppe_informer_panier($contexte["token_panier"],"Denied","vous avez rejeté le paiement. Ce cas se produit uniquement si le paiement était précédemment en attente.","paypal_sandbox",0,"warning");
		break;
		
		/* Expired : l'autorisation a expiré et ne peut être collectée. */
		case 'Expired' :
			spip_log("NOTIFICATION Delais de paiement d'un panier expire","echoppe");
			sql_updateq("spip_echoppe_paniers",Array("statut"=>"annule"),Array("token_panier = '".$contexte["token_panier"]."'"));
			notifier_client($token_client['token_client'], _T("paypal_sandbox:le_delai_de_paiement_a_expire_et_votre_panier_a_ete_annule"));
			notifier_marchand(_T("paypal_sandbox:le_delai_de_paiement_a_expire_et_votre_panier_a_ete_annule"));
			echoppe_informer_panier($contexte["token_panier"],"Expired","l'autorisation a expiré et ne peut être collectée.","paypal_sandbox",0,"warning");
		break;
		
		/* Failed : le paiement a échoué. Survient uniquement si le paiement a été effectué à partir du compte bancaire de l'utilisateur. */
		case 'Failed' :
			spip_log("NOTIFICATION Le paiement d'un panier a echoue","echoppe");
			sql_updateq("spip_echoppe_paniers",Array("statut"=>"annule"),Array("token_panier = '".$contexte["token_panier"]."'"));
			notifier_client($token_client['token_client'], _T("paypal_sandbox:le_paiement_a_echoue_et_votre_panier_a_ete_annule"));
			notifier_marchand(_T("paypal_sandbox:un_paiement_a_echoue_et_un_panier_a_ete_annule"))."\n Voir le panier: ".generer_url_ecrire('echoppe_edit_panier','token_panier='.$contexte["token_panier"],"&");
			echoppe_informer_panier($contexte["token_panier"],"Failed","Le paiement a échoué. Survient uniquement si le paiement a été effectué à partir du compte bancaire de l'utilisateur.","paypal_sandbox",0,"warning");
		break;
		
		/* In-Progress : la transaction est en cours d'autorisation et de collecte. */
		case 'In-Progress' :
			spip_log("NOTIFICATION Un paiement d'un panier est en cour","echoppe");
			sql_updateq("spip_echoppe_panier",Array("statut"=>"1_progress"),Array("token_panier = '".$contexte["token_panier"]."'"));
			notifier_client($token_client['token_client'], _T("paypal_sandbox:le_paiement_de_votre_panier_est_en_cour"));
			notifier_marchand(_T("paypal_sandbox:un_paiement_a_echoue_et_un_panier_a_ete_annule"));
			echoppe_informer_panier($contexte["token_panier"],"In-Progress","la transaction est en cours d'autorisation et de collecte.","paypal_sandbox",0,"warning");
		break;
		
		/* Partially-Refunded : la transaction a fait l'objet d'un remboursement partiel. */
		case 'Part-Ref' :
			spip_log("NOTIFICATION Un remboursement partiel d'un panier est en cour","echoppe");
			sql_updateq("spip_echoppe_panier",Array("statut"=>"1_Part-Ref"),Array("token_panier = '".$contexte["token_panier"]."'"));
			notifier_client($token_client['token_client'], _T("paypal_sandbox:le_paiement_de_votre_panier_est_rembourse_en_partie"));
			notifier_marchand(_T("paypal_sandbox:un_paiement_a_ete_rembourse_en_partie"));
			echoppe_informer_panier($contexte["token_panier"],"Partially-Refunded","la transaction a fait l'objet d'un remboursement partiel.","paypal_sandbox",0,"warning");
		break;
		
		/* Pending : le paiement est en attente. Reportez-vous à pending_ re pour plus d'informations. */
		case 'Pending' :
			spip_log("NOTIFICATION Paiement d'un panier en attente chez paypal. Cause: "._request('pending_'),"echoppe");
			sql_updateq("spip_echoppe_paniers",Array("statut"=>"attente"),Array("token_panier = '".$contexte["token_panier"]."'"));
			notifier_client($token_client['token_client'], _T("paypal_sandbox:votre_paiement_est_en_attente_de_validation_chez_paypal"));
			notifier_marchand(_T("paypal_sandbox:votre_paiement_est_en_attente_de_validation_chez_paypal"));
			echoppe_informer_panier($contexte["token_panier"],"Pending","le paiement est en attente. Cause : "._request("pending_"),"paypal_sandbox",0,"warning");
		break;
		
		/* Refunded : vous avez remboursé le paiement. */
		case 'Refunded' :
			spip_log("NOTIFICATION Un paiement d'un panier est rembourse","echoppe");
			sql_updateq("spip_echoppe_panier",Array("statut"=>"1_Refunded"),Array("token_panier = '".$contexte["token_panier"]."'"));
			notifier_client($token_client['token_client'], _T("paypal_sandbox:le_paiement_de_votre_panier_est_rembourse"));
			notifier_marchand(_T("paypal_sandbox:un_paiement_a_ete_rembourse"));
			echoppe_informer_panier($contexte["token_panier"],"Refunded","la transaction a fait l'objet d'un remboursement.","paypal_sandbox",0,"warning");
		
		
		break;
		
		/* Reversed : un paiement a été annulé en raison d'un rejet de débit ou d'un autre type d'annulation. Les fonds ont été retirés du solde de votre compte et renvoyés à l'acheteur. La raison du rejet est indiquée dans l'élément ReasonCode. */
		case 'Reversed' :
			spip_log("NOTIFICATION Le paiement d'un panier a echoue pour les raisons suivantes: "._request('ReasonCode'),"echoppe");
			sql_updateq("spip_echoppe_paniers",Array("statut"=>"annule"),Array("token_panier = '".$contexte["token_panier"]."'"));
			notifier_client($token_client, _T("paypal_sandbox:le_paiement_a_echoue_et_votre_panier_a_ete_annule_pour_les_raisons_suivantes_"._request('ReasonCode')));
			notifier_marchand(_T("paypal_sandbox:un_paiement_a_echoue_et_le_panier_a_ete_annule_pour_les_raisons_suivantes_"._request('ReasonCode'))."\n Voir le panier: ".generer_url_ecrire('echoppe_edit_panier','token_panier='.$contexte["token_panier"],"&"));
			echoppe_informer_panier($contexte["token_panier"],"Reversed","un paiement a été annulé en raison d'un rejet de débit ou d'un autre type d'annulation. Les fonds ont été retirés du solde de votre compte et renvoyés à l'acheteur. Raison : "._request('ReasonCode'),"paypal_sandbox",0,"warning");
		break;
		
		/* Processed : un paiement a été accepté. */
		case 'Processed' :
			spip_log("NOTIFICATION Paiement d'un panier accepte","echoppe");
			sql_updateq("spip_echoppe_panier",Array("statut"=>"paye"),Array("token_panier = '".$contexte["token_panier"]."'"));
			notifier_client($token_client['token_client'], _T("paypal_sandbox:votre_paiement_a_bien_ete_valide_depuis_le_site_de_paypal"));
			notifier_marchand(_T("paypal_sandbox:un_paiement_a_bien_ete_valide_depuis_le_site_de_paypal")."\n Voir le panier: ".generer_url_ecrire('echoppe_edit_panier','token_panier='.$contexte["token_panier"],"&"));
			echoppe_informer_panier($contexte["token_panier"],"Processed","un paiement a été accepté.","paypal_sandbox",0,"warning");
		break;
		
		/* Voided : cette autorisation a été annulée. */
		case 'Voided' :
			spip_log("NOTIFICATION Paiement d'un panier accepte","echoppe");
			sql_updateq("spip_echoppe_panier",Array("statut"=>"1_Voided"),Array("token_panier = '".$contexte["token_panier"]."'"));
			notifier_client($token_client['token_client'], _T("paypal_sandbox:votre_paiement_a_bien_ete_valide_depuis_le_site_de_paypal"));
			notifier_marchand(_T("paypal_sandbox:un_paiement_a_bien_ete_valide_depuis_le_site_de_paypal")."\n Voir le panier: ".generer_url_ecrire('echoppe_edit_panier','token_panier='.$contexte["token_panier"],"&"));
			echoppe_informer_panier($contexte["token_panier"],"Voided","cette autorisation a été annulée.","paypal_sandbox",0,"warning");
		break;
		
		default :
		
			spip_log("NOTIFICATION  PAYPAL pour ".$contexte["token_panier"]." sans statut !","echoppe");	
			echoppe_informer_panier($contexte["token_panier"],"NOTIFICATION Inconnue","NOTIFICATION  PAYPAL pour ".$contexte["token_panier"]." sans statut !","paypal_sandbox",0,"warning");
			notifier_marchand(_T("paypal_sandbox:notification_paypal_inconnue")."\n Voir le panier: ".generer_url_ecrire('echoppe_edit_panier','token_panier='.$contexte["token_panier"],"&"));
			
		break;
		
		
	}
	
	return $contexte;
	//On valide le lien via token
	
	//On verifie la réponse du prestataire
	
	//On modifie l'état du panier suivant la réponse du prestataire
	
	//On supprime toute les donnée de vaidation du panier
	
	//On redirigue vers formulaire_finalisation_paiement
	
}

?>
