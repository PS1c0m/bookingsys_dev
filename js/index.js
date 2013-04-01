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

function editOldBooking(id) {
  //return "add-event.php?" + "stat=" + stat + "&id=" + id;
  $.post("events/modify_event.php", {id : id},
   function(data) {
     $("#myModal").html(data);
     $("#myModal").modal('show')
   });
}
function newBookingRange(starting_date, ending_date, allDay) { 
  $.post("events/new_event.php", { starting_date : starting_date, ending_date : ending_date, allDay : allDay },
   function(data) {
     $("#myModal").html(data);
     $("#myModal").modal('show')
   });
  //return "add-event.php?stat=new&starting_date=" + starting_date + "&ending_date=" + ending_date + '&allDay=' + allDay;
}
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
      allDay: value.allDay,
    };
    events.push(event);
  });
    for(i in events){
        if(events[i].id != eventid.id){
              /**
                var xxx = events[i].start;
                var yyy = events[i].end;
                console.log("events[i].start: ", xxx, '>= event.end: ', eventid.end);
                console.log("events[i].end: ", yyy, ' <= event.start: ', eventid.start);
                console.log("events[i].alldDay: ", events[i].allDay);
                console.log("event.allDay: ", eventid.allDay);  
                */
            if(!(events[i].start >= eventid.end || events[i].end <= eventid.start)){
                return true;           
            } 
        }
    }
    return false;
}


$(document).ready(function() {
  /*
$('#tab-content div').hide();
$('#tab-content div:first').show();
$('#myCalTabs li:first').addClass('active');
*/

// create events suitable to fullCalendar format
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
      allDay: val.allDay,
      //url: isValid ? editUrl(val.id, "old") : null  // activate onclick
      //url: isValid ? editOldBooking(val.id) : null
      //urlhack : null,
      description: val.description,
      user: val.user,
      room: val.room,
      last_changed_by: val.last_changed_user,
      changing_date: val.changing_date
    };
    events.push(event);
  });

var room_events;

$('#myCalTabs a').click(function(e){

    //$('#myCalTabs li').removeClass('active');
    //$(this).parent().addClass('active');
    e.preventDefault();
    var room_name = this.id;
    var roomId='#room' + $(this).text();
    room_events = $.grep(events, function(e){ return e.room == room_name; });
    /*
    $('#tab-content div').hide();
    $(roomId).show();
    */
    $(this).tab('show');
    $(roomId).fullCalendar('destroy');
    $(roomId).fullCalendar('render');
    //calendarEvents(room_events, roomId);

    /*
    $('#myCalTabs a').click(function (e) {

      var room_name = this.id;
      var roomId='#room' + $(this).text();
      e.preventDefault();
      $(this).tab('show');
      room_events = $.grep(events, function(e){ return e.room == room_name; });
      //calendar.fullCalendar('render');
      calendarEvents(room_events, roomId);
      //calendar.fullCalendar('render');
    });*/
var calendar = $(roomId).fullCalendar({
  
  events: room_events,
  //dayClick: isValid ? dayClickHandler : null , // activate on click
  defaultView: 'month',
  header: {
      left: 'prev,next today',
    	center: 'title',
    	right: 'month,agendaWeek,agendaDay'
	},
 	selectable: isValid ? true : false, //if user logged in then selecting and editing is enabled else if not logged int then not enabled
	selectHelper: isValid ? true : false,
  editable: isValid ? true : false,
  firstDay: 1, //start week from monday
  weekMode: 'variable',
  aspectRatio: 1.6, //size of the cell day
  axisFormat: 'HH:mm', //timeformat on a week and day view
  slotMinutes: 15, //how many minutes in one cell -- 1h= 4'15min = 4 cells
  minTime: 7, //week/day table minimum starting time
  maxTime: 21, //calendar ending date
  timeFormat: 'HH:mm{ - HH:mm}', //24h format everywhere
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
          //$('#calendar').fullCalendar('rerenderEvents');
    }
  },
  eventResize: function(event, dayDelta, minuteDelta, revertFunc) {
    if (isOverlapping(event)) {
      revertFunc(); 
    };
  },
  //extended fields for events
  eventRender: function(event, element) {
      element.popover({ 
        animation: true,
        trigger: 'hover',
        placement: 'left',//event.start.getHours()>7?'top':'bottom',
        html : true,
        container: 'body',
        content: [
        '<b>Ruumi number</b>: ' + event.room  + 
        '</br><b>Broneeringu kirjeldus</b>: ' + event.description + 
        '<br /><b>Kasutaja</b>:' + event.user +
        "<br /><b>Viimati muudetud</b>:" + event.changing_date +
        "<br /><b>Viimati muutnud kasutaja</b>:" + event.last_changed_by
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
  /*
  eventRender: function (event, element) {
    if(isValid){
     // element.attr('href', 'javascript:void(0);');
      element.attr('onclick', 'javascript:editOldBooking("' + event.id + '");');
  }*/

  //if we are authenticated then we can click an event and modal opens to change different fields
  eventClick: function (event) {
    if (isValid){
      editOldBooking(event.id);
    }     
  },
  select: function(start, end, allDay) {
    //Chekc if we are dragging our new event to an existing event.
    //We are checking it againt the cache, if someone has allready booked something it would not work
    //--->
    for (i in events){
        if (!(events[i].start >= end || events[i].end <= start)){
              calendar.fullCalendar('unselect');
              return false;
            }
    }//<---
    //If not then let's store it into the DB
     var starting_date = $.fullCalendar.formatDate(start, "yyyy-MM-dd HH:mm:ss");
     var ending_date = $.fullCalendar.formatDate(end, "yyyy-MM-dd HH:mm:ss");
     newBookingRange(starting_date, ending_date, allDay);
     calendar.fullCalendar('unselect');
  },

  });	

});
});