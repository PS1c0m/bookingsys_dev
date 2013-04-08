<?php
if (substr_count($_SERVER['HTTP_ACCEPT_ENCODING'], 'gzip')) ob_start("ob_gzhandler"); else ob_start();
require_once "/../include/Session.php";
$session = new Session();
if (isset($session->user) && ($session->user->usertype === 'peakasutaja')){
  if(isset($_POST["room_nr"]))
  {
    require_once "/../include/db.php";
    $table = "room";
     $room_nr = $_POST['room_nr'];
     $room_type = $_POST['room_type'];
     $room_description = $_POST['room_description'];
     $room_size = $_POST['room_size'];
     $room_exists = R::findOne($table, 'room_nr = ?', array($room_nr));

     if (isset($room_exists)){ //Room allready exists
        echo "error";
     } else { //Room does not exist, add new room to DB
       $room = R::dispense($table);
       $room->room_nr =  $room_nr;
       $room->type = $room_type;
       $room->description = $room_description;
       $room->size = $room_size;
       R::store($room);
       echo "success";
     }
  }
}
?>