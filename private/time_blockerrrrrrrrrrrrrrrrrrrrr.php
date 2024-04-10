<?php
/*
require '../config.php';
$todaysDATE =  date('M d, Y H:i:00');
//Failed GH
$getallGH_notpaid = $con->prepare("SELECT * FROM `merge_gh` where attachment=''");
$getallGH_notpaid->execute();
$getallGH_notpaid_row = rows("SELECT * FROM `merge_gh` where attachment='' and status=''");
// print_r($getallGH_notpaid);
//if($getallGH_notpaid_row == 0 )
// {
	
// }
// else
// {
	// for($i=1; $i<=$getallGH_notpaid_row; $i++)
	// {	
		$getallGH_notpaidINFO = $getallGH_notpaid->fetch(PDO::FETCH_ASSOC);
		$dateMerge_expires = $getallGH_notpaidINFO['dateMerge_expires'];
		$attachment = $getallGH_notpaidINFO['attachment'];
		$ghID = $getallGH_notpaidINFO['ghID'];
		$phID = $getallGH_notpaidINFO['phID'];
		$participantID = $getallGH_notpaidINFO['participantID']; // User dat PH
		$amountGH = $getallGH_notpaidINFO['amountGH']; //Amount Merge
		
		//User gets additional 6 hours to make payment, if time clocks and it wasnt paid, user gets block
		//Trust betrayed
		$additional_time =  date('M d, Y H:i:00', strtotime("$dateMerge_expires+6 hours"));
		// Referral gets 10% only
		$referralAMNT = $amountGH / 10;
		// echo $dateMerge_expires . '<br>';
		
		// Time Elapse ??? Fuck U 4 not paying, trust betrayed
		if($additional_time <= $todaysDATE)
		{
			//We need to block participant who is to make payment
			$blockUSER = $con->prepare("update participant set status='blocked' where pid='$participantID'");
			
			if($blockUSER->execute())
			{
				//Delete PH first
				$cancelPH = $con->prepare("update providehelp set status='Cancelled' where phID='$phID' and participantID='$participantID' and amntPH='$amountGH'");
				$cancelPH->execute();
					
				//We need to block participant from being merged to receive
				$cancelGH = $con->prepare("update gethelp set user_status='blocked' where participantID='$participantID' and amountGH='$amountGH'");
				$cancelGH->execute();
				
				//Delete referral bonus
				$cancelReferral_BONUS = $con->prepare("update referral set status='Cancelled' where phID='$phID' and participantID='$participantID' and referralBonus='$referralAMNT'");
				$cancelReferral_BONUS->execute();
				print_r($cancelReferral_BONUS);
			
				
				//Now we need to remove pending GH wish was not paid
				$selectGH = $con->prepare("SELECT * FROM `gethelp` where ghID='$ghID'");
				$selectGH->execute();
				$selectGH_INFO = $selectGH->fetch(PDO::FETCH_ASSOC);
				$amount_userGH = $selectGH_INFO['amountGH'];
				$balance_inGH = $selectGH_INFO['balance'];
				$gh_participantID = $selectGH_INFO['participantID']; // User dat is expecting MONEY
				$merge_status = $selectGH_INFO['merge']; // User dat is expecting MONEY
				//Has user been merged to receive all his GH, and one refuses to pay him or her
				if($balance_inGH == 0)
				{
					$addblc_back = $con->prepare("update gethelp set balance='$amountGH', merge='partial' where ghID='$ghID' and participantID='$gh_participantID'");
					$addblc_back->execute();
				}
				else if($balance_inGH != 0 && $merge_status == 'partial') //Nobody Paid???
				{
					$totalbalance = $balance_inGH + $amountGH;
					
					//Chaiiii, User has to go back on queuee, what d fuck
					// Atleast I no fit use my money pay now, Sorry Case
					
					if($amount_userGH == $totalbalance)
					{
						//We need to return GH participant to begining 
						$addGH_back = $con->prepare("update gethelp set balance='0', merge='NO' where ghID='$ghID' and participantID='$gh_participantID' and amountGH='$totalbalance'");
						$addGH_back->execute();	
						
					}
				}
				//Cancel Merge
				$deleteMERGE = $con->prepare("update merge_gh set status='Cancelled', attachment='Cancelled' where phID='$phID' and ghID='$ghID' and participantID='$participantID' and amountGH='$amountGH' and status=''");
				$deleteMERGE->execute();
				
			
			}
			
			echo $amountGH;
		}
	// }
// }



*/
?>