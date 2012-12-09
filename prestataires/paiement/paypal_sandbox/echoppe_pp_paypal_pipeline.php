<?php

function echoppe_pp_paypal_descriptif($flux){
	
	$info = array();
	
	$info['nom'] = "PayPal Sandbox";
	$info['descriptif'] = "Logique de création des formulaire de paiement paypal";
	$info['logo'] = "https://developer.paypal.com/en_US/i/logo/new_logo_ic.gif";
	$info['url'] = "https://developer.paypal.com/";
	$info['prefix'] = "paypal_sandbox";
	$info['version'] = "0.1";
	$info['avertissement_user'] = "<multi>[fr]En utilisant ce prestataire de paiement, vous acceptez ses conditions g&eacute;n&eacute;rales d'utilisation[en]Warning, this is a </multi>";
	
	$flux[] = $info;
	
	
	return $flux;
	
}


?>
