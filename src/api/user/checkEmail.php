<?php
define('__ROOT__', dirname(__FILE__, 3)); // userportal.co.uk
define('MAX_EMAIL_LENGTH', 128);

$msgOutput = 'INVALID';

if ($_SERVER['REQUEST_METHOD'] === 'GET'
	&& isset($_GET['email'])
	&& strlen($_GET['email']) <= MAX_EMAIL_LENGTH
	&& filter_var($_GET['email'], FILTER_VALIDATE_EMAIL)) {
	
	require_once(__ROOT__.'\includes\db.php');
	
	$msgOutput = queryEmailStatus($_GET['email']);
}

echo $msgOutput;
?>