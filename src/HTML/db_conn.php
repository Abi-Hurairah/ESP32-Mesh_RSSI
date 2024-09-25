<?php
$servername = "xxxxxx";
$username = "xxxxxx";
$password = "xxxxxx";
$dbname = "xxxxxx";

$conn = new mysqli($servername, $username, $password, $dbname);

if ($conn->connect_error) {
    die("CONNECTION FAILED " . $conn->connect_error);
}
?>

