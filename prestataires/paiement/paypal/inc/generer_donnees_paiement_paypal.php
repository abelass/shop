<?php

function inc_generer_donnees_paiement_paypal_dist($contexte){
	
	include_spip('echoppe_fonctions');
	include_spip('inc/echoppe');
	include_spip('inc/filtres');
	include_spip('inc/echoppe_paiement');
	
	$panier = sql_select("*",Array("spip_echoppe_paniers"),Array("token_panier ='".$contexte["token_panier"]."'","statut = 'valide'"));
	if (sql_count($panier)){
		
		$contexte["hidden_prestataire"][] = Array("cmd"=>"_cart");
		/*$contexte["hidden_prestataire"][] = Array("notify_url"=>generer_url_action("notification_paypal","token_panier=".$contexte["token_panier"],"&"));*/
		$contexte["hidden_prestataire"][] = Array("amount"=>calculer_prix_panier($contexte["token_panier"]));
		
		$logo_site_spip = charger_fonction("chercher_logo",'inc');
		
		$logo_site = $logo_site_spip("0","site","on");		
		$logo_site = url_absolue($logo_site[0]);
		
		$contexte["hidden_prestataire"][] = Array("image_url"=>$logo_site);
		$contexte["hidden_prestataire"][] = Array("upload"=>"1");
		$contexte["hidden_prestataire"][] = Array("return" => generer_url_retour_paiement($contexte["token_panier"],"paypal"));
		$contexte["hidden_prestataire"][] = Array("rm" => "2");
		$contexte["hidden_prestataire"][] = Array("currency_code"=>"EUR");
		$contexte["hidden_prestataire"][] = Array("business"=>lire_config("echoppe/email_beneficiaire"));
		
		$i = 1;
		
		while($produit = sql_fetch($panier)){
			$lesprod = sql_select(Array("prix_base_htva,tva,titre"),"spip_echoppe_produits","id_produit = '".$produit["id_produit"]."'");
			while ($leprod = sql_fetch($lesprod)){
				
				
				$contexte["hidden_prestataire"][] = Array("item_name_".$i=>$leprod['titre']);
				

				
				if (!strlen($leprod["tva"])){
					$tva = lire_config("echoppe/taux_de_tva_par_defaut", 6);
				}else{
					$tva = $leprod["tva"];
				}
				
				$prix_tvac = calculer_prix_tvac($leprod['prix_base_htva'], $tva);
				
				$contexte["hidden_prestataire"][] = Array("amount_".$i=> $prix_tvac);
				
				$contexte["hidden_prestataire"][] = Array("quantity_".$i=> $produit["quantite"]);
				
				//$contexte["hidden_prestataire"][] = Array("tax_".$i=>"$tva");
				//$contexte["hidden_prestataire"][] = Array("tax_".$i=>"0");
				
				$i++;
			}
		}
		
		if ( lire_config("echoppe/mecanisme_gestion_frais_livraison","livraison_gratuite") == "forfait_unique" ){
			$contexte["hidden_prestataire"][] = Array("item_name_".$i=>_T("echoppe:frais_de_livraison"));
			$contexte["hidden_prestataire"][] = Array("amount_".$i=> lire_config("echoppe/montant_forfait_livraison",1));
			$contexte["hidden_prestataire"][] = Array("quantity_".$i=> "1");
			$i++;
		}
		
		/*
		if ( lire_config("echoppe/mecanisme_gestion_frais_livraison","livraison_gratuite") == "forfait_unique" ){
			//$contexte["hidden_prestataire"][] = Array("ShippingTotal".$i=>_T("echoppe:frais_de_livraison"));
			$contexte["hidden_prestataire"][] = Array("shipping"=> number_format(lire_config("echoppe/montant_forfait_livraison",1),2,","," "));
			//$contexte["hidden_prestataire"][] = Array("quantity_".$i=> "1");
			//$i++;
		}
		*/
		$contexte["action_prestataire"] = "https://www.paypal.com/fr/cgi-bin/webscr";
		$contexte["pause"] = 2;
		$contexte["method_prestataire"] = "post";
		$contexte["submit"] = "Payer via Paypal";
		
	}else{
		$contexte["erreur_generation_formulaire"][] = "Panier vide !";
	}
	
	return $contexte;
	
}

?>
