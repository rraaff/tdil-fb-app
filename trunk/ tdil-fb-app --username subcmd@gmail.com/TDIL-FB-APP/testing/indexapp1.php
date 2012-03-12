<?php
// PATH TO YOUR FACEBOOK PHP-SDK
	require '../include/facebook.php';


// REPLACE WITH YOUR APPLICATION ID AND SECRET
$facebook = new Facebook(array(
  'appId'  => '292861170783253',
  'secret' => '822b60809737ff91e6142f924e85e9d5',
  'cookie' => true,
));

function getAppAccessToken($fb) {
	$access_token_url = "https://graph.facebook.com/oauth/access_token"; 
	$parameters	= "grant_type=client_credentials&client_id=" . $fb->getAppId() . "&client_secret=" . $fb->getApiSecret();
	return file_get_contents($access_token_url . "?" . $parameters);
}

// GET Test Users
function getTestAccounts($fb, $a) {
	$accounts = $fb->api("/{$fb->getAppId()}/accounts/test-users?$a");
	if( isset($accounts['data']) )
		return $accounts;
	else
		return null;
}

// CREATE Test User
function createTestUser($fb, $a) {
	$params = array();
	$a = explode("=",$a);
	$params['access_token'] = $a[1];
	if( isset($_GET['installed']) && $_GET['installed'] == 'false' )
		$params['installed'] = 'false';
	if( isset($_GET['perms']) ) {
		$perms = trim($_GET['perms']);
		$params['permissions'] = $perms;
	}	
	$fb->api("/{$fb->getAppId()}/accounts/test-users", "POST", $params);
}

// DELETE Test User
function deleteTestUser($fb, $id, $a) {
	$fb->api("/$id?$a", "DELETE");
}

/////////////////////// \\\\\\\\\\\\\\\\\\\\\\\
function printTestUsers($accounts) {
	$html = "";
	if(isset($accounts['data']) && count($accounts['data'])) {
		$html .= "<table>";
		$html .= "<tr class=\"head\"><td colspan=\"4\">Test Users Table</td></tr>";
		$html .= "<tr class=\"head\"><td>Test User ID</td><td>Application User</td><td>Login URL</td><td>Delete</td></tr>";
		foreach($accounts['data'] as $arr) {
			$html .= "<tr>";
			$html .= "<td>{$arr['id']}</td>";
			$html .= "<td>" . ((empty($arr['access_token'])) ? "NO":"YES") . "</td>";
			$html .= "<td><a href=\"{$arr['login_url']}\" target=\"_blank\">Test User Login</a></td>";
			$html .= "<td><a href=\"{$_SERVER['PHP_SELF']}?id={$arr['id']}&action=delete\">Delete Test User</a></td>";
			$html .= "</tr>";
		}
		$html .= "</table>";
	} else {
		$html = "No users or something went wrong!";
	}
	return $html;
}

$app_access_token = getAppAccessToken($facebook);

// PROCESS ACTIONS
if( isset($_GET['action']) ) {
	if( isset($_GET['id']) && $_GET['action'] == 'delete' ) {
		deleteTestUser($facebook,$_GET['id'], $app_access_token);
	}
	if( $_GET['action'] == 'create' ) {
		createTestUser($facebook, $app_access_token);
	}
}

$acc = getTestAccounts($facebook, $app_access_token);
?>
<!doctype html>
<html>
<head>
	<title>Test Users</title>
	<link href="style.css" media="screen" type="text/css" rel="stylesheet">
</head>
<body>
<div id="wrapper">
	<div id="header">
		<h1><a href="<?php echo $_SERVER['PHP_SELF'] ?>">Test Users Panel</a></h1>
	</div>
	<div id="content">
		<?php echo printTestUsers($acc); ?>
		<br />
		<div>
			<form class="cmxform" action="<?php echo $_SERVER['PHP_SELF'] ?>" method="GET">
				<fieldset>
				<legend>Create test user:</legend>
				<ol>
				
				<input type="hidden" name="action" value="create" />
				<li>
					<label>Permissions:</label>
					<input type="text" name="perms" value="" />
				</li>
				
				<li>
				<label>Installed:</label>
				<input type="radio" name="installed" value="true" checked="checked" /> Yes <input type="radio" name="installed" value="false" /> No
				</li>

				</ol>
				</fieldset>
				<p><input type="submit" value="Create Test User" /></p>
			</form>
		</div>
	</div>
	<div id="footer">
		<p>&copy; 2011 <a href="http://www.masteringapi.com/">MasteringAPI.com</a></p>
	</div>
</div>
</body>
</html>