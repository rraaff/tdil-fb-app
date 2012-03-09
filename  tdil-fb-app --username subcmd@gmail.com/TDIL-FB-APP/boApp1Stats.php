<?php 
	include("include/headers.php");
	require("include/funcionesDB.php");
	require("include/boCheckLogin.php");
?>
<html>
<head>
<?php include("include/title_meta.php"); ?>
<!-- Contact Form CSS files -->
<link type='text/css' href='css/tdil.css' rel='stylesheet' media='screen' />
<?php include("include/headerBO.php"); ?>
</head>
<body>
<div id="content">
	<div id="hello">Hola <span class="remarcado"><?php echo($_SESSION['boNombre']);?> <?php echo($_SESSION['boApellido']);?></span></div>
	<div id="portaMenu"><?php include("include/menuBO.php"); ?></div>
	<div id="page">
		Aca van a ir las variables de la app1.<br>
		Cantidad de invitados originales<br>
		Cantidad de grupos formados<br>
		Cantidad de usuarios en la base<br>
		Si hay ganador, los datos de este<br>
		Etc, etc, todo lo que se me ocurra, hasta podria tener una evolucion, es decir, una consulta de usuarios ingresados por dia?
	</div>
</div>
</body>
</html>