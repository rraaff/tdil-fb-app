<?php

if (DB_USER == NULL || DB_USER == '' || DB_USER == 'DB_USER'){
	include("constantes.php");
}

//==========================================
//	ESCAPE DANGEROUS SQL CHARACTERS
//==========================================
function quote_smart($value, $handle) {

	if (get_magic_quotes_gpc()) {
		$value = stripslashes($value);
	}

	if (!is_numeric($value)) {
		$value = "'" . mysql_real_escape_string($value, $handle) . "'";
	}
	return $value;
}

Function getConnection(){
	//Por ahora no uso password para conectarme a la base.
	$connection = mysql_connect(DB_SERVER,DB_USER, DB_PASS) or die ("Problemas en la conexion");
	mysql_select_db(DB_NAME,$connection);
	return $connection;
}

Function doSelect($query){
	$connection = getConnection();
	printQuery($query);
	$returnSelect = mysql_query($query,$connection);// or die ("Problemas en select".mysql_error());
	closeConnection($connection);
	return $returnSelect;
}

Function doInsert($query){
	$connection = getConnection();
	printQuery($query);
	mysql_query($query,$connection);// or die ("Error en insert ".mysql_error()."\n".$query);
	closeConnection($connection);
}

Function doInsertAndGetLast($query){
	$conn = getConnection();
	printQuery($query);
	mysql_query($query,$conn) or die ("Error en insert".mysql_error());
	$returnInsert = mysql_insert_id($conn);
	closeConnection($conn);
	return $returnInsert;
}

Function printQuery($query){
	if ($_REQUEST['showSQL'] == 'Y'){
		echo $query."<br>";
	}
}

Function closeConnection($conn) {
	mysql_close($conn);
}

Function doExecuteAndGetCount($query){
	$conn = getConnection();
	mysql_query($query,$conn) or die ("Error en execute ".mysql_error());
	$cant = mysql_affected_rows($conn);
	closeConnection($conn);
	return $cant;
}

Function store_action($usrid,$act,$desc,$url) {
	$query = "insert into logs (user_id, ins_dt, act_id, descr, url) values (".$usrid.",sysdate(),".$act.",'".$desc."','".$url."')";
	doInsert($query);
}

?>