<?

function agent_info($ip=NULL) {
	global $baseserver;

  // Set the IP address if one hasn't been passed
	 if (empty($ip)) {
		$ip=$_SERVER['REMOTE_ADDR'];
	}

  // Connect to the server
	if ($baseserver=='192.168.1.209') {
		$db_con=mysqli_connect($baseserver, 'root', 'password') or die('Couldn\'t connect to the SQL server: ' . mysqli_error());
	}
	else {
		$db_con=mysqli_connect($baseserver, DB_USERNAME, DB_PASSWORD) or die('Couldn\'t connect to the SQL server: ' . mysqli_error());
	}

  // Select the database
	mysqli_select_db($db_con, 'system') or die('Couldn\'t select the SQL database: ' . mysql_error());

	// Build and run the query
	$sql="SELECT * FROM iplogins WHERE ipaddress='$ip' LIMIT 1";
	$data=mysqli_query($db_con, $sql) or die('Query failed: ' . mysql_error());

	//echo "<!--{$sql}-->";
	//echo "Rows:",mysql_numrows($data);
	if (mysqli_num_rows($data)>0) {
		$results=mysqli_fetch_assoc($data);
		//$agentinit=strtoupper(mysql_result($data,0,'agentinit'));
		//$agentinit=strtoupper($results{'agentinit'});
		$agentlogin=strtoupper($results{'agentinit'});
		$sql="SELECT * FROM agent WHERE agentlogin='$agentlogin' ORDER BY agentto DESC LIMIT 1";
		//$sql="SELECT * FROM agent WHERE agentlogin='$agentlogin' ORDER BY updated DESC LIMIT 1";
		//$sql="SELECT * FROM agent WHERE agentlogin='$agentinit' ORDER BY updated DESC LIMIT 1";
	//echo "Rows:",mysql_numrows($data);
	//echo "Agent:",$agentinit;
	//echo "SQL:",$sql;
	//echo var_dump($data);
	}
	else {
		$sql="SELECT * FROM agent WHERE agentip='$ip' ORDER BY updated DESC LIMIT 1";
	}
	$data=mysqli_query($db_con, $sql) or die('Query failed: ' . mysqli_error());

	// Retrieve the results
	$results=mysqli_fetch_assoc($data);

	// Clean up
	mysqli_free_result($data);
	//mysql_close($db_con);

	// Return the results
	return $results;
}

function agent_ingroup($grouptest) {
	global $banner_agent;

	//echo $banner_agent['agentgrp']."=".$grouptest."\n";
	//var_dump($banner_agent);
	return strpos($banner_agent['agentgrp'],$grouptest)!==FALSE;
}
?>
