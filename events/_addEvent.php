<?php
require_once "/../include/Session.php";
$session = new Session();
if (isset($session->user)){
  if(isset($_POST["event_heading"]) && isset($_POST['date-start']) && isset($_POST['date-end'])){
    if( $_POST["event_heading"] === '' || $_POST["date-start"] === '' || $_POST['date-end'] === '' ){
      echo "error2"; //väljad täitmata
    } else {
      require_once "/../include/db.php";
      $table = "event";
      date_default_timezone_set('Europe/Tallinn');
       $changing_date = date('Y-m-d H:i:s');   
       $last_changed_user = $session->user->username;
       $event_heading = $_POST['event_heading'];
       $date_start = $_POST['date-start'];
       $date_end = $_POST['date-end'];
       $event_description = $_POST['event_description'];

       $event_type = $_POST['event_type'];
       $event_user = $_POST['event_user'];
       $room_nr = $_POST['room_name'];

        //Get FK ID's
        $user_id = R::findOne('user', 'username = ?', array($event_user));
        $room_id = R::findOne('room', 'room_nr = ?', array($room_nr));
        $type_id = R::findOne('typeinfo', 'type = ?', array($event_type));
        //Serverside check if the ending date is later then starting date and get to knpw if it's allday event or not
        $subtract = floor(strtotime($date_end) - strtotime($date_start));
        $hours=floor($subtract/3600);
        if ($hours < 0){
         echo "error1"; //ending date is earlyer then starting date
        } else {
            if ($hours >= 24){
              $allDay = "true"; // allDay event
            } else {
              $allDay = "false"; // not an allDay event
            }
                 $event = R::dispense($table);
                 $event->title =  $event_heading;
                 $event->start = $date_start;
                 $event->end = $date_end;
                 $event->allDay = $allDay;
                 $event->changing_date = $changing_date;
                 $event->last_changed_user = $last_changed_user;
                 $event->description = $event_description;
                 $event->user_id = $user_id->id;
                 $event->typeinfo_id = $type_id->id;
                 $event->room_id = $room_id->id;
                 R::store($event);
                 echo "success";
      }
    } 
  } else {
    echo "error3"; //väljad täitmata
    header("location: ../index.php"); exit();
  }
} else {
  unset($session->user);
  header("location: ../index.php"); exit();
}
?>
