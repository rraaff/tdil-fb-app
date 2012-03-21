<html>
<link href="../css/tdil.css" rel="stylesheet" type="text/css">
<style type="text/css">
<!--
body {
	background-image: url(../images/cleanBase.jpg);
	background-repeat: no-repeat;
	background-position: left top;
}
#portaTable {
	background-image: url(../images/headerTablas.jpg);
	background-repeat: no-repeat;
	background-position: center top;
	padding: 10px;
	font-size: 12px;
	margin-top: 10px;
	border-bottom-width: 1px;
	border-bottom-style: solid;
	border-bottom-color: #ECECEC;
}
-->
</style>
<body>
<?php
	include("../include/headers.php");
	require("../include/funcionesDB.php");
	
	require '../include/facebook.php';
	include("../include/app1constants.php"); 
	// Create our Application instance (replace this with your appId and secret).
	$facebook = new Facebook(array(
			'appId'  => APPLICATION1_ID,
			'secret' => APPLICATION1_SECRET,
	));
	$app_token = get_app_access(APPLICATION1_ID,APPLICATION1_SECRET);
	// Get User ID
	$user = $facebook->getUser();
	if ($user) {
		$connection = mysql_connect(DB_SERVER,DB_USER, DB_PASS) or die ("Problemas en la conexion");
		mysql_select_db(DB_NAME,$connection);
		$fbid = quote_smart($user, $connection);
		
		$SQL = "SELECT ua.inv_email, ua.fbname, ua.fbusername FROM GROUP_APP1 ga, USER_APP1 ua
			WHERE ga.groupmember_fbid != 0
			AND ga.groupmember_fbid = ua.fbid AND ga.groupowner_fbid = " . $fbid;
		$result = mysql_query($SQL,$connection) or die("MySQL-err.Query: " . $SQL . " - Error: (" . mysql_errno() . ") " . mysql_error());
	?>
	
<div id="textContent">
	<div id="title"><img src="../images/tituloMiGrupodeAmigos.png" alt="Mi grupo de amigos" width="368" height="41"></div>
	<div id="portaTable">
		<table width="680" border="0" align="center" cellpadding="5">
			<tr>
				<td colspan="3" align="center"><h2>Amigos en Facebook confirmados en mi grupo</h2></td>
			</tr>
			<tr>
				<td class="simpleText">Nombre</td>
				<td class="simpleText">Usuario de facebook</td>
				<td class="simpleText">E-mail de invitaci&oacute;n</td>
			</tr>
			<?php
				while ($iw = mysql_fetch_array($result)){
			?>
				<tr>
					<td class="simpleText"><?php echo $iw['fbname'] ?></td>
					<td class="simpleText"><?php echo $iw['fbusername'] ?></td>
					<td class="simpleText"><?php 
						if (is_null($iw['inv_email'])) {
							echo '-';
						} else {
							echo $iw['inv_email'];
						}
					?></td>
				</tr>
			<?php 		
				} 
			?>
		</table>
	</div>
	<div id="portaTable">
		<?php
			$SQL = "SELECT friend.inv_email
				FROM EMAIL_INV_APP1 em, USER_APP1 owner, USER_APP1 friend
				where em.groupowner_id = owner.id
				and em.groupmember_id = friend.id
				and friend.participation = 0 
				and owner.fbid = " . $fbid;
			$result = mysql_query($SQL,$connection) or die("MySQL-err.Query: " . $SQL . " - Error: (" . mysql_errno() . ") " . mysql_error());
			?>
		<table width="680" border="0" align="center" cellpadding="5">
			<tr>
				<td align="center"><h2>Amigos invitados por E-Mail no confirmados</h2></td>
			</tr>
			<tr>
				<td align="center" class="simpleText">E-mail de invitaci&oacute;n</td>
			</tr>
			<?php
			while ($iw = mysql_fetch_array($result)){ ?>
				<tr>
					<td align="center" class="simpleText"><?php echo $iw['inv_email'] ?></td>
				</tr>
			<?php }	?>
		</table>
	</div>
	<div align="center" style="margin-top:25px;">
		<?php
			$SQL = "select count(distinct groupmember_fbid) pend_fb
				from FB_INV_APP1 fb_inv, USER_APP1 friend
				where fb_inv.groupmember_fbid = friend.fbid
				and friend.participation = 0
				AND fb_inv.groupowner_fbid = " . $fbid;
			$result = mysql_query($SQL,$connection) or die("MySQL-err.Query: " . $SQL . " - Error: (" . mysql_errno() . ") " . mysql_error());
			$iw = mysql_fetch_array($result);
			?>
		<?php if ($iw['pend_fb'] != 0) { ?>
			Adem&aacute;s tiene  <span class="remarcado"><?php echo $iw['pend_fb'];?></span> invitaciones de Facebook pendientes.
		<?php } ?>
		<?php 
			mysql_close($connection);
			} 
			
			$redirect = 'https://www.facebook.com/'. PAGE_NAME . '?sk=app_'. APPLICATION1_ID;
		?>
	<a href="<?php echo $redirect;?>" target="_top">Volver a la p&aacute;gina de inicio de la aplicaci&oacute;n</a></div>
</div>
</body>
</html>