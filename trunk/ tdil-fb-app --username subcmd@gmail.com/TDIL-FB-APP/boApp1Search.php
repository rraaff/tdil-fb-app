<html>
<head>

<style type="text/css" title="currentStyle">
			@import "css/demo_page.css";
			@import "css/demo_table.css";
		</style>
		<script type="text/javascript" language="javascript" src="js/jquery-1.7.min.js"></script>
		<script type="text/javascript" language="javascript" src="js/jquery.dataTables.js"></script>
	
<script>
var oTable = null;
function doSearch() {
	if (oTable != null) {
		oTable.fnDestroy();
	}
	oTable = $('#example').dataTable( {
					"bProcessing": true,
					"sAjaxSource": "doApp1Search.php"
				} );
			}

</script>
</head>

<body id="dt_example">

<input type="button" onclick="doSearch();">
<div id="container">
	<div id="dynamic">

<table cellpadding="0" cellspacing="0" border="0" class="display" id="example">
	<thead>
		<tr>
			<th width="20%">Id</th>
			<th width="25%">fbid</th>
			<th width="25%">fbname</th>
		</tr>
	</thead>
	<tbody>
		
	</tbody>
	<tfoot>
		<tr>
			<th width="20%">Id</th>
			<th width="25%">fbid</th>
			<th width="25%">fbname</th>
		</tr>
	</tfoot>
</table>

</div>
</div>
</body>
</html>