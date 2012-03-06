<?php
$output = '{ "success": "no", "error": "Error generico." }';
$output = str_replace("\r", "", $output);
	$output = str_replace("\n", "", $output);
	echo $output;
?>