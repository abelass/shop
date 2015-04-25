<?php
if (!defined("_ECRIRE_INC_VERSION")) return;


function shop_insert_head($flux){
       $flux .= '<link rel="stylesheet" href="'.find_in_path('css/styles_shop_public.css').'" type="text/css" media="all" />';
 	return $flux;	
}

function shop_header_prive($flux){
           $flux .= '<link rel="stylesheet" href="'.find_in_path('css/styles_shop_admin.css').'" type="text/css" media="all" />';
    return $flux;
}


function shop_formulaire_charger($flux){
 $form=$flux['args']['form'];
 
 // cré un contact si pas encore existant
 if($form == 'inscription_client'
         and _request('page') == 'shop'
         and _request('appel') == 'mes_coordonnees'
       ){
    if($id_auteur = verifier_session()){
        $inscrire_client = charger_fonction('traiter','formulaires/inscription_client');
        $inscrire_client();
        }
    }
    
 if($form == 'editer_client'
         and _request('page') == 'shop'
         and _request('appel') == 'mes_coordonnees'
       ){
        include_spip('inc/config');
		
		
		/*Charger les champs extras*/
        $config=lire_config('shop',array($config));
        $flux['data']['champs_extras']=shop_champs_extras_presents($config,'','par_objets','',$form);


        foreach($flux['data']['champs_extras'] AS $objet=>$champs){
            foreach(array_column($champs,'options') AS $data){
                $flux['data'][$data['nom']]=_request($data['nom']);
                }
            }   
        }  
	   


     return $flux;
}


function shop_formulaire_verifier($flux){
 $form=$flux['args']['form'];
 

 if($form == 'editer_client'
         and _request('page') == 'shop'
         and _request('appel') == 'mes_coordonnees'
       ){
       	
		/*
		 * Tester si les champs extras sont obligatoire
		 */
		 
        //Récupérer les champs extras choisis
        include_spip('inc/config');
        $config=lire_config('shop',array());
        $champs_extras=shop_champs_extras_presents($config,'','par_objets');
		
        //Déterminer les champs obligatoire
        $obligatoires=array();

        foreach($champs_extras['commande'] AS $value){
		
            if(isset($value['options']['obligatoire']) AND $value['options']['obligatoire']=='oui')
            	$obligatoires[]=$value['options']['nom'];

			}

        foreach($obligatoires AS $champ) {
        	$request=_request($champ);
        	if(is_array($request) AND count($request)==0){$request='';}
            if(!$request)$flux['data'][$champ]=_T("info_obligatoire");
            } 
	   }
		 return $flux;
}


/*
 * Salement pique dans z-commerce
 * S'inscruster apres le traitement classique du formulaire d'edition des coordonnees (etape 3) pour
 * - creer la commande e partir du panier en cours (s'il n'est pas vide)
 * - y associer les adresses de facturation et de livraison (copies de l'adresse principale du client)
 * - rediriger vers la page d'affichage de la commande et de paiement
 *
 * @return $flux 
 */
function shop_formulaire_traiter($flux){
    // Si on est sur le formulaire client qui est sur la page identification
    $form=$flux['args']['form'];
    
    if($form == 'inscription_client'
         and _request('page') == 'shop'
         and _request('appel') == 'mes_coordonnees'
         and include_spip('inc/paniers')
         and paniers_id_panier_encours()
       ){
      //si autentification automatique activé on authentifie
      include_spip('inc/config');
      //$config=lire_config('shop/authentification_automatique',array());
	  $authentification_automatique=lire_config('shop/authentification_automatique',array());

      if($authentification_automatique[0]=='on' AND !session_get('id_auteur')){
          if($auteur=sql_fetsel('*','spip_auteurs','email='.sql_quote(_request('mail_inscription')))){
                include_spip('inc/auth');
                auth_loger($auteur);
          }
      }
    }
    
    if($form == 'editer_client'
         and _request('page') == 'shop'
         and _request('appel') == 'mes_coordonnees'
         and include_spip('inc/paniers')
         and paniers_id_panier_encours()

       ){
        // On recupere d'abord toutes les informations dont on va avoir besoin
        // Deje le visiteur connecte
        include_spip('inc/commandes');
		include_spip('inc/config');
		include_spip('inc/shop');
		 
        $id_auteur = session_get('id_auteur');
		$config=lire_config('shop',array($config));
    
        /* On cree la commande ici*/
        $id_commande = creer_commande_encours();
        
        /*
		 * On rajoute les champs extras de la commande
		 */
        
        //Récupérer les champs extras choisis
		$champs_extras=shop_champs_extras_presents($config,'','par_objets');
        
        //preparer les valeurs pour chaque objet
        $c=array();
        $ids=array('commande'=>$id_commande);
		
		 
        foreach($champs_extras AS $objet=>$champs){ 
            foreach(array_column($champs,'options') AS $data){
				$c[$objet][$data['nom']]=_request($data['nom']);
                }
            }  
		
        //Actualiser les tables
        foreach($c AS $objet=>$valeurs) {
            sql_updateq('spip_'.$objet.'s',$valeurs,'id_'.$objet.'='.$ids[$objet]);
            }  
        
        
        // On cherche l'adresse principale du visiteur
        $id_adresse = sql_getfetsel( 'id_adresse',  'spip_adresses_liens',
                         array( 'objet = '.sql_quote('auteur'),
                        'id_objet = '.intval($id_auteur),
                        'type = '.sql_quote('pref') ) );
        
        $adresse = sql_fetsel('*', 'spip_adresses', 'id_adresse = '.$id_adresse);
        unset($adresse['id_adresse']);
        
        // On copie cette adresse comme celle de facturation
        $id_adresse_facturation = sql_insertq('spip_adresses', $adresse);
        sql_insertq( 'spip_adresses_liens',
                        array( 'id_adresse' => $id_adresse_facturation,
                                'objet' => 'commande',
                                'id_objet' => $id_commande,
                                'type' => 'facturation' ) );
    
        // On copie cette adresse comme celle de livraison
        $id_adresse_livraison = sql_insertq('spip_adresses', $adresse);
        sql_insertq( 'spip_adresses_liens',
                        array( 'id_adresse' => $id_adresse_livraison,
                                'objet' => 'commande',
                                'id_objet' => $id_commande,
                                'type' => 'livraison' ) );
    }
    return($flux);
}

function shop_recuperer_fond($flux){
    $fond=$flux['args']['fond'];
	
    if ($fond== 'formulaires/editer_client'){
            if(isset($flux['args']['contexte']['champs_extras']['commande'])){
            	//reconvertir les labels en unicode	
            	foreach($flux['args']['contexte']['champs_extras']['commande'] AS $key=>$value){
            		$flux['args']['contexte']['champs_extras']['commande'][$key]['options']['label']=html2unicode($value['options']['label']);
					
            	}
                $champs=recuperer_fond('formulaires/champs_commandes_extras',$flux['args']['contexte']);
            }

        $flux['data']['texte'] = str_replace("<!--extra-->",  $champs.'<!--extra-->',$flux['data']['texte']);
    }
    
    if ($fond== 'prive/objets/contenu/commande'){

        $id=$flux["data"]['contexte']['id'];
        include_spip('inc/shop');
        include_spip('inc/config'); 
		$config=lire_config('shop',array($config));
        
        //On chercher les champs prévus
        $champs_extras=shop_champs_extras_presents($config,'','','commande');;
		
		$champs=array();
		
        foreach(array_column($champs_extras[0],'options') AS $data){
        	$champs[$data['nom']]=$data['label'];
            }

        //Les valeurs de la commande
        $data=sql_fetsel(array_keys($champs),'spip_commandes','id_commande='.$id);

        $c = recuperer_fond("prive/squelettes/inclure/champs_extras_commande",array('champs_extras' =>$champs,'data'=>$data));

        $flux['data']['texte'] .= $c;
    }
    
    if ($fond== 'notifications/contenu_commande_mail'){

        $id=$flux["data"]['contexte']['id'];
        include_spip('inc/shop');
        include_spip('inc/config'); 
		
		$config=lire_config('shop',array($config));
        
        //On chercher les champs prévus
        $champs_extras=shop_champs_extras_presents($config,'','','commande');;
		
		$champs=array();
		
        foreach(array_column($champs_extras[0],'options') AS $data){
        	$champs[$data['nom']]=$data['label'];
            }

        //Les valeurs de la commande
        $data=sql_fetsel(array_keys($champs),'spip_commandes','id_commande='.$id);

        $c = recuperer_fond("prive/squelettes/inclure/champs_extras_commande",array('champs_extras' =>$champs,'data'=>$data));

        $flux['data']['texte'] = str_replace("<!--extra_commande-->",  $c.'<!--extra_commande-->',$flux['data']['texte']);
    }    
        
    return $flux;
}

// Eliminer le panier après le retour paypal
function shop_traitement_paypal($flux){
   
    $reference = $flux['args']['paypal']['invoice'];
    $commande = sql_fetsel('id_commande, statut, id_auteur', 'spip_commandes', 'reference = '.sql_quote($reference));
    $objet=sql_fetsel('objet,id_objet','spip_commandes_details','id_commande='.$commande['id_commande']);
    $id_panier=sql_getfetsel('id_panier','spip_paniers_liens','id_objet='.$objet['id_objet'].' AND objet='.sql_quote($objet['objet']));
    $action = charger_fonction('supprimer_panier', 'action/');
	$action($id_panier);
    spip_log("Retour paypal eliminer panier $id_panier",'paypal' . _LOG_INFO);
    return $flux;
}

//Eliminer le panier après le traitment bank
function shop_bank_traiter_reglement($flux){
	$id_panier=sql_getfetsel('id_panier','spip_transactions','id_transaction='.$flux['args']['id_transaction']);
	
	$action = charger_fonction('supprimer_panier', 'action/');
	$action($id_panier);
	return $flux;
}

//Afficher le menu shop pour les objets shop
function shop_affiche_gauche($flux){	
	$objet=$flux['args']['exec'];
	$objets_shop=objets_shop();	
	$actions=array_column($objets_shop, 'action');
	$afficher_objet=false;
	//Le cas normal l'exec correspond à l'action de a définition
	if(in_array($objet,$actions)) $afficher_objet=$objet;
	// cas ou une page action contient plusieurs onglets
	else{
		foreach(array_column($objets_shop, 'navigation','action') AS $action=>$navigation){
			if(in_array($objet,$navigation)){
				$afficher_objet=$action;
				break;
			}
		}
	}
	
	if ($afficher_objet) $flux['data'] .= recuperer_fond('prive/squelettes/navigation/shop',array('voir'=>$afficher_objet));
	
	return $flux;
}