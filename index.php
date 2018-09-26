<?php
require('includes/utility.php');
require('includes/user.php');
require('includes/db.php');

session_start();

$errMsg = '&nbsp;';
$user = null;

if (isset($_SESSION['user'])) {
	$user = $_SESSION['user'];
} else {
	header('location: login.php');
}

?>
<!doctype html>
<html lang="en">
<head>
	<meta charset="utf-8">
	<title>User Portal - Home</title>
	<link rel="stylesheet" href="css/styles.css">
</head>
<body>
	<a href="logout.php">Logout</a>
	<p class="fill">Welcome, <?= $user->getName(); ?>!</p>
	<form method="POST" action="api/file/uploadImage.php" enctype="multipart/form-data">
		<h1>Upload Image</h1>
		<h3 class="error"><?= $errMsg ?></h3>
		<input type="file" name="img[]" accept="image/*" class="fill" multiple required>
		<br>
		<input type="submit" value="Upload Selected Files" class="fill">
	</form>
</body>
</html>