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

		<section id="no-more-tables">
			<table class="table table-striped table-hover table-bordered" id="rooms-table-calendar">
			</table>
		</section>

		<!-- Sisseloginud kasutaja INFO -->
	    	<div class="alert alert-info">
			<a class="close" data-dismiss="alert">×</a> 
			<strong>Juhend!</strong> 
			<ul>
			<li>Vasakul ribamenüüs on ruumide kalendrid kuhu on võimalik teha broneeringuid.</li>
			<li>Ruumide suuruse, tüübi ja kirjelduse kohta saab infot ülevalolevast tabelist.</li>
			<li>Broneeringu muutmiseks/kustutamiseks või täpsema info saamiseks vajutage broneeringule.</li>
		    <li>Broneeringuid on võimalik teha nii kuu- kui nädalavaates, vajutades kalendris olevale kuupäevale.</li>
		    <li>Nädalavaates kindla ajavahemiku broneerimiseks vajutage hiirega algusajale ja vedage nuppu all hoides kuni broneeringu lõpukellaajani.</li>
			</ul>
			</div>

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
        <div class="tab-pane hidden-phone" id="room<?php echo $room->room_nr; ?>"></div> 
        <?php endforeach ?>

    </div> 
</div>