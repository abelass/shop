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

function formulaires_configurer_shop_traiter_dist(){
    include_spip('inc/config');
    $config = array();

    $res = array('editable'=>true);

    include_spip('inc/shop');
    $champs=shop_champs_extras_definis();

    foreach ($champs[0] as $k) {
           $config[$k] =  _request($k);
        }
    ecrire_meta('shop',serialize($config));
    $res['message_ok'] = _T('config_info_enregistree');


     //Installation des champs choisis
    include_spip('base/create');    

    //Récupérer les champs extras choisis
    include_spip('inc/config');
    $config=lire_config('shop');
    //echo serialize($config);
    //Déterminer les champs choisis
    include_spip('inc/shop');
    $objets_possibles=objets_champs_extras();
    $tables=array();
    foreach($objets_possibles AS $objet){
        $tables[]='spip_'.$objet.'s';
        }
    maj_tables($tables);

    return $res;
}


?>
