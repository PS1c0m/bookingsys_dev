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
         $type_id = $_POST['type_id'];
         $type_name = $_POST['type'];
         $text_color = $_POST['text_color'];
         $background_color = $_POST['background_color'];
         $type_exists = R::findOne($table, 'type = ?', array($type_name));

        if (isset($type_exists) && $type_exists->id != $type_id){
          echo "error2"; //Type name allready existst cannot change, dublicate entry, server-side check
        } else {
            $type_exists = R::findOne($table, 'id = ?', array($type_id));
            if (isset($type_exists)){ //Room exists, make the changes
                 $type_exists->type = $type_name;
                 $type_exists->text_color = $text_color;
                 $type_exists->background_color = $background_color;
                 R::store($type_exists);
                 echo "success";
            } else { //Type does not exist in DB, server-side check
                 echo "error3";       
            }
        }
    }
 }
}
?>