<?php
include("../include/headers.php");
require("../include/funcionesDB.php");
include_once('../phpmail/class.phpmailer.php');
include("../include/constantes_mail.php");

require '../include/facebook.php';
include("../include/app2constants.php");
// Create our Application instance (replace this with your appId and secret).
$facebook = new Facebook(array(
		'appId'  => APPLICATION2_ID,
		'secret' => APPLICATION2_SECRET,
));
$app_token = get_app_access(APPLICATION2_ID,APPLICATION2_SECRET);
// Get User ID
$user = $facebook->getUser();
?>
<?php 
if(!isset($_SESSION)) {
	session_start();
}
/* Aca tengo que sacar cosas del signed_request y del usuario logueado*/
$inv_email = "";
$action = "";

$savingData = 0;
// Si esta salvando salteo el chequeo del signed request
if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['savecontactdata'])) {
	$savingData = 1;
} else {
	$signed_request = $facebook->getSignedRequest();
	if (!$signed_request) {
		$errorMessage = "External links no permitidos";
		include("showerror.php");
		return;
	}
}
$app_data = null;
if (isset($_SESSION['app2_data'])) {
	$app_data = $_SESSION['app2_data'];
} else {
	if (isset($signed_request['app_data'])) {
		$app_data = $signed_request["app_data"];
	}
}
/* si el usuario es nulo, no lo autorizo */
if ($user == 0) {
	$_SESSION['app2_data'] = $app_data; // meto los datos en la session y redirijo
	include("askpermission.php");
	return;
}
$fbid = $user;
//$page_id = $signed_request['page']['id']; /*TODO Limitar a una pagina cuanto este productivo*/
//if ($page_id != $PAGEID) {
//	die("No allowed");
//}
if ($savingData == 0 && empty($signed_request['page']['liked'])) {
	// Si estoy en localhost
	if (APPLICATION2_URL == 'http://localhost/TDIL-FB-APP/app2') {
		$testuser = $facebook->api('/me');
		// Si el nombre incluye Tester entonces asumo que es fan
		if (strpos($testuser['name'], 'Tester')) {
			$fan = 1;
		} else {
			$_SESSION['app2_data'] = $app_data; // meto los datos en la session y redirijo
			$fan = 0;
			include("onlyforfans.php");
			return;
		}
	} else {
		$_SESSION['app2_data'] = $app_data; // meto los datos en la session y redirijo
		$fan = 0;
		include("onlyforfans.php");
		return;
	}
} else {
	$fan = 1;
}
?>
<?php
unset($_SESSION['app2_data']); // si llegue hasta aca, el usuario esta logueado y es fan, ya no necesito nada
// en la session
try {
	// Proceed knowing you have a logged in user who's authenticated.
	$user_profile = $facebook->api('/me');
} catch (FacebookApiException $e) {
	print_r($e);
	error_log($e);
	$user = null;
}
$fbemail = '';
$fbname = $user_profile['name'];
$fbusername = "";
if (isset($user_profile['username'])) {
	$fbusername = $user_profile['username'];
}
$fbgender = $user_profile['gender'];

if ($app_data) {
	$app_data_arr = explode("|", $app_data);
	$action = $app_data_arr[0];
	if ($action == 'join_group') {
		$idgroup = $app_data_arr[1];
		$iduser = $app_data_arr[2];
	}
}
?>
<?php
$pendingaction = 0;
$message = "";
$connection = mysql_connect(DB_SERVER,DB_USER, DB_PASS) or die ("Problemas en la conexion");

mysql_select_db(DB_NAME,$connection);

if ($savingData) {
	$fbname = quote_smart($fbname, $connection);
	$fbusername = quote_smart($fbusername, $connection);
	$fbgender = quote_smart($fbgender, $connection);
	$firstname = quote_smart($_POST['firstname'], $connection);
	$lastname = quote_smart($_POST['lastname'], $connection);
	$address = quote_smart($_POST['address'], $connection);
	$phone = quote_smart($_POST['phone'], $connection);
	$SQL = "SELECT * FROM USER_APP2 WHERE fbid = $fbid";
	$result = mysql_query($SQL) or die("MySQL-err.Query: " . $SQL . " - Error: (" . mysql_errno() . ") " . mysql_error());
	$num_rows = mysql_num_rows($result);
	if ($num_rows > 0) {
		// si existia, lo actualizo
		$SQL = "UPDATE USER_APP2 SET fbname = $fbname, fbusername = $fbusername, fbgender = $fbgender, 
		participation = 1,firstname = $firstname,lastname = $lastname,address = $address,phone = $phone where fbid = $fbid";
		$result = mysql_query($SQL,$connection) or die("MySQL-err.Query: " . $SQL . " - Error: (" . mysql_errno() . ") " . mysql_error());
	} else {
		// si no existia, lo inserto
		$SQL = "INSERT INTO USER_APP2 (fbid,fbname, fbusername, fbgender, origin, participation,firstname,lastname,address,phone) 
		VALUES($fbid,$fbname,$fbusername,$fbgender,1,1, $firstname,$lastname,$address,$phone)"; // 3 is fb invitation
		$result = mysql_query($SQL,$connection) or die("MySQL-err.Query: " . $SQL . " - Error: (" . mysql_errno() . ") " . mysql_error());
	}
}

$hasContactData = 0;
$isFriend = 0;
$hasFriend = 0;
// me fio si tiene contact data
$SQL = "SELECT * FROM USER_APP2 WHERE fbid = $fbid AND participation = 1";
$result = mysql_query($SQL) or die("MySQL-err.Query: " . $SQL . " - Error: (" . mysql_errno() . ") " . mysql_error());
$num_rows = mysql_num_rows($result);
if ($num_rows > 0) {
	$hasContactData = 1;
	$contactData = mysql_fetch_array($result);
}

// me fio si tiene amigo unido
$SQL = "SELECT * FROM GROUP_APP2 WHERE groupowner_fbid = $fbid";
$result = mysql_query($SQL) or die("MySQL-err.Query: " . $SQL . " - Error: (" . mysql_errno() . ") " . mysql_error());
$num_rows = mysql_num_rows($result);
if ($num_rows > 0) {
	$hasFriend = 1;
}
// me fijo si es amigo de alguien
$SQL = "SELECT * FROM GROUP_APP2 WHERE groupmember_fbid = $fbid";
$result = mysql_query($SQL) or die("MySQL-err.Query: " . $SQL . " - Error: (" . mysql_errno() . ") " . mysql_error());
$num_rows = mysql_num_rows($result);
if ($num_rows > 0) {
	$isFriend = 1;
}
if ($isFriend == 0) {
	// si no es amigo de nadie, busco a ver si tiene accion de hacerse amigo
	if ($action == "") {
		$SQL = "SELECT * FROM ACTION_APP2 WHERE fbid = $fbid";
		$result = mysql_query($SQL) or die("MySQL-err.Query: " . $SQL . " - Error: (" . mysql_errno() . ") " . mysql_error());
		$num_rows = mysql_num_rows($result);
		if ($num_rows > 0) {
			syslog(LOG_INFO, 'has pending action');
			$pendingaction = 1;
			$pending = mysql_fetch_array($result);
			$action = $pending["action"];
			$idgroup = $pending["dataid"];
			$iduser = $pending["userid"];
			$inv_email = $pending["data"];
			$inv_email = quote_smart($inv_email, $connection);
		}
	}

	if ($action == "join_group") {
		// si se esta uniendo a un grupo, veo que el id de grupo exista, tendria que validar la invitacion de alguna forma?
		$idgroup = quote_smart($idgroup, $connection);
		$SQL = "SELECT * FROM USER_APP2 WHERE id = $idgroup AND participation = 1";
		$result = mysql_query($SQL) or die("MySQL-err.Query: " . $SQL . " - Error: (" . mysql_errno() . ") " . mysql_error());
		$num_rows = mysql_num_rows($result);
		if ($num_rows > 0) {
			$idgrouprow = mysql_fetch_array($result);
			$groupowner_fbid = $idgrouprow["fbid"];
			$group_owner_name = $idgrouprow["fbname"];
			$iduser = quote_smart($iduser, $connection);
			$SQL = "SELECT * FROM USER_APP2 WHERE id = $iduser";
			$result = mysql_query($SQL) or die("MySQL-err.Query: " . $SQL . " - Error: (" . mysql_errno() . ") " . mysql_error());
			$num_rows = mysql_num_rows($result);
			if ($num_rows > 0) {
				// datos validos, completo el usuario
				$fbname = quote_smart($fbname, $connection);
				$fbusername = quote_smart($fbusername, $connection);
				$fbgender = quote_smart($fbgender, $connection);
				$SQL = "UPDATE USER_APP2 SET fbid = $fbid, fbname = $fbname, fbusername = $fbusername, fbgender = $fbgender , participation = 0 WHERE id = $iduser";
				$result = mysql_query($SQL,$connection) or die("MySQL-err.Query: " . $SQL . " - Error: (" . mysql_errno() . ") " . mysql_error());
				// se registra el miembro
				$SQL = "INSERT INTO GROUP_APP2(groupowner_fbid, groupmember_fbid, creation_date) VALUES($groupowner_fbid, $fbid, NOW())";
				$result = mysql_query($SQL,$connection) or die("MySQL-err.Query: " . $SQL . " - Error: (" . mysql_errno() . ") " . mysql_error());
				if($pendingaction = 1) {
					$SQL = "DELETE FROM ACTION_APP2 WHERE fbid = $fbid";
					mysql_query($SQL,$connection) or die("MySQL-err.Query: " . $SQL . " - Error: (" . mysql_errno() . ") " . mysql_error());
				}
				$isFriend = 1;
				/* Se formo un grupo END */
			} else {
				$errorMessage = "El usuario no existe";
				include("showerror.php");
				closeConnection($connection);
				return;
			}
		} else {
			$errorMessage = "El grupo no existe";
			include("showerror.php");
			closeConnection($connection);
			return;
		}
	}
}

if (!$hasFriend && $hasContactData) {
	$SQL = "SELECT groupmember_fbid FROM FB_INV_APP2 WHERE groupowner_fbid = $fbid AND creation_date >= (CURDATE() - INTERVAL (select invitation_days FROM CONFIG_APP2) DAY)";
	$result = mysql_query($SQL) or die("MySQL-err.Query: " . $SQL . " - Error: (" . mysql_errno() . ") " . mysql_error());
	$excluded = '';
	while ( $aRow = mysql_fetch_array( $result ) ) {
		$excluded = $excluded . $aRow['groupmember_fbid'] . ',';
	}
	$SQL = "SELECT groupowner_fbid FROM GROUP_APP2 WHERE groupmember_fbid = $fbid";
	$result = mysql_query($SQL) or die("MySQL-err.Query: " . $SQL . " - Error: (" . mysql_errno() . ") " . mysql_error());
	$num_rows = mysql_num_rows($result);
	if ($num_rows > 0) {
		$recommender = mysql_fetch_array($result);
		$excluded = $excluded . $recommender['groupowner_fbid'] . ',';
	}
	
	$excluded =  substr($excluded, 0 , strlen($excluded)-1);
	
	$SQL = "SELECT (select fb_daily_quota FROM CONFIG_APP2) - COUNT(*) remaining FROM FB_INV_APP2 WHERE groupowner_fbid = $fbid AND creation_date >= CURDATE()";
	$result = mysql_query($SQL) or die("MySQL-err.Query: " . $SQL . " - Error: (" . mysql_errno() . ") " . mysql_error());
	$aRow = mysql_fetch_array( $result );
	$fb_remaining = $aRow['remaining'];
	
	$SQL = "select (select email_daily_quota FROM CONFIG_APP2) - COUNT(*) remaining FROM EMAIL_INV_APP2 where groupowner_id = (select id from USER_APP2 where fbid = $fbid) AND creation_date >= CURDATE()";
	$result = mysql_query($SQL) or die("MySQL-err.Query: " . $SQL . " - Error: (" . mysql_errno() . ") " . mysql_error());
	$aRow = mysql_fetch_array( $result );
	$email_remaining = $aRow['remaining'];
	
	$SQL = "select active FROM CONFIG_APP2";
	$result = mysql_query($SQL) or die("MySQL-err.Query: " . $SQL . " - Error: (" . mysql_errno() . ") " . mysql_error());
	$aRow = mysql_fetch_array( $result );
	$active = $aRow['active'];
}

closeConnection($connection);
?>
<html>
<head>
<script type='text/javascript' src='../js/jquery-1.7.min.js'></script>

<script>
	function checkContactData() {
		var objdiv = document.getElementById("errmessage");
		if (document.getElementById("firstname").value == "") {
			objdiv.innerHTML = "Debe ingresar el nombre";
			return false;
		}
		if (document.getElementById("lastname").value == "") {
			objdiv.innerHTML = "Debe ingresar el apellido";
			return false;
		}
		if (document.getElementById("address").value == "") {
			objdiv.innerHTML = "Debe ingresar la direccion";
			return false;
		}
		if (document.getElementById("phone").value == "") {
			objdiv.innerHTML = "Debe ingresar el telefono";
			return false;
		}
		return true;
	}

	function checkEmail() {
		var email = document.getElementById('inv_email');
		var filter = /^([a-zA-Z0-9_\.\-])+\@(([a-zA-Z0-9\-])+\.)+([a-zA-Z0-9]{2,4})+$/;
		if (!filter.test(email.value)) {
			alert('La direccion de email es incorrecta');
			email.focus;
			return false;
		}
	}
</script>
<link href="../css/tdil.css" rel="stylesheet" type="text/css">
</head>
<body>
<?php if ($hasFriend) { ?>
	Te vamos a mandar la MMGG a tu domicilio
<?php } else { ?>
	<?php if ($hasContactData == 0) { ?>
		<form method="POST" action="<?php echo $_SERVER['PHP_SELF'] ?>" onSubmit="return checkContactData();">
		<div id="errmessage"></div>
		Nombre: <input type="text" name="firstname" id="firstname"><br>
		Apellido: <input type="text" name="lastname" id="lastname"><br>
		Direccion: <input type="text" name="address" id="address"><br>
		Telefono: <input type="text" name="phone" id="phone"><br>
		<input type="hidden" name="savecontactdata" value="true"><br>
		<input type="submit" value="Grabar datos">
		No tenes datos de contacto, para poder invitar a un amigo tenes que dejarlos primero
		Esto hace submit a esta misma pagina, primero debe grabar y sigue.
		</form>
	<?php } else { ?>
		<form method="POST" action="<?php echo $_SERVER['PHP_SELF'] ?>" onSubmit="return checkContactData();">
		<div id="errmessage"></div>
		Nombre: <input type="text" name="firstname" id="firstname" value="<?php echo $contactData['firstname'];?>"><br>
		Apellido: <input type="text" name="lastname" id="lastname" value="<?php echo $contactData['lastname'];?>"><br>
		Direccion: <input type="text" name="address" id="address" value="<?php echo $contactData['address'];?>"><br>
		Telefono: <input type="text" name="phone" id="phone" value="<?php echo $contactData['phone'];?>"><br>
		<input type="hidden" name="savecontactdata" value="true"><br>
		<input type="submit" value="Grabar datos">
		</form>
		<body>
<?php if ($active) { ?>
	<?php if ( $email_remaining > 0) { ?>
	<div id="invitationBlock">
	  <form action="./inviteemail.php" onSubmit="return checkEmail();">
			<input type="hidden" name="fbid" value="<?php echo $fbid;?>">
		<input type="text" name="inv_email" id="inv_email" class="galletaInput">
	    <input type="submit" class="okButton">
	  </form>
	</div>
	<?php } else { ?>
	<div id="notInvitationBlock"></div>
	<?php } ?>
<?php } ?>
<div id="fb-root"></div>
<script>
    window.fbAsyncInit = function() {
        FB.init({
            appId: '<?php echo APPLICATION2_ID;?>',
            status: true,
            cookie: true,
            oauth: true
        });
    };
 
    $('a').click(sendRequest);
    function sendRequest() {
        FB.ui({
            method: 'apprequests',
            message: 'De a 2',
            title: 'De a 2',
			max_recipients: 1,
			exclude_ids: [<?php echo $excluded;?>],
			data: '{"item_id":<?php echo $user; ?>}' /*Aca va el id del usuario que manda*/
        },
        function (response) {
            if (response.request && response.to) {
                var request_ids = [];
                for(i=0; i<response.to.length; i++) {
                    var temp = response.request + '_' + response.to[i];
                    request_ids.push(temp);
                }
                var requests = request_ids.join(',');
                $.post('<?php echo APPLICATION2_URL;?>/handle_fbrequest.php',{uid: <?php echo $user; ?>, request_ids: requests},function(resp) {
                    // callback after storing the requests
				   location.replace('<?php echo APPLICATION2_URL;?>/requestsent.php');
                });
				/* TODO Redirect to requestsent.php */
				
            } 
        });
        return false;
    }
 
      // Load the SDK Asynchronously
  (function(d){
     var js, id = 'facebook-jssdk'; if (d.getElementById(id)) {return;}
     js = d.createElement('script'); js.id = id; js.async = true;
     js.src = "//connect.facebook.net/en_US/all.js";
     d.getElementsByTagName('head')[0].appendChild(js);
   }(document));
</script>
<div id="buttonsLinks">
<?php if ($active) { ?>
	<?php if ($fb_remaining > 0) { ?>
		Envi&aacute; una <a href="#" onClick="sendRequest()">invitaci&oacute;n a un amigo</a>
	<?php } else { ?>
		Ya enviaste todas las invitaciones que pod&iacute;as
	<?php } ?>
<?php } else { ?>
		La promoción ya no esta activa.
<?php } ?>
</div>
	<?php }  ?>
<?php }  ?>
</body>
</html>