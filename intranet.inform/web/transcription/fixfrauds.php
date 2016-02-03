<?
include('../includes/header.php');
$exporturi=(isset($_REQUEST['exporturi']))?$_REQUEST['exporturi']:false;
$exportto=(isset($_REQUEST['exportto']))?$_REQUEST['exportto']:false;
echo "<head><title>Export Contents</title></head>\n";
echo "<BODY>\n";

//var_dump($banner_agent);

	$midnight=date('Y-m-d', time()-(date('N')=='1'?259200:86400));
	$username="root";
	$password="password";

	$database="loader07";
	mysql_connect("192.168.1.209",$username,$password);
	@mysql_select_db($database) or die( "Unable to select database");

	if ($exporturi>'' and $exportto>'') {
		$query="UPDATE records SET exportset='{$exportto}' WHERE recorduri='{$exporturi}' AND exportset='main' LIMIT 1";
//echo $query."<br>";
		mysql_query($query);
		//$num=mysql_numrows($result);
		//	echo "Updated!<br>";
		//}
	}

	$query="SELECT * FROM records LEFT JOIN additional ON records.recorduri=additional.recorduri WHERE exporttime IS NULL AND serviceid='inffr' AND exportset='main' AND loadtime<'{$midnight}' ORDER BY CALLTIME";
	//echo $query;
	$result=mysql_query($query);
	$num=mysql_numrows($result);

	echo "<table>\n";

	$i=0;
	$lasturi='';
	$receivers=array('haringey','luton','reading','other');
	while ($i < $num) {
		$recorduri=mysql_result($result,$i,'recorduri');
		if ($recorduri!=$lasturi) {
			$lasturi=$recorduri;
		//loadgood
		//loadtest
		//loadurgent
		//loadempty
		//loadpartial
		//recformat
			if ((mysql_result($result,$i,'loadempty')=='1') or (mysql_result($result,$i,'loadexclude')=='1')) {
				$colourhead='#FFAAAA';
				$colourbody='#FFEEEE';
			}
			else {
				if (mysql_result($result,$i,'loadtest')=='1') {
					$colourhead='#AAAAFF';
					$colourbody='#EEEEFF';
				}
				else {
					$colourhead='#AAFFAA';
					$colourbody='#EEFFEE';
				}
			}
			$sendlist='';
			foreach ($receivers as $key) {
				$sendlist.="<a href='?exporturi={$recorduri}&exportto={$key}'>{$key}</a> ";
			}
			echo "<tr bgcolor={$colourhead}>";
			echo "<td>{$recorduri}</td><td>Call:".mysql_result($result,$i,'calltime')
			." Load:".mysql_result($result,$i,'loadtime')
			." DDI:".mysql_result($result,$i,'callddi')
			." CLI:".mysql_result($result,$i,'callcli')
			." Agent:".mysql_result($result,$i,'loadagent')."<br>\n"
			.mysql_result($result,$i,'ns_primary')
			.' '.mysql_result($result,$i,'nf_primary')
			.' '.mysql_result($result,$i,'nn_primary')
			.' '.mysql_result($result,$i,'ar_primary')
			.' '.mysql_result($result,$i,'as_primary')
			.' '.mysql_result($result,$i,'al_primary')
			.' '.mysql_result($result,$i,'at_primary')
			.' '.mysql_result($result,$i,'ac_primary')
			//.' '.mysql_result($result,$i,'ay_primary')
			.' '.mysql_result($result,$i,'ap_primary')
			.' '.mysql_result($result,$i,'pp_primary')
			.' '.mysql_result($result,$i,'pe_primary')
			.((mysql_result($result,$i,'loadpartial')=='1')?" (PARTIAL)":'')
			."<br>\nSend To:{$sendlist}"
			."</td>\n"
			."</tr>";
		}
		$addfield=mysql_result($result,$i,'addfield');
		$addvalue=mysql_result($result,$i,'addvalue');
		//$exporttime=mysql_result($result,$i,'exporttime');

		//$LongName=GetAName($serviceid,$servicesub);
		echo "<tr bgcolor={$colourbody}>";
		echo "<td>{$addfield}</td><td>{$addvalue}</td>\n";
		echo "</tr>";
		$i++;
	}


	//TOO MUCH WORK TO DO!!!!!
	echo "</table>\n";

	mysql_close();
	include('../includes/footer.php');
?>
