<?php

error_reporting(-1);
ob_start();
session_start();
$timeout = 1800; //30minute
 if(isset($_SESSION['timeout'])) {
    // See if the number of seconds since the last
    // visit is larger than the timeout period.
    $duration = time() - (int)$_SESSION['timeout'];
    if($duration > $timeout) {
        // Destroy the session and restart it.
        session_destroy();
        session_start();
    }
}
 
// Update the timout field with the current time.
$_SESSION['timeout'] = time();


try
{
	$con = new PDO("mysql:host=localhost;dbname=giverscycler", 'root', '');
	$con->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
}
catch(PDOException $e)
{
?>
	<script>
	alert("Could not connect to database, error submitted successfully to Log file");
	</script>
<?php
	file_put_contents("errorlog.txt", $e->getMessage(), FILE_APPEND);
}


//functions
if(!function_exists("query")){
	function query($query)
	{
		global $con;
		$q = $con->prepare($query);
		$r = $q->execute();
		$res = $q->fetch(PDO::FETCH_ASSOC);
		return $res;
	}
}


if(!function_exists("rows")){
	function rows($query)
	{
		global $con;
		$q = $con->prepare($query);
		$r = $q->execute();
		$count = $q->rowCount();
		return $count;
	}
}


if(!function_exists("redirect_to")){
	
	function redirect_to($link)
	{
		$redirect = header("Location:".$link);
		return $redirect;
	}
}


if(!function_exists("clean")){
	function clean($value)
	{
		global $con;
		$clean = mysqli_real_escape_string($con, trim(strip_tags($value)));
		return $clean;
	}
}


if(isset($_SESSION["username"]))
{
	$username = $_SESSION["username"];
}
?>