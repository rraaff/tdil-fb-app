<?php
/**
 * Copyright 2011 Facebook, Inc.
 *
 * Licensed under the Apache License, Version 2.0 (the "License"); you may
 * not use this file except in compliance with the License. You may obtain
 * a copy of the License at
 *
 *     http://www.apache.org/licenses/LICENSE-2.0
 *
 * Unless required by applicable law or agreed to in writing, software
 * distributed under the License is distributed on an "AS IS" BASIS, WITHOUT
 * WARRANTIES OR CONDITIONS OF ANY KIND, either express or implied. See the
 * License for the specific language governing permissions and limitations
 * under the License.
 */


require '../include/facebook.php';
Facebook::$CURL_OPTS[CURLOPT_CAINFO] = '/var/www/TDIL-FB-APP/include/fb_ca_chain_bundle.crt';

// Create our Application instance (replace this with your appId and secret).
$facebook = new Facebook(array(
  'appId'  => '292861170783253',
  'secret' => '822b60809737ff91e6142f924e85e9d5',
));

// Get User ID
$user = $facebook->getUser();

// We may or may not have this data based on whether the user is logged in.
//
// If we have a $user id here, it means we know the user is logged into
// Facebook, but we don't know if the access token is valid. An access
// token is invalid if the user logged out of Facebook.

if ($user) {
  try {
    // Proceed knowing you have a logged in user who's authenticated.
    $user_profile = $facebook->api('/me');
  } catch (FacebookApiException $e) {
  	print_r($e);
    error_log($e);
    $user = null;
  }
}

$signed_request = $facebook->getSignedRequest();
$fbid = $signed_request['user_id'];
$page_id = $signed_request["page"]["id"]; /*TODO Limitar a una pagina cuanto este productivo*/
echo $page_id;echo '<br>';
echo $page_id;echo '<br>';
$likes = $signed_request["page"]["liked"]; /*TODO Limitar a una pagina cuanto este productivo*/
echo $likes; '<br>';
$app_data = $signed_request["app_data"];
echo $app_data;echo '<br>';

print_r($signed_request["page"]);

if ($user) {
	try {
		// Proceed knowing you have a logged in user who's authenticated.
		$user_profile = $facebook->api('/me');
	} catch (FacebookApiException $e) {
		/* TODO aca va un die */
		error_log($e);
		$user = null;
	}
} else {
	/* TODO aca va un die */
}
$fbemail = '';
$fbname = $user_profile['name'];
echo $fbname;echo '<br>';
$fbusername = $user_profile['username'];
echo $fbusername;echo '<br>';
$fbgender = $user_profile['gender'];
echo $fbgender;echo '<br>';
echo $fan;echo '<br>';

// Login or logout url will be needed depending on current user state.
if ($user) {
  $logoutUrl = $facebook->getLogoutUrl();
} else {
  $loginUrl = $facebook->getLoginUrl();
}

// This call will always work since we are fetching public data.

?>
<!doctype html>
<html xmlns:fb="http://www.facebook.com/2008/fbml">
  <head>
    <title>php-sdk</title>
    <style>
      body {
        font-family: 'Lucida Grande', Verdana, Arial, sans-serif;
      }
      h1 a {
        text-decoration: none;
        color: #3b5998;
      }
      h1 a:hover {
        text-decoration: underline;
      }
    </style>
  </head>
  <body>
    <h1>php-sdk</h1>
    <?php if ($user): ?>
      <a href="<?php echo $logoutUrl; ?>">Logout</a>
    <?php else: ?>
      <div>
        Login using OAuth 2.0 handled by the PHP SDK:
        <a href="<?php echo $loginUrl; ?>">Login with Facebook</a>
      </div>
    <?php endif ?>

    <h3>App data</h3>
	<?php echo $app_data; ?>
    
    <h3>PHP Session</h3>
    <pre><?php print_r($_SESSION); ?></pre>

    <?php if ($user): ?>
      <h3>You</h3>
      <img src="https://graph.facebook.com/<?php echo $user; ?>/picture">

      <h3>Your User Object (/me)</h3>
      <pre><?php print_r($user_profile); ?></pre>
    <?php else: ?>
      <strong><em>You are not Connected.</em></strong>
    <?php endif ?>

    <h3>Public profile of Naitik</h3>
    <img src="https://graph.facebook.com/naitik/picture">
    <?php echo $naitik['name']; ?>
  </body>
</html>
