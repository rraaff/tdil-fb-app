<?php 
	Facebook::$CURL_OPTS[CURLOPT_CAINFO] = 'C:/xampp/htdocs/TDIL-FB-APP/include/fb_ca_chain_bundle.crt'; // path al certificado
	
	define('APPLICATION1_ID','292861170783253'); // id de la app
	define('APPLICATION1_SECRET','822b60809737ff91e6142f924e85e9d5'); // secret id de la app
	$PAGEID = '';
	define('PAGE_NAME','tdil.test.page'); // nombre de la pagina en la cual la app esta instalada
	define('APPLICATION1_URL','http://localhost/TDIL-FB-APP/app1'); // Url de la aplicacion, debe ser https
	
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