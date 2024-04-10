<?php
require '../config.php';
if(isset($username) && !empty($username))
{
	// echo $username;
	
	$user_rows = rows("SELECT * FROM `participant` where email='$username'");
	if($user_rows == 0)
	{
		redirect_to("../account?signin");
		session_unset($username);
		session_destroy();
	}
	$getuinfo = $con->prepare("SELECT * FROM `participant` where email='$username'");
	$getuinfo->execute();
	$ginfo = $getuinfo->fetch(PDO::FETCH_ASSOC);
	$pid = $ginfo['pid'];
	$participant_name = $ginfo['name'];
	
	
	//First timer login
	$count_login  = rows("select * from firstlogin where username='$username'");
	if($count_login != 0 )
	{
		redirect_to("dashboard");
	}
	
}
else
{
	redirect_to("../account?signin");
}
?>
<!DOCTYPE html>
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
      <meta charset="utf-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <title>Rules & Regulations ::: Givers Cycler</title>
	<!-- BOOTSTRAP STYLES-->
    <link href="../assets/css/bootstrap.css" rel="stylesheet" />
  <link href="../img/favicon.jpg" rel="shortcut icon" type="" />
     <!-- FONTAWESOME STYLES-->
    <link href="../assets/css/font-awesome.css" rel="stylesheet" />
     <!-- MORRIS CHART STYLES-->
    <link href="../assets/js/morris/morris-0.4.3.min.css" rel="stylesheet" />
        <!-- CUSTOM STYLES-->
    <link href="../assets/css/custom.css" rel="stylesheet" />
     <!-- GOOGLE FONTS-->
   <link href='http://fonts.googleapis.com/css?family=Open+Sans' rel='stylesheet' type='text/css' />
</head>
<body>
    <div id="wrapper">
        <nav class="navbar navbar-default navbar-cls-top " role="navigation" style="margin-bottom: 0">
            <div class="navbar-header">
                <button type="button" class="navbar-toggle" data-toggle="collapse" data-target=".sidebar-collapse">
                    <span class="sr-only">Toggle navigation</span>
                    <span class="icon-bar"></span>
                    <span class="icon-bar"></span>
                    <span class="icon-bar"></span>
                </button>
                <a class="navbar-brand" href="dashboard"><img src='../img/logo.jpg' width='240px' height='40px' style='margin-top: -5px'/></a>
            </div>
  <div style="color: white; padding: 15px 50px 5px 50px; float: right; font-size: 16px;"> Today's Date: <?php echo date("l, Y-m-j h:i");?> &nbsp; <a href="logoff" class="btn btn-danger square-btn-adjust">Logout</a> </div>
        </nav>   
           <!-- /. NAV TOP  -->
                <nav class="navbar-default navbar-side" role="navigation">
            <div class="sidebar-collapse">
                <ul class="nav" id="main-menu">
				<li class="text-center">
                    <img src="../assets/img/find_user.png" class="user-image img-responsive"/>
					</li>
				
					
                    <li>
                        <a href="dashboard"><i class="fa fa-dashboard fa-3x"></i> Dashboard</a>
                    </li>
					                 
                    <li>
                        <a href="#"><i class="fa fa-user fa-3x"></i> Profile<span class="fa arrow"></span></a>
                        <ul class="nav nav-second-level">
                            <li>
                                <a href="edit_passwd">Change Password</a>
                            </li>
                            <li>
                                <a href="bank_info">Bank Account</a>
                            </li>
                        </ul>
					</li>
					     
                    <li>
                        <a href="packages"><i class="fa fa-sitemap fa-3x"></i> Packages</a>
                    </li>
					                   
                    <li>
                        <a href="#"><i class="fa fa-sort-amount-asc fa-3x"></i> Referal Panel<span class="fa arrow"></span></a>
                        <ul class="nav nav-second-level">
                            <li>
                                <a href="referral?refBonus">Referral Bonus</a>
                            </li>
                            <li>
                                <a href="referral?ref">My Referral</a>
                            </li>
							<li>
                                <a href="referral?reflink">Invitation Link</a>
                            </li>
                        </ul>
					</li>
					
                    <li>
                        <a href="#"><i class="fa fa-exchange fa-3x"></i> Transaction History<span class="fa arrow"></span></a>
                        <ul class="nav nav-second-level">
                            <li>
                                <a href="transaction?ph-history">PH History</a>
                            </li>
                            <li>
                                <a href="transaction?gh-history">GH History</a>
                            </li>
                        </ul>
					</li>
					
                     <li>
                        <a  href="gh-testimonial?testimonies"><i class="fa fa-smile-o fa-3x"></i> Letter of Joy</a>
                    </li>
					
                     <li>
                        <a  href="ticket"><i class="fa fa-book fa-3x"></i> Ticket</a>
                    </li>
                    <li>
                        <a  href="logoff"><i class="fa fa-sign-out fa-3x"></i> Sign Out</a>
                    </li>
					
                </ul>
               
            </div>
            
        </nav>  
        <!-- /. NAV SIDE  -->
        <div id="page-wrapper" >
            <div id="page-inner">
                <div class="row">
                    <div class="col-md-12">
                     <h2>Rules & Regulations</h2>   
                        <h5>Welcome <b><?php echo ucfirst($participant_name);?></b> , Love to see you back. </h5>
                    </div>
					
                </div>              
                 <!-- /. ROW  -->
                  <hr />
				  
				
                 <!-- /. ROW  -->
                
				<div class="row">
                    <div class="col-md-12" style='color: #000; font-size: 16px; text-align: justify'>
						<h1><font size='10px'>Important System Update</font></h1>
						<font color='red'><small><b>PLEASE READ EVERY LINE CAREFULLY, IGNORANCE WILL NOT BE AN EXCUSE </b></small></font>
						<br/><br/>
						Dear Participant,<br/><br/>
						The sustainability of the community depends on You. We are working day and night to ensure that your money is safe and the system is balance.
						<br>
						Please note the following conditions below;<br/><br/>
						<i class="fa fa-hand-o-right"></i> Once you activate any of our package, you will be merge within 24-96 hours (1-4days), ensure to have your money handy and always redeem your pledge on time.
						<br/><br/>
						<i class="fa fa-hand-o-right"></i> Once your PH (Provide Help) has been merged, you have only 24hours to redeem your pledge, do not upload fake Proof of Payment (POP) to avoid you losing your membership. Strictly business!
						<br/><br/>
						<i class="fa fa-hand-o-right"></i> After making payment, inform reciever to acknowledge your payment, once your payment has been approved, the system automate your withdrawal after 14days upon completion of your payment. Within 14 - 21days, your funds will be paid fully  into your bank account provided . i.e You do not need to Get Help yourself, the system does that!
						<br/><br/>
						<i class="fa fa-hand-o-right"></i> Automatic Confirmation of Payments : All payments not flagged as fake POP MUST be confirmed within 72hours of POP upload, else the system automatically confirm the payment. Flagging as Fake POP will halt this action.
						<br/><br/>
						Please be rest assured that the platform is as stable as a rock. 
						<br/><br/>
						<b>Important Question</b>; Do you know anybody who has lost money in the community before? . Your answer will definitely be a <b>NO</b>. And it is our promise that it will remain this way. 
						<br/><br/>
							<center><font color='red' size='6'><b>.:::WE DEAL RUTHLESSLY WITH CYBER BEGGARS:::.</b></font></center>
						<br/><br/>
						<B>I do solemnly promise to adhere to the rules and regulation guiding the community</b>
						<br/><br/>
							<div class='col-md-3'></div><div class='col-md-6'><a href='../action.php?term=agree'><button class='btn btn-lg'>YES, I AGREE</button></a> <a href='../action.php?term=disagree' onclick="return confirm('You are about to forfeit your membership, you might not be able to participate in the system again\n\n Do you want to proceed?');" ><button class='btn btn-lg'>NO, DELETE ACCOUNT</button></a> </div><div class='col-md-3'></div>
					</div>     
                </div>     
                 <!-- /. ROW  -->           
    </div>
             <!-- /. PAGE INNER  -->
            </div>
         <!-- /. PAGE WRAPPER  -->
        </div>
     <!-- /. WRAPPER  -->
    <!-- SCRIPTS -AT THE BOTOM TO REDUCE THE LOAD TIME-->
    <!-- JQUERY SCRIPTS -->
    <script src="../assets/js/jquery-1.10.2.js"></script>
      <!-- BOOTSTRAP SCRIPTS -->
    <script src="../assets/js/bootstrap.min.js"></script>
    <!-- METISMENU SCRIPTS -->
    <script src="../assets/js/jquery.metisMenu.js"></script>
     <!-- MORRIS CHART SCRIPTS -->
     <script src="../assets/js/morris/raphael-2.1.0.min.js"></script>
    <script src="../assets/js/morris/morris.js"></script>
      <!-- CUSTOM SCRIPTS -->
    <script src="../assets/js/custom.js"></script>
    
   
</body>
</html>
