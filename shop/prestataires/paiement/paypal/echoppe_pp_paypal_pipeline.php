<?php

function echoppe_pp_paypal_descriptif($flux){
	
	$info = array();
	
	$info['nom'] = "PayPal";
	$info['descriptif'] = "Logique de création des formulaire de paiement paypal";
	$info['logo'] = "https://www.paypal.com/fr_FR/i/logo/paypal_logo.gif";
	$info['url'] = "https://www.paypal.com/";
	$info['prefix'] = "paypal";
	$info['version'] = "0.1";
	$info['avertissement_user'] = "<multi>[fr]En utilisant ce prestataire de paiement, vous acceptez ses conditions g&eacute;n&eacute;rales d'utilisation[en]By using this payment system you accept its general conditions</multi>";
	
	$flux[] = $info;
	
	
	return $flux;
	
}


?>
