<html>
<link href="../css/tdil.css" rel="stylesheet" type="text/css">
<style type="text/css">
<!--
body {
	background-image: url(../images/cleanBase.jpg);
	background-repeat: no-repeat;
	background-position: left top;
	overflow:hidden;
}
#textContent{
	width: 700px;
	margin-top: 180px;
	margin-right: auto;
	margin-left: auto;
	text-align: center;
}
#textContent #title {
	text-align: center;
	margin-right: auto;
	margin-bottom: 30px;
	margin-left: auto;
	margin-top: 0px;
	height: 41px;
	width: 368px;
}
#contentSuccessfull {}
#contentError {

}
-->
</style>
<body>
<div id="textContent">
	<div id="title"><img src="../images/tituloErrores.png" alt="Uuuuooppsss!" width="265" height="42"></div>
	<div id="contentError"><?php echo $errorMessage;?></div>
<?php 
$redirect = 'https://www.facebook.com/'. PAGE_NAME . '?sk=app_'. APPLICATION2_ID;
?>
	<div align="center" style="margin-top:25px;"><a href="<?php echo $redirect;?>" target="_top">Volver a la p&aacute;gina de inicio de la aplicaci&oacute;n</a></div>
</div>
</body>
</html>
