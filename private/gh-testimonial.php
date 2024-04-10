<?php
require '../config.php';
require 'UrgentMerging.php';
if(isset($username))
{
	// echo $username;
	
	//Activating Strong Validaion.... If not a participant
	$user_rows = rows("SELECT * FROM `participant` where email='$username'");
	if($user_rows == 0)
	{
		session_unset($username);
		session_destroy();
		//Go and login
		redirect_to("../account?signin");
	}
	
	$getuinfo = $con->prepare("SELECT * FROM `participant` where email='$username'");
	$getuinfo->execute();
	$ginfo = $getuinfo->fetch(PDO::FETCH_ASSOC);
	$pid = $ginfo['pid'];
	$participant_name = $ginfo['name'];
	$to_pay_number = $ginfo['mobile'];
	$user_status = $ginfo['status'];
	$blocked = strtoupper(substr(md5("blocked"), -4));
	if($user_status != 'active')
	{
		// redirect_to("ticket?msg=$blocked");
	}
	
	//Bank account
	$get_account = $con->prepare("SELECT * FROM `bankaccount` where participant='$pid'");
	$get_account->execute();
	$get_account_rows = rows("SELECT * FROM `bankaccount` where participant='$pid'");
	$getBANKINFO = $get_account->fetch(PDO::FETCH_LAZY);
	$bankName = $getBANKINFO["bankName"];
	$merchantName = strtoupper($getBANKINFO["merchantName"]);
	$merchantNo = $getBANKINFO["merchantNo"];
	
	
	$check_allpendingGH = rows("Select * from gethelp where participantID='$pid' and merge='pending'");
	
	$available_PH = $con->prepare("select * from providehelp where participantID='$pid' and RegBonus='0' and status='Confirmed'");
	$available_PH->execute();
	$available_PH_row = rows("select * from providehelp where participantID='$pid' and RegBonus='0' and status='Confirmed'");
	
	if($available_PH_row == 0)
	{
		$return_amnt = 0;
		$wallet = '';
		$amntPH = 0;
	}
	else
	{
		$total_return_amnt = 0;
		for($i = 1; $i<=$available_PH_row; $i++)
		{
			$available_PH_INFO = $available_PH->fetch(PDO::FETCH_ASSOC);
			$merge = $available_PH_INFO['merge'];
			$status = $available_PH_INFO['status'];
			$wallet = $available_PH_INFO['wallet'];
			$paid = $available_PH_INFO['paid'];
			$amntPH = $available_PH_INFO['amntPH'];
			$RegBonus = $available_PH_INFO['RegBonus'];
			//Whats d name of d package
				
			$get_allpackkages = $con->prepare("select * from packages where package_id='$wallet'");
			$get_allpackkages->execute();
			$get_allpackkages_INFO = $get_allpackkages->fetch(PDO::FETCH_ASSOC);
			$package_id = $get_allpackkages_INFO['package_id'];
			$package_name = $get_allpackkages_INFO['package_name'];
			
			$return_amnt = $available_PH_INFO['return_amnt'];
			$total_return_amnt += $return_amnt;
		}
	}
	$ph_NOTPAID = rows("select * from providehelp where participantID='$pid' and regBonus=0 and merge='NO'");
		
		// echo $return_amnt;
	
	//First timer login
	$count_login  = rows("select * from firstlogin where username='$username'");
	if($count_login == 0)
	{
		redirect_to("termsupdate");
		
	}
	else if($count_login != 0 && $get_account_rows == 0)
	{
		redirect_to('bank_info');
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
    <title>TESTIMONIAL ::: Givers Cycler</title>
	<!-- BOOTSTRAP STYLES-->
    <link href="../assets/css/bootstrap.css" rel="stylesheet" />
     <!-- FONTAWESOME STYLES-->
    <link href="../assets/css/font-awesome.css" rel="stylesheet" />
     <!-- MORRIS CHART STYLES-->
    <link href="../assets/js/morris/morris-0.4.3.min.css" rel="stylesheet" />
        <!-- CUSTOM STYLES-->
    <link href="../assets/css/custom.css" rel="stylesheet" />
     <!-- GOOGLE FONTS-->
   <link href='http://fonts.googleapis.com/css?family=Open+Sans' rel='stylesheet' type='text/css' />
  <link href="/templates/ifreedom-fjt/favicon.ico" rel="shortcut icon" type="image/vnd.microsoft.icon" />
  <link href="../img/favicon.jpg" rel="shortcut icon" type="" />
   
    <!-- JQUERY SCRIPTS -->
	
<style type="text/css">
form{float: left;width: 100%;}
img, embed{margin-top: 20px;}
</style>
    <script src="../js/jquery-2.1.4.min.js"></script>
    <script src="../js/mentor.js"></script>
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
					
					<?php if($check_allpendingGH >=1)
					{?>
					<li>
                        <a  href="forum"><i class="fa fa-book fa-3x"></i> FORUM DISCUSSION</a>
                    </li>
					<?php }?>
					
                     <li>
                        <a  href="gh-testimonial?testimonies" class="active-menu" ><i class="fa fa-smile-o fa-3x"></i> Letter of Joy</a>
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
                     <h2>TESTIMONIAL LETTER</h2>   
                        <h5>Hello <b><?php echo ucfirst($participant_name);?>, write your testimony</b></h5>
                    </div>
					
                </div>              
                 <!-- /. ROW  -->
                  <hr />
                
				
                 <!-- /. ROW  -->
                
				<div class="row">
				
				
			<?php 
			if(isset($_GET["ghID"]))
			{
				//Lets See if attachment is available
				$ghID = $_GET["ghID"];
				$check_allrow = rows("Select * from merge_gh where gh_participantID='$pid' and ghID='$ghID' and gh_letter='YES'");
				if($check_allrow >= 2)
				{
					redirect_to("gh-testimonial?testimonies");
					//We need to manually update the gh testimonial
					$updateGHLETTER = $con->prepare("update merge_gh set gh_letter='YES' where gh_participantID='$pid' and ghID='$ghID'");
					$updateGHLETTER->execute();
					
				}
				
					
			?>
					<div class='col-md-12'>
					
						<br>
						<div id="result"></div>
						<form method='post' id='ghletter_info'>
							<textarea class='form-control input-lg' rows='7' name='gh_letter' id='gh_letter'></textarea>
							<input value="<?php echo $pid;?>" name='participantID' id='participantID' type='hidden'>
							<input value="<?php echo $ghID;?>" name='ghID' id='ghID' type='hidden'>
							<br>
							<button class="btn btn-success btn-lg" id='submitGH'><b>Submit Testimony</b></button>
						</form>
					</div>
						
				<?Php
				
			}
			else if(isset($_GET["testimonies"]))
			{
				?>
					<center><a href='testimonial' class='btn btn-default btn-lg'>OUR TESTIMONIAL</a></center><br><br>
				<?php
				$getalltestimony = $con->prepare("select * from testimonies where participantID='$pid' order by testimonial_id desc");
				$getalltestimony->execute();
				$getalltestimony_row = rows("select * from testimonies where participantID='$pid' order by testimonial_id desc");
				if($getalltestimony_row ==0)
				{
					echo "<div class='alert alert-info' style='width: 99.7%; '><b><i class='fa fa-exclamation-circle fa-3x'></i> <p style='margin-top: -10px;' class='pull-left'> You do not have a testimony  yet, activate a plan and share your testimony <a href='packages' target='_blank' style='color: red'>GET STARTED</a></div>";
				}
				else
				{
					for($i=1; $i<=$getalltestimony_row; $i++)
					{
						$getalltestimony_info = $getalltestimony->fetch(PDO::FETCH_ASSOC);
						$gh_letter = $getalltestimony_info['gh_letter'];
						$date_written = $getalltestimony_info['date_written'];
				?>
						<div class='col-md-12'>
							<?php echo nl2br(ucfirst($gh_letter)) . '<br><br>' . $date_written;?>
							<hr style='border-bottom: 1px dotted; color: #000'>
						</div>
				<?php
					}
				}
			}
			else
			{
				redirect_to("dashboard");
			}
				?>
                </div>     
                 <!-- /. ROW  -->           
    </div>
             <!-- /. PAGE INNER  -->
            </div>
         <!-- /. PAGE WRAPPER  -->
        </div>
     <!-- /. WRAPPER  -->
    <!-- SCRIPTS -AT THE BOTOM TO REDUCE THE LOAD TIME-->
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
