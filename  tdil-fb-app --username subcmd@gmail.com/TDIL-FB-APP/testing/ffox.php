<html>
<head>
<script type='text/javascript' src='../js/jquery-1.7.min.js'></script>
</head>
<body>

<a href="#" onClick="sendRequest()">Send Application Request</a><br>

<script>

function kc_load()
{
	 $.post('./ffox.php',{uid: 1, request_ids: 'requests'},function(resp) {
         // callback after storing the requests
		 $('#kecheng').html(resp);
     });
}
$(document).ready(function(){
       kc_load();
});
</script>
<div id="kecheng"></div>
</body>
</html>