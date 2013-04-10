<?php
if (substr_count($_SERVER['HTTP_ACCEPT_ENCODING'], 'gzip')) ob_start("ob_gzhandler"); else ob_start();
require_once "/../include/db.php";
require_once "/../include/Session.php";
$session = new Session();

//Is used on some cases when receiving data from DB and sending it to javascript
function convertEventsJSON($events){
$types = R::find('typeinfo');
$user = R::find('user');
$room = R::find('room');

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
      //IMPORTANT!---> start
      //Need to correctly pass the starting and ending date to fullcalendar javascript
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
      //IMPORTANT!<--- section end

      $calevent['color']      = $types[$event->typeinfo_id]['text_color'];
      $calevent['background'] = $types[$event->typeinfo_id]['background_color'];
      $calevent['type'] = $types[$event->typeinfo_id]['type'];
      $calevent['user'] = $user[$event->user_id]['username'];
      $calevent['room'] = $room[$event->room_id]['room_nr'];

      $calevents[] = $calevent; 
    }
    return $calevents;
}


//Check if PHPSession is set for registered user
if (isset($session->user)){
  /*
  if calendar view dates are specified with room (view.start, view.end from index.js fullcalendar)
  Used under index.js when rendering calendar for registered users
  */
  if (isset($_GET["room_nr"]) && isset($_GET["start"]) && isset($_GET["end"])){
    $room_nr = $_GET["room_nr"];
    date_default_timezone_set('Europe/Tallinn');
    $delta_start = date('Y-m-d H:i:s', $_GET["start"]);
    $delta_end = date('Y-m-d H:i:s', $_GET["end"]);
    $roomstartend_events_array = R::getAll('SELECT event.* FROM event INNER JOIN room ON event.room_id = room.id WHERE room.room_nr = :room_nr AND event.start >= :start AND event.end <= :end', array(':room_nr'=>$room_nr, ':start'=>$delta_start, ':end'=>$delta_end));
    $room_events = R::convertToBeans('event',$roomstartend_events_array);
    echo json_encode(convertEventsJSON($room_events));
  }
  /*
  If global search is used with starting and ending date ranges send back structured data without using function convertEventsJSON
  Used under broneeringud.php - $("#global-search").keyup(function ()
  */
  elseif (isset($_GET["search_keyword"]) && isset($_GET["start"]) && isset($_GET["end"])) {
    $search_keyword = $_GET["search_keyword"];
    date_default_timezone_set('Europe/Tallinn');
    $delta_start = date('Y-m-d H:i:s', $_GET["start"]);
    $delta_end = date('Y-m-d H:i:s', $_GET["end"]);

    $aColumns = array( 
      'event.title', 
      'event.start', 
      'event.end', 
      'event.changing_date', 
      'event.last_changed_user', 
      'event.description', 
      'username', 
      'typeinfo.type', 
      'room_nr' 
      );
    $sWhere = "WHERE event.start >= '".$delta_start."' AND event.end <= '".$delta_end."' AND (";
      for ( $i=0 ; $i<count($aColumns) ; $i++ ){
        $sWhere .= $aColumns[$i].' LIKE "%'.$search_keyword.'%" OR ';
      }
    $sWhere = substr_replace( $sWhere, "", -3 );
    $sWhere .= ") ORDER BY event.start";
    $sql = "SELECT 
            event.id, event.title, event.description, room.room_nr,
            event.last_changed_user, event.changing_date, event.start,
            event.end, user.username, typeinfo.type
            FROM event 
            INNER JOIN room ON event.room_id = room.id
            INNER JOIN user ON event.user_id = user.id
            INNER JOIN typeinfo ON event.typeinfo_id = typeinfo.id $sWhere";

    $search_result = R::getAll($sql);
    echo json_encode($search_result);
  } 
  /*
  If only dates are specified
  Used under broneeringud.php when search is not defined - function populateTable(start, end)
  */
  elseif (isset($_GET["start"]) && isset($_GET["end"])){
    date_default_timezone_set('Europe/Tallinn');
    $delta_start = date('Y-m-d H:i:s', $_GET["start"]);
    $delta_end = date('Y-m-d H:i:s', $_GET["end"]);
    $startend_events_array = R::getAll('SELECT * FROM event WHERE start >= :start AND end <= :end ORDER BY start', array(':start'=>$delta_start, ':end'=>$delta_end));
    $events = R::convertToBeans('event',$startend_events_array);
    echo json_encode(convertEventsJSON($events));
  }
  /*
  If Only specified rooms from DB
  Used under broneeringud.php when checking overlapping - function loadEventAjax(room_nr)
  */
  elseif (isset($_GET["room_nr"])) {
    $room_nr = $_GET["room_nr"];
    $room_events_array = R::getAll('SELECT event.* FROM event INNER JOIN room ON event.room_id = room.id WHERE room.room_nr = :room_nr', array(':room_nr'=>$room_nr));
    $room_events = R::convertToBeans('event',$room_events_array);
    echo json_encode(convertEventsJSON($room_events));
  } 
  /*
  If only global search - when start and end are undefined
  Used under broneeringud.php - $("#global-search").keyup(function () {
  */
  elseif (isset($_GET["search_keyword"])) {
    $search_keyword = $_GET["search_keyword"];
    $aColumns = array( 
      'event.title', 
      'event.start', 
      'event.end', 
      'event.changing_date', 
      'event.last_changed_user', 
      'event.description', 
      'username', 
      'typeinfo.type', 
      'room_nr' 
      );
    $sWhere = "WHERE ";
      for ( $i=0 ; $i<count($aColumns) ; $i++ ){
        $sWhere .= $aColumns[$i].' LIKE "%'.$search_keyword.'%" OR ';
      }
    $sWhere = substr_replace( $sWhere, "", -3 );

    $sql = "SELECT 
            event.id, event.title, event.description, room.room_nr,
            event.last_changed_user, event.changing_date, event.start,
            event.end, user.username, typeinfo.type
            FROM event 
            INNER JOIN room ON event.room_id = room.id
            INNER JOIN user ON event.user_id = user.id
            INNER JOIN typeinfo ON event.typeinfo_id = typeinfo.id $sWhere";

    $search_result = R::getAll($sql);
    echo json_encode($search_result);
  } 
  /*
  All events from DB
  Not used anywhere in the code
  */
  else {
    $events = R::findAll('event');
    echo json_encode(convertEventsJSON($events));
  }

}//if PHPSession


//iF session is not set, allow only calendar info without user information
else {
  /*
  If calendar view dates are specified with room (view.start, view.end)
  Used under index.js when rendering calendar for not registered users
  */
  if (isset($_GET["room_nr"]) && isset($_GET["start"]) && isset($_GET["end"])){
    $room_nr = $_GET["room_nr"];
    date_default_timezone_set('Europe/Tallinn');
    $delta_start = date('Y-m-d H:i:s', $_GET["start"]);
    $delta_end = date('Y-m-d H:i:s', $_GET["end"]);
    $roomstartend_events_array = R::getAll('SELECT event.* FROM event INNER JOIN room ON event.room_id = room.id WHERE room.room_nr = :room_nr AND event.start >= :start AND event.end <= :end', array(':room_nr'=>$room_nr, ':start'=>$delta_start, ':end'=>$delta_end));
    $room_events = R::convertToBeans('event',$roomstartend_events_array);

      $types = R::find('typeinfo');
      $room = R::find('room');

      $calevents = array();
      foreach ($room_events as $event) {
        $calevent = array();

          $calevent['id'] = $event->id;
          $calevent['title'] = $event->title;
          $calevent['start'] =  $event->start;
          $calevent['end'] =  $event->end;
          $calevent['description'] = $event->description;
      //IMPORTANT!---> start
      //Need to correctly pass the starting and ending date to fullcalendar javascript
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
      //IMPORTANT!<--- section end
        $calevent['color']      = $types[$event->typeinfo_id]['text_color'];
        $calevent['background'] = $types[$event->typeinfo_id]['background_color'];
        $calevent['type'] = $types[$event->typeinfo_id]['type'];
        $calevent['room'] = $room[$event->room_id]['room_nr'];

        $calevents[] = $calevent; 
      }

    echo json_encode($calevents);
  }

} //else user not logged in