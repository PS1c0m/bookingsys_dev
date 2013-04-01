<?php
require_once "/../include/Session.php";
$session = new Session();

if (isset($session->user) && ($session->user->usertype === 'peakasutaja')){
  if(isset($_POST["type"]) && isset($_POST["background_color"]) && isset($_POST["text_color"])){
    
    //Serverside check for null values
    if( $_POST["type"] === '' || $_POST["background_color"] === '' || $_POST['text_color'] === '' ){
    echo "error1"; //Type name, bg and text color cannot be NULL
    } else {
     require_once "/../include/db.php";
     $table = "typeinfo";
     $type_name = $_POST['type'];
     $text_color = $_POST['text_color'];
     $background_color = $_POST['background_color'];
     $type_exists = R::findOne($table, 'type = ?', array($type_name));
       if (isset($type_exists)){ //Type allready exists
          echo "error";
       } else { //Type does not exist, add new type to DB
         $type = R::dispense($table);
         $type->type = $type_name;
         $type->text_color = $text_color;
         $type->background_color = $background_color;
         R::store($type);
         echo "success";
       }
    } //NULL value check
  } //POST isset check
} //Session check
?>