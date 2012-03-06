<?php 
	include("include/headers.php");
	require("include/funcionesDB.php");
	require("include/boCheckLogin.php");
	if($_POST['excel'] == 'true') {
		header("Content-type: application/vnd.ms-excel; name='excel'");
		header("Content-Disposition: filename=tickets.xls");
		header("Pragma: no-cache");
		header("Expires: 0");
	} else {
		header("Content-type: text/html; charset=utf-8");
	}
	
	if ($_SERVER['REQUEST_METHOD'] == 'POST') {
		$connection = mysql_connect(DB_SERVER,DB_USER, DB_PASS) or die ("Problemas en la conexion");
		mysql_select_db(DB_NAME,$connection);
		$whereAnd = "";
		if ($_POST['documento'] != '') {
			$documento = quote_smart($_POST['documento'], $connection);
			$whereAnd = $whereAnd . " SY.documento = $documento AND ";
		}
		if ($_POST['apellido'] != '') {
			$apellido = quote_smart($_POST['apellido'], $connection);
			$whereAnd = $whereAnd . " SY.apellido = $apellido AND ";
		}
		if ($_POST['ticket'] != '') {
			$ticket = quote_smart($_POST['ticket'], $connection);
			$whereAnd = $whereAnd . " TI.ticket = $ticket AND ";
		}
		if ($_POST['fechaDesde'] != '') {
			$fechaDesde = quote_smart($_POST['fechaDesde'], $connection);
			$whereAnd = $whereAnd . " UNIX_TIMESTAMP(TI.fechaCarga) >= UNIX_TIMESTAMP($fechaDesde) AND ";
		}
		if ($_POST['fechaHasta'] != '') {
			$fechaHasta = quote_smart($_POST['fechaHasta'], $connection);
			$whereAnd = $whereAnd . " UNIX_TIMESTAMP(TI.fechaCarga) <= UNIX_TIMESTAMP($fechaHasta) AND ";
		}
		$order = $_POST['order'];
		$orderBy = "documento, fechaCarga";
		if ($order == 1) {
			$orderBy = "documento, fechaCarga";
		} else {
			if ($order == 2) {
				$orderBy = "apellido, fechaCarga";
			} else {
				if ($order == 3) {
					$orderBy = "fechaCarga, apellido";
				}
			}	
		}
	
		$query = "SELECT documento, nombre, apellido, fechaCarga, ticket FROM TICKETS TI, SYSTEMUSER SY WHERE $whereAnd TI.systemuserID = SY.id ORDER BY $orderBy";
		$res = mysql_query($query,$connection);
		mysql_close($connection);
	}
?>
<html>
<head>
<?php include("include/title_meta.php"); ?><link rel="icon" href="./favicon.ico" type="icon"/>
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
	<div id="portaMenu"><?php if($_POST['excel'] != 'true') { ?><?php include("include/menuBO.php"); ?><?php } ?></div>
	<div id="page">
		<?php if($_POST['excel'] != 'true') { ?>
		<form method="POST" action="boTickets.php" id="searchForm">
		<input type="hidden" name="excel" id="excel" value="">
		<table width="100%" cellpadding="5" cellspacing="5" border="0">
			<tr>
				<td>Documento: </td><td><input name="documento" type="text" value="<?php echo $_POST['documento']?>"></td><td>Apellido:</td><td><input name="apellido" type="text" value="<?php echo $_POST['apellido']?>"></td>
			</tr>
			<tr>
				<td>Ticket: </td><td><input name="ticket" type="text" value="<?php echo $_POST['ticket']?>"></td><td>&nbsp;</td><td>&nbsp;</td>
			</tr>
			<tr>
				<td>Fecha desde: </td><td><input name="fechaDesde" id="fechaDesde" type="text" value="<?php echo $_POST['fechaDesde']?>"></td><td>Fecha hasta:</td><td><input name="fechaHasta" id="fechaHasta" type="text" value="<?php echo $_POST['fechaHasta']?>"></td>
			</tr>
			<tr>
				<td>Ordenar por: </td>
				<td><select name="order">
						<option value="1" <?php if ($_POST['order'] == 1) {echo "selected";} ?>>Documento</option>
						<option value="2" <?php if ($_POST['order'] == 2) {echo "selected";} ?>>Apellido</option>
						<option value="3" <?php if ($_POST['order'] == 3) {echo "selected";} ?>>Fecha de carga</option>
					</select></td><td>&nbsp;</td><td>&nbsp;</td>
			</tr>
			<tr>
				<td colspan="4" align="center"><input type="submit" value="Buscar" onclick="document.getElementById('excel').value = 'false';"></td>
			</tr>
		</table>
		</form>
		<?php } ?>
		<table width="100%" cellpadding="5" cellspacing="5" border="0">
			<tr bgcolor="#CCCCCC">
				<td>Documento</td>
				<td>Nombre</td>
				<td>Apellido</td>
				<td>Fecha de carga</td>
				<td>Codigo</td>
			</tr>
<?php
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
	while ($ti = mysql_fetch_array($res)){
?>
	<tr>
		<td><?php echo $ti['documento'] ?></td>
		<td><?php echo $ti['nombre'] ?></td>
		<td><?php echo $ti['apellido'] ?></td>
		<td><?php echo $ti['fechaCarga'] ?></td>
		<td><?php echo $ti['ticket'] ?></td>
	</tr>
<?php 		
	} 
}
?>
</table>
</div>
<div align="center" style="margin-top:30px;">
<?php if($_POST['excel'] != 'true') { ?>
	<a href="javascript:exportExcel()"><img src="images/buttons/exportar.gif" width="168" height="51" border="0"></a>
<?php } ?>
</div>
</div>
<script>
$(document).ready(
		function(){
			$('#fechaDesde').datepicker({
				dateFormat: 'yy-mm-dd'
			});
			$('#fechaHasta').datepicker({
				dateFormat: 'yy-mm-dd'
			});
			
		}
	);

function exportExcel() {
	document.getElementById('excel').value = 'true';
	$('#searchForm').submit();
}
</script>
</body>
</html>