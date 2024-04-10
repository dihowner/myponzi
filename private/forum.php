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
	
	
	//Has participant reead the latest news??? Oh No!
	
	$check_allpendingGH = rows("Select * from gethelp where participantID='$pid' and merge='pending'");
	//Has participant reead the latest news??? Oh No!
	$forum_read = rows("SELECT * FROM `forumlogged` where loggedID='$username'");
	$getallForum = $con->prepare("SELECT * FROM `forumtopics`");
	$getallForum->execute();
	$getallForum_INFO = $getallForum->fetch(PDO::FETCH_ASSOC);
	$topicid = $getallForum_INFO['topicid'];
	
	if($check_allpendingGH >=1 && $forum_read == 0)
	{
		$saveforumlogged = $con->prepare("insert into forumlogged (loggedID) values ('$username')");
		$saveforumlogged->execute();
	}
	
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
                        <h5>Welcome <b><?php echo ucfirst($participant_name);?></b> , Welcome to our forum discussion portal. </h5><br><br>
                    </div>
										
				<div class="col-md-12">
					<?php
					if(isset($_GET['viewtopic']))
					{
						$viewtopic = $_GET['viewtopic'];
					?>
					
                    <div class="col-md-12">
						
						<?php
							if(isset($_GET['msg']))
							{
								$msg = $_GET['msg'];
								if($msg == strtoupper(substr(md5("Reply added successfully"), -4)))
								{
								?>
									<div class='alert alert-success'><b>Reply was added successfully</b></div>
								<?php
								}
								else if($msg == strtoupper(substr(md5("Error Occured, Please Retry"), -4)))
								{
								?>
									<div class='alert alert-warning'><b>Error Occured, Please Retry</b></div>
								<?php
								}
							}
							$getallForum = $con->prepare("SELECT * FROM `forumtopics` where topicid='$viewtopic'");
							$getallForum->execute();
							$getallForum_INFO = $getallForum->fetch(PDO::FETCH_ASSOC);
							$subject = $getallForum_INFO['subject'];	
							$topicmsg = $getallForum_INFO['topicmsg'];	
							$poster = $getallForum_INFO['poster'];	
							$date_written = $getallForum_INFO['date_written'];	
							?>
							<div class='col-md-12' style="color: #000; background: #fee996; border-radius: 10px; margin: 10px 0; padding: 10px;border: 1px solid #d19405;">
								<center><font size='6px'><strong><?php echo $subject;?></strong></font></center>
								
								<font color='red'><b><?php echo nl2br($poster);?></b></font>
								<br><br>
								<?php echo nl2br(htmlspecialchars_decode($topicmsg));?>
								<br><br>	
								<b> <?php echo $date_written;?></b>
							</div>
							<div class='row'></div>
							
							<?php
							//We need to fetch all replies made by receiver and payee
							$getREPLIES = $con->prepare("SELECT * FROM `forumreply` where topicid='$viewtopic' order by replyid desc");
							$getREPLIES->execute();
							$getreplyrow = rows("SELECT * FROM `forumreply` where topicid='$viewtopic' order by replyid desc");
							if($getreplyrow == 0)
							{
							?>
								<br><div class='alert alert-success'><b>NO Suggestion Yet! Be the first to do so.</b></div>
							<?php
							}
							else
							{
								for($i=1; $i<=$getreplyrow; $i++)
								{
									$getREPLIES_INFO = $getREPLIES->fetch(PDO::FETCH_ASSOC);
									$replymsg = $getREPLIES_INFO["replymsg"]; // Msg replied
									$participantID = $getREPLIES_INFO["participantID"];
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
										
										$participant_name_replied = $participant_name_replied . ' || Member';
									}
									?>
									<div class='col-md-12' style="color: #000; background: #fee996; border-radius: 10px; margin: 10px 0; padding: 10px;border: 1px solid #d19405;">
										<b><?php echo ucfirst($participant_name_replied);?>
										<br><br>
										<?php echo $replymsg;?>
										<br><br>
										<?php echo $date_written;?>
									</div>
									<?php
								}
							}
							?>
							
							
							
							<form method="post" action="../action" >
						
								<label><b>Message:</b></label>
								<br><textarea name="reply_topic_msg" rows="5" class="form-control input-lg" required></textarea>
								
								<input name="topicID" value="<?php echo $viewtopic;?>" type="hidden">
								<br>
								
								<div class="col-sm-12 text-center">
									<button class="btn btn-default" name="topic_reply"><i class="fa fa-mail-reply"></i> Submit Reply</button>
								</div>
							</form>
							<?php
						?>
					</div>
					<?Php
					}
					else
					{
						$getall_news = $con->prepare("select * from `forumtopics` order by topicid desc");
						$getall_news->execute();
						for($i=1; $i<=rows("select * from `forumtopics` order by topicid desc"); $i++)
						{
							$getall_news_INFO = $getall_news->fetch(PDO::FETCH_ASSOC);
							$topicmsg = $getall_news_INFO["topicmsg"];
							$subject = $getall_news_INFO["subject"];
							$news_id = $getall_news_INFO["topicid"];
							$date_written = $getall_news_INFO["date_written"];
							$replies = $getall_news_INFO["replies"];
						?>
							<a href='?viewtopic=<?php echo $news_id;?>'><?php if(empty($subject)){?> LATEST UPDATE<?php } else {echo strtoupper($subject);}?> (<font color="red"><?php echo $replies;?></font>)</a>
							<br><br>
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
