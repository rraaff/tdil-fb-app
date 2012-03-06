<?PHP
session_start();
if (!(isset($_SESSION['boLogin']) && $_SESSION['boLogin'] == 1)) {
	header ("Location: boLogin.php");
}

?>