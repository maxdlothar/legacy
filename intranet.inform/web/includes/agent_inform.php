<?

function agent_inform($ip=NULL) {
	global $baseserver;
	global $db_con;

  // Set the IP address if one hasn't been passed
  if (empty($ip)) {
    $ip=$_SERVER['REMOTE_ADDR'];
  }

  // Connect to the server
  //$db_con=mysql_connect($baseserver, 'root', 'password') or die('Couldn\'t connect to the SQL server: ' . mysql_error());

  // Select the database
  //mysql_select_db('system') or die('Couldn\'t select the SQL database: ' . mysql_error());

  // Build and run the query
  $sql="SELECT * FROM system.iplogins WHERE ipaddress='$ip' LIMIT 1";
  $data=mysqli_query($db_con, $sql) or die('Query failed: ' . mysqli_error($db_con));

	if (mysqli_num_rows($data)>0) {
		$results=mysqli_fetch_assoc($data);
		$agentlogin=strtoupper($results{'agentinit'});
		$sql="SELECT * FROM agent WHERE agentlogin='$agentlogin' ORDER BY agentto DESC LIMIT 1";
	}
	else {
		$sql="SELECT * FROM agent WHERE agentip='$ip' ORDER BY updated DESC LIMIT 1";
	}
	$data=mysqli_query($db_con, $sql) or die('Query failed: ' . mysqli_error($db_con));

  // Retrieve the results
  $results=mysqli_fetch_assoc($data);

  // Clean up
  mysqli_free_result($data);
//  mysql_close($db_con);

  // Return the results
  return ($results);

}

function agent_ingroup($grouptest) {
	global $banner_agent;

	//echo $banner_agent['agentgrp']."=".$grouptest."\n";
	//var_dump($banner_agent);
	return strpos($banner_agent['agentgrp'],$grouptest)!==FALSE;
}
?>
