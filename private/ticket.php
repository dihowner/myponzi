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
    <title>TICKET ::: Givers Cycler</title>
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
                        <a  href="gh-testimonial?testimonies"><i class="fa fa-smile-o fa-3x"></i> Letter of Joy</a>
                    </li>
					
                     <li>
                        <a  href="ticket" class="active-menu" ><i class="fa fa-book fa-3x"></i> Ticket</a>
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
				
				
                 <!-- /. ROW  -->
                
				<div class="row">
					<?php
						if(isset($_GET['msg']))
						{
							$msg = $_GET["msg"];
							if($msg == strtoupper(substr(md5("blocked"), -4)))
							{
							?>
								<div class='col-md-12'>
									<font color="red" size="30px"><center>PERSONAL OFFICE BLOCKED</font>
									<br><b>Your account has been blocked, create a ticket now to support</b></center>
								</div>
							<?php
							}	
							else if($msg == strtoupper(substr(md5("TICKET SAVED"), -4)))
							{
							?>
								<div class='col-md-12'>
									<font color="red" size="30px"><center>TICKET SAVED</font>
								</div>
							<?php
							}
							else if($msg == strtoupper(substr(md5("Reply was added successfully"), -4)))
							{
							?>
								<div class='col-md-12'>
									<font color="red" size="30px"><center>Reply was added successfully</font>
								</div>
							<?php
							}
							else if($msg == strtoupper(substr(md5("File is too large to be saved"), -4)))
							{
							?>
								<div class='col-md-12'>
									<font color="red" size="30px"><center>File is too large to be saved</font>
								</div>
							<?php
							}
						?>
						
						<?php
						}
					?>
					<div class='col-md-12'>
						<div class='col-md-12'>
							<br>
							<?php
							if(!isset($_GET["readTICKET"]) && !isset($_GET["phID"]) && !isset($_GET["tid"]))
							{
							?>
								<center><button class="btn btn-default btn-lg" id="OpenTicket"><b>CREATE TICKET</b></button></center>
							<?php
							}
							?>
							
							<br>
							<?php
							if(!isset($_GET["readTICKET"]))
							{
									 
								$getallTicket = $con->prepare("SELECT * FROM `ticket` where participant='$pid' order by tid desc");
								$getallTicket->execute();
								for($i=1; $i<=rows("SELECT * FROM `ticket` where participant='$pid' order by tid desc"); $i++)
								{
									$getallTicket_INFO = $getallTicket->fetch(PDO::FETCH_ASSOC);
									$subject = $getallTicket_INFO['subject'];
									$replied = $getallTicket_INFO['replied'];
									$tid = $getallTicket_INFO['tid'];
								?>
									<a href="?readTICKET=<?php echo $tid;?>"><font size="4"><b><?php echo $subject;?> (<font color="red"><?php echo $replied;?></font>)</b></font></a>
									<br><br>
								<?php
								}
							}
							if(!isset($_GET["phID"])) //No ph ID in MSG
							{
								//How will Participant that has a report see his or her message
								$getTicket = $con->prepare("SELECT * FROM `ticket` where report_participant='$pid' order by tid desc");
								$getTicket->execute();
								for($i=1; $i<=rows("SELECT * FROM `ticket` where report_participant='$pid' order by tid desc"); $i++)
								{
									$getTicket_INFO = $getTicket->fetch(PDO::FETCH_ASSOC);
									$subject = $getTicket_INFO['subject'];
									$replied = $getTicket_INFO['replied'];
									$ticketID = $getTicket_INFO['tid'];
									$phID = $getTicket_INFO['phID'];
								?>
									<a href="?phID=<?php echo $phID;?>&tid=<?php echo $ticketID;?>"><font size="4"><b><?php echo $subject;?> (<font color="red"><?php echo $replied;?></font>)</b></font></a>
									<br><br>
								<?php
								}
							}
							if(isset($_GET["readTICKET"]))
							{
								$tid = $_GET["readTICKET"];
								$getallTicket = $con->prepare("SELECT * FROM `ticket` where participant='$pid' and tid='$tid' order by tid desc");
								$getallTicket->execute();
								$getallTicket_INFO = $getallTicket->fetch(PDO::FETCH_ASSOC);
								$subject = $getallTicket_INFO['subject'];	
								$relatedISSUE = $getallTicket_INFO['relatedIssue'];
								$ticketmsg = nl2br($getallTicket_INFO['ticketmsg']);
								$locked = nl2br($getallTicket_INFO['locked']);
								$status = nl2br($getallTicket_INFO['replied']);
							?>
								<b>ISSUE TYPE:</b> <?php echo $relatedISSUE;?><br/>
								<b>SUBJECT:</b> <?php echo $subject;?><br/>
								<?php if($status == 0){?> <b>STATUS:</b> <font color="red"><b>NEW</b></font><?php }?><br/><br/>
							<?php echo $ticketmsg;?>
							<br><br>
							<?php
							//We need to fetch all replies made by receiver and payee
								$getREPLIES = $con->prepare("SELECT * FROM `ticket_replies` where ticketid='$tid'");
								$getREPLIES->execute();
								for($i=1; $i<=rows("SELECT * FROM `ticket_replies` where ticketid='$tid'"); $i++)
								{
									$getREPLIES_INFO = $getREPLIES->fetch(PDO::FETCH_ASSOC);
									$replymsg = $getREPLIES_INFO["replymsg"]; // Msg replied
									$participantID = $getREPLIES_INFO["participantID"];
									$attachment_reply = $getREPLIES_INFO["attachment"];
									$date_written = $getREPLIES_INFO["date_written"];
									if(!is_numeric($participantID))
									{
										$participant_name_replied = $participantID . ' || STAFF';
									}
									else
									{
										//We need to know who is replying 
										$getparticipant  = $con->prepare("SELECT * FROM `participant` where pid='$participantID'");
										$getparticipant->execute();
										$getparticipantinfo = $getparticipant->fetch(PDO::FETCH_ASSOC);
										$participant_name_replied = $getparticipantinfo['name'];
									}
									
								?>
								<div class='col-md-12'>
									<div class='col-md-12' style="color: #000; background: #fee996; border-radius: 10px; margin: 10px 0; padding: 10px;border: 1px solid #d19405;">
										<b><?php echo ucfirst($participant_name_replied);?>
										<br><br>
										<?php echo $replymsg;
										
										if(!empty($attachment_reply))
										{
											echo '<br>';
										?>
										View: <a href="../img/attachment/<?php echo $attachment_reply;?>" target="_blank"><img src="../img/attachment/<?php echo $attachment_reply;?>" width="40" height="40"/></a>
										<br><br>
										<?Php
										}
										?>
										<br><br>	
										 <?php echo $date_written;?>
										</b>
									</div>
								</div>
								<?php
								// echo "<br>".$participantID . $replymsg."<br>";
									
								}
							if($locked == "NO")
							{
							?>
								<form method="post" action="../action" enctype="multipart/form-data">
									<br><br><font color="red"><b>NOTE:</font> Reply is to be made only if required, wait patiently till you are attended to</b>
									<br><textarea name="reply_ticket_msg" rows="5" class="form-control input-lg" required></textarea>
									<br><b>Add Attachment:</b><br>
									<font color="red" size='4px'>Maximum file upload is 2mb</font>
									<br>
									<input type="file" name="file_reply" class="form-control input-sm">
									<input name="ticketID" value="<?php echo $tid;?>" type="hidden">
									<br>
									<button class="btn btn-default" name="reply_ticket"><i class="fa fa-mail-reply"></i> Submit Reply</button>
								</form>
							<?php
							}
							else if($locked == "YES")
							{
							?>
							
								<br><br><br><br>
								<div class='col-md-12'>
									<div class='col-md-12 alert alert-warning' style="color: #000">
										<b>Dear participant! Your request has been closed since your request has already been solved. If you still have any questions – please create a new ticket.</b>
									</div>
								</div>
							<?php
							}
							}
							//For payee to view
							if(isset($_GET["phID"]) && isset($_GET["tid"]))
							{
								$tid = $_GET["tid"];
								$phID = $_GET["phID"];
								$getallTicket = $con->prepare("SELECT * FROM `ticket` where report_participant='$pid' and tid='$tid' and phID='$phID' order by tid desc");
								$getallTicket->execute();
								$getallTicket_INFO = $getallTicket->fetch(PDO::FETCH_ASSOC);
								$subject = $getallTicket_INFO['subject'];	
								$relatedISSUE = $getallTicket_INFO['relatedIssue'];
								$ticketmsg = nl2br($getallTicket_INFO['ticketmsg']);
								$locked = nl2br($getallTicket_INFO['locked']);
								$status = nl2br($getallTicket_INFO['replied']);
							?>
								<b>ISSUE TYPE:</b> <?php echo $relatedISSUE;?><br/>
								<b>SUBJECT:</b> <?php echo $subject;?><br/>
								<?php if($status == 0){?> <b>STATUS:</b> <font color="red"><b>NEW</b></font><?php }?><br/><br/>
							<?php echo $ticketmsg;?>
							<br><br>
							<?php
							//We need to fetch all replies made by receiver and payee
								$getREPLIES = $con->prepare("SELECT * FROM `ticket_replies` where ticketid='$tid'");
								$getREPLIES->execute();
								for($i<=1; $i<=rows("SELECT * FROM `ticket_replies` where ticketid='$tid'"); $i++)
								{
									$getREPLIES_INFO = $getREPLIES->fetch(PDO::FETCH_ASSOC);
									$replymsg = $getREPLIES_INFO["replymsg"]; // Msg replied
									$participantID = $getREPLIES_INFO["participantID"];
									$attachment_reply = $getREPLIES_INFO["attachment"];
									$date_written = $getREPLIES_INFO["date_written"];
									
									if(!is_numeric($participantID))
									{
										$participant_name_replied = $participantID . ' || STAFF';
									}
									else
									{
										//We need to know who is replying 
										$getparticipant  = $con->prepare("SELECT * FROM `participant` where pid='$participantID'");
										$getparticipant->execute();
										$getparticipantinfo = $getparticipant->fetch(PDO::FETCH_ASSOC);
										$participant_name_replied = $getparticipantinfo['name'];
									}
									
								?>
								<div class='col-md-12'>
									<div class='col-md-12' style="color: #000; background: #fee996; border-radius: 10px; margin: 10px 0; padding: 10px;border: 1px solid #d19405;">
										<b><?php echo ucfirst($participant_name_replied);?>
										<br><br>
										<?php echo $replymsg;
										
										if(!empty($attachment_reply))
										{
											echo '<br>';
										?>
										View: <a href="../img/attachment/<?php echo $attachment_reply;?>" target="_blank"><img src="../img/attachment/<?php echo $attachment_reply;?>" width="40" height="40"/></a>
										<br><br>
										<?Php
										}
										?>
										<br><br>	
										 <?php echo $date_written;?>
										</b>
									</div>
								</div>
								<?php
								// echo "<br>".$participantID . $replymsg."<br>";
									
								}
								
								
								if($locked == "NO")
								{
								?>
									<form method="post" action="../action" enctype="multipart/form-data">
										<br><br><font color="red"><b>NOTE:</font> Reply is to be made only if required, wait patiently till you are attended to</b>
										<br><textarea name="reply_ticket_msg" rows="5" class="form-control input-lg"></textarea>
										<br><b>Add Attachment:</b><br>
										<font color="red" size='4px'>Maximum file upload is 2mb</font>
										<br>
										<input type="file" name="file_reply" class="form-control input-sm">
										<input name="ticketID" type="hidden" value="<?php echo $tid;?>">
										<br>
										<button class="btn btn-default" name="reply_ticket"><i class="fa fa-mail-reply"></i> Submit Reply</button>
									</form>
								<?php
								}
								else if($locked == "YES")
								{
								?>
								
									<br><br><br><br>
									<div class='col-md-12'>
										<div class='col-md-12 alert alert-warning' style="color: #000">
											<b>Dear participant! Your request has been closed since your request has already been solved. If you still have any questions – please create a new ticket.</b>
										</div>
									</div>
								<?php
								}
							}
							?>
							<br>
							<div id="showTICKET" style="display: none">
								<form method="post" action='../action'>
									<b>ISSUE TYPE:</b><br>
									<select class="form-control input-lg" name="relatedISSUE" 	required>
										<?php
										$relatedISSUE = $con->prepare("SELECT * FROM `relatedissue`");
										$relatedISSUE->execute();
											for($i=1; $i<=rows("SELECT * FROM `relatedissue`"); $i++)
											{
												$relatedISSUE_INFO = $relatedISSUE->fetch(PDO::FETCH_ASSOC);
												$option_issue = strtoupper($relatedISSUE_INFO["option_issue"]);
												// echo $option_issue;
											?>
												<option value="<?php echo $option_issue;?>"><?php echo $option_issue;?></option>
											<?php
											}
										?>
									</select>
									<br/>
									<b>SUBJECT:</b><br>
									<input type="text" name="subject" class="form-control input-lg" placeholder="Enter a subject" required>
									<br>
									<b>MESSAGE:</b><br>
									<textarea name="message" rows="3" class="form-control input-lg"></textarea>
									<br>
									<button class="btn btn-success btn-lg" name="SubmitTICKET"><b>SUBMIT TICKET</b></button>
								</form>
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
