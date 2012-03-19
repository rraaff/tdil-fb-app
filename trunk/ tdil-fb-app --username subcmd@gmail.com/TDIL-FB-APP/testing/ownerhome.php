<html>
<head>
<script type='text/javascript' src='../js/jquery-1.7.min.js'></script>
</head>
<body>

<a href="#" onClick="sendRequest()">Send Application Request</a><br>

<script>

function fb(org, fnc) {
	jQuery(fnc);
}

$('a').click(sendRequest);
	function sendRequest() {
        fb({
            method: 'apprequests',
            message: 'Sumate a la promo',
            title: 'Sumate a la promo',
			exclude_ids: [],
			data: '{"item_id":<?php echo $user; ?>}' /*Aca va el id del usuario que manda*/
        },
        function (response) {
            alert(1);
            /*if (response.request && response.to) {
                var request_ids = [];
                for(i=0; i<response.to.length; i++) {
                    var temp = response.request + '_' + response.to[i];
                    request_ids.push(temp);
                }
                var requests = request_ids.join(',');*/
                $.post('./ownerhome.php',{uid: 1<?php echo $user; ?>, request_ids: 'requests'},function(resp) {
                    // callback after storing the requests
					alert(resp);
                });
				/* TODO Redirect to requestsent.php */
				
            //} 
        });
        return false;
    }
</script>
</body>
</html>