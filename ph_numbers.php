PH Numbers<br><br><br>
<?php
require  "config.php";

$allParticipant = $con->prepare("select * from merge_gh where attachment='' and status=''");
$allParticipant->execute();
for($i=1; $i<=rows("select * from merge_gh where attachment='' and status=''"); $i++)
{
	$allParticipant_info = $allParticipant->fetch(PDO::FETCH_ASSOC);
	$participantID = $allParticipant_info["participantID"];
	//Lets get participant receiver info
	$ghUser = $con->prepare("select * from participant where pid='$participantID'");
	$ghUser->execute();
	$ghUserInfo = $ghUser->fetch(PDO::FETCH_ASSOC);
	$gh_mobile  = $ghUserInfo['mobile'];
	//numbers Needed
	echo str_replace("\n", ",",str_replace(" ", "", $gh_mobile)) . '<br>';
}
?>

