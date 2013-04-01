<?php
require_once "include/Session.php";
$session = new Session();
if (!isset($session->user)) {
  $target = $_SERVER['REQUEST_URI'];
    $message = $session->message; 
    $username = $session->username;
    unset($session->message);
    unset($session->username);
  header("location: index.php");  exit();
}

require_once "include/db.php";
$table = "calendarevents";

require_once "include/typeinfo.php"; //defines $typeinfo

$eventtypes = array_keys($typeinfo);

//If someone want's to directly load this base, forward him back to main page
if (!(isset($_POST['id']))){
	header("location: index.php"); exit();
}
//if event modal is loaded and id is provided load the data from DB
if (isset($_POST['id'])){
	$id = $_POST['id'];
	$event = R::findOne($table, "id = :id", array(':id' => $id));
	$id = $event->id;
    $starting_date = $event->start;
    $ending_date = $event->end;
    $allDay = $event->allDay;
    $title = $event->title;
    $type = $event->eventtype;
    $choices->eventtype[$type] = "selected";
}
//If event modification is done and modal is POST'ed from the form, save the data to DB
if (isset($_POST['update']) && ($_POST['id'] > 0)) {
   $event = R::load($table, $id);
   $event->title = $_POST['title'];
   $event->eventtype = $_POST['eventtype'];
   R::store($event);
   header("location: index.php"); exit();
}
?>

<div class="modal-header">
      <button type="button" class="close" data-dismiss="modal" aria-hidden="true">×</button>
      <h3 id="myModalLabel">Muuda broneeringut</h3>
    </div>
    <div class="modal-body">
          <form id="theform" method="POST" action="modify_event.php">
          <table>
          <tr>
          <td>starting:</td> 
          <td><?php echo $starting_date?></td>
          </tr>
          <tr>
          <tr>
          <td>type:</td>
          <td>
          <select name="eventtype">
           <?php foreach ($eventtypes as $val): ?>
           <option
            <?php if (isset($choices->eventtype[$val])) echo "selected" ?>
            ><?php echo $val?></option>
           <?php endforeach ?>
          </select>
          </td>
          </tr>
          <tr>
          <td>title:</td>
          <td><textarea name="title"><?php echo $title?></textarea></td>
          </tr>
          </table>
          <div class="modal-footer">
		      <button class="btn" data-dismiss="modal" aria-hidden="true">Close</button>
		      <button class="btn btn-primary" name="update">Save changes</button>
	      </div>
          <input type="hidden" name="id" value="<?php echo $id?>" />
          <input type="hidden" name="starting_date" value="<?php echo $starting_date?>" />
          <input type="hidden" name="ending_date" value="<?php echo $ending_date?>" />
          <input type="hidden" name="allDay" value="<?php echo $allDay?>" />
          </form>
    </div>