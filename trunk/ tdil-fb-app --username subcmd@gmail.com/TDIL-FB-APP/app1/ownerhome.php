<?php /* {PABLO} Esta es la home del owner */
	$SQL = "SELECT groupmember_fbid FROM FB_INV_APP1 WHERE groupowner_fbid = $fbid AND creation_date >= (CURDATE() - INTERVAL (select invitation_days FROM CONFIG_APP1) DAY)";
	$result = mysql_query($SQL) or die("MySQL-err.Query: " . $SQL . " - Error: (" . mysql_errno() . ") " . mysql_error());
	$excluded = '';
	while ( $aRow = mysql_fetch_array( $result ) ) {
		$excluded = $excluded . $aRow['groupmember_fbid'] . ',';
	}
	$excluded =  substr($excluded, 0 , strlen($excluded)-1);
	
	$SQL = "SELECT (select fb_daily_quota FROM CONFIG_APP1) - COUNT(*) remaining FROM FB_INV_APP1 WHERE groupowner_fbid = $fbid AND creation_date >= CURDATE()";
	$result = mysql_query($SQL) or die("MySQL-err.Query: " . $SQL . " - Error: (" . mysql_errno() . ") " . mysql_error());
	$aRow = mysql_fetch_array( $result );
	$fb_remaining = $aRow['remaining'];
	
	$SQL = "select (select email_daily_quota FROM CONFIG_APP1) - COUNT(*) remaining FROM EMAIL_INV_APP1 where groupowner_id = (select id from USER_APP1 where fbid = $fbid) AND creation_date >= CURDATE()";
	$result = mysql_query($SQL) or die("MySQL-err.Query: " . $SQL . " - Error: (" . mysql_errno() . ") " . mysql_error());
	$aRow = mysql_fetch_array( $result );
	$email_remaining = $aRow['remaining'];
?>
<html>
<head>
<script type='text/javascript' src='../js/jquery-1.7.min.js'></script>

<script>
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

<style type="text/css">
<!--
body {
	background-image: url(../images/ownerhome.jpg);
	background-repeat: no-repeat;
	background-position: center center;
}
#invitationBlock {
	height: 100px;
	margin-top: 460px;
	margin-right: auto;
	margin-left: auto;
	padding-left: 285px;
	width: 350px;
}
.galletaInput{
	background: none;
	border:none;
	height: 20px;
	width: 120px;
}
.okButton {
	font-size: 1px;
	color: #FFFFFF;
	background:none;
	background-image: url(../images/button_ok.png);
	background-repeat: no-repeat;
	background-position: center center;
	height: 69px;
	width: 69px;
	border-top-style: none;
	border-right-style: none;
	border-bottom-style: none;
	border-left-style: none;
	margin-left: 30px;
}
#buttonsLinks {
	height: 60px;
	width: 460px;
	margin-right: auto;
	margin-left: auto;
}
-->
</style>
</head>
<body>
<div id="invitationBlock">
<?php if ( $email_remaining > 0 ) { ?>
  <form action="./inviteemail.php" onSubmit="return checkEmail();">
		<input type="hidden" name="fbid" value="<?php echo $fbid;?>">
	<input type="text" name="inv_email" id="inv_email" class="galletaInput">
	<input type="submit" class="okButton">
  </form>
  <?php } ?>
</div>
<div id="fb-root"></div>
<script>
    window.fbAsyncInit = function() {
        FB.init({
            appId: '<?php echo APPLICATION1_ID;?>',
            status: true,
            cookie: true,
            oauth: true
        });
    };
 
    $('a').click(sendRequest);
    function sendRequest() {
        FB.ui({
            method: 'apprequests',
            message: 'Sumate a la promo',
            title: 'Sumate a la promo',
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
                $.post('<?php echo APPLICATION1_URL;?>/handle_fbrequest.php',{uid: <?php echo $user; ?>, request_ids: requests},function(resp) {
                    // callback after storing the requests
				   location.replace('<?php echo APPLICATION1_URL;?>/requestsent.php');
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
	<img src="../images/null.gif" width="460" height="60" border="0" usemap="#Map">
  <map name="Map">
        <area shape="rect" coords="326,29,456,52" href="groupdetails.php">
        <area shape="rect" coords="81,29,235,50" href="javascript:sendRequest();">
  </map>
</div>
<?php if ($fb_remaining > 0) { ?>
	<a href="#" onClick="sendRequest()">Send Application Request</a><br>
<?php } else { ?>
	Send Application Request<br>
<?php } ?>
<a href="groupdetails.php">Detalles de mi grupo</a>
</body>
</html>