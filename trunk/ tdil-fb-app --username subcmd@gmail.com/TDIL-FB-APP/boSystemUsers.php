<?php 
	include("include/headers.php");
	require("include/funcionesDB.php");
	require("include/boCheckLogin.php");
	if(isset($_GET['excel'])) {
		header("Content-type: application/vnd.ms-excel; name='excel'");
		header("Content-Disposition: filename=usuarios.xls");
		header("Pragma: no-cache");
		header("Expires: 0");
	} else {
		header("Content-type: text/html; charset=utf-8");
	}
	
	$query = "SELECT id, nombre, apellido, documento, edad, email, usuario, fechaCreacion,
		(select max(fechaCarga) from TICKETS WHERE systemuserID = SU.id) ultimaCarga,
		(select max(fechaLogin) from LOGINS WHERE systemuserID = SU.id) ultimoLogin,
		(select count(*) from TICKETS WHERE systemuserID = SU.id) cantidadCodigos 
		FROM SYSTEMUSER SU 
		ORDER BY fechaCreacion";
	$res = doSelect($query);
?>
<html>
<head>
<?php include("include/title_meta.php"); ?>
<link rel="icon" href="./favicon.ico" type="icon"/>
<!-- Contact Form CSS files -->
<link type='text/css' href='css/tdil.css' rel='stylesheet' media='screen' />
<style>
	#content #page {
	width: 850px;
	padding: 0px;
	margin-top: 20px;
}
</style>
<?php include("include/headerBO.php"); ?>
</head>
<body>
<div id="content">
	<div id="hello">Hola <span class="remarcado"><?php echo($_SESSION['boNombre']);?> <?php echo($_SESSION['boApellido']);?></span></div>
	<div id="portaMenu"><?php if(!isset($_GET['excel'])) { ?><?php include("include/menuBO.php"); ?><?php } ?></div>
	<div id="page">
		<table width="100%" cellpadding="5" cellspacing="5" border="0">
			<tr bgcolor="#CCCCCC">
				<td>Fecha de registro</td>
				<td>Fecha de ultimo login</td>
				<td>Cant. de c&oacute;digos</td>
				<td>Fecha de &Uacute;ltima carga</td>
				<td>Nombre</td>
				<td>Apellido</td>
				<td>Documento</td>
				<td>Edad</td>
				<td>Usuario</td>
				<td>E-mail</td>
			</tr>
<?php
	while ($iw = mysql_fetch_array($res)){
?>
	<tr>
		<td><?php echo $iw['fechaCreacion'] ?></td>
		<td><?php echo $iw['ultimoLogin'] ?></td>
		<td><a href="javascript:viewTickets('<?php echo $iw['documento'] ?>')"><?php echo $iw['cantidadCodigos'] ?></a></td>
		<td><?php 
			if (is_null($iw['ultimaCarga'])) {
   				echo '-';
			} else {
				echo $iw['ultimaCarga'];
			}
		?></td>
		<td><?php echo $iw['nombre'] ?></td>
		<td><?php echo $iw['apellido'] ?></td>
		<td><?php echo $iw['documento'] ?></td>
		<td><?php echo $iw['edad'] ?></td>
		<td><?php echo $iw['usuario'] ?></td>
		<td><?php echo $iw['email'] ?></td>
	</tr>
<?php 		
	} 
?>
</table>
<div align="center" style="margin-top:30px;">
<?php if(!isset($_GET['excel'])) { ?>
	<a href="boSystemUsers.php?excel=true"><img src="images/buttons/exportar.gif" width="168" height="51" border="0"></a>
<?php } ?>
</div>
<script>

$(document).ready(
		function(){
			$('#inicio').datetimepicker({
				showSecond: true,
				showMillisec: false,
				dateFormat: 'yy-mm-dd',
				timeFormat: 'hh:mm:ss',
				onSelect: function (selectedDateTime){
					var start = $(this).datetimepicker('getDate');
					$('#fin').datetimepicker('option', 'minDate', new Date(start.getTime()) );
				}
			});
			$('#fin').datetimepicker({
				showSecond: true,
				showMillisec: false,
				dateFormat: 'yy-mm-dd',
				timeFormat: 'hh:mm:ss',
				onSelect: function (selectedDateTime){
					var end= $(this).datetimepicker('getDate');
					$('#inicio').datetimepicker('option', 'maxDate', new Date(end.getTime()) );
				}
			});
		}
	)
</script>
</div>
<form method="POST" id="viewTicketsForms" action="boTickets.php">
<input type="hidden" name="documento" id="documento" value="">
</form>
<script>
function viewTickets(doc) {
	document.getElementById('documento').value = doc;
	$('#viewTicketsForms').submit();
}
</script>
</body>
</html>