<?php

if (!defined("_ECRIRE_INC_VERSION")) return;

// Retourne la liste des plugins type shop
function inc_objets_shop_dist(){
    
    /* On cherche d'abord ceux qui on contienn un shp_dan leur nom*/
    $sql=sql_select('prefixe','spip_paquets','installe="oui"');
    
    $objets_shop=array(
        'configurer_shop'=>array(
            'action'=>'configurer_shop',
            'nom_action'=>_T('spip:icone_configuration_site'),
            'icone'=>'cfg-16.png'
            ),
         'commandes'=>array(
            'action'=>'commandes',
            'nom_action'=>_T('commandes:commandes_titre'),
            'icone'=>'commande-16.png'
            ),
        );

    
    while($data=sql_fetch($sql)){
        $plugin=strtolower($data['prefixe']);
        
        $pattern = '/shop_/';

        if(preg_match($pattern,$plugin)){
            $plugin_objet= preg_replace($pattern,'',$plugin);
            $plugin_objet_sing=substr($plugin_objet,0,strlen($plugin_objet)-1);
            $objets_shop[$plugin_objet]=array(
            'action'=>$plugin_objet,
            'objet'=>$plugin_objet,
            'nom_action'=>_T($plugin_objet_sing.':titre_'.$plugin_objet),
            'nom_objet'=>_T($plugin_objet_sing.':titre_'.$plugin_objet_sing),
            'icone'=>$plugin_objet_sing.'-16.png'                      
            );

           unset($plugin_objet);  
            }
        }
    
   
     /*Possibilité de rajouter de plugin ou de modifier leur définition*/
    $objets_shop=pipeline('shop_objets',$objets_shop);

    
    return $objets_shop;
}

?>