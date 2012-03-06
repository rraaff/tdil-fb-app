<?php 
	include("include/headers.php");
	require("include/funcionesDB.php");
	require("include/boCheckLogin.php");
	
	if ($_SERVER['REQUEST_METHOD'] == 'POST' 
			&& $_FILES['imagen']['size'] > 0){
		// Inicio conexion
		$connection = mysql_connect(DB_SERVER,DB_USER, DB_PASS) or die ("Problemas en la conexion");
		mysql_select_db(DB_NAME,$connection);
		
		$descripcion = $_POST['descripcion'];
		$mensaje = $_POST['mensaje'];
		$inicio = $_POST['inicio'];
		$fin = $_POST['fin'];
		
		$descripcion = quote_smart($descripcion, $connection);
		$mensaje = quote_smart($mensaje, $connection);
		$inicio = quote_smart($inicio, $connection);
		$fin = quote_smart($fin, $connection);
		
		$fileName = quote_smart($_FILES['imagen']['name'], $connection);
		$tmpName  = $_FILES['imagen']['tmp_name'];
		$fileSize = quote_smart($_FILES['imagen']['size'], $connection);
		$fileType = quote_smart($_FILES['imagen']['type'], $connection);
		$fp      = fopen($tmpName, 'r');
		$content = fread($fp, filesize($tmpName));
		$content = addslashes($content);
		fclose($fp);
		
		$insertTicket = "INSERT INTO INSTANT_WIN (descripcion, mensaje, inicio, fin, name, type, size, content) 
			VALUES ($descripcion, $mensaje, $inicio, $fin, $fileName, $fileType, $fileSize, '$content')";
		$res = mysql_query($insertTicket,$connection);// or die ("Error en insert ".mysql_error()."\n".$query);
		
		mysql_close($connection);
		
		header("Location: boPremiosInstantaneos.php");
	} else {
	
	$query = "SELECT IW.*, UNIX_TIMESTAMP(IW.FIN) FIN_UNIX, TI.ticket, SU.id systemUserID, SU.nombre, SU.apellido
		FROM INSTANT_WIN IW 
		LEFT JOIN TICKETS TI ON (IW.ticketID = TI.id) 		
		LEFT JOIN SYSTEMUSER SU ON (TI.systemUserID = SU.id) 
		ORDER BY IW.inicio";
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
<script>
$(document).ready(
		function(){
			$("#altaIWForm").validate({
				rules: { descripcion: {required: true},
						mensaje: {required: true},
						imagen: {required: true},
						inicio: {required: true},
						fin: {required: true}
				},
				messages: {
					descripcion: {required: "Ingrese la descripcion."}, 
					mensaje: {required: "Ingrese el mensaje."},
					imagen: {required: "Seleccione la imagen."},
					inicio: {required: "Ingrese la fecha inicio."},
					fin: {required: "Ingrese la fecha fin."},
				}
			});
		}
	);
</script>
</head>
<body>
<div id="content">
	<div id="hello">Hola <span class="remarcado"><?php echo($_SESSION['boNombre']);?> <?php echo($_SESSION['boApellido']);?></span></div>
	<div id="portaMenu"><?php include("include/menuBO.php"); ?></div>
	<div id="page">

	<form action="boPremiosInstantaneos.php" name="altaIWForm" id="altaIWForm" method="POST" enctype="multipart/form-data">
		<input type="hidden" name="MAX_FILE_SIZE" value="10000000">
		<div align="center">
			<table cellpadding="5" cellspacing="5" border="0" align="center">
				<tr><td>Descripci&oacute;n</td><td><input type="text" name="descripcion"></td><td class="remarcado"></td></tr>
				<tr><td>Mensaje</td><td><input type="text" name="mensaje"></td><td class="remarcado"></td></tr>
				<tr><td>Imagen</td><td><input type="file" name="imagen" id="imagen"></td><td class="remarcado"></td></tr>
				<tr><td>Desde</td><td><input type="text" name="inicio" id="inicio"></td><td class="remarcado"></td></tr>
				<tr><td>Hasta</td><td><input type="text" name="fin" id="fin"></td><td class="remarcado"></td></tr>
				<tr><td colspan="2" align="center"><input type="submit" name="submit2" value=" " class="saveButton"></td></tr>
			</table>
		</div>
	</form>

	<table width="100%" cellpadding="5" cellspacing="5" border="0">
		<tr bgcolor="#CCCCCC">
			<td>Id</td>
			<td>Descripci&oacute;n</td>
			<td>Desde</td>
			<td>Hasta</td>
			<td>Estado</td>
			<td>Ganador</td>
			<td>Codigo</td>
			<td>Borrar</td>
		</tr>
<?php
	$today = time();
	while ($iw = mysql_fetch_array($res)){
?>
	<tr>
		<td><?php echo $iw['id'] ?></td>
		<td><?php echo $iw['descripcion'] ?></td>
		<td><?php echo $iw['inicio'] ?></td>
		<td><?php echo $iw['fin'] ?></td>
		<td><?php 
			if (is_null($iw['ticketID'])) {
				if ($iw['FIN_UNIX'] > $today) {
     				echo 'Pendiente';
				} else {
				    echo 'Vencido';
				}
			} else {
				echo 'Adjudicado';
			}
		?></td>
		<td><?php 
			if (!is_null($iw['nombre'])) { ?>
				<a href="javascript:showWinner('<?php echo $iw['ticketID'];?>')">
				<?php echo $iw['nombre'];?> <?php echo $iw['apellido'];?> 
				</a>
			<?php } else {
				echo '-';
			}
		?></td>
		<td><?php 
			if (is_null($iw['ticketID'])) { ?>
     				-
     		<?php 
				} else {
				    echo $iw['ticket'];
				}
			?>
		</td>
		<td><?php 
			if (is_null($iw['ticketID'])) {
				if ($iw['FIN_UNIX'] > $today) {
     				?>
     				<a href="doBorrarPremioInstantaneo.php?id=<?php echo $iw['id'];?>">
						Borrar 
					</a>
     				<?php 
				} else {
				    echo '-';
				}
			} else {
				echo '-';
			}
		?></td>
	</tr>
<?php 		
	} 
?>
</table>
<script>

function showWinner(ticketID) {
	$.get("getTicketWinner.php?ticketID=" + ticketID, function(data){
		// create a modal dialog with the data
		$(data).modal({
			overlayId: 'winner-overlay',
			containerId: 'winner-container'
		});
	});
}

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
</div>
</body>
</html>
<?php } ?>