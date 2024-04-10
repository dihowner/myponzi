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
	
	//Has participant reead the latest news??? Oh No!
	$news_read = rows("SELECT * FROM `newslogged` where loggedID='$username'");
	$getallForum = $con->prepare("SELECT * FROM `forumtopics`");
	$getallForum->execute();
	$getallForum_INFO = $getallForum->fetch(PDO::FETCH_ASSOC);
	$topicid = $getallForum_INFO['topicid'];	
	if($news_read == 0)
	{
		redirect_to("forum?viewtopic=$topicid");
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
	
	//Bank account
	$get_account = $con->prepare("SELECT * FROM `bankaccount` where participant='$pid'");
	$get_account->execute();
	$get_account_rows = rows("SELECT * FROM `bankaccount` where participant='$pid'");
	$getBANKINFO = $get_account->fetch(PDO::FETCH_LAZY);
	$bank_Name = $getBANKINFO["bankName"];
	$accntName = strtoupper($getBANKINFO["merchantName"]);
	$accntNo = $getBANKINFO["merchantNo"];
	
	
	//We need to redirect user to write letter of happiness
	$check_all= $con->prepare("Select * from merge_gh where gh_participantID='$pid' and status='Confirmed'");
	$check_all->execute();
	$check_allrow = rows("Select * from merge_gh where gh_participantID='$pid' and status='Confirmed'");
	for($i=1; $i<=$check_allrow; $i++)
	{
		$check_allINFO = $check_all->fetch(PDO::FETCH_ASSOC);
		$gh_letter = $check_allINFO['gh_letter'];
		$ghID = $check_allINFO['ghID'];
		if(empty($gh_letter))
		{
			redirect_to("gh-testimonial?ghID=$ghID");
		}
	}
	
	
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
	// echo $count_login;
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
    <title>Member Panel ::: Givers Cycler</title>
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
  <link href="../img/favicon.jpg" rel="shortcut icon" type="" />
   
    <!-- JQUERY SCRIPTS -->
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
                        <a class="active-menu"  href="dashboard"><i class="fa fa-dashboard fa-3x"></i> Dashboard</a>
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
                        <a  href="forum"><i class="fa fa-book fa-3x"></i> FORUM DISCUSSION</a>
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
                     <h2>Dashboard</h2>   
                        <h5>Welcome <b><?php echo ucfirst($participant_name);?></b> , Love to see you back. </h5>
                    </div>
					
					<div class='col-md-12'>									
						<div class='alert alert-info'><font size='5'><b>JOIN OUR FORUM DISCUSSION PORTAL <a href='forum' style='color: red'>JOIN NOW</a></font></b></div>
					</div>
										<?php
										if(isset($_GET["msg"]))
										{
											$msg = $_GET["msg"];
											if($msg == substr(md5("Your request was successful. Thank You"), -4))
											{
											?>
												<div class='col-md-12'>
													
													<div class='alert alert-success' style='width: 100%; color: #000; font-weight: 2400px'><b>YOUR REQUEST WAS SUCCESSFUL. THANK YOU</b></div>
										
												</div>
												
											<?php
											}
										}
					$checkallGH_row_topay = rows("SELECT * FROM `merge_gh` where participantID='$pid' and attachment=''");
					if($checkallGH_row_topay > 0)
					{
					?>
					<div class='col-md-12'>									
						<div class='alert alert-info'><font size='5'><b>YOU HAVE BEEN MERGED <i class="fa fa-exclamation-circle fa-2x"></i></font></b></div>
					</div>
					<?php
					}
					
					?>
						<!---PASSING MSG-->
						<?php
						if(isset($_GET["msg"]))
						{
							$msg = $_GET["msg"];
							if($msg == substr(md5("OPERATION SUCCESSFUL"), -4))
							{
							?>
								<script>
									alert("OPERATION SUCCESSFUL");
								</script>
							<?php
							}
							else if($msg == substr(md5("PAYMENT CONFIRMED. THANK YOU"), -4))
							{
							?>
								<script>
									alert("PAYMENT CONFIRMED. THANK YOU");
								</script>
							<?php
							}
							else if($msg == substr(md5("Transaction not found"), -4))
							{
							?>
								<script>
									alert("Transaction not found");
								</script>
							<?php
							}
							else if($msg == substr(md5("A ticket has been opened on your behalf. Thank you"), -4))
							{
							?>
								<script>
									alert("A ticket has been opened on your behalf. Thank you");
								</script>
							<?php
							}
						}
						?>
						<!---PASSING MSG-->
						
						
                    <div class="col-md-12">
						<div class="panel panel-default" style='box-shadow: 0px 0px 2px 0px'>
							<div class='panel-body'>
								<font color='red' size='3'><b>NEWS UPDATE:</b></font> <a href='news' class='btn btn-default'><b>READ MORE</b></a>
								<br/><br/>
								<marquee direction='up' height='50px' scrolldelay='800'>
								<?php
								$getall_news = $con->prepare("select * from `newsupdate` order by news_id desc");
								$getall_news->execute();
								$getall_news_INFO = $getall_news->fetch(PDO::FETCH_ASSOC);
								$newsmsg = $getall_news_INFO["newsmsg"];
								echo nl2br(htmlspecialchars_decode($newsmsg));
								?>
								</marquee>              
							</div>              
						</div>     

						<div class='alert alert-success' align='center'><b><font color='red' size='4px'>We deal ruthlessly with CYBER BEGGARS</font></b></div>
					</div>    
							
                </div>              
                 <!-- /. ROW  -->
                  <hr />
                <div class="row">
                <div class="col-md-4">           
			<div class="panel panel-default noti-box">
                <span class="icon-box bg-color-red set-icon">
                    <i class="fa fa-money" style='margin-top: 15px'></i>
                </span>
                <div class="text-box" >
                    <p class="main-text">Current Wallet</p>
                    <p class="text-muted"><b>&#8358;<?php if($available_PH_row == 0) { echo '0'; }else{ echo number_format($total_return_amnt); }?> Return Inward</b></p>
                </div>
             </div>
		     </div>
                    <div class="col-md-4">           
			<div class="panel panel-default noti-box">
                <span class="icon-box bg-color-green set-icon">
                    <i class="fa fa-bolt" style='margin-top: 15px'></i>
                </span>
				<a href="referral?refBonus">
					<div class="text-box" >
						<p class="main-text">Referral Bonus</p>
						<p class="text-muted"><b>Refer and earn</b></p>
					</div>
				</a>
             </div>
		     </div>
                
				 <div class="col-md-4">           
			<div class="panel panel-default noti-box">
                <span class="icon-box bg-color-brown set-icon">
                    <i class="fa fa-rocket" style='margin-top: 15px'></i>
                </span>
                <div class="text-box" >
                    <p class="main-text"><?php echo $ph_NOTPAID;?> Orders</p>
                    <p class="text-muted"><b>Pending Package</b></p>
                </div>
             </div>
		     </div>
                   
			</div>
                 <!-- /. ROW  -->
                <hr />             
				
                 <!-- /. ROW  -->
                
				<div class="row">
				
                    <div class="col-md-12">
						<div class="panel panel-default" style='box-shadow: 0px 0px 1px 0px'>
							<div class='panel-body'>
								Current Plan: <font color='red'><b><?php if(empty($package_name)) {echo '';} else{ echo strtoupper($package_name); }?> (&#8358;<?php echo number_format($amntPH);?>)</b></font>  » You can upgrade your package after receiving your outstanding payment
							</div>              
						</div>              
					</div>
					<!--
                    <div class="col-md-12">
							<?php
						$downline = $con->prepare("SELECT * FROM `downline` where referralID='$pid'");
						$downline->execute();
						for($i=1; $i<=rows("SELECT * FROM `downline` where referralID='$pid'"); $i++)
						{
							$downline_info = $downline->fetch(PDO::FETCH_ASSOC);
							$myreferID = $downline_info['participantID'];
							echo $myreferID;
							
							$checkoutqueue = $con->prepare("select * from merge_gh where status='' and (participantID='$myreferID' or gh_participantID='$myreferID')");
							$checkoutqueue->execute();
							
						}
						?>
					<select class='form-control input-lg' id='dynamic_select'>

							<option value="" selected>Select your downline</option>
							<option value="?refID=2">Google</option>
							<option value="?refID=25">YouTube</option>
							<option value="?refID=24">GuruStop.NET</option>
						</select>
						<br><br>
					</div>
					-->
					<div class='col-md-12'>
						<div class='col-md-7'>
						
							<?php
							//Checking pay out roll
							
							$checkallPH = $con->prepare("SELECT * FROM `merge_gh` where participantID='$pid' and status!='Confirmed'  and status!='Cancelled' order by mergeID desc");
							$checkallPH->execute();
							$checkallGH_row_topay = rows("SELECT * FROM `merge_gh` where participantID='$pid' and status!='Confirmed' and status!='Cancelled' order by mergeID desc");
							// echo $checkallGH_row_topay;
							
							if($checkallGH_row_topay != 0)
							{
							?>
							<div class="panel panel-default" style=''>
									<div class="panel-heading" style='background: #4caf50; color: #fff'>
										<b> Pay Out Queue</b>
									</div>
									<div class="panel-body">
										<?php
										for($i=1; $i<=$checkallGH_row_topay; $i++)
										{
											$checkallPH_INFO = $checkallPH->fetch(PDO::FETCH_ASSOC);
											$payto = $checkallPH_INFO['gh_participantID']; //Pay to
											$phID = $checkallPH_INFO['phID']; //PH ID
											$ghID = $checkallPH_INFO['ghID']; //PH ID
											$amountGH = $checkallPH_INFO['amountGH']; //Amount PH same as GH
											$dateMerge_expires = $checkallPH_INFO['dateMerge_expires']; //Date u have to pay else
											$mergeID = $checkallPH_INFO['mergeID'];
											$attachment = $checkallPH_INFO['attachment'];
											$status = $checkallPH_INFO['status'];
											//User gets additional 6 hours to make payment, if time clocks and it wasnt paid, user gets block
											//Trust betrayed
											$additional_time =  date('M d, Y H:i:00', strtotime("$dateMerge_expires+6 hours"));
											
										
											// Receiver Details
											$getrcv_participant = $con->prepare("select * from participant where pid='$payto'");
											$getrcv_participant->execute();
											$getrcv_participantINFO = $getrcv_participant->fetch(PDO::FETCH_ASSOC);
											$participant_name_to_rcv = $getrcv_participantINFO['name'];
											$to_rcv_number = $getrcv_participantINFO['mobile'];
											
											
											//Get the receiver bank account details
											$getbank = $con->prepare("select * from bankaccount where participant='$payto'");
											$getbank->execute();
											$getbankINFO = $getbank->fetch(PDO::FETCH_ASSOC);
											$bankName = $getbankINFO['bankName'];
											$merchantName = $getbankINFO['merchantName'];
											$merchantNo = $getbankINFO['merchantNo'];
											
									
											//Is there FAKE POP??????
											$searchticket = $con->prepare("SELECT * FROM `ticket` where report_participant='$pid' and phID='$phID'");
											$searchticket->execute();
											$searchticket_INFO = $searchticket->fetch(PDO::FETCH_ASSOC);
											$tid = $searchticket_INFO['tid'];
											
										?>
											
											
										<script>
										// Set the date we're counting down to
										var countDownDate<?php echo $mergeID;?> = new Date("<?php echo $dateMerge_expires;?>").getTime();

										// Update the count down every 1 second
										var x = setInterval(function() {

											// Get todays date and time
											var now = new Date().getTime();
											
											// Find the distance between now an the count down date
											var distance = countDownDate<?php echo $mergeID;?> - now;
											
											// Time calculations for days, hours, minutes and seconds
											var days = Math.floor(distance / (1000 * 60 * 60 * 24));
											var hours = Math.floor((distance % (1000 * 60 * 60 * 24)) / (1000 * 60 * 60));
											var minutes = Math.floor((distance % (1000 * 60 * 60)) / (1000 * 60));
											var seconds = Math.floor((distance % (1000 * 60)) / 1000);
											

											// If the count down is over, write some text 
											if (distance > 0) {
												document.getElementById("countDOWN<?php echo $mergeID;?>").innerHTML = "<center><span style='color: green; font-size: 18pt;'><b>Limited Time:<br><br><span style='color: green; font-size: 48pt;'> "+days + "d " + hours + "h "
											+ minutes + "m " + seconds + "s  </center>";
											}
											// If the count down is over, write some text 
											else if (distance < 0) {
												
												//Additional Time
												var countDOWN<?php echo $mergeID;?> = new Date("<?php echo $additional_time;?>").getTime();
												// Get todays date and time
												var now = new Date().getTime();

												// Find the distance between now an the count down date
												var distancee = countDOWN<?php echo $mergeID;?> - now;

												// Time calculations for days, hours, minutes and seconds
												var add_days = Math.floor(distancee / (1000 * 60 * 60 * 24));
												var add_hours = Math.floor((distancee % (1000 * 60 * 60 * 24)) / (1000 * 60 * 60));
												var add_minutes = Math.floor((distancee % (1000 * 60 * 60)) / (1000 * 60));
												var add_seconds = Math.floor((distancee % (1000 * 60)) / 1000);
												if(distancee > 0)
												{
												// Output the result in an element with id="demo"
												document.getElementById("countDOWN<?php echo $mergeID;?>").innerHTML = "<center><span style='color: red; font-size: 18pt;'><b>Additional Time:<br><br><span style='color: red; font-size: 48pt;'> "+add_days + "d " + add_hours + "h "+ add_minutes + "m " + add_seconds + "s  </b></center>";
												// alert();
												}
												else
												{
													document.getElementById("countDOWN<?php echo $mergeID;?>").innerHTML = "";
												}
											}
										}, 1000);
										</script>
										
											
											
											 
											<div class='col-md-5'>Pay to <b><?php echo strtoupper($participant_name_to_rcv);?></b>
											<?php 
										if(!empty($attachment)) 
										{?>
											<br><a href='../img/attachment/<?php echo $attachment;?>' target='_blank'><b><i class='glyphicon glyphicon-picture fa-2x'></i></b></a>
										<?php
										}
										?>
											</div>
											<div class='col-md-7'>
												<button data-toggle="modal" data-target="#phID<?php echo $mergeID;?>" class='btn btn-success'>ORDER DETAILS</button>
												<?php if(empty($attachment) && $status=='') {?> <a href='uploadPOP?ghID=<?php echo $ghID;?>&mergeID=<?php echo $mergeID;?>' class='btn btn-success'>UPLOAD POP</a><?php }?>
												<?php if(!empty($attachment) && $status=='Upload') {?> <button  class='btn btn-primary' style='background: black' disabled>PENDING</button><?php } 
												else if($status =='FAKEPOP' && !empty($attachment)){?>
											<a href='ticket?phID=<?php echo $phID;?>&tid=<?php echo $tid;?>' class='btn btn-default'><b>VIEW TICKET</b></a> <?php }?>
											</div>
											<br/><br/><hr style='border-bottom: 1px dotted; color: #000'>
											
											
											<div class="modal about-modal fade" id="phID<?php echo $mergeID;?>" tabindex="-1" role="dialog" style='margin-top: -20px'>
												<div class="modal-dialog" role="document">
													<div class="modal-content">
														<div class="modal-header" style='background: #5cc691; font-size: 18px; color: #fff'> 
															<button type="button" class="close btn btn-default pull-right" data-dismiss="modal">CLOSE</button>						
															<h4 class="modal-title"><b>Payment Transfer Information</b></h4>
														</div> 
														<div class="modal-body" style='margin-top: -40px'>
															<br><?php if(empty($attachment)){?><p id='countDOWN<?php echo $mergeID;?>'></p><?php }?>
															<center><span style="color: #008000; font-size: 14pt;"><strong>Please note down the PAYMENT INFORMATION Below.</strong></span></center>
															<div class="\&quot;alert" style="text-align: center;"><img src="../img/p2plogo.jpg" alt="" height="100" width="247"></div><br>
															<center><span style="color: #000; font-size: 12pt;"><b>Pay exactly &#8358;<?php echo number_format(($amountGH),2);?> to:
															<br><br><font size='3px'>
																BANK NAME: <?php echo strtoupper($bankName);?><br>
																HOLDER NAME: <?php echo strtoupper($merchantName);?>
																<br>ACCOUNT NUMBER: <?php echo $merchantNo;?><br><br></font>
															Before Making Payments Transfer, please contact receiver first: </b>
																<br><b>Username: <?php echo strtoupper($participant_name_to_rcv);?>
																<br/>Phone : +<?php echo $to_rcv_number;?> 
																<br>
																<div class="\&quot;alert" style="text-align: center;"><strong><span style="color: #008000; font-size: 12pt;">After Payment to your PAIRED Participant, simply click on <span style="color: #ff0000; font-size: 18px">Upload POP</span> After Closing this Window.</span></strong></div>
															</span></b></center>
															<!--
															<div class="modal-footer">
																<button type="button" data-dismiss="modal" class="btn btn-default">Close</button>

															</div>
															-->
														</div>
														
													</div>
												</div>
											</div>
										<?php
										}
										?>
									</div>
								</div>
							<?php
							}
							
							
							//WHO IS TO PAY ME  ????
							$checkallGH = $con->prepare("SELECT * FROM `merge_gh` where gh_participantID='$pid' and status!='Confirmed' and attachment!='Cancelled' and status!='Cancelled'");
							$checkallGH->execute();
							$checkallGH_row = rows("SELECT * FROM `merge_gh` where gh_participantID='$pid' and status!='Confirmed' and attachment!='Cancelled' and status!='Cancelled'");
							$checkallGH_queuerow = rows("SELECT * FROM `gethelp` where participantID='$pid' and merge!='YES' and merge!='Cancelled' and merge!='pending'");
							$checkallGH_queuerow_phtoGH = rows("SELECT * FROM `gethelp` where participantID='$pid' and merge='pending'");
							// echo $checkallGH_queuerow;
							if($checkallGH_row >=1)
							{
							?>
								
									<div class="panel panel-default" style=''>
										<div class="panel-heading" style='background: #4caf50; color: #fff'>
											<b>Get Request Queue</b>
										</div>
										<div class="panel-body">
								<?php
								for($i=1; $i<=$checkallGH_row; $i++)
								{
									$checkallGH_INFO = $checkallGH->fetch(PDO::FETCH_ASSOC);
									$to_pay = $checkallGH_INFO['participantID'];
									$ghID = $checkallGH_INFO['ghID'];
									$phID = $checkallGH_INFO['phID'];
									$amountGH = $checkallGH_INFO['amountGH'];
									$dateMerge_expires = $checkallGH_INFO['dateMerge_expires'];
									$mergeID = $checkallGH_INFO['mergeID'];
									$attachment = $checkallGH_INFO['attachment'];
									$merge_status_pay = $checkallGH_INFO['status'];
									// echo $phID;
									
									//Is there FAKE POP??????
									$searchticket = $con->prepare("SELECT * FROM `ticket` where participant='$pid' and phID='$phID'");
									$searchticket->execute();
									$searchticket_INFO = $searchticket->fetch(PDO::FETCH_ASSOC);
									$tid = $searchticket_INFO['tid'];
									
									//Sender Details
									$getparticipant = $con->prepare("select * from participant where pid='$to_pay'");
									$getparticipant->execute();
									$getparticipantINFO = $getparticipant->fetch(PDO::FETCH_ASSOC);
									$participant_name_to_pay = $getparticipantINFO['name'];
									$to_pay_number = $getparticipantINFO['mobile'];

									// Receiver Details
									$getrcv_participant = $con->prepare("select * from participant where pid='$pid'");
									$getrcv_participant->execute();
									$getrcv_participantINFO = $getrcv_participant->fetch(PDO::FETCH_ASSOC);
									$participant_name_to_rcv = $getrcv_participantINFO['name'];
									$to_rcv_number = $getrcv_participantINFO['mobile'];
									
									//Get the receiver bank account details
									$getbank = $con->prepare("select * from bankaccount where participant='$pid'");
									$getbank->execute();
									$getbankINFO = $getbank->fetch(PDO::FETCH_ASSOC);
									$rcv_bankName = $getbankINFO['bankName'];
									$rcv_merchantName = $getbankINFO['merchantName'];
									$rcv_merchantNo = $getbankINFO['merchantNo'];
									//User gets additional 6 hours to receive payment, if time clocks and it wasnt paid, user gets a new payee
									//So cool
									$additional_time =  date('M d, Y H:i:00', strtotime("$dateMerge_expires+6 hours"));
								?>
										<script>
										// Set the date we're counting down to
										var countDownDate<?php echo $mergeID;?> = new Date("<?php echo $dateMerge_expires;?>").getTime();

										// Update the count down every 1 second
										var x = setInterval(function() {

											// Get todays date and time
											var now = new Date().getTime();
											
											// Find the distance between now an the count down date
											var distance = countDownDate<?php echo $mergeID;?> - now;
											
											// Time calculations for days, hours, minutes and seconds
											var days = Math.floor(distance / (1000 * 60 * 60 * 24));
											var hours = Math.floor((distance % (1000 * 60 * 60 * 24)) / (1000 * 60 * 60));
											var minutes = Math.floor((distance % (1000 * 60 * 60)) / (1000 * 60));
											var seconds = Math.floor((distance % (1000 * 60)) / 1000);
											

											// If the count down is over, write some text 
											if (distance > 0) {
												document.getElementById("countDOWN<?php echo $mergeID;?>").innerHTML = "<center><span style='color: green; font-size: 18pt;'><b>Limited Time:<br><br><span style='color: green; font-size: 48pt;'> "+days + "d " + hours + "h "
											+ minutes + "m " + seconds + "s  </center>";
											}
											// If the count down is over, write some text 
											else if (distance < 0) {
												
												//Additional Time
												var countDOWN<?php echo $mergeID;?> = new Date("<?php echo $additional_time;?>").getTime();
												// Get todays date and time
												var now = new Date().getTime();

												// Find the distance between now an the count down date
												var distancee = countDOWN<?php echo $mergeID;?> - now;

												// Time calculations for days, hours, minutes and seconds
												var add_days = Math.floor(distancee / (1000 * 60 * 60 * 24));
												var add_hours = Math.floor((distancee % (1000 * 60 * 60 * 24)) / (1000 * 60 * 60));
												var add_minutes = Math.floor((distancee % (1000 * 60 * 60)) / (1000 * 60));
												var add_seconds = Math.floor((distancee % (1000 * 60)) / 1000);
												if(distancee > 0)
												{
												// Output the result in an element with id="demo"
												document.getElementById("countDOWN<?php echo $mergeID;?>").innerHTML = "<center><span style='color: red; font-size: 18pt;'><b>Additional Time:<br><br><span style='color: red; font-size: 48pt;'> "+add_days + "d " + add_hours + "h "+ add_minutes + "m " + add_seconds + "s  </b></center>";
												// alert();
												}
												else
												{
													document.getElementById("countDOWN<?php echo $mergeID;?>").innerHTML = "";
												}
											}
										}, 1000);
										</script>
										
												
									
									
									
									<div class='col-md-5'>Receive from <a href='#' data-toggle="modal" data-target="#ghID<?php echo $mergeID;?>"><b><?php echo strtoupper($participant_name_to_pay);?></b></a>
										<?php 
										if(!empty($attachment)) 
										{?>
											<br><a href='../img/attachment/<?php echo $attachment;?>' target='_blank'><b><i class='glyphicon glyphicon-picture fa-2x'></i></b></a>
										<?php
										}
										?>
									</div>
									<div class='col-md-7'>
										
										<?php 
										if(!empty($attachment) && $merge_status_pay =='Upload') 
										{?>
											<a href='../action?FAKEPOP&ID=<?php echo $ghID;?>&mergeID=<?php echo $mergeID;?>' class='btn btn-success' onclick="return confirm('FLAG AS FAKE PAYMENT? \n\n PAYMENT NOT RECEIVED?');"><b>FAKE POP</b></a>
											<a href='../action?ConfirmGH&ghID=<?php echo $ghID;?>&phID=<?php echo $phID;?>' class='btn btn-success' onclick="return confirm('Confirm &#8358;<?php echo $amountGH;?> Donation? \n \nDo you want to Proceed?');"><b>CONFIRM ORDER</b></a>
										
										<?php }
										
										else if($merge_status_pay == "FAKEPOP")
										{
										?>
											<a href='ticket?readTICKET=<?php echo $tid;?>' class='btn btn-default'><b>VIEW TICKET</b></a>
											<a href='../action?ConfirmGH&ghID=<?php echo $ghID;?>&phID=<?php echo $phID;?>' class='btn btn-success' onclick="return confirm('Confirm &#8358;<?php echo $amountGH;?> Donation? \n \nDo you want to Proceed?');"><b>CONFIRM ORDER</b></a>
										
										<?php	
										}?>
										
									</div>
									<br><br><br><br>
									
											<hr style='border-bottom: 1px dotted; color: #000'>
									
									
									
									
									<div class="modal about-modal fade" id="ghID<?php echo $mergeID;?>" tabindex="-1" role="dialog">
										<div class="modal-dialog" role="document">
											<div class="modal-content">
												<div class="modal-header" style='background: #5cc691; font-size: 18px; color: #fff'> 
													<button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>						
													<h4 class="modal-title">Payment Transfer Information</h4>
												</div>
												<div class="modal-body" style='margin-top: -40px'>
													<br><?php if(empty($attachment)){?> <p id='countDOWN<?php echo $mergeID;?>'></p> <?php }?>
													
													<center><span style="color: #008000; font-size: 14pt;"><strong>Please note down the PAYMENT INFORMATION Below.</strong></span></center>
														<div class="\&quot;alert" style="text-align: center;"><img src="../img/p2plogo.jpg" alt="" height="100" width="247"></div><br>
														<center><span style="color: #000; font-size: 12pt;"><b>A sum of &#8358;<?php echo number_format(($amountGH),2);?> will be paid to:
														<br><br><font size='3px'>
																BANK NAME: <?php echo strtoupper($rcv_bankName);?><br>
																HOLDER NAME: <?php echo strtoupper($rcv_merchantName);?><br>
																ACCOUNT NUMBER:<?php echo $rcv_merchantNo;?><br><br></font>
														Endeavor to inform your match about the order: </b>
														<br><b>Username: <?php echo strtoupper($participant_name_to_pay);?>
														<br/>Phone : +<?php echo $to_pay_number;?> 
														<br>
														<div class="\&quot;alert" style="text-align: center;"><strong><span style="color: #008000; font-size: 12pt;">Didn't receive payment? Failure to flag payment as <span style="color: #ff0000; font-size: 18px">FAKE POP</span> after 72hours will lead to Automatic confirmation.</span></strong></div>
													</span></b></center>
													<div class="modal-footer">
														<button type="button" data-dismiss="modal" class="btn btn-default">Close</button>

													</div>
												</div>
												
											</div>
										</div>
									</div>
								<?php
								}
								?>
								
										</div>
									</div>
								<?php
							}
							else if($checkallGH_queuerow != 0)
							{
							?>
								<div class="panel panel-default" style=''>
									<div class="panel-heading" style='background: #4caf50; color: #fff'>
										<b>Receive Help Queue</b>
									</div>
									<div class="panel-body">
										<div class='alert alert-info'><b>You are on Queue. Promote <b>GiversCycler</b> in any way you can. It is our community.<br> A single complaint can discourage new Investors from coming in and one of those might just be the one to pay you.  <a href='referral?reflink' style='color: red'>&laquo; Refer & Earn &raquo; </a></b></div>
									</div>
								</div>
							<?php	
							}
							else if($checkallGH_queuerow_phtoGH != 0)
							{
							?>
								<div class="panel panel-default" style=''>
									<div class="panel-heading" style='background: #4caf50; color: #fff'>
										<b>Receive Help Queue</b>
									</div>
									<div class="panel-body">
										<div class='alert alert-info'><b>Dear Participant, You need to Re-PH and Get 300% of your New PH Now. Sustainability of  <b>GiversCycler</b> lies in your hand <a href='packages' style='color: red'>&laquo; Activate Plan &raquo; </a> <a href='news' style='color: red'>&laquo; Check News Update &raquo; </a> <a href='forum' style='color: red'>&laquo; JOIN FORUM &raquo; </a></b></div>
									</div>
								</div>
							<?php	
							}
							else if($checkallGH_row == 0)
							{
							?>
								<div class="panel panel-default" style=''>
									<div class="panel-heading" style='background: #4caf50; color: #fff'>
										<b>Receive Help Queue</b>
									</div>
									<div class="panel-body">
										<div class='alert alert-info'><b>You are not on queue to receive, activate a package now to earn or wait patiently for your order to be merged. <a href='packages' style='color: red'>&laquo; Activate Plan &raquo; </a></b></div>
									</div>
								</div>
							<?php							
							}
							?>
						</div>
						<div class="col-md-5">
							
							<!----Javascript for showing all order at 1secs--->
								<div id='ph_order'></div>
								<div id='mergegh_order'></div>
							<!----Javascript for showing all order at 1secs--->
						<?php
						
						// include 'automate_GH.php';
						// include 'merge_GH.php';
						
						?>
							
							<div class="panel panel-primary" >
								<div class="panel-heading">
									Bank Account Details
								</div>
								<div class="panel-body">
								   <b>Bank Name: </b><?php echo $bank_Name;?> 
								   <br/><br/>
								   <b>Account Name: </b><?php echo $accntName;?> 
								   <br/><br/>
								   <b>Account Number: </b><?php echo $accntNo;?> 
								   <br/>
									
								</div>
							</div>
						</div>
					
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
