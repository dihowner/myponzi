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
	// echo $user_status;
	if($user_status != 'active')
	{
		redirect_to("ticket?msg=$blocked");
	}
	
	//Bank account
	$get_account = $con->prepare("SELECT * FROM `bankaccount` where participant='$pid'");
	$get_account->execute();
	if($get_account->rowCount() > 0) {
		$getBANKINFO = $get_account->fetch(PDO::FETCH_ASSOC);
		$bankName = $getBANKINFO["bankName"];
		$merchantName = strtoupper($getBANKINFO["merchantName"]);
		$merchantNo = $getBANKINFO["merchantNo"];
	}
	$check_allpendingGH = rows("Select * from gethelp where participantID='$pid' and merge='pending'");

	//First timer login
	$count_login  = rows("select * from firstlogin where username='$username'");
	if($count_login == 0)
	{
		redirect_to("termsupdate");
		
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
    <title>ACCOUNT DETAIL  ::: Givers Cycler</title>
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
                        <a href="#" class="active-menu"><i class="fa fa-user fa-3x"></i> Profile<span class="fa arrow"></span></a>
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
					
                </div>              
                 <!-- /. ROW  -->
                  <hr />           
				
                 <!-- /. ROW  -->
                
				<div class="row">
				
                    <div class="col-md-12">
						<?php
						if($get_account->rowCount() == 0)
						{
						?>
							<b>Before you can participate in the community, you need to provide your banking details. </b>
							<br><br><br>
							<button class="btn btn-success" data-toggle="modal" data-target="#Add_Account" style="margin-top: -10px"><b><i class="fa fa-plus"></i> Add Account Details</b></button>
							<br><br><br>					
							<!--Add Bank Account Details-->
							<div class="modal about-modal fade" id="Add_Account" tabindex="-1" role="dialog" style="margin-top: 30px">
								<div class="modal-dialog" role="document">
									<div class="modal-content">
										<div class="modal-header"> 
											<button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>						
											<h4 class="modal-title">Account Information</h4>
										</div> 
										<div class="modal-body">
										<font color="red" size="4px"><b>NOTE:</font> Ensure to provide accurate banking information details. Editing of account details is not allowed!<br/><br/><br/></b>
											<div id="result"></div>
											<form id="getAllBankINFO">
											  
												<div class="form-group">
													<label for="recipient-name" class="form-control-label">Bank Name <font color="red">*</font>:</label>
													<input type="text" class="form-control input-lg" name="bank_name" id="bank_name" required autocomplete="off">
												</div>
												
												<div class="form-group">
													<label for="recipient-name" class="form-control-label">Account Name <font color="red">*</font>:</label>
													<input type="text" class="form-control input-lg" name="accnt_name" id="accnt_name" required autocomplete="off">
												</div>
											  
												<div class="form-group">
													<label for="recipient-name" class="form-control-label">Account Number <font color="red">*</font>:</label>
													<input type="text" class="form-control input-lg" name="accnt_number"  id="accnt_number" required autocomplete="off">
												</div>
									  
											</form>
										</div>
												
										<div class="modal-footer">
											<button type="button" class="btn btn-success" data-dismiss="modal"><i class="fa fa-remove"></i> Close</button>
											<button type="button" class="btn btn-success" id="saveAccnt"><i class="fa fa-check"></i> Save Account</button>
										</div>
									</div>
								</div>
							</div>	
							
							<!--Add Details-->
						<?php
						}
						?>   

					</div>
					<br/><br/><br/>
					<div class="col-md-12">
						<?php if($get_account->rowCount() == 1) { ?>
							<div class='alert alert-info' style='color: #000; font-size: 18px'><b>Error in banking details. <a href='ticket'>Create A Ticket</a></b></div> 
						
							<div class="panel panel-default" >
								<div class="panel-heading">
									Bank Account Details
								</div>
								<div class="panel-body">
								   <b>Bank Name: </b><?php echo $bankName;?> 
								   <br/><br/>
								   <b>Account Name: </b><?php echo $merchantName;?> 
								   <br/><br/>
								   <b>Bank Name: </b><?php echo $merchantNo;?> 
								   <br/>
									
								</div>
							</div>
						<?php } ?>
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
