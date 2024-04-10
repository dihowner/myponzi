<?php

$todaysDay =  date('D'); //Mon?
$dateMerge = date('d.m.Y H:i');

$merge_time_forph = date('d.m.Y h:i A');
$timer = date('h:00 A');

if($todaysDay == 'Mon' || $todaysDay == 'Tue'  || $todaysDay == 'Wed'  || $todaysDay == 'Thu' || $todaysDay == 'Fri')
{
	$dateMerge_expires =  date('M d, Y H:i:00', strtotime('+14 hours')); //still 12hours to pay normal day
}
else if($todaysDay == 'Fri' && $timer > '02:00 PM')
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

echo 'Date Merge: ' . $dateMerge . '<br>';
echo 'Date Merge Expires: ' . $dateMerge_expires;

?>