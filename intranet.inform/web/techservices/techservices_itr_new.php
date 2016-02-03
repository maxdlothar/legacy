<?
//require_once "./techshare.php";
require_once "../includes/connect_idb.php";
require_once "../includes/agent_inform.php";
require_once "../includes/agent_banner.php";

$agentdet=agent_inform();
$agentinit=$agentdet['agentinit'];

//mysql_connect(localhost,$username,$password);
//@mysql_select_db($database) or die( "Unable to select database");

//echo "Hello\n";

if (isset($_POST{'action'}) and ($_POST{'action'}=='Submit')) {
	$itr_description=$_POST['itr_description'];
	if (!empty($itr_description)) {
		$query = "insert into itr_requests set itr_description='".mysqli_real_escape_string($db_con, $itr_description)."', itr_raised=now(), itr_raisedby='{$agentinit}';";
		mysqli_query($db_con, $query);
		//echo "Submit-{$query}\n";

		$itr_record=mysqli_insert_id($db_con);
		$itr_id=substr($itr_record,-3);
		$query = "update itr_requests set itr_id='{$itr_id}' where itr_record='{$itr_record}' limit 1;";
		mysqli_query($db_con, $query);
		echo "<table border=1 cellspacing=1 bgcolor=white><tr><td><pre>";
		echo "----------------------------------------\n";
		echo " IT REQUEST FORM\n";
		echo "----------------------------------------\n";
		echo "\n";
 		echo "IT Completed: \n";
		echo "\n";
 		echo "CS Tested: \n";
		echo "\n";
 		echo "Client Informed: \n";
		echo "\n";
		echo "----------------------------------------\n";
 		echo "Client: \n";
		echo "\n";
 		echo "Client Contact: \n";
		echo "\n";
		echo "ITR Number: ITR-{$itr_id}\n";
		echo "\n";
 		echo "Associated documentation: \n";
		echo "\n";
 		echo "Scheduled completion date: \n";
		echo "\n";
		echo "----------------------------------------\n";
		echo "</pre></td></tr></table>\n";
		//echo "Submit-{$query}\n";
	}
}

echo '<form action="" method=post>';
echo '<br />';
echo '<br />Enter Basic Description of Job (Title)';
echo '<br /><INPUT type=text name="itr_description" value="" size=120 /></td>';
echo '<br />';
echo '<input type=submit name="action" value="Submit" />';

echo '</form>';

mysqli_close($db_con);
?>
