<?php

	//Déclaration des pipelines
	// Forms
	$GLOBALS['spip_pipeline']['shop_form_commande_afficher_general'] = '';	
	$GLOBALS['spip_pipeline']['shop_form_commande_charger'] = '';	
	$GLOBALS['spip_pipeline']['shop_form_commande_traiter'] = '';
	$GLOBALS['spip_pipeline']['shop_form_commande_afficher_prev_prix'] = '';	
	$GLOBALS['spip_pipeline']['shop_form_cfg'] = '';	
    $GLOBALS['spip_pipeline']['objets_shop'] = '';    
		
	// Affichage interfacen interne
	$GLOBALS['spip_pipeline']['shop_affiche_gauche_shortcuts'] = '';	
	
	function autoriser_article_id_produit_modifierextra_dist($faire, $type, $id, $qui, $opt){
   		 $id_rubrique = $opt['contexte']['id_rubrique'];
   		 if(!$id_rubrique)$id_rubrique=_request('id_rubrique');
   		 
		if (!$id_rubrique) {
        		$id_rubrique = sql_getfetsel("id_rubrique", "spip_articles", "id_article=".intval($id));
    			}
    			
    		$rubriques_produits=rubrique_produits($id_rubrique,'rubrique');		
	    if ($rubriques_produits) {
			return true;
		    }
	    //elseif()
    return false;
}
function autoriser_article_id_produit_voirextra_dist($faire, $type, $id, $qui, $opt) {
    return autoriser('modifierextra', $type, $id, $qui, $opt);
}
	
	
			
			
?>
