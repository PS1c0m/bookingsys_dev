<?php
require_once "include/Session.php";
$session = new Session();

require_once "include/db.php";
$table = "user";

$params = (object) $_REQUEST;

$username = trim($params->username);
$password = $params->password;

$user = R::findOne($table, "username = ?", array($username) );
if ($user->username === $username){ //Casesenitivity check
	if (!isset($user)) {                              // no such user
	  $session->username = $params->username;
	  $session->message = "Failed user";
	} elseif ($user->password === sha1($password)) {  // password match 
	  $session->user->username = $username;
	  $session->user->usertype = $user->usertype;
	} else {                                          // wrong password
	  $session->username = $params->username;
	  $session->message = "Failed password";
	}
}
header( "location: index.php" );   // reload