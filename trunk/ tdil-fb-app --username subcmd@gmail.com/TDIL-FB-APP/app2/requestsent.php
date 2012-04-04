<?php
/* {PABLO} se usa para redirigir luego de las invitaciones por facebook */

	include("../include/headers.php");
	require("../include/funcionesDB.php");
	require '../include/facebook.php';
	include("../include/app2constants.php"); 
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
	width: 600px;
	margin-top: 130px;
	margin-right: auto;
	margin-left: auto;
	text-align: center;
}
-->
</style>

<body>
<div id="textContent">
	<div id="title"><img src="../images/tituloFelicitaciones.png" alt="Mi grupo de amigos" width="245" height="41"></div>
	<div id="contentSuccessfull">Las invitaciones han sido enviadas.</div>
<?php 
$redirect = 'https://www.facebook.com/'. PAGE_NAME . '?sk=app_'. APPLICATION2_ID;
?>
	<div align="center" style="margin-top:25px;"><a href="<?php echo $redirect;?>" target="_top">Volver a la p&aacute;gina de inicio de la aplicaci&oacute;n</a></div>
</body>
</html>