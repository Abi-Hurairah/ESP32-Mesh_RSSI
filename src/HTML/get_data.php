<?php

    echo "<table>";
    echo "<thead><tr><b><th>" . "ID" . "</th><th>" . "SSID" . "</th><th>" . "RSSI" . "</th><th>" . "SEC" . "</th><th>" . "MAC" . "</th><th>" . "DATE" . "</th></b></tr></thead>";

    require('db_conn.php');

    $selected_ssid = isset($_GET['ssid']) ? $_GET['ssid'] : 'ALL';
    $selected_mac = isset($_GET['mac']) ? $_GET['mac'] : 'ALL';

    if($selected_mac === 'ALL'){
        if($selected_ssid === 'ALL') {
            $query = "SELECT * FROM log ORDER BY ID DESC LIMIT 150";
        } else {
            $query = "SELECT * FROM log WHERE ssid='" . mysqli_real_escape_string($conn, $selected_ssid) . "' ORDER BY ID DESC LIMIT 150";
        }
    } else {
        if($selected_ssid === 'ALL') {
            $query = "SELECT * FROM log WHERE mac='" . mysqli_real_escape_string($conn, $selected_mac) . "' ORDER BY ID DESC LIMIT 150";
        } else {
            $query = "SELECT * FROM log WHERE ssid='" . mysqli_real_escape_string($conn, $selected_ssid) . "' AND mac='" . mysqli_real_escape_string($conn, $selected_mac) . "' ORDER BY ID DESC LIMIT 150";
        }
    }
    
    $result = mysqli_query($conn, $query);
    
    if (!$result) {
        echo "Error: " . mysqli_error($conn);
        exit;
    }

    echo "<tbody>";
    while($row = mysqli_fetch_assoc($result)) {
        echo    "<tr><td>" . $row['id'] . 
                "</td><td>" . $row['ssid'] . 
                "</td><td>" . $row['rssi'] .
                "</td><td>" . $row['sec'] .
                "</td><td>" . $row['mac'] .
                "</td><td>" . $row['date'] . "</td></tr>";
    }
    
    echo "</tbody></table><br>";
    
    mysqli_close($conn);
?>
