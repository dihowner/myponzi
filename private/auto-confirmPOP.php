<?php

###########################
#
# Since it's 72hours, then we need to auto confirm
# Wetin You dey do wey u no fit confirm or flag payment since???
# It's confirmed and not reversal
#
# CRON JOB !!!
#
# You must come here every 1secs to refresh this file!!!
###########################

require "../config.php";

$todaysDATE = date('d.m.Y h:i A');
$getallGH = $con->prepare("SELECT * FROM merge_gh where attachment !='' and status='Upload'");
$getallGH->execute();
for($i=1; $i<=rows("SELECT * FROM merge_gh where attachment !='' and status='Upload'"); $i++)
{
	$getallGH_INFO = $getallGH->fetch(PDO::FETCH_ASSOC);
	$auto_confirm = $getallGH_INFO["auto_confirm"];
	$phID = $getallGH_INFO["phID"];
	$ghID = $getallGH_INFO["ghID"];
	$mergePH_participantID = $getallGH_INFO["participantID"];
	$amountGH = $getallGH_INFO["amountGH"];
	
	// echo $amountGH;
	// echo $amountGH;
	if($auto_confirm <= $todaysDATE) //Upto 72hours and Payment not flag as fake pop??? Then we assume it was received
	{
		$checkPH_row = rows("select * from providehelp where phID='$phID' and paid='NO' and status='Unconfirmed'");
		if($checkPH_row == 1)
		{
			$searchPH = $con->prepare("select * from providehelp where phID='$phID' and paid='NO' and status='Unconfirmed'");
			$searchPH->execute();
			$searchPH_info = $searchPH->fetch(PDO::FETCH_ASSOC);
			$balance = $searchPH_info['balance'];
			$amntPH = $searchPH_info['amntPH'];
			$amount_confirm = $searchPH_info['amount_confirm'];
			//Is PH == GH and balance == 0
			
			$releaseDATE = date('d.m.Y h:i A', strtotime("+24 hours"));
			
			if($amntPH == $amountGH && $balance == 0)
			{
				//Since u did not confirm the payment, left to System
				$totalConfirm = $amount_confirm + $amountGH;
				
				//Update PH
				$updatePH = $con->prepare("update providehelp set paid='YES', status='Confirmed', releaseDATE='$releaseDATE' where phID='$phID' and amntPH='$amountGH'");
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
		
				// echo 12;
			}
			else 
			{
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
					$updatePH = $con->prepare("update providehelp set paid='YES', merge='complete', status='Confirmed', amount_confirm='$amount_confirm_pay', releaseDATE='$releaseDATE' where phID='$phID' and amntPH='$amountGH'");
					$updatePH->execute();
					

				}
				else if($amount_confirm_pay != $amntPH)
				{
					// / We have to update merge GH
					$updateGH = $con->prepare("update merge_gh set status='Confirmed' where ghID='$ghID' and phID='$phID'");
					$updateGH->execute();
				
					
					//Update PH, U need to pay all your money oooo, no refferal GH yet
					$updatePH = $con->prepare("update providehelp set amount_confirm='$amount_confirm_pay' where phID='$phID' and amntPH='$amntPH'");
					$updatePH->execute();
				
				}
			}
		}
		// echo $i .'||' . $auto_confirm . '<br><br>';
		// echo $;
	}
}
?>