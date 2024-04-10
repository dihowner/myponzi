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
	$user_row = rows("SELECT * FROM `participant`");
	
	//How many GH do we have???
	$running_gh_notyetpaid = rows("SELECT * FROM `merge_gh` where status='' and attachment=''");
	
	//How many GH do we have???
	$running_gh_notmerge_paidfully = rows("SELECT * FROM `gethelp` where merge='partial' or merge='NO'");
	
	//How many GH do we have???
	$running_ph_notmerge = rows("SELECT * FROM `providehelp` where status='Unconfirmed' or merge='NO'");
	
}
else 
{
	redirect_to('index');
}
?>


<!DOCTYPE HTML>
<html>
<head>
<title>Giverscycler Participants</title>
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
<script src="js/jquery-2.1.4.min.js"></script>
<script src="js/js.js"></script>
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
		    
			
			
			<div class="col-md-12 stats-info">
               
				
				<?php
				//For all active participant
				if(isset($_GET["All"]))
				{
				?>
					<br><font size='5px' color='red'><center><b>.::: Giverscycler Active Participants :::. </b></center></font>
				<?php
					if(isset($_GET['msg']))
					{
						$msg = $_GET['msg'];
						if($msg == strtoupper(substr(md5('Operation Successful'), -4)))
						{
						?>
							<br><div class='alert alert-success' align='center'><font size='6px'><b>Operation Successful</b></font></div>
						<?php 
						}
					}
					$allParticipant = $con->prepare("SELECT * FROM `participant` where status='active'");
					$allParticipant->execute();
					$allParticipant_row = rows("SELECT * FROM `participant` where status='active'");
					if($allParticipant_row == 0)
					{
					?>
						<br><div class='alert alert-warning' align='center'><font size='6px'><b>NO ACTIVE PARTICIPANT YET</b></font></div>
					<?php
					}
					else
					{
					?>
						<table class="table table-bordered table-responsive table-hover">
							<tr>
								<td style='color: #000'><b>MEMBER</b></td>
								<td style='color: #000'><b>PHONE</b></td>
								<td style='color: #000'><b>EMAIL</b></td>
							</tr>
							<tr>
					<?php
						for($i=1; $i<=$allParticipant_row; $i++)
						{
							$allParticipant_info = $allParticipant->fetch(PDO::FETCH_ASSOC);
							$participant_name = ucfirst($allParticipant_info['name']);
							$participant_mobile = $allParticipant_info['mobile'];
							$participant_email = $allParticipant_info['email'];
						?>
								<td style='color: #000'><?php echo $participant_name;?></td>
								<td style='color: #000'><?php echo $participant_mobile;?></td>
								<td style='color: #000'><?php echo $participant_email;?></td>
							</tr>
						<?php
							
						}
					?>
						</table>
					<?php
					}
				}
				
				else if(isset($_GET["Blocked"]))
				{
				?>
					<br><font size='5px' color='red'><center><b>.::: Giverscycler Blocked Participants :::. </b></center></font>
					
				<?php
					
						if(isset($_GET['msg']))
						{
							$msg = $_GET['msg'];
							if($msg == strtoupper(substr(md5('User Unblocked'), -4)))
							{
							?>
								<br><div class='alert alert-success' align='center'><font size='6px'><b>User Unblocked</b></font></div>
							<?php 
							}
						}
					$allParticipant = $con->prepare("SELECT * FROM `participant` where status!='active'");
					$allParticipant->execute();
					$allParticipant_row = rows("SELECT * FROM `participant` where status!='active'");
					if($allParticipant_row == 0)
					{
					?>
						<br><div class='alert alert-warning' align='center'><font size='6px'><b>NO BLOCKED PARTICIPANT YET</b></font></div>
					<?php
					}
					else
					{
					?>
						<br>
						<table class="table table-bordered table-responsive table-hover">
							<tr>
								<td style='color: #000'><b>MEMBER</b></td>
								<td style='color: #000'><b>EMAIL</b></td>
								<td style='color: #000'><b>Action</b></td>
							</tr>
							<tr>
					<?php
						for($i=1; $i<=$allParticipant_row; $i++)
						{
							$allParticipant_info = $allParticipant->fetch(PDO::FETCH_ASSOC);
							$participant_name = ucfirst($allParticipant_info['name']);
							$participant_id = $allParticipant_info['pid'];
							$participant_email = $allParticipant_info['email'];
						?>
								<td style='color: #000'><?php echo $participant_name;?></td>
								<td style='color: #000'><?php echo $participant_email;?></td>
								<td style='color: #000'><a href='../action?Unblock=<?php echo $participant_id;?>' class='btn btn-default'  onclick="return confirm('Unblock <?php echo $participant_name;?>? \n \nDo you want to Proceed?');"><b>Unblock</b></a></td>
							</tr>
						<?php
							
						}
					?>
						</table>
					<?php
					}
				}
				
				else if(isset($_GET["Search"]))
				{
				?>
					<br><font size='5px' color='red'><center><b>.::: Giverscycler Search Participants :::. </b></center></font>
					
				<?php
					
					if(isset($_GET['msg']))
					{
						$msg = $_GET['msg'];
						if($msg == strtoupper(substr(md5('User Unblocked'), -4)))
						{
						?>
							<br><div class='alert alert-success' align='center'><font size='6px'><b>User Unblocked</b></font></div>
						<?php 
						}
					}
				?>	
					<form method='post'>
						<label><b>Enter Email or Phone or Username</b></label>
						<br>
						<input type='text' name='search_participant' id='search_participant' placeholder='Enter Email or Phone or Username' class='form-control input-lg' autocomplete='off'/>
						<br>
						<!--<button class='btn btn-default' id='click_SEARCH_participant'><i class='fa fa-search'></i> Search Participant</button>-->
					</form>
						
				<div id='result'></div>
				
						<br><br><br>
				<?php

				}
				
				
				else if(isset($_GET["ChangePskey"]) && isset($_GET['pid']))
				{
					$pid = $_GET['pid'];
					$get_participants = $con->prepare("SELECT * FROM `participant` where pid='$pid'");
					$get_participants->execute();
					$get_participants_INFO = $get_participants->fetch(PDO::FETCH_ASSOC);
					$p_name = $get_participants_INFO['name'];
				?>
					<br><font size='5px' color='red'><center><b>.::: Giverscycler Edit User :::. </b></center></font>
					<font size='2px'><center><b>Do not edit any participant details without informing the participant first!</b></center></font>
					
				<?php
					
					if(isset($_GET['msg']))
					{
						$msg = $_GET['msg'];
						if($msg == strtoupper(substr(md5('User Unblocked'), -4)))
						{
						?>
							<br><div class='alert alert-success' align='center'><font size='6px'><b>User Unblocked</b></font></div>
						<?php 
						}
					}
				?>	
					<form method='post' action='../action'>
						<label><b>Participant Name</b></label>
						<br>
						<input value='<?php echo strtoupper($p_name);?>' class='form-control input-lg' disabled/>
						<br>
						<label><b>Enter Password</b></label>
						<br>
						<input type='password' name='change_pswd' id='change_pswd' placeholder='Enter New Password' class='form-control input-lg' autocomplete='off'/>
						<input type='hidden' name='pid' id='pid' value='<?php echo $pid;?>'/>
						<br>
						<button name='save_editPSK' class='btn btn-success'><b><i class='fa fa-save fa-2x'></i> Save Changes</b></button>
						<!--<button class='btn btn-default' id='click_SEARCH_participant'><i class='fa fa-search'></i> Search Participant</button>-->
					</form>
						
				<div id='result'></div>
				
						<br><br><br>
				<?php

				}




				else if(isset($_GET["ChangeBank"]) && isset($_GET['pid']))
				{
					$pid = $_GET['pid'];
					$get_participants = $con->prepare("SELECT * FROM `participant` where pid='$pid'");
					$get_participants->execute();
					$get_participants_INFO = $get_participants->fetch(PDO::FETCH_ASSOC);
					$p_name = $get_participants_INFO['name'];
				?>
					<br><font size='5px' color='red'><center><b>.::: Giverscycler Edit User :::. </b></center></font>
					<font size='2px'><center><b>Do not edit any participant details without informing the participant first!</b></center></font>

				<?php

					if(isset($_GET['msg']))
					{
						$msg = $_GET['msg'];
						if($msg == strtoupper(substr(md5('User Unblocked'), -4)))
						{
						?>
							<br><div class='alert alert-success' align='center'><font size='6px'><b>User Unblocked</b></font></div>
						<?php
						}
					}
				?>
					<form method='post' action='../action'>
						<label><b>Participant Name</b></label>
						<br>
						<input value='<?php echo strtoupper($p_name);?>' class='form-control input-lg' disabled/>
                        <br>
                        <label><b>Enter Bank Name:</b></label>
                        <br>
                        <input type='text' name='bank_name' id='bank_name' placeholder='Enter Bank Name' class='form-control input-lg' autocomplete='off'/>
                        <br>
                        <label><b>Enter Account Name:</b></label>
                        <br>
                        <input type='text' name='merchant_name' id='merchant_name' placeholder='Enter Account Name' class='form-control input-lg' autocomplete='off'/>
                        <br>
                        <label><b>Enter Account Number:</b></label>
                        <br>
                        <input type='text' name='merchant_number' id='merchant_number' placeholder='Enter Account Number' class='form-control input-lg' autocomplete='off'/>
						<input type='hidden' name='pid' id='pid' value='<?php echo $pid;?>'/>
						<br>
						<button name='save_bankDETAILS' class='btn btn-success'><b><i class='fa fa-save fa-2x'></i> Save Changes</b></button>
						<!--<button class='btn btn-default' id='click_SEARCH_participant'><i class='fa fa-search'></i> Search Participant</button>-->
					</form>

				<div id='result'></div>

						<br><br><br>
				<?php

				}
				?>
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
