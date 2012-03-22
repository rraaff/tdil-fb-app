<html>
<head>
<link href="../css/tdil.css" rel="stylesheet" type="text/css">
<style type="text/css">
<!--
body {
	background-image: url(../images/askpermisionCanvas.jpg);
	background-repeat: no-repeat;
	background-position: left top;
	overflow:hidden;
}
</style>
</head>
<body>
<?php 
$url = $facebook->getLoginUrl(array(
		'redirect_uri' => APPLICATION1_URL . '/logincallbackcanvas.php'
));
echo '<a href="' . $url . '" target="_top"><img src="../images/null.gif" width="740" height="450" border="0"></a>';
?>
</body>
</html>