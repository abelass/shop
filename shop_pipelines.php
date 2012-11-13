<?php
if (!defined("_ECRIRE_INC_VERSION")) return;
/*
 * pas utilisé pour le moment
function shop_affiche_gauche($flux){
     $exec = $flux["args"]["exec"];
     $args=$flux['args'];
    if (autoriser('shop_modifie') AND $exec=='shop'){
        $id_document=$args['id_document'];
        $voir=$args['voir'];
        //$flux['data'] .= recuperer_fond('prive/squelettes/navigation/shop',array('voir'=>$voir,'id_document'=>$id_document),array('ajax'=>true));
    }

    return $flux;
}*/

function shop_header_prive($flux){
	// affichage du formulaire d'activation désactivation projets	

       $flux .= '<link rel="stylesheet" href="'.find_in_path('css/styles_shop_admin.css').'" type="text/css" media="all" />';
 	return $flux;	
}

// ajout configuration Inscription2
function shop_I2_cfg_form($flux){
    $flux .= recuperer_fond('fonds/inscription2_shop');
	return $flux;	
}


function shop_objets_shop($flux){
    
    // La configuration
    $flux['configurer_shop']=array(
        'action'=>'configurer_shop',
        'nom_action'=>_T('spip:icone_configuration_site'),
        'icone'=>'cfg-16.png'
        );
        
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
    if( $flux['args']['form'] == 'editer_client'
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
        
        // On cherche l'adresse principale du visiteur
        $id_adresse = sql_getfetsel( 'id_adresse',  'spip_adresses_liens',
                         array( 'objet = '.sql_quote('auteur'),
                        'id_objet = '.intval($id_auteur),
                        'type = '.sql_quote('principale') ) );
        
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
?>
