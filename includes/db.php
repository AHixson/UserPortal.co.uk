<?php

require_once('cfg.php');

$conn = new mysqli($cfg['servername'], $cfg['username'], $cfg['password'], $cfg['dbname']);

if ($conn->connect_error) {
	die('Connection failed: ' . $conn->connect_error);
}

/*
 * Helper functions
 */

function queryEmailStatus($email) {
	global $conn;
	$status = 'INVALID';
	$sql = 'SELECT `id` FROM `user` WHERE UPPER(`email`) = ? LIMIT 1;';
	$userId = 0;
	$email = strtoupper($email);
	if ($stmt = $conn->prepare($sql)) {
		$stmt->bind_param('s', $email);
		$stmt->bind_result($userId);
		if ($stmt->execute()) {
			if ($stmt->fetch() && $userId > 0) {
				$status = 'ALREADY_IN_USE';
			} else {
				$status = 'NOT_USED';
			}
		}
		$stmt->close();
	}
	return $status;
}

function createNewUser($name, $email, $password) {
	global $conn;
	$userId = 0;
	$sql = 'INSERT INTO `user` (`id`, `name`, `email`, `password`) VALUES (NULL, ?, ?, ?);';
	if ($stmt = $conn->prepare($sql)) {
		$password = password_hash($password, PASSWORD_DEFAULT);
		$stmt->bind_param('sss', $name, $email, $password);
		if ($stmt->execute()) {
			$userId = $stmt->insert_id;
		}
		$stmt->close();
	}
	return $userId;
}

?>