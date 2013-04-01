<?php
require_once "include/Session.php";
$session = new Session();
echo json_encode($session->user);
?>