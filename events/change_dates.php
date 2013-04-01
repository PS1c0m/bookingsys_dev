<?php
require_once "include/Session.php";
$session = new Session();
if (!isset($session->user)) {
  $target = $_SERVER['REQUEST_URI'];
    $message = $session->message; 
    $username = $session->username;
    unset($session->message);
    unset($session->username);
    header("location: index.php"); exit();
}
require_once "include/db.php";
$table = "calendarevents";
if (!(isset($_POST['eventid']))){
	header("location: index.php"); exit();
}
//if event modal is loaded and id is provided load the data from DB
if (isset($_POST['eventid'])){
	$id = $_POST['eventid'];
	$new_starting_date = $_POST['starting_date'];
	$new_ending_date = $_POST['ending_date'];
	$new_allDay = $_POST['allDay'];

    $event = R::load($table, $id);
    $event->start = $new_starting_date;
    $event->end = $new_ending_date;
    $event->allDay = $new_allDay;
    R::store($event);
    header("location: index.php"); exit();
}
?>