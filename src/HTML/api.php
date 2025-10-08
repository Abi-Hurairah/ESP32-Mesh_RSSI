<?php
error_reporting(E_ALL);
ini_set('display_errors', 1);

include "db_conn.php";  // your connection script

// Only accept POST
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $ssid = $_POST['ssid'] ?? '';
    $rssi = $_POST['rssi'] ?? '';
    $sec  = $_POST['sec'] ?? '';
    $mac  = $_POST['mac'] ?? '';

    // Validate essential fields
    if ($ssid && $rssi && $mac) {
        // Prepare query (use prepared statements for safety)
        $stmt = mysqli_prepare($conn, 
            "INSERT INTO rssi_readings (ssid, rssi, sec, mac) VALUES (?, ?, ?, ?)"
        );
        mysqli_stmt_bind_param($stmt, "siss", $ssid, $rssi, $sec, $mac);

        if (mysqli_stmt_execute($stmt)) {
            echo "OK"; 
        } else {
            echo "ERROR: " . mysqli_error($conn);
        }

        mysqli_stmt_close($stmt);
    } else {
        echo "ERROR: Missing required fields";
    }
} else {
    echo "ERROR: Invalid request method";
}
?>
