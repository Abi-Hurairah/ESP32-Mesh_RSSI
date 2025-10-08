<?php
	$host = "localhost";
	$user = "user"; //user of database
	$pass = "pass"; //password of the database user
	$dbnm = "rssi_readings"; //database name

	$conn = mysqli_connect($host, $user, $pass, $dbnm);

	if ($conn->connect_error) {
		die("Connection failed: " . $conn -> connect_error);
	}
?>
