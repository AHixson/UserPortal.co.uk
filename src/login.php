<?php
require('includes/utility.php');
require('includes/user.php');
require('includes/db.php');

session_start();

$errMsg = '&nbsp;';

try {
	if (isset($_SESSION['user'])) { // User already logged in -> go home

		header('location: index.php');

	} elseif ($_SERVER['REQUEST_METHOD'] === 'POST') {

		if (!isEmailSuitable($_POST['email'])) {
			throw new Exception('Invalid email address');
		} elseif (!isPasswordSuitable($_POST['password'])) {
			throw new Exception('Invalid password');
		}
		
		$_SESSION['user'] = User::findUsingLoginCredentials($_POST['email'], $_POST['password']);
		
		if (isset($_SESSION['user'])) {
			header('location: index.php');
		} else {
			throw new Exception('Email and/or password is invalid');
		}
	}
	
} catch (Exception $e) {
	$errMsg = $e->getMessage();
}
?>
<!doctype html>
<html lang="en">
<head>
	<meta charset="utf-8">
	<title>User Portal - Login</title>
	<link rel="stylesheet" href="css/styles.css">
</head>
<body>
	<form method="post">
		<h1>Login</h1>
		<h3 class="error"><?= $errMsg ?></h3>
		<label class="fill">Email<input name="email" type="email" maxlength="<?= MAX_EMAIL_LENGTH ?>" class="fill" required></label>
		<label class="fill">Password<input name="password" type="password" maxlength="<?= MAX_PASSWORD_LENGTH ?>" class="fill" required></label>
		<input type="submit" value="Login" class="fill">
		<a href="./register.php">Click here to register</a>
	</form>
</body>
</html>