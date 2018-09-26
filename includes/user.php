<?php
class User {
	
	var $id;
	var $name;
	var $email;
	
	function __construct($id, $name, $email) {
		$this->id = $id;
		$this->name = $name;
		$this->email = $email;
	}
	
	/* Getters */
	
	function getID() {
		return $this->id;
	}
	
	function getName() {
		return $this->name;
	}
	
	function getEmail() {
		return $this->email;
	}
	
	/* DB helpers */
	
	private function update($key, $value) { // Private because $key is not escaped
		global $conn;
		$sql = "UPDATE `user` SET $key = ? WHERE `user`.`id` = ?;";
		$stmt = null;
		if ($stmt = $conn->prepare($sql)) {
			$stmt->bind_param('si', $value, $this->id);
			$stmt->execute();
			$stmt->close();
		}
	}
	
	function updateName($name) {
		$this->update('name', $name);
	}
	
	function updateEmail($email) {
		$this->update('email', $email);
	}
	
	function updatePassword($password) {
		$this->update('password', password_hash($password, PASSWORD_DEFAULT));
	}
	
	/* Static methods */
	
	public static function findUsingLoginCredentials($email, $password) {
		global $conn;
		$user = null;
		$userId = 0;
		$userName = '';
		$passwordHash = '';
		$sql = 'SELECT `id`, `name`, `password` FROM `user` WHERE `email` = ? LIMIT 1;';
		if ($stmt = $conn->prepare($sql)) {
			$stmt->bind_param('s', $email);
			$stmt->bind_result($userId, $userName, $passwordHash);
			if ($stmt->execute()) {
				if ($stmt->fetch() && $userId > 0) {
					if (password_verify($password, $passwordHash)) {
						$user = new User($userId, $userName, $email);
					}
				}
			}
			$stmt->close();
		}
		return $user;
	}
	
}
?>