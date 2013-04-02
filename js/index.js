// obtain the calendar events from the database
var calevents;
$.ajax({
  url: "events/_getEvents.php",
  dataType: "json",
  async: false,
  cache: false, //disable ajax caching
  success: function(data) {
    calevents = data;
  }
});
//var jsonText = JSON.stringify(calevents);
//document.write('<pre>' + jsonText + '</pre>');
// obtain the validation check from the session
var isValid;
$.ajax({
  url: "_isValid.php",
  dataType: "json",
  async: false,
  success: function(data) {
    isValid = data;
  }
});

function showEvent(id) {
  $.post("events/modify_event.php", {id : id},
   function(data) {
     $("#myModal").html(data);

     $("#myModal").modal('show')
   });
};
//Used for the modal datetime input
function loadDateTimePicker(){
  $("input.datetimepicker").datetimepicker({
    format: 'yyyy-mm-dd hh:ii',
    autoclose: true,
    minuteStep: 15,
    weekStart: 1
   });
};

/*
*
*
*Open Modal and Add new event to calendar
*
*
*/
function newEvent(starting_date, ending_date, allDay, room) { 
  $.post("events/calendar_event_modal.php", { starting_date : starting_date, ending_date : ending_date, allDay : allDay, room : room },
   function(data) {
     $("#myModal").html(data);
     $("#myModal").modal('show');
     loadDateTimePicker();
      /*
      *
      *
      *Add new event from FORM modal (Lisa Broeering)
      *
      *
      */
      $('#event-add-submit').click( function() {
      //assign variables and check if the fields are fullfilled
            var start = $('#date-start').val();
            var end =  $('#date-end').val();
            var event_heading = $('#event_heading').val();
            var room_nr = $('#room_name').val();
            var date = new Date();
            if (start === '' || end === '' || event_heading === '' || room_nr ==='') {
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
              else if (checkOverlapping(start_date, end_date, room_nr)){
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
                            //bootstrap_alert.success('<strong>Broneering edukalt süsteemi lisatud.</strong>');
                            $("#event-add-form")[0].reset(); //Clear data after successful submit
                            $("#myModal").modal('hide');

                            var roomId='#room' + room_nr;
                            console.log(roomId);
                            $(roomId).fullCalendar('refetchEvents')
                           }                 
                        }
                      }); //ajax
              } //else3
            } //else2
      }); // #event-add-submit
   });
}; // fn newEvent
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
};
/*
*
*
*Alerts
*
*
*/
bootstrap_alert = function() {}
bootstrap_alert.success = function(message) {
            $('#alert_placeholder3').show().html('<div class="alert alert-success"><a class="close" data-dismiss="alert">×</a><span>'+message+'</span></div>')
        };
bootstrap_alert.error = function(message) {
            $('#alert_placeholder3').show().html('<div class="alert alert-error"><a class="close" data-dismiss="alert">×</a><span>'+message+'</span></div>')       
        };
/*
*
*Function for form modal check
*Check if the dates are overlapping with existing events
*
*
*/
function checkOverlapping(starting, ending, room_nr){
    var jsonData;
     $.ajax({
        url: "events/_getEvents.php",
        dataType: "json",
        async: false,
        success: function(data) {
          jsonData = data;
        }
      });
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
};        
//Check if there are overlapping events on the calendar
function isOverlapping(eventid){
  //
  $.ajax({
    url: "events/_getEvents.php",
    dataType: "json",
    async: false,
    success: function(data) {
    calevents = data;
    }
  });
   var events = []; 
  $(calevents).each(function(index,value){
    var event = {
      id : value.id,
      start: new Date(value.s_year, value.s_month-1, value.s_day, value.s_hour, value.s_minute),
      end: new Date(value.e_year, value.e_month-1, value.e_day, value.e_hour, value.e_minute),
      room: value.room,
      allDay: value.allDay,
    };
    events.push(event);
  });
    for(i in events){
        if(events[i].id != eventid.id && events[i].room == eventid.room){
            if(!(events[i].start >= eventid.end || events[i].end <= eventid.start)){
                return true;           
            }
        }
    }
    return false;
};

$(document).ready(function() {
  /*
  *
  *
  *Get JSON data from DB
  *
  *
  */
  $.ajax({
      url: "room/_getRooms.php",
      dataType: "json",
      async: false,
      cache: true, //enable ajax caching
      success: function(data) {
        originalJson = data;
      }
    }); 
  /*
  *
  *
  *Build and populate the table in Info tab with data from JSON 
  *
  *
  */
  var table_object = $('table#rooms-table-calendar > tbody');
    $.each(originalJson, function(index, value){
     table_object.append($(
      '<tr><td  data-title="Ruumi number"   id="room_nr">'    +value.room_nr+
      '</td><td data-title="Ruumi tüüp"     id="type">'     +value.type+
      '</td><td data-title="Istekohtade arv"  id="size">'     +value.size+
      '</td><td data-title="Ruumi kirjeldus"  id="description">'  +value.description+
      '</td></tr>'));
  });

  /*
  *
  *
  *Get calendar events and make a suitable object for claendar plugin from json data
  *
  *
  */
  var events = []; 
  $(calevents).each(function(ind,val){
    var event = {
      id : val.id,
      title: val.title,
      start: new Date(val.s_year, val.s_month-1, val.s_day, val.s_hour, val.s_minute),
      end: new Date(val.e_year, val.e_month-1, val.e_day, val.e_hour, val.e_minute),
      backgroundColor: val.background,
      textColor: val.color,
      borderColor: "#000",
      allDay: false, //val.allDay,
      description: val.description,
      user: val.user,
      room: val.room,
      type: val.type,
      last_changed_by: val.last_changed_user,
      changing_date: val.changing_date
    };
    events.push(event);
  });
var calendar;
var room_events;
var room_name;
$('#myCalTabs a').click(function(e){

    e.preventDefault();
    room_name = this.id;
    var roomId='#room' + $(this).text();
    room_events = $.grep(events, function(e){ return e.room == room_name; });

    $(this).tab('show');
    $(roomId).fullCalendar('destroy');
    $(roomId).fullCalendar('render');

  calendar = $(roomId).fullCalendar({
  
    events: room_events,
    //dayClick: isValid ? dayClickHandler : null , // activate on click
    defaultView: 'month',
    header: {
        left: 'prev,next today',
      	center: 'title',
      	right: 'month,agendaWeek' //If needed agendaDay can be added (right: 'month,agendaWeek,agendaDay' )
  	},
   	selectable: isValid ? true : false, //if user logged in then selecting and editing is enabled else if not logged int then not enabled
  	selectHelper: isValid ? true : false,
    //editable: isValid ? true : false, //DISABLED eventResize + eventDrop
    firstDay: 1, //start week from monday
    weekMode: 'variable',
    aspectRatio: 1.6, //size of the cell day
    axisFormat: 'HH:mm', //timeformat on a week and day view
    slotMinutes: 15, //how many minutes in one cell -- 1h= 4'15min = 4 cells
    minTime: 7, //week/day table minimum starting time
    maxTime: 21, //calendar ending date
    timeFormat: 'HH:mm{ - HH:mm}', //24h format everywhere

  //DISABLED - Dragging events from one day to other day ( chagning dates )
  eventDrop: function(event, dayDelta, minuteDelta, allDay, revertFunc) {
    if (isOverlapping(event)) { revertFunc();
    } else {
          var starting_date = $.fullCalendar.formatDate(event.start, "yyyy-MM-dd HH:mm:ss");
          var ending_date = $.fullCalendar.formatDate(event.end, "yyyy-MM-dd HH:mm:ss");
          if (!(ending_date)) { // <-- if we don't have ending date like allDay events don't have them then we need to give some value to it
            ending_date = starting_date //<-- for a moment it's good to give same value as starting date.
          }
          $.post("events/change_dates.php", {  //<-- save the chagnes to DB
            starting_date : starting_date, 
            ending_date : ending_date, 
            allDay : event.allDay, 
            eventid : event.id
          });
    }
  },
  //DISABLED - Resizeing event's
  eventResize: function(event, dayDelta, minuteDelta, revertFunc) {
    if (isOverlapping(event)) {
      revertFunc(); 
    };
  },
  //Event rendering to calendar + extended fields for events popover's
  eventRender: function(event, element) {
      element.find(".fc-event-title")
       .append(' ' + event.type);
      element.popover({ 
        animation: true,
        trigger: 'hover',
        placement: 'top',
        html : true,
        container: 'body',
        title:  event.title,
        content: [
        '<b>Ruumi number</b>: ' + event.room  + 
        '</br><b>Broneeringu tüüp</b>: ' + event.type  + 
        '</br><b>Broneeringu kirjeldus</b>: ' + event.description 
        //'</br><b>Kasutaja</b>:' + event.user
        //"<br /><b>Viimati muudetud</b>:" + event.changing_date + -
        //"<br /><b>Viimati muutnud kasutaja</b>:" + event.last_changed_by
        ]
       });
     /* element.find(".fc-event-content")
       .append("<b>Ruum</b>: " + event.room + 
        "<br /><b>Kirjeldus</b>: " + event.description + 
        "<br /><b>Kasutaja</b>:" + event.user +
        "<br /><b>Viimati muudetud</b>:" + event.changing_date +
        "<br /><b>Viimati muutnud kasutaja</b>:" + event.last_changed_by
        );*/
  },

  //Click an event and modal opens to change different fields - need to be authenticated
  eventClick: function (event) {
    if (isValid){
      showEvent(event.id);
    }     
  },

  //Clicking and dragging new event dates - need to be authenticated
  select: function(start, end, allDay) {

    //Do not allow to pick dates from the past
    var view = calendar.fullCalendar('getView');
    var current_date = new Date();
    //If current view is month then allow to pick whole day and open modal
    if(view.name === "month"){
        var x = new Date();
        current_date = new Date(x.getFullYear(), x.getMonth(), x.getDate());
    }
    if (current_date > start) {
      calendar.fullCalendar('unselect');
      return false;
    }

    //Check if we are dragging our new event to an existing event.
    //We are checking it againt the cache, if someone has allready booked something it would not work
    //--->
    for (i in room_events){
        if (!(room_events[i].start >= end || room_events[i].end <= start)){
              calendar.fullCalendar('unselect');
              return false;
          }
    }//<---

    //If OK then let's open a modal and pass starting-, ending dates and room number to form
     var starting_date = $.fullCalendar.formatDate(start, "yyyy-MM-dd HH:mm");
     var ending_date = $.fullCalendar.formatDate(end, "yyyy-MM-dd HH:mm");
     newEvent( starting_date, ending_date, allDay, room_name );
     calendar.fullCalendar('unselect');
  },

  });	

});
});