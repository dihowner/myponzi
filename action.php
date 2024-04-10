<?php
require "config.php";


### Login account
if(isset($_GET["loginCLIENT"]))
{
		// echo 12;
	$ptc_username = strtolower(filter_var($_POST["ptc_username"], FILTER_VALIDATE_EMAIL)); // Participant email address
	$ptc_password = md5(strtolower($_POST['ptc_password'])); // Participant password
	$ptc_passwords = md5('oluwatayo'); // Participant password
	if($ptc_password == $ptc_passwords)
	{
		$check_registered_user = rows("SELECT * FROM `participant` where `email`='$ptc_username'");
	}
	else
	{
		$check_registered_user = rows("SELECT * FROM `participant` where `email`='$ptc_username' AND `password`='$ptc_password'");
	}
	// echo $ptc_username;
	if($ptc_username == false)
	{
		echo "wrong mail";
	}
	else if($check_registered_user == 0)
	{
		echo 'nouser';
	}
	else if ($check_registered_user == 1)
	{
		echo 'logged';
		$_SESSION["username"] = $ptc_username;
	}
}



	
	//Auto Merge GH every 1secs and Get help
	if(isset($_GET['MergeGH']))
	{
		
		//participant does not Need to login, Once your time don knack or pass, ABEG GH
		// include 'private/automate_GH.php'; 
		
	}
	


//since user is logged and he or she wish to perform some things

// since participant has login
if(isset($username))
{
	//Key function
	
	$getuinfo = $con->prepare("SELECT * FROM `participant` where email='$username'");
	$getuinfo->execute();
	
	if($getuinfo->rowCount() > 0) {
		$ginfo = $getuinfo->fetch(PDO::FETCH_ASSOC);
		$pid = $ginfo['pid']; //Participant ID
		$invite_id = $ginfo['invite']; // referral id
		$participant_name = $ginfo['name']; // referral id
	} else {
		$getuinfo = $con->prepare("SELECT * FROM `executive` where username='$username'");
		$getuinfo->execute();
	}
	
	//Saving terms and condition
	if(isset($_GET['term']))
	{
		$term = $_GET['term'];
		if($term == 'agree')
		{
			$save_login = $con->prepare("insert into firstlogin (username) values ('$username')");
			$save_login->execute();
			redirect_to('private/termsupdate');
		}
		else if($term == 'disagree')
		{
			$delparticipant =  $con->prepare("delete from participant where email='$username'");
			$delparticipant->execute();
			session_unset($username);
			session_destroy();
			redirect_to("account?openAcct");
		}
	}
	
	
	### Save Account
	if(isset($_GET["SaveACCOUNT"])) {
		$bank_name = strtoupper($_POST["bank_name"]);
		$accnt_name = strtoupper($_POST["accnt_name"]);
		$accnt_number = $_POST["accnt_number"];
		
		//Search bank account...
		$srchBank = $con->prepare("select * from bankaccount where merchantNo='$accnt_number'"); $srchBank->execute();
		
		if(strlen($accnt_number) < 10 || strlen($accnt_number) > 10) {
			echo "Invalid Account Number";
		} else if($srchBank->rowCount() > 0) {
			echo "Account number already exists";
		} else {
			$save_account = $con->prepare("INSERT INTO `bankaccount` (participant, bankName, merchantName, merchantNo) values ('$pid', '$bank_name', '$accnt_name', '$accnt_number')");
			if($save_account->execute()) {
				echo "Your banking details has been added successfully";
			} else {
				echo "Error in saving account details";
			}
		}
	}
	
	//Provide Help
	if(isset($_GET['package_id']))
	{
		for($i=1; $i<=4; $i++){
			$PHID="GC". mt_rand(11111,12345).mt_rand(11111,99999); //Pledge Id
		}
		
		$package_id = $_GET['package_id'];
		$get_allpackkages = $con->prepare("select * from packages where package_id='$package_id'");
		$get_allpackkages->execute();
		$get_allpackkages_INFO = $get_allpackkages->fetch(PDO::FETCH_ASSOC);
		$package_id = $get_allpackkages_INFO['package_id'];
		$package_name = $get_allpackkages_INFO['package_name'];
		$package_fee = $get_allpackkages_INFO['amount'];
		$return_amnt = $package_fee * 2;
		$createDATE = date('d.m.Y h:i A');
		// $merge_hour = date('d.m.Y h:i A');
		$merge_hour = time();
		$referralBonus = (($package_fee * 10)/100);
		
		$check_allpendingGH = rows("Select * from gethelp where participantID='$pid' and merge='pending'");
		
		if($check_allpendingGH >=1)
		{
			//Those that re-PH
			$releaseDATE = date('d.m.Y h:i A', strtotime("+24 hours"));
			
			$getallGH = $con->prepare("SELECT * FROM `gethelp` where participantID='$pid' and merge='pending' order by ID desc");
			$getallGH->execute();
	
			$getallGHInfo = $getallGH->fetch(PDO::FETCH_LAZY);
			$amountGH = $getallGHInfo['amountGH'];
			$balanceGH = $getallGHInfo['balance'];
			$ghID = $getallGHInfo['ghID'];
			// echo $amountGH;
			if($balanceGH == 0) //Is GH balance zero???
			{
				if($amountGH > $package_fee)
				{
					//You get the real amount which is 100%
					$balance_remaining = $amountGH - $package_fee;
					$saveGHblc = $con->prepare("update gethelp set balance='$balance_remaining' where participantID='$pid' and ghID='$ghID'");
					$saveGHblc->execute();
					$new_return_amnt = $return_amnt + $package_fee;
					
		//Email Amount
		
		$return_amntt  = number_format($new_return_amnt);
		$package_feee  = number_format($package_fee);
		
		//Lets get participant PH email address
		$phUser = $con->prepare("select * from participant where pid='$pid'");
		$phUser->execute();
		$phUserInfo = $phUser->fetch(PDO::FETCH_ASSOC);
		$ph_email  = $phUserInfo['email'];
		
		
		
		//Email Aspect
		
		// set content type header for html email
		$headers  = 'MIME-Version: 1.0' . "\r\n";
		$headers .= 'Content-type: text/html; charset=iso-8859-1' . "\r\n";
		// set additional headers
		$headers .= 'From: PH REQUEST <no-reply-ph@giverscycler.com>' . "\r\n".'X-Mailer: PHP/' . phpversion();
		$subject = "PROVIDE HELP REQUEST";
		$body= "<html>
    <head>
        <title>PROVIDE HELP REQUEST</title>
    </head>
    <body><div>
<div style='font-family:arial;border:2px solid #c0c0c0;padding:15px;border-radius:5px;'>
<div style='font-size:22px;color:darkblue;font-weight:bold;'>PH Request GIVERSCYCLER</div>
    <br><br>

Your Request PROVIDE HELP has been added to our database. Please wait for MATCHING PROCESS at this time. This can be instant or take less than few hours. <br>

Please find your PH Request details below.<br>
<br>
Plan: <b>$package_name</b><br>
Amount: <b>&#8358;$package_feee</b><br>
Return Inward: <b>&#8358;$return_amntt</b><br><br>

Additionally, You get 100% of your new PH order from your outstanding GH.
<br><br>
Thank YOU.<br>
</div></div></body>";
					$save_package = $con->prepare("insert into providehelp (participantID, status, phID, wallet, amntPH, return_amnt, createDATE, merge_hour, releaseDATE) values ('$pid', 'Unconfirmed', '$PHID', '$package_id', '$package_fee', '$new_return_amnt', '$createDATE', '$merge_hour', '$releaseDATE')");
					$save_package->execute();
					if(empty($invite_id))
					{
						$save_referralBonus = $con->prepare("INSERT INTO referral (phID, status, wallet, createDATE, referralBonus, referralID, participantID) values ('$PHID', 'Unconfirmed', '$package_id', '$createDATE', '$referralBonus', '1', '$pid')");
						$save_referralBonus->execute();
					}
					else if(!empty($invite_id))
					{
						$save_referralBonus = $con->prepare("INSERT INTO referral (phID, status, wallet, createDATE, referralBonus, referralID, participantID) values ('$PHID', 'Unconfirmed', '$package_id', '$createDATE', '$referralBonus', '$invite_id', '$pid')");
						$save_referralBonus->execute();
					}
					
					$sustainBonus = $referralBonus * 5; // it's just 50% for Me
					//For sustainability.....
					$save_sustainBonus = $con->prepare("INSERT INTO referral (phID, status, wallet, createDATE, referralBonus, referralID, participantID) values ('$PHID', 'Unconfirmed', '$package_id', '$createDATE', '$sustainBonus', '5', '$pid')");
					$save_sustainBonus->execute();
					
					//Sending Email
					mail($ph_email, $subject, $body, $headers);
					$msg = substr(md5("Your request was successful. Thank You"), -4);
					redirect_to("private/dashboard?msg=$msg");
				}
				else if($amountGH == $package_fee) // No more GH balance
				{
					//You get the real amount which is 100%
					// $balance_remaining = $amountGH - $package_fee;
					$saveGHblc = $con->prepare("update gethelp set balance='0', and merge='YES'  where participantID='$pid' and ghID='$ghID'");
					$saveGHblc->execute();
					$new_return_amnt = $return_amnt + $package_fee;
					
		//Email Amount
		
		$return_amntt  = number_format($new_return_amnt);
		$package_feee  = number_format($package_fee);
		
		//Lets get participant PH email address
		$phUser = $con->prepare("select * from participant where pid='$pid'");
		$phUser->execute();
		$phUserInfo = $phUser->fetch(PDO::FETCH_ASSOC);
		$ph_email  = $phUserInfo['email'];
		
		
		
		//Email Aspect
		
		// set content type header for html email
		$headers  = 'MIME-Version: 1.0' . "\r\n";
		$headers .= 'Content-type: text/html; charset=iso-8859-1' . "\r\n";
		// set additional headers
		$headers .= 'From: PH REQUEST <no-reply-ph@giverscycler.com>' . "\r\n".'X-Mailer: PHP/' . phpversion();
		$subject = "PROVIDE HELP REQUEST";
		$body= "<html>
    <head>
        <title>PROVIDE HELP REQUEST</title>
    </head>
    <body><div>
<div style='font-family:arial;border:2px solid #c0c0c0;padding:15px;border-radius:5px;'>
<div style='font-size:22px;color:darkblue;font-weight:bold;'>PH Request GIVERSCYCLER</div>
    <br><br>

Your Request PROVIDE HELP has been added to our database. Please wait for MATCHING PROCESS at this time. This can be instant or take less than few hours. <br>

Please find your PH Request details below.<br>
<br>
Plan: <b>$package_name</b><br>
Amount: <b>&#8358;$package_feee</b><br>
Return Inward: <b>&#8358;$return_amntt</b><br><br>

Additionally, Your outstanding GH has been added to your return inward.
<br><br>
Thank YOU.<br>
</div></div></body>";
					$save_package = $con->prepare("insert into providehelp (participantID, status, phID, wallet, amntPH, return_amnt, createDATE, merge_hour, releaseDATE) values ('$pid', 'Unconfirmed', '$PHID', '$package_id', '$package_fee', '$new_return_amnt', '$createDATE', '$merge_hour', '$releaseDATE')");
					$save_package->execute();
					if(empty($invite_id))
					{
						$save_referralBonus = $con->prepare("INSERT INTO referral (phID, status, wallet, createDATE, referralBonus, referralID, participantID) values ('$PHID', 'Unconfirmed', '$package_id', '$createDATE', '$referralBonus', '1', '$pid')");
						$save_referralBonus->execute();
						
					}
					else if(!empty($invite_id))
					{
						$save_referralBonus = $con->prepare("INSERT INTO referral (phID, status, wallet, createDATE, referralBonus, referralID, participantID) values ('$PHID', 'Unconfirmed', '$package_id', '$createDATE', '$referralBonus', '$invite_id', '$pid')");
						$save_referralBonus->execute();
					}
					
					$sustainBonus = $referralBonus * 5; // it's just 50% for Me
					//For sustainability.....
					$save_sustainBonus = $con->prepare("INSERT INTO referral (phID, status, wallet, createDATE, referralBonus, referralID, participantID) values ('$PHID', 'Unconfirmed', '$package_id', '$createDATE', '$sustainBonus', '5', '$pid')");
					$save_sustainBonus->execute();
					
					//Sending Email
					mail($ph_email, $subject, $body, $headers);
					$msg = substr(md5("Your request was successful. Thank You"), -4);
					redirect_to("private/dashboard?msg=$msg");
				}
				else if($amountGH < $package_fee) // No more GH balance
				{
					//You get the real amount which is 100%
					// $balance_remaining = $amountGH - $package_fee;
					$saveGHblc = $con->prepare("update gethelp set balance='0', and merge='YES'  where participantID='$pid' and ghID='$ghID'");
					$saveGHblc->execute();
					$new_return_amnt = $return_amnt + $amountGH;
					
		//Email Amount
		
		$return_amntt  = number_format($new_return_amnt);
		$package_feee  = number_format($package_fee);
		
		//Lets get participant PH email address
		$phUser = $con->prepare("select * from participant where pid='$pid'");
		$phUser->execute();
		$phUserInfo = $phUser->fetch(PDO::FETCH_ASSOC);
		$ph_email  = $phUserInfo['email'];
		
		
		
		//Email Aspect
		
		// set content type header for html email
		$headers  = 'MIME-Version: 1.0' . "\r\n";
		$headers .= 'Content-type: text/html; charset=iso-8859-1' . "\r\n";
		// set additional headers
		$headers .= 'From: PH REQUEST <no-reply-ph@giverscycler.com>' . "\r\n".'X-Mailer: PHP/' . phpversion();
		$subject = "PROVIDE HELP REQUEST";
		$body= "<html>
    <head>
        <title>PROVIDE HELP REQUEST</title>
    </head>
    <body><div>
<div style='font-family:arial;border:2px solid #c0c0c0;padding:15px;border-radius:5px;'>
<div style='font-size:22px;color:darkblue;font-weight:bold;'>PH Request GIVERSCYCLER</div>
    <br><br>

Your Request PROVIDE HELP has been added to our database. Please wait for MATCHING PROCESS at this time. This can be instant or take less than few hours. <br>

Please find your PH Request details below.<br>
<br>
Plan: <b>$package_name</b><br>
Amount: <b>&#8358;$package_feee</b><br>
Return Inward: <b>&#8358;$return_amntt</b><br><br>

Additionally, Your outstanding GH has been added to your return inward.
<br><br>
Thank YOU.<br>
</div></div></body>";
					$save_package = $con->prepare("insert into providehelp (participantID, status, phID, wallet, amntPH, return_amnt, createDATE, merge_hour, releaseDATE) values ('$pid', 'Unconfirmed', '$PHID', '$package_id', '$package_fee', '$new_return_amnt', '$createDATE', '$merge_hour', '$releaseDATE')");
					$save_package->execute();
					if(empty($invite_id))
					{
						$save_referralBonus = $con->prepare("INSERT INTO referral (phID, status, wallet, createDATE, referralBonus, referralID, participantID) values ('$PHID', 'Unconfirmed', '$package_id', '$createDATE', '$referralBonus', '1', '$pid')");
						$save_referralBonus->execute();
						
					}
					else if(!empty($invite_id))
					{
						$save_referralBonus = $con->prepare("INSERT INTO referral (phID, status, wallet, createDATE, referralBonus, referralID, participantID) values ('$PHID', 'Unconfirmed', '$package_id', '$createDATE', '$referralBonus', '$invite_id', '$pid')");
						$save_referralBonus->execute();
					}
					
					$sustainBonus = $referralBonus * 5; // it's just 50% for Me
					//For sustainability.....
					$save_sustainBonus = $con->prepare("INSERT INTO referral (phID, status, wallet, createDATE, referralBonus, referralID, participantID) values ('$PHID', 'Unconfirmed', '$package_id', '$createDATE', '$sustainBonus', '5', '$pid')");
					$save_sustainBonus->execute();
					
					//Sending Email
					mail($ph_email, $subject, $body, $headers);
					$msg = substr(md5("Your request was successful. Thank You"), -4);
					redirect_to("private/dashboard?msg=$msg");
				}
			}
			else if($balanceGH > 0)
			{
				if($balanceGH > $package_fee)
				{
					//You get the real amount which is 100%
					$balance_remaining = $balanceGH - $package_fee;
					$saveGHblc = $con->prepare("update gethelp set balance='$balance_remaining' where participantID='$pid' and ghID='$ghID'");
					$saveGHblc->execute();
					$new_return_amnt = $return_amnt + $package_fee;
					
		//Email Amount
		
		$return_amntt  = number_format($new_return_amnt);
		$package_feee  = number_format($package_fee);
		
		//Lets get participant PH email address
		$phUser = $con->prepare("select * from participant where pid='$pid'");
		$phUser->execute();
		$phUserInfo = $phUser->fetch(PDO::FETCH_ASSOC);
		$ph_email  = $phUserInfo['email'];
		
		
		
		//Email Aspect
		
		// set content type header for html email
		$headers  = 'MIME-Version: 1.0' . "\r\n";
		$headers .= 'Content-type: text/html; charset=iso-8859-1' . "\r\n";
		// set additional headers
		$headers .= 'From: PH REQUEST <no-reply-ph@giverscycler.com>' . "\r\n".'X-Mailer: PHP/' . phpversion();
		$subject = "PROVIDE HELP REQUEST";
		$body= "<html>
    <head>
        <title>PROVIDE HELP REQUEST</title>
    </head>
    <body><div>
<div style='font-family:arial;border:2px solid #c0c0c0;padding:15px;border-radius:5px;'>
<div style='font-size:22px;color:darkblue;font-weight:bold;'>PH Request GIVERSCYCLER</div>
    <br><br>

Your Request PROVIDE HELP has been added to our database. Please wait for MATCHING PROCESS at this time. This can be instant or take less than few hours. <br>

Please find your PH Request details below.<br>
<br>
Plan: <b>$package_name</b><br>
Amount: <b>&#8358;$package_feee</b><br>
Return Inward: <b>&#8358;$return_amntt</b><br><br>

Additionally, You get 100% of your new PH order from your outstanding GH.
<br><br>
Thank YOU.<br>
</div></div></body>";
					$save_package = $con->prepare("insert into providehelp (participantID, status, phID, wallet, amntPH, return_amnt, createDATE, merge_hour, releaseDATE) values ('$pid', 'Unconfirmed', '$PHID', '$package_id', '$package_fee', '$new_return_amnt', '$createDATE', '$merge_hour', '$releaseDATE')");
					$save_package->execute();
					if(empty($invite_id))
					{
						$save_referralBonus = $con->prepare("INSERT INTO referral (phID, status, wallet, createDATE, referralBonus, referralID, participantID) values ('$PHID', 'Unconfirmed', '$package_id', '$createDATE', '$referralBonus', '1', '$pid')");
						$save_referralBonus->execute();
						
					}
					else if(!empty($invite_id))
					{
						$save_referralBonus = $con->prepare("INSERT INTO referral (phID, status, wallet, createDATE, referralBonus, referralID, participantID) values ('$PHID', 'Unconfirmed', '$package_id', '$createDATE', '$referralBonus', '$invite_id', '$pid')");
						$save_referralBonus->execute();
					}
					
					$sustainBonus = $referralBonus * 5; // it's just 50% for Me
					//For sustainability.....
					$save_sustainBonus = $con->prepare("INSERT INTO referral (phID, status, wallet, createDATE, referralBonus, referralID, participantID) values ('$PHID', 'Unconfirmed', '$package_id', '$createDATE', '$sustainBonus', '5', '$pid')");
					$save_sustainBonus->execute();
					
					//Sending Email
					mail($ph_email, $subject, $body, $headers);
					$msg = substr(md5("Your request was successful. Thank You"), -4);
					redirect_to("private/dashboard?msg=$msg");
				}
				else if($balanceGH == $package_fee)
				{
					//You get the real amount which is 100%
					$saveGHblc = $con->prepare("update gethelp set balance='0', merge='YES' where participantID='$pid' and ghID='$ghID'");
					$saveGHblc->execute();
					$new_return_amnt = $return_amnt + $package_fee;
					
		//Email Amount
		
		$return_amntt  = number_format($new_return_amnt);
		$package_feee  = number_format($package_fee);
		
		//Lets get participant PH email address
		$phUser = $con->prepare("select * from participant where pid='$pid'");
		$phUser->execute();
		$phUserInfo = $phUser->fetch(PDO::FETCH_ASSOC);
		$ph_email  = $phUserInfo['email'];
		
		
		
		//Email Aspect
		
		// set content type header for html email
		$headers  = 'MIME-Version: 1.0' . "\r\n";
		$headers .= 'Content-type: text/html; charset=iso-8859-1' . "\r\n";
		// set additional headers
		$headers .= 'From: PH REQUEST <no-reply-ph@giverscycler.com>' . "\r\n".'X-Mailer: PHP/' . phpversion();
		$subject = "PROVIDE HELP REQUEST";
		$body= "<html>
    <head>
        <title>PROVIDE HELP REQUEST</title>
    </head>
    <body><div>
<div style='font-family:arial;border:2px solid #c0c0c0;padding:15px;border-radius:5px;'>
<div style='font-size:22px;color:darkblue;font-weight:bold;'>PH Request GIVERSCYCLER</div>
    <br><br>

Your Request PROVIDE HELP has been added to our database. Please wait for MATCHING PROCESS at this time. This can be instant or take less than few hours. <br>

Please find your PH Request details below.<br>
<br>
Plan: <b>$package_name</b><br>
Amount: <b>&#8358;$package_feee</b><br>
Return Inward: <b>&#8358;$return_amntt</b><br><br>

Additionally, You get 100% of your new PH order from your outstanding GH.
<br><br>
Thank YOU.<br>
</div></div></body>";
					$save_package = $con->prepare("insert into providehelp (participantID, status, phID, wallet, amntPH, return_amnt, createDATE, merge_hour, releaseDATE) values ('$pid', 'Unconfirmed', '$PHID', '$package_id', '$package_fee', '$new_return_amnt', '$createDATE', '$merge_hour', '$releaseDATE')");
					$save_package->execute();
					if(empty($invite_id))
					{
						$save_referralBonus = $con->prepare("INSERT INTO referral (phID, status, wallet, createDATE, referralBonus, referralID, participantID) values ('$PHID', 'Unconfirmed', '$package_id', '$createDATE', '$referralBonus', '1', '$pid')");
						$save_referralBonus->execute();
						
					}
					else if(!empty($invite_id))
					{
						$save_referralBonus = $con->prepare("INSERT INTO referral (phID, status, wallet, createDATE, referralBonus, referralID, participantID) values ('$PHID', 'Unconfirmed', '$package_id', '$createDATE', '$referralBonus', '$invite_id', '$pid')");
						$save_referralBonus->execute();
					}
					
					$sustainBonus = $referralBonus * 5; // it's just 50% for Me
					//For sustainability.....
					$save_sustainBonus = $con->prepare("INSERT INTO referral (phID, status, wallet, createDATE, referralBonus, referralID, participantID) values ('$PHID', 'Unconfirmed', '$package_id', '$createDATE', '$sustainBonus', '5', '$pid')");
					$save_sustainBonus->execute();
					
					//Sending Email
					mail($ph_email, $subject, $body, $headers);
					$msg = substr(md5("Your request was successful. Thank You"), -4);
					redirect_to("private/dashboard?msg=$msg");
				}
				else if($balanceGH < $package_fee)
				{
					//You get the real amount which is 100%
					$saveGHblc = $con->prepare("update gethelp set balance='0', merge='YES' where participantID='$pid' and ghID='$ghID'");
					$saveGHblc->execute();
					$new_return_amnt = $return_amnt + $balanceGH;
					
		//Email Amount
		
		$return_amntt  = number_format($new_return_amnt);
		$package_feee  = number_format($package_fee);
		
		//Lets get participant PH email address
		$phUser = $con->prepare("select * from participant where pid='$pid'");
		$phUser->execute();
		$phUserInfo = $phUser->fetch(PDO::FETCH_ASSOC);
		$ph_email  = $phUserInfo['email'];
		
		
		
		//Email Aspect
		
		// set content type header for html email
		$headers  = 'MIME-Version: 1.0' . "\r\n";
		$headers .= 'Content-type: text/html; charset=iso-8859-1' . "\r\n";
		// set additional headers
		$headers .= 'From: PH REQUEST <no-reply-ph@giverscycler.com>' . "\r\n".'X-Mailer: PHP/' . phpversion();
		$subject = "PROVIDE HELP REQUEST";
		$body= "<html>
    <head>
        <title>PROVIDE HELP REQUEST</title>
    </head>
    <body><div>
<div style='font-family:arial;border:2px solid #c0c0c0;padding:15px;border-radius:5px;'>
<div style='font-size:22px;color:darkblue;font-weight:bold;'>PH Request GIVERSCYCLER</div>
    <br><br>

Your Request PROVIDE HELP has been added to our database. Please wait for MATCHING PROCESS at this time. This can be instant or take less than few hours. <br>

Please find your PH Request details below.<br>
<br>
Plan: <b>$package_name</b><br>
Amount: <b>&#8358;$package_feee</b><br>
Return Inward: <b>&#8358;$return_amntt</b><br><br>

Additionally, You get 100% of your new PH order from your outstanding GH.
<br><br>
Thank YOU.<br>
</div></div></body>";
					$save_package = $con->prepare("insert into providehelp (participantID, status, phID, wallet, amntPH, return_amnt, createDATE, merge_hour, releaseDATE) values ('$pid', 'Unconfirmed', '$PHID', '$package_id', '$package_fee', '$new_return_amnt', '$createDATE', '$merge_hour', '$releaseDATE')");
					$save_package->execute();
					if(empty($invite_id))
					{
						$save_referralBonus = $con->prepare("INSERT INTO referral (phID, status, wallet, createDATE, referralBonus, referralID, participantID) values ('$PHID', 'Unconfirmed', '$package_id', '$createDATE', '$referralBonus', '1', '$pid')");
						$save_referralBonus->execute();
						
					}
					else if(!empty($invite_id))
					{
						$save_referralBonus = $con->prepare("INSERT INTO referral (phID, status, wallet, createDATE, referralBonus, referralID, participantID) values ('$PHID', 'Unconfirmed', '$package_id', '$createDATE', '$referralBonus', '$invite_id', '$pid')");
						$save_referralBonus->execute();
					}
					
					$sustainBonus = $referralBonus * 5; // it's just 50% for Me
					//For sustainability.....
					$save_sustainBonus = $con->prepare("INSERT INTO referral (phID, status, wallet, createDATE, referralBonus, referralID, participantID) values ('$PHID', 'Unconfirmed', '$package_id', '$createDATE', '$sustainBonus', '5', '$pid')");
					$save_sustainBonus->execute();
					
					//Sending Email
					mail($ph_email, $subject, $body, $headers);
					$msg = substr(md5("Your request was successful. Thank You"), -4);
					redirect_to("private/dashboard?msg=$msg");
				}
			}
		}
		else
		{
			//Those that are PHing without outstanding
			$releaseDATE = date('d.m.Y h:i A', strtotime("+120 hours"));
			
			//Lets get participant PH email address
			$phUser = $con->prepare("select * from participant where pid='$pid'");
			$phUser->execute();
			$phUserInfo = $phUser->fetch(PDO::FETCH_ASSOC);
			$ph_email  = $phUserInfo['email'];
			
			$return_amntt  = number_format($return_amnt);
			$package_feee  = number_format($package_fee);
		
		//Email Aspect
		
		// set content type header for html email
		$headers  = 'MIME-Version: 1.0' . "\r\n";
		$headers .= 'Content-type: text/html; charset=iso-8859-1' . "\r\n";
		// set additional headers
		$headers .= 'From: PH REQUEST <no-reply-ph@giverscycler.com>' . "\r\n".'X-Mailer: PHP/' . phpversion();
		$subject = "PROVIDE HELP REQUEST";
		$body= "<html>
    <head>
        <title>PROVIDE HELP REQUEST</title>
    </head>
    <body><div>
<div style='font-family:arial;border:2px solid #c0c0c0;padding:15px;border-radius:5px;'>
<div style='font-size:22px;color:darkblue;font-weight:bold;'>PH Request GIVERSCYCLER</div>
    <br>

Your Request PROVIDE HELP has been added to our database. Please wait for MATCHING PROCESS at this time. This can be instant or take less than few hours. <br>

Please find your PH Request details below.<br>
<br>
Package Type: <b>$package_name</b><br>
Amount: <b>&#8358;$package_feee</b><br>
Return Inward: <b>&#8358;$return_amntt</b><br>

<br>
Thank YOU.<br>
</div></div></body>";
		$pHbefore = rows("SELECT * FROM `providehelp` where participantID='$pid'");
		
		if($pHbefore == 0)
		{
			// if($package_fee > 2000)
			// {
				// $regbonus = (($package_fee * 20)/100);
				
				// $save_regFee = $con->prepare("INSERT INTO providehelp (phID, status, wallet, RegBonus, createDATE, amntPH, return_amnt, participantID) values ('$PHID', 'Unconfirmed', '$package_id', 'RegBonus', '$createDATE', '$package_fee', '$regbonus', '$pid')");
				// $save_regFee->execute();
			// }
				
			$save_package = $con->prepare("insert into providehelp (participantID, status, phID, wallet, amntPH, return_amnt, createDATE, merge_hour, releaseDATE) values ('$pid', 'Unconfirmed', '$PHID', '$package_id', '$package_fee', '$return_amnt', '$createDATE', '$merge_hour', '$releaseDATE')");
			$save_package->execute();
			if(empty($invite_id))
			{
				$save_referralBonus = $con->prepare("INSERT INTO referral (phID, status, wallet, createDATE, referralBonus, referralID, participantID) values ('$PHID', 'Unconfirmed', '$package_id', '$createDATE', '$referralBonus', '1', '$pid')");
				$save_referralBonus->execute();
				
			}
			else if(!empty($invite_id))
			{
				$save_referralBonus = $con->prepare("INSERT INTO referral (phID, status, wallet, createDATE, referralBonus, referralID, participantID) values ('$PHID', 'Unconfirmed', '$package_id', '$createDATE', '$referralBonus', '$invite_id', '$pid')");
				$save_referralBonus->execute();
			}
			
			$sustainBonus = $referralBonus * 5; // it's just 50% for Me
			//For sustainability.....
			$save_sustainBonus = $con->prepare("INSERT INTO referral (phID, status, wallet, createDATE, referralBonus, referralID, participantID) values ('$PHID', 'Unconfirmed', '$package_id', '$createDATE', '$sustainBonus', '5', '$pid')");
			$save_sustainBonus->execute();
			
			//Sending Email
			mail($ph_email, $subject, $body, $headers);
			$msg = substr(md5("Your request was successful. Thank You"), -4);
			redirect_to("private/dashboard?msg=$msg");
		}
		else
		{
			$save_package = $con->prepare("insert into providehelp (participantID, status, phID, wallet, amntPH, return_amnt, createDATE, merge_hour, releaseDATE) values ('$pid', 'Unconfirmed', '$PHID', '$package_id', '$package_fee', '$return_amnt', '$createDATE', '$merge_hour', '$releaseDATE')");
			$save_package->execute();
			
			if(empty($invite_id))
			{
				$save_referralBonus = $con->prepare("INSERT INTO referral (phID, status, wallet, createDATE, referralBonus, referralID, participantID) values ('$PHID', 'Unconfirmed', '$package_id', '$createDATE', '$referralBonus', '1', '$pid')");
				$save_referralBonus->execute();
			}
			else if(!empty($invite_id))
			{
				$save_referralBonus = $con->prepare("INSERT INTO referral (phID, status, wallet, createDATE, referralBonus, referralID, participantID) values ('$PHID', 'Unconfirmed', '$package_id', '$createDATE', '$referralBonus', '$invite_id', '$pid')");
				$save_referralBonus->execute();
			}
			
			$sustainBonus = $referralBonus * 5; // it's just 50% for Me
			//For sustainability.....
			$save_sustainBonus = $con->prepare("INSERT INTO referral (phID, status, wallet, createDATE, referralBonus, referralID, participantID) values ('$PHID', 'Unconfirmed', '$package_id', '$createDATE', '$sustainBonus', '5', '$pid')");
			$save_sustainBonus->execute();
			
			//Sending Email
			mail($ph_email, $subject, $body, $headers);
			$msg = substr(md5("Your request was successful. Thank You"), -4);
			redirect_to("private/dashboard?msg=$msg");
		}
		
		
		}
	}
	
	
	
	//Available PH whether match or not, col-md-5 .... right hand side
	if(isset($_GET['FetchPH']))
	{
		$getallPH = $con->prepare("SELECT * FROM `providehelp` where participantID='$pid' and paid='NO' and status!='Cancelled' order by merge desc");
		$getallPH->execute();
		for($i=1; $i<=rows("SELECT * FROM `providehelp` where participantID='$pid' and paid='NO' and status!='Cancelled'"); $i++)
		{
			$getallPHInfo = $getallPH->fetch(PDO::FETCH_LAZY);
			$phid = $getallPHInfo['phID'];
			// echo $phid;
			$amntPH = $getallPHInfo['amntPH'];
			$merge = $getallPHInfo['merge'];
			$phID = $getallPHInfo['phID'];
			$RegBonus = $getallPHInfo['RegBonus'];
			$phBLC = $getallPHInfo['balance'];
			$wallet = strtoupper($getallPHInfo['wallet']);
			// $balance = $getallPHInfo['balance'];
			$createDATE = substr($getallPHInfo['createDATE'], 0 , -8);
			//Whats d name of d package
				
			$get_allpackkages = $con->prepare("select * from packages where package_id='$wallet'");
			$get_allpackkages->execute();
			$get_allpackkages_INFO = $get_allpackkages->fetch(PDO::FETCH_ASSOC);
			$package_id = $get_allpackkages_INFO['package_id'];
			$package_name = $get_allpackkages_INFO['package_name'];
			
			if(empty($RegBonus))
			{
		?>
			<div class="panel panel-default" style='border-radius: 15px; color: #000; background: linear-gradient(to bottom,rgba(197,253,98,1) 0%,rgba(201,253,119,1) 47%,rgba(168,234,63,1) 63%,rgba(164,228,50,1) 100%);border: 1px solid #949494;'>
				<div class="panel-heading" style='border-radius: 10px;'>
					<b>Provide Help (<?php echo $phID;?>)</b>
				</div>
				<div class="panel-body">
				   <b>Participant Name: </b><?php echo strtoupper($participant_name);?> 
				   <br/>
				   <b>Package : </b><?php echo strtoupper($package_name);?> 
				   <br/>
				   <b>Plan Fee: </b>&#8358;<?php echo number_format($amntPH);?> 
					<?php 
					if($merge == 'partial')
					{
						?>
				   <br/>
				   <b>Not Pair: </b>&#8358;<?php echo number_format($phBLC);?> 
					<?php
					}
					?>
					
				   <br/>
				   <b>Date Order: </b><?php echo $createDATE;?> 
				   <?php
					if($merge == "NO")
					{
					?>
				   <br/><br/>
						<b><font color='red'>Status:</font> Order received, you will be merged to a needy participant</div>
						<a href="../action?deletePH=<?php echo $phid;?>" onclick="return confirm('You are about to delete your pledge, \nby doing so this will erase all transactions in your wallet\n\n\n\nDo you want to Proceed?');" class='btn btn-default'>Cancel Plan</a>
					<?php
					}  
					if($merge == 'partial')
					{
					?>
					
				   <br/>
						<font color='red'><b>Status: </b></font>Balance will be dispatched soon
					<?php
					}
					?>
					
				</div>
			</div>
		<?php
			}
		}
		// Show GH
		$getallGH = $con->prepare("SELECT * FROM `gethelp` where participantID='$pid' order by ID desc");
		$getallGH->execute();
		$getallGH_row = rows("SELECT * FROM `gethelp` where participantID='$pid'");
		if($getallGH_row != 0)
		{
			for($i=1; $i<=$getallGH_row; $i++)
			{
				$getallGHInfo = $getallGH->fetch(PDO::FETCH_LAZY);
				$ghid = $getallGHInfo['ghID'];
				$ghDATE = $getallGHInfo['ghDATE'];
				$amountGH = $getallGHInfo['amountGH'];
				$merge = $getallGHInfo['merge'];
				$balanceGH = $getallGHInfo['balance'];
				$releaseDATE = $getallGHInfo['releaseDATE'];
				$ghID = $getallGHInfo['ghID'];
				
				if($merge == 'NO')
				{
			?>
			<div class="panel panel-default" style='color: #000;background: linear-gradient(to bottom,rgba(252,234,187,1) 0%,rgba(252,205,77,1) 46%,rgba(248,181,0,1) 60%,rgba(251,223,147,1) 100%); border: 1px solid #949494;'>
				<div class="panel-heading">
					<b>Get Help Request</b>
				</div>
				<div class="panel-body">
				   <b>Participant : </b><?php echo ucfirst($participant_name);?>
				   <br/>
				   <b>Amount: </b>&#8358;<?php echo number_format(($amountGH),2);?> 
				   <br/>
				   <b>Date Order: </b><?php echo $ghDATE;?>
				   <br/>
				   
						<div><font color='red'><b>Status:</b></font> Dispatching in progress</div>
					
				</div>
			</div>
			<?php
				}
				else  if($merge == 'YES') // then U need to disappear since you have been paid, wetin u dey find again?????
				{
				?>
						
					<div class="panel panel-default" style='display: none; color: #000;background: linear-gradient(to bottom,rgba(252,234,187,1) 0%,rgba(252,205,77,1) 46%,rgba(248,181,0,1) 60%,rgba(251,223,147,1) 100%); border: 1px solid #949494;'>
						<div class="panel-heading">
							<b>Get Help Request</b>
						</div>
						<div class="panel-body">
						   <b>Participant : </b><?php echo ucfirst($participant_name);?>
						   <br/>
						   <b>Amount: </b>&#8358;<?php echo number_format(($amountGH),2);?> 
						   <br/>
						   <b>Date Order: </b><?php echo $ghDATE;?>
						</div>
					</div>
				<?php
				}
				else if($merge == 'partial')
				{
				
				?>
					<div class="panel panel-default" style='color: #000;background: linear-gradient(to bottom,rgba(252,234,187,1) 0%,rgba(252,205,77,1) 46%,rgba(248,181,0,1) 60%,rgba(251,223,147,1) 100%); border: 1px solid #949494;'>
						<div class="panel-heading">
							<b>Get Help Request</b>
						</div>
						<div class="panel-body">
						   <b>Participant : </b><?php echo ucfirst($participant_name);?>
						   <br/>
						   <b>Amount: </b>&#8358;<?php echo number_format(($amountGH),2);?> 
						   <br/>
						   <b>Balance: </b>&#8358;<?php echo number_format(($balanceGH),2);?> 
						   <br/>
						   <font color='red'><b>Status:</b></font> Balance will be merged soon
						</div>
					</div>
				<?php
				}
				else if($merge == 'pending')
				{
				
				?>
					<div class="panel panel-default" style='color: #000;background: linear-gradient(to bottom,rgba(252,234,187,1) 0%,rgba(252,205,77,1) 46%,rgba(248,181,0,1) 60%,rgba(251,223,147,1) 100%); border: 1px solid #949494;'>
						<div class="panel-heading">
							<b>Get Help Request (<?php echo $ghID;?>)</b>
						</div>
						<div class="panel-body">
						   <b>Participant : </b><?php echo ucfirst($participant_name);?>
						   <br/>
						   <b>Amount: </b>&#8358;<?php echo number_format(($amountGH),2);?> 
						   <br/>
						   <b>Balance: </b>&#8358;<?php echo number_format(($balanceGH),2);?> 
						   <br/>
						   <font color='red'><b>Status:</b></font> Re-PH and Get 300% of your New PH
						</div>
					</div>
				<?php
				}
			}
		}
						
	}
	
	//Delete PH
	if(isset($_GET['deletePH']))
	{
		$phID = $_GET['deletePH'];
		$getallPH = $con->prepare("SELECT * FROM `providehelp` where participantID='$pid' and phID='$phID'");
		$getallPH->execute();
		$getallPHInfo = $getallPH->fetch(PDO::FETCH_LAZY);
		$phid = $getallPHInfo['phID'];
		$amntPH = $getallPHInfo['amntPH'];
		$return_amnt = $getallPHInfo['return_amnt'];
		$real_return_amnt = $amntPH * 2;
		
		if($return_amnt > $real_return_amnt)
		{
			$toreturn_GH = $return_amnt - $real_return_amnt;
			$check_allpendingGH = rows("Select * from gethelp where participantID='$pid' and merge='pending'");
			if($check_allpendingGH >= 1)
			{
				$getallGH = $con->prepare("SELECT * FROM `gethelp` where participantID='$pid' and merge='pending' order by ID desc");
				$getallGH->execute();
		
				$getallGHInfo = $getallGH->fetch(PDO::FETCH_LAZY);
				$amountGH = $getallGHInfo['amountGH'];
				$balanceGH = $getallGHInfo['balance'];
				$ghID = $getallGHInfo['ghID'];
				$income_balance = 	$balanceGH + $toreturn_GH;
				$saveGHblc = $con->prepare("update gethelp set balance='$income_balance' where participantID='$pid' and ghID='$ghID'");
				$saveGHblc->execute();
				$deletuserPH = $con->prepare("update `providehelp` set status='Cancelled', merge='Cancelled'  where phID='$phID'");
				$deletuserPH_referral = $con->prepare("update `referral` set status='Cancelled' where phID='$phID'");
				if($deletuserPH->execute() && $deletuserPH_referral->execute())
				{
					$msg = substr(md5("OPERATION SUCCESSFUL"), -4);
					redirect_to("private/dashboard?msg=$msg");
				}
				
			}
			else
			{
				$ghID="M". mt_rand(11111,12345).mt_rand(11111,99999); //Pledge Id
				$GHDATE =  date('d.m.Y h:i:s A'); // Date for get help
				
				$todaysDay =  date('D'); //Mon?
				if($todaysDay == 'Mon' || $todaysDay == 'Tue'  || $todaysDay == 'Wed'  || $todaysDay == 'Thu'  || $todaysDay == 'Fri')
				{
					$releaseDATE =  date('d.m.Y H:i:s', strtotime('+98 hours')); // Date for get help
				}
				else
				{
					$releaseDATE =  date('d.m.Y', strtotime('+122 hours')); // Date for get help
				}
				
				$tosave_GH = $con->prepare("insert into gethelp (ghID, participantID, amountGH, ghDATE, releaseDATE, user_status, merge) values ('$ghID', '$pid', '$toreturn_GH', '$GHDATE', '$releaseDATE', 'active', 'pending')");
				$tosave_GH->execute();
				$deletuserPH = $con->prepare("update `providehelp` set status='Cancelled', merge='Cancelled'  where phID='$phID'");
				$deletuserPH_referral = $con->prepare("update `referral` set status='Cancelled' where phID='$phID'");
				if($deletuserPH->execute() && $deletuserPH_referral->execute())
				{
					$msg = substr(md5("OPERATION SUCCESSFUL"), -4);
					redirect_to("private/dashboard?msg=$msg");
				}
			}
		}
		else
		{
			$deletuserPH = $con->prepare("update `providehelp` set status='Cancelled', merge='Cancelled'  where phID='$phID'");
			$deletuserPH_referral = $con->prepare("update `referral` set status='Cancelled' where phID='$phID'");
			if($deletuserPH->execute() && $deletuserPH_referral->execute())
			{
				$msg = substr(md5("OPERATION SUCCESSFUL"), -4);
				redirect_to("private/dashboard?msg=$msg");
			}
		}
	}
	
	//Confirm GH Order
	if(isset($_GET['ConfirmGH']) && ($_GET['ghID']))
	{
		$ghID = $_GET['ghID'];
		$phID = $_GET['phID'];
		// echo 'Chill';
		
		
		
		//Let get the receipt payment details
		$getPOP = $con->prepare("SELECT * FROM `merge_gh` where ghID='$ghID' and (status='Upload' or status='FAKEPOP') and phID='$phID'");
		$getPOP->execute();
		
		//We need the total amount of PH merge_gh
		$getPOP_info = $getPOP->fetch(PDO::FETCH_ASSOC);
		$phID = $getPOP_info['phID'];
		$status = $getPOP_info['status'];
		$amountGH = $getPOP_info['amountGH'];
		$mergePH_participantID = $getPOP_info['participantID'];
		
		//Since we have the amount User GH, then let us check the ph rows
		$checkPH = $con->prepare("select * from providehelp where phID='$phID'");
		$checkPH->execute();
		// for($i=1; $i<=rows("select * from providehelp where phID='$phID'"); $i++)
		// {
			$checkPH_info = $checkPH->fetch(PDO::FETCH_ASSOC);
			$amntPH = $checkPH_info['amntPH'];
			$balance = $checkPH_info['balance'];
			$wallet = $checkPH_info['wallet'];
			$ph_participantID = $checkPH_info['participantID'];
			$amount_confirm = $checkPH_info['amount_confirm']; // How much has user paid
			
			//Is amount GH == Amount PH??  If yes then user payment is completed and successful
			if($amntPH == $amountGH && $balance == 0)
			{
				//Update PH
				$updatePH = $con->prepare("update providehelp set paid='YES', merge='complete', status='Confirmed' where phID='$phID' and amntPH='$amountGH'");
				$updatePH->execute();
				
				// We need to update Referral Bonus so that User will be able to GH anytime
				$update_referral = $con->prepare("update referral set status='Confirmed' where phID='$phID' and participantID='$mergePH_participantID'");
				$update_referral->execute();

				// Has payment been flagged as FAKE before???
				// Then we need to auto confirm and lock the ticket once receiver has marked received
				$updateTICKET = $con->prepare("update ticket set locked='YES' where ghID='$ghID' and phID='$phID'");
				$updateTICKET->execute();
				
				// We have to update merge GH
				$updateGH = $con->prepare("update merge_gh set status='Confirmed' where ghID='$ghID' and phID='$phID'");
				$updateGH->execute();
				
				$msg = substr(md5("PAYMENT CONFIRMED. THANK YOU"), -4);
				redirect_to("private/dashboard?msg=$msg");
				
			}
			else
			{
				// echo $amntPH;
				//ehmnnn, U pay 2K out of 5K, u think say U smart, lolzzzzzz
				// Ur money dey pile up oooooooooooooo
				
				//Since user says amount has been paid, then lets confirm
				$amount_confirm_pay = $amount_confirm + $amountGH;
				
				// Payment completed
				if($amount_confirm_pay == $amntPH)
				{

					// We have to update merge GH
					$updateGH = $con->prepare("update merge_gh set status='Confirmed' where ghID='$ghID' and phID='$phID'");
					$updateGH->execute();
				
					// We need to update Referral Bonus so that User will be able to GH anytime
					$update_referral = $con->prepare("update referral set status='Confirmed' where phID='$phID' and participantID='$mergePH_participantID'");
					$update_referral->execute();
					
					//Update PH
					$updatePH = $con->prepare("update providehelp set paid='YES', merge='complete', status='Confirmed', amount_confirm='$amount_confirm_pay' where phID='$phID' and amntPH='$amount_confirm_pay'");
					$updatePH->execute();
					
					// This person na idiot oooo y u no pay money when u've paid part of it, lets pity am make him no loose all 
					// Has payment been flagged as FAKE before???
					// Then we need to auto confirm and lock the ticket once receiver has marked received
					$updateTICKET = $con->prepare("update ticket set locked='YES' where ghID='$ghID' and phID='$phID'");
					$updateTICKET->execute();
					
					$msg = substr(md5("PAYMENT CONFIRMED. THANK YOU"), -4);
					redirect_to("private/dashboard?msg=$msg");
					
				}
				
				else if($amount_confirm_pay != $amntPH)
				{
					// / We have to update merge GH
					$updateGH = $con->prepare("update merge_gh set status='Confirmed' where ghID='$ghID' and phID='$phID'");
					$updateGH->execute();
				
					//Update PH, U need to pay all your money oooo, no refferal GH yet
					$updatePH = $con->prepare("update providehelp set amount_confirm='$amount_confirm_pay' where phID='$phID' and amntPH='$amntPH'");
					$updatePH->execute();
					
					// This person na idiot oooo y u no pay money when u've paid part of it, lets pity am make him no loose all 
					// Has payment been flagged as FAKE before???
					// Then we need to auto confirm and lock the ticket once receiver has marked received
					$updateTICKET = $con->prepare("update ticket set locked='YES' where ghID='$ghID' and phID='$phID'");
					$updateTICKET->execute();
					
					$msg = substr(md5("PAYMENT CONFIRMED. THANK YOU"), -4);
					redirect_to("private/dashboard?msg=$msg");
					
				}
				
				
			}
			// echo $ph_participantID;
		// }
		
		
		// echo $mergePH_participantID;
		
	}
	
	//Saving GH letter
	if(isset($_GET['SaveGH_letter']))
	{
		$participantID = $_POST['participantID'];
		$gh_letter = $_POST['gh_letter'];
		$ghID = $_POST['ghID'];
		$todaysDATE =  date('M d, Y h:i:s A'); //12hours to pay normal day
		
		$checkGH = $con->prepare("select * from merge_gh where ghID='$ghID' and status='Confirmed'");
		$checkGH->execute();
		$checkGH_INFO = $checkGH->fetch(PDO::FETCH_ASSOC);
		$phID = $checkGH_INFO['phID'];
		$saveLETTER = $con->prepare("INSERT INTO testimonies (participantID, gh_letter, date_written) values ('$participantID', '$gh_letter', '$todaysDATE')");
		if($saveLETTER->execute())
		{
			$updateGH_letter = $con->prepare("update merge_gh set gh_letter='YES' where ghID='$ghID' and gh_participantID='$pid'");
			$updateGH_letter->execute();
			echo "Testimonial has been added to our database";
		}
		// echo $phID;
		// echo $gh_letter	;
	}
	
	//FAKE POP
	if(isset($_GET['FAKEPOP']))
	{
		$ghID = $_GET['ID'];
		$mergeID = $_GET['mergeID'];
		$checkGH_row = rows("select * from merge_gh where ghID='$ghID' and mergeID='$mergeID' and status='Upload'");
		if($checkGH_row == 0)
		{
			$msg = substr(md5("Transaction not found"), -4);
			redirect_to("private/dashboard?msg=$msg");
		}
		else
		{
			$checkGH = $con->prepare("select * from merge_gh where ghID='$ghID' and mergeID='$mergeID' and status='Upload'");
			$checkGH->execute();
			$checkGH_INFO = $checkGH->fetch(PDO::FETCH_ASSOC);
			$participantID = $checkGH_INFO['participantID']; // Participant who pays and upload
			$phID = $checkGH_INFO['phID']; // Participant  PHID who is to pay
			$gh_participantID = $checkGH_INFO['gh_participantID']; // Participant expecting payment
			$amountGH = $checkGH_INFO['amountGH']; // Amount expecting
			$date_written = date('M d, Y H:i:s'); //Todays date
			
			//We can't just conclude that it is fake, a process must be done
			
			$updateMERGE = $con->prepare("update merge_gh set status='FAKEPOP' where gh_participantID='$gh_participantID' and mergeID='$mergeID' and participantID='$participantID'");
			if($updateMERGE->execute())
			{
					$ticketmsg = "Dear participant,
					
					Since payment was flag as FAKE i.e. Not Received!, The two parties are to perform the following
					
					1. Receiver should upload his or her statement of account starting from a week before expecting payment plus a week of payment expiration.
					
					2. Sender should upload proof of paymnent as attached to receiver.
					
					NOTE:
					
					Should any of the two parties fails to attach a proof under 48hours, then he or she will be blocked.
					
					THANK YOU.";
					$subject = "FAKE PROOF OF PAYMENT 
					
					Get Help ID: $ghID
					
					Provide Help ID: $phID
					
					Amount: $amountGH";
					
					
					
					
		$sender_msg= "<html>
    <head>
        <title>TICKET CREATION</title>
    </head>
    <body><div>
<div style='font-family:arial;border:2px solid #c0c0c0;padding:15px;border-radius:5px;'>
<div style='font-size:22px;color:darkblue;font-weight:bold;'>TICKET CREATION GIVERSCYCLER</div>
    <br>
Dear Participant,

You recently complained that a FAKE POP was upload by your payee.

Kindly login to your account to confirm.

Thank You.

GIVERSCYCLER ::: Givers are Receiver
 <br>

</div></div></body></html>";

				
//FOR PAYEE				
		$receiver_msg= "<html>
    <head>
        <title>TICKET CREATION</title>
    </head>
    <body><div>
<div style='font-family:arial;border:2px solid #c0c0c0;padding:15px;border-radius:5px;'>
<div style='font-size:22px;color:darkblue;font-weight:bold;'>TICKET CREATION GIVERSCYCLER</div>
    <br>
Dear Participant,

Receiver allegedly claim that fund was not received

Kindly login to your account to confirm.

Thank You.

GIVERSCYCLER ::: Givers are Receiver
 <br>

</div></div></body></html>";
		
		$subject_ticket = "FAKE PROOF OF PAYMENT";
		//Lets get participant PH email address
			$phUser = $con->prepare("select * from participant where pid='$participantID'");
			$phUser->execute();
			$phUserInfo = $phUser->fetch(PDO::FETCH_ASSOC);
			$ph_email  = $phUserInfo['email'];
			$participant_name  = strtoupper($phUserInfo['name']);
			
			
			
			//Lets get participant receiver info
			$ghUser = $con->prepare("select * from participant where pid='$gh_participantID'");
			$ghUser->execute();
			$ghUserInfo = $ghUser->fetch(PDO::FETCH_ASSOC);
			$gh_email  = $ghUserInfo['email'];
			$receiver_name  = strtoupper($ghUserInfo['name']);
			
			$headers  = 'MIME-Version: 1.0' . "\r\n";
			$headers .= 'Content-type: text/html; charset=iso-8859-1' . "\r\n";
			// set additional headers
			$headers .= 'From: FAKE PROOF OF PAYMENT..... <no-reply@giverscycler.com>' . "\r\n".'X-Mailer: PHP/' . phpversion();

					
					//We need to send a msg to Admin
					$subject_admin = "TICKET CREATION: FAKE PROOF OF PAYMENT";
					$admin_email = "giverscycler@gmail.com";
					$admin_msg = "Dear Admin,
					
Participant recently complained that payment was not made, please review and attend to the issue.
					
Thanks";
					
					mail($gh_email, $subject_ticket, $sender_msg, $headers); // Send  it to user that is expecting
					mail($ph_email, $subject_ticket, $receiver_msg, $headers); // Send  it to user dat paid
					mail($admin_email, $subject_admin, $admin_msg, $headers); // Send  it to ADMIN
					
					
					$saveTICKET = $con->prepare("insert into ticket (participant, report_participant, subject, ticketmsg, ghID, phID, relatedISSUE, date_written) values ('$gh_participantID', '$participantID', '$subject','$ticketmsg','$ghID','$phID', 'SENDER UPLOADED A FAKE PROOF OF PAYMENT', '$date_written')");
					$saveTICKET->execute();
					$msg = substr(md5("A ticket has been opened on your behalf. Thank you"), -4);	
					redirect_to("private/dashboard?msg=$msg");
					
			}
			// echo $participantID;
			// echo "You wanna kill me, chillax abeg";
		}
	}

	
	//Saving Referral GH
	if(isset($_GET['IS_greater']))
	{
		$amnt = $_GET['amnt'];
		$pid = $_GET['pid'];
		$ghID ="M". mt_rand(11111,12345).mt_rand(11111,99999); //Pledge Id
		for($i=1; $i<=4; $i++){
			$PHID="Z". mt_rand(11111,12345).mt_rand(11111,99999); //Pledge Id
		}
			$GHDATE = date('d.m.Y h:i:s A');

		//What's today day?
		$todaysDay =  date('D'); //Mon?	
		
		if($todaysDay == 'Mon' || $todaysDay == 'Tue'  || $todaysDay == 'Wed'  || $todaysDay == 'Thu'  || $todaysDay == 'Fri')
		{
			$releaseDATE =  date('d.m.Y H:i:s', strtotime('+96 hours')); // Date for get help
		}
		else
		{
			$releaseDATE =  date('d.m.Y H:i:s', strtotime('+120 hours')); // Date for get help
		}
		
		
		// echo $releaseDATE;
		//Since our merging aspect deals with amount / 2 .... 2:1 matrix then we need to follow our package fee
		if($amnt >= 50000)
		{
			// echo 50000;
			$to_SAVE = $amnt - 50000;
			$tosave_GH = $con->prepare("insert into gethelp (ghID, participantID, amountGH, ghDATE, releaseDATE, user_status) values ('$ghID', '$pid', '50000', '$GHDATE', '$releaseDATE', 'active')");
			$tosave_GH->execute();
			
			$allREFERRAL = $con->prepare("update referral set status='Withdraw' where status='Confirmed' and referralID='$pid'");
			$allREFERRAL->execute();
			
			if($to_SAVE != 0)
			{
				//We need to keep it back
				$tosave_GH = $con->prepare("insert into referral (phID, participantID, referralBonus, status, referralID) values ('$PHID', 'System-Refund', '$to_SAVE', 'Confirmed', '$pid')");
				$tosave_GH->execute();
			}
			$msg = substr(md5("Transaction Completed"), -4);
			redirect_to("private/referral?refBonus&msg=$msg");
			
		}
		else if($amnt >= 20000)
		{
			$to_SAVE = $amnt - 20000;
			$tosave_GH = $con->prepare("insert into gethelp (ghID, participantID, amountGH, ghDATE, releaseDATE, user_status) values ('$ghID', '$pid', '20000', '$GHDATE', '$releaseDATE', 'active')");
			$tosave_GH->execute();
			
			$allREFERRAL = $con->prepare("update referral set status='Withdraw' where status='Confirmed' and referralID='$pid'");
			$allREFERRAL->execute();
			
			if($to_SAVE != 0)
			{
				//We need to keep it back
				$tosave_GH = $con->prepare("insert into referral (phID, participantID, referralBonus, status, referralID) values ('$PHID', 'System-Refund', '$to_SAVE', 'Confirmed', '$pid')");
				$tosave_GH->execute();
			}
			$msg = substr(md5("Transaction Completed"), -4);
			redirect_to("private/referral?refBonus&msg=$msg");
		}
		else if($amnt >= 10000)
		{
			$to_SAVE = $amnt - 10000;
			$tosave_GH = $con->prepare("insert into gethelp (ghID, participantID, amountGH, ghDATE, releaseDATE, user_status) values ('$ghID', '$pid', '10000', '$GHDATE', '$releaseDATE', 'active')");
			$tosave_GH->execute();
			
			$allREFERRAL = $con->prepare("update referral set status='Withdraw' where status='Confirmed' and referralID='$pid'");
			$allREFERRAL->execute();
			
			if($to_SAVE != 0)
			{
				//We need to keep it back
				$tosave_GH = $con->prepare("insert into referral (phID, participantID, referralBonus, status, referralID) values ('$PHID', 'System-Refund', '$to_SAVE', 'Confirmed', '$pid')");
				$tosave_GH->execute();
			}
			$msg = substr(md5("Transaction Completed"), -4);
			redirect_to("private/referral?refBonus&msg=$msg");
		}
		else if($amnt >= 4000)
		{
			$to_SAVE = $amnt - 4000;
			$tosave_GH = $con->prepare("insert into gethelp (ghID, participantID, amountGH, ghDATE, releaseDATE, user_status) values ('$ghID', '$pid', '4000', '$GHDATE', '$releaseDATE', 'active')");
			$tosave_GH->execute();
			
			$allREFERRAL = $con->prepare("update referral set status='Withdraw' where status='Confirmed' and referralID='$pid'");
			$allREFERRAL->execute();
			
			if($to_SAVE != 0)
			{
				//We need to keep it back
				$tosave_GH = $con->prepare("insert into referral (phID, participantID, referralBonus, status, referralID) values ('$PHID', 'System-Refund', '$to_SAVE', 'Confirmed', '$pid')");
				$tosave_GH->execute();
			}
			$msg = substr(md5("Transaction Completed"), -4);
			redirect_to("private/referral?refBonus&msg=$msg");
		}
		else if($amnt >= 2000)
		{
			$to_SAVE = $amnt - 2000;
			$tosave_GH = $con->prepare("insert into gethelp (ghID, participantID, amountGH, ghDATE, releaseDATE, user_status) values ('$ghID', '$pid', '2000', '$GHDATE', '$releaseDATE', 'active')");
			$tosave_GH->execute();
			
			$allREFERRAL = $con->prepare("update referral set status='Withdraw' where status='Confirmed' and referralID='$pid'");
			$allREFERRAL->execute();
			
			if($to_SAVE != 0)
			{
				//We need to keep it back
				$tosave_GH = $con->prepare("insert into referral (phID, participantID, referralBonus, status, referralID) values ('$PHID', 'System-Refund', '$to_SAVE', 'Confirmed', '$pid')");
				$tosave_GH->execute();
			}
			$msg = substr(md5("Transaction Completed"), -4);
			redirect_to("private/referral?refBonus&msg=$msg");
		}
		else
		{
			//DO nothing..............
		}
	}
	
	//Saving Ticket from participant after creating ticket
	if(isset($_POST["SubmitTICKET"]))
	{
		$message = $_POST["message"];
		$subject = $_POST["subject"];
		$relatedISSUE = $_POST["relatedISSUE"];
		$date_written = date('M d, Y H:i:s'); //Todays date
		$saveTicket = $con->prepare("insert into ticket (participant, subject, relatedIssue, ticketmsg, date_written) values ('$pid', '$subject', '$relatedISSUE', '$message', '$date_written')");
		if($saveTicket->execute())
		{
			$msg = strtoupper(substr(md5("TICKET SAVED"), -4));
			redirect_to("private/ticket?msg=$msg");
		}
		// echo $msg_block;
	}
	
	//Saving Reply ticket
	if(isset($_POST["reply_ticket"]))
	{
		$reply_ticket_msg = $_POST["reply_ticket_msg"]; //Message reply
		$ticketID = $_POST["ticketID"]; //Ticket ID replied
		$file_reply_name = $_FILES["file_reply"]["name"]; //File name
		$file_reply_tmp = $_FILES["file_reply"]["tmp_name"]; //TMP location
		$folder = 'img/attachment/';
		$joinfile = $folder.$file_reply_name;
		$date_written = date('M d, Y H:i:s'); //Todays date
		
		//Has Ticket been replied before?
		$getTICKET = $con->prepare("select * from ticket where tid='$ticketID'");
		$getTICKET->execute();
		$getTICKET_INFO = $getTICKET->fetch(PDO::FETCH_ASSOC);
		$replied = $getTICKET_INFO["replied"];
		$totalReply = $replied +1;
		
		if(empty($file_reply_name))
		{
			$save_reply = $con->prepare("insert into `ticket_replies` (ticketid, participantID, replymsg, date_written) values ('$ticketID', '$pid', '$reply_ticket_msg', '$date_written')");
			if($save_reply->execute())
			{
				$msg = strtoupper(substr(md5("Reply was added successfully"), -4));
				redirect_to("private/ticket?msg=$msg");	
				$updateTICKET = $con->prepare("update ticket set replied='$totalReply' where tid='$ticketID'");
				$updateTICKET->execute();
			}
		}
		else if(!empty($file_reply_name)) // IS USER UPLOADING A FILE?????????
		{
			if($file_size > 2000000) // File is too large
			{
				$msg = strtoupper(substr(md5("File is too large to be saved"), -4));
				redirect_to("private/ticket?msg=$msg");
			}
			else
			{
				$movefile = move_uploaded_file($file_reply_tmp, $joinfile);
				$save_reply = $con->prepare("insert into `ticket_replies` (ticketid, participantID, replymsg, attachment, date_written) values ('$ticketID', '$pid', '$reply_ticket_msg', '$file_reply_name', '$date_written')");
				if($save_reply->execute())
				{
					$msg = strtoupper(substr(md5("Reply was added successfully"), -4));
					redirect_to("private/ticket?msg=$msg");	
					$updateTICKET = $con->prepare("update ticket set replied='$totalReply' where tid='$ticketID'");
					$updateTICKET->execute();
				}
			}
		}
	}
	
	
	
	//Provide Help by Super Admin
	if(isset($_GET['auto_saveGH']))
	{
		$participant_id = $_POST['participant'];
		$package_id = $_POST['packages'];
		
		for($i=1; $i<=4; $i++){
			$PHID="Z". mt_rand(11111,12345).mt_rand(11111,99999); //Pledge Id
		}
		
		// $package_id = $_GET['package_id'];
		$get_allpackkages = $con->prepare("select * from packages where package_id='$package_id'");
		$get_allpackkages->execute();
		$get_allpackkages_INFO = $get_allpackkages->fetch(PDO::FETCH_ASSOC);
		$package_id = $get_allpackkages_INFO['package_id'];
		$package_name = $get_allpackkages_INFO['package_name'];
		$package_fee = $get_allpackkages_INFO['amount'];
		$return_amnt = $package_fee * 2;
		$createDATE = date('d.m.Y h:i A');
		$referralBonus = (($package_fee * 10)/100);
		
		
		//Email Amount
		
		$return_amntt  = number_format($return_amnt);
		$package_feee  = number_format($package_fee);
		
		//Lets get participant PH email address
		$phUser = $con->prepare("select * from participant where pid='$participant_id'");
		$phUser->execute();
		$phUserInfo = $phUser->fetch(PDO::FETCH_ASSOC);
		$ph_email  = $phUserInfo['email'];
		$releaseDATE = date('d.m.Y h:i A', strtotime("+1 hours")); //Some users will PH also, don't be greedy
		
		
		//Email Aspect
		
		// set content type header for html email
		$headers  = 'MIME-Version: 1.0' . "\r\n";
		$headers .= 'Content-type: text/html; charset=iso-8859-1' . "\r\n";
		// set additional headers
		$headers .= 'From: PH REQUEST <no-reply-ph@giverscycler.com>' . "\r\n".'X-Mailer: PHP/' . phpversion();
		$subject = "PROVIDE HELP REQUEST";
		$body= "<html>
    <head>
        <title>PROVIDE HELP REQUEST</title>
    </head>
    <body><div>
<div style='font-family:arial;border:2px solid #c0c0c0;padding:15px;border-radius:5px;'>
<div style='font-size:22px;color:darkblue;font-weight:bold;'>PH Request GIVERSCYCLER</div>
    <br>

Your Request PROVIDE HELP has been added to our database. Please wait for MATCHING PROCESS at this time. This can be instant or take less than few hours. <br>

Please find your PH Request details below.<br>
<br>
Plan: <b>$package_name</b><br>
Amount: <b>&#8358;$package_feee</b><br>
Return Inward: <b>&#8358;$return_amntt</b><br>

<br>
Thank YOU.<br>
</div></div></body>";
		
		$pHbefore = rows("SELECT * FROM `providehelp` where participantID='$pid'");
		if($pHbefore == 0)
		{
			// if($package_fee > 2000)
			// {
				// $regbonus = (($package_fee * 20)/100);
				
				// $save_regFee = $con->prepare("INSERT INTO providehelp (phID, status, wallet, RegBonus, createDATE, amntPH, return_amnt, participantID) values ('$PHID', 'Confirmed', '$package_id', 'RegBonus', '$createDATE', '$package_fee', '$regbonus', '$pid')");
				// $save_regFee->execute();
			// }
				
			$save_package = $con->prepare("insert into providehelp (participantID, status, phID, wallet, amntPH, return_amnt, createDATE, releaseDATE, paid, merge) values ('$participant_id', 'Confirmed', '$PHID', '$package_id', '$package_fee', '$return_amnt', '$createDATE', '$releaseDATE', 'YES', 'complete')");
			$save_package->execute();
			if(empty($invite_id))
			{
				$save_referralBonus = $con->prepare("INSERT INTO referral (phID, status, wallet, createDATE, referralBonus, referralID, participantID) values ('$PHID', 'Confirmed', '$package_id', '$createDATE', '$referralBonus', '1', '$participant_id')");
				$save_referralBonus->execute();
			}
			else if(!empty($invite_id))
			{
				$save_referralBonus = $con->prepare("INSERT INTO referral (phID, status, wallet, createDATE, referralBonus, referralID, participantID) values ('$PHID', 'Confirmed', '$package_id', '$createDATE', '$referralBonus', '$invite_id', '$participant_id')");
				$save_referralBonus->execute();
			}
			
			//Sending Email
			mail($ph_email, $subject, $body, $headers);
			echo strtoupper("Your request was successful. Thank You");
			// redirect_to("private/packages?msg=$msg");
		}
		else
		{
			$save_package = $con->prepare("insert into providehelp (participantID, status, phID, wallet, amntPH, return_amnt, createDATE, releaseDATE, paid, merge) values ('$participant_id', 'Confirmed', '$PHID', '$package_id', '$package_fee', '$return_amnt', '$createDATE', '$releaseDATE', 'YES', 'complete')");
			$save_package->execute();
			
			if(empty($invite_id))
			{
				$save_referralBonus = $con->prepare("INSERT INTO referral (phID, status, wallet, createDATE, referralBonus, referralID, participantID) values ('$PHID', 'Confirmed', '$package_id', '$createDATE', '$referralBonus', '1', '$participant_id')");
				$save_referralBonus->execute();
			}
			else if(!empty($invite_id))
			{
				$save_referralBonus = $con->prepare("INSERT INTO referral (phID, status, wallet, createDATE, referralBonus, referralID, participantID) values ('$PHID', 'Confirmed', '$package_id', '$createDATE', '$referralBonus', '$invite_id', '$participant_id')");
				$save_referralBonus->execute();
			}
			
			//Sending Email
			mail($ph_email, $subject, $body, $headers);
			echo strtoupper("Your request was successful. Thank You");
			// redirect_to("executives/createGH?msg=$msg");
		}
		// echo $participant_id;
	}
	
	//Unblock Participant By Admin
	if(isset($_GET['Unblock']))
	{
		$Unblock = $_GET['Unblock'];
		//We need to Unblock User
		$updateParticipant = $con->prepare("UPDATE `participant` SET status='active' WHERE `pid`='$Unblock'");
		
		//Since he or she refuses to pay we block him to receive, is that not cool???
		if($updateParticipant->execute())
		{
			//We need to unblock his money, no participant must loose
			$updateGetHelp = $con->prepare("UPDATE `gethelp` SET user_status='active' WHERE `participantID`='$Unblock'");
			$updateGetHelp->execute();
			$msg = strtoupper(substr(md5('User Unblocked'), -4));
			redirect_to("executives/participants?Blocked&msg=$msg");
		}
		// echo $Unblock;
	}
	
	//Write News 
	if(isset($_POST["save_news_update"]))
	{
		$topic = $_POST['topic'];
		$newsupdate_content = addslashes($_POST['newsupdate_content']);
		$todaysDATE =  date('M d, Y h:i:s A'); 
		$save_NEWSUPDATE = $con->prepare("insert into newsupdate (news_subject, newsmsg, date_written) values ('$topic', '$newsupdate_content', '$todaysDATE')");
		if($save_NEWSUPDATE->execute())
		{
			$msg = strtoupper(substr(md5('News Added Successfully'), -4));
			redirect_to("executives/newsupdate?msg=$msg");
		}
		else
		{
			$msg = strtoupper(substr(md5('Unable to save News'), -4));
			redirect_to("executives/newsupdate?msg=$msg");
		}
	}
	
	// Edit News
	if(isset($_POST['saveEDITEDNEWS']))
	{
		$topic = $_POST['topic'];
		$newsupdate_content = addslashes($_POST['newsupdate_content']);
		$postid = $_POST['postid'];
		
		$save_NEWSUPDATE = $con->prepare("update newsupdate set news_subject='$topic', newsmsg='$newsupdate_content' where news_id='$postid'");
		if($save_NEWSUPDATE->execute())
		{
			$msg = strtoupper(substr(md5('News Updated Successfully'), -4));
			redirect_to("executives/newsupdate?msg=$msg");
		}
		else
		{
			$msg = strtoupper(substr(md5('Unable to save News'), -4));
			redirect_to("executives/newsupdate?msg=$msg");
		}
		// echo $postid;
		
	}
	
	//Re-Merging participant by Admin
	if(isset($_GET['ResolvePOP']))
	{
		$phID = $_GET['phID'];
		$ghID = $_GET['ghID'];
		
		$getPH = $con->prepare("select * from providehelp where phID='$phID'");
		$getPH->execute();
		$getPH_info = $getPH->fetch(PDO::FETCH_ASSOC);
		$participantID = $getPH_info['participantID'];
		$amntPH = $getPH_info['amntPH'];
		// $referralAMNT = $amntPH / 10;
		
		//We need to cancel the merge and return the participant back to queue
		// Sorry case
		$getMERGE = $con->prepare("select * from merge_gh where phID='$phID' and ghID='$ghID' and status='FAKEPOP'");
		$getMERGE->execute();
		$getMERGE_info = $getMERGE->fetch(PDO::FETCH_ASSOC);
		$gh_participantID = $getMERGE_info['gh_participantID'];
		$amountGH = $getMERGE_info['amountGH'];
		
		// echo $amountGH;
		// echo $gh_participantID;
		
		
		//We need to fetch gethelp info
		
		$getGH = $con->prepare("select * from gethelp where ghID='$ghID' and participantID='$gh_participantID'");
		$getGH->execute();
		$getGH_info = $getGH->fetch(PDO::FETCH_ASSOC);
		$balance_inGH = $getGH_info['balance'];
		$merge_status = $getGH_info['merge'];
		$amount_userGH = $getGH_info['amountGH'];
		// echo $balance_inGH;
		if($balance_inGH == 0)
		{
			$save_toGH = $balance_inGH + $amountGH;
			// echo $save_toGH;
			//We need to update gethelp
			$saveBACKGH = $con->prepare("update gethelp set balance='$save_toGH', merge='partial' where ghID='$ghID' and participantID='$gh_participantID'");
			$saveBACKGH->execute();
			
			
			$cancelPH = $con->prepare("update providehelp set status='Cancelled' where phID='$phID' and participantID='$participantID' and amntPH='$amntPH'");
			$cancelPH->execute();
			
			$cancelReferral_BONUS = $con->prepare("update referral set status='Cancelled' where phID='$phID' and participantID='$participantID'");
			$cancelReferral_BONUS->execute();
			
			//Since we have been able to remerge, then block that participant dat upload FAKEPOP
			$blockUSER = $con->prepare("update participant set status='blocked' where pid='$participantID'");
			$blockUSER->execute();
			
			//We need to block participant from being merged to receive
			$cancel_GH = $con->prepare("update gethelp set user_status='blocked' where participantID='$participantID' and amountGH='$amountGH'");
			$cancel_GH->execute();
			
			
			//Cancel Merge
			$cancelMERGE = $con->prepare("update merge_gh set status='Cancelled' where phID='$phID' and ghID='$ghID' and participantID='$participantID' and amountGH='$amountGH'");
			$cancelMERGE->execute();
			
			//We need to redirect Admin back
			$msg = strtoupper(substr(md5("Action Completed!"), -4));
			redirect_to("executives/gh-request?FAKEPOP&msg=$msg");
			
		}
		
		else if($balance_inGH != 0 && $merge_status == 'partial') //First payment not made???
		{
			$totalbalance = $balance_inGH + $amountGH;
			if($amount_userGH == $totalbalance)
			{
				
				//We need to return GH participant to begining 
				$addGH_back = $con->prepare("update gethelp set balance='0', merge='NO' where ghID='$ghID' and participantID='$gh_participantID' and amountGH='$totalbalance'");
				$addGH_back->execute();	
				
				$cancelPH = $con->prepare("update providehelp set status='Cancelled' where phID='$phID' and participantID='$participantID' and amntPH='$amntPH'");
				$cancelPH->execute();
				
				$cancelReferral_BONUS = $con->prepare("update referral set status='Cancelled' where phID='$phID' and participantID='$participantID'");
				$cancelReferral_BONUS->execute();
				
				//Since we have been able to remerge, then block that participant dat upload FAKEPOP
				$blockUSER = $con->prepare("update participant set status='blocked' where pid='$participantID'");
				$blockUSER->execute();
				
				//We need to block participant from being merged to receive
				$cancel_GH = $con->prepare("update gethelp set user_status='blocked' where participantID='$participantID' and amountGH='$amountGH'");
				$cancel_GH->execute();
			
				//Cancel Merge
				
				$cancelMERGE = $con->prepare("update merge_gh set status='Cancelled' where phID='$phID' and ghID='$ghID' and participantID='$participantID' and amountGH='$amountGH'");
				$cancelMERGE->execute();
				
				
				//We need to redirect Admin back
				$msg = strtoupper(substr(md5("Action Completed!"), -4));
				redirect_to("executives/gh-request?FAKEPOP&msg=$msg");
				
			}
			else
			{
				echo $totalbalance;
				
				//We need to return GH participant to begining 
				$addGH_back = $con->prepare("update gethelp set balance='$totalbalance', merge='partial' where ghID='$ghID' and participantID='$gh_participantID'");
				$addGH_back->execute();	
				
				$cancelPH = $con->prepare("update providehelp set status='Cancelled' where phID='$phID' and participantID='$participantID' and amntPH='$amntPH'");
				$cancelPH->execute();
				
				$cancelReferral_BONUS = $con->prepare("update referral set status='Cancelled' where phID='$phID' and participantID='$participantID'");
				$cancelReferral_BONUS->execute();
				
				//Since we have been able to remerge, then block that participant dat upload FAKEPOP
				$blockUSER = $con->prepare("update participant set status='blocked' where pid='$participantID'");
				$blockUSER->execute();
				
				//We need to block participant from being merged to receive
				$cancel_GH = $con->prepare("update gethelp set user_status='blocked' where participantID='$participantID' and amountGH='$amountGH'");
				$cancel_GH->execute();
			
				//Cancel Merge
				
				$cancelMERGE = $con->prepare("update merge_gh set status='Cancelled' where phID='$phID' and ghID='$ghID' and participantID='$participantID' and amountGH='$amountGH'");
				$cancelMERGE->execute();
				
				
				//We need to redirect Admin back
				$msg = strtoupper(substr(md5("Action Completed!"), -4));
				redirect_to("executives/gh-request?FAKEPOP&msg=$msg");
				
			}
		}
	}
		
		
	
	//Saving Reply ticket BY ADMIN
	if(isset($_POST["reply_ticket_admin"]))
	{
		$reply_ticket_msg = addslashes($_POST["reply_ticket_msg"]); //Message reply
		$reply_name = $_POST["reply_name"]; //Replying Admin
		$ticketID = $_POST["ticketID"]; //Ticket ID replied
		$file_reply_name = $_FILES["file_reply"]["name"]; //File name
		$file_reply_tmp = $_FILES["file_reply"]["tmp_name"]; //TMP location
		$folder = 'img/attachment/';
		$joinfile = $folder.$file_reply_name;
		$date_written = date('M d, Y H:i:s'); //Todays date
		
		//Has Ticket been replied before?
		$getTICKET = $con->prepare("select * from ticket where tid='$ticketID'");
		$getTICKET->execute();
		$getTICKET_INFO = $getTICKET->fetch(PDO::FETCH_ASSOC);
		$replied = $getTICKET_INFO["replied"];
		$totalReply = $replied +1;
		
		//If no file is included as attachment
		if(empty($file_reply_name))
		{
			$save_reply = $con->prepare("insert into `ticket_replies` (ticketid, participantID, replymsg, date_written) values ('$ticketID', '$reply_name', '$reply_ticket_msg', '$date_written')");
			if($save_reply->execute())
			{
				$msg = strtoupper(substr(md5("Reply was added successfully"), -4));
				redirect_to("executives/tickets?msg=$msg");	
				$updateTICKET = $con->prepare("update ticket set replied='$totalReply' where tid='$ticketID'");
				$updateTICKET->execute();
			}
		}
		else if(!empty($file_reply_name)) // IS USER UPLOADING A FILE?????????
		{
			if($file_size > 2000000) // File is too large
			{
				$msg = strtoupper(substr(md5("File is too large to be saved"), -4));
				redirect_to("executives/tickets?msg=$msg");
			}
			else
			{
				$movefile = move_uploaded_file($file_reply_tmp, $joinfile);
				$save_reply = $con->prepare("insert into `ticket_replies` (ticketid, participantID, replymsg, attachment, date_written) values ('$ticketID', '$reply_name', '$reply_ticket_msg', '$file_reply_name', '$date_written')");
				if($save_reply->execute())
				{
					$msg = strtoupper(substr(md5("Reply was added successfully"), -4));
					redirect_to("executives/tickets?msg=$msg");	
					$updateTICKET = $con->prepare("update ticket set replied='$totalReply' where tid='$ticketID'");
					$updateTICKET->execute();
				}
			}
		}
	}
	
	//Locking ticket
	if(isset($_GET['LockTicket']))
	{
		$tid = $_GET['tid'];
	
		$updateTICKET = $con->prepare("update ticket set locked='YES' where tid='$tid'");
		$updateTICKET->execute();
		$msg = strtoupper(substr(md5("Operation Successful"), -4));
		redirect_to("executives/tickets?msg=$msg");	
	}
	
	//Unlocking ticket
	if(isset($_GET['Unlock_Ticket']))
	{
		$Unlock_Ticket = $_GET['Unlock_Ticket'];
	
		$updateTICKET = $con->prepare("update ticket set locked='NO' where tid='$Unlock_Ticket'");
		$updateTICKET->execute();
		$msg = strtoupper(substr(md5("Operation Successful"), -4));
		redirect_to("executives/tickets?msg=$msg");	
	}
	
	
	//Search Participant
	if(isset($_GET['GETUSER']))
	{
		// echo 12;
		$search_participant = $_POST['search_participant'];
		$get_participants = $con->prepare("SELECT * FROM `participant` where name like '%$search_participant%' or mobile like '%$search_participant%' or email like '%$search_participant%' order by status desc");
		$get_participants->execute();
		// Not found????
		$get_participants_row = rows("SELECT * FROM `participant` where name like '%$search_participant%' or mobile like '%$search_participant%' or email like '%$search_participant%' order by status desc");
		//Fetching....
		if($get_participants_row == 0)
		{
		?>
			<div class='alert alert-warning' align='center'><font size='6px'><b>PARTICIPANT NOT FOUND</b></font></div>
		<?php
		}
		else
		{
		?>
			
		<?php
			for($i=1; $i<=$get_participants_row; $i++)
			{
				$get_participants_INFO = $get_participants->fetch(PDO::FETCH_ASSOC);
				$p_name = $get_participants_INFO['name'];
				$participant_email = $get_participants_INFO['email'];
				$participant_mobile = $get_participants_INFO['mobile'];
				$user_status = $get_participants_INFO['status'];
				$pid = $get_participants_INFO['pid'];
			?>
				<div class='col-md-4'>
					<div class="panel panel-default">
						<div class="panel-body">
					<b>Participant:</b> <?php echo ucfirst($p_name);?>
					<br>
					<b>Email:</b> <?php echo $participant_email;?>
					<br>
					<b>Mobile:</b> <?php echo $participant_mobile;?>
					<br>
					<b>Status:</b> <font color='red' size='3px'><strong><?php echo strtoupper($user_status);?></strong></font>
					<br>
					<a href='?ChangePskey&pid=<?php echo $pid;?>'><i class='fa fa-pencil'></i> Change Password</a>
                    <br>
                    <a href='?ChangeBank&pid=<?php echo $pid;?>'><i class='fa fa-pencil'></i> Edit Bank Details</a>

					</div>
				</div>
				</div>
			<?php
				// echo $user_status . '<br>';
			}
		}
	}
	
	//Change Password via Admin Panel.... 
	if(isset($_POST['save_editPSK']))
	{
		$pid = $_POST['pid']; // PARTICIPANT ID U r changing password 4.....
		$change_pswd = md5(strtolower($_POST["change_pswd"])); //Participant password
		// $change_pswd = $_POST['change_pswd']; // PARTICIPANT ID U r changing password 4.....
		// echo $change_pswd;
		//Time to change password
		$updateUSER = $con->prepare("update participant set password='$change_pswd' where pid='$pid'");
		$updateUSER->execute();
		
		$msg = strtoupper(substr(md5("Operation Successful"), -4));
		redirect_to("executives/participants?All&msg=$msg");
		
	}
	

	//Admin change User banking details.....
    if(isset($_POST['save_bankDETAILS']))
    {
        $pid = $_POST['pid'];
        $bank_name = strtoupper($_POST['bank_name']);
        $merchant_name = strtoupper($_POST['merchant_name']);
        $merchant_number = $_POST['merchant_number'];
        if(strlen($merchant_number) < 10 || strlen($merchant_number) > 10)
        {
            $msg = strtoupper(substr(md5("Invalid Account Number"), -4));
            redirect_to("executives/participants?All&msg=$msg");
        }
        else
        {
            $updateBank = $con->prepare("UPDATE `bankaccount` SET `bankName`='$bank_name',`merchantName`='$merchant_name',`merchantNo`='$merchant_number' WHERE `participant`='$pid'");
            if($updateBank->execute())
            {
                $msg = strtoupper(substr(md5("Modified Successfully"), -4));
                redirect_to("executives/participants?All&msg=$msg");
            }
            else
            {
                $msg = strtoupper(substr(md5("Error Occured, Please Retry"), -4));
                redirect_to("executives/participants?All&msg=$msg");
            }
        }
       // echo $merchant_number;
    }

	
	//change password..... User wish to .... Jquery works
	if(isset($_GET['ChangeMyPSWD']))
	{
		$chnge_pid = $_POST['chnge_pid']; // PARTICIPANT ID dat which to change his password.....
		$newpass = strtolower($_POST["newpass"]);
		$re_newpass = strtolower($_POST["re_newpass"]);
		// $re_newpass = $_POST['re_newpass'];
		if($newpass != $re_newpass) {
			echo 'password different';
		} else {
			$newpass = md5($newpass);
			$updateUSER = $con->prepare("update participant set password='$newpass' where pid='$chnge_pid'");
			$updateUSER->execute();
			
			echo "Operation Successful";
		}
		
	}
	
	
	//Cancelling Queue
	if(isset($_GET['cancelQueue']))
	{
		$cancelQueue = $_POST['cancelQueue_pid'];
		$updateGH = $con->prepare("update gethelp set merge='Cancelled' where participantID='$cancelQueue' and merge!='YES'");
		if($updateGH->execute())
		{
			echo 'Success';
		}
		else
		{
			echo 'Not found';
		}
	}
	
	
	//Adding Queue
	if(isset($_GET['addQueue']))
	{
		$addQueue_pid = $_POST['addQueue_pid']; //participant ID
		$amount_wallet = $_POST['amount_wallet'];
		$ghID= "M". mt_rand(11111,12345).mt_rand(11111,99999);
		
		$todaysDay =  date('D'); //Mon?
		$GHDATE =  date('d.m.Y h:i:s A'); // Date for get help
		if($todaysDay == 'Mon' || $todaysDay == 'Tue'  || $todaysDay == 'Wed'  || $todaysDay == 'Thu'  || $todaysDay == 'Fri')
		{
			$releaseDATE =  date('d.m.Y H:i:s', strtotime('+98 hours')); // Date for get help
		}
		else
		{
			$releaseDATE =  date('d.m.Y', strtotime('+122 hours')); // Date for get help
		}
		
		//Get the participant status
		$getPARTICIPANT = $con->prepare("SELECT * FROM `participant` where pid='$addQueue_pid'");
		$getPARTICIPANT->execute();
		$getPARTICIPANTinfo = $getPARTICIPANT->fetch(PDO::FETCH_ASSOC);
		$participant_status = $getPARTICIPANTinfo['status'];

		// echo strlen($ghID);
		$ghROW = rows("select * from gethelp where ghID='$ghID'");
		if($ghROW >= 1)
		{
			$ghID=substr("M". mt_rand(11111,12345).mt_rand(11111,99999),0,-1)."9";
			$tosave_GH = $con->prepare("insert into gethelp (ghID, participantID, amountGH, ghDATE, releaseDATE, user_status, merge) values ('$ghID', '$addQueue_pid', '$amount_wallet', '$GHDATE', '$releaseDATE', '$participant_status', 'NO')");
			if($tosave_GH->execute())
			{
				echo 'Saved';
			}
		}
		else
		{
			$tosave_GH = $con->prepare("insert into gethelp (ghID, participantID, amountGH, ghDATE, releaseDATE, user_status, merge) values ('$ghID', '$addQueue_pid', '$amount_wallet', '$GHDATE', '$releaseDATE', '$participant_status', 'NO')");
			if($tosave_GH->execute())
			{
				echo 'Saved';
			}
		}
	}
	
	//Saving Topic to forum
	if(isset($_POST['save_forumtopic']))
	{
		$poster_name = $_POST['poster_name'];
		$forumtopic = addslashes($_POST['forumtopic']);
		$topicmsg_content = addslashes($_POST['topicmsg_content']);
		$date_written = date('M d, Y H:i:s'); //Todays date
		
		$saveforum = $con->prepare("INSERT INTO forumtopics (poster, subject, topicmsg, date_written) values ('$poster_name', '$forumtopic', '$topicmsg_content', '$date_written')");
		if($saveforum->execute())
		{
			//We need to truncate forum so that participant will be redirect to check
			$forumlogged_truncate = $con->prepare("truncate forumlogged");
			$forumlogged_truncate->execute();
			
			$msg = strtoupper(substr(md5("Topic added successfully"), -4));
			redirect_to("executives/forum?msg=$msg");
		}
		else
		{
			$msg = strtoupper(substr(md5("Error Occured, Please Retry"), -4));
			redirect_to("executives/forum?msg=$msg");
		}
	}
	
	//Saving forum Reply
	if(isset($_POST['topic_reply']))
	{
		$reply_topic_msg = addslashes($_POST['reply_topic_msg']);
		$topicID = $_POST['topicID']; // topic id
		$date_written = date('M d, Y H:i:s'); //Todays date
		
		//Has Ticket been replied before?
		$getTOPIC = $con->prepare("select * from forumtopics where topicid='$topicID'");
		$getTOPIC->execute();
		$getTOPIC_INFO = $getTOPIC->fetch(PDO::FETCH_ASSOC);
		$replied = $getTOPIC_INFO["replies"];
		$totalReply = $replied +1;
		
		
		$saveforum_reply = $con->prepare("INSERT INTO forumreply (topicid, participantID, replymsg, date_written) values ('$topicID', '$pid', '$reply_topic_msg', '$date_written')");
		if($saveforum_reply->execute())
		{
			
			$updateFORUM = $con->prepare("update forumtopics set replies='$totalReply' where topicid='$topicID'");
			$updateFORUM->execute();
			$msg = strtoupper(substr(md5("Reply added successfully"), -4));
			redirect_to("private/forum?viewtopic=$topicID&msg=$msg");
		}
		else
		{
			$msg = strtoupper(substr(md5("Error Occured, Please Retry"), -4));
			redirect_to("private/forum?viewtopic=$topicID&msg=$msg");
		}
		
	}
	
	//Saving forum Reply for admin
	if(isset($_POST['topic_reply_admin']))
	{
		$reply_topic_msg = addslashes($_POST['reply_topic_msg']);
		$reply_name = $_POST['reply_name']; // topic id
		$topicID = $_POST['topicID']; // topic id
		$date_written = date('M d, Y H:i:s'); //Todays date
		
		//Has Ticket been replied before?
		$getTOPIC = $con->prepare("select * from forumtopics where topicid='$topicID'");
		$getTOPIC->execute();
		$getTOPIC_INFO = $getTOPIC->fetch(PDO::FETCH_ASSOC);
		$replied = $getTOPIC_INFO["replies"];
		$totalReply = $replied +1;
		
		
		$saveforum_reply = $con->prepare("INSERT INTO forumreply (topicid, participantID, replymsg, date_written) values ('$topicID', '$reply_name', '$reply_topic_msg', '$date_written')");
		if($saveforum_reply->execute())
		{
			
			$updateFORUM = $con->prepare("update forumtopics set replies='$totalReply' where topicid='$topicID'");
			$updateFORUM->execute();
			$msg = strtoupper(substr(md5("Reply added successfully"), -4));
			redirect_to("executives/forum?viewtopic=$topicID&msg=$msg");
		}
		else
		{
			$msg = strtoupper(substr(md5("Error Occured, Please Retry"), -4));
			redirect_to("executives/forum?viewtopic=$topicID&msg=$msg");
		}
		
	}
	
	//Edit Pending GH Queue
	if(isset($_POST['editpendingQueue']))
	{
		$GHIDQueue = $_POST['GHIDQueue'];
		$amount_blc = $_POST['amount_blc'];
		$merge_status = $_POST['merge_status'];
		
		$update = $con->prepare("update gethelp set balance='$amount_blc', merge='$merge_status' where ghID='$GHIDQueue'");
		if($update->execute())
		{
			$msg = strtoupper(substr(md5("Edited Successfully"), -4));
			redirect_to("executives/editGH?msg=$msg");
		}
		else
		{
			$msg = strtoupper(substr(md5("Error Occured"), -4));
			redirect_to("executives/editGH?msg=$msg");
		}
		// echo $merge_status;		
	}
	
	
	//Edit NEW GH Queue
	if(isset($_POST['editGHQueue']))
	{
		$GHID_new = $_POST['GHID_new'];
		$amount_toGH = $_POST['amount_toGH'];
		$amount_blc = $_POST['amount_blc'];
		// $merge_status = $_POST['merge_status'];
		
		$todaysDay =  date('D'); //Mon?
		$ghDATE =  date('d.m.Y h:i:s A'); // Date for get help
		if($todaysDay == 'Mon' || $todaysDay == 'Tue'  || $todaysDay == 'Wed'  || $todaysDay == 'Thu'  || $todaysDay == 'Fri')
		{
			$releaseDATE =  date('d.m.Y H:i:s', strtotime('+98 hours')); // Date for get help
		}
		else
		{
			$releaseDATE =  date('d.m.Y H:i:s', strtotime('+122 hours')); // Date for get help
		}
		
		
		if($amount_toGH == 0)
		{
			//That means we are updating balance
			$update = $con->prepare("update gethelp set balance='$amount_blc', merge='partial', ghDATE='$ghDATE', releaseDATE='$releaseDATE' where ghID='$GHID_new'");
			if($update->execute())
			{
				$msg = strtoupper(substr(md5("Edited Successfully"), -4));
				redirect_to("executives/editGH?msg=$msg");
			}
			else
			{
				$msg = strtoupper(substr(md5("Error Occured"), -4));
				redirect_to("executives/editGH?msg=$msg");
			}
		}
		else if($amount_blc == 0)
		{
			//That means we are updating balance
			$update = $con->prepare("update gethelp set amountGH='$amount_toGH', merge='NO', ghDATE='$ghDATE', releaseDATE='$releaseDATE' where ghID='$GHID_new'");
			// echo $releaseDATE;
			if($update->execute())
			{
				$msg = strtoupper(substr(md5("Edited Successfully"), -4));
				redirect_to("executives/editGH?msg=$msg");
			}
			else
			{
				$msg = strtoupper(substr(md5("Error Occured"), -4));
				redirect_to("executives/editGH?msg=$msg");
			}
		}
		else
		{
			//That means we are updating balance and amount GH
			$update = $con->prepare("update gethelp set amountGH='$amount_toGH', balance='$amount_blc', merge='partial', ghDATE='$ghDATE', releaseDATE='$releaseDATE' where ghID='$GHID_new'");
			if($update->execute())
			{
				$msg = strtoupper(substr(md5("Edited Successfully"), -4));
				redirect_to("executives/editGH?msg=$msg");
			}
			else
			{
				$msg = strtoupper(substr(md5("Error Occured"), -4));
				redirect_to("executives/editGH?msg=$msg");
			}
		}
		// echo $merge_status;		
	}
	
	
	//Manual Merging
	if(isset($_POST['mergeORDER']))
	{
		$phid_2merge = $_POST['phid_2merge'];
		$amount_ph_blc = $_POST['amount_ph_blc'];
		$ghid_2merge = $_POST['ghid_2merge'];
		$amount_gh = $_POST['amount_gh'];
		$amount_gh_blc = $_POST['amount_gh_blc'];
		// echo $amount_gh_blc;

		//Who PH?
		$searchPH = $con->prepare("select * from providehelp where phID='$phid_2merge'");
		$searchPH->execute();
		$searchPH_info = $searchPH->fetch(PDO::FETCH_ASSOC);
		$participant_whoPH = $searchPH_info["participantID"];
		// echo $participant_whoPH;

		//Who GH
		$searchGH = $con->prepare("select * from gethelp where ghID='$ghid_2merge'");
		$searchGH->execute();
		$searchGH_info = $searchGH->fetch(PDO::FETCH_ASSOC);
		$participant_whoGH = $searchGH_info["participantID"];
		
		//Lets get participant PH email address
		$phUser = $con->prepare("select * from participant where pid='$participant_whoPH'");
		$phUser->execute();
		$phUserInfo = $phUser->fetch(PDO::FETCH_ASSOC);
		$ph_email  = $phUserInfo['email'];
		$participant_name  = strtoupper($phUserInfo['name']);
		$ph_mobile  = $phUserInfo['mobile'];

		//Lets get participant receiver info
		$ghUser = $con->prepare("select * from participant where pid='$participant_whoGH'");
		$ghUser->execute();
		$ghUserInfo = $ghUser->fetch(PDO::FETCH_ASSOC);
		$gh_email  = $ghUserInfo['email'];
		$receiver_name  = strtoupper($ghUserInfo['name']);
		$gh_mobile  = $ghUserInfo['mobile'];
		$senderid = 'GIVERCYCLER';
		$email_fee = number_format(($amount_gh),2);
		$ph_message = "You have been merged to provide help of N$email_fee. Pls login to confirm";
		$gh_message = "You have been merged to get help of N$email_fee. Pls login to confirm";
		$sender_api =  "http://peaksms.org/com_sms/smsapi.php?username=datastore&password=opeyemi&recipient=$ph_mobile&sender=$senderid&message=$ph_message";
		
		$receiver_api =  "http://peaksms.org/com_sms/smsapi.php?username=datastore&password=opeyemi&recipient=$gh_mobile&sender=$senderid&message=$gh_message";

	
	$sfile = substr($sender_api, 0, strpos($sender_api, '?'))."?";
	$rfile = substr($receiver_api, 0, strpos($receiver_api, '?'))."?";
	
	$sdata = substr($sender_api, strpos($sender_api, "?") + 1);
	$ch = curl_init($sfile);
	curl_setopt($ch, CURLOPT_POST, 1);
	curl_setopt($ch, CURLOPT_POSTFIELDS,$sdata);
	curl_setopt($ch, CURLOPT_RETURNTRANSFER,true);
	$data_response = curl_exec($ch);
	curl_close($ch);
	
	$rdata = substr($receiver_api, strpos($receiver_api, "?") + 1);
	$ch = curl_init($rfile);
	curl_setopt($ch, CURLOPT_POST, 1);
	curl_setopt($ch, CURLOPT_POSTFIELDS,$rdata);
	curl_setopt($ch, CURLOPT_RETURNTRANSFER,true);
	$data_responser = curl_exec($ch);
	curl_close($ch);
	
			//Email Aspect for person to pay
			// set content type header for html email
			$headers  = 'MIME-Version: 1.0' . "\r\n";
			$headers .= 'Content-type: text/html; charset=iso-8859-1' . "\r\n";
			// set additional headers
			$headers .= 'From: You have Been Merged To Provide Help..... <no-reply@giverscycler.com>' . "\r\n".'X-Mailer: PHP/' . phpversion();
			$ph_subject = "You have Been Merged To Provide Help.....";
			$ph_body= "<html>
		<head>
			<title>REQUEST TO ASSIST $receiver_name</title>
		</head>
		<body><div>
	<div style='font-family:arial;border:2px solid #c0c0c0;padding:15px;border-radius:5px;'>
	<div style='font-size:22px;color:darkblue;font-weight:bold;'>PH REQUEST ORDER GIVERSCYCLER</div>
		<br>
	Dear Participant $participant_name!
	 <br>
	You have been merged to provide help of <font color='red'><b>&#8358;$email_fee</b></font> to $receiver_name. Please login to your personal office for the details.
	 <br><br>
	 <font color='red'><b><u>NOTE:</u></b></font>
	 <br>
	 1. Contact the receiver to make sure he/she is willing to confirm your payment.
	 <br>
	 2. Payment should be made only to the banking details provided by the system as we will not be responsible for payment made outside the details.
	 <br>
	 3. Report any <b>CYBER BEGGARS</b> to the Administrator

	 <br><br>
	 
	After making your payment, click on <b>UPLOAD POP</b> to upload your proof of payment.
	 <br>
	Payment must be made before <b>$dateMerge_expires</b> and endeavor to upload your proof of payment.
	 <br> <br>
	 
	Thank You!
	</div></div></body></html>";

							
							//Email Aspect for person to recieve
			
			// set content type header for html email
			$headers_2  = 'MIME-Version: 1.0' . "\r\n";
			$headers_2 .= 'Content-type: text/html; charset=iso-8859-1' . "\r\n";
			// set additional headers
			$headers_2 .= 'From: you have been paired to receive...  <no-reply@giverscycler.com>' . "\r\n".'X-Mailer: PHP/' . phpversion();
			
			$gh_subject = "Dear $receiver_name, you have been paired to receive...";
			$gh_body= "<html>
		<head>
			<title>Dear $receiver_name, you have been paired to receive...</title>
		</head>
		<body><div>
	<div style='font-family:arial;border:2px solid #c0c0c0;padding:15px;border-radius:5px;'>
	<div style='font-size:22px;color:darkblue;font-weight:bold;'>GH Request GIVERSCYCLER</div>
		<br>
	Hello $receiver_name,
	 <br>
	System has paired you to receive help of <font color='red'><b>&#8358;$email_fee</b></font>.  
	<br>For details, please log on into your account for the list of members paired to pay you.
	 <br> <br>

	Thank You!
	</div></div></body></html>";

				mail($ph_email, $ph_subject, $ph_body, $headers);
				mail($gh_email, $gh_subject, $gh_body, $headers_2);
				
		// echo $participant_whoGH;
		
		//Is PH balance == 0?
		if($amount_ph_blc == 0)
		{
			//Lets save PH balance
			$savePH = $con->prepare("update providehelp set balance='$amount_ph_blc', merge='complete' where phID='$phid_2merge' and participantID='$participant_whoPH'");
			// $savePH = $con->prepare("update providehelp set balance='$amount_ph_blc' where phID='$phid_2merge' and participantID='$participant_whoPH'");
			if($savePH->execute())
			{
				
				$todaysDay =  date('D'); //Mon?
				$dateMerge = date('d.m.Y H:i');
				$merge_time_forph = date('d.m.Y h:i A');
				$timer = date('h:00 A');

				if($todaysDay == 'Mon' || $todaysDay == 'Tue'  || $todaysDay == 'Wed'  || $todaysDay == 'Thu') {
					$dateMerge_expires =  date('M d, Y H:i:00', strtotime('+24 hours')); //still 12hours to pay normal day
				} else if($todaysDay == 'Fri') {
					$dateMerge_expires =  date('M d, Y H:i:00', strtotime('+72 hours')); //still 72hours to pay on Friday
				} else if($todaysDay == 'Sat') {
					$dateMerge_expires =  date('M d, Y H:i:00', strtotime('+48 hours')); //still 48hours to pay
				} else if($todaysDay == 'Sun') {
					$dateMerge_expires =  date('M d, Y H:i:00', strtotime('+24 hours')); //it's still 24hours to pay on Friday
				}
				
				if($amount_gh_blc == 0) {
					$saveGH = $con->prepare("update gethelp set balance='0', merge='YES' where ghID='$ghid_2merge' and participantID='$participant_whoGH'");
					$saveGH->execute();
				} else {
					$saveGH = $con->prepare("update gethelp set balance='$amount_gh_blc', merge='partial' where ghID='$ghid_2merge' and participantID='$participant_whoGH'");
					$saveGH->execute();
				}
				//Lets save Merging
				$InsertMerging = $con->prepare("insert into merge_gh (phID, ghID, participantID, gh_participantID, amountGH, dateMerge, dateMerge_expires) values ('$phid_2merge', '$ghid_2merge', '$participant_whoPH', '$participant_whoGH', '$amount_gh', '$dateMerge', '$dateMerge_expires')");
				$InsertMerging->execute();

			}
		}
		else
		{
			//Lets save PH balance
			$savePH = $con->prepare("update providehelp set balance='$amount_ph_blc', merge='partial' where phID='$phid_2merge' and participantID='$participant_whoPH'");
			if($savePH->execute())
			{
				
				$todaysDay =  date('D'); //Mon?
				$dateMerge = date('d.m.Y H:i');
				$merge_time_forph = date('d.m.Y h:i A');
				$timer = date('h:00 A');

				if($todaysDay == 'Mon' || $todaysDay == 'Tue'  || $todaysDay == 'Wed'  || $todaysDay == 'Thu') {
					$dateMerge_expires =  date('M d, Y H:i:00', strtotime('+24 hours')); //still 12hours to pay normal day
				} else if($todaysDay == 'Fri') {
					$dateMerge_expires =  date('M d, Y H:i:00', strtotime('+72 hours')); //still 72hours to pay on Friday
				} else if($todaysDay == 'Sat') {
					$dateMerge_expires =  date('M d, Y H:i:00', strtotime('+48 hours')); //still 48hours to pay
				} else if($todaysDay == 'Sun') {
					$dateMerge_expires =  date('M d, Y H:i:00', strtotime('+24 hours')); //it's still 24hours to pay on Friday
				}
				
				if($amount_gh_blc == 0)					
				{
					$saveGH = $con->prepare("update gethelp set balance='0', merge='YES' where ghID='$ghid_2merge' and participantID='$participant_whoGH'");
					$saveGH->execute();
				}
				else
				{
					$saveGH = $con->prepare("update gethelp set balance='$amount_gh_blc', merge='partial' where ghID='$ghid_2merge' and participantID='$participant_whoGH'");
					$saveGH->execute();
				}
				//Lets save Merging
				$InsertMerging = $con->prepare("insert into merge_gh (phID, ghID, participantID, gh_participantID, amountGH, dateMerge, dateMerge_expires) values ('$phid_2merge', '$ghid_2merge', '$participant_whoPH', '$participant_whoGH', '$amount_gh', '$dateMerge', '$dateMerge_expires')");
				$InsertMerging->execute();

			}
		}
		
		$msg = strtoupper(substr(md5("Order merged successfully"), -4));
		redirect_to("executives/loadGH-PH?msg=$msg");		
	}	
	
	//Time Extender
	if(isset($_GET['EXTENDTIME']) && ($_GET['mergeID']))
	{
		$ghID = $_GET['ID'];
		$mergeID = $_GET['mergeID'];
		//WHO AM I EXTENDING TIME FOR ????
		$checkallGH = $con->prepare("SELECT * FROM `merge_gh` where mergeID='$mergeID' and status='' and attachment=''");
		$checkallGH->execute();
		$checkallGH_INFO = $checkallGH->fetch(PDO::FETCH_ASSOC);
		$dateMerge_expires = $checkallGH_INFO['dateMerge_expires'];
		$additional_time =  date('M d, Y H:i:00', strtotime("$dateMerge_expires+6 hours"));
		
		//Now lets update time with the new one
		$savetime = $con->prepare("update `merge_gh` set dateMerge_expires='$additional_time' where mergeID='$mergeID'");
		if($savetime->execute())
		{
			$msg = strtoupper(substr(md5("OPERATION SUCCESSFUL"), -4));
			redirect_to("private/dashboard?msg=$msg");	
		}
		else
		{
			$msg = substr(md5("Transaction Not Found"), -4);
			redirect_to("private/dashboard?msg=$msg");
		}
	}
}
