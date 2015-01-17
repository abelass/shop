<?php
if (!defined('_ECRIRE_INC_VERSION')) return;

//Génère les donnés de l'objet
function generer_objet_details($id_objet,$objet='article',$env=array(),$fichier='inclure/objet_precieux_detail'){
    include_spip('inc/pipelines_ecrire');
    include_spip('inc/utils');
    

    $ancien_objet=$objet;
    $e = trouver_objet_exec($objet);
    $objet=$e['type'];
    $id_table_objet=$e['id_table_objet'];
    // Pour les récalcitrants
    if(!$objet){
           $objet=$ancien_objet;
           $id_table_objet='id_'.$objet;
        }
    $table = table_objet_sql($objet);  

    $where=$id_table_objet.'='.$id_objet;
    if(!$contexte=sql_fetsel('*',$table,$where))$contexte=array();
          

    //Filtrer les champs vides
    foreach($env as $k=>$v){
        if(!$v)unset($env[$k]);
    }
    
   	if(!$cont=calculer_contexte())$cont=array();
   	$contexte= array_merge($cont,$contexte);

    $contexte['objet']=$objet;
    $contexte['id_objet']=$id_objet;
    
    
    
     //déterminer le titre
    if(!$contexte['titre'])$contexte['titre']=titre_objet_shop($objet,$contexte);

    
    //Chercher le logo correspondant
    //Si il y a un logo Selection Objet
    $chercher_logo = charger_fonction('chercher_logo', 'inc');
    $logo=$chercher_logo($contexte['id_selection_objet'],'id_selection_objet','on');
    //sinon le logo de l'objet sélectionné
    
    $_id_objet=id_table_objet($objet);        
    $logo=$chercher_logo($id_objet,$_id_objet,'on');
    $contexte['logo_objet']=$logo[0];
     
    $fond=recuperer_fond($fichier,$contexte,array('ajax'=>'objet_precieux_'.$contexte['id_objet_prix']));
    
    return $fond;
}

/*Etablit le titre de l'objet*/
function titre_objet_shop($objet,$contexte){

    $exceptions=charger_fonction('exceptions','inc');
    $exception_titre=array('auteur'=>'nom','site'=>'nom_site','syndic'=>'nom_site');
	
    //Les exceptions du titre
    if(!$titre=$contexte[$exception_titre[$objet]] and isset($contexte['titre']))$titre=$contexte['titre'];
	else{
        if($objet=='document'){
            $f=explode('/',$contexte['fichier']);
            $titre=$f[1];
            }
		// A vérifier
        else{
            $table_sql = table_objet_sql($objet);
            $tables=lister_tables_objets_sql();
            $titre_objet=_T($tables[$table_sql]['texte_objet']);
            if(isset($contexte['id_objet']))$id=$contexte['id_objet'];
            if($objet='selection_objet' AND isset($contexte['id_selection_objet']))$id=$contexte['id_selection_objet'];
           $titre=$titre_objet.' '.$id; 
        }    
    }
    return $titre;
}