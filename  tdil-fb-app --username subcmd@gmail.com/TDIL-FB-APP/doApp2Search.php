<?php /*
* Output
*/
include("include/headers.php");
require("include/funcionesDB.php");
//require("include/boCheckLogin.php");

$sParticipation = $_REQUEST["sParticipation"];
$sGender = $_REQUEST["sGender"];
$sCoupleOrigin = $_REQUEST["sCoupleOrigin"];
$sCoupleDest = $_REQUEST["sCoupleDest"];

$connection = mysql_connect(DB_SERVER,DB_USER, DB_PASS) or die ("Problemas en la conexion");

$sGender = quote_smart($sGender, $connection);
mysql_select_db(DB_NAME,$connection);
	
$SQL = "select u.fbid, u.fbname, u.fbgender, u.firstname,u.lastname,u.address,u.phone,
	(SELECT fbname from USER_APP2 usero, GROUP_APP2 groupo WHERE usero.fbid = groupo.groupowner_fbid
	AND groupo.groupmember_fbid = u.fbid) origin,
	(SELECT fbname from USER_APP2 usero, GROUP_APP2 groupo WHERE usero.fbid = groupo.groupmember_fbid
	AND groupo.groupowner_fbid = u.fbid) friend
from USER_APP2 u WHERE 1 = 1 ";

if ($sParticipation != "-1") {
	$SQL = $SQL . " AND u.participation = $sParticipation";
}
if ($sGender != "'all'") {
	$SQL = $SQL . " AND u.fbgender = $sGender";
}
if ($sCoupleOrigin == "0") {
	$SQL = $SQL . " AND NOT EXISTS (SELECT * FROM GROUP_APP2 WHERE groupowner_fbid = u.fbid)";
}
if ($sCoupleOrigin == "1") {
	$SQL = $SQL . " AND EXISTS (SELECT * FROM GROUP_APP2 WHERE groupowner_fbid = u.fbid)";
}
if ($sCoupleDest == "0") {
	$SQL = $SQL . " AND NOT EXISTS (SELECT * FROM GROUP_APP2 WHERE groupmember_fbid = u.fbid)";
}
if ($sCoupleDest == "1") {
	$SQL = $SQL . " AND EXISTS (SELECT * FROM GROUP_APP2 WHERE groupmember_fbid = u.fbid)";
}


$result = mysql_query($SQL,$connection) or die("MySQL-err.Query: " . $SQL . " - Error: (" . mysql_errno() . ") " . mysql_error());
$iTotal = mysql_num_rows($result);

$sEcho = 1;
if (isset($_GET['sEcho'])) {
	$sEcho = intval($_GET['sEcho']);
}

$output = array(
		"sEcho" => $sEcho,
		"iTotalRecords" => $iTotal,
		"iTotalDisplayRecords" => $iTotal,
		"aaData" => array()
);
$aColumns = array( 'fbid', 'fbname', 'firstname', 'lastname', 'fbgender', 'address','phone','origin','friend');

while ( $aRow = mysql_fetch_array( $result ) ) {
	$row = array();
	for ( $i=0 ; $i<count($aColumns) ; $i++ ) {
		if ( $aColumns[$i] != ' ' )	{
			/* General output */
			$row[] = $aRow[ $aColumns[$i] ];
		}
	}
	$output['aaData'][] = $row;
}
mysql_close($connection);
echo json_encode( $output );
?>