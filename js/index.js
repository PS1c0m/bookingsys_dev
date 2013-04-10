var isValid;
$.ajax({
  url: "_isValid.php",
  dataType: "json",
  async: false,
  success: function(data) {
    isValid = data;
  }
});
//Used for the modal datetime input
function loadDateTimePicker(){
  $("input.datetimepicker").datetimepicker({
    format: 'yyyy-mm-dd hh:ii',
    autoclose: true,
    minuteStep: 15,
    weekStart: 1
   });
}
/*
*
*
*Open Modal and Change/View existing event
*
*
*/
function showEvent(id, room, calendar, unixtime_deltastart, unixtime_deltaend) {
  $.post("events/existingEventModal.php", {id : id},
   function(data) {
     $("#myModal").html(data);
     $("#event-change-form").hide();
     $("#myModal").modal('show');
     loadDateTimePicker();
     $('#event-change-submit').click( function() {     
      $("#static-event-data").hide(); 
      $("#event-change-form").show();  
            $("#event-change-submit").click( function(){
              //Get inputs--->>
                var inputs = $('#event-change-form :input'); //Gather html input fields
                var values = {}; //Array
                inputs.each(function() {  //Each input put into array object
                    values[this.name] = $(this).val();
                });
              //--->>Input end

            if (values.date_start === '' || values.date_end === '' || values.event_heading === '') {
              bootstrap_alert.error('<strong><h4>Muutmine ebaõnnestus!</h4></strong> Palun täitke kõik väljad.');     
            //Fields are fullfilled 
            } else { //else1
              var start_date = formatDate(values.date_start);
              var end_date = formatDate(values.date_end);
              var date = new Date();
            //Check if the user has picket a date from the past
              if (date > start_date  || date > end_date) {
                bootstrap_alert.error('<strong><h4>Muutmine ebaõnnestus!</h4></strong> Broneeringu algus ega lõpp ei saa olla minevikus.');
              }
            //Check if start and end dates are not the same
              else if (values.date_start === values.date_end) {
                bootstrap_alert.error('<strong><h4>Muutmine ebaõnnestus!</h4></strong> Broneeringu algus- ja lõpukellaaeg ei saa olla samad.');
              }
            //Check if start is before end
              else if (values.date_start > values.date_end) {
                bootstrap_alert.error('<strong><h4>Muutmine ebaõnnestus!</h4></strong> Broneeringu algusaeg ei saa olla hilisem lõpukellajast.');
              }
            // If not let's send the data to PHP for server-side proccessing via AJAX POST
               else { //else2
                $.ajax({
                        url: "events/_modifyEvent.php",
                        type: "POST",
                        data: { 
                          id : id,
                          date_end: values.date_end,
                          date_start: values.date_start,
                          event_description: values.event_description,
                          event_heading: values.event_heading,
                          event_type: values.event_type,
                          event_user: values.event_user,
                          room_name: values.room_name
                        },
                        success: function(msg) {
                           if(msg == 'error') { 
                             bootstrap_alert.error('<strong><h4>Muutmine ebaõnnestus!</h4>Broneeringut ei saanud muuta, kuna seda ei leitud süsteemist.</strong>');
                           } else if (msg === 'error1') {
                             bootstrap_alert.error('<strong><h4>Muutmine ebaõnnestus!</h4>Broneeringu algus ei saa olla hilisem broneeringu lõpust.</strong>');
                           } else if (msg === 'error2') {
                             bootstrap_alert.error('<strong><h4>Muutmine ebaõnnestus!</h4>Kõik väljad peavad olema täidetud.</strong>');
                           } else if (msg === 'error3') {
                             bootstrap_alert.error('<strong><h4>Muutmine ebaõnnestus!</h4>Antud ajad kattuvad süsteemis varasemalt olemasoleva broneeringuga.</strong>');
                           } else {
                             //success do something   
                             calendar.fullCalendar('removeEvents');
                             calendar.fullCalendar('addEventSource', pullEvents(room, unixtime_deltastart, unixtime_deltaend));
                             calendar.fullCalendar('rerenderEvents'); 
                             $("#myModal").modal('hide');
                           }                 
                        }
                      });
                  }//else2
              } //else1
      });
     });
      //If event-delete-submit is clicked delete the event
      $("#event-delete-submit").click( function(){
          bootstrap_alert.error('<h4>Hoiatus!</h4>Olete kindel, et soovite antud broneeringut kustutada? </br>Kinnituseks vajutage veelkord "Kustuta"');
              $("#event-delete-submit").click( function(){
                     $.ajax({
                        url: "events/_deleteEvent.php",
                        type: "POST",
                        data: { id : id },
                        success: function(msg) {
                           if(msg == 'error') { 
                             //error do something 
                             bootstrap_alert.error('<strong><h4>Kustutamine ebaõnnestus!</h4>Broneeringut ei saanud kustutatada, kuna seda ei leitud süsteemist.</strong>');
                           } else {
                             //success do something   
                             calendar.fullCalendar('removeEvents');
                             calendar.fullCalendar('addEventSource', pullEvents(room, unixtime_deltastart, unixtime_deltaend));
                             calendar.fullCalendar('rerenderEvents'); 
                             $("#myModal").modal('hide');
                           }                 
                        }
                      });
                   });
       });     
   });
}
/*
*
*
*Open Modal and Add new event to calendar
*
*
*/
function newEvent(starting_date, ending_date, allDay, room, calendar, unixtime_deltastart, unixtime_deltaend) { 
  $.post("events/newEventModal.php", { starting_date : starting_date, ending_date : ending_date, allDay : allDay, room : room },
   function(data) {
     $("#myModal").html(data);
     $("#checkbox_inputs").hide();
     $("#myModal").modal('show');
     loadDateTimePicker();
     //Show recurring events inputs
    /*  $('#recurring_event').click(function () {
          $("#checkbox_inputs").toggle(this.checked);
      }); */
      /*
      *
      *
      *Add new event from FORM modal (Lisa Broeering)
      *
      *
      */
      $('#event-add-submit').click( function() {
      
   /*   if($('#recurring_event').is(':checked')){ 
         var recurring_event_end =  $('#recurring_event_end').val();
         console.log(recurring_event_end);
      } */
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
              else if (start === end) {
                bootstrap_alert.error('<strong><h4>Lisamine ebaõnnestus!</h4></strong> Broneeringu algus- ja lõpukellaaeg ei saa olla samad.');
              }
            //Check if start is before end
              else if (start > end) {
                bootstrap_alert.error('<strong><h4>Lisamine ebaõnnestus!</h4></strong> Broneeringu algusaeg ei saa olla hilisem lõpukellajast.');
              }
            // Now check if the dates are overlapping with existing events in DB
              else if (checkOverlapping(start_date, end_date, room_nr, unixtime_deltastart, unixtime_deltaend)){
                bootstrap_alert.error('<strong><h4>Lisamine ebaõnnestus!</h4></strong> Antud ajad kattuvad süsteemis varasemalt olemasoleva broneeringuga.');
            // If not let's send the data to PHP for server-side proccessing via AJAX POST
              } else { //3
                $.ajax({
                        url: "events/_addEvent.php",
                        type: "POST",
                        data: $("#event-add-form").serialize(),
                        success: function(msg) {
                           if(msg === 'error1') {
                             //error do something
                             bootstrap_alert.error('<strong><h4>Lisamine ebaõnnestus!</h4></strong> Broneeringu algusaeg ei saa olla hilisem lõpukellajast.');
                           } else if(msg === 'error2') { 
                             bootstrap_alert.error('<strong><h4>Lisamine Ebaõnnestus!</h4></strong> Broneeringu väljad peavad olema täidetud.');
                           } else {
                            //success do something
                            //bootstrap_alert.success('<strong>Broneering edukalt süsteemi lisatud.</strong>');
                            calendar.fullCalendar('removeEvents');
                            calendar.fullCalendar('addEventSource', pullEvents(room, unixtime_deltastart, unixtime_deltaend));
                            calendar.fullCalendar('rerenderEvents');
                            $("#event-add-form")[0].reset(); //Clear data after successful submit
                            $("#myModal").modal('hide');
                           }                 
                        }
                      }); //ajax
              } //else3
            } //else2
      }); // #event-add-submit
   });
} // fn newEvent
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
*Alerts
*
*
*/
bootstrap_alert = function() {}
bootstrap_alert.success = function(message) {
            $('#alert_placeholder3').show().html('<div class="alert alert-success"><a class="close" data-dismiss="alert">×</a><span>'+message+'</span></div>');
        };
bootstrap_alert.error = function(message) {
            $('#alert_placeholder3').show().html('<div class="alert alert-error"><a class="close" data-dismiss="alert">×</a><span>'+message+'</span></div>');    
        };
/*
*
*Function for form modal check
*Check if the dates are overlapping with existing events
*
*
*/
function checkOverlapping(starting, ending, room_nr, unixtime_deltastart, unixtime_deltaend){
    var jsonData;
     $.ajax({
        url: "events/_getEvents.php?room_nr=" + room_nr + "&start=" + unixtime_deltastart + "&end=" + unixtime_deltaend,
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
    for(var i in events){
      if (room_nr == events[i].room_nr){
            if(!(events[i].start >= ending || events[i].end <= starting)){
                return true; //Match, dates are overlapping    
           }
      }
    }
    return false; //No overlapping dates  
}  
/*    
//DISABLED - Check if there are overlapping events on the calendar
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
      allDay: value.allDay
    };
    events.push(event);
  });
    for(var i in events){
        if(events[i].id !== eventid.id && events[i].room === eventid.room){
            if(!(events[i].start >= eventid.end || events[i].end <= eventid.start)){
                return true;           
            }
        }
    }
    return false;
}
*/
function initializeCalendar(roomId, room_name){
  var calendar = $(roomId).fullCalendar({
    //events: room_events,
    lazyFetching: true,
    //weekNumbers: true,
    //dayClick: isValid ? dayClickHandler : null , // activate on click
    monthNames: ["jaanuar","veebruar","märts","aprill","mai","juuni","juuli", "august", "september", "oktoober", "november", "detsember" ], 
    monthNamesShort: ['jaan','veebr','märts','apr','mai','juuni','juuli','aug','sept','okt','nov','dets'],
    dayNames: [ 'Pühapäev', 'Esmaspäev', 'Teisipäev', 'Kolmapäev', 'Neljapäev', 'Reede', 'Laupäev'],
    dayNamesShort: ['P','E','T','K','N','R','L'],
    buttonText: {
      today: 'Täna',
      month: 'Kuu',
      week: 'Nädal',
      day: 'Päev'
     },
    allDaySlot: false,
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
/*
  //DISABLED - Dragging events from one day to other day ( chagning dates )
  eventDrop: function(event, dayDelta, minuteDelta, allDay, revertFunc) {
    if (isOverlapping(event)) { revertFunc();
    } else {
          var starting_date = $.fullCalendar.formatDate(event.start, "yyyy-MM-dd HH:mm:ss");
          var ending_date = $.fullCalendar.formatDate(event.end, "yyyy-MM-dd HH:mm:ss");
          if (!(ending_date)) { // <-- if we don't have ending date like allDay events don't have them then we need to give some value to it
            ending_date = starting_date; //<-- for a moment it's good to give same value as starting date.
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
    }
  }, 
*/
  //Event rendering to calendar + extended fields for events popover's
  eventRender: function(event, element) {
      element.find(".fc-event-title")
       .append(' [' + event.type + ']');
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
      var view = calendar.fullCalendar('getView');
      var unixtime_deltastart = parseInt(view.visStart.getTime() / 1000);
      var unixtime_deltaend = parseInt(view.visEnd.getTime() / 1000);
      showEvent(event.id, room_name, calendar, unixtime_deltastart, unixtime_deltaend);
    }     
  },
  //Lazy loading, if view changes then ask the events that should fit on that view, etc. month/week/previous/next
  viewDisplay: function(view) { 
    //if(view.name == 'month'){
      var unixtime_deltastart = parseInt(view.visStart.getTime() / 1000);
      var unixtime_deltaend = parseInt(view.visEnd.getTime() / 1000);
      $(roomId).fullCalendar('removeEvents');
      $(roomId).fullCalendar('addEventSource', pullEvents(room_name, unixtime_deltastart, unixtime_deltaend));
      $(roomId).fullCalendar('rerenderEvents');
    //}
  },

  //Clicking and dragging new event dates - need to be authenticated
  select: function(start, end, allDay) {

    //Do not allow to pick dates from the past
    var view = calendar.fullCalendar('getView');
    var unixtime_deltastart = parseInt(view.visStart.getTime() / 1000);
    var unixtime_deltaend = parseInt(view.visEnd.getTime() / 1000);

    var current_date = new Date();
    //If current view is month then allow to pick whole day and open modal
    if(view.name === "month"){
        var x = new Date();
        current_date = new Date(x.getFullYear(), x.getMonth(), x.getDate());
    }
    //If user has clicked or dragged a timerange from the past, do not allow this
    if (current_date > start) {
      calendar.fullCalendar('unselect');
      return false;
    }

    //Check if we are dragging our new event to an existing event.
    //--->
    if(view.name === "agendaWeek"){
      var room_events = pullEvents(room_name, unixtime_deltastart, unixtime_deltaend);
      for (var i in room_events){
          if (!(room_events[i].start >= end || room_events[i].end <= start)){
                calendar.fullCalendar('unselect');
                return false;
            }
      }
    }//<---

    //If OK then let's open a modal and pass starting-, ending dates and room number to form
     var starting_date = $.fullCalendar.formatDate(start, "yyyy-MM-dd HH:mm");
     var ending_date = $.fullCalendar.formatDate(end, "yyyy-MM-dd HH:mm");
     newEvent( starting_date, ending_date, allDay, room_name, calendar, unixtime_deltastart, unixtime_deltaend);
     calendar.fullCalendar('unselect');

  }

  });
}
/*
*
*
*Pull specific room events from server and make an object compatible for fullcalendar plugin
*
*
*/
function pullEvents(room_name, start, end){
  var calevents;
  $.ajax({
    url: "events/_getEvents.php?room_nr=" + room_name + "&start=" + start + "&end=" + end,
    dataType: "json",
    async: false,
    cache: false, //disable ajax caching
    success: function(data) {
      calevents = data;
    }
  });

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
      //user: val.user,
      room: val.room,
      type: val.type
      /*last_changed_by: val.last_changed_user,
      changing_date: val.changing_date */
    };
    events.push(event);
  });
  //var room_events = $.grep(events, function(e){ return e.room == room_name; });
  return events;
}

$(document).ready(function() {
  /*
  *
  *
  *Build and populate the table in Info tab with data from JSON 
  *
  *
  */
  $.getJSON('room/_getRooms.php', function(data) {
    var r = [];
    var j = -1;
    r[++j] = '<thead class="cf"><tr><th>Ruumi number</th><th>Ruumi tüüp</th><th>Istekohtade arv</th><th style="width: 50%">Ruumi kirjeldus</th></tr></thead><tbody>';
      for (var i in data){
          var d = data[i];
          r[++j] = '<tr><td  data-title="Ruumi number" id="room_nr">';
          r[++j] = d.room_nr;
          r[++j] = '</td><td data-title="Ruumi tüüp" id="type">';
          r[++j] = d.type;
          r[++j] = '</td><td data-title="Istekohtade arv" id="size">';
          r[++j] = d.size;
          r[++j] = '</td><td data-title="Ruumi kirjeldus" id="description">';
          r[++j] = d.description;
          r[++j] = '</td></tr>';
      }
    r[++j] = '</tbody>';
    $('table#rooms-table-calendar').html(r.join(''));
  });
  /*
  *
  *
  *TABS clicked for a specific room, initialie fullcalendar plugin
  *
  *
  */
  $('#myCalTabs a').click(function(e){  
    e.preventDefault();
    var room_name = this.id;
    var roomId='#room' + $(this).text();
    $(this).tab('show');
    if (!(room_name === '')) {
      $(roomId).fullCalendar('destroy'); //because of using tabs need to destroy cal before loading new
      initializeCalendar(roomId, room_name);
    }
  });
});