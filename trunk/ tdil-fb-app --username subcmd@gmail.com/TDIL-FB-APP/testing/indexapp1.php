<?php
// PATH TO YOUR FACEBOOK PHP-SDK
	require '../include/facebook.php';
	require '../include/app1constants.php';
	require("../include/funcionesDB.php");


// REPLACE WITH YOUR APPLICATION ID AND SECRET
$facebook = new Facebook(array(
  'appId'  => APPLICATION1_ID,
  'secret' => APPLICATION1_SECRET,
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
	if( isset($_GET['nombre']) ) {
		$params['name'] = $_GET['nombre'];
	}
	$fb->api("/{$fb->getAppId()}/accounts/test-users", "POST", $params);
}

// DELETE Test User
function deleteTestUser($fb, $id, $a) {
	$fb->api("/$id?$a", "DELETE");
}

/////////////////////// \\\\\\\\\\\\\\\\\\\\\\\
function printTestUsers($accounts) {
	$connection = mysql_connect(DB_SERVER,DB_USER, DB_PASS) or die ("Problemas en la conexion");
	mysql_select_db(DB_NAME,$connection);
	$html = "";
	if(isset($accounts['data']) && count($accounts['data'])) {
		$html .= "<table>";
		$html .= "<tr class=\"head\"><td colspan=\"4\">Usuarios de prueba</td></tr>";
		$html .= "<tr class=\"head\"><td>ID</td><td>Nombre</td><td>App user</td><td>Login URL</td><td>Borrar</td></tr>";
		foreach($accounts['data'] as $arr) {
			$html .= "<tr>";
			$html .= "<td>{$arr['id']}</td>";
			$fbid = $arr['id'];
			$SQL = "SELECT USER_APP1 WHERE USER_APP1.fbid = $fbid";
			$result = mysql_query($SQL) or die("MySQL-err.Query: " . $SQL . " - Error: (" . mysql_errno() . ") " . mysql_error());
			$num_rows = mysql_num_rows($result);
			if ($num_rows == 1) {
				$aRow = mysql_fetch_array( $result );
				$html .= "<td>". $aRow['fbname']."</td>";
			} else {
				$html .= "<td>-</td>";
			}
			
			$html .= "<td>" . ((empty($arr['access_token'])) ? "NO":"YES") . "</td>";
			$html .= "<td><a href=\"{$arr['login_url']}\" target=\"_blank\">Test User Login</a></td>";
			$html .= "<td><a href=\"{$_SERVER['PHP_SELF']}?id={$arr['id']}&action=delete\">Delete Test User</a></td>";
			$html .= "</tr>";
		}
		$html .= "</table>";
	} else {
		$html = "No hay usuarios";
	}
	closeConnection($connection);
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
	<title>Usuarios de prueba</title>
	<link href="style.css" media="screen" type="text/css" rel="stylesheet">
</head>
<body>
<div id="wrapper">
	<div id="header">
		<h1><a href="<?php echo $_SERVER['PHP_SELF'] ?>">Usuarios de prueba</a></h1>
	</div>
	<div id="content">
		<?php echo printTestUsers($acc); ?>
		<br />
		<div>
			<form class="cmxform" action="<?php echo $_SERVER['PHP_SELF'] ?>" method="GET">
				<input type="hidden" name="action" value="create" />
				<fieldset>
				<legend>Creacion de usuario de prueba:</legend>
				<ol>
					<li>
						<label>Nombre (Incluir Tester para que sea considerado fan):</label>
						<input type="text" name="nombre" value="" />
					</li>
					<li>
					<label>Aplicacion instalada:</label>
					<input type="radio" name="installed" value="true" checked="checked" /> Si <input type="radio" name="installed" value="false" /> No
					</li>
				</ol>
				</fieldset>
				<p><input type="submit" value="Crear usuario de prueba" /></p>
			</form>
		</div>
	</div>
</div>
</body>
</html>