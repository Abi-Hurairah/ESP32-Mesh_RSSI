<?php
	$host = "localhost";
	$user = "pp";
	$pass = "change";
	$dbnm = "pp";

	$conn = mysqli_connect($host, $user, $pass, $dbnm);

	if ($conn->connect_error) {
		die("Connection failed: " . $conn -> connect_error);
	}
?>
