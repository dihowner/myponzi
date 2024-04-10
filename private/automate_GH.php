<?PHP
//require "../config.php";
#########################
# Participant does not need to login before he or she will be gh
#########################
//What's today day?
$todaysDay =  date('D'); //Mon?
$ghID="M". mt_rand(11111,12345).mt_rand(11111,99999); //Pledge Id

$GHDATE =  date('d.m.Y h:i:s A'); // Date for get help
if($todaysDay == 'Mon' || $todaysDay == 'Tue'  || $todaysDay == 'Wed'  || $todaysDay == 'Thu'  || $todaysDay == 'Fri')
{
	$releaseDATE =  date('d.m.Y H:i:s', strtotime('+98 hours')); // Date for get help
}
else
{
	$releaseDATE =  date('d.m.Y', strtotime('+122 hours')); // Date for get help
}

// echo $releaseDATE;
//Automated GH
$total_GH = $con->prepare("select * from providehelp where status='Confirmed' and paid='YES' order by rand()");
$total_GH->execute();
$total_return_amnt = 0;


	//We do not need to use for loop else user get additional income
	$GHinfo = $total_GH->fetch(PDO::FETCH_ASSOC);
	$return_amnt = $GHinfo['return_amnt'];
	$participantID = $GHinfo['participantID'];
	$phID = $GHinfo['phID'];
	$releaseDATE_ph = $GHinfo['releaseDATE']; //Release Date
	$total_return_amnt += $return_amnt;
	$todaysDATE = date('d.m.Y h:i A');
	
	//Get the participant status
	$getPARTICIPANT = $con->prepare("SELECT * FROM `participant` where pid='$participantID'");
	$getPARTICIPANT->execute();
	$getPARTICIPANTinfo = $getPARTICIPANT->fetch(PDO::FETCH_ASSOC);
	$participant_status = $getPARTICIPANTinfo['status'];
	$participant_email = $getPARTICIPANTinfo['email'];
	$participant_name = $getPARTICIPANTinfo['name'];
		
		
		//Email Aspect for person to pay
		// set content type header for html email
		$headers  = 'MIME-Version: 1.0' . "\r\n";
		$headers .= 'Content-type: text/html; charset=iso-8859-1' . "\r\n";
		// set additional headers
		$headers .= 'From: GET HELP REQUEST ..... <no-reply@giverscycler.com>' . "\r\n".'X-Mailer: PHP/' . phpversion();
		$autogh_subject = "GET HELP REQUEST.....";
		$email_fee = number_format($total_return_amnt);
		$autogh_msg= "<html>
    <head>
        <title>GET HELP REQUEST</title>
    </head>
    <body><div>
<div style='font-family:arial;border:2px solid #c0c0c0;padding:15px;border-radius:5px;'>
<div style='font-size:22px;color:darkblue;font-weight:bold;'>GET HELP REQUEST ORDER GIVERSCYCLER</div>
    <br>
Dear Participant $participant_name!
 <br>

GET HELP Request of <font color='red'><b>&#8358;$email_fee</b></font> was added to your account.

 <br><br>
 <font color='red'><b><u>NOTE:</u></b></font>
 <br>
 You will be merged starting from 12hours - 7days
 <br>
 DO inform your friend about <b>GIVERSCYCLER</b>
 
 <br>
 
 <b>Remember: <i>Givers are Receivers</i></b>

 <br>
  Thank You!
 

</div></div></body></html>";
	// echo 12;
	
if($releaseDATE_ph <= $todaysDATE) //Release Date must be lesser than or equal to Todays Date
{
	
	if($total_return_amnt !=0)
	{
		
		//Lets turn it to withdraw
		$savePH = $con->prepare("update providehelp set status='Withdraw' where phID='$phID' and participantID='$participantID'");
		$savePH->execute();
		
		$toCollect_GH = $total_return_amnt; // Total sum of GH
		$tosave_GH = $con->prepare("insert into gethelp (ghID, participantID, amountGH, ghDATE, releaseDATE, user_status) values ('$ghID', '$participantID', '$toCollect_GH', '$GHDATE', '$releaseDATE', '$participant_status')");
		$tosave_GH->execute();
		
		mail($participant_email, $autogh_subject, $autogh_msg, $headers);
	}
}
?>