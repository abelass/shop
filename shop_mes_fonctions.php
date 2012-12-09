<?php
include_spip('base/abstract_sql');


// traduit le nom de la devise
function traduire_devise($code_devise){
	include_spip('inc/devises');

	$devises =devises();
	$trad= $devises[$code_devise];

	return $trad;
}
function prix_defaut($id_objet,$objet='article'){

	if($_COOKIE['spip_devise'])$devise_defaut=$_COOKIE['spip_devise'];
	elseif(lire_config('shop/devise_default'))$devise_defaut=lire_config('shop/devise_default');
	else 	$devise_defaut='EUR';

	$req=sql_select('code_devise,prix','spip_shop_prix','id_objet='.$id_objet.' AND objet='.sql_quote($objet));

	while($row=sql_fetch($req)){
	
		$prix= $row['prix'].' '.traduire_devise($row['code_devise']);
	
		if($row['code_devise']==$devise_defaut) $defaut = $row['prix'].' '.traduire_devise($row['code_devise']);
	}	
		
	if($defaut)$defaut=$defaut;
	else $defaut=$prix;

	return $defaut;
}

function prix_objet($id_objet,$objet='article',$devise='',$integer=false){

	if(!$devise)$devise=devise_defaut($id_objet,$objet);
	if(!$devise)$devise='EUR';

	$sql_prix=sql_fetsel('code_devise,prix','spip_shop_prix','id_objet='.$id_objet.' AND objet='.sql_quote($objet).' AND code_devise='.sql_quote($devise));

		$prix= $sql_prix['prix'].($integer?'':' '.traduire_devise($sql_prix['code_devise']));

	return $prix;
}

function devise_defaut($id_objet,$objet='article'){

	if($_COOKIE['spip_devise'])$devise_defaut=$_COOKIE['spip_devise'];
	elseif(lire_config('shop/devise_default'))$devise_defaut=lire_config('shop/devise_default');
	else 	$devise_defaut='EUR';

	$req=sql_select('code_devise,prix','spip_shop_prix','id_objet='.$id_objet.' AND objet='.sql_quote($objet));

	while($row=sql_fetch($req)){
	
		$prix= $row['prix'].' '.traduire_devise($row['code_devise']);
	
		if($row['code_devise']==$devise_defaut) $defaut = $row['code_devise'];
	}	
		
	if($defaut)$defaut=$defaut;
	else $defaut=$prix;

	return $defaut;
}

function titre_mot($id_mot){
	$titre=sql_fetsel('titre','spip_mots','id_mot='.$id_mot);

	return extraire_multi($titre['titre']);
}



function titre_objet($id_objet,$objet='article'){
	$titre=sql_fetsel('titre','spip_'.$objet.'s','id_'.$objet.'='.$id_objet);

	return $titre['titre'];
}

function donnees_objet($id_objet,$objet='article',$champs='*'){
	$donnees=sql_fetsel($champs,'spip_'.$objet.'s','id_'.$objet.'='.$id_objet);

	return $donnees;
}

function traduire_code_devise($code_devise,$id_objet,$objet='article',$option=""){

	$prix=sql_fetsel('prix','spip_shop_prix','id_objet='.$id_objet.' AND objet='.sql_quote($objet).' AND code_devise ='.sql_quote($code_devise));

	$return =$prix['prix'].' '. traduire_devise($code_devise);
	
	if($option=='prix') $return =$prix['prix'];
	
	
	return $return;
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
    
function rubrique_config($rubrique_produit){

	$rubriques=array();
	foreach($rubrique_produit AS $rubrique){
		$explode="";
		$explode=explode('|',$rubrique);
		$rubriques[]=$explode[1];
		}

     	return $rubriques;
}
    
function rubrique_produits($id,$objet='article',$sousrubriques=false){

     	$rubrique_produit=lire_config('shop/rubrique_produits');
     	if($rubrique_produit){
		$id_parent=rubrique_config($rubrique_produit);

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
	
	if (is_array($id_parent))$id_parent=implode(',',$id_parent);
	


	$sql=sql_select('id_rubrique','spip_rubriques','id_parent IN ('.$id_parent.')');
		
	while($row=sql_fetch($sql)){
		$rubriques[$row['id_rubrique']]=rubriques_enfant($row['id_rubrique'],$row['id_rubrique']);
		}
return $rubriques;
}

// Les fonctions du plugin prix

// Sécurité
if (!defined('_ECRIRE_INC_VERSION')) return;
/*
// Un filtre pour obtenir le prix HT d'un objet
function prix_ht_objet($id_objet, $type_objet){
	$fonction = charger_fonction('ht', 'inc/prix');
	return $fonction($type_objet, $id_objet);
}*/
/*
// La balise qui va avec le prix HT
function balise_PRIX_HT_dist($p) {
	if (!$_type = interprete_argument_balise(1,$p)){
		$_type = sql_quote($p->type_requete);
		$_id = champ_sql($p->boucles[$p->id_boucle]->primary,$p);
	}
	else
		$_id = interprete_argument_balise(2,$p);
	$connect = $p->boucles[$p->id_boucle]->sql_serveur;
	$p->code = "prix_ht_objet(intval(".$_id."),".$_type.','.sql_quote($connect).")";
	$p->interdire_scripts = false;
	return $p;
}

// Un filtre pour obtenir le prix TTC d'un objet
function prix_objet($id_objet, $type_objet){
	$fonction = charger_fonction('prix', 'inc/');
	return $fonction($type_objet, $id_objet);
}

// La balise qui va avec le prix TTC
function balise_PRIX_dist($p) {
	if (!$_type = interprete_argument_balise(1,$p)){
		$_type = _q($p->type_requete);
		$_id = champ_sql($p->boucles[$p->id_boucle]->primary,$p);
	}
	else
		$_id = interprete_argument_balise(2,$p);
	$connect = $p->boucles[$p->id_boucle]->sql_serveur;
	$p->code = "prix(intval(".$_id."),".$_type.','.sql_quote($connect).")";
	$p->interdire_scripts = false;
	return $p;
}
*/
/*
 * Formater un nombre pour l'afficher comme un prix avec une devise
 *
 * @param float $prix Valeur du prix à formater
 * @return string Retourne une chaine contenant le prix formaté avec une devise (par défaut l'euro)
 */

function prix_formater($prix){
	// On formate d'abord le montant suivant les conventions du pays
	setlocale(LC_MONETARY, 'fr_FR');
	$prix = money_format('%i', $prix);
	
	// Ensuite on ajoute la devise
	$prix .= ' €';
	
	// Fini
	return $prix;
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
