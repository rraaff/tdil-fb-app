<?php 
	include("../include/headers.php");
	require("../include/funcionesDB.php");
	include_once('../phpmail/class.phpmailer.php');
	include("../include/constantes_mail.php");
	
	
	$fbid = $_REQUEST['fbid']; /*id de facebook*/
	$inv_email = $_REQUEST['inv_email']; /*mail de invitacion*/
	
	$connection = mysql_connect(DB_SERVER,DB_USER, DB_PASS) or die ("Problemas en la conexion");
	mysql_select_db(DB_NAME,$connection);
	
	$fbid = quote_smart($fbid, $connection);
	$inv_email = quote_smart($inv_email, $connection);
	
	$SQL = "SELECT * FROM USER_APP1 WHERE fbid = $fbid";
	$ownerid = mysql_query($SQL) or die("MySQL-err.Query: " . $SQL . " - Error: (" . mysql_errno() . ") " . mysql_error());
	$num_rows = mysql_num_rows($ownerid);
	// el usuario es group owner
	if ($num_rows = 1) {
		$groupownerrow = mysql_fetch_array($ownerid);
		$groupownerid = $groupownerrow["id"];
		$SQL = "SELECT * FROM USER_APP1 WHERE upper(inv_email) = upper($inv_email)";
		$result = mysql_query($SQL) or die("MySQL-err.Query: " . $SQL . " - Error: (" . mysql_errno() . ") " . mysql_error());
		$num_rows = mysql_num_rows($result);
		if ($num_rows == 0) {
			// nadie lo invito, mando email 
			// Inserto en la base
			$SQL = "INSERT INTO USER_APP1 (inv_email,origin, participation) VALUES($inv_email,2,0)"; // 2 es email invitation
			$result = mysql_query($SQL,$connection) or die("MySQL-err.Query: " . $SQL . " - Error: (" . mysql_errno() . ") " . mysql_error());
			$returnInsert = mysql_insert_id($connection);
			//log de invitaciones por email
			$SQL = "INSERT INTO EMAIL_INV_APP1 (groupowner_id,groupmember_id,followed, completed, creation_date) VALUES($groupownerid,$returnInsert,0,0,NOW())";
			$result = mysql_query($SQL,$connection) or die("MySQL-err.Query: " . $SQL . " - Error: (" . mysql_errno() . ") " . mysql_error());
			
			$mail = new PHPMailer(); // defaults to using php "mail()"
			$mail->SMTPDebug = true;
			$mail->From       = EMAIL_FROM_APP1;
			$mail->FromName   = EMAIL_FROM_NAME_APP1;
			$mail->FromName = str_replace('{SENDER_NAME}', $groupownerrow["fbname"], $mail->FromName);
			//Headers
			$headers['X-Mailer'] = 'X-Mailer: PHP/' . phpversion();
			$mail -> AddCustomHeader($headers);
			$mail->Subject    = APP1_SUMATE_SUBJECT;
			$mail->Subject = str_replace('{SENDER_NAME}', $groupownerrow["fbname"], $mail->Subject);
			$mail->AltBody    = BODY_ALT;
			$body             = $mail->getFile('invitacion_app1.html');
			$body = str_replace('{SERVER_NAME}', SERVER_NAME, $body);
			$body = str_replace('{SENDER_NAME}', $groupownerrow["fbname"], $body);
			$link = 'http://www.facebook.com/'. $PAGE_NAME . '?sk=app_'. $APPLICATION_ID;
			$link = $link . '&app_data=join_group|' . $groupownerid . '|' . $returnInsert . '|';
			$body = str_replace('{PAGE_LINK}', SERVER_NAME, $body);
			$mail->MsgHTML("$body");
			$mail->AddAddress("$email");
			$mail_sent = $mail->Send();
			if(!$mail_sent) {
				/* borrar los datos, no se pudo invitar al amigo*/
				$SQL = "DELETE FROM USER_APP1 WHERE id = $returnInsert"; // 2 es email invitation
				$result = mysql_query($SQL,$connection) or die("MySQL-err.Query: " . $SQL . " - Error: (" . mysql_errno() . ") " . mysql_error());
			} 
		} else {
			// si fue invitado, me fijo si participo, si es asi, ya esta en otro grupo
			$user_app1 = mysql_fetch_array($result);
			$user_app1id = $user_app1["id"];
			if ($user_app1['participation'] == 1 || $user_app1['origin'] == 1) {
				include("friendismember.php");
				closeConnection($connection);
				return;
			} else {
				$mail = new PHPMailer(); // defaults to using php "mail()"
				$mail->SMTPDebug = true;
				$mail->From       = EMAIL_FROM_APP1;
				$mail->FromName   = EMAIL_FROM_NAME_APP1;
				$mail->FromName = str_replace('{SENDER_NAME}', $groupownerrow["fbname"], $mail->FromName);
				//Headers
				$headers['X-Mailer'] = 'X-Mailer: PHP/' . phpversion();
				$mail -> AddCustomHeader($headers);
				$mail->Subject    = APP1_SUMATE_SUBJECT;
				$mail->AltBody    = BODY_ALT;
				$body             = $mail->getFile('invitacion_app1.html');
				$body = str_replace('{SERVER_NAME}', SERVER_NAME, $body);
				$body = str_replace('{SENDER_NAME}', $groupownerrow["fbname"], $body);
				$link = 'http://www.facebook.com/'. $PAGE_NAME . '?sk=app_'. $APPLICATION_ID;
				$link = $link . '&app_data=join_group|' . $groupownerid . '|' . $user_app1id . '|';
				$body = str_replace('{PAGE_LINK}', SERVER_NAME, $body);
				$mail->MsgHTML("$body");
				$mail->AddAddress("$email");
				$mail_sent = $mail->Send();
			}
		}
	} 
	closeConnection($connection);
	echo $message;
?>
<?php 
/* {PABLO} Esta pagina muestra el resultado de la invitacion por email*/
if ($mail_sent) { ?>
Invitaciones enviadas
<?php } else { ?>
No se pudo enviar el mail
<?php } ?>