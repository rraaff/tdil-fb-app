<?php 
	Facebook::$CURL_OPTS[CURLOPT_CAINFO] = '/var/www/TDIL-FB-APP/include/fb_ca_chain_bundle.crt'; // path al certificado
	
	define('APPLICATION2_ID','373863869313904'); // id de la app
	define('APPLICATION2_SECRET','b288c31fd46a3252bc96c077505008bc'); // secret id de la app
	$PAGEID = '';
	define('PAGE_NAME','tdil.test.page'); // nombre de la pagina en la cual la app esta instalada
	define('APPLICATION2_URL','http://localhost/TDIL-FB-APP/app2'); // Url de la aplicacion, debe ser https
	
	define('APP1_WIN',10); // Numero de fans para ganar
	
	
	
	function get_app_access($appId, $appSecret) {
		$token_url =    "https://graph.facebook.com/oauth/access_token?" .
				"client_id=" . $appId .
				"&client_secret=" . $appSecret .
				"&grant_type=client_credentials";
		$token = file_get_contents($token_url);
		return $token;
	}
	
	
?>