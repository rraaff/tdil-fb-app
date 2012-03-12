<!-- Esta pagina es la que se muestra a los que son dueños de un grupo -->
<form action="./inviteemail.php">
<input type="text" name="fbid" value="<?php echo $fbid;?>">
Invite friend: <input type="text" name="inv_email">
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
            message: 'Hacete fan ayudame a ganar',
            title: 'Send your friends an application request',
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
                $.post('http://localhost/TDIL-FB-APP/app1/handle_fbrequest.php',{uid: <?php echo $user; ?>, request_ids: requests},function(resp) {
                    // callback after storing the requests
                });
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
 
<a href="#" onclick="sendRequest()">Send Application Request</a>
 