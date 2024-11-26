<?php

require('db_conn.php');

$ssid = $_POST['ssid'] ?? null;
$rssi = $_POST['rssi'] ?? null;
$sec = $_POST['sec'] ?? null;
$mac = $_POST['mac'] ?? null;

if (!$ssid || !$rssi || !$sec || !$mac) {
    die("Invalid input data");
}

$stmt = $conn->prepare("SELECT * FROM ssid WHERE ssid = ?");
$stmt->bind_param("s", $ssid);
$stmt->execute();
$result = $stmt->get_result();

if ($result->num_rows == 0) {
    $stmt = $conn->prepare("INSERT INTO ssid (ssid) VALUES (?)");
    $stmt->bind_param("s", $ssid);
    if (!$stmt->execute()) {
        die("Error recording SSID: " . $stmt->error);
    }
    echo "New SSID recorded.";
}

$stmt = $conn->prepare("SELECT * FROM mac WHERE mac = ?");
$stmt->bind_param("s", $mac);
$stmt->execute();
$result = $stmt->get_result();

if ($result->num_rows == 0) {
    $stmt = $conn->prepare("INSERT INTO mac (mac) VALUES (?)");
    $stmt->bind_param("s", $mac);
    if (!$stmt->execute()) {
        die("Error recording MAC: " . $stmt->error);
    }
    echo "New MAC recorded.";
}

$stmt = $conn->prepare("INSERT INTO log (ssid, rssi, sec, mac) VALUES (?, ?, ?, ?)");
$stmt->bind_param("ssss", $ssid, $rssi, $sec, $mac);

if ($stmt->execute()) {
    echo "WRITE LOG OK";
} else {
    die("ERROR: " . $stmt->error);
}

$conn->close();

?>
