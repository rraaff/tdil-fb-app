<?php
/* Esta pagina le avisa al usuario que la aplicacion necesita acceder a sus datos publicos*/
?>
<html>
<body>
<style>

</style>

<?php 
$encodedParams = urlencode(json_encode($app_data)); // Encode the parameters to a JSON string for use in a URL query string
$url = $facebook->getLoginUrl(array(
		'redirect_uri' => $APPLICATION1_URL . '/logincallback.php?app_data=' . $app_data
));
echo '<a href="' . $url . '" target="_top"><img src="../images/askpermision.jpg" width="795" height="780" border="0"></a> ';
?>
</body>
</html>