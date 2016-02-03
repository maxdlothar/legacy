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

echo <<<STARTDOC
<html>
  <head>
    <script type="text/javascript" src="https://www.gstatic.com/charts/loader.js"></script>
    <script type="text/javascript">

		// Load the Visualization API and the piechart package
      google.charts.load("current", {packages:["corechart"]});

		// Set a callback to run when API is loaded
      google.charts.setOnLoadCallback(drawChart);

      function drawChart() {

		// Chart 1
        var data1 = google.visualization.arrayToDataTable([
          ['Rate', 'Information'],
STARTDOC;

// Parse the data
while($row = $result->fetch_assoc()) {
	echo "['".$row['calldate']."',".$row['countvar']."],";
}

$result->free();

echo <<<ENDDOC
        ]);
        var options1 = {
          title: 'March-August 2013',
          is3D: true,
        };
        var chart1 = new google.visualization.PieChart(document.getElementById('chart_div1'));
        chart1.draw(data1, options1);
      }
	       
    </script>
  </head>
  <body>
   <div id="chart_div1" style="width: 900px; height: 500px;"></div>
  </body>
</html>
ENDDOC;

	// End stuff
	$db_con->close();
?>
