<!DOCTYPE html>
<html lang="en">
  <head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>GiversCycler ::: Givers are receivers</title>
    <meta name="description" content="Free Bootstrap Theme by BootstrapMade.com">
    <meta name="keywords" content="free website templates, free bootstrap themes, free template, free bootstrap, free website template">
    
    <link rel="stylesheet" type="text/css" href="https://fonts.googleapis.com/css?family=Open+Sans|Candal|Alegreya+Sans">
    <link rel="stylesheet" type="text/css" href="css/font-awesome.min.css">
    <link rel="stylesheet" type="text/css" href="css/bootstrap.min.css">
    <link rel="stylesheet" type="text/css" href="css/imagehover.min.css">
    <link rel="stylesheet" type="text/css" href="css/style.css">
    <link rel="stylesheet" type="text/css" href="css/priceplan.css">
	<link href="img/favicon.jpg" rel="shortcut icon" type="" />
	
    <script src="js/jquery.min.js"></script>
    <script src="js/jquery.easing.min.js"></script>
    <script src="js/bootstrap.min.js"></script>
    <script src="js/custom.js"></script>
    
	
  </head>
  <body>
  
    <!--Navigation bar-->
    <nav class="navbar navbar-default navbar-fixed-top">
      <div class="container">
        <div class="navbar-header">
        <button type="button" class="navbar-toggle" data-toggle="collapse" data-target="#myNavbar">
          <span class="icon-bar"></span>
          <span class="icon-bar"></span>
          <span class="icon-bar"></span>
        </button>
        <a class="navbar-brand" href="index">GIVERS<span>CYCLER</span></a>
        </div>
        <div class="collapse navbar-collapse" id="myNavbar">
			<ul class="nav navbar-nav navbar-right">
				<li><a href="index"><i class='fa fa-home'></i> Home</a></li>
				<li><a href="how-it-works"><i class='fa fa-recycle'></i> How It Works</a></li>
				<li><a href="faq"><i class='fa fa-question-circle'></i> FAQ</a></li>
				<li class='active'><a href="contact"><i class='fa fa-envelope'></i> Contact</a></li>
				<li><a href="account?signin" target='_blank'>Sign in</a></li>
				<li class="btn-trial"><a href="register" target='_blank'>Create Account</a></li>
			</ul>
        </div>
      </div>
    </nav>
    <!--/ Navigation bar-->
	
    <!--Banner-->
    <div class="banner">
      <div class="bg-color">
        <div class="container">
          <div class="row">
            <div class="banner-text text-center">
              <div class="text-border">
                <h2 class="text-dec">Trust & CONFIDENCE</h2>
              </div>
              <div class="intro-para text-center quote">
                <p class="big-text">Building a system that works...</p>
                <p class="small-text"></p>
                <a href="register" class="btn">GET STARTED</a>
              </div>
              <a href="#feature" class="mouse-hover"><div class="mouse"></div></a>
            </div>
          </div>
        </div>
      </div>
    </div>
    <!--/ Banner-->
    <!--Feature-->
    <section id ="feature" class="section-padding">
		<div class="container">
			
			<div class='col-md-7'>
			
				<h2 style="text-transform: uppercase;">We are here to help you</h2>
				<hr class="bottom-line" style=' background: #5FCF80;margin-top: -20px; width: 48%; margin-bottom: -10px; margin-left: 10px'>
				<br/>
				<div style='color: #000; text-align: justify;font-size: 21px;'>We are always looking forward to render our assistance to you. 
				Please, feel free to send us your complain, request and suggestions. This will enable us more in improving our way of service.
				<Br><Br>
				</div>
			
				<div style='color: #000; text-align: justify;font-size: 16px;'>
					Please feel free to contact us, our customer service center is working for you 24/7.
					<br><br>
					<?php
					
					//Sending of contact msg

					if(isset($_POST['Send_enquiry']))
					{
						$firstname = $_POST['firstname'];
						$mobile = $_POST['mobile'];
						$email = $_POST['email'];
						$subject = $_POST['subject'];
						$message	 = $_POST['message'];
						$support = 'giverscycler@gmail.com';
						
						$body = "<html>
    <head>
        <title>COMPLAINT & REQUEST</title>
    </head>
    <body><div>
<div style='font-family:arial;border:2px solid #c0c0c0;padding:15px;border-radius:5px;'>
<div style='font-size:22px;color:darkblue;font-weight:bold;'>COMPLAIN OR REQUEST</div>
    <br>
Full Name: $firstname	
	
Message: $message



Email Address: $email


 <br>

</div></div></body></html>";

			$headers  = 'MIME-Version: 1.0' . "\r\n";
			$headers .= 'Content-type: text/html; charset=iso-8859-1' . "\r\n";
			// set additional headers
			$headers .= 'From:'. $subject ."  <".$email.'>' . "\r\n".'X-Mailer: PHP/' . phpversion();
			$sendmail = mail($support, $subject, $body, $headers); // Send  it to user that is expecting					
					if($sendmail)
					{
					?>
						<div class='alert alert-success'><b>Message Sent Successfully. We will get in touch with you soon</b></div>
					<?php
					}
					else
					{
					?>
						<div class='alert alert-warning'><b>Unable to send Email</b></div>
					<?php
					}
					}
					?>
					
					<form method="post">
						<div class="row">
							<div class="col-sm-6">
								<div class="form-group">
									<label for="firstname">Full Name</label>
									<input class="form-control" id="firstname" name="firstname" type="text" autocomplete='off' required>
								</div>
							</div>
							<div class="col-sm-6">
								<div class="form-group">
									<label for="lastname">Mobile Number</label>
									<input class="form-control" id="mobile" name="mobile" type="text" autocomplete='off' required>
								</div>
							</div>
							<div class="col-sm-6">
								<div class="form-group">
									<label for="email">Email Address</label>
									<input class="form-control" id="email" name="email" type="email" autocomplete='off' required>
								</div>
							</div>
							<div class="col-sm-6">
								<div class="form-group">
									<label for="subject">Subject</label>
									<input class="form-control" id="subject" name="subject" type="text" autocomplete='off' required>
								</div>
							</div>
							<div class="col-sm-12">
								<div class="form-group">
									<label for="message">Message</label>
									<textarea id="message" name="message" class="form-control" rows='5' autocomplete='off' required></textarea>
								</div>
							</div>

							<div class="col-sm-12 text-center">
								<button type="submit" id='Send_enquiry' name='Send_enquiry' class="btn btn-default"><i class="fa fa-envelope-o"></i> Send message</button>

							</div>
						</div>
					</form>
					
				</div>
				
				
			</div>
			
			<div class='col-md-1'>
			</div>
			<div class='col-md-4'>
				<h3 class="text-uppercase" style='color: #333333'>Call center</h3>
				<div style='color: #000;'>
					Our support are much more eager in assisting you. Feel free to call our support helpdesk anytime anyday.
					<br/><br/>
					We are 24/7 available always.
					<br><br>
					<b>+14 242 713 225</b>
				</div>
				<br>
				<h3 class="text-uppercase" style='color: #333333'>ELECTRONIC SUPPORT</h3>
				<div style='color: #000;'>
					Please feel free to write an email to us using	 our electronic ticketing system.
					<br>
					<ul>
						<li><a href='mailto:support@giverscycler.com' style='color: blue;'>support@giverscycler.com</a></li>
						<li><a href='mailto:giverscycler@gmail.com' style='color: blue;'>giverscycler@gmail.com</a></li>
					</ul>
				</div>
			</div>
			
		</div>
    </section>
    <!--/ feature-->
	
    <!--Footer-->
    <footer id="footer" class="footer">
      <div class="container text-center">
    
      <ul class="social-links">
        <li><a href="#link"><i class="fa fa-twitter fa-fw"></i></a></li>
        <li><a href="#link"><i class="fa fa-facebook fa-fw"></i></a></li>
        <li><a href="#link"><i class="fa fa-google-plus fa-fw"></i></a></li>
        <li><a href="#link"><i class="fa fa-instagram fa-fw"></i></a></li>
      </ul>
		<b>© 2017  <?php //echo substr(date("Y"),2);?> Giverscycler ::: Givers are recievers.</b>
        
      </div>
    </footer>
    <!--/ Footer-->
    
  </body>
</html>