<?php 
	Facebook::$CURL_OPTS[CURLOPT_CAINFO] = '/var/www/TDIL-FB-APP/include/fb_ca_chain_bundle.crt';
	
	$APPLICATION_ID = '292861170783253';
	$APPLICATION_SECRET = '822b60809737ff91e6142f924e85e9d5';
	$PAGEID = '';
	$PAGE_NAME = 'tdil.test.page';
	$APPLICATION_URL = 'http://localhost/TDIL-FB-APP/app1';
	
	define('INVITATION_DELAY',1);
	define('APP1_WIN',10);
	
	function get_app_access($appId, $appSecret) {
		$token_url =    "https://graph.facebook.com/oauth/access_token?" .
				"client_id=" . $appId .
				"&client_secret=" . $appSecret .
				"&grant_type=client_credentials";
		$token = file_get_contents($token_url);
		return $token;
	}
	
	
?>