<?php
ini_set('display_errors', 'Off');
define('DEBUG', false);
error_reporting(0);
$conn = mysqli_connect("localhost", "", "", "") or die(mysqli_error($conn));


?>
