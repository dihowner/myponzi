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
		redirect_to("ticket?msg=$blocked");
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
	
	//Does user have a pending order i.e has user paid and has user been paid back?
	// $getPH_pend = rows("select * from providehelp where participantID='$pid' and status='Unconfirmed' or status='Confirmed'");
	// echo $getPH_pend;
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
    <title>Givers Packages ::: Givers Cycler</title>
	<!-- BOOTSTRAP STYLES-->
    <link href="../assets/css/bootstrap.css" rel="stylesheet" />
     <!-- FONTAWESOME STYLES-->
    <link href="../assets/css/font-awesome.css" rel="stylesheet" />
     <!-- MORRIS CHART STYLES-->
    <link href="../assets/js/morris/morris-0.4.3.min.css" rel="stylesheet" />
        <!-- CUSTOM STYLES-->
    <link href="../assets/css/custom.css" rel="stylesheet" />
    <link rel="stylesheet" type="text/css" href="../css/priceplan.css">
  <link href="../img/favicon.jpg" rel="shortcut icon" type="" />
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
                        <a href="#" class="active-menu"><i class="fa fa-exchange fa-3x"></i> Transaction History<span class="fa arrow"></span></a>
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
					<div class='col-md-12'>
					
						<div class="header-section text-center">
							<h2>TRANSACTION HISORY</h2>
							<h5>Hey <b><?php echo ucfirst($participant_name);?></b>, find below transaction which occured on your account </h5>
							
										<br><br><br>
						</div>
						
							<?php 
							if(isset($_GET['ph-history']))
							{
                                $select_ph = $con->prepare("SELECT * FROM `providehelp` where participantID='$pid' order by status ");
                                $select_ph->execute();
                                $select_ph_row = rows("SELECT * FROM `providehelp` where participantID='$pid' order by status");
//                                echo $select_ph_row;
                                if($select_ph_row == 0)
                                {
                                    ?>
                                    <div class='alert alert-warning' align='center'><font size='6px'><b>You do not have any transaction history yet <a href='packages' style='color: red'>&laquo; Activate Plan &raquo; </a></b></font></div>
                                    <?php
                                }
                                else
                                {
                                    for($i=1; $i<=$select_ph_row; $i++)
                                    {

                                        $select_ph_INFO = $select_ph->fetch(PDO::FETCH_ASSOC);
                                        $merge = $select_ph_INFO['merge'];
                                        $phID = $select_ph_INFO['phID'];
                                        $status = $select_ph_INFO['status'];
                                        $amntPH = $select_ph_INFO['amntPH'];
                                        $createDATE = $select_ph_INFO['createDATE'];

                                        $select_ph_merge = $con->prepare("SELECT * FROM `merge_gh` where phID='$phID' and participantID='$pid'");
                                        $select_ph_merge->execute();

                                        $select_ph_merge_info = $select_ph_merge->fetch(PDO::FETCH_ASSOC);
                                        $gh_participantID = $select_ph_merge_info['gh_participantID'];
                                        $dateMerge = $select_ph_merge_info['dateMerge'];
										if(empty($dateMerge))
										{
											$dateMerge = $createDATE;
										}
										
                                        $who_rcv = $con->prepare("SELECT * FROM `participant` where pid='$gh_participantID'");
                                        $who_rcv->execute();
                                        $who_rcvinfo = $who_rcv->fetch(PDO::FETCH_ASSOC);
                                        $who_rcv_participant_name = $who_rcvinfo['name'];

                                        ?>
                                        <div class='col-md-4'>

                                            <div class="panel panel-default" style='border-radius: 15px; color: #000; background: linear-gradient(to bottom,rgba(197,253,98,1) 0%,rgba(201,253,119,1) 47%,rgba(168,234,63,1) 63%,rgba(164,228,50,1) 100%);border: 1px solid #949494;'>
                                                <div class="panel-heading" style='border-radius: 10px;'>
                                                    <b>Provide Help (<?php echo $phID;?>)</b>
                                                </div>
                                                <div class="panel-body" style='font-size: 15px'>
                                                    <b>Participant Name: </b><?php echo strtoupper($participant_name);?>
                                                    <br/>
                                                    <b>Amount: </b>&#8358;<?php echo number_format(($amntPH),2);?>
													<br>
													<b>Date Order: </b> <?php echo $dateMerge;?>
                                                    <br/>
                                                    <?php
                                                    if($merge == 'NO')
                                                    {
                                                    ?>
                                                        <b>Status:</b> <font color='red' size='3'><b>Running</b></font>
                                                    <?php
                                                    }
                                                    ?>
                                                    <?php
                                                    if($status == 'Cancelled')
                                                    {
                                                        ?>
                                                        <b>Status:</b> <font color='red' size='3'><b>Cancelled</b></font>
                                                        <?php
                                                    }
                                                    else if($status == 'Confirmed' || $status == 'Withdraw')
                                                    {
                                                        ?>
                                                        <b>Status:</b> <font color='green' size='3'><b>Paid</b></font>
                                                        <?php
                                                    }
                                                    else if($status == 'Unconfirmed' && $merge == 'complete')
                                                    {
                                                        ?>
                                                        <b>Status:</b> <font color='red' size='3'><b>Running</b></font>
                                                        <?php
                                                    }

                                                    ?>
                                                </div>
                                            </div>

                                        </div>
                                        <?php
                                    }
                                }
                            }

							//Get History
							else if(isset($_GET['gh-history']))
							{
								$select_gh = $con->prepare("SELECT * FROM `merge_gh` where gh_participantID='$pid' order by status");
								$select_gh->execute();
								$select_gh_row = rows("SELECT * FROM `merge_gh` where gh_participantID='$pid' order by status");
								// echo $select_gh_row;
								if($select_gh_row == 0)
								{
								?>
									<div class='alert alert-warning' align='center'><font size='6px'><b>You do not have any transaction history yet <a href='packages' style='color: red'>&laquo; Activate Plan &raquo; </a></b></font></div>
								<?php
								}
								else
								{
									
									for($i=1; $i<=$select_gh_row; $i++)
									{
										$select_gh_info = $select_gh->fetch(PDO::FETCH_ASSOC);
										$phID = $select_gh_info['phID'];
										$ghID = $select_gh_info['ghID'];
										$status = $select_gh_info['status'];
										$amountGH = $select_gh_info['amountGH'];
										$participantID = $select_gh_info['participantID'];
										$dateMerge = $select_gh_info['dateMerge'];
										// $status = $select_ph_info['status'];
										
										//Participant who receive the fee
										// $select_ph_merge = $con->prepare("SELECT * FROM `providehelp` where phID='$phID' and participantID='$participantID'");
										// $select_ph_merge->execute();
										// $select_ph_merge_info = $select_ph_merge->fetch(PDO::FETCH_ASSOC);
										// $participantID = $select_ph_merge_info['participantID'];
										
										$who_paid = $con->prepare("SELECT * FROM `participant` where pid='$participantID'");
										$who_paid->execute();
										$who_paidinfo = $who_paid->fetch(PDO::FETCH_ASSOC);
										$who_paid_participant_name = $who_paidinfo['name'];
										
										
									?>
							<div class='col-md-4'>
								
								<div class="panel panel-default" style='color: #000;background: linear-gradient(to bottom,rgba(252,234,187,1) 0%,rgba(252,205,77,1) 46%,rgba(248,181,0,1) 60%,rgba(251,223,147,1) 100%); border: 1px solid #949494;'>									<div class="panel-heading" style='border-radius: 10px;'>
										<b>Get Help (<?php echo $ghID;?>)</b>
									</div>
									<div class="panel-body" style='font-size: 15px'>
									   <b>Participant Name: </b><?php echo strtoupper($participant_name);?> 
									   <br/>
									   <b>Amount: </b>&#8358;<?php echo number_format(($amountGH),2);?> 
										<br/>
										<?php
										if($status == 'Confirmed')
										{
										?>
										
											<b>Received From: </b> <?php echo ucfirst($who_paid_participant_name);?> 
											<br>
											<b>Date Order: </b> <?php echo $dateMerge;?>
											<br>
											<b>Status:</b> <font color='green' size='3'><b>Paid</b></font>

										<?php
										}
										else if($status == 'Cancelled')
										{
										?>
											<b>Receive From: </b> <?php echo ucfirst($who_paid_participant_name);?> 
											<br>
											<b>Date Order: </b> <?php echo $dateMerge;?>
											<br>
											<b>Status:</b> <font color='red' size='3'><b>Cancelled</b></font>
										<?php
										}
										else if($status == '')
										{
										?>
											<b>Receive From: </b> <?php echo ucfirst($who_paid_participant_name);?> 
											<br>
											<b>Status:</b> <font color='red' size='3'><b>Running</b></font>
										<?php
										}
										?>
									</div>
								</div>
								
							</div>
									<?php
									}
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
