<?php

/***************************************************************************\
 *  SPIP, Systeme de publication pour l'internet                           *
 *                                                                         *
 *  Copyright (c) 2001-2012                                                *
 *  Arnaud Martin, Antoine Pitrou, Philippe Riviere, Emmanuel Saint-James  *
 *                                                                         *
 *  Ce programme est un logiciel libre distribue sous licence GNU/GPL.     *
 *  Pour plus de details voir le fichier COPYING.txt ou l'aide en ligne.   *
\***************************************************************************/

if (!defined('_ECRIRE_INC_VERSION')) return;

include_spip('inc/editer');
include_spip('inc/autoriser');
include_spip('inc/puce_statut');
include_spip('prive/formulaires/instituer_objet');


/**
 * Charger #FORMULAIRE_INSTITUER_COMMANDE
 * @param int $id_commande
 * @param string $retour
 * @return array|bool
 */
function formulaires_instituer_commande_charger_dist($id_commande,$retour=""){
	$editable = true;
	$table = table_objet_sql('commande');
	$desc = lister_tables_objets_sql($table);

	if (!isset($desc['statut_textes_instituer']))
		return false;
	
	if (!autoriser('modifier', 'commande', $id_commande))
		$editable = false;

	// charger le contenu de l'objet
	// dont son champ statut
	$v = formulaires_editer_objet_charger('commande',$id_commande,0,0,'','');

	$publiable = true;
	$statuts = lister_statuts_proposes($desc);
	// tester si on a le droit de publier, si un statut publie existe
	if (isset($statuts['publie'])){
		if (!autoriser('instituer', 'commande', $id_commande, null, array('statut'=>'publie'))){
			if ($v['statut'] == 'publie')
				$editable = false;
			else
				$publiable = false;
		}
	}
	$valeurs = array(
		'editable' => $editable,
		'statut' => $v['statut'],
		'_id_commande' => $id_commande,
		'_statuts' => lister_statuts_proposes($desc, $editable?$publiable:true),
		'_publiable' => $publiable,
		'_label' => isset($desc['texte_changer_statut'])?$desc['texte_changer_statut']:'texte_article_statut',
		'_aide' => isset($desc['aide_changer_statut'])?$desc['aide_changer_statut']:'',
		'_hidden' => "<input type='hidden' name='statut_old' value='".$v['statut']."' />",
	);

	#if (!count($valeurs['statuts']))
	return $valeurs;
}

/**
 * Verifier #FORMULAIRE_INSTITUER_COMMANDE
 * @param int $id_commande
 * @param string $retour
 * @return array
 */
function formulaires_instituer_commande_verifier_dist($id_commande,$retour=""){
	$erreurs = array();
	// charger le contenu de la commande
	// dont son champ statut
	$v = formulaires_editer_objet_charger('commande',$id_commande,0,0,'','');

	if ($v['statut']!==_request('statut_old'))
		$erreurs['statut'] = _T('instituer_erreur_statut_a_change');
	else {
		$table = table_objet_sql('commande');
		$desc = lister_tables_objets_sql($table);

		$publiable = true;
		if (isset($v['id_rubrique'])
			AND !autoriser('publierdans', 'rubrique', $v['id_rubrique'])) {
			$publiable = false;
		}
		$l = lister_statuts_proposes($desc, $publiable);
		$statut = _request('statut');
		if (!isset($l[$statut])
		  OR !autoriser('instituer','commande',$id_commande,'',array('statut'=>$statut)))
			$erreurs['statut'] = _T('instituer_erreur_statut_non_autorise');
	}

	return $erreurs;
}

/**
 * Traiter #FORMULAIRE_INSTITUER_COMMANDE
 * @param int $id_commande
 * @param string $retour
 * @return array
 */
function formulaires_instituer_commande_traiter_dist($id_commande,$retour=""){


    $action = charger_fonction('instituer_commande', 'action',true);
    $action($id_commande.'-'._request('statut'));


	$res = array('message_ok'=>_T('info_modification_enregistree'));


	return $res;
}
