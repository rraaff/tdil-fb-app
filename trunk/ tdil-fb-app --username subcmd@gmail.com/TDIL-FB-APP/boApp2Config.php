<?php 
	include("include/headers.php");
	require("include/funcionesDB.php");
	require("include/boCheckLogin.php");
	
	$connection = mysql_connect(DB_SERVER,DB_USER, DB_PASS) or die ("Problemas en la conexion");
	mysql_select_db(DB_NAME,$connection);
	$message = '';
	if ($_SERVER['REQUEST_METHOD'] == 'POST'){
		// Inicio conexion
	
		$invitation_days = $_POST['invitation_days'];
		$email_daily_quota = $_POST['email_daily_quota'];
		$fb_daily_quota = $_POST['fb_daily_quota'];
	
		$invitation_days = quote_smart($invitation_days, $connection);
		$email_daily_quota = quote_smart($email_daily_quota, $connection);
		$fb_daily_quota = quote_smart($fb_daily_quota, $connection);
		$show_winner = quote_smart($show_winner, $connection);
	
		$SQL = "UPDATE CONFIG_APP2 SET invitation_days = $invitation_days, email_daily_quota = $email_daily_quota
		, fb_daily_quota = $fb_daily_quota";
		$result = mysql_query($SQL) or die("MySQL-err.Query: " . $SQL . " - Error: (" . mysql_errno() . ") " . mysql_error());	
		$message = 'Cambios guardados';
	} 
	
	$SQL = "SELECT * FROM CONFIG_APP2";
	$result = mysql_query($SQL) or die("MySQL-err.Query: " . $SQL . " - Error: (" . mysql_errno() . ") " . mysql_error());
	$aRow = mysql_fetch_array( $result );
	mysql_close($connection);
?>
<html>
<head>
<?php include("include/title_meta.php"); ?>
<!-- Contact Form CSS files -->
<link type='text/css' href='css/tdil_bo.css' rel='stylesheet' media='screen' />
<?php include("include/headerBO.php"); ?>
<style type="text/css" title="currentStyle">
			@import "css/demo_page.css";
			@import "css/demo_table.css";
			@import "css/TableTools.css";
		</style>
		<script type="text/javascript" src="js/jquery.dataTables.js"></script>
			<script type="text/javascript" charset="utf-8" src="js/ZeroClipboard.js"></script>
		<script type="text/javascript" charset="utf-8" src="js/TableTools.min.js"></script>
<style>
	#content #page {
	width: 850px;
	padding: 0px;
	margin-top: 20px;
}
</style>	
</head>
<body id="dt_example">

<div id="content">
	<div id="header"></div>
    <div id="block">
		<div id="promoTitle"><h1>Bienvenido al BackOffice de la promoci&oacute;n <span class="remarcado">ARLISTAN De a 2.</span></h1></div>
        <div id="hello">Hola <span class="remarcado"><?php echo($_SESSION['boNombre']);?> <?php echo($_SESSION['boApellido']);?></span></div>
        <div id="portaMenu"><?php include("include/menuBO.php"); ?></div>
        <div id="page">
            <div align="center">
            	<form method="POST" action="<?php echo $_SERVER['PHP_SELF'] ?>">
            	<h2>Configuracion de la Aplicacion</h2>
                <table width="350" cellspacing="10" cellpadding="0" align="center" border="0">
                    <tr>
                        <td>Duracion de las invitaciones (en dias):</td>
                        <td><input type="text" name="invitation_days" id="invitation_days" value="<?php echo $aRow['invitation_days'];?>">
                        </td>
                    </tr>
                    <tr>
                        <td>Emails diarios:</td>
                        <td><input type="text" name="email_daily_quota" id="email_daily_quota" value="<?php echo $aRow['email_daily_quota'];?>">
                        </td>
                    </tr>
                    <tr>
                        <td>App requests diarios:</td>
                        <td><input type="text" name="fb_daily_quota" id="fb_daily_quota" value="<?php echo $aRow['fb_daily_quota'];?>">
                        </td>
                    </tr>
                    <tr>
                        <td colspan="2" align="center">
                            <input type="submit" value="Salvar">
                        </td>
                    </tr>
                    <tr>
                        <td colspan="2" align="center">
                            <?php echo $message;?>
                        </td>
                    </tr>
                </table>
                </form>		
            </div>
        </div>
	</div>
</div>
</body>
</html>