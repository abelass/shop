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

function shop_affiche_milieu($flux){
	// affichage du formulaire d'activation désactivation projets
		
	

    if ($flux['args']['exec']=='article') {
    include_spip('shop_fonctions');    
	$id_article = $flux['args']['id_article'];
	$rubriques_produits=rubrique_produits($id_article);
		if($rubriques_produits){
		    include_spip('inc/layer');
				$deplie=false;
				if(_request('formulaire_action')=='prix' OR $_REQUEST['retour_action']) $deplie=true;
				$contexte = array('id_article'=>$id_article);
				$contenu .= recuperer_fond('prive/squelettes/contenu/prix', $contexte);
				$res .= cadre_depliable('',_T('shop:info_prix'),$deplie,$contenu,'edition_prix');    		
                if ($p=strpos($flux['data'],"<!--affiche_milieu-->"))
                 $flux['data'] = substr_replace($flux['data'],$res,$p,0);
                else
                    $flux['data'] .= $res;
				    }
		}
return $flux;
}

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

// Ajouter la configuration aux objets
function shop_objets_shop($flux){
    
    // La configuration
    $flux['data']['configurer_shop']=array(
        'action'=>'configurer_shop',
        'objet'=>'configurer_shop',
        'nom_action'=>_T('spip:icone_configuration_site'),
        'icone'=>'cfg-16.png'
        );
        
    return $flux;
}
?>
