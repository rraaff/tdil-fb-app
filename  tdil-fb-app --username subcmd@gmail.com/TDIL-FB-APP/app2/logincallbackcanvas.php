<?php
	include("../include/headers.php");
	require '../include/facebook.php';
	include("../include/app2constants.php"); 
	// Create our Application instance (replace this with your appId and secret).
	$facebook = new Facebook(array(
			'appId'  => APPLICATION2_ID,
			'secret' => APPLICATION2_SECRET,
	));
?>
<?php
$redirect = 'https://apps.facebook.com/'. APPLICATION2_ID;
header('Location: '.$redirect);
?>