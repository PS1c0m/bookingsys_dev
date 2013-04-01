<?php 
require_once "include/Session.php";
$session = new Session();
require_once "include/db.php";
$table = "room";
$rooms = R::findAll($table);
?>
<script type="text/javascript" src="js/index.js"></script>

<!-- <div class="tabbable">
     <ul id="myCalTabs" class="nav nav-tabs" > -->

<div class="tabbable tabs-left">
	<ul id="myCalTabs" class="nav nav-tabs span2">
     	<li class='active'><a href='#first-tab-info' data-toggle="tab">Info</a></li>
     	<?php foreach ($rooms as $room) : ?>
		<li class='hidden-phone'><a data-toggle="tab" id="<?php echo $room->room_nr; ?>" href='#room<?php echo $room->room_nr; ?>'><?php echo $room->room_nr; ?></a></li>
		<?php endforeach ?>
	</ul>
    <div class="tab-content span9" id="tab-content">
    	
    	<div class="tab-pane active" id="first-tab-info">
		<?php if (isset($session->user)): ?>

			<!-- Sisseloginud kasutaja INFO -->
	    	<div class="alert alert-info">
			<a class="close" data-dismiss="alert">×</a> 
			<strong>Juhend!</strong> 
			<ul>
			<li>Vasakul ribamenüüs on ruumide kalendrid kuhu on võimalik teha broneeringuid.</li>
			<li>Ruumide suuruse, tüübi ja kirjelduse kohta saab infot allolevast tabelist.</li>
		    <li>Terve päeva broneerimiseks vajutage kalendri kuuvaates vastavale kuupäevale.</li>
		    <li>Kindla ajavahemiku broneerimiseks vajutage kalendri nädala- 
		    või päevavaates hiirega algusajale ja vedage nuppu all hoides kuni broneeringu lõpukellaajani.</li>
			</ul>
			</div>
		<section id="no-more-tables">
			<table class="table table-striped table-hover table-bordered" id="rooms-table-calendar">
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
		<?php else: ?>

			<!-- Registreerimata kasutaja INFO -->
			<div class="alert alert-info">
			<a class="close" data-dismiss="alert">×</a> 
			<strong>Info!</strong>
			<ul>
			<li>Külastajatel on võimalik näha ainult kalendris olevaid broneeringuid.</li>
			<li>Ruumide ja kalendrite kohta on põhjalikum info ainult registreeritud kasutajatele.</li>
			</ul>
			</div>

		<?php endif ?>

    	</div><!--#first-tab-info-->

    	<?php foreach ($rooms as $room) : ?>   	
        <div class="tab-pane" id="room<?php echo $room->room_nr; ?>"></div> 
        <?php endforeach ?>
    </div> 

</div>

<?php if (isset($session->user)): ?>
<script type="text/javascript">
$(document).ready(function() {
//Get JSON data from DB
$.ajax({
    url: "room/_getRooms.php",
    dataType: "json",
    async: false,
    cache: true, //enable ajax caching
    success: function(data) {
      originalJson = data;
    }
  }); 
//Build and populate the table with data from JSON 
var table_object = $('#rooms-table-calendar');
$.each(originalJson, function(index, value){
   table_object.append($(
   	'<tr><td  data-title="Ruumi number" 	id="room_nr">'		+value.room_nr+
   	'</td><td data-title="Ruumi tüüp" 		id="type">'			+value.type+
   	'</td><td data-title="Istekohtade arv" 	id="size">'			+value.size+
   	'</td><td data-title="Ruumi kirjeldus" 	id="description">'	+value.description+
   	'</td></tr>'));
});

});
</script>
<?php endif ?>