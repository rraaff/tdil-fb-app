<?php 
	include("include/headers.php");
	require("include/funcionesDB.php");
	require("include/boCheckLogin.php");
	
	$connection = mysql_connect(DB_SERVER,DB_USER, DB_PASS) or die ("Problemas en la conexion");
	mysql_select_db(DB_NAME,$connection);
	
	$SQL = "SELECT 'Usuarios' AS DATA_DESC, COUNT(1) AS DATA_VALUE FROM USER_APP2
		UNION ALL
		SELECT 'Grupos formados', COUNT(1) FROM GROUP_APP2";

	$result = mysql_query($SQL) or die("MySQL-err.Query: " . $SQL . " - Error: (" . mysql_errno() . ") " . mysql_error());
	$num_rows = mysql_num_rows($result);	

?>
<html>
<head>
<?php include("include/title_meta.php"); ?>
<!-- Contact Form CSS files -->
<link type='text/css' href='css/tdil_bo.css' rel='stylesheet' media='screen' />
<?php include("include/headerBO.php"); ?>
<style>
	#content #page {
	width: 850px;
	padding: 0px;
	margin-top: 20px;
	line-height: 30px;
}
</style>	
</head>
<body>
<div id="content">
	<div id="header"></div>
    <div id="block">
		<div id="promoTitle"><h1>Bienvenido al BackOffice de la promoci&oacute;n <span class="remarcado">ARLISTAN De a 2.</span></h1></div>
        <div id="hello">Hola <span class="remarcado"><?php echo($_SESSION['boNombre']);?> <?php echo($_SESSION['boApellido']);?></span></div>
        <div id="portaMenu"><?php include("include/menuBO.php"); ?></div>
        <div id="page">
        	<h2>Estado de la aplicacion: De a 2</h2>
            <div align="center">
            <table width="350" cellspacing="10" cellpadding="0" align="center" border="0">
                <?php while ( $aRow = mysql_fetch_array( $result ) ) { ?>
                    <tr><td><?php echo $aRow["DATA_DESC"] ?></td><td><?php echo $aRow["DATA_VALUE"] ?></td></tr>
                <?php }	?>
            </table>
            </div>
		</div>
	</div>
</div>
<?php 	closeConnection($connection); ?>
</body>
</html>