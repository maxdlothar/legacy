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
<script type="text/javascript" src="https://www.gstatic.com/charts/loader.js"></script>
  <script type="text/javascript">
    google.charts.load("current", {packages:['corechart']});
    google.charts.setOnLoadCallback(drawChart);
    function drawChart() {
      var data = google.visualization.arrayToDataTable([
        ["Element", "Density", { role: "style" } ],
        ["Copper", 8.94, "#b87333"],
        ["Silver", 10.49, "silver"],
        ["Gold", 19.30, "gold"],
        ["Platinum", 21.45, "color: #e5e4e2"]
      ]);

      var view = new google.visualization.DataView(data);
      view.setColumns([0, 1,
                       { calc: "stringify",
                         sourceColumn: 1,
                         type: "string",
                         role: "annotation" },
                       2]);

      var options = {
        title: "Density of Precious Metals, in g/cm^3",
        width: 600,
        height: 400,
        bar: {groupWidth: "95%"},
        legend: { position: "none" },
      };
      var chart = new google.visualization.ColumnChart(document.getElementById("columnchart_values"));
      chart.draw(view, options);
  }
  </script>
<div id="columnchart_values" style="width: 900px; height: 300px;"></div>
STARTDOC;

// Parse the data
while($row = $result->fetch_assoc()) {
	echo "['".$row['calldate']."',".$row['countvar']."],";
}

$result->free();

//echo <<<ENDDOC
//ENDDOC;

	// End stuff
	$db_con->close();
?>
