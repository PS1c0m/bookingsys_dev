<div id="alert_placeholder1"></div>
<div class="alert alert-info">
<a class="close" data-dismiss="alert">×</a> 
<strong>NB!</strong> Ruumi parameetrite muutmiseks vajutage vastavale ruumi reale
</div>
<section id="no-more-tables">
<table class="table table-striped table-hover table-bordered" id="rooms-table">
<thead>
  <tr>
      <th>Ruumi number</th>
      <th>Ruumi tüüp</th>
      <th>Istekohtade arv</th>
      <th style="width: 50%">Ruumi kirjeldus</th>
  </tr>
</thead>
<tbody>
</tbody>
</table>
</section>
<div class="modal hide" id="roomModal">
 <div class="modal-header">
    <button class="close" data-dismiss="modal">×</button>
    <h3>Ruumi andmed</h3>
  </div>
    <div class="modal-body">
      <form class="form-horizontal" data-async id="room-changeing-form" name="room-change">
      <fieldset>
          <div class="control-group">
          <!-- Text input-->
          <label class="control-label" for="room_nr" name="">Ruumi number</label>  
          <div class="controls">
          <input name="room_nr" id="room_nr" type="text" class="input-xlarge">
          </div>
          </div>
          <div class="control-group">
          <!-- Text input-->
          <label class="control-label" for="room_size">Istekohtade arv</label>
          <div class="controls">
          <input name="room_size" id="room_size" type="text" class="input-xlarge">
          </div>
          </div>
          <div class="control-group">
          <!-- Text input-->
          <label class="control-label" for="room_type">Ruumi tüüp</label>
          <div class="controls">
          <input name="room_type" id="room_type" type="text" class="input-xlarge">
          </div>
          </div>
          <div class="control-group">
          <!-- Textarea -->
          <label class="control-label">Ruumi kirjeldus</label>
          <div class="controls">
          <div class="textarea">  
          <textarea rows="4" type="" class="" name="room_description" id="room_description"> </textarea>
          </div>
          </div>
          </div>
          </fieldset>
        </form>

            <!-- Button -->
           <div class="pull-right">
            <input class="btn" type="submit" data-dismiss="modal" aria-hidden="true" value="Sulge"/>
            <input class="btn btn-success" type="submit" id="room-change-submit" name="SUBMIT_CHANGE" value="Muuda"/>
            <input class="btn btn-danger" type="submit" id="room-delete-submit" name="SUBMIT_DELETE" value="Kustuta"/>
           </div>
          
    </div>
</div>

<div class="alert alert-block">
<a class="close" data-dismiss="alert">×</a> 
<strong>Tähelepanu!</strong> Ruumi kustutamisel kaovad süsteemist ka kõik selle ruumiga seonduvad broneerinud.
</div>
<script type="text/javascript">

//Alerts
bootstrap_alert = function() {}
bootstrap_alert.success = function(message) {
            $('#alert_placeholder1').html('<div class="alert alert-success"><a class="close" data-dismiss="alert">×</a><span>'+message+'</span></div>')
        }
bootstrap_alert.error = function(message) {
            $('#alert_placeholder1').html('<div class="alert alert-error"><a class="close" data-dismiss="alert">×</a><span>'+message+'</span></div>')       
        }

//Get JSON data from DB
function loadAjax(){
  $.ajax({
    url: "room/_getRooms.php",
    dataType: "json",
    async: false,
    cache: false, //disable ajax caching
    success: function(data) {
      originalJson = data;
    }
  }); 
  return originalJson;
} 

//Build and populate the table with data from JSON 
function populateTable(){ 
  var table_obj = $('#rooms-table');
      $.each(loadAjax(), function(index, item){
           table_obj.append(
            '<tr id="'+item.id+'"><td id="room_nr" data-title="Ruumi number">'+item.room_nr+
            '</td><td id="type" data-title="Ruumi tüüp">'+item.type+
            '</td><td id="size" data-title="Istekohtade arv">'+item.size+
            '</td><td id="description" data-title="Ruumi kirjeldus">'+item.description+
            '</td></tr>'
            );
      });
}
// Remove Table Row
function removeTableRow(trID){
 $('#' + trID).detach();
}

$(document).ready(function() {
var trId;

  populateTable();
  //Open modal to change or delete room
 $("tr").on('click',function(){
    if(this.id){
      trId = this.id;
      var room_nr = $(this).children('#room_nr').text();
      var type = $(this).children('#type').text();
      var size = $(this).children('#size').text();
      var description = $(this).children('#description').text();
       $(".modal-body #room_nr").val(room_nr);
       $(".modal-body #room_type").val(type);
       $(".modal-body #room_size").val(size);
       $(".modal-body #room_description").val(description);
       $("#roomModal").modal('show')
    }
  });
  //If room-delete-submit is pressed
  $("#room-delete-submit").on('click',function(){
    $("#roomModal").modal('hide');
    var currentForm = this;
    //Get inputs--->>
    var $inputs = $('#room-changeing-form :input'); //Gather html input fields
    var values = {}; //Array
    $inputs.each(function() {  //Each input put into array object
        values[this.name] = $(this).val();
    });
    //--->>Input end
    var warning = '<div class="alert alert-error"><h4>Hoiatus!</h4>Olete kindel, et soovite ruumi <strong>"' + values.room_nr +'"</strong> kustutada? Sellega kaasneb antud ruumi kõikide broneeringute kustutamine süsteemis. <strong>Kinnituseks vajutage "Jah"</strong>.</div>'
    bootbox.confirm(warning, "Ei", "Jah",
     function(result) {
            if (result) {
               $.ajax({
                  url: "room/_deleteRoom.php",
                  type: "POST",
                  data: { room_nr : values.room_nr },
                  success: function(msg) {
                     if(msg == 'error') { 
                       //error do something 
                       bootstrap_alert.error('<strong><h4>Kustutamine ebaõnnestus!</h4>Ruumi ei saanud kustutatada, kuna ruumi ei leitud süsteemist.</strong>');
                     } else {
                       //success do something   
                       bootstrap_alert.success('<strong>Ruum ja sellega seonduvad broneeringud süsteemist kustutatud.</strong>');
                       removeTableRow(trId);
                     }                 
                  }
                });
            }
      });// <<---bootbox.confirm end
  });

  //If room-change-submit is pressed
  $("#room-change-submit").on('click',function(){
    $("#roomModal").modal('hide');
      var currentForm = this;
      //Get inputs--->>
      var $inputs = $('#room-changeing-form :input'); //Gather html input fields
      var values = {}; //Array
      $inputs.each(function() {  //Each input put into array object
          values[this.name] = $(this).val();
      });
      //--->>Input end
       $.ajax({
                  url: "room/_modifyRoom.php",
                  type: "POST",
                  data: { 
                    room_id : trId,
                    room_nr : values.room_nr, 
                    room_size : values.room_size, 
                    room_type : values.room_type, 
                    room_description : values.room_description
                  },
                  success: function(msg) {
                     if(msg == 'error3') { 
                       //error do something ..Room does not exist in DB, server-side check
                       bootstrap_alert.error('<strong><h4>Ruumi andmete muutmine ebaõnnestus!</h4>"Ruumi ei leitud süsteemist"</strong>');
                     } 
                     else if(msg == 'error1') { 
                       //error do something ..Room name and size cannot be empty, server side check
                       bootstrap_alert.error('<strong><h4>Ruumi andmete muutmine ebaõnnestus!</h4>"Ruumi number ja suurus peab olema määratud"</strong>');
                     }
                     else if(msg == 'error2') { 
                       //error do something ..Room name allready existst cannot change, dublicate entry, server-side check
                       bootstrap_alert.error('<strong><h4>Ruumi andmete muutmine ebaõnnestus!</h4>"Antud ruumi number juba eksisteerib süsteemis"</strong>');
                     }
                      else if(msg == 'error4') { 
                       //error do something ..Room size must be numeric the size has to be numberic, server side check
                       bootstrap_alert.error('<strong><h4>Ruumi andmete muutmine ebaõnnestus!</h4>"Ruumi suurus peab olema arvväärtus"</strong>');
                     }
                     else {
                       //success do something   
                       bootstrap_alert.success('<strong>Ruumi andmed edukalt muudetud.</strong>');
                      /*
                       HackAround: Populate the changed row and do not use DB again, 
                       put the same changed data into row if ajax success)
                      */
                       $('#' + trId).empty();
                       $('#' + trId).append('<td id="room_nr">'+values.room_nr+'</td><td  id="type">'+values.room_type+'</td><td  id="size">'+values.room_size+'</td><td  id="description" style="width: 50%">'+values.room_description+'</td></tr>');

                     }                 
                  }
              });
  });

});
</script>