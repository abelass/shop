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
		
	include_spip('shop_fonctions');

    if ($flux['args']['exec']=='article') {
	$id_article = $flux['args']['id_article'];
	$rubriques_produits=rubrique_produits($id_article);
		if($rubriques_produits){
				$deplie=false;
				if($_REQUEST['formulaire_action']=='prix' OR $_REQUEST['retour_action']) $deplie=true;
				if($_REQUEST['retour_action']=='prix')$deplie=true;
				$contexte = array('id_article'=>$id_article);
				$contenu .= recuperer_fond('prive/squelettes/contenu/prix', $contexte,array('ajax'=>'oui'));
				$res .= cadre_depliable('',_T('shop:info_prix'),$deplie,$contenu,'prix','e');    		
				$flux["data"] .= $res;
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

?>
