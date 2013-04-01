<?php 
require_once "/../include/db.php";
$table = "room";
$rooms = R::findAll($table);
$rooms_arrary = array();
foreach ($rooms as $room) {
  $room_array = array();

  $room_array['id'] = $room->id;
  $room_array['room_nr'] = $room->room_nr;
  $room_array['type'] = 	$room->type;
  $room_array['size'] =  $room->size;
  $room_array['description'] = $room->description;

  $rooms_arrary[] = $room_array;
}
echo json_encode($rooms_arrary);
?>