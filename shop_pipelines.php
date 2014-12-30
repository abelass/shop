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
        $config=lire_config('shop',array($config));
    
        $flux['data']['champs_extras']=shop_champs_extras_presents($config,'','par_objets','',$form);
        include_spip('inc/shop');

        foreach($flux['data']['champs_extras'] AS $objet=>$champs){
           // echo serialize($objet);
            $noms=noms_champs_extras_presents($champs);
            foreach($noms AS $nom=>$label){
               // echo serialize($nom);
                $flux['data'][$nom]=_request($nom);
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
        //Récupérer les champs extras choisis
        include_spip('inc/config');
        $config=lire_config('shop',array($config));
        
        //Déterminer les champs obligatoire
        $obligatoires=array();
        foreach($config AS $name=>$value){
            $obligatoire='';
            list($objet,$champ,$obligatoire)=explode('-',$name);
            if(isset($config[$name.'_obligatoire']))$obligatoires[]=$champ;
            }
        foreach($obligatoires AS $champ) {
            if(!_request($champ))$flux['data'][$champ]=_T("info_obligatoire");
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
      $config=lire_config('shop/authentification_automatique',array());

      if($config[0]=='on'){
          if($auteur=sql_fetsel('*','spip_auteurs','email='.sql_quote(_request('mail_inscription')))){
                include_spip('inc/auth');
                //$edition_dist = charger_fonction('traiter', 'formulaires/editer_client');
                //$erreurs = $edition_dist($auteur['id_auteur'],'');
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
        $id_auteur = session_get('id_auteur');
    
        // On cree la commande ici
        include_spip('inc/commandes');
        $id_commande = creer_commande_encours();
        
        //On rajoute les champs extras de la commande
        
        //Récupérer les champs extras choisis
        include_spip('inc/config');
        $config=lire_config('shop',array($config));
        
        //Déterminer les champs choisis
        include_spip('inc/shop');
        $objets_possibles=objets_champs_extras();
        
        //preparer les valeurs pour chaque objet
        $champs=array();
        $ids=array('commande'=>$id_commande);
        foreach($config AS $name=>$value){
            list($objet,$champ,$obligatoire)=explode('-',$name);
            if(in_array($objet,$objets_possibles))$champs[$objet][$champ]=_request($champ);
            }
        
        //Actualiser les tables
        foreach($champs AS $objet=>$valeurs) {
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

                $champs=recuperer_fond('formulaires/champs_commandes_extras',$flux['args']['contexte']);

            }

        $flux['data']['texte'] = str_replace("<!--extra-->",  $champs.'<!--extra-->',$flux['data']['texte']);
    }
    

    if ($fond== 'prive/objets/contenu/commande'){

        $id=$flux["data"]['contexte']['id'];
        include_spip('inc/shop');
        include_spip('inc/config'); 
        
        //On chercher les champs prévus
        $champs_extras=champs_reduits();
               
        //Les valeurs de la commande
        $champs=sql_fetsel('*','spip_commandes','id_commande='.$id);

        //On détermine les valeurs qui sont des champs extras
        $champs = array_intersect_key($champs,array_flip($champs_extras));

        $c = recuperer_fond("prive/squelettes/inclure/champs_extras_commande",array('champs_extras' =>$champs));

        $flux['data']['texte'] .= $c;
    }
    
    if ($fond== 'notifications/contenu_commande_mail'){

        $id=$flux["data"]['contexte']['id'];
        include_spip('inc/shop');
        include_spip('inc/config'); 
        
        //On chercher les champs prévus
        $champs_extras=champs_reduits();
               
        //Les valeurs de la commande
        $champs=sql_fetsel('*','spip_commandes','id_commande='.$id);

        //On détermine les valeurs qui sont des champs extras
        $champs = array_intersect_key($champs,array_flip($champs_extras));

        $c .= recuperer_fond("prive/squelettes/inclure/champs_extras_commande",array('champs_extras' =>$champs));

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
    sql_delete('spip_paniers_liens','id_panier='.$id_panier);
    sql_delete('spip_paniers','id_panier='.$id_panier);
    spip_log("Retour paypal eliminer panier $id_panier",'paypal' . _LOG_INFO);
    return $flux;
}

?>
