<?php
require_once "include/Session.php";
$session = new Session();

unset($session->user);
header( "location: index.php" );
