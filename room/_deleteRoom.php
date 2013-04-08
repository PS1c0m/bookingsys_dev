<?php
if (substr_count($_SERVER['HTTP_ACCEPT_ENCODING'], 'gzip')) ob_start("ob_gzhandler"); else ob_start();
require_once "/../include/Session.php";
$session = new Session();
if (isset($session->user) && ($session->user->usertype === 'peakasutaja')){
  if(isset($_POST["room_nr"])){
    require_once "/../include/db.php";
     $table = "room";
     $room_nr = $_POST['room_nr'];
     $room_exists = R::findOne($table, 'room_nr = ?', array($room_nr));
     if (isset($room_exists)){ //Room exists
         R::trash($room_exists);
         echo "success";
     } else { //Room does not exist in DB
      echo "error";
     }
  }
}
?>