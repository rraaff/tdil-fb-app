 /* Sacarlo por que no cae nunca por aca */
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
	