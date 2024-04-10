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
			<br><font size='5px' color='red'><center><b>.::: Giverscycler NEWS UPDATE :::. </b></center></font>
				<?php
				if(isset($_GET['ViewNEWS']))
				{
					$getall_news = $con->prepare("select * from `newsupdate` order by news_id desc");
					$getall_news->execute();
					for($i=1; $i<=rows("select * from `newsupdate`"); $i++)
					{
						$getall_news_INFO = $getall_news->fetch(PDO::FETCH_ASSOC);
						$newsmsg = $getall_news_INFO["newsmsg"];
						$news_subject = $getall_news_INFO["news_subject"];
						$news_id = $getall_news_INFO["news_id"];
						$date_written = $getall_news_INFO["date_written"];
					?>
						<a href='#' id='news_id<?php echo $news_id;?>'><?php if(empty($news_subject)){?> LATEST UPDATE<?php } else {echo strtoupper($news_subject);}?></a>
						<a href='#'  id='EditNews<?php echo $news_id;?>' class='btn btn-default'>EDIT NEWS</a>
						<br>
						<div id='OPenNews_4EDIT<?php echo $news_id;?>'style='display: none;'>
							<form method='post' action='../action'>
								<br>	
								<input type='text' class='form-control input-lg' name='topic' id='topic' autocomplete='off' value='<?php echo $news_subject;?>'>
								<input type='hidden' name='postid' value='<?php echo $news_id;?>'>
								<br><textarea class='form-control input-lg' name='newsupdate_content' rows='7'><?php echo $newsmsg;?></textarea>
								<button class='btn btn-success' name='saveEDITEDNEWS'><i class='fa fa-pencil'></i> Edit News</button>
							</form>
						</div>
						<div id='showNEWS<?php echo $news_id;?>' class='panel panel-default' style='display: none; color: #000; background: #fee996; border-radius: 10px; margin: 10px 0; padding: 10px;border: 1px solid #d19405;'>
						
						<?php echo nl2br(htmlspecialchars_decode($newsmsg));?>
							<br><br>
							<?php echo $date_written;?>
						</div>
						<br>
						<script>
						$(document).ready(function(){

							//OPening & Closing of News
							$("#news_id<?php echo $news_id;?>").click(function(PI){
								PI.preventDefault();
								$("#showNEWS<?php echo $news_id;?>").slideToggle(3000);
								
							});
							
							//Editing of News
							$("#EditNews<?php echo $news_id;?>").click(function(PI){
								PI.preventDefault();
								$("#OPenNews_4EDIT<?php echo $news_id;?>").slideToggle(3000);
								
							});
						});
						</script>
						
					<?php
					}
				}
				else
				{
				?>
				
					
						<center><small><b>News are published directly on the website homepage, be mindful of what you post!</b></small></center>
						<br><center><a href='?ViewNEWS' class='btn btn-default btn-lg'>VIEW ALL NEWS</a></center>
					<br>
						<!--<div id="result"></div>-->
						<?php
						if(isset($_GET['msg']))
						{
							$msg = $_GET['msg'];
							if($msg == strtoupper(substr(md5('News Added Successfully'), -4)))
							{
						?>
							<div class="alert alert-success">News Added Successfully</div>
						<?php
							}
							else if($msg == strtoupper(substr(md5('Unable to save News'), -4)))
							{
						?>
							<div class="alert alert-warning">Unable to save News</div>
						<?php
							}
							else if($msg == strtoupper(substr(md5('News Updated Successfully'), -4)))
							{
						?>
							<div class="alert alert-success">News Updated Successfully</div>
						<?php
							}
						}?>
						
					<br><br>
						
					<form method='post' action='../action'>
						<label><b>Topic:</b></label>
						<br>
						<input type='text' class='form-control input-lg' name='topic' id='topic' autocomplete='off'>
						<br>
						<label><b>Message:</b></label>
						<br>
						<textarea name='newsupdate_content' id='newsupdate_content' class='form-control input-lg' rows='4'></textarea>
						<br>
						<button class='btn btn-success' name='save_news_update' id='save_news_update'><i class='fa fa-save fa-2x'></i> Save Update</button>
					</form>
					<?php
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
