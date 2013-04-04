<?php
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
$table = "event";

if (isset($_POST['starting_date'])){
  	$starting_date = $_POST['starting_date'];
  	$ending_date =$_POST['ending_date'];
  	$allDay = $_POST['allDay'];
}

if (isset($_POST['SUBMIT_ADD'])) {
	 $event = R::dispense($table);
   $event->title = $_POST['title'];
	 $event->eventtype = $_POST['eventtype'];
	 $event->start = $_POST['starting_date'];
	 $event->end = $_POST['ending_date'];
	 $event->allDay = $_POST['allDay'];
   R::store($event); 
   header("location: ../index.php"); exit();
}

?>
<div class="modal-header">
      <button type="button" class="close" data-dismiss="modal" aria-hidden="true">×</button>
      <h3 id="myModalLabel">Broneeri ruum</h3>
</div>
    <div class="modal-body">
          <form id="modal-form" class="form-horizontal" action="events/new_event.php" METHOD=POST>
          <input type="hidden" name="starting_date" value="<?php echo $starting_date?>" />
          <input type="hidden" name="ending_date" value="<?php echo $ending_date?>" />
          <input type="hidden" name="allDay" value="<?php echo $allDay?>" />

				   <p>Starting time:<?php echo $starting_date?></p>
				   <p>Ending time:<?php echo $ending_date?></p>
           <p>Select Booking Type:<select name="eventtype"></p>
			           <?php foreach ($eventtypes as $val): ?>
			           <option
			            <?php if (isset($choices->eventtype[$val])) echo "selected" ?>
			            ><?php echo $val?></option>
			           <?php endforeach ?>
			          </select>
			    <p>Insert the title for booking:<input name="title"></input></p>
          <div class="modal-footer">
              <button class="btn" data-dismiss="modal" aria-hidden="true">Close</button>
              <button class="btn btn-primary" name="SUBMIT_ADD" id="submit">Save changes</button>
          </div>
          </form>   
    </div>