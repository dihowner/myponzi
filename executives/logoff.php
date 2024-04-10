<?php
ERROR_REPORTING(0);
require_once "../config.php";
$username=$_SESSION["username"];
unset($_SESSION["username"]);
session_destroy();
header("location: index");
exit();
?>
