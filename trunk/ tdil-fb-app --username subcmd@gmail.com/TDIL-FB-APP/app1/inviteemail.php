<?php 
	include("../include/headers.php");
	require("../include/funcionesDB.php");
	
	$fbid = $_REQUEST['fbid']; /*id de facebook*/
	$inv_email = $_REQUEST['inv_email']; /*mail de invitacion*/
	
	$connection = mysql_connect(DB_SERVER,DB_USER, DB_PASS) or die ("Problemas en la conexion");
	mysql_select_db(DB_NAME,$connection);
	
	$fbid = quote_smart($fbid, $connection);
	$inv_email = quote_smart($inv_email, $connection);
	
	$SQL = "SELECT id FROM USER_APP1 WHERE fbid = $fbid";
	$ownerid = mysql_query($SQL);
	$num_rows = mysql_num_rows($ownerid);
	if ($ownerid) {
		// el usuario es group owner
		if ($num_rows = 1) {
			$groupownerrow = mysql_fetch_array($ownerid);
			$groupownerid = $groupownerrow["id"];
			$SQL = "SELECT * FROM USER_APP1 WHERE upper(inv_email) = upper($inv_email)";
			$result = mysql_query($SQL);
			$num_rows = mysql_num_rows($result);
			if ($result) {
				if ($num_rows == 0) {
					// nadie lo invito, mando email 
					// Inserto en la base
					$SQL = "INSERT INTO USER_APP1 (inv_email,origin, participation) VALUES($inv_email,0,0)";
					$result = mysql_query($SQL,$connection);
					if ($result) {
						$returnInsert = mysql_insert_id($connection);
						$message = "Invitado (nuevo) id: $returnInsert para grupo: $groupownerid";
					} else {
						$message = "Error de conexion";
					}
				} else {
					// si fue invitado, me fijo si participo, si es asi, ya esta en otro grupo
					$user_app1 = mysql_fetch_array($result);
					$user_app1id = $user_app1["id"];
					if ($user_app1['participation'] == 1 || $user_app1['origin'] == 1) {
						$message = "Tu amigo ya se unio a la base";
					} else {
						// nadie lo invito, mando email, no inserto en la base ya esta
						$message = "Invitado (Ya existente) id: $user_app1id grupo: $groupownerid";
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