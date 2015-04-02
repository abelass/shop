<?php

if (!defined("_ECRIRE_INC_VERSION")) return;

// Retourne la liste des champs extras
function inc_shop_champs_extras_dist($defaut=array()){
    if(count($defaut)==0){
    	include_spip('inc/config');
    	$defaut=lire_config('shop',array());
	}
	

	$accepter_conditions_texte=isset($defaut['accepterconditions_texte'])?$defaut['accepterconditions_texte']:'';
	if($accepter_conditions_texte){
		include_spip('formulaires/selecteur/generique_fonctions');
		foreach(array('rubrique','article') AS $objet){
			$id_objet=picker_selected($accepter_conditions_texte,$objet);
			if($id=sql_getfetsel('id_'.$objet,'spip_'.$objet.'s','id_'.$objet.' IN ('.implode(',',$id_objet).')')){
				$url_cg=generer_url_entite($id,$objet);
				break;
			}
		}
	
	}
	if($url_cg)$url='<a href="'.$url_cg.'">'._T('shop:conditions_generales').'</a>';
	else $url=_T('shop:conditions_generales');
	
	
	
	
     $champs_extras=array(
        'commande'=>array(
            'saisie' => 'fieldset',
            'objet' => 'commande', //Objet concerné par les champs suivants
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
                    'tables'=>array(
                        'spip_commandes'=>array(
                            'field'=>array(
                                'commentaire'=>"text NOT NULL"
                                )
                             )
                        ),
                    'options' => array(
                        'li_class'=>'conditionnel',

                        'nom' => 'commande_commentaire',// Pour la configuration
                        'nom_2' => 'commentaire',// Pour le formulaire public                          
                        'label' => _T('shop:label_commentaire'),
                        'defaut' => isset($defaut['commande_commentaire'])?$defaut['commande_commentaire']:'',

                    )
                ),
                array(
                    'saisie' => 'oui_non',
                    'formulaires' => array('configuer_shop'),
                    'options' => array(
                        'li_class'=>'conditionnel last',
                        'nom' => 'commentaire_obligatoire',
                        'label' => _T('saisies:option_obligatoire_label'),
                        'defaut' => isset($defaut['commentaire_obligatoire'])?$defaut['commentaire_obligatoire']:'',
                        'afficher_si' => '@commande_commentaire@ == "on"',
                    )
                ), 
 				array(
                    'saisie' => 'oui_non',// Pour la configuration              
                    'formulaires' => array('configuer_shop','editer_client'),
                    'tables'=>array(
                        'spip_commandes'=>array(
                            'field'=>array(
                                'accepter_conditions'=>"varchar(2) NOT NULL"
                                )
                             )
                        ),
                    'options' => array(
                        'li_class'=>'conditionnel',                     
                        'nom' => 'commande_accepterconditions',// Pour la configuration
                        'nom_2' => 'accepterconditions',// Pour le formulaire public                          
                        'label' => _T('shop:label_accepter_conditions'),// Pour la configuration
                        'label_2' => _T('shop:label_accepter_conditions_public',array('url'=>$url)),// Pour le formulaire public                           
                        'defaut' => isset($defaut['commande_accepterconditions'])?$defaut['commande_accepterconditions']:'',
                        'defaut_2' => '',
                                                
                    )
                ),
                array(
                    'saisie' => 'oui_non',
                    'formulaires' => array('configuer_shop'),
                    'options' => array(
                        'li_class'=>'conditionnel last',
                        'nom' => 'accepterconditions_obligatoire',
                        'label' => _T('saisies:option_obligatoire_label'),
                        'defaut' => isset($defaut['accepterconditions_obligatoire'])?$defaut['accepterconditions_obligatoire']:'',
                        'afficher_si' => '@commande_accepterconditions@ == "on"',
                    )
                ),
                 array(
                    'saisie' => 'selecteur_rubrique_article',
                    'formulaires' => array('configuer_shop'),
                    'options' => array(
                        'li_class'=>'conditionnel last',
                        'nom' => 'accepterconditions_texte',
                        'label' => _T('shop:label_accepter_conditions_texte'),
                        'defaut' => $accepter_conditions_texte,
                        'afficher_si' => '@commande_accepterconditions@ == "on"',
                    )
                )            
            )
        ),
        array(
            'saisie' => 'fieldset',
            'options' => array(
                'nom' => 'authentification',
                'label' => _T('shop:cfg_titre_authentification'),            
            ),
            'saisies' => array(
                            array(
                    'saisie' => 'checkbox',
                    'formulaires' => array('configuer_shop'),
                    'options' => array(
                        'class'=>'float_right',
                        'datas'=>array('on'=>_T('item_oui')),
                        'nom' => 'authentification_automatique',
                'label' => _T('shop:cfg_titre_authentification_automatique'),
                'explication' => _T('shop:explication_authentification_automatique'), 
                        'defaut' => isset($defaut['authentification_automatique'])?$defaut['authentification_automatique']:'',
                    )
                ), 
            )
                                           
          )
       );
	   

	$shop_champs_extras =pipeline('shop_champs_extras',$champs_extras);
	$champs_extras=array_merge($champs_extras,$shop_champs_extras);
	
    return $champs_extras;
}

?>