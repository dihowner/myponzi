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
<title>Giverscycler GH</title>
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
                                    <a href="participants?Blocked">Blocked User</a>
                                </li>
								<li>
                                    <a href="participants?Search">Search User</a>
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
                            <a href="tickets"><i class="fa fa-book nav_icon"></i>TICKETS</a>
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
               
				
				<?php
				//For all available PH??
				if(isset($_GET["AvailableGH"]))
				{
				?>
					<br><font size='5px' color='red'><center><b>.::: Giverscycler AVAILABLE GH :::. </b></center></font>
						<center><small><b>Get Help REQUEST on Queue</b></small></center>
					<br>
				<?php
					$allGH = $con->prepare("SELECT * FROM `gethelp` where merge!='YES' and merge!='Cancelled' and user_status='active' order by ID desc"); //Be it NO or partial
					$allGH->execute();
					$allGH_row = rows("SELECT * FROM `gethelp` where merge!='YES' and merge!='Cancelled' and user_status='active' order by ID desc");
					// echo $allPH_row;
					if($allGH_row == 0)
					{
					?>
						<br><div class='alert alert-warning' align='center'><font size='6px'><b>NO GH ON QUEUE YET</b></font></div>
					<?php
					}
					else
					{
						for($i=1; $i<=$allGH_row; $i++)
						{
							$allGH_info = $allGH->fetch(PDO::FETCH_ASSOC);
							$ghID = ucfirst($allGH_info['ghID']);
							// $wallet = $allGH_info['wallet'];
							$participantID = $allGH_info['participantID'];
							$amountGH = $allGH_info['amountGH'];
							$balance_inGH = $allGH_info['balance'];
							$ghDATE = $allGH_info['ghDATE'];
							$releaseDATE = $allGH_info['releaseDATE'];
							
							//Whats the participant name
							$getuinfo = $con->prepare("SELECT * FROM `participant` where pid='$participantID'");
							$getuinfo->execute();
							$ginfo = $getuinfo->fetch(PDO::FETCH_ASSOC);
							$pid = $ginfo['pid'];
							$participant_name = $ginfo['name'];
							
						?>
							<div class='col-md-4'>
								
								<div class="panel panel-default" style='color: #000;background: linear-gradient(to bottom,rgba(252,234,187,1) 0%,rgba(252,205,77,1) 46%,rgba(248,181,0,1) 60%,rgba(251,223,147,1) 100%); border: 1px solid #949494;'>
									<div class="panel-heading" style='border-radius: 10px;'>
										<b>Get Help (<?php echo $ghID;?>)</b>
									</div>
									<div class="panel-body" style='font-size: 15px'>
									   <b>Participant Name: </b><?php echo strtoupper($participant_name);?> 
									   <br/>
									   <b>Return Inward: </b>&#8358;<?php echo number_format(($amountGH),2);?> 
									   <br/>
									   
									   <b>Balance: </b>&#8358;<?php echo number_format(($balance_inGH),2);?> 
									   <br/>
									   
									   <b>Release Date: </b><?php echo $ghDATE;?> 
									   <br/>
									   <b>Receive Before: </b><?php echo $releaseDATE;?> 
									   
									</div>
								</div>
								
							</div>
							
						<?php
							
						}
						
					}
				}
				
				else if(isset($_GET["Merge"])) //Order Merge
				{
				?>
					<br><font size='5px' color='red'><center><b>.::: Giverscycler RUNNING GH :::. </b></center></font>
					<font size='3px'><center><b>GH YET TO BE PAID OR CONFIRMED BY RECEIVER</b></center></font><br>
				<?php
					$allGH = $con->prepare("SELECT * FROM `merge_gh` where attachment='' and status='' or status='Upload' order by mergeID desc");
					$allGH->execute();
					$allGH_merge_row = rows("SELECT * FROM `merge_gh` where attachment='' and status='' or status='Upload' order by mergeID desc");
					// echo $allGH_merge_row;
					if($allGH_merge_row == 0)
					{
					?>
						<br><div class='alert alert-warning' align='center'><font size='6px'><b>NO RUNNING GH</b></font></div>
					<?php
					}
					else
					{
						for($i=1; $i<=$allGH_merge_row; $i++)
						{
							$allGH_info = $allGH->fetch(PDO::FETCH_ASSOC);
							$ghID = ucfirst($allGH_info['ghID']);
							// $wallet = $allGH_info['wallet'];
							$ph_participantID = $allGH_info['participantID'];
							$gh_participantID = $allGH_info['gh_participantID'];
							$amountGH = $allGH_info['amountGH'];
							$createDATE = $allGH_info['dateMerge'];
							
							//Whats the participant name dat PH
							$getuinfo = $con->prepare("SELECT * FROM `participant` where pid='$ph_participantID'");
							$getuinfo->execute();
							$ginfo = $getuinfo->fetch(PDO::FETCH_ASSOC);
							$pid = $ginfo['pid'];
							$participant_name = $ginfo['name'];
							
							
							
							//Whats the participant name who is to pay
							$getpaytoinfo = $con->prepare("SELECT * FROM `participant` where pid='$gh_participantID'");
							$getpaytoinfo->execute();
							$paytoinfo = $getpaytoinfo->fetch(PDO::FETCH_ASSOC);
							$pid = $ginfo['pid'];
							$participant_name_toRCV = ucfirst($paytoinfo['name']);
							
							
						?>
							<div class='col-md-4'>
								
								<div class="panel panel-default" style='color: #000;background: linear-gradient(to bottom,rgba(252,234,187,1) 0%,rgba(252,205,77,1) 46%,rgba(248,181,0,1) 60%,rgba(251,223,147,1) 100%); border: 1px solid #949494;'>
									<div class="panel-heading" style='border-radius: 10px;'>
										<b>Get Help Request (<?php echo $ghID;?>)</b>
									</div>
									<div class="panel-body">
										<b>Participant Name: </b><?php echo strtoupper($participant_name_toRCV);?> 
										<br/>
										<b>Receive From: </b><?php echo ucfirst($participant_name);?> 
										<br/>
										<b>Amount: </b>&#8358;<?php echo number_format($amountGH);?> 
										<br/>
										<b>Date Merge: </b><?php echo $createDATE;?> 
									</div>
								</div>
								
							</div>
						<?php
							
						}
						?>
						<?php
					}
				}
				else if(isset($_GET["FAKEPOP"])) //FAKEPOP
				{
				?>
					<br><font size='5px' color='red'><center><b>.::: Giverscycler FAKE PROOF OF PAYMNET :::. </b></center></font>
					<font size='3px'><center><b>  Resolve FAKE POP AND REMERGE GH ORDER </b></center></font><br>
					
				<?php
					if(isset($_GET['msg']))
					{
						$msg = $_GET['msg'];
						if($msg == strtoupper(substr(md5("Action Completed!"), -4)))
						{
					?>
						<div class='alert alert-success'><b>Action Completed!</b></div>
					<?php
						}
					}
					$allGH = $con->prepare("SELECT * FROM `merge_gh` where attachment!='' and status='FAKEPOP' order by mergeID desc");
					$allGH->execute();
					$allGH_merge_row = rows("SELECT * FROM `merge_gh` where attachment!='' and status='FAKEPOP' order by mergeID desc");
					// echo $allGH_merge_row;
					if($allGH_merge_row == 0)
					{
					?>
						<br><div class='alert alert-warning' align='center'><font size='6px'><b>NO FAKE POP</b></font></div>
					<?php
					}
					else
					{
						for($i=1; $i<=$allGH_merge_row; $i++)
						{
							$allGH_info = $allGH->fetch(PDO::FETCH_ASSOC);
							$ghID = ucfirst($allGH_info['ghID']);
							$phID = ucfirst($allGH_info['phID']);
							// $wallet = $allGH_info['wallet'];
							$ph_participantID = $allGH_info['participantID'];
							$gh_participantID = $allGH_info['gh_participantID'];
							$amountGH = $allGH_info['amountGH'];
							$createDATE = $allGH_info['dateMerge'];
							$attachment = $allGH_info['attachment'];
							
							//Whats the participant name dat PH
							$getuinfo = $con->prepare("SELECT * FROM `participant` where pid='$ph_participantID'");
							$getuinfo->execute();
							$ginfo = $getuinfo->fetch(PDO::FETCH_ASSOC);
							$pid = $ginfo['pid'];
							$participant_name = $ginfo['name'];
							
							
							
							//Whats the participant name who is to pay
							$getpaytoinfo = $con->prepare("SELECT * FROM `participant` where pid='$gh_participantID'");
							$getpaytoinfo->execute();
							$paytoinfo = $getpaytoinfo->fetch(PDO::FETCH_ASSOC);
							$pid = $ginfo['pid'];
							$participant_name_toRCV = ucfirst($paytoinfo['name']);
							
							
						?>
							<div class='col-md-4'>
								
								<div class="panel panel-default" style='color: #000;background: linear-gradient(to bottom,rgba(252,234,187,1) 0%,rgba(252,205,77,1) 46%,rgba(248,181,0,1) 60%,rgba(251,223,147,1) 100%); border: 1px solid #949494;'>
									<div class="panel-heading" style='border-radius: 10px;'>
										<b>Get Help Request (<?php echo $ghID;?>)</b>
									</div>
									<div class="panel-body">
										<b>Participant Name: </b><?php echo strtoupper($participant_name_toRCV);?> 
										<br/>
										<b>Receive From: </b><?php echo ucfirst($participant_name);?> 
										<br/>
										<b>Inward: </b>&#8358;<?php echo number_format($amountGH);?> 
										<br/>
										<b>Date Merge: </b><?php echo $createDATE;?> 
										<br>
										<a href='../img/attachment/<?php echo $attachment;?>' class='btn btn-default' target='_blank'>View</a>
										<br>
										<a href='../action?ResolvePOP&phID=<?php echo $phID;?>&ghID=<?php echo $ghID;?>' class='btn btn-default' onclick="return confirm('Action is irreversible!');">Re-Merge Order</a>
										<a href='#' class='btn btn-default' onclick="return confirm('Payment should be confirmed by participant only');">CONFIRM</a>
									</div>
								</div>
								
							</div>
						<?php
							
						}
						?>
						<?php
					}
				}
				else
				{
					redirect_to("gh-request?AvailableGH");
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
