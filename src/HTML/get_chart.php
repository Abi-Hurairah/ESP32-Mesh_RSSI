<?php


require('db_conn.php');


// Get SSID and MAC from GET parameters

$selected_ssid = isset($_GET['ssid']) ? strtolower(trim($_GET['ssid'])) : 'eduroam';

$selected_mac = isset($_GET['mac']) ? strtolower(trim($_GET['mac'])) : 'ALL';


try {

    // Prepare the base query

    $query = "SELECT DATE(date) as date, MAX(rssi) as rssi FROM log WHERE 1=1"; // Group by date and get max RSSI


    // Add conditions based on SSID and MAC

    if ($selected_ssid !== 'ALL') {

        $query .= " AND ssid = ?";

    }

    if ($selected_mac !== 'ALL') {

        $query .= " AND mac = ?";

    }


    // Group by date

    $query .= " GROUP BY DATE(date) ORDER BY DATE(date)"; // Group by date and order results


    // Prepare the statement

    $stmt = $conn->prepare($query);


    // Bind parameters dynamically based on the input

    if ($selected_ssid !== 'ALL' && $selected_mac !== 'ALL') {

        $stmt->bind_param("ss", $selected_ssid, $selected_mac);

    } elseif ($selected_ssid !== 'ALL') {

        $stmt->bind_param("s", $selected_ssid);

    } elseif ($selected_mac !== 'ALL') {

        $stmt->bind_param("s", $selected_mac);

    }


    // Execute the statement

    $stmt->execute();

    $result = $stmt->get_result();


    // Fetch all results

    $rows = $result->fetch_all(MYSQLI_ASSOC);


    // Return the results as JSON

    echo json_encode($rows);


} catch (Exception $e) {

    http_response_code(500);

    echo json_encode(["error" => $e->getMessage()]);

} finally {

    $conn->close();

}

?>