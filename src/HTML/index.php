<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Nijika: Monitoring System</title>

    <script type="text/javascript" src="https://www.gstatic.com/charts/loader.js"></script>
    <script src="https://ajax.googleapis.com/ajax/libs/jquery/3.2.1/jquery.min.js"></script>

    <script type="text/javascript">
        google.charts.load('current', {'packages':['corechart']});
        google.charts.setOnLoadCallback(initializeChart);
        
        var chart;
        var options = {
            title: 'EDUROAM WIFI QUALITY CHART',
            width: '100%',
            height: 400,
            hAxis: { title: 'TIME', format: 'MMM dd, YYYY HH:mm' },
            vAxis: { title: 'RSSI' },
            legend: { position: 'bottom' },
            curveType: 'function',
            colors: ['#cc351b'], // Line color
            backgroundColor: { fill: 'transparent' }
        };

        function initializeChart() {
            chart = new google.visualization.LineChart(document.getElementById('chart'));
            options.width = document.getElementById('chart').offsetWidth;
            options.height = document.getElementById('chart').offsetHeight;

            refreshChart(); 
            setInterval(refreshChart, 1000);
        }

        function refreshChart() {
            var selectedSSID = $("#ssidSelect").val();
            var selectedMAC = $("#macSelect").val();

            options.title = selectedSSID + ' WIFI QUALITY CHART';

            if (selectedMAC === "ALL") {
                $('#chart').hide();
                $('#noChartMessage').show();
            } else {
                $('#noChartMessage').hide();
                $('#chart').show();

                $.getJSON('get_chart.php?ssid=' + selectedSSID + '&mac=' + selectedMAC, function(data){
                    let result = [['date', 'rssi']];
                    data.forEach(row => {
                        result.push([new Date(row['date']), parseInt(row['rssi'])]);
                    });

                    var dataTable = google.visualization.arrayToDataTable(result);
                    chart.draw(dataTable, options);
                });
            }
        }

        function refreshTable(){
            var selectedSSID = $("#ssidSelect").val();
            var selectedMAC = $("#macSelect").val();
            var encodedSSID = encodeURIComponent(selectedSSID);

            $('#tableID').load('get_data.php?ssid=' + encodedSSID + '&mac=' + selectedMAC);
        }

        $(document).ready(function(){
            refreshTable(); 

            $('#macSelect').change(function() {
                refreshTable();
                refreshChart();
            });

            setInterval(refreshTable, 1000);
            // $('#macSelect').change(refreshChart);
        });
    </script>

    <style>
        body {
            font-family: 'Arial', sans-serif;
            background-color: #f4f4f4;
            margin: 0;
            padding: 0;
        }

        .header {
            background-color: #fff98c;
            color: #212121;
            padding: 20px;
            display: flex;
            justify-content: space-between;
            align-items: center;
        }

        h1 {
            margin: 0;
            font-size: 2.5em;
        }

        .controls {
            display: flex;
            gap: 20px;
        }

        label {
            font-weight: bold;
            margin-right: 10px;
            color: #212121;
        }

        select {
            padding: 10px;
            border-radius: 5px;
            border: 1px solid #ccc;
            font-size: 1em;
        }

        #chart {
            width: 100%;
            height: 400px;
            border-radius: 8px;
            box-shadow: 0 2px 10px rgba(0, 0, 0, 0.1);
            background-color: white;
        }

        #noChartMessage {
            width: 100%;
            height: 400px;
            display: none;
            text-align: center;
            padding: 20px;
            font-size: 1.2em;
            color: #555;
        }

        table {
            border: 2px solid #ddd;
            background-color: #fff;
            width: 100%;
            border-collapse: collapse;
            margin-top: 20px;
            border-radius: 8px;
            overflow: hidden;
        }

        th, td {
            padding: 15px;
            text-align: left;
            border-bottom: 1px solid #ddd;
        }

        th {
            background-color: #fff98c ;
            color: #212121;
            position: sticky;
            top: 0;
            z-index: 10;
        }

        tr:nth-child(even) {
            background-color: #f9f9f9;
        }

        tr:hover {
            background-color: #f1f1f1;
        }

        @media (max-width: 600px) {
            .controls {
                flex-direction: column;
                align-items: center;
            }

            select {
                width: 100%;
                margin-bottom: 10px;
            }
        }
    </style>
</head>

<body>
    <div class="header">
        <h1>njk_monsys</h1>

        <div class="controls">
        <div>
            <label for="ssidSelect">CHOOSE SSID:</label>
            <select id="ssidSelect" name="ssidSelect">
                <?php
                    require('db_conn.php');
                    $result = mysqli_query($conn, "SELECT * FROM ssid ORDER BY ssid");
                    while($row = mysqli_fetch_assoc($result)) {
                        echo "<option value=\"" . $row['ssid'] . "\">" . $row['ssid'] . "</option>";
                    }
                    $conn->close();
                ?>
                <option value="ALL">ALL</option>
            </select>
        </div>

        <div>
            <label for="macSelect">CHOOSE MAC:</label>
            <select id="macSelect">
                <?php
                    require('db_conn.php');
                    $result = mysqli_query($conn, "SELECT * FROM mac ORDER BY mac");
                    while($row = mysqli_fetch_assoc($result)) {
                        echo "<option value=\"" . $row['mac'] . "\">" . $row['mac'] . "</option>";
                    }
                    $conn->close();
                ?>
                <option value="ALL">ALL</option>
            </select>
        </div>
    </div>
    </div>

    <div id="noChartMessage">
        <p>Please select a specific MAC address to view the chart.</p>
    </div>

    <div id="chart"></div>

    <div style="height: 50vh; overflow: auto;">
        <div id="tableID"></div>
    </div>
</body>
</html>