<?php

if (!defined("_ECRIRE_INC_VERSION")) return;

function action_notification_paypal_dist(){
	
	$contexte = Array();
	$contexte["payment_status"] = _request("payment_status");
	//$contexte["token_notification"] = _request("token_panier");
	$contexte["token_panier"] = _request("token_panier");
	
	/*
	 * 
	 * les etats de "payment_status"
	 * 
	 * Canceled-Reversal : une annulation de paiement a été annulée. Par exemple, vous avez obtenu gain de cause dans un litige avec le client et les fonds de la transaction qui avaient été remboursés vous sont retournés.
	 * Completed : le paiement a été effectué et les fonds ont été ajoutés au solde de votre compte.
	 * Denied : vous avez rejeté le paiement. Ce cas se produit uniquement si le paiement était précédemment en attente pour des raisons possibles décrites dans l'élément PendingReason.
	 * Expired : l'autorisation a expiré et ne peut être collectée.
	 * Failed : le paiement a échoué. Survient uniquement si le paiement a été effectué à partir du compte bancaire de l'utilisateur.
	 * In-Progress : la transaction est en cours d'autorisation et de collecte.
	 * Partially-Refunded : la transaction a fait l'objet d'un remboursement partiel.
	 * Pending : le paiement est en attente. Reportez-vous à pending_ re pour plus d'informations.
	 * Refunded : vous avez remboursé le paiement.
	 * Reversed : un paiement a été annulé en raison d'un rejet de débit ou d'un autre type d'annulation. Les fonds ont été retirés du solde de votre compte et renvoyés à l'acheteur. La raison du rejet est indiquée dans l'élément ReasonCode.
	 * Processed : un paiement a été accepté.
	 * Voided : cette autorisation a été annulée.
	 * 
	 * */
	include_spip('inc/echoppe');
	switch ($contexte["payment_status"]) {
		
		
		case 'Canceled-Reversal' :
			spip_log("NOTIFICATION Annulation d'un panier","echoppe");
			sql_updateq("spip_echoppe_panier",Array("statut"=>"annule"),Array("token_panier = '".$contexte["token_panier"]."'"));
			notifier_client($id_client, _T("votre_panier_a_ete_annuler_depuis_le_site_de_paypal"));
		break;
		
		
		case 'Completed' :
			spip_log("NOTIFICATION Validation de paiement d'un panier","echoppe");
			sql_updateq("spip_echoppe_panier",Array("statut"=>"paye"),Array("token_panier = '".$contexte["token_panier"]."'"));
			notifier_client($id_client, _T("votre_paiement_a_bien_ete_valide_depuis_le_site_de_paypal"));
		break;
		
		
		case 'Denied' :
			spip_log("NOTIFICATION Refu de paiement d'un panier","echoppe");
			sql_updateq("spip_echoppe_panier",Array("statut"=>"annule"),Array("token_panier = '".$contexte["token_panier"]."'"));
			notifier_client($id_client, _T("votre_paiement_a_ete_refuse_depuis_le_site_de_paypal_pour_les_raison_suivantes_"._request('PendingReason')));
			notifier_marchand($id_client, _T("un_paiement_a_ete_refuse_depuis_le_site_de_paypal_pour_les_raison_suivantes_"._request('PendingReason')));
		break;
		
		
		case 'Expired' :
			spip_log("NOTIFICATION Delais de paiement d'un panier expire","echoppe");
			sql_updateq("spip_echoppe_panier",Array("statut"=>"annule"),Array("token_panier = '".$contexte["token_panier"]."'"));
			notifier_client($id_client, _T("le_delai_de_paiement_a_expire_et_votre_panier_a_ete_annule"));
		
		break;
		
		
		case 'Failed' :
			spip_log("NOTIFICATION Le paiement d'un panier a echoue","echoppe");
			sql_updateq("spip_echoppe_panier",Array("statut"=>"annule"),Array("token_panier = '".$contexte["token_panier"]."'"));
			notifier_client($id_client, _T("le_paiement_a_echoue_et_votre_panier_a_ete_annule"));
			notifier_marchand($id_client, _T("un_paiement_a_echoue_et_un_panier_a_ete_annule"));
		
		break;
		
		
		case 'In-Progress' :
			spip_log("NOTIFICATION Un paiement d'un panier est en cour","echoppe");
			/*sql_updateq("spip_echoppe_panier",Array("statut"=>"valide"),Array("token_panier = '".$contexte["token_panier"]."'"));
			notifier_client($id_client, _T("le_paiement_de_votre_panier_est_en_cour"));
			notifier_marchand($id_client, _T("un_paiement_a_echoue_et_un_panier_a_ete_annule"));*/
		
		
		break;
		
		
		case 'Partially-Refunded' :
			
		break;
		
		
		case 'Pending' :
			spip_log("NOTIFICATION Validation de paiement d'un panier","echoppe");
			sql_updateq("spip_echoppe_panier",Array("statut"=>"paye"),Array("token_panier = '".$contexte["token_panier"]."'"));
			notifier_client($id_client, _T("votre_paiement_a_bien_ete_valide_depuis_le_site_de_paypal"));
		
		break;
		
		
		case 'Refunded' :
		
		break;
		
		
		case 'Reversed' :
			spip_log("NOTIFICATION Le paiement d'un panier a echoue pour les raisons suivantes: "._request('ReasonCode'),"echoppe");
			sql_updateq("spip_echoppe_panier",Array("statut"=>"annule"),Array("token_panier = '".$contexte["token_panier"]."'"));
			notifier_client($id_client, _T("le_paiement_a_echoue_et_votre_panier_a_ete_annule_pour_les_raisons_suivantes_"._request('ReasonCode')));
			notifier_marchand($id_client, _T("un_paiement_a_echoue_et_le_panier_a_ete_annule_pour_les_raisons_suivantes_"._request('ReasonCode')));
		
		break;
		
		
		case 'Processed' :
			spip_log("NOTIFICATION Paiement d'un panier accepte","echoppe");
			/*sql_updateq("spip_echoppe_panier",Array("statut"=>"paye"),Array("token_panier = '".$contexte["token_panier"]."'"));
			notifier_client($id_client, _T("votre_paiement_a_bien_ete_valide_depuis_le_site_de_paypal"));*/
		
		break;
		
		
		case 'Voided' :
		
		break;
		
		
	}
	
	spip_log("NOTIFICATION  PAYPAL pour ".$contexte["token_panier"]." avec le statut ".$contexte["payment_status"],"echoppe");	
	
}

?>
