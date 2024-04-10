<?php
require "config.php";
$getallqueuelist = $con->prepare("select * from gethelp where merge !='YES'");
$getallqueuelist->execute();
for($i=1; $i<=rows("select * from gethelp where merge !='YES'"); $i++)
{
	$getallqueuelist_info = $getallqueuelist->fetch(PDO::FETCH_ASSOC);
	$pid = $getallqueuelist_info['participantID'];
	
	$getallnumbers = $con->prepare("select * from participant where pid='$pid'");
	$getallnumbers->execute();
	$getallnumbersinfo = $getallnumbers->fetch(PDO::FETCH_ASSOC);
	echo str_replace("+", "" , str_replace(" ", "" , $getallnumbersinfo['mobile'] . "<br>"));
	// echo $pid . '<br>';
}