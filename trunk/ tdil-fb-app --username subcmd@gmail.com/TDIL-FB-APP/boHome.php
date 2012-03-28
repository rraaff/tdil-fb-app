<?php 
	include("include/headers.php");
	require("include/funcionesDB.php");
	require("include/boCheckLogin.php");
?>
<html>
<head>
<?php include("include/title_meta.php"); ?>
<!-- Contact Form CSS files -->
<link type='text/css' href='css/tdil_bo.css' rel='stylesheet' media='screen' />
<?php include("include/headerBO.php"); ?>
</head>
<body>
<div id="content">
	<div id="header"></div>
    <div id="block">
		<div id="promoTitle"><h1>Bienvenido al BackOffice de la promoci&oacute;n <span class="remarcado">Degustaci&oacute;n exclusiva.</span></h1></div>
		<div id="hello">Hola <span class="remarcado"><?php echo($_SESSION['boNombre']);?> <?php echo($_SESSION['boApellido']);?></span></div>
		<div id="portaMenu"><?php include("include/menuBO.php"); ?></div>
		<div id="page">Seleccione una acci&oacute;n</div>
	</div>
</div>
</body>
</html>