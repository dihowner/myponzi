<?php


/*********
This part is very complicating but looks easy if you understand the algorithm of merging User

MERGING is base on 4steps

Step 1: USER PH = USER GH: Merge

Step 2: User PH is greater than User GH then remove it from it and update balance for user 

Step 3: Available balance is equal to User GH: Merge

Step 4: Available balance is greater than user GH: Substract User GH from it and Merge then update balance; if user balance is same as another user GH, Step 3 else Continue step 4 till it gets to Zero


**********/


/*************MAKE IT A CONSTANT VARIABLE ***********/


//What's today day?
$todaysDay =  date('D'); //Mon?
//What's today date?
$dateMerge = date('d.m.Y H:i');

if($todaysDay == 'Mon' || $todaysDay == 'Tue'  || $todaysDay == 'Wed'  || $todaysDay == 'Thu' || $todaysDay == 'Fri')
{
	$dateMerge_expires =  date('M d, Y H:i:00', strtotime('+14 hours')); //still 12hours to pay normal day
}
else if($todaysDay == 'Fri' && $timer > '09:00 PM')
{
	$dateMerge_expires =  date('M d, Y H:i:00', strtotime('+74 hours')); //still 72hours to pay on Friday
}
else if($todaysDay == 'Sat')
{
	$dateMerge_expires =  date('M d, Y H:i:00', strtotime('+50 hours')); //still 48hours to pay
}
else if($todaysDay == 'Sun')
{
	$dateMerge_expires =  date('M d, Y H:i:00', strtotime('+26 hours')); //it's still 24hours to pay on Friday
}

/**************CONSTANT VARIABLE********************/
$senderid = 'GIVERCYCLER';
$sms_username = 'giverscycler';
$sms_password = '12345';



#########
# We need t order it by GH date...... U might use same day but seconds differentiate US!!!
#########
$merge_time_forph = date('d.m.Y h:i A');
$allGH = $con->prepare("SELECT * FROM `gethelp` where user_status='active' order by ID ASC");

$allGH->execute();
$allGH_row = rows("SELECT * FROM `gethelp` where user_status='active' order by ID ASC");
// for($i=1; $i<=$allGH_row; $i++)
// {
	$allGHInfo = $allGH->fetch(PDO::FETCH_ASSOC);
	$amountGH = $allGHInfo['amountGH']; //Amount user GH
	$ghID = $allGHInfo['ghID']; // GH id
	$ID = $allGHInfo['ID']; // id of each GH in auto_increment value
	$gh_participantID = $allGHInfo['participantID']; // id of each GH in auto_increment value
	$merge_gh_status = $allGHInfo['merge']; // id of each GH in auto_increment value
	$balance_4GH = $allGHInfo['balance']; // id of each GH in auto_increment value
	
	if($balance_4GH == 0 && $merge_gh_status == 'NO')
	{
		//We can't merge a single user to pay U
		$slash_fee = $amountGH / 2;
		
		//Checking ph row so as not to merge participant that GH to pay his PH to him or herself
		$searchPH = $con->prepare("SELECT * FROM `providehelp` WHERE participantID != '$gh_participantID' and status = 'Unconfirmed' and paid='NO' and merge='NO' and RegBonus='0' order by RAND() limit 1");
		$searchPH->execute();
		$searchPH_row = rows("SELECT * FROM `providehelp` WHERE participantID != '$gh_participantID' and status = 'Unconfirmed' and paid='NO' and merge='NO' and RegBonus='0' order by RAND() limit 1");
		for($i=1; $i<=$searchPH_row; $i++)
		{	
			$searchPHinfo = $searchPH->fetch(PDO::FETCH_ASSOC);
			$amntPH = $searchPHinfo['amntPH'];
			$participantID = $searchPHinfo['participantID'];
			$balance = $searchPHinfo['balance'];
			$participantID_pledge = $searchPHinfo['phID'];
			$merge_hour = $searchPHinfo['merge_hour'];
			
			//Lets get participant PH email address
			$phUser = $con->prepare("select * from participant where pid='$participantID'");
			$phUser->execute();
			$phUserInfo = $phUser->fetch(PDO::FETCH_ASSOC);
			$ph_email  = $phUserInfo['email'];
			$participant_name  = strtoupper($phUserInfo['name']);
			$ph_mobile  = $phUserInfo['mobile'];
			
			
			//Lets get participant receiver info
			$ghUser = $con->prepare("select * from participant where pid='$gh_participantID'");
			$ghUser->execute();
			$ghUserInfo = $ghUser->fetch(PDO::FETCH_ASSOC);
			$gh_email  = $ghUserInfo['email'];
			$receiver_name  = strtoupper($ghUserInfo['name']);
			$gh_mobile  = $ghUserInfo['mobile'];
			
			if($merge_hour <= $merge_time_forph)
			{
				if($participantID != $gh_participantID)
				{
					if($slash_fee == $amntPH)
					{
						//Before saving it to DB, wait.....
						// Have u forgotten we are paying half first
						//Save / keep Balance
						$merge_save = $con->prepare("Insert into merge_gh (phID, ghID, participantID, gh_participantID, amountGH, dateMerge, dateMerge_expires) values ('$participantID_pledge', '$ghID', '$participantID', '$gh_participantID', '$slash_fee', '$dateMerge', '$dateMerge_expires')");
						
						// echo 'GH No '.$gh_mobile . ' PH No ' . $ph_mobile;
						if($merge_save->execute())
						{
							//Update PH
							$updatePH = $con->prepare("update providehelp set merge='complete', balance='0' where amntPH='$slash_fee' and phID='$participantID_pledge' and RegBonus='0' and participantID='$participantID'");
							$updatePH->execute();
							
					$email_fee = number_format(($slash_fee),2);
					$ph_message = str_replace(" ", "%20", "You have been merged to provide help of N$email_fee. Pls login to confirm");
					$gh_message = str_replace(" ", "%20", "You have been merged to get help of N$email_fee. Pls login to confirm");
					$sender_api =  "http://developers.cloudsms.com.ng/api.php?userid=24779922&password=XR_PJxtL&type=0&destination=$ph_mobile&sender=$senderid&message=$ph_message";
							
					$data_sender = file_get_contents($sender_api);
					
					// $file_sender = "http://peaksms.org/components/com_spc/smsapi.php?";
					// $data_sender = "username=".$sms_username ."&password=".$sms_password ."&sender=".$senderid."&recipient=".$ph_mobile."&message=".$ph_message;
					// $joinfiledata_sender = $file_sender.$data_sender;
					// $ch_sender = curl_init($file_sender);
					// curl_setopt($ch_sender, CURLOPT_POST, 1);
					// curl_setopt($ch_sender, CURLOPT_POSTFIELDS,$data_sender);
					// curl_setopt($ch_sender, CURLOPT_RETURNTRANSFER,true);
					// $data_sender = curl_exec($ch_sender);
					// curl_close($ch_sender);
					
					//Has payee receive SMS? then receiver should get also
					
					if($data_sender)
					{
					
						$receiver_api =  "http://developers.cloudsms.com.ng/api.php?userid=24779922&password=XR_PJxtL&type=0&destination=$gh_mobile&sender=$senderid&message=$gh_message";
						file_get_contents($receiver_api);
					
						// $file_rcver = "http://peaksms.org/components/com_spc/smsapi.php?";
						// $data_rcver = "username=".$sms_username ."&password=".$sms_password ."&sender=".$senderid."&recipient=".$gh_mobile."&message=".$gh_message;
						// $joinfiledata_rcv = $file_rcver.$data_rcver;
						// $ch_rcver = curl_init($file_rcver);
						// curl_setopt($ch_rcver, CURLOPT_POST, 1);
						// curl_setopt($ch_rcver, CURLOPT_POSTFIELDS,$data_rcver);
						// curl_setopt($ch_rcver, CURLOPT_RETURNTRANSFER,true);
						// $data_rcver = curl_exec($ch_rcver);
						// curl_close($ch_rcver);
					}
					
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
							
							//Since User is unable to get his full money at a go , then he has a balance left
							$updateGH = $con->prepare("update gethelp set merge='partial', balance='$slash_fee' where ghID='$ghID' and participantID='$gh_participantID'");
							$updateGH->execute();
						}
						// echo $amntPH . $participantID_pledge; 
					}
				}
			// echo $amountGH;
			}
		}
	}
	else if($balance_4GH > 0 && $merge_gh_status == 'partial')
	{
		$searchPH = $con->prepare("SELECT * FROM `providehelp` WHERE amntPH='$balance_4GH' and participantID != '$gh_participantID' and status='Unconfirmed' and paid='NO' and merge='NO' and RegBonus='0' order by RAND() limit 1");
		$searchPH->execute();
		$searchPH_row = rows("SELECT * FROM `providehelp` WHERE amntPH='$balance_4GH' and participantID != '$gh_participantID' and status='Unconfirmed' and paid='NO' and merge='NO' and RegBonus='0' order by RAND() limit 1");
		for($i=1; $i<=$searchPH_row; $i++)
		{
			$searchPHinfo = $searchPH->fetch(PDO::FETCH_ASSOC);
			$amntPH = $searchPHinfo['amntPH'];
			$participantID = $searchPHinfo['participantID'];
			$participantID_pledge = $searchPHinfo['phID'];
			$merge_hour = $searchPHinfo['merge_hour'];
			$leftGH = $balance_4GH - $amntPH;
			
		
			//Lets get participant PH email address
			$phUser = $con->prepare("select * from participant where pid='$participantID'");
			$phUser->execute();
			$phUserInfo = $phUser->fetch(PDO::FETCH_ASSOC);
			$ph_email  = $phUserInfo['email'];
			$participant_name  = strtoupper($phUserInfo['name']);
			$ph_mobile  = $phUserInfo['mobile'];
			
			
			//Lets get participant receiver info
			$ghUser = $con->prepare("select * from participant where pid='$gh_participantID'");
			$ghUser->execute();
			$ghUserInfo = $ghUser->fetch(PDO::FETCH_ASSOC);
			$gh_email  = $ghUserInfo['email'];
			$gh_mobile  = $ghUserInfo['mobile'];
			$receiver_name  = strtoupper($ghUserInfo['name']);
			
			
			if($merge_hour <= $merge_time_forph)
			{
				$merge_save = $con->prepare("Insert into merge_gh (phID, ghID, participantID, gh_participantID, amountGH, dateMerge, dateMerge_expires) values ('$participantID_pledge', '$ghID', '$participantID', '$gh_participantID', '$amntPH', '$dateMerge', '$dateMerge_expires')");
				if($merge_save->execute())
				{
					//since it has merge user then let's stop multiple merging
					//Update GH
					$updateGH = $con->prepare("update gethelp set merge='YES', balance='$leftGH' where ghID='$ghID' and participantID='$gh_participantID'");
					$updateGH->execute();
					

						
					$email_fee = number_format(($amntPH),2);
					$ph_message = str_replace(" ", "%20", "You have been merged to provide help of N$email_fee. Pls login to confirm");
					$gh_message = str_replace(" ", "%20", "You have been merged to get help of N$email_fee. Pls login to confirm");
					// $gh_message = "You have been merged to get help of N$email_fee. Pls login to confirm";
					// $file_sender = "http://peaksms.org/components/com_spc/smsapi.php?";
					// $data_sender = "username=".$sms_username ."&password=".$sms_password ."&sender=".$senderid."&recipient=".$ph_mobile."&message=".$ph_message;
					// $joinfiledata_sender = $file_sender.$data_sender;
					// $ch_sender = curl_init($file_sender);
					// curl_setopt($ch_sender, CURLOPT_POST, 1);
					// curl_setopt($ch_sender, CURLOPT_POSTFIELDS,$data_sender);
					// curl_setopt($ch_sender, CURLOPT_RETURNTRANSFER,true);
					// $data_sender = curl_exec($ch_sender);
					// curl_close($ch_sender);
					
					$sender_api =  "http://developers.cloudsms.com.ng/api.php?userid=24779922&password=XR_PJxtL&type=0&destination=$ph_mobile&sender=$senderid&message=$ph_message";
							
					$data_sender = file_get_contents($sender_api);
					if($data_sender)
					{
						$receiver_api =  "http://developers.cloudsms.com.ng/api.php?userid=24779922&password=XR_PJxtL&type=0&destination=$gh_mobile&sender=$senderid&message=$gh_message";
						file_get_contents($receiver_api);
						
						// $file_rcver = "http://peaksms.org/components/com_spc/smsapi.php?";
						// $data_rcver = "username=".$sms_username ."&password=".$sms_password ."&sender=".$senderid."&recipient=".$gh_mobile."&message=".$gh_message;
						// $joinfiledata_rcver = $file_rcver.$data_rcver;
						// $ch_rcver = curl_init($file_rcver);
						// curl_setopt($ch_rcver, CURLOPT_POST, 1);
						// curl_setopt($ch_rcver, CURLOPT_POSTFIELDS,$data_rcver);
						// curl_setopt($ch_rcver, CURLOPT_RETURNTRANSFER,true);
						// $data_rcver = curl_exec($ch_rcver);
						// curl_close($ch_rcver);
						
					}
					
					
							//Email Aspect for person to pay
			
			// set content type header for html email
			$headers_ph  = 'MIME-Version: 1.0' . "\r\n";
			$headers_ph .= 'Content-type: text/html; charset=iso-8859-1' . "\r\n";
			// set additional headers
			$headers_ph .= 'From: '. "You've Been Merged To Provide Help..... <no-reply@giverscycler.com>" . "\r\n".'X-Mailer: PHP/' . phpversion();
			$ph_subject = "You've Been Merged To Provide Help.....";
			$ph_body= "<html>
		<head>
			<title>REQUEST TO ASSIST $receiver_name</title>
		</head>
		<body><div>
	<div style='font-family:arial;border:2px solid #c0c0c0;padding:15px;border-radius:5px;'>
	<div style='font-size:22px;color:darkblue;font-weight:bold;'>PH Request GIVERSCYCLER</div>
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
			$headers  = 'MIME-Version: 1.0' . "\r\n";
			$headers .= 'Content-type: text/html; charset=iso-8859-1' . "\r\n";
			// set additional headers
			$headers .= 'From: you have been paired to receive... <no-reply@giverscycler.com>' . "\r\n".'X-Mailer: PHP/' . phpversion();
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
	<br><br>
	System has paired you to receive help of <font color='red'><b>&#8358;$email_fee</b></font>. <br><br>For details, please log on into your account for the list of members paired to pay you.
	<br><br><br>Thank You!
	</div></div></body></html>";

					mail($ph_email, $ph_subject, $ph_body, $headers_ph);
					mail($gh_email, $gh_subject, $gh_body, $headers);
					//Update PH
					$updatePH = $con->prepare("update providehelp set merge='complete', balance='0' where amntPH='$amntPH' and phID='$participantID_pledge' and RegBonus='0' and participantID='$participantID'");
					$updatePH->execute();
					// echo 'Merged';
				}
		
			
			}
		}
	}
// }

