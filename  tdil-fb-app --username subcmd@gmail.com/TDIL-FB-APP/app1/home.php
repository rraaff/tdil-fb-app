<html>
<head>
<script type='text/javascript' src='../js/jquery-1.7.min.js'></script>
</head>
<?php 
	include("../include/headers.php");
	require("../include/funcionesDB.php");
	
	require '../include/facebook.php';
	Facebook::$CURL_OPTS[CURLOPT_CAINFO] = '/var/www/TDIL-FB-APP/include/fb_ca_chain_bundle.crt';
	
	$APPLICATION_ID = '292861170783253';
	$APPLICATION_SECRET = '822b60809737ff91e6142f924e85e9d5';
	$PAGEID = '';
	
	// Create our Application instance (replace this with your appId and secret).
	$facebook = new Facebook(array(
			'appId'  => $APPLICATION_ID,
			'secret' => $APPLICATION_SECRET,
	));
	
	$app_token = get_app_access($APPLICATION_ID,$APPLICATION_SECRET);
	
	// Get User ID
	$user = $facebook->getUser();
?>
<?php if (TESTING == 'XXX') {
	// esto sale directamente del signed request/profile del usuario
	$fan = $_REQUEST['fan']; /*1 fan, 0 no fan*/
	
	$fbid = $_REQUEST['fbid']; /*id de facebook*/

	$fbname = $_REQUEST['fbname'];
	$fbusername = $_REQUEST['fbusername'];
	$fbgender = $_REQUEST['fbgender'];

	// esto sale de la data
	$action = $_REQUEST['action']; /* new_group, join_group, empty*/
	$inv_email = $_REQUEST['inv_email']; /*mail de invitacion*/
	$idgroup = $_REQUEST['idgroup']; /*id de grupo a hacer el join*/
	$iduser = $_REQUEST['iduser'];
} else {
	/* Aca tengo que sacar cosas del signed_request y del usuario logueado*/
	$inv_email = "";
	$signed_request = $facebook->getSignedRequest();
	$app_data = $signed_request["app_data"];
	print_r($signed_request);
	echo '<br>';
	echo '<a href="';echo $facebook->getLoginUrl();echo '&app_data=' . $app_data;echo '">Login</a>';
	$fbid = $signed_request['user_id'];
	$page_id = $signed_request["page"]["id"]; /*TODO Limitar a una pagina cuanto este productivo*/
	if (empty($signed_request["page"]["liked"])) {
		$fan = 0;
	} else {
		$fan = 1;
	}
	
	//if ($page_id != $PAGEID) {
	//	die("No allowed");
	//}
	echo '<br>';
	echo $app_data;
	if ($user) {
		try {
			// Proceed knowing you have a logged in user who's authenticated.
			$user_profile = $facebook->api('/me');
		} catch (FacebookApiException $e) {
			print_r($e);
			error_log($e);
			$user = null;
		}
	} else {
		/* TODO aca va un die */
		die('not logged');
	}
	$fbemail = '';
	$fbname = $user_profile['name'];
	$fbusername = $user_profile['username'];
	$fbgender = $user_profile['gender'];
	
	/* Si viene con request_id, es una invitacion de facebook*/
	if(!empty($_REQUEST['request_ids'])) {
		/*
		 * Get the current user, you may use the PHP-SDK
		* or your own server-side flow implementation
		*/
		// We may have more than one request, so it's better to loop
		$requests = explode(',',$_REQUEST['request_ids']);
		foreach($requests as $request_id) {
			// If we have an authenticated user, this would return a recipient specific request: <request_id>_<recipient_id>
			if($fbid) {
				$request_id = $request_id . "_{$fbid}";
			}
			// Get the request details using Graph API
			$request_content = json_decode(file_get_contents("https://graph.facebook.com/$request_id?$app_token"), TRUE);
			// An example of how to get info from the previous call
			$request_message = $request_content['message'];
			$from_id = $request_content['from']['id'];
			// An easier way to extract info from the data field
			extract(json_decode($request_content['data'], TRUE));
			// Now that we got the $item_id and the $item_type, process them
			// Or if the recevier is not yet a member, encourage him to claims his item (install your application)!
			$idgroup = $item_id; /*Tomo el grupo al cual se quiere unir el usuario */
			if($fbid) {
				/*
				 * When all is done, delete the requests because Facebook will not do it for you!
				* But first make sure we have a user (OR access_token - not used here)
				* because you can't delete a "general" request, you can only delete a recipient specific request
				* <request_id>_<recipient_id>
				*/
				$deleted = file_get_contents("https://graph.facebook.com/$request_id?$app_token&method=delete"); // Should return true on success
			}
		}
	} else {
		if ($app_data) {
			$app_data_arr = explode("|", $app_data);
			$action = $app_data_arr[0];
			if ($action == 'new_group') {
				$inv_email = $app_data_arr[1];
			} else {
				$idgroup = $app_data_arr[1];
				$iduser = $app_data_arr[2];
			}
		}
	}
	
} ?>
<body>
Debug info:
action:<?php echo $action?><br>
inv_email:<?php echo $inv_email?><br>
fan:<?php echo $fan?><br>
fbid:<?php echo $fbid?><br>
fbemail:<?php echo $fbemail?><br>
fbname:<?php echo $fbname?><br>
fbusername:<?php echo $fbusername?><br>
fbgender:<?php echo $fbgender?><br>
idgroup:<?php echo $idgroup?><br>
iduser:<?php echo $iduser?><br>

<?php

	$pendingaction = 0;
	$message = "";
	$connection = mysql_connect(DB_SERVER,DB_USER, DB_PASS) or die ("Problemas en la conexion");
		
	mysql_select_db(DB_NAME,$connection);
	
	$fbid = quote_smart($fbid, $connection);
	$inv_email = quote_smart($inv_email, $connection);
	
	$SQL = "SELECT * FROM GROUP_APP1 WHERE groupowner_fbid = $fbid";
	$result = mysql_query($SQL) or die("MySQL-err.Query: " . $SQL . " - Error: (" . mysql_errno() . ") " . mysql_error());
	$num_rows = mysql_num_rows($result);
	if ($num_rows > 0) {
		// el usuario es group owner
		include("ownerhome.php");
	} else {
		$SQL = "SELECT * FROM GROUP_APP1 WHERE groupmember_fbid = $fbid";
		$result = mysql_query($SQL) or die("MySQL-err.Query: " . $SQL . " - Error: (" . mysql_errno() . ") " . mysql_error());
		$num_rows = mysql_num_rows($result);
			if ($num_rows > 0) {
				// el usuario es group member
				include("memberhome.php");
			} else {
				// si es fan
				if ($fan == 1) {
					// si la accion esta vacia, busco si tengo alguna pendiente para ese usuario y seteo los valores para que siga funcionando
					if ($action == "") {
						$SQL = "SELECT * FROM ACTION_APP1 WHERE fbid = $fbid";
						$result = mysql_query($SQL) or die("MySQL-err.Query: " . $SQL . " - Error: (" . mysql_errno() . ") " . mysql_error());
						$num_rows = mysql_num_rows($result);
						if ($num_rows > 0) {
							$pendingaction = 1;
							$pending = mysql_fetch_array($result);
							$action = $pending["action"];
							$idgroup = $pending["dataid"];
							$iduser = $pending["userid"];
							$inv_email = $pending["data"];
							$inv_email = quote_smart($inv_email, $connection);
						}
					}
					// Es fan y esta formando un nuevo grupo
					if ($action == "new_group") {
						// busco si fue invitado
						$SQL = "SELECT * FROM USER_APP1 WHERE inv_email = $inv_email AND origin = 1 AND participation = 0";
						$result = mysql_query($SQL) or die("MySQL-err.Query: " . $SQL . " - Error: (" . mysql_errno() . ") " . mysql_error());
						$num_rows = mysql_num_rows($result);
						if ($num_rows > 0) {
							$fbname = quote_smart($fbname, $connection);
							$fbusername = quote_smart($fbusername, $connection);
							$fbgender = quote_smart($fbgender, $connection);
							if($pendingaction == 0) {
								// el usuario fue invitado, se completan sus datos de fb
								$SQL = "UPDATE USER_APP1 SET fbid = $fbid, fbname = $fbname, fbusername = $fbusername, fbgender = $fbgender , participation = 1 WHERE inv_email = $inv_email";
								$result = mysql_query($SQL,$connection) or die("MySQL-err.Query: " . $SQL . " - Error: (" . mysql_errno() . ") " . mysql_error());
							} else {
								$SQL = "UPDATE USER_APP1 SET participation = 1 WHERE inv_email = $inv_email";
								$result = mysql_query($SQL,$connection) or die("MySQL-err.Query: " . $SQL . " - Error: (" . mysql_errno() . ") " . mysql_error());
							}
							// se registra un grupo inicial con el member 0
							$SQL = "INSERT INTO GROUP_APP1(groupowner_fbid, groupmember_fbid, creation_date) VALUES($fbid, 0, NOW())";
							$result = mysql_query($SQL,$connection) or die("MySQL-err.Query: " . $SQL . " - Error: (" . mysql_errno() . ") " . mysql_error());
							if($pendingaction = 1) {
								$SQL = "DELETE FROM ACTION_APP1 WHERE fbid = $fbid";
								mysql_query($SQL,$connection) or die("MySQL-err.Query: " . $SQL . " - Error: (" . mysql_errno() . ") " . mysql_error());
							}
							/* Se formo un grupo START */
							$message = "Grupo formado";
							include("ownerhome.php");
							/* Se formo un grupo END */
						} else {
							/* ERROR tratando de forma START */
							$message = "no tiene permiso para formar un grupo";
							/* ERROR tratando de forma START */
						}
					} else {
						if ($action == "join_group") {
							// si se esta uniendo a un grupo, veo que el id de grupo exista, tendria que validar la invitacion de alguna forma?
							$idgroup = quote_smart($idgroup, $connection);
							$SQL = "SELECT * FROM USER_APP1 WHERE id = $idgroup AND origin = 1 AND participation = 1";
							$result = mysql_query($SQL) or die("MySQL-err.Query: " . $SQL . " - Error: (" . mysql_errno() . ") " . mysql_error());
							$num_rows = mysql_num_rows($result);
							if ($num_rows > 0) {
								$idgrouprow = mysql_fetch_array($result);
								$groupowner_fbid = $idgrouprow["fbid"];
								$iduser = quote_smart($iduser, $connection);
								$SQL = "SELECT * FROM USER_APP1 WHERE id = $iduser AND origin = 0";
								$result = mysql_query($SQL) or die("MySQL-err.Query: " . $SQL . " - Error: (" . mysql_errno() . ") " . mysql_error());
								$num_rows = mysql_num_rows($result);
								if ($num_rows > 0) {
									// datos validos, completo el usuario
									$fbname = quote_smart($fbname, $connection);
									$fbusername = quote_smart($fbusername, $connection);
									$fbgender = quote_smart($fbgender, $connection);
									$SQL = "UPDATE USER_APP1 SET fbid = $fbid, fbname = $fbname, fbusername = $fbusername, fbgender = $fbgender , participation = 1 WHERE id = $iduser";
									$result = mysql_query($SQL,$connection) or die("MySQL-err.Query: " . $SQL . " - Error: (" . mysql_errno() . ") " . mysql_error());
									// se registra el miembro
									$SQL = "INSERT INTO GROUP_APP1(groupowner_fbid, groupmember_fbid, creation_date) VALUES($groupowner_fbid, $fbid, NOW())";
									$result = mysql_query($SQL,$connection) or die("MySQL-err.Query: " . $SQL . " - Error: (" . mysql_errno() . ") " . mysql_error());
									if($pendingaction = 1) {
										$SQL = "DELETE FROM ACTION_APP1 WHERE fbid = $fbid";
										mysql_query($SQL,$connection) or die("MySQL-err.Query: " . $SQL . " - Error: (" . mysql_errno() . ") " . mysql_error());
									}
									/* Se formo un grupo START */
									include("memberhome.php");
									// Cuento si llego a diez amigos, si es asi, lo marco como ganador y mando el mail
									$SQL = "SELECT COUNT(*) AS CANT FROM GROUP_APP1 WHERE groupowner_fbid = $groupowner_fbid";
									$result = mysql_query($SQL,$connection) or die("MySQL-err.Query: " . $SQL . " - Error: (" . mysql_errno() . ") " . mysql_error());
									$cantidadrow = mysql_fetch_array($result);
									$cantidad = $cantidadrow["CANT"];
									if ($cantidad > APP1_WIN) { // Si llego al limite
										$SQL = "UPDATE WINNER_APP1 SET groupowner_fbid = $groupowner_fbid, win_date = NOW() WHERE groupowner_fbid IS NULL AND active = 1";
										mysql_query($SQL,$connection) or die("MySQL-err.Query: " . $SQL . " - Error: (" . mysql_errno() . ") " . mysql_error());
										// si gano
										if (mysql_affected_rows() == 1) {
											echo "Owner es ganador!!!";
										}
									}									
									/* Se formo un grupo END */
								} else {
									$message = "El usuario no existe";
								}
							} else {
								$message = "El grupo no existe";
							}
						} 
					}
				} else {
					/* *************SI NO ES FAN *******************/
					// si viene con accion valida
					if ($action == "new_group") {
						// busco si fue invitado
						$SQL = "SELECT * FROM USER_APP1 WHERE inv_email = $inv_email AND origin = 1 AND participation = 0";
						$result = mysql_query($SQL) or die("MySQL-err.Query: " . $SQL . " - Error: (" . mysql_errno() . ") " . mysql_error());
						$num_rows = mysql_num_rows($result);
						if ($num_rows > 0) {
							$ownerrow = mysql_fetch_array($result);
							$userid = $ownerrow["id"];
							$fbname = quote_smart($fbname, $connection);
							$fbusername = quote_smart($fbusername, $connection);
							$fbgender = quote_smart($fbgender, $connection);
							// el usuario fue invitado, se completan sus datos de fb
							$SQL = "UPDATE USER_APP1 SET fbid = $fbid, fbname = $fbname, fbusername = $fbusername, fbgender = $fbgender , participation = 0 WHERE inv_email = $inv_email";
							$result = mysql_query($SQL,$connection) or die("MySQL-err.Query: " . $SQL . " - Error: (" . mysql_errno() . ") " . mysql_error());
							// borro acciones pendientes que tenga
							$SQL = "DELETE FROM ACTION_APP1 WHERE fbid = $fbid";
							$result = mysql_query($SQL,$connection) or die("MySQL-err.Query: " . $SQL . " - Error: (" . mysql_errno() . ") " . mysql_error());
							// inserto la ultima
							$newgroup = quote_smart("new_group", $connection);
							$SQL = "INSERT INTO ACTION_APP1(fbid, userid, action, data) VALUES($fbid, $userid, $newgroup, $inv_email)";
							$result = mysql_query($SQL,$connection) or die("MySQL-err.Query: " . $SQL . " - Error: (" . mysql_errno() . ") " . mysql_error());
							include("ownermustbefan.php");
						}
					} else {
						if ($action == "join_group") {
							// si se esta uniendo a un grupo, veo que el id de grupo exista, tendria que validar la invitacion de alguna forma?
							$idgroup = quote_smart($idgroup, $connection);
							$SQL = "SELECT * FROM USER_APP1 WHERE id = $idgroup AND origin = 1 AND participation = 1";
							$result = mysql_query($SQL) or die("MySQL-err.Query: " . $SQL . " - Error: (" . mysql_errno() . ") " . mysql_error());
							$num_rows = mysql_num_rows($result);
							if ($num_rows > 0) {
								$idgrouprow = mysql_fetch_array($result);
								$groupowner_fbid = $idgrouprow["fbid"];
								$iduser = quote_smart($iduser, $connection);
								$SQL = "SELECT * FROM USER_APP1 WHERE id = $iduser AND origin = 0";
								$result = mysql_query($SQL) or die("MySQL-err.Query: " . $SQL . " - Error: (" . mysql_errno() . ") " . mysql_error());
								$num_rows = mysql_num_rows($result);
								if ($num_rows > 0) {
									// datos validos, completo el usuario
									$fbname = quote_smart($fbname, $connection);
									$fbusername = quote_smart($fbusername, $connection);
									$fbgender = quote_smart($fbgender, $connection);
									$SQL = "UPDATE USER_APP1 SET fbid = $fbid, fbname = $fbname, fbusername = $fbusername, fbgender = $fbgender , participation = 1 WHERE id = $iduser";
									$result = mysql_query($SQL,$connection) or die("MySQL-err.Query: " . $SQL . " - Error: (" . mysql_errno() . ") " . mysql_error());
									// borro acciones pendientes que tenga
									$SQL = "DELETE FROM ACTION_APP1 WHERE fbid = $fbid";
									$result = mysql_query($SQL,$connection) or die("MySQL-err.Query: " . $SQL . " - Error: (" . mysql_errno() . ") " . mysql_error());
									// inserto la ultima
									$SQL = "INSERT INTO ACTION_APP1(fbid, userid, action, dataid) VALUES($fbid, $iduser, 'join_group',$idgroup)";
									$result = mysql_query($SQL,$connection) or die("MySQL-err.Query: " . $SQL . " - Error: (" . mysql_errno() . ") " . mysql_error());
									$message = "Para unirte al grupo primero tenes que hacerte fan";
									include("membermustbefan.php");
								}
							}
						}
					}
				}
			} 
		}
		
	closeConnection($connection);
	echo $message;
	
	
	function get_app_access($appId, $appSecret) {
		$token_url =    "https://graph.facebook.com/oauth/access_token?" .
				"client_id=" . $appId .
				"&client_secret=" . $appSecret .
				"&grant_type=client_credentials";
		$token = file_get_contents($token_url);
		return $token;
	}
	
	function getUserFromSignedRequest() {
		if(!empty($_REQUEST['signed_request'])) {
			$signed_request = $_REQUEST["signed_request"];
			list($encoded_sig, $payload) = explode('.', $signed_request, 2);
			$data = json_decode(base64_decode(strtr($payload, '-_', '+/')), true);
	
			if( !empty($data['user_id']) )
				return $data['user_id'];
		}
		return null;
	}
?>
</body>
</html>