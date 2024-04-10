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
   
  <script>
  
	$(document).ready(function(){
		    var preloader = $('.preloader');
    $(window).load(function () {
        setTimeout(function(){
            $('.preloader').fadeOut('slow', function () {
            });
        },2000); // set the time here
    }); 
});
  </script>
   
</head>
<body>

	<!--PRELOADER WORKS-->
	<div class="preloader"><i class="fa fa-circle-o-notch fa-spin fa-2x"></i></div>
	
	<!--PRELOADER WORKS-->
	
    <div class="container">
        <div class="row text-center ">
            <div class="col-md-12">
                <br /><br />
			<?php
			if(isset($_GET['signin']))
			{
			?>
			
                <h2 style='color: green'> Personal Office : Login</h2>
                <h5><font color='blue' size='4px'>( Participant Login )</font></h5>
            </div>
        </div>
         <div class="row ">
			<div class="col-md-4 col-md-offset-4 col-sm-6 col-sm-offset-3 col-xs-10 col-xs-offset-1">
				<div id='result'></div>
				<div class="panel panel-default">
					<div class="panel-heading">
						<strong>   Enter Details To Login </strong>  
					</div>
					<div class="panel-body">
						<form id='gLogin' method='post'>
							
							<label>Email Address:</label>
							<div class="form-group input-group">
								<span class="input-group-addon"><i class="fa fa-tag"  ></i></span>
								<input type="text" class="form-control input-lg" placeholder="Email Address" id='ptc_username' name='ptc_username' required autocomplete="off"/>
							</div>
							
							<br/>
							<label>Enter Password:</label>
							<div class="form-group input-group">
								<span class="input-group-addon"><i class="fa fa-lock"  ></i></span>
								<input type="password" class="form-control input-lg" placeholder="Your Password" id="ptc_password" name="ptc_password" required autocomplete="off"/>
							</div>
							<div class="form-group">
								<label class="checkbox-inline">
									<input type="checkbox" checked /> Remember me
								</label>
								<span class="pull-right">
									   <a href="?fixpassword" >Forget password ? </a> 
								</span>
							</div>
                                     
							<button class="btn btn-primary" id="loginPO">Login Now</button>
							<hr />
							<b>Not yet register ? <a href="register" >Join Us</a> 
						</form>
					</div>
                       <center><a href='index'><b><i class='fa fa-home fa-2x'></i>GO BACK TO HOME</b></a></center>
                            
				</div>
			</div>
			<?php
			}
			else if(isset($_GET['fixpassword']))
			{
			?>
				
				 <h2 style='color: green; text-transform: uppercase'> Forgot Password</h2>
                <h5><font color='blue' size='4px'>( Did you forget your Password?  )</font></h5>
				
				
				<?php
					
				if(isset($_POST["participant_reset"]))
				{
					
					$participant_email = $_POST['participant_email'];
					$check_reguser = $con->prepare("select * from participant where email='$participant_email'");
					$check_reguser->execute();
					$check_reguser_row = rows("select * from participant where email='$participant_email'");
					if($check_reguser_row == 0)
					{
						echo "<div class='alert alert-info'><b>No user account is associated with the email address provided</b></div>";
					}
					else
					{
						$check_reguser_info = $check_reguser->fetch(PDO::FETCH_ASSOC);
						$name = ucfirst($check_reguser_info['name']); // participant name
						
						//Use for generating alpha numeric coded
						function randomKey($length) {
							$pool = array_merge(range(0,9), range('a', 'z'),range('A', 'Z'));
							$key = '';
							for($i=0; $i < $length; $i++) {
								$key .= $pool[mt_rand(0, count($pool) - 1)];
							}
							return $key;
						}

						$code =  strtoupper(randomKey(3) . '-' . randomKey(3) . '-' . randomKey(3));
						
						
		
		//Email Aspect
		
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

<a href='https://giverscycler.com/account?ResetPswd&email=$participant_email&code=$code' target='_blank'>https://giverscycler.com/account?ResetPswd&email=$participant_email&code=$code</a>
<br><br>
Thanks!
<br><br>
GIVERSCYCLER Support
<br><br><br>
This is an automated response, please do not reply!
</div></div></body>";
		

					//Sending Email
					mail($participant_email, $subject, $body, $headers);		
						// echo $participant_email;
						// echo $participant_pswd;
						
					//We need to save details
					$savereset_verify = $con->prepare("insert into reset_verify (email, code, status) values ('$participant_email','$code','pending')");
					$savereset_verify->execute();
					?>
						<div class='alert alert-info'><b>A mail has been sent to your email address. Thank You!</b></div>
					<?php
					
					}
				}
				?>
				
            </div>
        </div>
         <div class="row ">
                <div class="col-md-6 col-md-offset-4 col-sm-6 col-sm-offset-3 col-xs-10 col-xs-offset-1">
                        <div class="panel panel-default">
                            <div class="panel-heading">
                        <strong>  Enter your details </strong>  
                            </div>
                            <div class="panel-body">
                                <form role="form" method="post">
									<br/>
									<label>Enter your email address</label>
									<div class="form-group input-group">
										<span class="input-group-addon"><i class="fa fa-user"  ></i></span>
										<input type="email" name='participant_email' class="form-control input-lg" placeholder="Provide registered email address" required autocomplete="off"/>
									</div>
									
									
									<div class="col-sm-12 text-center">
										<button class="btn btn-default" name='participant_reset'><i class="fa fa-envelope-o"></i> Get Reset Link</button>
                                    </div>
									<hr />
									<br/>
									<br/>
                                   <b> Already Registered ?  <a href="?signin" >Login here</a></b>
                                    </form>
                            </div>
                           
                        </div>
                    </div>
			<?php
			}
			else if(isset($_GET['ResetPswd']) && isset($_GET['email']) && isset($_GET['code']))
			{
				$code = $_GET['code'];
				$email = $_GET['email'];
				$check_reset_code = rows("select * from reset_verify where email='$email' and code='$code' and status='pending'");
				if($check_reset_code == 0)
				{
				?>
						<div class='alert alert-info'><b>Invalid Reset code</b></div>
				<?php
				
				}
				else
				{
					$check_reguser = $con->prepare("select * from participant where email='$email'");
					$check_reguser->execute();
					$check_reguser_info = $check_reguser->fetch(PDO::FETCH_ASSOC);
					$pid = $check_reguser_info['pid']; // participant name
					// echo $pid;
					if(isset($_POST['changePSWD']))
					{
						$change_pswd = substr(sha1("ponzi"),0,8).":".md5($_POST['newpass']);
						$updateUSER = $con->prepare("update participant set password='$change_pswd' where pid='$pid'");
						$updateUSER->execute();
						
						//since participant has been able to change his or her password then we need to update the code 
						$updateCode = $con->prepare("update reset_verify set status='used' where email='$email' and code='$code'");
						$updateCode->execute();
					
					?>
							<div class='alert alert-success'><b>Password Modified! You can now login <a href="account?signin" >Login here</a></b></div>
					<?php
					}
			?>
				
				<h2 style='color: green; text-transform: uppercase'> Reset Password</h2>
				
				
				<?php
				
				?>
				
            </div>
        </div>
         <div class="row ">
                <div class="col-md-6 col-md-offset-4 col-sm-6 col-sm-offset-3 col-xs-10 col-xs-offset-1">
                        <div class="panel panel-default">
                            <div class="panel-heading">
                        <strong>  Enter your details </strong>  
                            </div>
                            <div class="panel-body">
                                <form role="form" method="post">
									<br/>
									<label>Email Address:</label>
									<div class="form-group input-group">
										<span class="input-group-addon"><i class="fa fa-envelope-o"></i></span>
										<input type="email" name='participant_email' class="form-control input-lg" value="<?php echo $email;?>" disabled/>
									</div>
									<br/>
									<label>Reset Code: </label>
									<div class="form-group input-group">
										<span class="input-group-addon"><i class="fa fa-lock"  ></i></span>
										<input type="email" name='code' class="form-control input-lg" value="<?php echo $code;?>" disabled/>
									</div>
									<br/>
									<label>Enter your password:</label>
									<div class="form-group input-group">
										<span class="input-group-addon"><i class="fa fa-key"  ></i></span>
										<input type="password" name='newpass' class="form-control input-lg" placeholder="Enter your new password" required autocomplete="off"/>
									</div>
									
									
									<div class="col-sm-12 text-center">
										<button class="btn btn-default" name='changePSWD'><i class="fa fa-pencil"></i> Change Password</button>
                                    </div>
									<hr />
									<br/>
									<br/>
                                   <b> Already Registered ?  <a href="?signin" >Login here</a></b>
                                    </form>
                            </div>
                           
                        </div>
                    </div>
			<?php
				}
			}
			else
			{
				redirect_to("account?signin");
			}
			?>
                
        </div>
    </div>


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
