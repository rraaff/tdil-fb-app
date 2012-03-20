<?php
/* {PABLO} se usa para redirigir luego de las invitaciones por facebook */

	include("../include/headers.php");
	require("../include/funcionesDB.php");
	require '../include/facebook.php';
	include("../include/app1constants.php"); 
?>
<html>
<body>
Las invitaciones han sido enviadas.<br>

<?php 
$redirect = 'https://www.facebook.com/'. PAGE_NAME . '?sk=app_'. APPLICATION1_ID;
?>
<a href="<?php echo $redirect;?>" target="_top">Volver a la home de la app</a>
</body>
</html>