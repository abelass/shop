<?php

if (!defined("_ECRIRE_INC_VERSION")) return;

//Enlèves les champs utilitaires et remplace saisie par saisie_2 et nom_2 par nom et teste si obligatoire
function shop_champs_extras_nettoyes($champs_actifs=array(),$champs_extras,$objet='',$defaut=array(),$form=''){
    $champs=array();
    
     foreach($champs_extras as $key=>$value){
     	//nom différent de celui de la configuration
        if(isset($value['options']['nom_2'])){
            $value['options']['nom']=$value['options']['nom_2'];
            unset($value['options']['nom_2']);
        }
		//saisie différent de celui de la configuration 
        if(isset($value['saisie_2'])){
                $value['saisie']=$value['saisie_2'];
                unset($value['saisie_2']);
            }
		//label différent de celui de la configuration 
        if(isset($value['options']['label_2'])){
            $value['options']['label']=$value['options']['label_2'];
            unset($value['options']['label_2']);
        } 
		//label différent de celui de la configuration 
        if(isset($value['options']['defaut_2'])){
            $value['options']['defaut']=$value['options']['defaut_2'];
            unset($value['options']['label_2']);
        } 
		
		//data différent de celui de la configuration 
        if(isset($value['options']['datas_2'])){
            $value['options']['datas']=$value['options']['datas_2'];
            unset($value['options']['datas_2']);
        } 		
				
		
        if($value['type']!='champ_outil' AND $champs_actifs[$objet.'_'.$value['options']['nom']]=='on'){
        	//Tester si obligatoire
            if($champs_actifs[$value['options']['nom'].'_obligatoire']=='on')$value['options']['obligatoire']='oui';
            
            //La valeur par défaut   
            $value['options']['defaut']=isset($defaut[$value['options']['nom']])?$defaut[$value['options']['nom']]:'';
            $champs[]=$value;
            }
        elseif(empty($champs_actifs))$champs[]=$value['options']['nom'];
        
     }
    return $champs;

}

function shop_champs_extras_nom($champs_actifs=array(),$champs_extras){
    $champs=array();
    
     foreach($champs_extras as $key=>$value){
        $champs[]=$value['options']['nom'];
     }
    return $champs;

}
// Retourne la liste des plugins type shop depuis champs_extras_nettoyes()
/*function noms_champs_extras_presents($champs_extras){
    $noms=array();
    
     foreach($champs_extras as $key=>$value){
        if(isset($value['options']['nom_2'])){
            $value['options']['nom']=$value['options']['nom_2'];
            unset($value['options']['nom_2']);
            }     
        $noms[$value['options']['nom']]=$value['options']['label'];
        }
    return $noms;
}*/

//Cherche les objets définis
function objets_champs_extras(){
    $champs_extras=charger_fonction('shop_champs_extras','inc');
    $champs_extras=$champs_extras();
    $objets=array();
    foreach($champs_extras AS $value) {
        $objets[]=$value['objet'];
        }
    return $objets;
}

//Cherche les définitions des tables
function definitions_sql_champs_extras($champs_actifs=array()){
    $champs_extras=charger_fonction('shop_champs_extras','inc');
    $champs_extras=$champs_extras();
    $tables=array();
    foreach($champs_extras AS $value) {
        foreach($value['saisies'] AS $f){
            if(isset($f['tables']))$tables=$f['tables'];
            }
        }
   
    return $tables;
}

//Retourne la définition des champs extras définis

function shop_champs_extras_definis(){
    include_spip('inc/shop');

    //Charger la définition des champs extras
    $champs_extras=charger_fonction('shop_champs_extras','inc');
    $champs_extras=$champs_extras(); 
    $champs=array();
    
    foreach($champs_extras as $key=>$value){
        $champs[]=shop_champs_extras_nom('',$value['saisies']);
          }

    return $champs;
}

function champs_reduits(){
     //Récupérer les champs extras choisis
    
    $c=shop_champs_extras_definis();

    //Déterminer les champs à afficher
    $champs_extras=array();
    foreach($c[0] AS $name){
       list($objet,$champ)=explode('-',$name);
            if($champ)$champs_extras[]=$champ;
        } 
    return $champs_extras;

    
}
