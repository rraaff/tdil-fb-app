<?php 
	include("include/headers.php");
	require("include/funcionesDB.php");
	include_once('phpmail/class.phpmailer.php');
	include("include/constantes_mail.php");
	
	// Inicio conexion
	$connection = mysql_connect(DB_SERVER,DB_USER, DB_PASS) or die ("Problemas en la conexion");
	mysql_select_db(DB_NAME,$connection);

	$email = $_POST['email'];	
	
		$mail = new PHPMailer(); // defaults to using php "mail()"
		
		//$mail->Mailer = 'smtp';
		$mail->SMTPDebug = true;
		$mail->From       = EMAIL_FROM;
		$mail->FromName   = EMAIL_FROM_NAME;
		//Headers
		$headers['X-Mailer'] = 'X-Mailer: PHP/' . phpversion();
		$mail -> AddCustomHeader($headers);
		$mail->Subject    = COMPARTIR_SUBJECT;
		$mail->AltBody    = COMPARTIR_BODY_ALT;
		$body             = $mail->getFile('compartir.html');
		$body = str_replace('{SERVER_NAME}', SERVER_NAME, $body);
		$mail->MsgHTML("$body");
		$mail->AddAddress("$email");
		if(!$mail->Send()) {
			$err_mail = $mail->ErrorInfo;
			$output = '{ "success": "no", "error": "Error enviando el email." }';
		} else {
			$output = '{ "success": "yes", "error": "" }';
		}
	// Cierre conexion
	mysql_close($connection);
	//validar todo lo que haga falta, campo a campo
	
	$output = str_replace("\r", "", $output);
	$output = str_replace("\n", "", $output);
	echo $output;
?>