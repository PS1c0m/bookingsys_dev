<?php
if (substr_count($_SERVER['HTTP_ACCEPT_ENCODING'], 'gzip')) ob_start("ob_gzhandler"); else ob_start();
require_once "/../include/Session.php";
$session = new Session();
if (isset($session->user) && ($session->user->usertype === 'peakasutaja')){
 if(isset($_POST["room_nr"]) && isset($_POST["room_id"])){
  if ( $_POST["room_nr"] === '' || $_POST['room_size'] === ''){
    echo "error1"; //Room name and size cannot be empty, server side check
  } elseif (!(is_numeric($_POST['room_size']))){
    echo "error4"; //  Room size must be numeric the size has to be numberic, server side check
  } else {
        require_once "/../include/db.php";
        $table = "room";
        $room_id = $_POST['room_id'];
        $room_nr = $_POST['room_nr'];
        $room_type = $_POST['room_type'];
        $room_description = $_POST['room_description'];
        $room_size = $_POST['room_size']; 

        $check_room = R::findOne($table, 'room_nr = ?', array($room_nr));
        if (isset($check_room) && $check_room->id != $room_id){
          echo "error2"; //Room name allready existst cannot change, dublicate entry, server-side check
        } else {
            $room_exists = R::findOne($table, 'id = ?', array($room_id));
            if (isset($room_exists)){ //Room exists, make the changes
                 $room_exists->room_nr =  $room_nr;
                 $room_exists->type = $room_type;
                 $room_exists->description = $room_description;
                 $room_exists->size = $room_size;
                 R::store($room_exists);
                 echo "success";
            } else { //Room does not exist in DB, server-side check
                 echo "error3";       
            }
        }
    }
 }
}
?>