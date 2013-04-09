<?php
require_once "/../include/Session.php";
$session = new Session();
if (isset($session->user)){
    if(isset($_POST["id"]) && !($_POST["id"] === '') && isset($_POST['event_heading']) && isset($_POST['date_start']) && isset($_POST['date_end'])){
         require_once "/../include/db.php";
         $table = 'event';
         $event_id = $_POST["id"];
         $event_exists = R::findOne($table, 'id = ?', array($event_id));
          if (isset($event_exists)){//Event exists

             //Gather inputs
             date_default_timezone_set('Europe/Tallinn');
             $changing_date = date('Y-m-d H:i:s');   
             $last_changed_user = $session->user->username;
             $event_heading = $_POST['event_heading'];
             $date_start = $_POST['date_start'];
             $date_end = $_POST['date_end'];
             $event_description = $_POST['event_description'];
             $event_type = $_POST['event_type'];
             $event_user = $_POST['event_user'];
             $room_nr = $_POST['room_name'];

             //Get FK ID's
             $user_id = R::findOne('user', 'username = ?', array($event_user));
             $room_id = R::findOne('room', 'room_nr = ?', array($room_nr));
             $type_id = R::findOne('typeinfo', 'type = ?', array($event_type));

             $subtract = floor(strtotime($date_end) - strtotime($date_start));
             $hours=floor($subtract/3600);
                  if ($hours < 0){
                   echo "error1"; //ending date is earlyer then starting date
                  } else {
                      //Check if new selected dates are overlapping with existing ones
                         //1. Format dates
                      $day3_delta_start = date("Y-m-d H:i:s", strtotime($date_start)-259200);
                      $day3_delta_end = date("Y-m-d H:i:s", strtotime($date_end)+259200);
                      $start =  date("Y-m-d H:i:s", strtotime($date_start));
                      $end = date("Y-m-d H:i:s", strtotime($date_end));
                         //2. Get specific room events array with the day delta of +-3 days from the selected start end date
                      $roomstartend_events_array = R::getAll('SELECT event.* FROM event INNER JOIN room ON event.room_id = room.id WHERE room.room_nr = :room_nr AND (event.start >= :start AND event.end <= :end)', array(':room_nr'=>$room_nr, ':start'=>$day3_delta_start, ':end'=>$day3_delta_end));
                         //3. Go over the array and check if overlapping
                      foreach ($roomstartend_events_array as $key => $value) {
                          if ($value['id'] != $event_id){
                            if(!($value['start'] >= $end  || $value['end'] <= $start)){
                                        echo 'error3'; //Overlapping match, exit!
                                        return false;          
                                    }
                          }
                      }
                      if ($hours >= 24){
                        $allDay = "true"; // allDay event
                      } else {
                        $allDay = "false"; // not an allDay event
                      }
                           $event_exists->title = $event_heading;
                           $event_exists->start = $date_start;
                           $event_exists->end = $date_end;
                           $event_exists->allDay = $allDay;
                           $event_exists->changing_date = $changing_date;
                           $event_exists->last_changed_user = $last_changed_user;
                           $event_exists->description = $event_description;
                           $event_exists->user_id = $user_id->id;
                           $event_exists->typeinfo_id = $type_id->id;
                           $event_exists->room_id = $room_id->id;
                           R::store($event_exists);
                           echo "success";
                }

          } else { //Event does not exist in DB
            echo "error";
          }
    } else {
     echo "error2"; //All fileds must be fullfilled
     header("location: ../index.php"); exit();    
    }
} else {
  unset($session->user);
  header("location: ../index.php"); exit();
}
?>