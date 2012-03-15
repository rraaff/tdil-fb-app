<?php 
	include("../include/headers.php");
	require("../include/funcionesDB.php");
	
	require '../include/facebook.php';
	if( isset($_REQUEST['request_ids']) && isset($_REQUEST['uid']) ) {
		$message = "";
		$connection = mysql_connect(DB_SERVER,DB_USER, DB_PASS) or die ("Problemas en la conexion");
		mysql_select_db(DB_NAME,$connection);
		
		$uid = quote_smart($_REQUEST['uid'], $connection);
		
		$requests = explode(',',$_REQUEST['request_ids']);
		foreach($requests as $request_id) {
			$request_idarr = $requests = explode('_',$request_id);
			$invitation = quote_smart($request_idarr[0], $connection);
			$invited = quote_smart($request_idarr[1], $connection);
			
			$SQL = "SELECT * FROM USER_APP1 WHERE fbid = $invited";
			$result = mysql_query($SQL) or die("MySQL-err.Query: " . $SQL . " - Error: (" . mysql_errno() . ") " . mysql_error());
			$num_rows = mysql_num_rows($result);
			if ($num_rows == 0) {
				$SQL = "INSERT INTO USER_APP1 (fbid,origin, participation) VALUES($invited,3,0)"; // 3 is fb invitation
				$result = mysql_query($SQL,$connection) or die("MySQL-err.Query: " . $SQL . " - Error: (" . mysql_errno() . ") " . mysql_error());
			} 
			mysql_query("INSERT INTO FB_INV_APP1 (groupowner_fbid, groupmember_fbid,request_id,followed,completed,creation_date) VALUES ($uid, $invited,$invitation,0,0, NOW())") or die("MySQL Error: " . mysql_error());
		}
		closeConnection($connection);
	}
?>
Sus invitaciones han sido guardadas.