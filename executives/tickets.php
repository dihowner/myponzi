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
	
}
else 
{
	redirect_to('index');
}
?>


<!DOCTYPE HTML>
<html>
<head>
<title>Giverscycler NEWS UPDATE</title>
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
  <link href="../img/favicon.jpg" rel="shortcut icon" type="" />
<link href="css/font-awesome.css" rel="stylesheet"> 
<!-- jQuery -->
<script src="js/jquery.min.js"></script>
<!----webfonts--->
<link href='http://fonts.googleapis.com/css?family=Roboto:400,100,300,500,700,900' rel='stylesheet' type='text/css'>
<!---//webfonts--->  
<!-- Nav CSS -->
<link href="css/custom.css" rel="stylesheet">
<!-- Metis Menu Plugin JavaScript -->
<script src="js/metisMenu.min.js"></script>
<script src="js/custom.js"></script>
<!-- Graph JavaScript -->
<script src="js/d3.v3.js"></script>
<script src="js/rickshaw.js"></script>
<script src="../js/mentor.js"></script>
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
                            <a href="logoff"><i class="fa fa-sign-out nav_icon"></i> Log Off</a>
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
			<br><font size='5px' color='red'><center><b>.::: Giverscycler TICKET :::. </b></center></font>
			<font size='3px'><center>Ensure to reply all ticket as they appear, don't be self-centered</center></font>
				
				<!--- PASSING MSG -->
				<?php
				if(isset($_GET['msg']))
				{
					$msg = $_GET['msg'];
					if($msg == strtoupper(substr(md5("Reply was added successfully"), -4)))
					{
					?>
						<div class='alert alert-success'>Reply was added successfully</div>
					<?php
					}
					else if($msg == strtoupper(substr(md5("Operation Successful"), -4)))
					{
					?>
						<div class='alert alert-success'>Operation Successful</div>
					<?php
					}
				}
				// <!--- PASSING MSG -->
				
				
				if(isset($_GET['tid']))
				{
					$tid = $_GET["tid"];
					$getallTicket = $con->prepare("SELECT * FROM `ticket` where tid='$tid' order by tid desc");
					$getallTicket->execute();
					$getallTicket_INFO = $getallTicket->fetch(PDO::FETCH_ASSOC);
					$subject = $getallTicket_INFO['subject'];	
					$relatedISSUE = $getallTicket_INFO['relatedIssue'];
					$ticketmsg = nl2br($getallTicket_INFO['ticketmsg']);
					$participant_cr8 = $getallTicket_INFO['participant']; // participant creating ticket
					$report_participant = $getallTicket_INFO['report_participant'];
					$locked = nl2br($getallTicket_INFO['locked']);
					$status = nl2br($getallTicket_INFO['replied']);
					?>
						<b>ISSUE TYPE:</b> <?php echo $relatedISSUE;?><br/>
						<b>SUBJECT:</b> <?php echo $subject;?><br/>
						<?php if($status == 0){?> <b>STATUS:</b> <font color="red"><b>NEW</b></font><?php }?><br/>
						<?php
						//Is it a single ticket??? dat is NOT FAKE POP
						if(empty($report_participant))
						{
							//We need to get participant dat created d ticket
							$getparticipant_cr8  = $con->prepare("SELECT * FROM `participant` where pid='$participant_cr8'");
							$getparticipant_cr8->execute();
							$getparticipant_cr8info = $getparticipant_cr8->fetch(PDO::FETCH_ASSOC);
							$participant_name_cr8 = $getparticipant_cr8info['name'];
							echo ucfirst($participant_name_cr8);
						}
						else if(!empty($report_participant)) 
						{
							//We need to get participant dat created d ticket
							$getparticipant_cr8  = $con->prepare("SELECT * FROM `participant` where pid='$participant_cr8'");
							$getparticipant_cr8->execute();
							$getparticipant_cr8info = $getparticipant_cr8->fetch(PDO::FETCH_ASSOC);
							$participant_name_cr8 = $getparticipant_cr8info['name'];
							echo ucfirst($participant_name_cr8) . ' || Receiver';
						}
						?>
						<br/>
						
					<?php echo $ticketmsg;?>
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
					
					<br><br>
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
					if(rows("SELECT * FROM `ticket_replies` where ticketid='$tid'") != 0)
					{
						echo '<br><br><br><br><br><br><br><br><br><br><br><br><br>';
					}
				?>
			
					
					<form method="post" action="../action" enctype="multipart/form-data">
						
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
						<br><textarea name="reply_ticket_msg" rows="5" class="form-control input-lg" required></textarea>
						<br><b>Add Attachment:</b><br>
						<font color="red" size='4px'>Maximum file upload is 2mb</font>
						<br>
						<input type="file" name="file_reply" class="form-control input-sm">
						<input name="ticketID" value="<?php echo $tid;?>" type="hidden">
						<br>
						
						<div class="col-sm-12 text-center">
							<button class="btn btn-default" name="reply_ticket_admin"><i class="fa fa-mail-reply"></i> Submit Reply</button>
							<a href='../action?LockTicket&tid=<?php echo $tid;?>' class="btn btn-default" name="lock_ticket"  onclick="return confirm('Issue Resolved?');"><i class="fa fa-lock"></i> Lock Ticket</a>
							
						</div>
					</form>
				<?php
				}
				else if(isset($_GET['Locked_TICKET']))
				{
					//We need to fetch all locked ticket
					$getallTicket = $con->prepare("SELECT * FROM `ticket` where locked='YES' order by tid desc");
					$getallTicket->execute();
					$ticketROWS = rows("SELECT * FROM `ticket` where locked='YES' order by locked desc");
					if($ticketROWS == 0)
					{
					?>
						<br><div class='alert alert-warning'><b>No Locked ticket yet</b></div>
					<?php
					}
					else
					{
						for($i= 1; $i<=$ticketROWS; $i++)
						{
							$getallTicket_INFO = $getallTicket->fetch(PDO::FETCH_ASSOC);
							$subject = $getallTicket_INFO['subject'];	
							$ticketID = $getallTicket_INFO['tid'];
							$ticketmsg = nl2br($getallTicket_INFO['ticketmsg']);
							$locked = nl2br($getallTicket_INFO['locked']);
							$replied = nl2br($getallTicket_INFO['replied']);	
						?>
							<a href="?tid=<?php echo $ticketID;?>"><font size="4"><b><?php echo $subject;?> (<font color="red"><?php echo $replied;?></font>)</b></font></a>
							<a href="../action?Unlock_Ticket=<?php echo $ticketID;?>" class='btn btn-default'><i class='fa fa-unlock'></i> Unlock </b></font></a>
							<br><br>
						<?php
						}
					}
				}
				else
				{
				?>
				<center><a href='?Locked_TICKET' class='btn btn-default btn-lg'>LOCKED TICKET</a></center>
				<br/>
				<?php
					$getallTicket = $con->prepare("SELECT * FROM `ticket` where locked='NO' order by tid desc");
					$getallTicket->execute();
					$ticketROWS = rows("SELECT * FROM `ticket` where locked='NO' order by locked desc");
					if($ticketROWS == 0)
					{
					?>
						<div class='alert alert-info'><b>No ticket yet</b></div>
					<?php
					}
					else
					{
						for($i= 1; $i<=$ticketROWS; $i++)
						{
							$getallTicket_INFO = $getallTicket->fetch(PDO::FETCH_ASSOC);
							$subject = $getallTicket_INFO['subject'];	
							$ticketID = $getallTicket_INFO['tid'];
							$ticketmsg = nl2br($getallTicket_INFO['ticketmsg']);
							$locked = nl2br($getallTicket_INFO['locked']);
							$replied = nl2br($getallTicket_INFO['replied']);	
						?>
							<a href="?tid=<?php echo $ticketID;?>"><font size="4"><b><?php echo $subject;?> (<font color="red"><?php echo $replied;?></font>)</b></font></a>
							<br><br>
						<?php
						}
					}
				}
				?>
				
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
