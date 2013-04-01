<?php 
require_once "/../include/db.php";
$table = "typeinfo";
$types = R::findAll($table);
$types_array = array();
foreach ($types as $type) {
  $type_array = array();
  $type_array['id'] = $type->id;
  $type_array['type'] = $type->type;
  $type_array['text_color'] = 	$type->text_color;
  $type_array['background_color'] =  $type->background_color;
  $types_array[] = $type_array;
}
echo json_encode($types_array);
?>