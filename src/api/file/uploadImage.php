<?php
define('__ROOT__', dirname(__FILE__, 3)); // userportal.co.uk
define("__IMAGE_FOLDER__", __ROOT__.'/upload/images/');
define("__IMAGE_EXTENSIONS__", array('png', 'jpg', 'jpeg', 'bmp', 'gif'));
define('GIGABYTE', 1073741824);

session_start();

require(__ROOT__.'/includes/utility.php');
require(__ROOT__.'/includes/user.php');
require(__ROOT__.'/includes/db.php');

$msgOutput = '';
$fileCount = count($_FILES['img']['name']);
$file = null;

if (!isset($_SESSION['user'])) { // not logged in
	die();
} elseif ($_SERVER['REQUEST_METHOD'] !== 'POST') { // not posting
	die();
} elseif ($fileCount === 0) { // nothing to upload
	die();
}

/* Image uploading starts here */

for ($i = 0; $i < $fileCount; $i++) {
	
	$file = getFile($i);

	$msgOutput .= uploadFile($file);
	$msgOutput .= '<br>';
}

$msgOutput .= '<br>Redirecting in 5 seconds.';

echo $msgOutput;

header('Refresh: 5; URL='.$_SERVER['HTTP_REFERER']); // Go back in 5 seconds

/* Helper functions */

function uploadFile(&$file) {
	$status = '';
	
	if (isFileBroken($file)) {
		$status .= 'File is broken: '.$file['name'];
		
	} elseif(isFileNotAnImage($file)) {
		$status .= 'File is not an image: '.$file['name'];
		
	} elseif(isFileTooLarge($file)) {
		$status .= 'File is too large: '.$file['name'];
		
	} elseif(isFilenameTaken($file)) {
		$status .= 'File already exists: '.$file['name'];
	
	} elseif(moveTempFileToUploadFolder($file)) {
		$status .= 'Successfully uploaded: '.$file['name'];
	}
	
	return $status;
}

function getFile($index) {
	return array(
		     'name' => $_FILES['img']['name'][$index],
		     'type' => $_FILES['img']['type'][$index],
		 'tmp_name' => $_FILES['img']['tmp_name'][$index],
		    'error' => $_FILES['img']['error'][$index],
		     'size' => $_FILES['img']['size'][$index],
		'extension' => strtolower(pathinfo($_FILES['img']['name'][$index], PATHINFO_EXTENSION))
	);
}

function isFileBroken(&$file) {
	return $file['error'] !== 0;
}

function isFileNotAnImage(&$file) {
	return !in_array($file['extension'], __IMAGE_EXTENSIONS__);
}

function isFileTooLarge(&$file) {
	return $file['size'] > GIGABYTE;
}

function isFilenameTaken(&$file) {
	return file_exists(__IMAGE_FOLDER__.$file['name']);
}

function moveTempFileToUploadFolder(&$file) {
	return move_uploaded_file($file['tmp_name'], __IMAGE_FOLDER__.$file['name']);
}

?>