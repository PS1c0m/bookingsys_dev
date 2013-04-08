<?php
if (substr_count($_SERVER['HTTP_ACCEPT_ENCODING'], 'gzip')) ob_start("ob_gzhandler"); else ob_start();
require_once "/../include/Session.php";
$session = new Session();
if (!isset($session->user) || ($session->user->usertype === 'tavakasutaja')) {
  $target = $_SERVER['REQUEST_URI'];
    $message = $session->message; 
    $username = $session->username;
    unset($session->message);
    unset($session->username);
  header("location: ../index.php");  exit();
}
?>
<div id="alert_placeholder"></div>
<div class="alert alert-info">
<a class="close" data-dismiss="alert">×</a> 
Väljad "Ruumi number" ja "Istekohtade arv" on kohustuslikud
</div>
      <form class="form-horizontal" data-async data-target="#lisa_ruum" method="POST" id="room-adding-form" name="room-adding">
      <fieldset>
          <div class="control-group">
          <!-- Text input-->
          <label class="control-label" for="room_nr" name="">Ruumi number</label>  
          <div class="controls">
          <input name="room_nr" id="room_nr" type="text" placeholder="Sisestage ruumi number" class="input-xlarge">
          </div>
          </div>
          <div class="control-group">
          <!-- Text input-->
          <label class="control-label" for="room_size">Istekohtade arv</label>
          <div class="controls">
          <input name="room_size" id="room_size" type="text" placeholder="Sisestage ruumi suurus" class="input-xlarge">
          </div>
          </div>
          <div class="control-group">
          <!-- Text input-->
          <label class="control-label" for="input01">Ruumi tüüp</label>
          <div class="controls">
          <input name="room_type" id="room_type" type="text" placeholder="Loengusaal / Seminariruum" class="input-xlarge">
          </div>
          </div>
          <div class="control-group">
          <!-- Textarea -->
          <label class="control-label">Ruumi kirjeldus</label>
          <div class="controls">
          <div class="textarea">  
          <textarea rows="5" type="" class="" name="room_description" id="room_description"> </textarea>
          </div>
          </div>
          </div>
          <div class="control-group">
          <!-- Button -->
          <div class="controls">
          <button class="btn btn-success" type="submit" id="room-add-submit" name="SUBMIT_ADD">Salvesta</button>
          </div>
          </div>
       </fieldset>
       </form>
       <span id="error" style="display:none"> Palun täida väljad</span> 
<script type="text/javascript">
$(document).ready(function(){
bootstrap_alert = function() {}
bootstrap_alert.success = function(message) {
            $('#alert_placeholder').html('<div class="alert alert-success"><a class="close" data-dismiss="alert">×</a><span>'+message+'</span></div>')
        }
bootstrap_alert.error = function(message) {
            $('#alert_placeholder').html('<div class="alert alert-error"><a class="close" data-dismiss="alert">×</a><span>'+message+'</span></div>')       
        }

var validator = $('#room-adding-form').validate({
  rules: {
    room_nr: {
      required: true
    },
    room_size: {
      required: true,
      number: true
    }
  },
  messages: {
      room_nr: "Sisestage palun ruumi number!",
      room_size: "Sisestage palun arvväärtus!"
  },
  highlight: function(element) {
    $(element).closest('.control-group').removeClass('success').addClass('error');
  },
  success: function(element) {
    element
    .text('Sobib!').addClass('valid')
    .closest('.control-group').removeClass('error').addClass('success');
  },
  submitHandler: function() {
    var currentForm = this;
    //Get inputs--->>
    var $inputs = $('#room-adding-form :input'); //Gather html input fields
    var values = {}; //Array
    $inputs.each(function() {  //Each input put into array object
        values[this.name] = $(this).val();
    });
    //--->>Input end
    bootbox.confirm("<h2>Kinnitus</h2>Kinnitage, et soovite sisestada ruumi:<b> " + values.room_nr + "</b></br>Istekohtade arv: <b>" + values.room_size + "</b></br>Ruumitüüp: <b>" + values.room_type + "</b></br>Kirjeldus: " + values.room_description,
    function(result) {
        if (result) {
           $.ajax({
              url: "room/_addRoom.php",
              type: "POST",
              data: $("#room-adding-form").serialize(),
              success: function(msg) {
                 if(msg == 'error') {
                   //error do something
                   bootstrap_alert.error('<strong><h4>Lisamine Ebaõnnestus!</h4>"Antud nimega ruumi number juba eksisteerib süsteemis"</strong>');
                 } else {
                    //success do something
                  bootstrap_alert.success('<strong>Uus ruum edukalt süsteemi lisatud.</strong>');
                  $("#room-adding-form")[0].reset(); //Clear data after successful submit
                  validator.resetForm(); //Clear validation check after successful check
                  $('.control-group').removeClass('success'); //Clear validation highlight
                 }                 
              }
            });
        }
    });
    return false;
  }//<---submitHandler
});//<---validate
});//<---end document.ready
</script>
