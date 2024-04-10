<?php
echo  "M". mt_rand(11111,12345).mt_rand(11111,99999). '<br>';
echo 'GH date: ' . date('d.m.Y h:i:s A') . '<br>';
echo 'Release date: ' . date('d.m.Y H:i:s', strtotime('+98 hours'));
// echo date('M d, Y H:i:00', strtotime('+98 hours'));
?>