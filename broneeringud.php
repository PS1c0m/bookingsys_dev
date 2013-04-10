<?php 
if (substr_count($_SERVER['HTTP_ACCEPT_ENCODING'], 'gzip')) ob_start("ob_gzhandler"); else ob_start();
require_once "include/Session.php";
$session = new Session();
if (isset($session->user)):

require_once "include/db.php";
$rooms = R::findAll("room");
$event_types = R::findAll("typeinfo");
$users = R::findAll("user");

?>

<style type="text/css">
table { table-layout:fixed; }
table td { overflow: hidden; }
</style>

<section id="no-more-tables">

<div id="hidden-usertype" style="display: none"><?php echo $session->user->usertype ?></div>

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
        	<div class="alert alert-error" id="alert"></div>
        	<form>

	          	<div class="input-prepend" id="global-search-div">
				  <span class="add-on">Otsing <i class="icon-search"></i></span>
				  <input type="search" class="input" id="global-search" placeholder="Sisesta otsisõna">
				</div>
				<div class="input-prepend">
					<span class="add-on">Perioodi algus <i class="icon-calendar"></i></span>
			    	<input class="input-small" type="text" id="search-start-date">
			    </div>
			    <div class="input-prepend">
					<span class="add-on">Perioodi lõpp <i class="icon-calendar"></i></span>
			    	<input class="input-small" type="text" id="search-end-date">
			    </div>
			    <div class="input-prepend input-append"><span class="add-on">Näita korraga</span>
			    <select class="input-mini" name="rows_per_page" id="rows_per_page">	          
				    <option value="10">10</option> 
				    <option value="20">20</option>
				    <option value="50">50</option>
				    <option value="100">100</option>
		         </select><span class="add-on">kirjet</span></div>
			    <div class="pull-right" id="row-count"></div>
			    <div class="pagination  pagination-small pagination-centered"><ul id="paggination"></ul></div> 
			</form>
			


        	<table class="table table-hover table-bordered table-condensed" id="event-table">
			</table>
			<!--<table class="table table-hover table-bordered table-condensed" id="search-table">
			</table>-->
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
 			  
	          <!-- Kasutaja valimine -->
 			  <?php if($session->user->usertype === 'peakasutaja'): ?>
 			  <div class="control-group">	
	          <label class="control-label" for="event_user" name="">Kasutaja</label>  
	          <div class="controls">
		          <select name="event_user" id="event_user">
		           <?php foreach ($users as $user): ?>
		           <option><?php echo $user->username ?></option>
		           <?php endforeach ?>
		          </select>
	          </div>
	          </div>
	      	  <?php endif?>
	      	  <?php if($session->user->usertype === 'tavakasutaja'): ?>
	      	  	<input type="hidden" name="event_user" value="<?php echo $session->user->username ?>" />
	      	  <?php endif?>


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
//Global variables
var row_count;
var no_rec_per_page=10; 

/*
*
*
*Datepikcer language
*
*
*/
$.fn.datepicker.dates['et'] = {
    days: ['Pühapäev', 'Esmaspäev', 'Teisipäev', 'Kolmapäev', 'Neljapäev', 'Reede', 'Laupäev'],
    daysShort: ['P','E','T','K','N','R','L'],
    daysMin: ['P','E','T','K','N','R','L'],
    months: ["jaanuar","veebruar","märts","aprill","mai","juuni","juuli", "august", "september", "oktoober", "november", "detsember"],
    monthsShort: ['jaan','veebr','märts','apr','mai','juuni','juuli','aug','sept','okt','nov','dets']
}
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
function populateTable(start, end){ 
	if (!(start === '') && !(end === '')){
		 var deltastart = parseInt(formatPaginationDate(start) / 1000);
		 var deltaend = parseInt((formatPaginationDate(end) / 1000)+86400);
		$('#loading-indicator').show();
		$.getJSON('events/_getEvents.php?start=' + deltastart + "&end=" + deltaend, function(data) {
			var r = new Array();
			var j = -1, recordId, i;
			r[++j] = '<thead class="cf"><tr><th>Pealkiri</th><th>Kasutaja</th><th>Ruum</th><th>Algus</th><th>Lõpp</th><th style="width: 20%">Kirjeldus</th><th>Viimati muudetud</th><th>Viimane muutja</th><th>Broneeringu tüüp</th></tr></thead><tbody>';
				for (i in data){
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
			$('#event-table').empty();
			if (i === undefined){
			 i = -1;
			}
			row_count = parseInt(i)+1;		
			$("#row-count").show().text("Kokku " + row_count + " kirjet");
			$('#event-table').html(r.join(''));

			$('#loading-indicator').hide();

			pagination(row_count, no_rec_per_page);
			if ($('#hidden-usertype').text() === 'peakasutaja'){
				tableRowClick();
			}
		});
	}
}
/*
*
*
*Need to have this function to get foramted datetime which is compatible for all browsers
*
*
*/

function formatPaginationDate(date_time){
	var date = date_time.split("-");
	return new Date(date[0],date[1]-1,date[2]);  
}

function formatTimestamp(date_time){
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
*Global search field is used
*Build and populate the table with data from JSON
*
*
*/
function buildSearchTable(data){
	var r = new Array();
		var j = -1, recordId, i;
		r[++j] = '<thead class="cf"><tr><th>Pealkiri</th><th>Kasutaja</th><th>Ruum</th><th>Algus</th><th>Lõpp</th><th style="width: 20%">Kirjeldus</th><th>Viimati muudetud</th><th>Viimane muutja</th><th>Broneeringu tüüp</th></tr></thead><tbody>';
			for (i in data){
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
		$('#event-table').empty();
		if (i === undefined){
			 i = -1;
		}
		row_count = parseInt(i)+1;
		$("#row-count").show().text("Kokku " + row_count + " kirjet");
		$('#event-table').html(r.join(''));
		
		pagination(row_count, no_rec_per_page);
		if ($('#hidden-usertype').text() === 'peakasutaja'){
			tableRowClick();
		}
}
/*
*
*
*Table paggination function
*
*
*/
function pagination(row_count, no_rec_per_page){
	$('#paggination li').remove();
	var no_pages= Math.ceil(row_count/no_rec_per_page);
	for(i=0;i<no_pages;i++)
	{
	$('<li><a href="#">'+(i+1)+'</a></li>').appendTo('#paggination');
	}

	$('table').find('tbody tr').hide();
	var tr=$('table tbody tr');
	var x = 0;
	for(var i=0;i<=no_rec_per_page-1;i++)
	{
	$(tr[i]).show();
	}

	$('#paggination li:first').addClass('active');
	$('#paggination li').click(function(event){
	$('#paggination li').removeClass('active');
	$(this).addClass('active');
	$('table').find('tbody tr').hide();
	for(i=($(this).text()-1)*no_rec_per_page;i<=$(this).text()*no_rec_per_page-1;i++)
	{
	$(tr[i]).show();
	}

	});
}
/*
*
*
*DOCUMENT READY
*
*
*/

$(document).ready(function() {	

	//When page is loaded initially hide the error DIV
	$('#alert').hide();

	//How many rows per page
	$('#rows_per_page').bind('change', function () {
		no_rec_per_page = this.value; 
	    pagination(row_count, no_rec_per_page);
	});

	//Tooltip for the search
	$('#global-search-div').tooltip({
		trigger: 'hover',
		title: 'Otsitakse üle kõikide väljade. Perioodi muutmisel tuleb uuesti otsida.',
		placement: 'bottom'
	});

	//Default date view range for bookings (one week, starting from current date)
	var myDate = new Date();
	var default_searchStartDate = myDate.getFullYear() + '-' + (myDate.getMonth()+1) + '-' + myDate.getDate();
	var default_searchEndDate = myDate.getFullYear()+ '-' + (myDate.getMonth()+1) + '-' + (myDate.getDate()+7);
	$('#search-start-date').val(default_searchStartDate);
	$('#search-end-date').val(default_searchEndDate);
	

	var start = default_searchStartDate;
	var end = default_searchEndDate;

	$('#search-start-date')
	    .datepicker({
	    format : 'yyyy-mm-dd',
		weekStart: 1,
		language: 'et',
		autoclose: true
		
	  }).on('changeDate', function(ev) {
			start = $(this).val();
	        if (formatPaginationDate(end) >= formatPaginationDate(start)){   
	        	 $('#alert').hide();
	        	 $("#global-search").val('');
	        	 populateTable(start, end);
	        } else {
	        	$('#alert').show().text('Perioodi algus ei saa olla hilisem perioodi lõpust');
	        }
	});
	$('#search-end-date')
	    .datepicker({
	    format : 'yyyy-mm-dd',
		weekStart: 1,
		language: 'et',
		autoclose: true
		
	  }).on('changeDate', function(ev) {
	        end = $(this).val();
	        
	        if (formatPaginationDate(end) >= formatPaginationDate(start)){
	        	 $('#alert').hide();
	        	 $("#global-search").val('');
	        	 populateTable(start, end);
	        } else {
	        	$('#alert').show().text('Perioodi lõpp ei saa olla varasem perioodi algusest');
	        }
	});




	populateTable(start, end); 

	$('a[href="#addevent"]').click(function(){
		$("#date-start").datetimepicker({
	 	format: 'yyyy-mm-dd hh:ii',
	 	minuteStep: 15,
	 	weekStart: 1,
	 	autoclose: true,
	 	startDate: new Date()
	 });
		$("#date-end").datetimepicker({
	 	format: 'yyyy-mm-dd hh:ii',
	 	minuteStep: 15,
	 	weekStart: 1,
	 	autoclose: true,
	 	startDate: new Date()
	 });

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
      	var form_start = $('#date-start').val();
	  	var form_end =  $('#date-end').val();
        var event_heading = $('#event_heading').val();
        var date = new Date();
        if (form_start === '' || form_end === '' || event_heading === '') {
        	bootstrap_alert.error('<strong><h4>Lisamine ebaõnnestus!</h4></strong> Palun täitke kõik väljad.');     
        //Fields are fullfilled 
        } else { //2
        	var start_date = formatTimestamp(form_start);
        	var end_date = formatTimestamp(form_end);
        //Check if the user has picket a date from the past
        	if (date > start_date  || date > end_date) {
        		bootstrap_alert.error('<strong><h4>Lisamine ebaõnnestus!</h4></strong> Broneeringu algus ega lõpp ei saa olla minevikus.');
        	}
        //Check if start and end dates are not the same
        	else if (form_start == form_end) {
        		bootstrap_alert.error('<strong><h4>Lisamine ebaõnnestus!</h4></strong> Broneeringu algus- ja lõpukellaaeg ei saa olla samad.');
        	}
        //Check if start is before end
        	else if (form_start > form_end) {
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
    var minlength = 3;

    $("#global-search").keyup(function () {
        var that = this,
        value = $(this).val();

        if (value.length >= minlength ) {

        start = $('#search-start-date').val();
		end = $('#search-end-date').val();
		
		if (!(start === '') && !(end === '')){
			var delta_start = parseInt(formatPaginationDate(start) / 1000);
			var delta_end = parseInt((formatPaginationDate(end) / 1000)+86400);
		} else {
			var delta_start = undefined;
			var delta_end = undefined;
		}
		$('#loading-indicator').show();

	            $.ajax({
	                type: "GET",
	                url: "events/_getEvents.php",
	                data: {
	                    'search_keyword' : value,
	                    'start' : delta_start,
	                    'end' : delta_end
	                },
	                dataType: "json",
	                success: function(msg){
	                    //we need to check if the value is the same
	                    if (value==$(that).val()) {                	
	                    //Receiving the result of search here
	                    buildSearchTable(msg);
	                    $('#loading-indicator').hide();
	                    }
	                }
	            });
        } 
        if (value.length < minlength ) {
        	populateTable(start, end);
        }
    });

});

function tableRowClick(){
	//Open modal to change or delete types
	$("tr").on('click',function(){
	    if(this.id){
	      var trId = this.id;
	       $("#myModal").modal('show');
	    }
	});
}
</script>
<?php endif ?>