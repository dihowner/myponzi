<?php
require '../config.php';
if(isset($username))
{
	
	
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
    <title>Upload Attachment ::: Givers Cycler</title>
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
                        <a href="packages"><i class="fa fa-sitemap fa-3x"></i> Invest in Package</a>
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
                     <h2>Upload Proof of payment</h2>   
                        <h5>Hello <b><?php echo ucfirst($participant_name);?>, do not upload FAKE POP </b></h5>
                    </div>
					
                </div>              
                 <!-- /. ROW  -->
                  <hr />
                
				
                 <!-- /. ROW  -->
                
				<div class="row">
				
				
			<?php 
			if(isset($_GET["ghID"]))
			{
				//Lets See if attachment is available
				$ghID = $_GET["ghID"];
				$get_mergeID = $_GET["mergeID"];
				$getGH = $con->prepare("SELECT * FROM `merge_gh` where ghID='$ghID' and mergeID='$get_mergeID'");
				$getGH->execute();
				$getGH_row = rows("SELECT * FROM `merge_gh` where ghID='$ghID' and mergeID='$get_mergeID'");
				if($getGH_row == 0 )
				{
					redirect_to("dashboard");
				}
				
				else if($getGH_row >= 1) //If a participant has paid part of the GH, then we have the GH id in two folds
				{
					$getGHInfo = $getGH->fetch(PDO::FETCH_ASSOC);
					$amountGH = $getGHInfo['amountGH'];
					$mergeID = $getGHInfo["mergeID"];
					$gh_participantID = $getGHInfo["gh_participantID"];
					
					
					//Lets get receiver info
					$ghUser = $con->prepare("select * from participant where pid='$gh_participantID'");
					$ghUser->execute();
					$ghUserInfo = $ghUser->fetch(PDO::FETCH_ASSOC);
					$gh_mobile  = $ghUserInfo['mobile'];
					$senderid = 'GIVERCYCLER';
					$gh_message = str_replace(" ", "%20", "You have got a paid order. Pls login to confirm");
					$sender_api =  "http://developers.cloudsms.com.ng/api.php?userid=24779922&password=XR_PJxtL&type=0&destination=$gh_mobile&sender=$senderid&message=$gh_message";
							
					
					$phID = $getGHInfo["phID"];
					$attachment = $getGHInfo["attachment"];
					if(!empty($attachment) && $get_mergeID == $mergeID)
					{
						// Since you don upload, wetin u dey find again
						redirect_to("dashboard");
					}
					if(isset($_POST['upload_receipt']))
					{
						$filename = "POP".$_FILES['file']['name'];
						$file_tmp_name = $_FILES['file']['tmp_name'];
						$file_size = $_FILES['file']['size'];
						$folder = '../img/attachment/';
						$joinfile = $folder.$filename;
						// echo $file_size;
						if($file_size > 2000000)
						{
						?>
							
							<div class='alert alert-danger'><b>File is too large</b></div>
						<?php
						}
						else
						{
							
							$dateUpload = date('d.m.Y h:i A');
							$auto_confirm = date('d.m.Y h:i A', strtotime("+72 hours"));
							$movefile = move_uploaded_file($file_tmp_name, $joinfile);
							if($movefile == true)
							{
								$saveattachment = $con->prepare("update merge_gh set attachment='$filename', status='Upload', time_upload='$dateUpload', auto_confirm='$auto_confirm' where ghID='$ghID' and participantID='$pid' and mergeID='$get_mergeID'");
								if($saveattachment->execute())
								{
									
									file_get_contents($sender_api);
								?>
									<div class='alert alert-success'><b>Attachment successfully uploaded</b></div>
									<script>
										alert("Attachment uploaded, inform receiver to confirm your payment");
									</script>
								<?php
									// redirect_to("dashboard");
								}
								else
								{
								?>
									<div class='alert alert-danger'><b>Uploading failed</b></div>
								<?php
								}
							}
						}
					}
				?>
					<div class='col-md-12'>
						
						
							Dear <b><?php echo ucfirst($participant_name);?>,</b><br>
					<b>You consent that the payment of NGN<?php echo number_format(($amountGH),2);?> was successfully made<br/>
						<br/><font color='red'>NOTE:</font> Uploading of fake proof of payment will lead to permanent blocking of your account
					</b><br/>

					<form method="post" enctype="multipart/form-data" id="uploadForm">
						<input type="file" name="file" id="file" class='form-control input-lg' required/><br/>
						<font color="red" size='4px'>Maximum file upload is 2mb</font>
						<br/>
						<button name='upload_receipt' class='btn btn-success btn-lg'><b>Upload Receipt</b></button>
					</form>
					<br><br><br><br><br><br><br><br>
					<p style="text-align: center;"><span style="font-size: 24pt; color: #ff0000;"><strong>WARNING!!!</strong></span></p>
					<br>
					<p style="text-align: center;"><span style="font-size: 12pt; color: green">Don't even try to forge any payment screenshot using Photoshop, Paint or whatsoever softwares you wish to use in forging any payment screenshots.</span></p>
					<hr>
					<p style="text-align: center;"><span style="font-size: 12pt; color: green">Please be informed we are 100% compliant to make sure all payments are confirmed and real.
						
					<hr>
					<p style="text-align: center;"><span style="color: #ff0000;"><strong><span style="font-size: 12pt;">Be WARNED!!! </span></strong></span></p>
					<p style="text-align: center;"><span style="font-size: 12pt; color: green">Let's build a community that works togethers. Remember, Givers are Receivers</span></p>
					<br>

				<?Php
				}
			}
			else
			{
				redirect_to("dashboard");
			}
				?>
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
	  
    <script src="../js/jquery.min.js"></script>
<script>
function filePreview(input) {
    if (input.files && input.files[0]) {
        var reader = new FileReader();
        reader.onload = function (e) {
            $('#uploadForm + img').remove();
            $('#uploadForm').after('<img src="'+e.target.result+'" width="150px" height="100px"/>');
            //$('#uploadForm + embed').remove();
            //$('#uploadForm').after('<embed src="'+e.target.result+'" width="450" height="300">');
        }
        reader.readAsDataURL(input.files[0]);
    }
}

$("#file").change(function () {
    filePreview(this);
});
</script>
   
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
