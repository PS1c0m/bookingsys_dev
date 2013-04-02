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

require_once "/../include/db.php";
$event_types = R::findAll("typeinfo");
$users = R::findAll("user");

if (isset($_POST['starting_date'])){
  	$starting_date = $_POST['starting_date'];
  	$ending_date =$_POST['ending_date'];
  	$allDay = 'false';
  	$room = $_POST['room'];
}
?>

<div class="modal-header">
      <button type="button" class="close" data-dismiss="modal" aria-hidden="true">×</button>
      <h3 id="myModalLabel">Lisa uus broneering ruumi <?php echo $room?></h3>
</div>
<div class="modal-body">
	      <form class="form-horizontal" data-async id="event-add-form" name="event-adding">
	      <fieldset>

	      	  <input type="hidden" name="room_name" id='room_name' value="<?php echo $room?>" />

	          <div class="control-group">
	          <!-- Text input-->
	          <label class="control-label" for="date-start">Algus</label>
	          <div class="controls">
	          <input type="text" placeholder="yyyy-mm-dd hh:mm" name="date-start" class="datetimepicker" id='date-start' value="<?php echo $starting_date?>">
	          </div>
	          </div>

	          <div class="control-group">
	          <!-- Text input-->
	          <label class="control-label" for="date-end">Lõpp</label>
	          <div class="controls">
	          <input type="text" placeholder="yyyy-mm-dd hh:mm" name="date-end" class="datetimepicker" id='date-end' value="<?php echo $ending_date?>">
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
</div><!--modal-body-->
