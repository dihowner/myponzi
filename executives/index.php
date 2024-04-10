<?php
require "../config.php";

//Why will u be logged and U will come and Login again????
if(isset($username))
{
	unset($_SESSION["username"]);
	session_destroy();
}
?>
<!DOCTYPE HTML>
<html>
<head>
<title>Executive Login :: Givers Cycler</title>
<meta name="viewport" content="width=device-width, initial-scale=1">
<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
<meta name="keywords" content="Modern Responsive web template, Bootstrap Web Templates, Flat Web Templates, Andriod Compatible web template, 
Smartphone Compatible web template, free webdesigns for Nokia, Samsung, LG, SonyErricsson, Motorola web design" />
<script type="application/x-javascript"> addEventListener("load", function() { setTimeout(hideURLbar, 0); }, false); function hideURLbar(){ window.scrollTo(0,1); } </script>
 <!-- Bootstrap Core CSS -->
<link href="css/bootstrap.min.css" rel='stylesheet' type='text/css' />
<!-- Custom CSS -->
<link href="css/style.css" rel='stylesheet' type='text/css' />
<link href="css/font-awesome.css" rel="stylesheet"> 
  <link href="../img/favicon.jpg" rel="shortcut icon" type="" />
<!-- jQuery -->
<script src="js/jquery.min.js"></script>
<!----webfonts--->
<link href='http://fonts.googleapis.com/css?family=Roboto:400,100,300,500,700,900' rel='stylesheet' type='text/css'>
<!---//webfonts--->  
<!-- Bootstrap Core JavaScript -->
<script src="js/bootstrap.min.js"></script>
</head>
<body id="login">
  <div class="login-logo" style='color: #000'>
	<font color='#000' size='38px'><b>GIVERS <font color='red'>CYCLER</font> EXECUTIVE  <font color='red'>PANEL</font></b></font>
	
	<?php
	// echo $admin_user;
	if(isset($_POST["login_admin"]))
	{
		$admin_user = strtolower($_POST["admin_user"]);
		$admin_pass = md5($_POST["admin_pass"]);
		$query_executive_row = rows("select * from executive where username='$admin_user' and password='$admin_pass'");
		if($query_executive_row == 1)
		{
			redirect_to("dashboard");
			$_SESSION["username"] = $admin_user;
		}
		else
		{
		?>
			<script>
				alert("BAD COMBINATION OF USERNAME OR PASSWORD");
			</script>
			<div class='alert alert-warning'><b>BAD COMBINATION OF USERNAME OR PASSWORD</b></div>
		<?php
		}
		// echo $admin_user;
	}
	?>
  </div>
	<div class="app-cam">
	
		<form method='post'>
			<input type="text" class="text" placeholder="Enter your Username" name='admin_user' required autocomplete="off">
			<br><br>
			<input type="password" placeholder="Enter your Password" name='admin_pass' required autocomplete="off">
			<br><br>
			<div class="submit" align='center'><button type="submit" class='btn btn-default btn-lg' name='login_admin'>LOGIN NOW &raquo;</button></div>
		
		
		</form>
	</div>
   <div class="copy_layout login">
      <p><b>© 2017 Giverscycler ::: Givers are recievers.</b></p>
   </div>
</body>
</html>
