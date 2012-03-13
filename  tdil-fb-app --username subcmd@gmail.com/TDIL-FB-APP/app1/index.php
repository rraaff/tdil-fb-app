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
	echo $request_ids;
	echo $user;
	if(empty($_REQUEST['request_ids'])) {
		$request_ids = $_SESSION['request_ids'];
	} else {
		$request_ids = $_REQUEST['request_ids'];
	}
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
			extract(json_decode($request_content['data'], TRUE));
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
				$idgroup = $item_id; /*Tomo el grupo al cual se quiere unir el usuario */
			}
		}
	}

?>
A hacer el join <?php $idgroup;?>

si es fan, lo termino al proceso, si no es fan, cargo la op pendiente y le digo que se tiene que hacer y luego volver al tab de la app