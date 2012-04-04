<?php // TODO
	include("../include/headers.php");
	require("../include/funcionesDB.php");
	include_once('../phpmail/class.phpmailer.php');
	include("../include/constantes_mail.php");
	require '../include/facebook.php';
	include("../include/app2constants.php"); 
	
	
	$fbid = $_REQUEST['fbid']; /*id de facebook*/
	$inv_email = $_REQUEST['inv_email']; /*mail de invitacion*/
	
	$connection = mysql_connect(DB_SERVER,DB_USER, DB_PASS) or die ("Problemas en la conexion");
	mysql_select_db(DB_NAME,$connection);
	
	$fbid = quote_smart($fbid, $connection);
	$email_address = $inv_email;
	$inv_email = quote_smart($inv_email, $connection);
	
	$SQL = "SELECT * FROM GROUP_APP2 WHERE groupmember_fbid IN (SELECT fbid FROM USER_APP2 WHERE inv_email = $inv_email)";
	$alreadyfriend = mysql_query($SQL) or die("MySQL-err.Query: " . $SQL . " - Error: (" . mysql_errno() . ") " . mysql_error());
	$num_rows = mysql_num_rows($alreadyfriend);
	if ($num_rows > 0) {
		$errorMessage = "Esta persona ya está unida a un usuario";
		include("showerror.php");
		closeConnection($connection);
		return;
	}
	
	$SQL = "SELECT * FROM GROUP_APP2 WHERE groupowner_fbid = (SELECT fbid FROM USER_APP2 WHERE inv_email = $inv_email) AND groupmember_fbid = $fbid";
	$result = mysql_query($SQL) or die("MySQL-err.Query: " . $SQL . " - Error: (" . mysql_errno() . ") " . mysql_error());
	$num_rows = mysql_num_rows($result);
	if ($num_rows > 0) {
		$errorMessage = "Ya estas en pareja con ese amigo";
		include("showerrorcanvas.php");
		closeConnection($connection);
		return;
	}
	
	$SQL = "SELECT * FROM USER_APP2 WHERE fbid = $fbid";
	$ownerid = mysql_query($SQL) or die("MySQL-err.Query: " . $SQL . " - Error: (" . mysql_errno() . ") " . mysql_error());
	$num_rows = mysql_num_rows($ownerid);
	if ($num_rows == 1) {
		$groupownerrow = mysql_fetch_array($ownerid);
		$groupownerid = $groupownerrow["id"];
		$SQL = "SELECT * FROM USER_APP2 WHERE upper(inv_email) = upper($inv_email)";
		$result = mysql_query($SQL) or die("MySQL-err.Query: " . $SQL . " - Error: (" . mysql_errno() . ") " . mysql_error());
		$num_rows = mysql_num_rows($result);
		if ($num_rows == 0) {
			// nadie lo invito, mando email 
			// Inserto en la base
			$SQL = "INSERT INTO USER_APP2 (inv_email,origin, participation) VALUES($inv_email,2,0)"; // 2 es email invitation
			$result = mysql_query($SQL,$connection) or die("MySQL-err.Query: " . $SQL . " - Error: (" . mysql_errno() . ") " . mysql_error());
			$returnInsert = mysql_insert_id($connection);
			//log de invitaciones por email
			$SQL = "INSERT INTO EMAIL_INV_APP2 (groupowner_id,groupmember_id,followed, completed, creation_date) VALUES($groupownerid,$returnInsert,0,0,NOW())";
			$result = mysql_query($SQL,$connection) or die("MySQL-err.Query: " . $SQL . " - Error: (" . mysql_errno() . ") " . mysql_error());
			
			$mail = new PHPMailer(); // defaults to using php "mail()"
			$mail->From       = EMAIL_FROM_APP2;
			$mail->FromName   = EMAIL_FROM_NAME_APP2;
			$mail->FromName = str_replace('{SENDER_NAME}', $groupownerrow["fbname"], $mail->FromName);
			//Headers
			$mail->Subject    = APP2_SUMATE_SUBJECT;
			$mail->Subject = str_replace('{SENDER_NAME}', $groupownerrow["fbname"], $mail->Subject);
			$mail->AltBody    = BODY_ALT;
			$body             = $mail->getFile('../invitacion_app2.html');
			$body = str_replace('{SERVER_NAME}', SERVER_NAME, $body);
			$body = str_replace('{SENDER_NAME}', $groupownerrow["fbname"], $body);
			$link = 'https://www.facebook.com/'. PAGE_NAME . '?sk=app_'. APPLICATION2_ID;
			$link = $link . '&app_data=join_group|' . $groupownerid . '|' . $returnInsert . '|';
			$body = str_replace('{PAGE_LINK}', $link, $body);
			$body             = eregi_replace("[\]",'',$body);
			$mail->MsgHTML($body);
			$mail->AddAddress($email_address, $email_address);
			$mail_sent = $mail->Send();
			if(!$mail_sent) {
				/* borrar los datos, no se pudo invitar al amigo*/
				$SQL = "DELETE FROM USER_APP2 WHERE id = $returnInsert"; // 2 es email invitation
				$result = mysql_query($SQL,$connection) or die("MySQL-err.Query: " . $SQL . " - Error: (" . mysql_errno() . ") " . mysql_error());
			} 
		} else {
			$user_app1 = mysql_fetch_array($result);
			$user_app1id = $user_app1["id"];
			$SQL = "INSERT INTO EMAIL_INV_APP2 (groupowner_id,groupmember_id,followed, completed, creation_date) VALUES($groupownerid,$user_app1id,0,0,NOW())";
			$result = mysql_query($SQL,$connection) or die("MySQL-err.Query: " . $SQL . " - Error: (" . mysql_errno() . ") " . mysql_error());
			$mail = new PHPMailer(); // defaults to using php "mail()"
			$mail->From       = EMAIL_FROM_APP2;
			$mail->FromName   = EMAIL_FROM_NAME_APP2;
			$mail->FromName = str_replace('{SENDER_NAME}', $groupownerrow["fbname"], $mail->FromName);
			//Headers
			$mail->Subject    = APP2_SUMATE_SUBJECT;
			$mail->Subject = str_replace('{SENDER_NAME}', $groupownerrow["fbname"], $mail->Subject);
			$mail->AltBody    = BODY_ALT;
			$body             = $mail->getFile('../invitacion_app2.html');
			$body = str_replace('{SERVER_NAME}', SERVER_NAME, $body);
			$body = str_replace('{SENDER_NAME}', $groupownerrow["fbname"], $body);
			$link = 'https://www.facebook.com/'. PAGE_NAME . '?sk=app_'. APPLICATION2_ID;
			$link = $link . '&app_data=join_group|' . $groupownerid . '|' . $user_app1id . '|';
			$body = str_replace('{PAGE_LINK}', $link, $body);
			$body             = eregi_replace("[\]",'',$body);
			$mail->MsgHTML("$body");
			$mail->AddAddress($email_address,$email_address);
			$mail_sent = $mail->Send();
		}
	} 
	closeConnection($connection);
?>
<html>
<link href="../css/tdil.css" rel="stylesheet" type="text/css">
<style type="text/css">
<!--
body {
	background-image: url(../images/homeBaseApp2.jpg);
	background-repeat: no-repeat;
	background-position: center top;
	overflow:hidden !important;
}
#textContent{
	width: 700px;
	margin-top: 130px;
	margin-right: auto;
	margin-left: auto;
	text-align: center;
}
#textContent #title {
	text-align: center;
	margin-right: auto;
	margin-bottom: 30px;
	margin-left: auto;
	margin-top: 0px;
	height: 41px;
	width: 368px;
}
#contentSuccessfull {}
#contentError {

}
-->
</style>
<body>
<?php 
/* {PABLO} Esta pagina muestra el resultado de la invitacion por email*/
if ($mail_sent) { ?>
<div id="textContent">
	<div id="title"><img src="../images/tituloFelicitaciones.png" alt="Felicitaciones" width="245" height="41"></div>
	<div id="contentSuccessfull">Invitaciones enviadas</div>
<?php } else { ?>
<div id="textContent">
	<div id="title"><img src="../images/tituloErrores.png" alt="Uuuuooppsss" width="265" height="42"></div>
	<div id="contentError">No se pudo enviar el E-mail de invitaci&oacute;n</div>
<?php } 
$redirect = 'https://www.facebook.com/'. PAGE_NAME . '?sk=app_'. APPLICATION2_ID;
?>
	<div align="center" style="margin-top:25px;"><a href="<?php echo $redirect;?>" target="_top">Volver a la p&aacute;gina de inicio de la aplicaci&oacute;n</a></div>
</div>
</body>
</html>