<?php
require("config.php");
//Why will u be logged and U will come and register????
if(isset($username))
{
	unset($_SESSION["username"]);
	session_destroy();
}
?>
<!DOCTYPE html>
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
    <meta charset="utf-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <title>Membership Login</title>
	<!-- BOOTSTRAP STYLES-->
    <link href="assets/css/bootstrap.css" rel="stylesheet" />
     <!-- FONTAWESOME STYLES-->
    <link href="assets/css/font-awesome.css" rel="stylesheet" />
        <!-- CUSTOM STYLES-->
    <link href="assets/css/custom.css" rel="stylesheet" />
  <link href="img/favicon.jpg" rel="shortcut icon" type="" />
     <!-- GOOGLE FONTS-->
   <link href='http://fonts.googleapis.com/css?family=Open+Sans' rel='stylesheet' type='text/css' />

    <script src="js/jquery-2.1.4.min.js"></script>
    <script src="js/mentor.js"></script>
	<style>
		.signinpanel {
			width: 780px;
			margin: 10% auto 0 auto;
		}
		
		.signinpanel form {
	background: rgba(255,255,255,0.2);
	border: 1px solid #ccc;
	-moz-box-shadow: 0 3px 0 rgba(12,12,12,0.03);
	-webkit-box-shadow: 0 3px 0 rgba(12,12,12,0.03);
	box-shadow: 0 3px 0 rgba(12,12,12,0.03);
	-moz-border-radius: 3px;
	-webkit-border-radius: 3px;
	border-radius: 3px;
	padding: 30px;
}
.mt5 {
    margin-top: -25px;
}
.mb20 {
    margin-bottom: 20px;
	font-size: 12px;
}
	.signinpanel .form-control {
	display: block;
	margin-top: 15px;
}
.form-control {
	-moz-border-radius: 3px;
	-webkit-border-radius: 3px;
	border-radius: 3px;
	padding: 10px;
	height: auto;
	-moz-box-shadow: none;
	-webkit-box-shadow: none;
	box-shadow: none;
	font-size: 13px;
}
	</style>
</head>
<body style="background: #e4e7ea">
	
	<section>
		<div class="signinpanel">
			<div class="row">
				<div class="col-md-3"></div>
				<div class="col-md-6">
					<form method="post">
						<?php if(isset($_GET['signin'])) { ?>
							<h4><b>Login Account</b></h4>
							<p class="mt5 mb20">Login to access your account.</p>
							
							<input type="text" class="form-control" placeholder="Email Address" id='ptc_username' name='ptc_username' required autocomplete="off"/>
							
							<input type="password" class="form-control input-lg" placeholder="Your Password" id="ptc_password" name="ptc_password" required autocomplete="off"/>
								
							<div class="form-group" style="margin-top: 10px">
								<label class="checkbox-inline">
									<input type="checkbox" checked /> Remember me
								</label>
								<span class="pull-right">
									   <a href="?fixpassword" >Forget password ? </a> 
								</span>
							</div>
							
							<button class="btn btn-success btn-block btn-lg" id="loginPO"><b>Login Now</b></button>
							
						<?php } else if(isset($_GET['fixpassword'])) { 
							
						
								if(isset($_POST["participant_reset"])) {
									
									$participant_email = $_POST['participant_email'];
									$check_reguser = $con->prepare("select * from participant where email='$participant_email'"); $check_reguser->execute();
									
									if($check_reguser->rowCount() == 0) {
										echo "<div class='alert alert-info'><b>No user account is associated with the email address provided</b></div>";
									} else {
										$check_reguser_info = $check_reguser->fetch(PDO::FETCH_ASSOC);
										$name = ucfirst($check_reguser_info['name']); // participant name
										
										//check if user has a pending reset... 
										$check_verfy = $con->prepare("select * from reset_verify where email='$participant_email' and status='pending'"); $check_verfy->execute();
										if($check_verfy->rowCount() > 0) {
											$check_verfys = $check_verfy->fetch(PDO::FETCH_ASSOC);
											$code = $check_verfys["code"];
										} else {
											
											//Use for generating alpha numeric coded
											function randomKey($length) {
												$pool = array_merge(range(0,9), range('a', 'z'),range('A', 'Z'));
												$key = '';
												for($i=0; $i < $length; $i++) {
													$key .= $pool[mt_rand(0, count($pool) - 1)];
												}
												return $key;
											}
											
											$code =  strtoupper(randomKey(3) . randomKey(3) . randomKey(3));
											
											//We need to save details
											$savereset_verify = $con->prepare("insert into reset_verify (email, code, status) values ('$participant_email','$code','pending')");
											$savereset_verify->execute();
										}
																	
										
										// Email Aspect
				
				// set content type header for html email
				$headers  = 'MIME-Version: 1.0' . "\r\n";
				$headers .= 'Content-type: text/html; charset=iso-8859-1' . "\r\n";
				// set additional headers
				$headers .= 'From: FORGET PASSWORD <no-reply@giverscycler.com>' . "\r\n".'X-Mailer: PHP/' . phpversion();
				$subject = "FORGET PASSWORD RESET LINK";
				$body= "<html>
			<head>
				<title>FORGET PASSWORD RESET LINK</title>
			</head>
			<body><div>
		<div style='font-family:arial;border:2px solid #c0c0c0;padding:15px;border-radius:5px;'>
		<div style='font-size:22px;color:darkblue;font-weight:bold;'>FORGET PASSWORD RESET</div>
			<br>

		Hi $name, you have requested to reset your Giverscycler account password. Please copy or click the link below 

		<a href='https://giverscycler.com/account?ResetPswd&code=$code' target='_blank'>https://giverscycler.com/account?ResetPswd&code=$code</a>
		<br><br>
		Thanks!
		<br><br>
		GIVERSCYCLER Support
		<br><br><br>
		This is an automated response, please do not reply!
		</div></div></body>";

							//Sending Email
							mail($participant_email, $subject, $body, $headers);
							?>
								<div class='alert alert-info'><b>Reset link has been sent to your email address. Thank You!</b></div>
							<?php
									}
								}
						?>
							<h4><b>Forgot Password</b></h4>
							<p class="mt5 mb20">( Did you forget your Password?  )</p>
							
							<input type="email" name='participant_email' class="form-control input-lg" placeholder="Enter valid email address" required autocomplete="off"/>
							
							<br/>
							<button class="btn btn-success btn-block btn-lg" name='participant_reset'><b><i class="fa fa-envelope-o"></i> Get Reset Link</b></button>
							<br>
							<div align="center" style="font-size: 18px"> Already Registered ?  <a href="?signin" >Login here</a></div>
							
						<?php } else if(isset($_GET['ResetPswd'])) { 

						} ?>
					</form>
				
				</div>
			</div>	
		</div>	
	
	</section>
	
	
	
	
     <!-- SCRIPTS -AT THE BOTOM TO REDUCE THE LOAD TIME-->
    <!-- JQUERY SCRIPTS -->
      <!-- BOOTSTRAP SCRIPTS -->
    <script src="assets/js/bootstrap.min.js"></script>
    <!-- METISMENU SCRIPTS -->
    <script src="assets/js/jquery.metisMenu.js"></script>
      <!-- CUSTOM SCRIPTS -->
    <script src="assets/js/custom.js"></script>
   
</body>
</html>
