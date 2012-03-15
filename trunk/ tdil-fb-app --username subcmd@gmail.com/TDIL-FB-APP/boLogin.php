<?php 
	include("include/headers.php");
	require("include/funcionesDB.php");
	
	$errorMessage = "";
	if ($_SERVER['REQUEST_METHOD'] == 'POST'){
		// Inicio conexion
		$connection = mysql_connect(DB_SERVER,DB_USER, DB_PASS) or die ("Problemas en la conexion");
		
		mysql_select_db(DB_NAME,$connection);
		
		$usuario = $_POST['usuario'];
		$password = $_POST['password'];
		
		$usuario = quote_smart($usuario, $connection);
		$password = quote_smart($password, $connection);
		
		$SQL = "SELECT * FROM BOUSER WHERE usuario = $usuario AND password = $password";
		$result = mysql_query($SQL);
		$num_rows = mysql_num_rows($result);
		
		if ($result) {
			if ($num_rows > 0) {
				session_start();
				$user = mysql_fetch_array($result);
				$_SESSION['boLogin'] = "1";
				$_SESSION['boNombre'] = $user['nombre'];
				$_SESSION['boApellido'] = $user['apellido'];
				header ("Location: boHome.php");
			} else {
				$errorMessage = "El usuario no es valido";
			}
		} else {
			$errorMessage = "Error de conexion";
		}
		
		closeConnection($connection);
	}
?>
<html>
<head>
<?php include("include/title_meta.php"); ?>
<link rel="icon" href="./favicon.ico" type="icon"/>
<!-- Contact Form CSS files -->
<link type='text/css' href='css/tdil.css' rel='stylesheet' media='screen' />
<style>
body {
	background-image: url(images/fondoLoginBO.jpg);
}
input {
	background-color: transparent;
	border: none;
	width:280px;
	color:#aa0410;
	font-family: "Univers LT", Geneva, Arial, Helvetica, sans-serif;
	font-size: 12px;
	line-height: normal;
}
#login {
	height: auto;
	width: 800px;
	margin-right: auto;
	margin-left: auto;
}
#login #field1 {
	width: 300px;
	margin-left: 340px;
	margin-top: 95px;
}
#login #field2 {
	width: 300px;
	margin-top: 25px;
	margin-left: 340px;
}
#login #errorMessage {
	text-align: center;
	padding-left: 150px;
	margin-top: 50px;
}
#login .ingresarButton {
	background-image: url(images/buttons/ingresar.gif);
	background-repeat: no-repeat;
	background-position: center center;
	height:51px;
	width:205px;
	cursor: hand;
	font-size: 10px;
	margin: 0px;
	padding: 0px;
	/*border:1px dashed #212021;*/
	border:none;
}
#login #aclaracion {
	text-align: center;
	width: 350px;
	margin-right: auto;
	margin-left: auto;
	margin-top: 220px;
	padding-left: 60px;
	height: auto;
}
#login #buttonAreas {
	width: 250px;
	margin-top: 20px;
	margin-left: 400px;
}
</style>
</head>
<body>

<FORM NAME ="form1" METHOD ="POST" ACTION ="boLogin.php">
<div id="login">
	<div id="aclaracion">
		<p>Bienvenido al BackOffice de la promoci&oacute;n<br/><span class="remarcado">RECOMPENSA TU FORMA DE DISFRUTAR LA VIDA.</span></p>
	</div>
	<div id="field1"><INPUT TYPE = 'TEXT' Name ='usuario'  value="" maxlength="20"></div>
	<div id="field2">
		<input type = 'password' name ='password'  value="" maxlength="20" AUTOCOMPLETE="off">
	</div>
	<div id="errorMessage"><span class="remarcado"><?PHP print $errorMessage;?></span></div>
	<div id="buttonAreas"><input type="submit" name="Submit1" value=" " class="ingresarButton"></div>
</div>
</FORM>

</body>
</html>