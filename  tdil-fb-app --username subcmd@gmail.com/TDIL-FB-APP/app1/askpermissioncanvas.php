<?php
/* Esta pagina le avisa al usuario que la aplicacion necesita acceder a sus datos publicos
"tus amigos te estan esperando, agregate al grupo"
*/
?>
<html>
<body>
<?php 
$url = $facebook->getLoginUrl(array(
		'redirect_uri' => $APPLICATION_URL . '/logincallbackcanvas.php'
));
echo '<a href="' . $url . '" target="_top"><img src="../images/askpermision.jpg" width="795" height="780" border="0"></a>';
?>
</body>
</html>