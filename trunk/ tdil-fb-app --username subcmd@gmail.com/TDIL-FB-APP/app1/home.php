<?php
	include("../include/headers.php");
	require("../include/funcionesDB.php");
	include_once('../phpmail/class.phpmailer.php');
	include("../include/constantes_mail.php");
	
	require '../include/facebook.php';
	include("../include/app1constants.php"); 
	// Create our Application instance (replace this with your appId and secret).
	$facebook = new Facebook(array(
			'appId'  => APPLICATION1_ID,
			'secret' => APPLICATION1_SECRET,
	));
	$app_token = get_app_access(APPLICATION1_ID,APPLICATION1_SECRET);
	// Get User ID
	$user = $facebook->getUser();
?>
<?php 
	if(!isset($_SESSION)) {
		session_start();
	}
	/* Aca tengo que sacar cosas del signed_request y del usuario logueado*/
	$inv_email = "";
	$signed_request = $facebook->getSignedRequest();
	if (!$signed_request) {
		echo "External links no permitidos";
		return;
	}
	$app_data = null;
	if (isset($_SESSION['app_data'])) {
		$app_data = $_SESSION['app_data'];
	} else {
		if (isset($signed_request['app_data'])) {
			$app_data = $signed_request["app_data"];
		}
	}
	/* si el usuario es nulo, no lo autorizo */
	if ($user == 0) {
		$_SESSION['app_data'] = $app_data; // meto los datos en la session y redirijo
		include("askpermission.php");
		return;
	}
	$fbid = $signed_request['user_id'];
	$page_id = $signed_request['page']['id']; /*TODO Limitar a una pagina cuanto este productivo*/
	//if ($page_id != $PAGEID) {
	//	die("No allowed");
	//}
	if (empty($signed_request['page']['liked'])) {
		// Si estoy en localhost
		if (APPLICATION1_URL == 'http://localhost/TDIL-FB-APP/app1') {
			$testuser = $facebook->api('/me');
			// Si el nombre incluye Tester entonces asumo que es fan
			if (strpos($testuser['name'], 'Tester')) {
				$_SESSION['fan'] = '1'; // pongo que es fan
				$fan = 1;
			} else {
				$_SESSION['app_data'] = $app_data; // meto los datos en la session y redirijo
				$fan = 0;
				$_SESSION['fan'] = '0';
				include("onlyforfans.php");
				return;
			}
		} else {
			$_SESSION['app_data'] = $app_data; // meto los datos en la session y redirijo
			$fan = 0;
			$_SESSION['fan'] = '0';
			include("onlyforfans.php");
			return;
		}
	} else {
		$_SESSION['fan'] = '1'; // pongo que es fan
		$fan = 1;
	}
?>
<?php
	unset($_SESSION['app_data']); // si llegue hasta aca, el usuario esta logueado y es fan, ya no necesito nada 
	// en la session
	try {
		// Proceed knowing you have a logged in user who's authenticated.
		$user_profile = $facebook->api('/me');
	} catch (FacebookApiException $e) {
		print_r($e);
		error_log($e);
		$user = null;
	}
	$fbemail = '';
	$fbname = $user_profile['name'];
	$fbusername = "";
	if (isset($user_profile['username'])) {
		$fbusername = $user_profile['username'];
	}
	$fbgender = $user_profile['gender'];
	
	if ($app_data) {
		$app_data_arr = explode("|", $app_data);
		$action = $app_data_arr[0];
		if ($action == 'new_group') {
			$inv_email = $app_data_arr[1];
		} else {
			if ($action == 'join_group') {
				$idgroup = $app_data_arr[1];
				$iduser = $app_data_arr[2];
			}
		}
	}
 ?>
<?php
	$pendingaction = 0;
	$message = "";
	$connection = mysql_connect(DB_SERVER,DB_USER, DB_PASS) or die ("Problemas en la conexion");
		
	mysql_select_db(DB_NAME,$connection);
	
	$SQL = "SELECT USER_APP1.*,WINNER_APP1.win_date FROM USER_APP1, WINNER_APP1 WHERE USER_APP1.fbid = WINNER_APP1.groupowner_fbid AND 1 = (select show_winner from CONFIG_APP1)";
	$result = mysql_query($SQL) or die("MySQL-err.Query: " . $SQL . " - Error: (" . mysql_errno() . ") " . mysql_error());
	$num_rows = mysql_num_rows($result);
	if ($num_rows == 1) { 
		$aRow = mysql_fetch_array( $result );
		$winnerame = $aRow["fbname"];
		$winnerfbid = $aRow["fbid"];
		$winnerusername =  $aRow["fbusername"];
		include("winner.php");
		closeConnection($connection);
		return;
	}
	
	$fbid = quote_smart($fbid, $connection);
	$inv_email = quote_smart($inv_email, $connection);
	
	$SQL = "SELECT * FROM GROUP_APP1 WHERE groupowner_fbid = $fbid";
	$result = mysql_query($SQL) or die("MySQL-err.Query: " . $SQL . " - Error: (" . mysql_errno() . ") " . mysql_error());
	$num_rows = mysql_num_rows($result);
	if ($num_rows > 0) {
		// el usuario es group owner
		
		include("ownerhome.php");
		closeConnection($connection);
		return;
	} 
	$SQL = "SELECT * FROM GROUP_APP1 WHERE groupmember_fbid = $fbid";
	$result = mysql_query($SQL) or die("MySQL-err.Query: " . $SQL . " - Error: (" . mysql_errno() . ") " . mysql_error());
	$num_rows = mysql_num_rows($result);
	if ($num_rows > 0) {
		// el usuario es group member
		$friend_group = mysql_fetch_array( $result );
		$friend_groupid = $friend_group['groupowner_fbid'];
		$friend_groupid = quote_smart($friend_groupid, $connection);
		$SQL = "SELECT * FROM USER_APP1 WHERE fbid = $friend_groupid";
		$result = mysql_query($SQL) or die("MySQL-err.Query: " . $SQL . " - Error: (" . mysql_errno() . ") " . mysql_error());
		$owner = mysql_fetch_array( $result );
		$group_owner_name = $owner['fbname'];
		include("memberhome.php");
		closeConnection($connection);
		return;
	} 
	// si es fan
	if ($fan == 1) {
		// si la accion esta vacia, busco si tengo alguna pendiente para ese usuario y seteo los valores para que siga funcionando
		if ($action == "") {
			$SQL = "SELECT * FROM ACTION_APP1 WHERE fbid = $fbid";
			$result = mysql_query($SQL) or die("MySQL-err.Query: " . $SQL . " - Error: (" . mysql_errno() . ") " . mysql_error());
			$num_rows = mysql_num_rows($result);
			if ($num_rows > 0) {
				syslog(LOG_INFO, 'has pending action');
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
				include("ownerhome.php");
				closeConnection($connection);
				return;
				/* Se formo un grupo END */
			} else {
				$errorMessage = "No tiene permisos para formar un grupo";
				include("showerror.php");
				closeConnection($connection);
				return;
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
					$group_owner_name = $idgrouprow["fbname"];
					$iduser = quote_smart($iduser, $connection);
					$SQL = "SELECT * FROM USER_APP1 WHERE id = $iduser AND origin != 1";
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
							/* NO SE USA EL ENVIO DE EMAILif (mysql_affected_rows() == 1) {
								$mail = new PHPMailer(); // defaults to using php "mail()"
								$mail->SMTPDebug = true;
								$mail->From       = EMAIL_FROM_APP1;
								$mail->FromName   = EMAIL_FROM_NAME_APP1;
								//Headers
								$mail->Subject    = APP1_WINNER_SUBJECT;
								$mail->AltBody    = BODY_ALT;
								$body             = $mail->getFile('../winner_app1.html');
								$body = str_replace('{SERVER_NAME}', SERVER_NAME, $body);
								$body = str_replace('{WINNER_NAME}', $idgrouprow['fbname'], $body);
								$body = str_replace('{PAGE_LINK}', SERVER_NAME, $body);
								$body             = eregi_replace("[\]",'',$body);
								$mail->MsgHTML($body);
								$mail->AddAddress($idgrouprow['inv_email'],$idgrouprow['inv_email']);
								$mail_sent = $mail->Send();
							}*/
						}
						closeConnection($connection);
						return;
						/* Se formo un grupo END */
					} else {
						$errorMessage = "El usuario no existe";
						include("showerror.php");
						closeConnection($connection);
						return;
					}
				} else {
					$errorMessage = "El grupo no existe";
					include("showerror.php");
					closeConnection($connection);
					return;
				}
			} else {
				include("notinvited.php");
				closeConnection($connection);
				return;
			}
		}
	} 
		
	closeConnection($connection);
		
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