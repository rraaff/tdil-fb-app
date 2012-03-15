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
<link type='text/css' href='css/tdil_bo.css' rel='stylesheet' media='screen' />
</head>
<body>
<div id="content">
	<div id="header"></div>
	<div id="promoTitle"><h1>Bienvenido al BackOffice de la promoci&oacute;n <span class="remarcado">ARLISTAN 10 + 10 = 17.</span></h1></div>
  <FORM NAME ="form1" METHOD ="POST" ACTION ="boLogin.php">
    <div id="login">
      <div id="field1">Usuario: <INPUT TYPE = 'TEXT' Name ='usuario'  value="" maxlength="20"></div>
        <div id="field2">Clave: <input type = 'password' name ='password'  value="" maxlength="20" AUTOCOMPLETE="off"></div>
        <div id="errorMessage"><span class="remarcado"><?PHP print $errorMessage;?></span></div>
        <div id="buttonAreas"><input type="submit" name="Submit1" value="Ingresar" class="ingresarButton"></div>
    </div>
    </FORM>
</div>
</body>
</html>