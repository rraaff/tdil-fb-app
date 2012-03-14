<?php
/* {PABLO} Esta pagina tiene el listado de invitaciones hechas */
?>
<html>
<body>

<?php
	include("../include/headers.php");
	require("../include/funcionesDB.php");
	
	require '../include/facebook.php';
	include("../include/app1constants.php"); 
	// Create our Application instance (replace this with your appId and secret).
	$facebook = new Facebook(array(
			'appId'  => $APPLICATION_ID,
			'secret' => $APPLICATION_SECRET,
	));
	$app_token = get_app_access($APPLICATION_ID,$APPLICATION_SECRET);
	// Get User ID
	$user = $facebook->getUser();
	if ($user) {
		$connection = mysql_connect(DB_SERVER,DB_USER, DB_PASS) or die ("Problemas en la conexion");
		
		$sGender = quote_smart($sGender, $connection);
		mysql_select_db(DB_NAME,$connection);
		$fbid = quote_smart($user, $connection);
		
		$SQL = "SELECT ua.inv_email, ua.fbname, ua.fbusername FROM GROUP_APP1 ga, USER_APP1 ua
			WHERE ga.groupmember_fbid != 0
			AND ga.groupmember_fbid = ua.fbid AND ga.groupowner_fbid = " . $fbid;
		$result = mysql_query($SQL,$connection) or die("MySQL-err.Query: " . $SQL . " - Error: (" . mysql_errno() . ") " . mysql_error());
	?>

Esta pagina tiene los detalles de mi grupo

Amigos en mi grupo
	<table>
		<tr>
			<td>Nombre</td>
			<td>Usuario de facebook</td>
			<td>Email de invitacion</td>
		</tr>
<?php
	while ($iw = mysql_fetch_array($result)){
?>
	<tr>
		<td><?php echo $iw['fbname'] ?></td>
		<td><?php echo $iw['fbusername'] ?></td>
		<td><?php 
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
<?php
	$SQL = "SELECT friend.inv_email
		FROM EMAIL_INV_APP1 em, USER_APP1 owner, USER_APP1 friend
		where em.groupowner_id = owner.id
		and em.groupmember_id = friend.id
		and friend.participation = 0 
		and owner.fbid = " . $fbid;
	$result = mysql_query($SQL,$connection) or die("MySQL-err.Query: " . $SQL . " - Error: (" . mysql_errno() . ") " . mysql_error());
	?>
	
Amigos no confirmados
<table>
<tr>
<td>Email de invitacion</td>
</tr>
<?php
while ($iw = mysql_fetch_array($result)){ ?>
	<tr>
		<td><?php echo $iw['inv_email'] ?></td>
	</tr>
<?php }	?>
</table>

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
	Ademas tiene  <?php echo $iw['pend_fb'];?> invitaciones de facebook pendientes.
<?php } ?>
<?php 
	mysql_close($connection);
	} 
	
	$redirect = 'https://www.facebook.com/'. $PAGE_NAME . '?sk=app_'. $APPLICATION_ID;
?>
<a href="<?php echo $redirect;?>" target="_top">Volver a la home</a>
</body>
</html>