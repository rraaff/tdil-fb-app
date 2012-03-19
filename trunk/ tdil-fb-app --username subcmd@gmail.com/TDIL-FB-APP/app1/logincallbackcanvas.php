<?php
	include("../include/headers.php");
	require '../include/facebook.php';
	include("../include/app1constants.php"); 
	// Create our Application instance (replace this with your appId and secret).
	$facebook = new Facebook(array(
			'appId'  => $APPLICATION1_ID,
			'secret' => $APPLICATION1_SECRET,
	));
?>
<?php
$redirect = 'https://apps.facebook.com/'. $APPLICATION1_ID;
header('Location: '.$redirect);
?>