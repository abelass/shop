<?php
if (!defined("_ECRIRE_INC_VERSION")) return;


function shop_upgrade($nom_meta_base_version,$version_cible){
        $current_version = 0.0;
        if ((!isset($GLOBALS['meta'][$nom_meta_base_version]))
        || (($current_version = $GLOBALS['meta'][$nom_meta_base_version])!=$version_cible)){
                include_spip('base/shop_tables_principales');
                include_spip('base/shop_tables_auxiliaires');
                // cas d'une installation
                if ($version_cible > $GLOBALS['meta'][$nom_meta_base_version]){
                	if($GLOBALS['meta'][$nom_meta_base_version]==''){
				include_spip('base/create');
				creer_base();
				maj_tables('spip_shop_prix');	
				maj_tables('spip_shop_commande');									
				maj_tables('spip_shop_tokens_retour');																	
				}
			else{
				include_spip('base/create');
				creer_base();
				maj_tables('spip_shop_prix');	
				maj_tables('spip_shop_commande');	
				}
               }

        }
	ecrire_meta($nom_meta_base_version, $current_version=$version_cible);
	ecrire_metas();        
}

?>
