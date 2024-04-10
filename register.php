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
    <title>GiversCycler Membership Form</title>
	<!-- BOOTSTRAP STYLES-->
    <link href="assets/css/bootstrap.css" rel="stylesheet" />
     <!-- FONTAWESOME STYLES-->
    <link href="assets/css/font-awesome.css" rel="stylesheet" />
        <!-- CUSTOM STYLES-->
    <link href="assets/css/custom.css" rel="stylesheet" />
     <!-- GOOGLE FONTS-->
   <link href='http://fonts.googleapis.com/css?family=Open+Sans' rel='stylesheet' type='text/css' />
  <link href="img/favicon.jpg" rel="shortcut icon" type="" />

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
                <h2 style="margin-top: -35px; color: green">Account Opening : Register</h2>
					
					
                <h5><font color='blue' size='4px'>( Fill the form below and get started )</font></h5>
				
				
				<?php
				##PROCESSING..........
				for($i=1; $i<=10; $i++){
					$numb="0". mt_rand(11111,79654); 
				}
				
				if(isset($_POST["participant_reg"]))
				{
					$participant_name = strtolower($_POST["participant_name"]); // participant name
					$participant_email = strtolower($_POST["participant_email"]); //participant email address
					$participant_mobile = $_POST["participant_mobile"]; //participant mobile number
					$participant_pswd = substr(sha1("ponzi"),0,8).":".md5($_POST["participant_pswd"]); //Participant password
					$participant_rfr_email = $_POST["participant_rfr_email"]; // Referral Email Address
					
					//Participant Number Needs to be formatted to Nigeria own
					$position = "/^0/";
					$replaceText = 234;
					$participant_mobile = preg_replace($position, $replaceText, $participant_mobile);
					$check_registered_user = rows("SELECT * FROM `participant` where `email`='$participant_email'");
					$check_registered_name = rows("SELECT * FROM `participant` where `name`='$participant_name'");
					$check_registered_mobile = rows("SELECT * FROM `participant` where `mobile`='$participant_mobile'");
					// echo $participant_pswd;
					// if(isset($_GET['ref']))
					// {
						// $ref = $_GET['ref'];
						// $getref = $con->prepare("select * from participant where email='$ref'");
						// $getref->execute();
						// $getrefinfo = $getref->fetch(PDO::FETCH_ASSOC);
						// $refid = $getrefinfo['pid'];
					// }
					if($check_registered_user == 1)
					{	
					?>
						<div class="alert alert-warning"><b>Email address already exist!</b></div>
					<?php
					}
					else if($check_registered_name == 1)
					{	
					?>
						<div class="alert alert-warning"><b>Participant already exist!</b></div>
					<?php
					}
					else if($check_registered_mobile == 1)
					{	
					?>
						<div class="alert alert-warning"><b>Mobile Number already in use</b></div>
					<?php
					}
					else if($participant_rfr_email == $participant_email)
					{	
					?>
						<div class="alert alert-warning"><b>Sorry! You can't be a referral for your account</b></div>
					<?php
					}
					else
					{
						if(empty($participant_rfr_email))
						{
							$participant_rfr_email = "oluwatayoadeyemi@yahoo.com";
							
							### Save Account
							$getuinfo = $con->prepare("SELECT * FROM `participant` where email='$participant_rfr_email'");
							$getuinfo->execute();
							$ginfo = $getuinfo->fetch(PDO::FETCH_ASSOC);
							$pid = $ginfo['pid'];
						
						
							$save_accnt = $con->prepare("INSERT INTO `participant` (name, mobile, password, email, invite) values ('$participant_name','$participant_mobile','$participant_pswd','$participant_email','$pid')");
							if($save_accnt->execute())
							{	
								$LastINSERTID = $con->lastInsertId(); //Stands for New User ID
								$save_downlines = $con->prepare("INSERT INTO `downline` (referralID, participantID) values ('$pid', $LastINSERTID)");
								$save_downlines->execute();
							?>
								<script>
								alert("Your registration was successful, please login now");
								</script>
								<div class='alert alert-success'><b>Your registration was successful, please login now <a href='account?signin'>Click Here</a></b></div> 
							<?php
								// redirect_to("account?signin");
		// set content type header for html email
		$headers  = 'MIME-Version: 1.0' . "\r\n";
		$headers .= 'Content-type: text/html; charset=iso-8859-1' . "\r\n";
		// set additional headers
		$headers .= 'From: GIVERSCYCLER REGISTRATION <no-reply@giverscycler.com>' . "\r\n".'X-Mailer: PHP/' . phpversion();
		$subject = "GIVERSCYCLER - Profile Created";
		$body= "<html>
    <head>
        <title>GIVERSCYCLER - Profile Created</title>
    </head>
    <body><div>
<div style='font-family:arial;border:2px solid #c0c0c0;padding:15px;border-radius:5px;'>
<div style='font-size:22px;color:darkblue;font-weight:bold;'>GIVERSCYCLER - Profile Created</div>
    <br>
<center>Dear $participant_name</center><br><br>
Welcome to Giverscycler!<br>
You have now been registered on the GIVERSCYCLER platform<br><br>
What does this mean?
1. You provide to a member within the community
<br>
2. For every donation made, you’ll be rewarded with 200%. The payment method involves are bank payment and transfer. 

<br>
3. Endeavour to invite your friends to the community as these will boost up your financial status with our 10% referral bonus. 

<br><br>

Everyone of us welcome you to GIVERSCYCLER

<br><br><br>
<font color='red'><b>GIVERS ARE RECEIVRS</b></font>
<br>
</div></div></body>";
						mail($participant_email, $subject, $body, $headers);
						
						unset($participant_name);
						unset($participant_mobile);
						unset($participant_pswd);
						unset($participant_email);
						unset($participant_rfr_email);
							}
							else
							{	
							?>
								<div class="alert alert-warning"><b>Registration failed! Please try again.</b></div>
							<?php
							}
						}
						else
						{
							
							### Save Account
							$getuinfo = $con->prepare("SELECT * FROM `participant` where email='$participant_rfr_email'");
							$getuinfo->execute();
							$ginfo = $getuinfo->fetch(PDO::FETCH_ASSOC);
							$pid = $ginfo['pid'];
							if($pid == 0) //Is user found?
							{
								$pid = 1;
							}
							$save_accnt = $con->prepare("INSERT INTO `participant` (name, mobile, password, email, invite) values ('$participant_name','$participant_mobile','$participant_pswd','$participant_email','$pid')");
							
							if($save_accnt->execute())
							{	
								$LastINSERTID = $con->lastInsertId(); //Stands for New User ID
								$save_downlines = $con->prepare("INSERT INTO `downline` (referralID, participantID) values ('$pid', $LastINSERTID)");
								$save_downlines->execute();
							
							?>
								<script>
									alert("Your registration was successful, please login now");
								</script>
								<div class='alert alert-success'><b>Your registration was successful, please login now <a href='account?signin'>Click Here</a></b></div> 
							<?php
									unset($participant_name);
									unset($participant_mobile);
									unset($participant_pswd);
									unset($participant_email);
									unset($participant_rfr_email);
								// redirect_to("account?signin");
							}
							else
							{	
							?>
								<div class="alert alert-warning"><b>Registration failed! Please try again.</b></div>
							<?php
							}
						}
					}
					
					// echo $participant_pswd;
				}
				?>
				
            </div>
        </div>
         <div class="row ">
                <div class="col-md-4 col-md-offset-4 col-sm-6 col-sm-offset-3 col-xs-10 col-xs-offset-1">
                        <div class="panel panel-default">
                            <div class="panel-heading">
                        <strong>  New User ? Register Yourself </strong>  
                            </div>
                            <div class="panel-body">
                                <form role="form" method="post">
									<br/>
									<label>Nickname</label>
									<div class="form-group input-group">
										<span class="input-group-addon"><i class="fa fa-circle-o-notch"  ></i></span>
										<input type="text" name='participant_name' value='<?php if(isset($participant_name)){ echo $participant_name;}?>' class="form-control" placeholder="Your Name" required autocomplete="off"/>
									</div>
									
									<label>Email Address (Login Info)</label>
									<div class="form-group input-group">
										<span class="input-group-addon">@</span>
										<input type="email" name='participant_email' value='<?php if(isset($participant_email)){ echo $participant_email;}?>' class="form-control" placeholder="Your Email" required autocomplete="off"/>
									</div>
									
									<label>Mobile Number: </label>
									<div class="form-group input-group">
										<span class="input-group-addon"><i class="fa fa-phone"  ></i></span>
										<input type="text" class="form-control" name='participant_mobile' value='<?php if(isset($participant_mobile)){ echo $participant_mobile;}?>' placeholder="Active Mobile Number" required autocomplete="off"/>
									</div>
									
									<label>Invitee: (Leave empty if none)</label>
									<div class="form-group input-group">
										<span class="input-group-addon"><i class="fa fa-bolt"></i></span>
											<?php
											if(isset($_GET['ref']))
											{
											?>
												<input class="form-control" type="text" value="<?php echo $_GET['ref'];?>" disabled>
												<input type="hidden" name='participant_rfr_email' value="<?php echo $_GET['ref'];?>" >
											
											<?php
											}
											else
											{
											?>
												<input type="text" name='participant_rfr_email' class="form-control" value='<?php if(isset($participant_rfr_email)){ echo $participant_rfr_email;}?>'  placeholder="Referral Email" autocomplete="off"/>
											<?php
											}
											?>
									</div>
									
									<label>Your Password</label>
									<div class="form-group input-group">
										<span class="input-group-addon"><i class="fa fa-lock"  ></i></span>
										<input type="password" name="participant_pswd" class="form-control" placeholder="Enter Password" required autocomplete="off"/>
									</div>
									
                                     
                                     <button class="btn btn-success" name='participant_reg'>Register Me</button>
                                    <hr />
                                   <b> Already Registered ?  <a href="account?signin" >Login here</a></b>
                                    </form>
                            </div>
								<center><a href='index'><b><i class='fa fa-home fa-2x'></i>GO BACK TO HOME</b></a></center>
                        </div>
                    </div>
			
                
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
