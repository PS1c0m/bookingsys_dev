<?php 
date_default_timezone_set('Europe/Tallinn');
$changing_date = date('Y-m-d H:i:s');  
echo  $changing_date;

$date_start = strtotime("2013-03-31 07:30");
$date_end = strtotime("2013-03-30 14:00");

 $subtract = floor($date_end - $date_start);
 $remainhour=floor($subtract/3600);
echo "</br>";
echo $remainhour;
echo "</br>";
if ($remainhour < 0){
 echo "error";
}
if ($remainhour >= 24){
echo "allDay";
}
require_once "include/db.php";
$event_type ='Seminar';
$event_user = 'erikno';
$room_nr_pick = 'IT-190';

$user_id = R::findOne('user', 'username = ?', array($event_user));
$room_id = R::findOne('room', 'room_nr = ?', array($room_nr_pick));
$type_id = R::findOne('typeinfo', 'type = ?', array($event_type));
echo "</br>";
echo $user_id->id;
echo "</br>";
echo $room_id->id;
echo "</br>";
echo $type_id->id;
?>