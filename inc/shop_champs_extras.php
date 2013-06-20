<?php

if (!defined("_ECRIRE_INC_VERSION")) return;

// Retourne la liste des plugins type shop
function inc_shop_champs_extras_dist($defaut=array()){

     $champs_extras=array(
        array(
            'saisie' => 'fieldset',
            'objet' => 'commande', //Objet concerné par ce champ
            'options' => array(
                'nom' => 'champs_extras',
                'label' => _T('shop:cfg_titre_champs_commandes')
            ),
            'saisies' => array(
                array(
                    'type'=>'champ_outil', //Permet de distinguer les champs normaux de ceux qui servent à construire le formulaire
                    'saisie' => 'explication',//type de saisie 
                     'formulaires' => array('configuer_shop'), //Les formulaires ou les champs sont insérés, pas utilisé pour le moment
                    'options' => array(
                        'nom' => 'exp1',
                        'texte' => _T('shop:explication_champs_commandes')
                    )
                ),
                array(
                    'saisie' => 'oui_non',// Pour la configuration
                    'saisie_2' => 'textarea', // Pour le formulaire public                   
                    'formulaires' => array('configuer_shop','editer_client'),
                    'options' => array(
                        'nom' => 'commande_commentaire',// Pour la configuration
                        'nom_2' => 'commentaire',// Pour le formulaire public                          
                        'label' => _T('shop:label_commentaire'),
                        'defaut' => $defaut['commande_commentaire']
                    )
                ),
                array(
                    'saisie' => 'checkbox',
                    'formulaires' => array('configuer_shop'),
                    'options' => array(
                        'class'=>'float_right',
                        'datas'=>array('on'=>_T('item_oui')),
                        'nom' => 'commande_commentaire_obligatoire',
                        'label' => _T('saisies:option_obligatoire_label'),
                        'defaut' => $defaut['commande_commentaire_obligatoire'],
                        'afficher_si' => '@commande_commentaire@ == "on"',
                    )
                ), 
              
              )
                               
            )
        );
     /*Possibilité de rajouter de plugin ou de modifier leur définition*/
    $champs_extras =pipeline('shop_champs_extras',$champs_extras);

    
    return $champs_extras;
}

?>