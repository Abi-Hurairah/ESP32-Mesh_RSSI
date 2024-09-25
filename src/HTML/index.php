<!DOCTYPE html>
<html lang="en">
<head>
	<meta charset="utf-8">
	<meta name="viewport" content="width=device-width, initial-scale=1">
	
	<title>Nijika: Monitoring System</title>

	<?php 
		require('db_conn.php');

		$result = mysqli_query($conn, "SELECT RSSI,DATE FROM log WHERE SSID='eduroam'");
		$rows = mysqli_fetch_all($result, MYSQLI_ASSOC);
	?>

	<script type="text/javascript" src="https://www.gstatic.com/charts/loader.js"></script>
	<div class="container" id="output"></div>
	<script src="https://ajax.googleapis.com/ajax/libs/jquery/3.2.1/jquery.min.js"></script>
	
	<script type="text/javascript">

		google.charts.load('current', {'packages':['corechart']});
		google.charts.setOnLoadCallback(drawChart);			

		const data = <?php echo json_encode($rows); ?>;
		let result = [['DATE', 'RSSI']];
	
		data.forEach(row => {
			result.push([new Date(row['DATE']), parseInt(row['RSSI'])]);
		});
		console.log(result);

		function drawChart() {
			var data = google.visualization.arrayToDataTable(result);
			var chartDiv = document.getElementById('chart');	

			var options = {
				title: 'EDUROAM WIFI QUALITY CHART',
				width: chartDiv.offsetWidth,
				height: chartDiv.offsetHeight,
				hAxis: { title: 'TIME', format: 'MMM dd, YYYY HH:mm'	},
				vAxis: { title: 'RSSI' },
				legend: { position: 'bottom' }
			};

			var chart = new google.visualization.LineChart(chartDiv);

			chart.draw(data, options);
		}

		window.onload = function() {
			let tableDiv = document.getElementById('table');
			
			let thead = table.createTHead();
			let row = thead.insertRow();
			let headers = ['RSSI', 'DATE'];
			headers.forEach(headerText => {
				let th = document.createElement('th');
				th.appendChild(document.createTextNode(headerText));
				row.appendChild(th);
			});

			let tbody = table.createTBody();
			data.forEach(item => {
				let row = tbody.insertRow();

				let cellRSSI = row.insertCell();
				let cellData = row.insertCell();

				cellRSSI.appendChild(document.createTextNode(item['RSSI']));
				cellData.appendChild(document.createTextNode(item['DATE']));
			});
		};
	</script>

	<style>
		#chart {
			width: 100%;
			height: 40vh;
		}
		
		div {
			margin: auto;
		}
		
		table{
			border: 2px solid white;
			width: 100%;
			height: 50vh;
			border-collapse: collapse;
		}

		th,td {
			padding: 10px;
			border: 2px solid white;
			text-align: left;
			padding: 8px;
		}	

		tr:nth-child(even) {
			background-color: #f2f2f2;
		}

		tr:hover {
			background-color: #555555;
			color: #f2f2f2;
		}

		div.header {
			width: 100%;
			height: 10vh;
			text-align: center;
		}
	</style>
</head>

<body>
	<div class="header">
		<h1>Nijika_MonSys</h1>
		<p>Wifi Monitoring System</p>
	</div>
	<div id="chart"></div>
	<div style="overflow: scroll; width: 100%; height: 50vh;">
		<table id="table"></table>
	</div>
</body>
</html>