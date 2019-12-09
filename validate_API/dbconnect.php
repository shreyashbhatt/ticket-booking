<?php
	require_once('config.php');

	//Connect to mysql server
	$con = mysqli_connect(DB_HOST, DB_USER, DB_PASSWORD,DB_DATABASE);
	if(!$con) {
		die('Failed to connect to server: ' . mysqli_connect_error());
	}
    date_default_timezone_set("Asia/Kolkata");
    mysqli_query($con,"SET NAMES utf8");
?>
