<?php

function formulaires_transference_charger_dist(){
	include_spip('shop_mes_fonctions');
	
	$valeurs = array(
		'id_commande'=>_request('id_commande'),	
						
		);

return $valeurs;
}

function formulaires_commande_verifier_dist($id_produit,$n_titre=NULL){

	$email=_request('email');


	$date_enregistrement_annee=_request('date_enregistrement_annee');

   	$erreurs = array();
    	foreach(array('nom','email','adresse','code_postal','ville','titre','id_option','code_devise','date_enregistrement_jour','date_enregistrement_mois','date_enregistrement_annee','pays') as $champ) {
        	if (!_request($champ)) {
            		$erreurs[$champ] = _T('spip:info_obligatoire');
        		}
    	}
    	
    	if ($n_titre AND !_request('titre_2'))$erreurs['titre_2'] = _T('spip:info_obligatoire');
    	
    	if (count($erreurs)) {
        	$erreurs['message_erreur'] = _T('spip:avis_erreur');
   		 }
    
    	if($email AND !email_valide($email)){
		$erreurs['email'] = _T('info_email_invalide');
		}
		
			
				
	if (!checkdate(_request('date_enregistrement_mois') ,_request('date_enregistrement_jour'),$date_enregistrement_annee )){		
 		$erreurs['date_enregistrement_annee'] = _T('shop:date_non_valide');
 			}
 			
 	//Insertion du pipeline verifier	
	$pipelines = pipeline('shop_form_commande_verifier',array(
   		 'args'=>array(
        			'id_objet'=>$id_produit     			
    				), 
    			'data'=>""
			)
		);
	if(is_array($pipelines)){
		foreach($pipelines as $key=>$value)
		$erreurs[$key]=$value;
		}			
	
    return $erreurs;



}
function formulaires_commande_traiter_dist($id_produit,$n_titre=NULL){
   	refuser_traiter_formulaire_ajax(); 	
	include_spip('shop_mes_fonctions');
	$prev=_request('prev');
	$code_devise=_request('code_devise');
	$prix=_request('prix');
	//if(_request('prix'))$prix=traduire_code_devise($code_devise,$id_produit,'prix');
	$frais_livraison=lire_config('shop/frais_livraison_'.$code_devise);
	$date_enregistrement = _request('date_enregistrement_annee').'-'._request('date_enregistrement_mois').'-'._request('date_enregistrement_jour');
	$retour = array();

	if($prev){

		$retour['message_ok']='prev';
		}
	elseif(_request('modifier')){
		$retour['message_ok']='modifier';
		}
	else{
		$token = md5(uniqid(rand(), true));
		include_spip('inc/cookie');
	 	spip_setcookie('spip_shop_session',$token, time()+2*3600 );
		
		$valeurs = array(
			'nom'=>_request('nom'),
			'lang'=>_request('lang'),		
			'email'=>_request('email'),
			'adresse'=>_request('adresse'),
			'code_postal'=>_request('code_postal'),
			'ville'=>_request('ville'),		
			'pays'=>_request('pays'),					
			'id_produit'=>$id_produit,		
			'titre'=>_request('titre'),
			'titre_2'=>_request('titre_2'),			
			'de_la_part_de'=>_request('de_la_part_de'),			
			'id_option'=>_request('id_option'),
			'commentaire'=>_request('commentaire'),
			'prix'=>$prix,
			'frais_livraison'=>$frais_livraison,				
			'code_devise'=>$code_devise,
			'date_enregistrement'=>$date_enregistrement,
			'date_creation'=>date('Y-m-d H:i:s'),
			'token'=>$token							
			);
	 
	 	$id_commande=sql_insertq('spip_commandes',$valeurs);
	 	
		$retour['message_ok']=array('id_commande'=>$id_commande);
		
		 //Insertion du pipeline traiter
		 	
		$pipelines = pipeline('shop_form_commande_traiter',array(
			'args'=>array(
					'id_objet'=>$id_produit     			
					), 
				'data'=>""
				)
			);
			
	 	header('Location:'.parametre_url(_request('url_retour'),'id_commande',$id_commande,'&'));
	 	}



    return $retour;
}


?>
