<?php
                               
// Sécurité
if (!defined('_ECRIRE_INC_VERSION')) return;

function formulaires_configurer_shop_saisies_dist($retour=''){
    
    include_spip('inc/config');
    $config=lire_config('shop',array());
    
    //Charger la définition des champs extras
    $champs_extras=charger_fonction('shop_champs_extras','inc');
    $champs_extras=$champs_extras($config);    
    
    return $champs_extras;
}

?>
