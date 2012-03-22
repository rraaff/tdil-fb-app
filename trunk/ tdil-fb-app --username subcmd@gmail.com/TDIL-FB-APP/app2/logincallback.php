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
$redirect = 'https://www.facebook.com/'. PAGE_NAME . '?sk=app_'. APPLICATION2_ID;
if (!empty($_GET['app_data'])) {
	// Strip the slashes that Facebook added
	$redirect .= '&app_data='.urlencode(stripslashes($_GET['app_data']));
}
header('Location: '.$redirect);
?>