<?php

define('MAX_NAME_LENGTH', 128);
define('MAX_EMAIL_LENGTH', 128);
define('MAX_PASSWORD_LENGTH', 256);

function isNameSuitable(&$name) {
	return isset($name) && strlen($name) <= MAX_NAME_LENGTH;
}

function isEmailSuitable(&$email) {
	return isset($email) && strlen($email) <= MAX_EMAIL_LENGTH
		&& filter_var($email, FILTER_VALIDATE_EMAIL);
}

function isPasswordSuitable(&$password) {
	return isset($password) && strlen($password) <= MAX_PASSWORD_LENGTH;
}

function generateUniqueIDSha256Hash($value) {
	return hash('sha256', uniqid($value, true));
}

?>