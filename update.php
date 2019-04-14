<?php

// @TODO: set the correct status code, not 0
if( !chdir( dirname( __FILE__) ) )
	exit();

$args = $_SERVER['argv'];

// need to set the user in order to load correct settings
if( count( $args ) > 6 )
	$_SERVER['REMOTE_USER'] = $args[6];

require_once 'emailer.php';

$em = rEmailer::load();

// parse the details of the completed download
$details = array(
	'name' => trim($args[1]),
	'size' => trim($args[2]),
	'created' => trim($args[3]),
	'added' => trim($args[4]),
	'finished' => $_SERVER['REQUEST_TIME']
);

// send the email!
$result = $em->sendEmailOnCompletion($details, var_export($args, TRUE));

//echo "RESULT: ", $result;

// exit normally, not needed as scripts terminate with code 0 normally
exit(0);