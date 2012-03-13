<?php
	include("../include/headers.php");
	require '../include/facebook.php';
	include("../include/app1constants.php"); 
	// Create our Application instance (replace this with your appId and secret).
	$facebook = new Facebook(array(
			'appId'  => $APPLICATION_ID,
			'secret' => $APPLICATION_SECRET,
	));
?>
<?php
$redirect = 'http://apps.facebook.com/'. $APPLICATION_ID;
header('Location: '.$redirect);
?>