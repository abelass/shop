<?php

// Sécurité
if (!defined('_ECRIRE_INC_VERSION')) return;

include_spip('base/abstract_sql');


<<<<<<< HEAD:shop_fonctions.php
function titre_mot($id_mot){
	$titre=sql_fetsel('titre','spip_mots','id_mot='.$id_mot);

	return extraire_multi($titre['titre']);
=======
function devises(){
    $devises=array(
    
        //A
        'AUD'=>'AUD',   
             
        //B 
        'BRL'=>'Real',               
             
        //C          
        'CAD'=>'CAD',        
        'CHF'=>'CHF',    
        'CNY'=>'Yuan',
        'CSD'=>'CSD',                        
        'CZK'=>'CZK',
                 
        //D     
        'DKK'=>'DKK', 
             
        //E     
        'EUR'=>'€',
             
        //G          
        'GBP'=>'£',
             
        //H 
        'HKD'=>'HKD',        
        'HUF'=>'HUF',
                 
        //I              
        'IDR'=>'IDR',                
        'ILS'=>'Shekel',
        'IQD'=>'IQD',       
        'IRR'=>'IRR',       
        'ISK'=>'ISK',   
            
        //J         
        'JEP'=>'JEP',   
        'JOD'=>'JOD',                       
        'JMD'=>'JMD',                
        'JPY'=>'¥',
        
        //K     
        'KES'=>'KES',   
        'KGS'=>'KGS',
        'KWD'=>'KWD',           
        'KZT'=>'Tenge',                         
        
        //L     
        'LAK'=>'Kip',   
        'LBP'=>'LBP',   
        'LKR'=>'LKR',                           
        'LRD'=>'LRD',    
        'LTL'=>'Litas', 
        'LVL'=>'Lat',                               
                            
        //M     
        'MAD'=>'Dirham',     
        'MDL'=>'MDL',                   
        'MGA'=>'Ariary',    
        'MKD'=>'MKD',           
        'MNT'=>'Tughrik',           
        'MRO'=>'Ouguiya',   
        'MUR'=>'MUR',
        'MVR'=>'Rufiyaa',                           
        'MWK'=>'MWK',                    
        'MXN'=>'MXN',
        'MYR'=>'Ringgit',       
        'MZN'=>'Metical',       
        
        //N     
        'NAD'=>'NAD',
        'NGN'=>'Naira',
        'NIO'=>'Cordoba',           
        'NPR'=>'NPR',                                    
        'NOK'=>'NOK',        
        'NZD'=>'NZD',
        
        //O     
        'OMR'=>'OMR',
                
        'QAR'=>'Riyal',             
        
        //P     
        'PGK'=>'Kina',
        'PHP'=>'PHP',   
        'PKR'=>'PKR',                                
        'PLN'=>'Zloty',  
            
        
        'RON'=>'RON',       
        'RUB'=>'Rouble',            
        'RWF'=>'RWF',
        
        //S     
        'SCR'=>'SCR',           
        'SDD'=>'SDD',                            
        'SEK'=>'SEK',        
        'SGD'=>'SGD',
        'SOS'=>'SOS',       
        'SLL'=>'Leone',         
        'SRD'=>'SRD',       
        'STD'=>'Dobra',
        'SVC'=>'Colon',
        'SYP'=>'SYP',                           
                
        //T 
        'THB'=>'Baht',              
        'TJS'=>'Somoni',                    
        'TND'=>'TND',   
        'TMM'=>'TMM',                   
        'TRY'=>'Lirasi',
        'TTD'=>'TTD',       
        'TWD'=>'TWD',
        'TZS'=>'TZS',       
            
            
        //U     
        'UAH'=>'Hryvna',    
        'UGX'=>'UGX',                                                    
        'USD'=>'USD',
        'UZS'=>'UZS',       
        
        //V     
        'VND'=>'Dong',
        
        //X     
        'XAF'=>'XAF',
        'XOF'=>'XOF',   
        
        //Y     
        'YER'=>'Rial',          
        
        //Z     
        'ZMK'=>'ZMK',                               
        'ZWN'=>'ZWN',       
        );

    return $devises;
}

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

	$req=sql_select('code_devise,prix','spip_prix','id_objet='.$id_objet.' AND objet='.sql_quote($objet));

	while($row=sql_fetch($req)){
	
		$prix= $row['prix'].' '.traduire_devise($row['code_devise']);
	
		if($row['code_devise']==$devise_defaut) $defaut = $row['prix'].' '.traduire_devise($row['code_devise']);
	}	
		
	if($defaut)$defaut=$defaut;
	else $defaut=$prix;

	return $defaut;
}

/*
 * déja utilisé à revoir
function prix_objet($id_objet,$objet='article',$devise='',$integer=false){

	if(!$devise)$devise=devise_defaut($id_objet,$objet);
	if(!$devise)$devise='EUR';

	$sql_prix=sql_fetsel('code_devise,prix','spip_prix','id_objet='.$id_objet.' AND objet='.sql_quote($objet).' AND code_devise='.sql_quote($devise));

		$prix= $sql_prix['prix'].($integer?'':' '.traduire_devise($sql_prix['code_devise']));

	return $prix;
}
*/
function devise_defaut($id_objet,$objet='article'){

	if($_COOKIE['spip_devise'])$devise_defaut=$_COOKIE['spip_devise'];
	elseif(lire_config('shop/devise_default'))$devise_defaut=lire_config('shop/devise_default');
	else 	$devise_defaut='EUR';

	$req=sql_select('code_devise,prix','spip_prix','id_objet='.$id_objet.' AND objet='.sql_quote($objet));

	while($row=sql_fetch($req)){
	
		$prix= $row['prix'].' '.traduire_devise($row['code_devise']);
	
		if($row['code_devise']==$devise_defaut) $defaut = $row['code_devise'];
	}	
		
	if($defaut)$defaut=$defaut;
	else $defaut=$prix;

	return $defaut;
>>>>>>> b82f96885917e579b0f5bd5dfe68c536c22a8629:shop_prix_fontions.php
}



<<<<<<< HEAD:shop_fonctions.php
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

=======
function traduire_code_devise($code_devise,$id_objet,$objet='article',$option=""){

	$prix=sql_fetsel('prix','spip_prix','id_objet='.$id_objet.' AND objet='.sql_quote($objet).' AND code_devise ='.sql_quote($code_devise));

	$return =$prix['prix'].' '. traduire_devise($code_devise);
	
	if($option=='prix') $return =$prix['prix'];
	
	
	return $return;
}
>>>>>>> b82f96885917e579b0f5bd5dfe68c536c22a8629:shop_prix_fontions.php
?>
