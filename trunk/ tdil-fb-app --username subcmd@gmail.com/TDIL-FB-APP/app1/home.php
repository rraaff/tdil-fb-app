<html>
<head>
</head>
<?php 
	include("../include/headers.php");
	require("../include/funcionesDB.php");
?>
<?php if (TESTING == 'TRUE') {
	// esto sale directamente del signed request
	$fan = $_REQUEST['fan']; /*1 fan, 0 no fan*/
	$fbid = $_REQUEST['fbid']; /*id de facebook*/
	$fbemail = $_REQUEST['fbemail']; /*id de facebook*/
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
	$action = "todo";
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
							// el usuario fue invitado, se completan sus datos de fb
							$SQL = "UPDATE USER_APP1 SET fbid = $fbid, fbname = $fbname, fbusername = $fbusername, fbgender = $fbgender , participation = 1 WHERE inv_email = $inv_email";
							$result = mysql_query($SQL,$connection) or die("MySQL-err.Query: " . $SQL . " - Error: (" . mysql_errno() . ") " . mysql_error());
							// se registra un grupo inicial con el member 0
							$SQL = "INSERT INTO GROUP_APP1(groupowner_fbid, groupmember_fbid, creation_date) VALUES($fbid, 0, NOW())"; // TODO XXX esto creo que no hace falta
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
?>
</body>
</html>