<?php

if (!defined("_ECRIRE_INC_VERSION")) return;


function formulaires_prix_charger_dist($id_objet,$objet='article'){


	$devises_dispos =lire_config('shop/devises');
	
	
	// Devise par défaut si rien configuré
	if(!$devises_dispos)$devises_dispos=array('0'=>'EUR');
	$devises_choisis =array();	
	$prix_choisis =array();		
	$d=sql_select('code_devise,objet,id_objet,prix,id_prix','spip_shop_prix','id_objet='.$id_objet.' AND objet ='.sql_quote($objet));
	
	//établit les devises diponible moins ceux déjà utilisés
		
	while($row=sql_fetch($d)){
		$devises_choisis[$row['code_devise']] = $row['code_devise'];

		$prix_choisis[]=$row;
			
		}
		
	
		
	$devises = array_diff($devises_dispos,$devises_choisis);
	
	
	$valeurs = array(
		'prix_choisis'=>$prix_choisis,
	
		'devises'=>$devises,	
		'code_devise'=>'',
		'prix'=>'',									
		);
	return $valeurs;			
}


function formulaires_prix_verifier_dist($id_objet,$objet='article'){

	foreach(array('prix','code_devise') as $obligatoire)
	
	if (!_request($obligatoire)) $erreurs[$obligatoire] = _T('encheres:champ_obligatoire');	
		
    return $erreurs; // si c'est vide, traiter sera appele, sinon le formulaire sera resoumis
}

/*Elimination de la base de donées */
function formulaires_prix_traiter_dist($id_objet,$objet='article'){
	$valeurs=array(
		'id_objet'=>$id_objet,
		'objet'=>$objet,	
		'prix' => _request('prix'),
		'code_devise' => _request('code_devise')
		);
	sql_insertq('spip_shop_prix', $valeurs);
}

?>