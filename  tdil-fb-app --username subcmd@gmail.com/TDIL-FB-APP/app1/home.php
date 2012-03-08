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

	$message = "";
	$connection = mysql_connect(DB_SERVER,DB_USER, DB_PASS) or die ("Problemas en la conexion");
		
	mysql_select_db(DB_NAME,$connection);
	
	$fbid = quote_smart($fbid, $connection);
	$inv_email = quote_smart($inv_email, $connection);
	
	$SQL = "SELECT * FROM GROUP_APP1 WHERE groupowner_fbid = $fbid";
	$result = mysql_query($SQL);
	$num_rows = mysql_num_rows($result);
	if ($result) {
		if ($num_rows > 0) {
			// el usuario es group owner
			include("ownerhome.php");
		} else {
			$SQL = "SELECT * FROM GROUP_APP1 WHERE groupmember_fbid = $fbid";
			$result = mysql_query($SQL);
			$num_rows = mysql_num_rows($result);
			if ($result) {
				if ($num_rows > 0) {
					// el usuario es group member
					$message = "group member";
				} else {
					// si es fan
					if ($fan == 1) {
						// si la accion esta vacia, busco si tengo alguna pendiente para ese usuario y seteo los valores para que siga funcionando
						if ($action == "") {
							$SQL = "SELECT * FROM ACTION_APP1 WHERE fbid = $fbid";
							$result = mysql_query($SQL);
							$num_rows = mysql_num_rows($result);
							if ($result) {
								if ($num_rows > 0) {
									$pending = mysql_fetch_array($result);
									$action = $pending["action"];
									$idgroup = $pending["dataid"];
									$iduser = $pending["iduser"];
									$inv_email = $pending["data"];
									$inv_email = quote_smart($inv_email, $connection);
								}
							} else {
								$message = "Error de conexion";
							}
						}
						// si viene con accion le doy bola a la accion
						if ($action == "new_group") { /* o pase el parametro ese en el request ...*/
							// busco si fue invitado
							$SQL = "SELECT * FROM USER_APP1 WHERE inv_email = $inv_email AND origin = 1 AND participation = 0";
							$result = mysql_query($SQL);
							if ($result) {
								$num_rows = mysql_num_rows($result);
								if ($num_rows > 0) {
									$fbname = quote_smart($fbname, $connection);
									$fbusername = quote_smart($fbusername, $connection);
									$fbgender = quote_smart($fbgender, $connection);
									// el usuario fue invitado, se completan sus datos de fb
									$SQL = "UPDATE USER_APP1 SET fbid = $fbid, fbname = $fbname, fbusername = $fbusername, fbgender = $fbgender , participation = 1 WHERE inv_email = $inv_email";
									$result = mysql_query($SQL,$connection);
									if ($result) {
										// se registra un grupo inicial con el member 0
										$SQL = "INSERT INTO GROUP_APP1(groupowner_fbid, groupmember_fbid) VALUES($fbid, 0)"; // TODO XXX esto creo que no hace falta
										$result = mysql_query($SQL,$connection);
										if ($result) {
											/* Se formo un grupo START */
											$message = "Grupo formado";
											include("ownerhome.php");
											/* Se formo un grupo END */
										} else {
											$message = "Error de conexion";
										}
									} else {
										$message = "Error de conexion";
									}
								} else {
									/* ERROR tratando de forma START */
									$message = "no tiene permiso para formar un grupo";
									/* ERROR tratando de forma START */
								}
							} else {
								$message = "Error de conexion";
							}
						} else {
							if ($action == "join_group") {
								// si se esta uniendo a un grupo, veo que el id de grupo exista, tendria que validar la invitacion de alguna forma?
								$idgroup = quote_smart($idgroup, $connection);
								$SQL = "SELECT * FROM USER_APP1 WHERE id = $idgroup AND origin = 1 AND participation = 1";
								$result = mysql_query($SQL);
								$num_rows = mysql_num_rows($result);
								if ($result) {
									if ($num_rows > 0) {
										$idgrouprow = mysql_fetch_array($result);
										$groupowner_fbid = $idgrouprow["fbid"];
										$iduser = quote_smart($iduser, $connection);
										$SQL = "SELECT * FROM USER_APP1 WHERE id = $iduser AND origin = 0";
										$result = mysql_query($SQL);
										$num_rows = mysql_num_rows($result);
										if ($result) {
											if ($num_rows > 0) {
												// datos validos, completo el usuario
												$fbname = quote_smart($fbname, $connection);
												$fbusername = quote_smart($fbusername, $connection);
												$fbgender = quote_smart($fbgender, $connection);
												$SQL = "UPDATE USER_APP1 SET fbid = $fbid, fbname = $fbname, fbusername = $fbusername, fbgender = $fbgender , participation = 1 WHERE id = $iduser";
												$result = mysql_query($SQL,$connection);
												if ($result) {
													// se registra el miembro
													$SQL = "INSERT INTO GROUP_APP1(groupowner_fbid, groupmember_fbid) VALUES($groupowner_fbid, $fbid)";
													$result = mysql_query($SQL,$connection);
													if ($result) {
														/* Se formo un grupo START */
														$message = "Se unio al grupo";
														// TODO XXX ojo que aca tengo que contar y ver a cuantos sum
														/* Se formo un grupo END */
													} else {
														$message = "Error de conexion";
													}
												} else {
													$message = "Error de conexion";
												}		
											} else {
												$message = "El usuario no existe";
											}
										} else {
											$message = "Error de conexion";
										}
									} else {
										$message = "El grupo no existe";
									}
								} else {
									$message = "Error de conexion";
								}
							} else {
								// si no viene con accion
									// si tiene accion pendiente, la termino
									// si no tiene accion pendiente, aviso de solo para invitados
								
							}
						}
					} else {
						/* *************SI NO ES FAN *******************/
						// si viene con accion valida
						if ($action == "new_group") {
							// busco si fue invitado
							$SQL = "SELECT * FROM USER_APP1 WHERE inv_email = $inv_email AND origin = 1 AND participation = 0";
							$result = mysql_query($SQL);
							$num_rows = mysql_num_rows($result);
							if ($result) {
								if ($num_rows > 0) {
									$ownerrow = mysql_fetch_array($result);
									$userid = $ownerrow["id"];
									$fbname = quote_smart($fbname, $connection);
									$fbusername = quote_smart($fbusername, $connection);
									$fbgender = quote_smart($fbgender, $connection);
									// el usuario fue invitado, se completan sus datos de fb
									$SQL = "UPDATE USER_APP1 SET fbid = $fbid, fbname = $fbname, fbusername = $fbusername, fbgender = $fbgender , participation = 0 WHERE inv_email = $inv_email";
									$result = mysql_query($SQL,$connection);
									if ($result) {
										// borro acciones pendientes que tenga
										$SQL = "DELETE FROM ACTION_APP1 WHERE fbid = $fbid";
										$result = mysql_query($SQL,$connection);
										if ($result) {
											// inserto la ultima
											$newgroup = quote_smart("new_group", $connection);
											$SQL = "INSERT INTO ACTION_APP1(fbid, userid, action, data) VALUES($fbid, $userid, $newgroup, $inv_email)";
											$result = mysql_query($SQL,$connection);
											if ($result) {
												$message = "Para empezar tu grupo primero tenes que hacerte fan";
											} else {
												$message = "Error de conexion $fbid, $newgroup";
											}
										} else {											
											$message = "Error de conexion";					
										}
									}
								}
							} else {
								// no hago nada, llego sin invitacion
							}
					} else {
						if ($action == "join_group") {
							// si se esta uniendo a un grupo, veo que el id de grupo exista, tendria que validar la invitacion de alguna forma?
							$idgroup = quote_smart($idgroup, $connection);
							$SQL = "SELECT * FROM USER_APP1 WHERE id = $idgroup AND origin = 1 AND participation = 1";
							$result = mysql_query($SQL);
							$num_rows = mysql_num_rows($result);
							if ($result) {
								if ($num_rows > 0) {
									$idgrouprow = mysql_fetch_array($result);
									$groupowner_fbid = $idgrouprow["fbid"];
									$iduser = quote_smart($iduser, $connection);
									$SQL = "SELECT * FROM USER_APP1 WHERE id = $iduser AND origin = 0";
									$result = mysql_query($SQL);
									$num_rows = mysql_num_rows($result);
									if ($result) {
										if ($num_rows > 0) {
											// datos validos, completo el usuario
											$fbname = quote_smart($fbname, $connection);
											$fbusername = quote_smart($fbusername, $connection);
											$fbgender = quote_smart($fbgender, $connection);
											$SQL = "UPDATE USER_APP1 SET fbid = $fbid, fbname = $fbname, fbusername = $fbusername, fbgender = $fbgender , participation = 1 WHERE id = $iduser";
											$result = mysql_query($SQL,$connection);
											if ($result) {
												// borro acciones pendientes que tenga
												$SQL = "DELETE FROM ACTION_APP1 WHERE fbid = $fbid";
												$result = mysql_query($SQL,$connection);
												if ($result) {
													// inserto la ultima
													$SQL = "INSERT INTO ACTION_APP1(fbid, userid, action, datataid) VALUES($fbid, $iduser, 'join_group',$idgroup)";
													$result = mysql_query($SQL,$connection);
													$message = "Para unirte al grupo primero tenes que hacerte fan";			
												} else {											
													$message = "Error de conexion";					
												}
											}
										}
									}
								}
							}
						}
					}
				}
			} 
		} else {
			$message = "Error de conexion";
		}
		}
	}
		
	closeConnection($connection);
	echo $message;
?>
</body>
</html>