 <?php
	// Connect to the database
	// include '../includes/connect_209.php';
	// 209 has loader07
	define('DB_HOSTNAME', '192.168.1.209');
	define('DB_USERNAME', 'root');
	define('DB_PASSWORD', 'password');
	define('DB_DATABASE', 'loader07');
	define('DB_LOGFILE', 'error_sql.log');

	$db_con=@mysqli_connect(DB_HOSTNAME, DB_USERNAME, DB_PASSWORD);

	// If the connection failed..
	if ($db_con==FALSE) {
		db_error('Sorry, couldn\'t connect to the SQL server.');
		exit(0);
	} else {
		mysqli_select_db($db_con, DB_DATABASE);
	}

	// Variables for SQL query
	$startdate="20160201";

	// SQL query
	$sql="SELECT count(*) AS countvar,DATE_FORMAT(calltime, '%Y%m%d') AS calldate FROM `records` WHERE calltime >= {$startdate} and serviceid='prerb' group by calldate";

	if(!$result=$db_con->query($sql)) {
		die("Error running query [".$db_con->error."]");
	}

	echo "<html>";
	echo "  <head>";
	echo "    <script type='text/javascript' src='https://www.gstatic.com/charts/loader.js'></script>";
	echo "    <script type='text/javascript'>";

	// Load the Visualization API and the piechart package
	echo "      google.charts.load('current', {packages:['corechart']});";

	// Set a callback to run when API is loaded
	echo "      google.charts.setOnLoadCallback(drawChart);";

	echo "      function drawChart() {";

	// Chart 1
	echo "      var data1 = google.visualization.arrayToDataTable([";
	echo "          ['Rate', 'Information'],";
	while($row = $result->fetch_assoc()) {
		echo "['".$row['calldate']."',".$row['countvar']."],";
	}
	$result->free();
	echo "        ]);";
	echo "        var options1 = {";
	echo "          title: 'March-August 2013',";
	echo "          is3D: true,";
	echo "        };";
	echo "        var chart1 = new google.visualization.PieChart(document.getElementById('chart_div1'));";
	echo "        chart1.draw(data1, options1);";
	echo "      }";
			    
	echo "    </script>";
	echo "  </head>";
	echo "  <body>";
	echo "   <div id='chart_div1' style='width: 900px; height: 500px;'></div>";
	echo "  </body>";
	echo "</html>";

	// End stuff
	$db_con->close();
?>
