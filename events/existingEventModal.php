<?php 
if (substr_count($_SERVER['HTTP_ACCEPT_ENCODING'], 'gzip')) ob_start("ob_gzhandler"); else ob_start();

require_once "/../include/Session.php";
$session = new Session();
if (!isset($session->user)) {
  	$target = $_SERVER['REQUEST_URI'];
    $message = $session->message; 
    $username = $session->username;
    unset($session->message);
    unset($session->username);
    header("location: ../index.php"); exit();
}
//If someone want's to directly load this base, forward him back to main page
if (!(isset($_POST['id']))){
	header("location: ../index.php"); exit();
} else {
	require_once "/../include/db.php";
	$event_table = "event";
	//if id is provided load the data from DB
	if (isset($_POST['id'])){
	  $id = $_POST['id'];

	  $rooms = R::findAll("room");
	  $event_types = R::findAll("typeinfo");
	  $users = R::findAll("user");
	  $event = R::findOne($event_table, "id = :id", array(':id' => $id));
	  
	  	//If event was found from DB
	  if (isset($event)){
		  $id = $event->id;
	      $title = $event->title;
	      $start =  $event->start;
	      $end =  $event->end;
	      //$allDay = $event->allDay;
	      $changing_date = $event->changing_date;
	      $last_changed_user = $event->last_changed_user;
	      $description = $event->description;
	      $type = $event_types[$event->typeinfo_id]['type'];
	      $user = $users[$event->user_id]['username'];
	      $room_nr = $rooms[$event->room_id]['room_nr'];
	  } else {
	  	//Event does not exsits
	  	echo "</br><div class='alert alert-error'><strong>Broneeringut ei leitud! Keegi on selle kustutanud, värskenda kalendrit.</strong></div>";
	  }
	}
}
?>
<?php if (isset($event)): ?>
<div class="modal-header">
      <button type="button" class="close" data-dismiss="modal" aria-hidden="true">×</button>
      <h3 id="myModalLabel">Broneering</h3>
      <p class="text-info"><small>Viimati muudetud kasutaja <?php echo $last_changed_user ?> poolt <?php echo $changing_date ?></small></p>
</div>
<div class="modal-body">
	<div id="static-event-data">
		<dl class="dl-horizontal">	
		  <dt>Ruumi number:</dt>
		  <dd><?php echo $room_nr ?></dd>

		  <dt>Broneeringu pealkiri:</dt>
		  <dd><?php echo $title ?></dd>

		  <dt>Kasutaja:</dt>
		  <dd><?php echo $user ?></dd>

		  <dt>Broneeringu tüüp:</dt>
		  <dd><?php echo $type ?></dd>

		  <dt>Algus:</dt>
		  <dd><?php echo $start ?></dd>

		  <dt>Lõpp:</dt>
		  <dd><?php echo $end ?></dd>

		  <dt>Broneeringu kirjeldus:</dt>
		  <dd> <?php echo $description ?></dd>	  
		</dl>
	</div>

	<form class="form-horizontal" data-async id="event-change-form" name="event-change">
	    <fieldset>
	      	  <div class="control-group">
	          <!-- Text input-->
	          <label class="control-label" for="event_heading">Pealkiri</label>
	          <div class="controls">
	          <input type="text" placeholder="Broneeringu pealkiri" name="event_heading" id='event_heading' value="<?php echo $title?>">
	          </div>
	          </div>

	          <div class="control-group">	
	          <!-- Text input-->
	          <label class="control-label" for="event_type" name="">Broneeringu tüüp</label>  
	          <div class="controls">
		          <select name="event_type" id="event_type">
		           <?php foreach ($event_types as $eventtype): ?>
		           <option
		           	<?php if ($type === $eventtype->type) echo "selected" ?>>
		           	<?php echo $eventtype->type ?>
		           </option>
		           <?php endforeach ?>
		          </select>
	          </div>
	          </div>

		 	  <div class="control-group">
	          <!-- Text input-->
	          <label class="control-label" for="date-start">Algus</label>
	          <div class="controls">
	          <input type="text" placeholder="yyyy-mm-dd hh:mm" name="date-start" class="datetimepicker" id='date-start' value="<?php echo $start?>">
	          </div>
	          </div>

	          <div class="control-group">
	          <!-- Text input-->
	          <label class="control-label" for="date-end">Lõpp</label>
	          <div class="controls">
	          <input type="text" placeholder="yyyy-mm-dd hh:mm" name="date-end" class="datetimepicker" id='date-end' value="<?php echo $end?>">
	          </div>
	          </div>

	          <div class="control-group">
	          <!-- Textarea -->
	          <label class="control-label">Broneeringu kirjeldus</label>
	          <div class="controls">
	          <div class="textarea">  
	          <textarea rows="3" type="" class="" name="event_description" id="event_description"><?php echo $description ?></textarea>
	          </div>
	          </div>
	          </div> 

	    </fieldset>
	</form>
			<!-- ALERT -->
	        <div id="alert_placeholder3"></div>
			<div class="pull-right">
	        <input class="btn" type="submit" id="type-cancel-submit" data-dismiss="modal" aria-hidden="true" value="Sulge"/>
		   	<?php if(($session->user->usertype === 'peakasutaja') || ($session->user->username === $user)): ?>
		    <input class="btn btn-success" type="submit" id="event-change-submit" value="Muuda"/>
		    <input class="btn btn-danger" type="submit" id="event-delete-submit" value="Kustuta"/>
			<?php endif ?>
        	</div> 
</div><!--modal-body-->
<?php endif ?>