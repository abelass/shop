<?php
                               
// Sécurité
if (!defined('_ECRIRE_INC_VERSION')) return;
include_spip('inc/config');

function formulaires_configurer_shop_saisies_dist($retour=''){

$config=lire_config('shop',array());

    return array(
        array(
            'saisie' => 'fieldset',
            'options' => array(
                'nom' => 'champs_extras',
                'label' => _T('shop:cfg_titre_champs_commandes')
            ),
            'saisies' => array(
                array(
                    'saisie' => 'explication',
                    'options' => array(
                        'nom' => 'exp1',
                        'texte' => _T('shop:explication_champs_commandes')
                    )
                ),
                array(
                    'saisie' => 'oui_non',
                    'options' => array(
                        
                        'nom' => 'commentaire',
                        'label' => _T('shop:label_commentaire'),
                        'defaut' => $config['commentaire']
                    )
                ),
                array(
                    'saisie' => 'checkbox',
                    'options' => array(
                        'class'=>'float_right',
                        'datas'=>array('on'=>_T('item_oui')),
                        'nom' => 'commentaire_obligatoire',
                        'label' => _T('saisies:option_obligatoire_label'),
                        'defaut' => $config['commentaire_obligatoire'],
                        'afficher_si' => '@commentaire@ == "on"',
                    )
                ), 
                
                               
            )
        ),  

    );
}

?>
