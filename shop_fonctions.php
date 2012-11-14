<?php

// Sécurité
if (!defined('_ECRIRE_INC_VERSION')) return;

include_spip('base/abstract_sql');

//Liste les objets gérés par ce plugin
function objets_shop(){
    
    $objets_shop=charger_fonction('objets_shop','inc');
    
    return $objets_shop();
}


function titre_mot($id_mot){
	$titre=sql_fetsel('titre','spip_mots','id_mot='.$id_mot);

	return extraire_multi($titre['titre']);
}


function titre_objet($id_objet,$objet='article'){
	$titre=sql_fetsel('titre','spip_'.$objet.'s','id_'.$objet.'='.$id_objet);

	return $titre['titre'];
}

function donnees_objet($id_objet='',$objet='article',$champs='*'){

    if(!$id_objet)$id_objet=_request('id_'.$objet);
	if($id_objet)$donnees=sql_fetsel($champs,'spip_'.$objet.'s','id_'.$objet.'='.$id_objet);

	return $donnees;
}


// Cherche le id_produit correspondant
function traduire_numero_article($id_objet,$objet='article'){

	$id_produit=sql_getfetsel('id_produit','spip_'.$objet.'s','id_'.$objet.'='.sql_quote($id_objet));
	
	return $id_produit;
}


function generer_url_retour_paiement($id_commande,$prestataire_paiement,$url_encode=''){
	
	$id_uniq_temp = sha1($id_commande.date("YmdGis"));
	
	
	$valeurs=array(
		'token_retour'=>$id_uniq_temp,
		'id_commande'=>$id_commande,
		'token_panier'=>$token_panier,
		'prestataire_paiement'=>$prestataire_paiement
		);
	

	sql_insertq("spip_shop_tokens_retour",$valeurs);
	
	$url=generer_url_action("shop_retour_prestataire_paiement","token_retour_paiement=$id_uniq_temp",$separateur);
	
	if ($url_encode) $url=myUrlEncode($url);
	
	return $url;
	
}

function myUrlEncode($string) {
    $replacements = array('%21', '%2A', '%27', '%28', '%29', '%3B', '%3A', '%40', '%26', '%3D', '%2B', '%24', '%2C', '%2F', '%3F', '%25', '%23', '%5B', '%5D');
    $entities = array('!', '*', "'", "(", ")", ";", ":", "@", "&", "=", "+", "$", ",", "/", "?", "%", "#", "[", "]");
    return str_replace($entities, $replacements, urlencode($string));
    }
    
/*function rubrique_config($rubrique_produit){

	$rubriques=array();
	foreach($rubrique_produit AS $rubrique){
		$explode="";
		$explode=explode('|',$rubrique);
		$rubriques[]=$explode[1];
		}

     	return $rubriques;
}*/
    
function rubrique_produits($id,$objet='article',$sousrubriques=false){
        include_spip('inc/config');

     	$rubrique_produit=picker_selected(lire_config('shop/rubrique_produits'),'rubrique');

     	if($rubrique_produit){
		$id_parent=$rubrique_produit;

     	if(!$sousrubriques){
			$rubriques=$id_parent;
			}
		else $rubriques=array();

		$rubriques=rubriques_enfant($id_parent,$rubriques);
		$valide=sql_getfetsel('id_'.$objet, 'spip_'.$objet.'s', 'id_'.$objet.'='.$id.' AND id_rubrique IN ('.implode(',',$rubriques).')');
	}
	else echo '<div class="erreur">veuillez configurer une rubrique produit</div>';
return $valide;
} 


function rubriques_enfant($id_parent,$rubriques=array()){
	//echo serialize($id_parent);
$id_p='';
	if (is_array($id_parent))$id_parent=implode(',',$id_parent);


	if($id_parent)$sql=sql_select('id_rubrique','spip_rubriques','id_parent IN ('.$id_parent.')');
	
    $id_p=array();
	while($row=sql_fetch($sql)){
		$id_p[]=$row['id_rubrique'];
        $rubriques[]=$row['id_rubrique'];
		}

    if(count($id_p)>0)$rubriques=rubriques_enfant($id_p,$rubriques);
return $rubriques;
}

//teste si l'objet est un produit

function objet_produit($id_rubrique,$objet,$id_objet){
    include_spip('inc/config');
    $rubrique_produit=picker_selected(lire_config('shop/rubrique_produits'),'rubrique');
    
}

// Fournis les données pour l'api selon l'environnemnt (teste ou production)
function api_paypal($objet=''){
	
	$teste=lire_config('shop/test_paypal');
	$url_teste='sandbox.paypal.com';
	$url_production='www.paypal.com';
	if($teste)
		$donnes_api=array('url_paypal'=>$url_teste,'email_paypal'=>lire_config('shop/email_paypal_test'));
	else 
		$donnes_api=array('url_paypal'=>$url_production,'email_paypal'=>lire_config('shop/email_paypal'));
		
	if($objet)$donnes_api=$donnes_api[$objet];
	
	return $donnes_api;
	}

?>
