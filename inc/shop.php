<?php

if (!defined("_ECRIRE_INC_VERSION")) return;

//Enlèves les champs utilitaires et remplace saisie par saisie_2 et nom_2 par nom et teste si obligatoire
function shop_champs_extras_nettoyes($champs_actifs,$champs_extras,$objet,$defaut=array(),$form=''){
    $champs=array();
    
     foreach($champs_extras as $key=>$value){
        if(isset($value['options']['nom_2'])){
            $value['options']['nom']=$value['options']['nom_2'];
            unset($value['options']['nom_2']);
        } 

        if($value['type']!='champ_outil' AND $champs_actifs[$objet.'_'.$value['options']['nom']]=='on'){
            if(isset($value['saisie_2'])){
                $value['saisie']=$value['saisie_2'];
                unset($value['saisie_2']);
            }
            if($champs_actifs[$objet.'_'.$value['options']['nom'].'_obligatoire'][0]=='on')$value['options']['obligatoire']='oui';
            
            //La valeur par défaut   
            $value['options']['defaut']=isset($defaut[$value['options']['nom']])?$defaut[$value['options']['nom']]:'';
            $champs[]=$value;
            }
        }
    return $champs;
}

// Retourne la liste des plugins type shop depuis un tableau nettoyé- passé par shop_champs_extras_nettoyes()
function noms_champs_extras_presents($champs_extras){
    $noms=array();
    
     foreach($champs_extras as $key=>$value){
        if(isset($value['options']['nom_2'])){
            $value['options']['nom']=$value['options']['nom_2'];
            unset($value['options']['nom_2']);
            }     
        $noms[$value['options']['nom']]=$value['options']['label'];
        }
    return $noms;
}


function objets_champs_extras($champs_actifs=array()){
    $champs_extras=charger_fonction('shop_champs_extras','inc');
    $champs_extras=$champs_extras();
    $objets=array();
    foreach($champs_extras AS $value) {
        $objets[]=$value['objet'];
        }
    
    return $objets;
}
