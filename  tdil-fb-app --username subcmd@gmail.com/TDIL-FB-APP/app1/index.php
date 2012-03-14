<?php 
/* Esta pagina se encarga de procesar las aceptaciones de app request */
	include("../include/headers.php");
	require("../include/funcionesDB.php");
	
	require '../include/facebook.php';
	include("../include/app1constants.php"); 
	// Create our Application instance (replace this with your appId and secret).
	$facebook = new Facebook(array(
			'appId'  => $APPLICATION_ID,
			'secret' => $APPLICATION_SECRET,
	));
	$app_token = get_app_access($APPLICATION_ID,$APPLICATION_SECRET);
	// Get User ID
	$user = $facebook->getUser();
	
	if ($user == 0) {
		session_start();
		if (!empty($_REQUEST['request_ids'])) {
			$_SESSION['request_ids'] = $_REQUEST['request_ids']; // meto los datos en la session y redirijo
		}
		include("askpermissioncanvas.php");
		return;
	}
	if(empty($_REQUEST['request_ids'])) {
		$request_ids = $_SESSION['request_ids'];
	} else {
		$request_ids = $_REQUEST['request_ids'];
	}
	$ok_to_procced = 0;
	/* Si viene con request_id, es una invitacion de facebook*/
	if(!empty($request_ids)) {
		/*
		 * Get the current user, you may use the PHP-SDK
		* or your own server-side flow implementation
		*/
		// We may have more than one request, so it's better to loop
		$requests = explode(',',$request_ids);
		$first = 0;
		foreach($requests as $request_id) {
			// If we have an authenticated user, this would return a recipient specific request: <request_id>_<recipient_id>
			if($user) {
				$request_id = $request_id . "_{$user}";
			}
			// Get the request details using Graph API
			$request_content = json_decode(file_get_contents("https://graph.facebook.com/$request_id?$app_token"), TRUE);
			// An example of how to get info from the previous call
			$request_message = $request_content['message'];
			$from_id = $request_content['from']['id'];
			// An easier way to extract info from the data field
			// Now that we got the $item_id and the $item_type, process them
			// Or if the recevier is not yet a member, encourage him to claims his item (install your application)!
			if($user) {
				/*
				 * When all is done, delete the requests because Facebook will not do it for you!
				* But first make sure we have a user (OR access_token - not used here)
				* because you can't delete a "general" request, you can only delete a recipient specific request
				* <request_id>_<recipient_id>
				*/
				$deleted = file_get_contents("https://graph.facebook.com/$request_id?$app_token&method=delete"); // Should return true on success
			}
			/* Lo uno solo al primer grupo*/
			if ($first == 0) {
				$first = 1;
				$groupowner_fbid = $from_id; /*Tomo el grupo al cual se quiere unir el usuario */
			}
		}
		// Esta todo ok, preparo la data para la app pendiente
		$connection = mysql_connect(DB_SERVER,DB_USER, DB_PASS) or die ("Problemas en la conexion");
		mysql_select_db(DB_NAME,$connection);
		$groupowner_fbid = quote_smart($groupowner_fbid, $connection);
		$SQL = "SELECT * FROM USER_APP1 WHERE fbid = $groupowner_fbid AND origin = 1";
		$group_owner = mysql_query($SQL) or die("MySQL-err.Query: " . $SQL . " - Error: (" . mysql_errno() . ") " . mysql_error());
		$num_rows = mysql_num_rows($group_owner);
		if ($num_rows > 0) {
			$user_fbid = quote_smart($user, $connection);
			$SQL = "SELECT * FROM USER_APP1 WHERE fbid = $user_fbid AND origin != 1";
			$group_member = mysql_query($SQL) or die("MySQL-err.Query: " . $SQL . " - Error: (" . mysql_errno() . ") " . mysql_error());
			$num_rows = mysql_num_rows($group_member);
			if ($num_rows == 1) {
				$group_ownerrow = mysql_fetch_array($group_owner);
				$idgroup = $group_ownerrow['id'];
				$group_memberrow = mysql_fetch_array($group_member);
				$iduser = $group_memberrow['id'];
				// todo ok, registro la accion pendiente y muestro el link a tab para que termine de unirse al grupo
				$SQL = "DELETE FROM ACTION_APP1 WHERE fbid = $user_fbid";
				$result = mysql_query($SQL,$connection) or die("MySQL-err.Query: " . $SQL . " - Error: (" . mysql_errno() . ") " . mysql_error());
				// inserto la ultima
				$SQL = "INSERT INTO ACTION_APP1(fbid, userid, action, dataid) VALUES($user_fbid, $iduser, 'join_group',$idgroup)";
				$result = mysql_query($SQL,$connection) or die("MySQL-err.Query: " . $SQL . " - Error: (" . mysql_errno() . ") " . mysql_error());
				$ok_to_procced = 1;
			} else {
				echo "Usuario inexistente";
			}
		} else {
			echo "Grupo invalido";
		}
		closeConnection($connection);
	} else {
		echo "La peticion ya expiro";
	}
?>
<?php 
/* {PABLO} Esto se muestra para que termine el proceso de union al grupo*/
if ($ok_to_procced == 1) { 
	$redirect = 'https://www.facebook.com/'. $PAGE_NAME . '?sk=app_'. $APPLICATION_ID;
?>
	Para unirte al grupo tenes que cliquear <a href="<?php echo $redirect;?>" target="_top">Aca</a>
<?php } ?>