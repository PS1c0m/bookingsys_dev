<?php
require_once "/../include/db.php";
$table = "event";
$events = R::find($table);
$types = R::find('typeinfo');
$user = R::find('user');
$room = R::find('room');

$calevents = array();
foreach ($events as $event) {
  $calevent = array();

  $calevent['id'] = $event->id;
  $calevent['title'] = $event->title;
  $calevent['start'] = 	$event->start;
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
echo json_encode($calevents);
