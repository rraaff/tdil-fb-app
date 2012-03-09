<?php /*
* Output
*/
include("include/headers.php");
require("include/funcionesDB.php");
//require("include/boCheckLogin.php");

$connection = mysql_connect(DB_SERVER,DB_USER, DB_PASS) or die ("Problemas en la conexion");
mysql_select_db(DB_NAME,$connection);
	
$SQL = "SELECT * FROM USER_APP1";
$result = mysql_query($SQL,$connection) or die("MySQL-err.Query: " . $SQL . " - Error: (" . mysql_errno() . ") " . mysql_error());
$iTotal = mysql_num_rows($result);

$output = array(
		"sEcho" => intval($_GET['sEcho']),
		"iTotalRecords" => $iTotal,
		"iTotalDisplayRecords" => $iTotal,
		"aaData" => array()
);
$aColumns = array( 'id', 'fbid', 'fbname');

while ( $aRow = mysql_fetch_array( $result ) ) {
	$row = array();
	for ( $i=0 ; $i<count($aColumns) ; $i++ ) {
		if ( $aColumns[$i] != ' ' )
		{
			/* General output */
			$row[] = $aRow[ $aColumns[$i] ];
		}
	}
	$output['aaData'][] = $row;
}
mysql_close($connection);
echo json_encode( $output );
?>