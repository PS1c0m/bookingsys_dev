<?php
require_once "/../include/Session.php";
$session = new Session();
if (isset($session->user)){
  if(isset($_POST["id"])){
    require_once "/../include/db.php";
     $table = "event";
     $event_id = $_POST['id'];
     $event_exists = R::findOne($table, 'id = ?', array($event_id));
     if (isset($event_exists)){ //Event exists
         R::trash($event_exists);
         echo "success";
     } else { //Event does not exist in DB
      echo "error";
     }
  } else {
    header("location: ../index.php"); exit();
  }
} else {
  unset($session->user);
  header("location: ../index.php"); exit();
}
?>