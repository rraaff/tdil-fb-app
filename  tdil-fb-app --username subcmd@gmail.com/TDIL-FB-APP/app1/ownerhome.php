<?php /* {PABLO} Esta es la home del owner */?>
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

</head>
<body>
<!-- Esta pagina es la que se muestra a los que son dueños de un grupo -->
<form action="./inviteemail.php" onSubmit="return checkEmail();">
<input type="text" name="fbid" value="<?php echo $fbid;?>">
Invite friend: <input type="text" name="inv_email" id="inv_email">
<input type="submit">
</form>
<br><br>

<div id="fb-root"></div>
<script>
    window.fbAsyncInit = function() {
        FB.init({
            appId: '292861170783253',
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
			exclude_ids: [],
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
                $.post('<?php echo $APPLICATION_URL;?>/handle_fbrequest.php',{uid: <?php echo $user; ?>, request_ids: requests},function(resp) {
                    // callback after storing the requests
					alert(resp);
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
 
<a href="#" onclick="sendRequest()">Send Application Request</a><br>

<a href="groupdetails.php">Detalles de mi grupo</a>
</body>
</html>