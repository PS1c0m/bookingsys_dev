<?php
require_once "/../include/db.php";
$types = R::find('typeinfo');
$user = R::find('user');
$room = R::find('room');

function convertEventsJSON($events, $room , $user, $types){
  $calevents = array();
    foreach ($events as $event) {
      $calevent = array();

      $calevent['id'] = $event->id;
      $calevent['title'] = $event->title;
      $calevent['start'] =  $event->start;
      $calevent['end'] =  $event->end;
      $calevent['allDay'] = $event->allDay;
      $calevent['changing_date'] = $event->changing_date;
      $calevent['last_changed_user'] = $event->last_changed_user;
      $calevent['description'] = $event->description;

          if ($calevent['allDay']){
            if($calevent['allDay'] == 'true'){
              $calevent['allDay'] = true;
            } else {
              $calevent['allDay'] = false;
            }
          }

      if (isset($event->end)){
        list($end_ymd,$end_hms) = explode(" ",$event->end);

        list($e_y,$e_m,$e_d) = explode("-",$end_ymd); // extract from YYYY-MM-DD format
        $calevent['e_day']   = (int) $e_d;
        $calevent['e_year']  = (int) $e_y;
        $calevent['e_month'] = (int) $e_m;

        list($e_hh,$e_mm,$e_ss) = explode(":",$end_hms);
        $calevent['e_hour'] = (int) $e_hh;
        $calevent['e_minute'] = (int) $e_mm;

      }

      list($start_ymd,$start_hms) = explode(" ",$event->start);
      
      list($s_y,$s_m,$s_d) = explode("-",$start_ymd); // extract from YYYY-MM-DD format
      $calevent['s_day']   = (int) $s_d;
      $calevent['s_year']  = (int) $s_y;
      $calevent['s_month'] = (int) $s_m;

      list($s_hh,$s_mm,$s_ss) = explode(":",$start_hms);
      $calevent['s_hour'] = (int) $s_hh;
      $calevent['s_minute'] = (int) $s_mm;

      $calevent['color']      = $types[$event->typeinfo_id]['text_color'];
      $calevent['background'] = $types[$event->typeinfo_id]['background_color'];
      $calevent['type'] = $types[$event->typeinfo_id]['type'];

      $calevent['user'] = $user[$event->user_id]['username'];
      $calevent['room'] = $room[$event->room_id]['room_nr'];

      $calevents[] = $calevent; 
    }
    return $calevents;
}
  //if calendar view dates are specified (view.start, view.end)
if (isset($_GET["room_nr"]) && isset($_GET["start"]) && isset($_GET["end"])){
  $room_nr = $_GET["room_nr"];
  date_default_timezone_set('Europe/Tallinn');
  $delta_start = date('Y-m-d H:i:s', $_GET["start"]);
  $delta_end = date('Y-m-d H:i:s', $_GET["end"]);
  $roomstartend_events_array = R::getAll('SELECT event.* FROM event INNER JOIN room ON event.room_id = room.id WHERE room.room_nr = :room_nr AND event.start >= :start AND event.end <= :end', array(':room_nr'=>$room_nr, ':start'=>$delta_start, ':end'=>$delta_end));
  $room_events = R::convertToBeans('event',$roomstartend_events_array);
  echo json_encode(convertEventsJSON($room_events, $room , $user, $types));
  //Only specified rooms from DB
} elseif (isset($_GET["room_nr"])) {
  $room_nr = $_GET["room_nr"];
  $room_events_array = R::getAll('SELECT event.* FROM event INNER JOIN room ON event.room_id = room.id WHERE room.room_nr = :room_nr', array(':room_nr'=>$room_nr));
  $room_events = R::convertToBeans('event',$room_events_array);
  echo json_encode(convertEventsJSON($room_events, $room , $user, $types));
} else {
  //All events from DB
  $events = R::find('event');
  echo json_encode(convertEventsJSON($events, $room , $user, $types));
}