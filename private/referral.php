<?php
require '../config.php';

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
		redirect_to("ticket?msg=$blocked");
	}
	
	
	$check_allpendingGH = rows("Select * from gethelp where participantID='$pid' and merge='pending'");
	
	//Bank account
	$get_account = $con->prepare("SELECT * FROM `bankaccount` where participant='$pid'");
	$get_account->execute();
	$get_account_rows = rows("SELECT * FROM `bankaccount` where participant='$pid'");
	$getBANKINFO = $get_account->fetch(PDO::FETCH_LAZY);
	$bankName = $getBANKINFO["bankName"];
	$merchantName = strtoupper($getBANKINFO["merchantName"]);
	$merchantNo = $getBANKINFO["merchantNo"];
	
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
    <title>Referral ::: Givers Cycler</title>
	<!-- BOOTSTRAP STYLES-->
    <link href="../assets/css/bootstrap.css" rel="stylesheet" />
     <!-- FONTAWESOME STYLES-->
    <link href="../assets/css/font-awesome.css" rel="stylesheet" />
     <!-- MORRIS CHART STYLES-->
    <link href="../assets/js/morris/morris-0.4.3.min.css" rel="stylesheet" />
        <!-- CUSTOM STYLES-->
    <link href="../assets/css/custom.css" rel="stylesheet" />
  <link href="../img/favicon.jpg" rel="shortcut icon" type="" />
     <!-- GOOGLE FONTS-->
   <link href='http://fonts.googleapis.com/css?family=Open+Sans' rel='stylesheet' type='text/css' />
  <link href="/templates/ifreedom-fjt/favicon.ico" rel="shortcut icon" type="image/vnd.microsoft.icon" />
   
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
                        <a href="#" class="active-menu" ><i class="fa fa-sort-amount-asc fa-3x"></i> Referal Panel<span class="fa arrow"></span></a>
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
                     <h2>REFERRAL PANEL</h2>   
                        <h5>Hey <b><?php echo ucfirst($participant_name);?>, welcome to your referral panel, coolest money making engine room</b></h5>
                    </div>
					
                </div>              
                 <!-- /. ROW  -->
                  <hr />
                
				
                 <!-- /. ROW  -->
                
				<div class="row">
					<div class='col-md-12'>
					<?php
						if(isset($_GET['reflink']))
						{
						?>
							<font size='5' weight="700px"><b>My Referral Link : <div class="alert alert-info">https://giverscycler.com/register?ref=<?php echo $username;?></div></font>
							<br><br>Copy your referral link below and start earning 10% on all payment made by your referral</b><br><br>
							<textarea class="form-control input-lg" autofocus>https://giverscycler.com/register?ref=<?php echo $username;?></textarea>
						<?php
						}
						else if(isset($_GET["ref"]))
						{
						?>
							<font size='38px' color='#37363e'><b>My Referrals : </b></font>
							<br><br>
							
						<?php
							$searchallreferral = $con->prepare("SELECT * FROM `downline` where referralID='$pid'");
							$searchallreferral->execute();
							$searchallreferral_rows = rows("SELECT * FROM `downline` where referralID='$pid'");
							if($searchallreferral_rows == 0)
							{
							?>
								<div class="alert alert-info"><b>You do not have any invitee yet. <a href='?reflink' target='_blank' style='color: red'>GET REFERRAL LINK</a></div>
							<?php
							}
							else
							{
							?>
								<table class="table table-bordered table-responsive table-hover">
								<tr>
									<td>ID</td>
									<td>MEMBER</td>
									<td>PHONE</td>
									<td>EMAIL</td>
									<td>STATUS</td>
								</tr>
								<tr>
							<?php
								for($i=1; $i<=$searchallreferral_rows; $i++)
								{
									$searchallreferral_INFO = $searchallreferral->fetch(PDO::FETCH_ASSOC);
									$downline_id = $searchallreferral_INFO["participantID"];
									
									//We need participant name
									$getparticipant = $con->prepare("SELECT * FROM `participant` where pid='$downline_id'");
									$getparticipant->execute();
									$getparticipant_INFO = $getparticipant->fetch(PDO::FETCH_ASSOC);
									$referred_name = $getparticipant_INFO['name'];
									$referred_mobile = $getparticipant_INFO['mobile'];
									$referred_email = $getparticipant_INFO['email'];
									$referred_status = $getparticipant_INFO['status'];
									if(!empty($referred_name) ||!empty($referred_mobile) ||!empty($referred_email) ||!empty($referred_status))
									{
								?>
										<td><?php echo $i;?></td>
										<td><?php echo ucfirst($referred_name);?></td>
										<td><?php echo $referred_mobile;?></td>
										<td><?php echo strtolower($referred_email);?></td>
										<td><?php echo ucfirst($referred_status);?></td>
										</tr>
								<?php
									}
								}
								?>
								
								</table>
								<?php
							}
						}
						else if(isset($_GET['refBonus']))
						{
							#########
							# MOst crucial Aspect
							#########
							$searchallbonus = $con->prepare("SELECT * FROM `referral` where referralID='$pid' and status!='Withdraw' and status!='Cancelled' order by status");
							$searchallbonus->execute();
							$searchallbonus_rows = rows("SELECT * FROM `referral` where referralID='$pid' and status!='Withdraw' and status!='Cancelled' order by status");
							// echo $searchallreferral_rows;
							if($searchallbonus_rows == 0)
							{
							?>
								<div class="alert alert-info"><b>No referral bonus yet.........  <a href='?reflink' target='_blank' style='color: red'>GET YOUR REFERRAL LINK</a></div>
							<?php
							}
							else
							{
							
								//Automated GH
								$sum_referralBonus = 0;
								$total_GH = $con->prepare("select * from referral where status='Confirmed' and referralID='$pid'");
								$total_GH->execute();
								for($i=1; $i<=rows("select * from referral where status='Confirmed' and referralID='$pid'"); $i++)
								{
									$GHinfo = $total_GH->fetch(PDO::FETCH_ASSOC);
									$return_amnt = $GHinfo['referralBonus'];
									$phID = $GHinfo['phID'];
									$sum_referralBonus += $return_amnt;
									
									//Since we are bringing it out in batches, then we need to Withdraw all
								}
								//Does user has an active GH rolling???
								
								$is_activeGH = rows("SELECT * FROM `gethelp` where participantID='$pid' and merge!='YES'");
								if($is_activeGH > 0)
								{
									if($sum_referralBonus == 2000)
									{
									?>
										<a href="../action?IS_greater&amnt=2000&pid=<?php echo $pid;?>" class="btn btn-default btn-lg" onclick="return confirm('You are about withdraw &#8358;2000 from your wallet');" >REDEEM HELP</a><br><br>
									<?php
									}
									else if($sum_referralBonus > 2000)
									{
									?>
										<a href="../action?IS_greater&amnt=<?php echo $sum_referralBonus;?>&pid=<?php echo $pid;?>" class="btn btn-default btn-lg" onclick="return confirm('You are about withdraw &#8358;'+ <?php echo $sum_referralBonus;?> +' from your wallet');" >REDEEM HELP</a><br><br>
									<?php
									}
								}
								else
								{
								?>
									<a href='#' class='btn btn-default btn-lg' onclick="return confirm('Sorry! You are required to provide help before getting paid');"><b>REDEEM HELP ALERT</b></a>
									<br><br>
								
								<?php
								}
								?>
								<font size="4"><b>Available Fund for Withdrawal: &#8358;<?php echo number_format($sum_referralBonus);?></b></font>
								<table class="table table-bordered table-responsive table-hover">
								<tr>
									<td>ID</td>
									<td>MEMBER</td>
									<td>DONATION AMOUNT</td>
									<td>MY BONUS</td>
								</tr>
								<tr>
							<?php
								for($i=1; $i<=$searchallbonus_rows; $i++)
								{
									$searchallbonus_INFO = $searchallbonus->fetch(PDO::FETCH_ASSOC);
									$downline_id = $searchallbonus_INFO["participantID"]; //Referred User
									$referralBonus = $searchallbonus_INFO["referralBonus"]; //10% of PH
									$status = $searchallbonus_INFO["status"]; //10% of PH
									
									//Participant Name
									$getparticipant = $con->prepare("SELECT * FROM `participant` where pid='$downline_id'");
									$getparticipant->execute();
									$getparticipant_INFO = $getparticipant->fetch(PDO::FETCH_ASSOC);
									$referred_name = $getparticipant_INFO['name'];
									// $sum_referralBonus += $referralBonus;
									// echo $referralBonus . '<br>';
								
								?>
									<td><b><?php echo $i;?></b></td>
									<td><b><?php if(empty($referred_name)){?> System Refund <?php } else { echo ucfirst($referred_name);}?></b></td>
									<td><b><?php echo number_format($referralBonus * 10);?></b></td>
									<td><b><?php if($status == "Confirmed"){ ?> <font color="green"><?php echo number_format($referralBonus);?></font><?php } else if($status == "Unconfirmed"){ ?> <font color="red"><?php echo number_format($referralBonus);?></font><?php }?></b></td>
									</tr>
								<?php
								}?>
								
								</table>
								<?php
							}
						}
					?>
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
