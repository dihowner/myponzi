<?php
require "../config.php";
require '../private/UrgentMerging.php';

//Since user has logged in, then we need to know his level, Admin or Super Admin
if(isset($username))
{
	
	$query_executive_row = rows("select * from executive where username='$username'");
	if($query_executive_row == 0)
	{
		//thief..... Go out
		redirect_to('index');
	}
	
	$query_executive = $con->prepare("select * from executive where username='$username'");
	$query_executive->execute();
	$query_executive_info = $query_executive->fetch(PDO::FETCH_ASSOC);
	$level = $query_executive_info['level'];
	
	
	//How many participant do we have???
	$user_row_active = rows("SELECT * FROM `participant` where status='active'");
	$user_row = rows("SELECT * FROM `participant`");
	
	//How many GH do we have???
	$running_gh_notyetpaid = rows("SELECT * FROM `merge_gh` where status='' and attachment=''");
	
	//How many GH do we have???
	$running_gh_notmerge_paidfully = rows("SELECT * FROM `gethelp` where merge='partial' or merge='NO'");
	
	//How many GH do we have???
	$running_ph_notmerge = rows("SELECT * FROM `providehelp` where status='Unconfirmed' and merge='NO'");
	
}
else 
{
	redirect_to('index');
}
?>


<!DOCTYPE HTML>
<html>
<head>
<title>Giverscycler FORUM DISCUSSION</title>
<meta name="viewport" content="width=device-width, initial-scale=1">
<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
<meta name="keywords" content="Modern Responsive web template, Bootstrap Web Templates, Flat Web Templates, Andriod Compatible web template, 
Smartphone Compatible web template, free webdesigns for Nokia, Samsung, LG, SonyErricsson, Motorola web design" />
<script type="application/x-javascript"> addEventListener("load", function() { setTimeout(hideURLbar, 0); }, false); function hideURLbar(){ window.scrollTo(0,1); } </script>
 <!-- Bootstrap Core CSS -->
<link href="css/bootstrap.min.css" rel='stylesheet' type='text/css' />
<!-- Custom CSS -->
<link href="css/style.css" rel='stylesheet' type='text/css' />
<!-- Graph CSS -->
<link href="css/lines.css" rel='stylesheet' type='text/css' />
<link href="css/font-awesome.css" rel="stylesheet"> 
<!-- jQuery -->
<script src="js/jquery.min.js"></script>
<!----webfonts--->
<link href='http://fonts.googleapis.com/css?family=Roboto:400,100,300,500,700,900' rel='stylesheet' type='text/css'>
<!---//webfonts--->  
<!-- Nav CSS -->
<link href="css/custom.css" rel="stylesheet">
  <link href="../img/favicon.jpg" rel="shortcut icon" type="" />
<!-- Metis Menu Plugin JavaScript -->
<script src="js/metisMenu.min.js"></script>
<script src="js/custom.js"></script>
<!-- Graph JavaScript -->
<script src="js/d3.v3.js"></script>
<script src="js/rickshaw.js"></script>
</head>
<body>
<div id="wrapper">
     <!-- Navigation -->
        <nav class="top1 navbar navbar-default navbar-static-top" role="navigation" style="margin-bottom: 0">
            <div class="navbar-header">
                <button type="button" class="navbar-toggle" data-toggle="collapse" data-target=".navbar-collapse">
                    <span class="sr-only">Toggle navigation</span>
                    <span class="icon-bar"></span>
                    <span class="icon-bar"></span>
                    <span class="icon-bar"></span>
                </button>
                <a class="navbar-brand" href="dashboard"><img src='../img/logo.jpg' width='200px' height='40px' style='margin-top: -8px; margin-left: -10px'/></a>
            </div>
            <!-- /.navbar-header -->
            <ul class="nav navbar-nav navbar-right">
				<li class="dropdown">
	        		<a href="#" class="dropdown-toggle" data-toggle="dropdown" aria-expanded="false"><i class="fa fa-comments-o"></i><span class="badge">4</span></a>
	        		<ul class="dropdown-menu">
						<li class="dropdown-menu-header">
							<strong>Messages</strong>
							<div class="progress thin">
							  <div class="progress-bar progress-bar-success" role="progressbar" aria-valuenow="40" aria-valuemin="0" aria-valuemax="100" style="width: 40%">
							    <span class="sr-only">40% Complete (success)</span>
							  </div>
							</div>
						</li>
						<li class="avatar">
							<a href="#">
								<img src="images/1.png" alt=""/>
								<div>New message</div>
								<small>1 minute ago</small>
								<span class="label label-info">NEW</span>
							</a>
						</li>
						<li class="avatar">
							<a href="#">
								<img src="images/2.png" alt=""/>
								<div>New message</div>
								<small>1 minute ago</small>
								<span class="label label-info">NEW</span>
							</a>
						</li>
						<li class="avatar">
							<a href="#">
								<img src="images/3.png" alt=""/>
								<div>New message</div>
								<small>1 minute ago</small>
							</a>
						</li>
						<li class="avatar">
							<a href="#">
								<img src="images/4.png" alt=""/>
								<div>New message</div>
								<small>1 minute ago</small>
							</a>
						</li>
						<li class="avatar">
							<a href="#">
								<img src="images/5.png" alt=""/>
								<div>New message</div>
								<small>1 minute ago</small>
							</a>
						</li>
						<li class="avatar">
							<a href="#">
								<img src="images/pic1.png" alt=""/>
								<div>New message</div>
								<small>1 minute ago</small>
							</a>
						</li>
						<li class="dropdown-menu-footer text-center">
							<a href="#">View all messages</a>
						</li>	
	        		</ul>
	      		</li>
				<li><a href="logoff"><i class="fa fa-lock"></i> Logout </a></li>
				
			</ul>
			
            <div class="navbar-default sidebar" role="navigation">
                <div class="sidebar-nav navbar-collapse">
                    <ul class="nav active" id="side-menu">
                        <li>
                            <a href="dashboard" class=''><i class="fa fa-home nav_icon"></i> Dashboard</a>
                        </li>
						<?php
						if($level =='Super Admin')
						{
						?>
							<li>
								<a href="createGH"><i class="fa fa-plus nav_icon"></i>CREATE GH</a>
							</li>
							
							<li>
								<a href="all-transaction"><i class="fa fa-exchange nav_icon"></i>ALL TRANSACTIONS</a>
							</li>
							
							<li>
								<a href="#"><i class="fa fa-users nav_icon"></i>QUEUE<span class="fa arrow"></span></a>
								<ul class="nav nav-second-level">
									<li>
										<a href="CancelQueue"><i class="fa fa-exchange nav_icon"></i>Cancel Queue</a>
									</li>
									<li>
										<a href="loadghtotray"><i class="fa fa-exchange nav_icon"></i>OLD GH TRAY</a>
									</li>
									
									<li>
										<a href="addGHQueue"><i class="fa fa-exchange nav_icon"></i>MANUAL QUEUE</a>
									</li>
									
									<li>
										<a href="editGH"><i class="fa fa-pencil nav_icon"></i>EDIT GH</a>
									</li>
									
								</ul>
								<!-- /.nav-second-level -->
							</li>
                        
							
							<li>
								<a href="forum"><i class="fa fa-book nav_icon"></i>FORUM DISCUSSION</a>
							</li>
							
							<li>
								<a href="loadGH-PH"><i class="fa fa-book nav_icon"></i>MANUAL MERGING</a>
							</li>
							
						<?php
						}
						?>
                        <li>
                            <a href="#"><i class="fa fa-users nav_icon"></i>PARTICIPANT<span class="fa arrow"></span></a>
                            <ul class="nav nav-second-level">
                                <li>
                                    <a href="participants?All">All Participants</a>
                                </li>
								<li>
                                    <a href="participants?Blocked">Blocked Participants</a>
                                </li>
								<li>
                                    <a href="participants?Search">Search Participants</a>
                                </li>
                            </ul>
                            <!-- /.nav-second-level -->
                        </li>
                        <li>
                            <a href="#"><i class="fa fa-money nav_icon"></i>PH REQUEST<span class="fa arrow"></span></a>
                            <ul class="nav nav-second-level">
                                <li>
                                    <a href="ph-request?AvailablePH"><i class='fa fa-briefcase'></i> Available PH</a>
                                </li>
                                <li>
                                    <a href="ph-request?Merge"><i class='fa fa-exchange'></i> Running PH</a>
                                </li>
                                <li>
                                    <a href="ph-request?Cancelled"><i class='fa fa-times'></i> Cancelled PH</a>
                                </li>
                            </ul>
                            <!-- /.nav-second-level -->
                        </li>
                        <li>
                            <a href="#"><i class="fa fa-money nav_icon"></i> GH REQUEST<span class="fa arrow"></span></a>
                            <ul class="nav nav-second-level">
                                <li>
                                    <a href="gh-request?AvailableGH"><i class='fa fa-briefcase'></i> Available GH</a>
                                </li>
                                <li>
                                    <a href="gh-request?Merge"><i class='fa fa-exchange'></i> Merged GH</a>
                                </li>
                                <li>
                                    <a href="gh-request?FAKEPOP"><i class='fa fa-refresh fa-spin'></i> FAKE POP</a>
                                </li>
                            </ul>
                            <!-- /.nav-second-level -->
                        </li>
                        <li>
                            <a href="gh-request?FAKEPOP"><i class="fa fa-refresh nav_icon fa-spin"></i>RESOLVE FAKE POP</a>
                        </li>
						<li>
							<a href="newsupdate"><i class="fa fa-book nav_icon"></i>NEWS UPDATE</a>
						</li>
                        <li>
                            <a href="tickets"><i class="fa fa-envelope nav_icon fa-spin"></i>TICKETS</a>
                        </li>
                        <li>
                            <a href="logoff"><i class="fa fa-sign-out nav_icon"></i> Log Off(<?php echo $username;?>)</a>
                        </li>
						
                    </ul>
                </div>
                <!-- /.sidebar-collapse -->
            </div>
            <!-- /.navbar-static-side -->
        </nav>
        <div id="page-wrapper">
        <div class="graphs">
     	
		
		
		
		<div class="col_1">
		    
				
			
			<div class="col-md-12 stats-info" style='color: #000'>
			<br><font size='5px' color='red'><center><b>.::: Giverscycler FORUM UPDATE :::. </b></center></font>
				
					
					<center><small><b>Forum discussion are added to the website directly, be mindful of what you post!</b></small></center><br>
					<?php
					if(isset($_GET['msg']))
					{
						$msg = $_GET['msg'];
						if($msg == strtoupper(substr(md5("Topic added successfully"), -4)))
						{
						?>
							<div class='alert alert-success'>Topic added successfully</div>
						<?php
						}
						else if($msg == strtoupper(substr(md5("Error Occured, Please Retry"), -4)))
						{
						?>
							<div class='alert alert-warning'>Error Occured, Please Retry</div>
						<?php
						}
					}
					if(isset($_GET['ViewFORUM']))
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
					?>
					<?php
					}
					else if(isset($_GET['viewtopic']))
					{
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
						$viewtopic = $_GET['viewtopic'];
					
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
								
								<label><b>Select Admin:</b></label>
								<select class='form-control input-lg' name='reply_name' required>
									<option value=''>Select Replying Officer</option>
									
									<?php
									$reply_admin = $con->prepare("SELECT * FROM `reply_admin` order by rand()");
									$reply_admin->execute();
									for($i=1; $i<=rows("SELECT * FROM `reply_admin` order by rand()"); $i++)
									{
									$reply_admin_INFO = $reply_admin->fetch(PDO::FETCH_ASSOC);
									$reply_name = strtoupper($reply_admin_INFO['reply_name']);
									?>
										<option value='<?php echo $reply_name;?>'><?php echo $reply_name;?></option>
									<?php
									}
									?>
								</select>
								<label><b>Message:</b></label>
								<br><textarea name="reply_topic_msg" rows="5" class="form-control input-lg" required></textarea>
								
								<input name="topicID" value="<?php echo $viewtopic;?>" type="hidden">
								<br>
								
								<div class="col-sm-12 text-center">
									<button class="btn btn-default" name="topic_reply_admin"><i class="fa fa-mail-reply"></i> Submit Reply</button>
								</div>
							</form>
							
						<?php
					}
					else
					{
					?>
					
						<center><a href='?ViewFORUM' class='btn btn-default btn-lg'>VIEW ALL FORUM</a></center>
						<br>
						<form method='post' action='../action'>
						
							<label><b>Select Admin:</b></label>
							<select class='form-control input-lg' name='poster_name' required>
								<option value=''>Select Officer</option>
								
								<?php
								$reply_admin = $con->prepare("SELECT * FROM `reply_admin` order by rand()");
								$reply_admin->execute();
								for($i=1; $i<=rows("SELECT * FROM `reply_admin` order by rand()"); $i++)
								{
								$reply_admin_INFO = $reply_admin->fetch(PDO::FETCH_ASSOC);
								$reply_name = strtoupper($reply_admin_INFO['reply_name']);
								?>
									<option value='<?php echo $reply_name;?>'><?php echo $reply_name;?></option>
								<?php
								}
								?>
							</select>
							<br>
							<label><b>Forum Topic:</b></label>
							<br>
							<input type='text' class='form-control input-lg' name='forumtopic' id='forumtopic' autocomplete='off' required>
							<br>
							<label><b>Message:</b></label>
							<br>
							<textarea name='topicmsg_content' id='topicmsg_content' class='form-control input-lg' rows='4' required></textarea>
							<br>
							<button class='btn btn-success' name='save_forumtopic' id='save_forumtopic'><i class='fa fa-save fa-2x'></i> Submit Topic</button>
						</form>
					<?php
					}?>
					<br><br><br>
		
            </div>
		
			<div class="clearfix"> </div>
		
		</div>
	  
	  <br>
	  
		<div class="copy">
             <p><b>© 2017 Giverscycler ::: Givers are recievers.</b></p>
	    </div>
		</div>
       </div>
      <!-- /#page-wrapper -->
   </div>
    <!-- /#wrapper -->
    <!-- Bootstrap Core JavaScript -->
    <script src="js/bootstrap.min.js"></script>
</body>
</html>
