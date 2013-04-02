<div id="alert_placeholder2"></div>
<div class="alert alert-info">
<a class="close" data-dismiss="alert">×</a> 
<strong>NB!</strong> Antud klassifkaatoreid on võimalik kasutada kalendris broneeriguid tehes. </br>Välja muutmiseks vajutage vastavale välja reale</br> Muutmisel ja lisamisel on kõik väljad kohustuslikud
</br> Värve tuleb lisada kas RGB värvikoodina (#FFFFFF) või ingliskeelse sõnana (black)
</div>
<table class="table table-striped table-hover table-bordered" id="types-table">
<thead>
  <tr>
      <th>Tüüp</th>
      <th>Tekstivärv</th>
      <th>Taustavärv</th>
  </tr>
</thead>
<tbody>
</tbody>
</table>
<div><input class="btn btn-success" type="submit" id="type-addnew-submit" name="SUBMIT_ADDNEW" value="Lisa uus klassifikaator"/></div>
<div class="modal hide" id="typeModal">
 <div class="modal-header">
    <button class="close" data-dismiss="modal" id="type-cancel-modal" >×</button>
    <h3>Klassifikaatori andmed</h3>
  </div>
    <div class="modal-body">
 <form class="form-horizontal" data-async id="type-changeing-form" name="type-change">
      <fieldset>
          <div class="control-group">
          <!-- Text input-->
          <label class="control-label" for="type" name="">Tüüp</label>  
          <div class="controls">
          <input name="type" id="type" type="text" placeholder="Loeng / Seminar" class="input-xlarge">
          </div>
          </div>
          <div class="control-group">
          <!-- Text input-->
          <label class="control-label" for="text_color">Tekstivärv</label>
          <div class="controls">
          <input name="text_color" id="text_color" type="text"  placeholder="Black / #000000" class="input-xlarge">
          </div>
          </div>
          <div class="control-group">
          <!-- Text input-->
          <label class="control-label" for="background_color" >Tasutavärv</label>
          <div class="controls">
          <input name="background_color" id="background_color" placeholder="Yellow / #FFFF00"  type="text" class="input-xlarge">
          </div>
          </div>
        </fieldset>
  </form>

            <!-- Button -->
         <div class="pull-right">
            <input class="btn" type="submit" id="type-cancel-submit" data-dismiss="modal" aria-hidden="true" value="Sulge"/>
            <input class="btn btn-success" type="submit" id="type-changeadd-submit" name="SUBMIT_CHANGEADD" value="Lisa/Muuda"/>
            <input class="btn btn-danger" type="submit" id="type-delete-submit" name="SUBMIT_DELETE" value="Kustuta"/>
         </div>
	</div>
</div>

</br>
<div class="alert alert-block">
<a class="close" data-dismiss="alert">×</a> 
<strong>Tähelepanu!</strong> Klassifikaatori kustutamisel kaob märge ka broneerigusüsteemis olevatelt broneerigutelt. Broneeringud ise jäävad alles.
</div>
<script type="text/javascript">
//Alerts
bootstrap_alert = function() {}
bootstrap_alert.success = function(message) {
            $('#alert_placeholder2').html('<div class="alert alert-success"><a class="close" data-dismiss="alert">×</a><span>'+message+'</span></div>')
        }
bootstrap_alert.error = function(message) {
            $('#alert_placeholder2').html('<div class="alert alert-error"><a class="close" data-dismiss="alert">×</a><span>'+message+'</span></div>')       
        }

//Get JSON data from DB
function loadAjax(){
  $.ajax({
    url: "type/_getTypes.php",
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
  var table_obj = $('#types-table');
      $.each(loadAjax(), function(index, item){
           table_obj.append($('<tr id="'+item.id+'"><td id="type">'+item.type+'</td><td  id="text_color">'+item.text_color+'</td><td id="background_color">'+item.background_color+'</td></tr>'));
});
}
// Remove Table Row
function removeTableRow(trID){
 $('#' + trID).detach();
}

$(document).ready(function() {

var trId; //Tabele row ID

	populateTable();

	//Open modal to change or delete types
	$("tr").on('click',function(){
	    if(this.id){
	      trId = this.id;
	      var type = $(this).children('#type').text();
	      var text_color = $(this).children('#text_color').text();
	      var background_color = $(this).children('#background_color').text();
	       $(".modal-body #type").val(type);
	       $(".modal-body #text_color").val(text_color);
	       $(".modal-body #background_color").val(background_color);
	       $("#typeModal").modal('show')
	    }
	});

	$("#type-addnew-submit").on('click',function(){
		$("#type-changeing-form")[0].reset();
		$("#typeModal").modal('show')
	});

//Client-side form validation 
var validator = $('#type-changeing-form').validate({
  rules: {
    type: {
      required: true
    },
    text_color: {
      required: true
    },
    background_color: {
      required: true
    }
  },
  messages: {
      type: "Sisestage palun tüüp!",
      text_color: "Sisestage palun antud tüübi tekstivärv!",
      background_color: "Sisestage palun antud tüübi taustavärv!"
  },
  highlight: function(element) {
    $(element).closest('.control-group').removeClass('success').addClass('error');
  },
  success: function(element) {
    element
    .text('Sobib!').addClass('valid')
    .closest('.control-group').removeClass('error').addClass('success');
  }
});//<---validate

/*
If id="type-addnew-submit" name="SUBMIT_ADDNEW" is pressed then set the row ID to null, because we need to add 
a new row to DB not to modify one
*/
$('#type-addnew-submit').click(function(){
	trId = null;
});
//Clear validation highlights and stuff after cancel
$('#type-cancel-submit').click(function(){
 	$("#type-changeing-form")[0].reset(); //Clear data after successful submit
	$('.control-group').removeClass('success'); //Clear validation check after successful check
	$('.control-group').removeClass('error'); //Clear validation check after successful check
	validator.resetForm(); //Clear validation check after successful check
});
//Clear validation if the X button is pressed on the modal (right top)
$('#type-cancel-modal').click(function(){
	$("#type-changeing-form")[0].reset(); //Clear data after successful submit
	$('.control-group').removeClass('success'); //Clear validation check after successful check
	$('.control-group').removeClass('error'); //Clear validation check after successful check
	validator.resetForm(); //Clear validation check after successful check
});

//If modal btn-success is pressed check if all values are correct and check if adding a new row or changing an old one has to occure
$("#type-changeadd-submit").click(function(e) {
    e.preventDefault();
    if($("#type-changeing-form").valid()){
		//Existing row, need to modify (_modifyType)
		if(trId){
    		  //Get inputs--->>
		      var $inputs = $('#type-changeing-form :input'); //Gather html input fields
		      var values = {}; //Array
		      $inputs.each(function() {  //Each input put into array object
		          values[this.name] = $(this).val();
		      });
		      //--->>Input end
    		$.ajax({
              url: "type/_modifyType.php",
              type: "POST",
               data: { 
                    type_id : trId,
                    type : values.type, 
                    text_color : values.text_color, 
                    background_color : values.background_color
                  },
              success: function(msg) {
                 if(msg == 'error2') {
	                  //error do something
	                  bootstrap_alert.error('<strong><h4>Muutmine ebaõnnestus!</h4>"Antud nimega klassifikaator juba eksisteerib süsteemis"</strong>');
                 } else if(msg == 'error1') { 
                      //error do something ..Room name and size cannot be empty, server side check
                      bootstrap_alert.error('<strong><h4>Muutmine ebaõnnestus!</h4>"Klassifikaatori kõik väljad peavad olema täidetud"</strong>');
                 } else if(msg == 'error3') { 
                      //error do something ..Room name and size cannot be empty, server side check
                      bootstrap_alert.error('<strong><h4>>Muutmine ebaõnnestus!</h4>"Antud nimega klassifikaatorit ei eksisteeri süsteemis"</strong>');
                 } else {
	                  //success do something
	                  bootstrap_alert.success('<strong>Klassifikaator edukalt muudetud.</strong>');
	                  $("#type-changeing-form")[0].reset(); //Clear data after successful submit
	                  $('.control-group').removeClass('success'); //Clear validation check after successful check
	                  validator.resetForm(); //Clear validation check after successful check
	                  $('#' + trId).empty();
                    $('#' + trId).append('<td id="type">'+values.type+'</td><td  id="text_color">'+values.text_color+'</td><td id="background_color">'+values.background_color+'</td></tr>');
                	  $("#typeModal").modal('hide');
                 }                 
              }
            });

    	//New row, need to add (_addType)	
    	} else {
    		$.ajax({
              url: "type/_addType.php",
              type: "POST",
              data: $("#type-changeing-form").serialize(),
              success: function(msg) {
                 if(msg == 'error') {
	                  //error do something
	                  bootstrap_alert.error('<strong><h4>Lisamine ebaõnnestus!</h4>"Antud nimega klassifikaator juba eksisteerib süsteemis"</strong>');
                 } else if(msg == 'error1') { 
                      //error do something ..Room name and size cannot be empty, server side check
                      bootstrap_alert.error('<strong><h4>Lisamine ebaõnnestus!</h4>"Klassifikaatori kõik väljad peavad olema täidetud"</strong>');
                 } else {
	                  //success do something
	                  bootstrap_alert.success('<strong>Uus klassifikaator edukalt süsteemi lisatud.</strong>');
	                  $("#type-changeing-form")[0].reset(); //Clear data after successful submit
	                  $('.control-group').removeClass('success'); //Clear validation check after successful check
	                  validator.resetForm(); //Clear validation check after successful check
	                  $("#typeModal").modal('hide');
                 }
              }
            });
    	}
    }//if valid
});

});
</script>