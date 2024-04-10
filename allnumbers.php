<?php

require "config.php";

$getallnumbers = $con->prepare("select * from participant");
$getallnumbers->execute();
for ($i=1; $i<=rows("select * from participant"); $i++)
{
	$getallnumbersinfo = $getallnumbers->fetch(PDO::FETCH_ASSOC);
	echo str_replace("+", "" , str_replace(" ", "" , $getallnumbersinfo['mobile'] . "<br>"));
}