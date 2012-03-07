<?php 
	include("../include/headers.php");
	require("../include/funcionesDB.php");
?>
<?php if (TESTING == 'TRUE') {
	$action = $_REQUEST['action']; /* new_group, join_group, empty*/
	$inv_email = $_REQUEST['inv_email']; /*mail de invitacion*/
	$fan = $_REQUEST['fan']; /*1 fan, 0 no fan*/
	$fbid = $_REQUEST['fbid']; /*id de facebook*/
	$fbemail = $_REQUEST['fbemail']; /*id de facebook*/
	$fbname = $_REQUEST['fbname'];
	$fbusername = $_REQUEST['fbusername'];
	$fbgender = $_REQUEST['fbgender'];
} else {
	/* Aca tengo que sacar cosas del signed_request y del usuario logueado*/
	$action = "todo";
} ?>
Debug info:
action:<?php echo $action?><br>
inv_email:<?php echo $inv_email?><br>
fan:<?php echo $fan?><br>
fbid:<?php echo $fbid?><br>
fbemail:<?php echo $fbemail?><br>
fbname:<?php echo $fbname?><br>
fbusername:<?php echo $fbusername?><br>
fbgender:<?php echo $fbgender?><br>
