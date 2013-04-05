<?php 
require_once "include/Session.php";
$session = new Session();
require_once "include/db.php";
$rooms = R::findAll("room");
$event_types = R::findAll("typeinfo");
$users = R::findAll("user");
?>

<style type="text/css">
table { table-layout:fixed; }
table td { overflow: hidden; }
#loading-indicator { position: absolute; left:50%; top:50%;}
</style>

<img src="img/ajax-loader.gif" id="loading-indicator" style="display:none" />
<section id="no-more-tables">

<div class="tabbable">
     <ul id="myEventTabs" class="nav nav-tabs" >
		<li class='active'><a data-toggle="tab" href="#allevents">Kõik broneeringud</a></li>
		<li><a data-toggle="tab" href="#personalevents">Minu broneeringud</a></li>
		<li><a data-toggle="tab" href="#addevent">Lisa broneering</a></li>
	</ul>

    <div class="tab-content">
 		<!--

		ALLEVENTS TAB

    	-->
        <div class="tab-pane active" id="allevents">
        	<form class="form-inline">
			  <i class="icon-search"></i> <input type="search" class="input" id="global-search" placeholder="Otsi...">
			</form>
        	<table class="table table-hover table-bordered table-condensed" id="event-table">
			</table>
			<table class="table table-hover table-bordered table-condensed" id="search-table">
			</table>
        </div>
        <!--

		PERSONALEVENTS TAB

    	-->
        <div class="tab-pane" id="personalevents">
       
        </div>
        <!--

		ADDEVENT TAB

    	-->
        <div class="tab-pane" id="addevent">
	      <form class="form-horizontal" data-async id="event-add-form" name="event-adding">
	      <fieldset>

	          <div class="control-group">	
	          <!-- Text input-->
	          <label class="control-label" for="room_name" name="">Ruum</label>  
	          <div class="controls">
		          <select name="room_name" id="room_name">
		           <?php foreach ($rooms as $room): ?>
		           <option><?php echo $room->room_nr ?></option>
		           <?php endforeach ?>
		          </select>
	          </div>
	          </div>

	          <div class="control-group">
	          <!-- Text input-->
	          <label class="control-label" for="date-start">Algus</label>
	          <div class="controls">
	          <input type="text" placeholder="yyyy-mm-dd hh:mm" name="date-start" class="datetimepicker" id='date-start'>
	        
	          </div>
	          </div>

	          <div class="control-group">
	          <!-- Text input-->
	          <label class="control-label" for="date-end">Lõpp</label>
	          <div class="controls">
	          <input type="text" placeholder="yyyy-mm-dd hh:mm" name="date-end" class="datetimepicker" id='date-end'>
	          <!--<input type="text" placeholder="hh:mm" class="datepicker input-small" id='date-end-time'>-->
	          </div>
	          </div>

	           <div class="control-group">	
	          <!-- Text input-->
	          <label class="control-label" for="event_type" name="">Broneeringu tüüp</label>  
	          <div class="controls">
		          <select name="event_type" id="event_type">
		           <?php foreach ($event_types as $eventtype): ?>
		           <option><?php echo $eventtype->type ?></option>
		           <?php endforeach ?>
		          </select>
	          </div>
	          </div>
 			  
 			  <div class="control-group">	
	          <!-- Text input-->
	          <label class="control-label" for="event_user" name="">Kasutaja</label>  
	          <div class="controls">
		          <select name="event_user" id="event_user">
		           <?php foreach ($users as $user): ?>
		           <option><?php echo $user->username ?></option>
		           <?php endforeach ?>
		          </select>
	          </div>
	          </div>

 			  <div class="control-group">
	          <!-- Text input-->
	          <label class="control-label" for="event_heading">Pealkiri</label>
	          <div class="controls">
	          <input type="text" placeholder="Broneeringu pealkiri" name="event_heading" id='event_heading'>
	          </div>
	          </div>

	          <div class="control-group">
	          <!-- Textarea -->
	          <label class="control-label">Broneeringu kirjeldus</label>
	          <div class="controls">
	          <div class="textarea">  
	          <textarea rows="3" type="" class="" name="event_description" id="event_description"> </textarea>
	          </div>
	          </div>
	          </div>
	       </fieldset>
	       </form>
	        <!-- ALERT -->
	        <div id="alert_placeholder3"></div>
	        <div class="control-group">
	        <!-- Text input-->
	        <div class="controls">
		    <button class="btn btn-success offset2" type="submit" id="event-add-submit" name="EVENT_SUBMIT_ADD">Sisesta broneering</button>
        	</div>
	        </div> 
        </div>
    </div>  
</div>

</section>
<script type="text/javascript">
/*
*
*
*Alerts
*
*
*/
bootstrap_alert = function() {}
bootstrap_alert.success = function(message) {
            $('#alert_placeholder3').show().html('<div class="alert alert-success"><a class="close" data-dismiss="alert">×</a><span>'+message+'</span></div>');
        }
bootstrap_alert.error = function(message) {
            $('#alert_placeholder3').show().html('<div class="alert alert-error"><a class="close" data-dismiss="alert">×</a><span>'+message+'</span></div>');     
        }
/*
*
*
*Build and populate the table with data from JSON 
*
*
*/
function populateTable(){ 
	$.getJSON('events/_getEvents.php', function(data) {
		var r = new Array();
		var j = -1, recordId;
		r[++j] = '<thead class="cf"><tr><th>Pealkiri</th><th>Kasutaja</th><th>Ruum</th><th>Algus</th><th>Lõpp</th><th style="width: 20%">Kirjeldus</th><th>Viimati muudetud</th><th>Viimane muutja</th><th>Broneeringu tüüp</th></tr></thead><tbody>';
			for (var i in data){
			    var d = data[i];
			    recordId = d.id;
			    r[++j] = '<tr id="';
			    r[++j] = recordId;
			    r[++j] = '"><td data-title="Pealkiri" id="title">';
			    r[++j] = d.title;
			    r[++j] = '</td><td data-title="Kasutaja" id="user">';
			    r[++j] = d.user;
			    r[++j] = '</td><td data-title="Ruum" id="room">';
			    r[++j] = d.room;
			    r[++j] = '</td><td data-title="Algus" id="start">';
			    r[++j] = d.start;
			    r[++j] = '</td><td data-title="Lõpp" id="end">';
			    r[++j] = d.end;
			    r[++j] = '</td><td data-title="Kirjeldus" id="description">';
			    r[++j] = d.description;
			    r[++j] = '</td><td data-title="Viimati muudetud" id="changing_date">';
			    r[++j] = d.changing_date;
			    r[++j] = '</td><td data-title="Viimane muutja" id="last_changed_user">';
			    r[++j] = d.last_changed_user;
			    r[++j] = '</td><td data-title="Broneeringu tüüp" id="type">';
			    r[++j] = d.type;
			    r[++j] = '</td></tr>';
			}
		r[++j] = '</tbody>';
		$('#event-table').html(r.join(''));
	});
}
/*
*
*
*Need to have this function to get foramted datetime which is compatible for all browsers
*
*
*/
function formatDate(date_time){
	var datetime = date_time.split(" ");
	var date = datetime[0].split("-");
	var time = datetime[1].split(":");
	return new Date(date[0],date[1]-1,date[2],time[0],time[1]);  
}
/*
*
*
*Load specific Room events
*
*
*/
function loadEventAjax(room_nr){
  $.ajax({
    url: "events/_getEvents.php?room_nr=" + room_nr,
    dataType: "json",
    async: false,
    cache: false, //disable ajax caching
    success: function(data) {
      jsonData = data;
    }
  }); 
  return jsonData;
}
/*
*
*
*Check if the dates are overlapping with existing event
*
*
*/
function isOverlapping(starting, ending){
	  var room_nr = $('#room_name').val();
	  var jsonData = loadEventAjax(room_nr);
	  var events = []; 
  $(jsonData).each(function(index,value){
    var event = {
      start: new Date(value.s_year, value.s_month-1, value.s_day, value.s_hour, value.s_minute),
      end: new Date(value.e_year, value.e_month-1, value.e_day, value.e_hour, value.e_minute),
      room_nr : value.room
    };
    events.push(event);
  });
    for(i in events){
    	if (room_nr == events[i].room_nr){
            if(!(events[i].start >= ending || events[i].end <= starting)){
                return true; //Match, dates are overlapping    
       		 }
    	}
    }
    return false; //No overlapping dates  
}
/*
*
*
*
*
*
*/
$(document).ready(function() {
	$('#loading-indicator').show();
	populateTable();
	$('#loading-indicator').hide(); 
	/*
	*
	*
	*Datepicker
	*
	*
	*/
	$("input.datetimepicker").datetimepicker({
	 	format: 'yyyy-mm-dd hh:ii',
	 	autoclose: true,
	 	minuteStep: 15,
	 	weekStart: 1,
	 	startDate: new Date()
	 });

	/*
	*
	*
	*Add new event from FORM tab (Lisa Broeering)
	*
	*
	*/
 	$('#event-add-submit').click( function() {
 	//assign variables and check if the fields are fullfilled
      	var start = $('#date-start').val();
	  	var end =  $('#date-end').val();
        var event_heading = $('#event_heading').val();
        var date = new Date();
        if (start === '' || end === '' || event_heading === '') {
        	bootstrap_alert.error('<strong><h4>Lisamine ebaõnnestus!</h4></strong> Palun täitke kõik väljad.');     
        //Fields are fullfilled 
        } else { //2
        	var start_date = formatDate(start);
        	var end_date = formatDate(end);
        //Check if the user has picket a date from the past
        	if (date > start_date  || date > end_date) {
        		bootstrap_alert.error('<strong><h4>Lisamine ebaõnnestus!</h4></strong> Broneeringu algus ega lõpp ei saa olla minevikus.');
        	}
        //Check if start and end dates are not the same
        	else if (start == end) {
        		bootstrap_alert.error('<strong><h4>Lisamine ebaõnnestus!</h4></strong> Broneeringu algus- ja lõpukellaaeg ei saa olla samad.');
        	}
        //Check if start is before end
        	else if (start > end) {
        		bootstrap_alert.error('<strong><h4>Lisamine ebaõnnestus!</h4></strong> Broneeringu algusaeg ei saa olla hilisem lõpukellajast.');
        	}
    	// Now check if the dates are overlapping with existing events in DB
        	else if (isOverlapping(start_date, end_date)){
				bootstrap_alert.error('<strong><h4>Lisamine ebaõnnestus!</h4></strong> Antud ajad kattuvad süsteemis varasemalt olemasoleva broneeringuga.');
		// If not let's send the data to PHP for server-side proccessing via AJAX POST
			} else { //3
			 	$.ajax({
			              url: "events/_addEvent.php",
			              type: "POST",
			              data: $("#event-add-form").serialize(),
			              success: function(msg) {
			                 if(msg == 'error1') {
			                   //error do something
			                   bootstrap_alert.error('<strong><h4>Lisamine ebaõnnestus!</h4></strong> Broneeringu algusaeg ei saa olla hilisem lõpukellajast.');
			                 } else if(msg == 'error2') { 
			                   bootstrap_alert.error('<strong><h4>Lisamine Ebaõnnestus!</h4></strong> Broneeringu väljad peavad olema täidetud.');
			                 } else {
			                    //success do something
			                  bootstrap_alert.success('<strong>Broneering edukalt süsteemi lisatud.</strong>');
			                  $("#event-add-form")[0].reset(); //Clear data after successful submit
			                 }                 
			              }
			            }); //ajax
			} //else3
        } //else2
	}); // $('#event-add-submit')
	/*
	*
	*
	*
	*
	*
	*/
	/*
	$("#search").on("keyup", function() {
    var value = $(this).val();
    console.log(value);

    $("table tr").each(function(index) {
        if (index !== 0) {

            $row = $(this);

            var id = $row.find("td:first").text();

            if (id.indexOf(value) !== 0) {
                $row.hide();
            }
            else {
                $row.show();
            }
        }
    });
});*/

/*
		// Write on keyup event of keyword input element
	$("#search").keyup(function(){
		// When value of the input is not blank
		if( $(this).val() != "")
		{
			// Show only matching TR, hide rest of them
			$("#event-table tbody>tr").hide();
			$("#event-table td:contains-ci('" + $(this).val() + "')").parent("tr").show();
		}
		else
		{
			// When there is no input or clean again, show everything back
			$("#event-table tbody>tr").show();
		}
	});

*/
    var minlength = 3;

    $("#global-search").keyup(function () {
        var that = this,
        value = $(this).val();

        if (value.length >= minlength ) {
            $.ajax({
                type: "GET",
                url: "events/_getEvents.php",
                data: {
                    'search_keyword' : value
                },
                dataType: "json",
                success: function(msg){
                    //we need to check if the value is the same
                    if (value==$(that).val()) {                	
                    //Receiving the result of search here
                    $('#event-table').hide();
  
                    buildSearchTable(msg);
                    $('#search-table').show();
                    }
                }
            });
        } 
        if (value.length < minlength ) {
        	$('#search-table').hide();
        	$('#event-table').show();
        }
    });


});
function buildSearchTable(data){
	var r = new Array();
		var j = -1, recordId;
		r[++j] = '<thead class="cf"><tr><th>Pealkiri</th><th>Kasutaja</th><th>Ruum</th><th>Algus</th><th>Lõpp</th><th style="width: 20%">Kirjeldus</th><th>Viimati muudetud</th><th>Viimane muutja</th><th>Broneeringu tüüp</th></tr></thead><tbody>';
			for (var i in data){
			    var d = data[i];
			    recordId = d.id;
			    r[++j] = '<tr id="';
			    r[++j] = recordId;
			    r[++j] = '"><td data-title="Pealkiri" id="title">';
			    r[++j] = d.title;
			    r[++j] = '</td><td data-title="Kasutaja" id="user">';
			    r[++j] = d.username;
			    r[++j] = '</td><td data-title="Ruum" id="room">';
			    r[++j] = d.room_nr;
			    r[++j] = '</td><td data-title="Algus" id="start">';
			    r[++j] = d.start;
			    r[++j] = '</td><td data-title="Lõpp" id="end">';
			    r[++j] = d.end;
			    r[++j] = '</td><td data-title="Kirjeldus" id="description">';
			    r[++j] = d.description;
			    r[++j] = '</td><td data-title="Viimati muudetud" id="changing_date">';
			    r[++j] = d.changing_date;
			    r[++j] = '</td><td data-title="Viimane muutja" id="last_changed_user">';
			    r[++j] = d.last_changed_user;
			    r[++j] = '</td><td data-title="Broneeringu tüüp" id="type">';
			    r[++j] = d.type;
			    r[++j] = '</td></tr>';
			}
		r[++j] = '</tbody>';
		$('#search-table').html(r.join(''));
}
</script>