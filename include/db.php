<?php
require_once "rb.php";
 
$db = "bookingsys";
$user = "root";
$pass = "admin1234";
$url = "mysql:host=localhost;dbname=$db";
 
R::setup( $url, $user, $pass );
R::freeze( true );
?>
