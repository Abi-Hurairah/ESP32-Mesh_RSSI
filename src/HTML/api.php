<?php

require('db_conn.php');

$ssid = $_POST['ssid'];
$rssi = $_POST['rssi'];
$sec = $_POST['sec'];
$mac = $_POST['mac'];

//check if ssid recorded
$sql_check_ssid = $conn->query("SELECT * FROM ssid WHERE ssid = '$ssid'");
echo " Checking if ssid already recorded...";
if($sql_check_ssid){
	if(mysqli_num_rows($sql_check_ssid) > 0){
		//damn
	} else {
		//add to record
		mysqli_query($conn, "INSERT INTO ssid (ssid) VALUES ('$ssid')");
		echo " No. New ssid recorded.";
	}
}

$sql_add_log = $conn->query("INSERT INTO log (SSID, RSSI, SEC, MAC) VALUES ('$ssid', '$rssi', '$sec', '$mac')");

if ($sql_add_log === TRUE){
	echo " WRITE LOG OK";
} else {
	echo " ERROR " . $conn->error;
}

$conn->close();

?>
