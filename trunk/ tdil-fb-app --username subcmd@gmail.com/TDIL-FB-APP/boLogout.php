<?php
include("include/headers.php");
session_start();
session_destroy();

header ("Location: boLogin.php");
?>