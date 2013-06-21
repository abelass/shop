<?php

if (!defined("_ECRIRE_INC_VERSION")) return;

// Retourne la liste des plugins type shop
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
