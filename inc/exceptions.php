<?php

if (!defined("_ECRIRE_INC_VERSION")) return;

/*Les exceptions*/
function inc_exceptions_dist($filtrer=''){
    $exceptions=array(
        'objet'=>array('site'=>'syndic','syndic'=>'syndic'),
        'titre'=>array(
            'auteur'=>'nom',
            'site'=>'nom_site',
            'syndic'=>'nom_site',            
            ),
        );
    $retour=$exceptions;   
    if($filtrer)$retour=$exceptions[$filtrer];
    return $retour;
}

?>