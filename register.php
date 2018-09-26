<?php
require('includes/utility.php');
require('includes/user.php');
require('includes/db.php');

session_start();

if (isset($_SESSION['user'])) { // User already logged in -> go home
	header('location: index.php');
}

$errMsg = '&nbsp;';

try {
	if ($_SERVER['REQUEST_METHOD'] === 'POST') {
		if (!isNameSuitable($_POST['name'])) {
			throw new Exception('Invalid name');
			
		} elseif (!isEmailSuitable($_POST['email']) || $_POST['email'] !== $_POST['confirm-email']) {
			throw new Exception('Invalid email address');
			
		} elseif (!isPasswordSuitable($_POST['password']) || $_POST['password'] !== $_POST['confirm-password']) {
			throw new Exception('Invalid password');
			
		} elseif (queryEmailStatus($_POST['email']) !== 'NOT_USED') {
			throw new Exception('Invalid email address'); // No new info for potential hackers
		
		} elseif (createNewUser($_POST['name'], $_POST['email'], $_POST['password']) === 0) {
			throw new Exception('Failed to create new user account');
			
		} else { // Email server required to send email
			mail($_POST['email'], 'User Portal', 'Hi, thank you for registering to userportal.co.uk.');
			header('location:./login.php');
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
	<title>User Portal - Register</title>
	<link rel="stylesheet" href="css/styles.css">
	<script>
	
	function checkEmail(e) {
		
		var hiddenEmailStatusField = document.querySelector("input[name='email-status']");
		
		xhrCheckDBForEmail(e.value,
			(resolved) => hiddenEmailStatusField.value = resolved,
			(rejected) => hiddenEmailStatusField.value = rejected);
	}
	
	function validateForm(e) {
		
		var valid = true;
		var err = "";
		
		var errorElement = document.querySelector("h3.error");
		
		var values = {
			name: e.elements["name"].value,
			email: e.elements["email"].value,
			confirmEmail: e.elements["confirm-email"].value,
			password: e.elements["password"].value,
			confirmPassword: e.elements["confirm-password"].value,
			emailStatus: e.elements["email-status"].value
		};
		
		if (!checkNameSuitability(values.name)) {
			valid = false;
			err = "Invalid name";
		} else if (!checkEmailSuitability(values.email)) {
			valid = false;
			err = "Invalid email address";
		} else if (!checkPasswordSuitability(values.email)) {
			valid = false;
			err = "Invalid password";
		} else if (values.email !== values.confirmEmail) {
			valid = false;
			err = "Emails don't match";
		} else if (values.password !== values.confirmPassword) {
			valid = false;
			err = "Passwords don't match";
		} else if (typeof values.emailStatus !== 'undefined') {
			if (values.emailStatus === 'ALREADY_IN_USE') {
				valid = false;
				err = "Email is already in use";
			} else if (values.emailStatus === 'INVALID') {
				valid = false;
				err = "Email is invalid";
			}
		}
		
		if (!valid) {
			errorElement.innerText = err;
		}
		
		return valid;
	}
	
	/* Helper functions */
	 
	function checkNameSuitability(name) {
		return (typeof name === 'string') && name.length > 0 && name.length <= <?= MAX_NAME_LENGTH ?>;
	}
	
	function checkEmailSuitability(email) {
		return (typeof email === 'string') && email.length > 0 && email.length < <?= MAX_EMAIL_LENGTH ?>
			&& email.includes("@");
	}
	
	function checkPasswordSuitability(password) {
		return (typeof password === 'string') && password.length > 0
			&& password.length < <?= MAX_PASSWORD_LENGTH ?>; // can extend to include password strength
	}
	
	function xhrCheckDBForEmail(email, resolve, reject) {
		var xhr = new XMLHttpRequest();
		xhr.onload = function(e) {
			resolve(this.responseText);
		};
		xhr.onerror = function(e) {
			reject(this.statusText);
		};
		xhr.open("GET", `api/user/checkEmail.php?email=${email}`);
		xhr.send();
	}
	
	</script>
</head>
<body>
	<form method="post" autocomplete="off" onsubmit="return validateForm(this);">
		<h1>Register</h1>
		<h3 class="error"><?= $errMsg ?></h3>
		<label class="fill">Name<input name="name" type="text" class="fill" maxlength="<?= MAX_NAME_LENGTH ?>" required></label>
		<label class="fill">Email<input name="email" type="email" class="fill" maxlength="<?= MAX_EMAIL_LENGTH ?>" onblur="checkEmail(this)" required></label>
		<label class="fill">Confirm email<input name="confirm-email" type="email" maxlength="<?= MAX_EMAIL_LENGTH ?>" class="fill" required></label>
		<label class="fill">Password<input name="password" type="password" maxlength="<?= MAX_PASSWORD_LENGTH ?>" class="fill" required></label>
		<label class="fill">Confirm password<input name="confirm-password" type="password" maxlength="<?= MAX_PASSWORD_LENGTH ?>" class="fill" required></label>
		<input name="email-status" type="hidden">
		<input type="submit" value="Register" class="fill">
		<a href="./login.php">Click here to login</a>
	</form>
</body>
</html>