<?php
if (!defined('_ECRIRE_INC_VERSION'))
  return;

//Génère les donnés de l'objet
function generer_objet_details($id_objet, $objet = 'article', $env = array(), $fichier = 'inclure/objet_precieux_detail') {
  include_spip('inc/pipelines_ecrire');
  include_spip('inc/utils');

  $ancien_objet = $objet;
  $e = trouver_objet_exec($objet);
  $objet = $e['type'];
  $id_table_objet = $e['id_table_objet'];
  // Pour les récalcitrants
  if (!$objet) {
    $objet = $ancien_objet;
    $id_table_objet = 'id_' . $objet;
  }
  $table = table_objet_sql($objet);

  $where = $id_table_objet . '=' . $id_objet;
  if (!$contexte = sql_fetsel('*', $table, $where))
    $contexte = array();

  //Filtrer les champs vides
  foreach ($env as $k => $v) {
    if (!$v)
      unset($env[$k]);
  }

  if (!$cont = calculer_contexte())
    $cont = array();
  $contexte = array_merge($cont, $contexte, $env);

  $contexte['objet'] = $objet;
  $contexte['id_objet'] = $id_objet;

  //déterminer le titre
  if (!$contexte['titre'])
    $contexte['titre'] = $titre = generer_info_entite($id_objet, $objet, 'titre');;

  //Chercher le logo correspondant
  //Si il y a un logo Selection Objet
  $chercher_logo = charger_fonction('chercher_logo', 'inc');
  $logo = $chercher_logo($contexte['id_selection_objet'], 'id_selection_objet', 'on');
  //sinon le logo de l'objet sélectionné

  $_id_objet = id_table_objet($objet);
  $logo = $chercher_logo($id_objet, $_id_objet, 'on');
  $contexte['logo_objet'] = $logo[0];

  $fond = recuperer_fond($fichier, $contexte);

  return $fond;
}
