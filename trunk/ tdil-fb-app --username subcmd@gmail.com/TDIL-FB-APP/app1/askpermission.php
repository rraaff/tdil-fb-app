<?php
/* Esta pagina le avisa al usuario que la aplicacion necesita acceder a sus datos publicos*/
?>
<html>
<body>
Esta aplicacion necesita acceder a tus datos publicos
<?php 
$encodedParams = urlencode(json_encode($app_data)); // Encode the parameters to a JSON string for use in a URL query string
$url = $facebook->getLoginUrl(array(
		'redirect_uri' => $APPLICATION_URL . '/logincallback.php?app_data=' . $app_data
));
echo '<a href="' . $url . '" target="_top">Login</a><br/> ';
?>
</body>
</html>