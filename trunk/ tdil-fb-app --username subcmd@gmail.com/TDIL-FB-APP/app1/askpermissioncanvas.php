<?php
/* {PABLO} Esta pagina le avisa al usuario que la aplicacion necesita acceder a sus datos publicos*/
?>
<html>
<body>
Esta aplicacion necesita acceder a tus datos publicos
<?php 
$url = $facebook->getLoginUrl(array(
		'redirect_uri' => $APPLICATION_URL . '/logincallbackcanvas.php'
));
echo '<a href="' . $url . '" target="_top">Login</a><br/> ';
?>
</body>
</html>