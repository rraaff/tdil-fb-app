<?php
	include("../include/headers.php");
	require '../include/facebook.php';
	include("../include/app1constants.php"); 
	// Create our Application instance (replace this with your appId and secret).
	$facebook = new Facebook(array(
			'appId'  => APPLICATION1_ID,
			'secret' => APPLICATION1_SECRET,
	));
?>
<?php
$redirect = 'https://www.facebook.com/'. PAGE_NAME . '?sk=app_'. APPLICATION1_ID;
if (!empty($_GET['app_data'])) {
	// Strip the slashes that Facebook added
	$redirect .= '&app_data='.urlencode(stripslashes($_GET['app_data']));
}
header('Location: '.$redirect);
?>