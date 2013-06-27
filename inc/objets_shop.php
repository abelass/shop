<?php

if (!defined("_ECRIRE_INC_VERSION")) return;

// Retourne la liste des plugins type shop
function inc_objets_shop_dist(){

    
    $objets_shop=array(
        'configurer_shop'=>array(
            'action'=>'configurer_shop',
            'nom_action'=>_T('spip:icone_configuration_site'),
            'icone'=>'cfg-16.png',
            'configurer'=>array(
                'titre'=>_T('shop:shop'),
                'chemin'=>'prive/squelettes/contenu/inc-configurer_shop')            
            ),
         'commandes'=>array(
            'action'=>'commandes',
            'nom_action'=>_T('commandes:commandes_titre'),
            'icone'=>'commande-16.png',
            'configurer'=>array(
                'titre'=>_T('commandes:commandes_titre'),
                'chemin'=>'prive/squelettes/contenu/configurer_commandes')
            ),
         'clients'=>array(
            'configurer'=>array(
                'titre'=>_T('clients:clients_titre'),
                'chemin'=>'prive/exec/configurer_clients')
            ),               
         'contacts'=>array(
            'configurer'=>array(
                'titre'=>_T('contacts:contact'),
                'chemin'=>'prive/squelettes/contenu/configurer_contacts')
            ), 
         'paniers'=>array(
            'configurer'=>array(
                'titre'=>_T('paniers:titre_panier'),
                'chemin'=>'prive/squelettes/contenu/configurer_paniers')
            ),   
         'coordonnes'=>array(
            'configurer'=>array(
                'titre'=>_T('coordonnees:titre_coordonnees'),
                'chemin'=>'prive/exec/configurer_coordonnees')
            ),
         'paypal'=>array(
            'configurer'=>array(
                'titre'=>_T('paypal:configuration_paypal'),
                'chemin'=>'prive/exec/configurer_paypal'
                )
            ),                                                            
        );
    
    /* On cherche d'abord ceux qui on contiennent un shop_dan leur nom*/
    $sql=sql_select('prefixe','spip_paquets',"actif='oui' AND prefixe LIKE '%shop_%'");
    
    while($data=sql_fetch($sql)){
        $plugin=strtolower($data['prefixe']);
        $pattern = '/shop_/';
        $plugin_objet= preg_replace($pattern,'',$plugin);
        $plugin_objet_sing=substr($plugin_objet,0,strlen($plugin_objet)-1);
        $objets_shop[$plugin_objet]=array(
        'action'=>$plugin_objet,
        'objet'=>$plugin_objet,
        'nom_action'=>_T($plugin_objet_sing.':titre_'.$plugin_objet),
        'nom_objet'=>_T($plugin_objet_sing.':titre_'.$plugin_objet_sing),
        'icone'=>$plugin_objet_sing.'-16.png'                      
        );
         if(find_in_path('configurer_'.$plugin.'.html','prive/squelettes/contenu/')){
             if($plugin_objet=='prix'){
                 $plugin_objet='shopprix';
                 $plugin_objet_sing=$plugin_objet;
             }
             $objets_shop[$plugin_objet]['configurer']=array(
            'titre' =>_T($plugin_objet_sing.':titre_'.$plugin_objet),
            'chemin'=>'prive/squelettes/contenu/configurer_'.$plugin); 
            }
       unset($plugin_objet);  
            
        }

     /*Possibilité de rajouter de plugin ou de modifier leur définition*/
    $objets_shop=pipeline('shop_objets',$objets_shop);

    
    return $objets_shop;
}

?>