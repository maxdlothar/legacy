<?
require_once "./loadshare.php";
require_once "../includes/system_time.php";
//require_once "../includes/agent_info.php";
//require_once "../includes/agent_banner.php";

//$agentdet=agent_info();
//$agentinit=strtolower($agentdet['agentinit']);

$myservice=$_REQUEST['service'];
//$myservice=$_POST['service'];
//if (empty($myservice)) {
//	$myservice=$_GET['service'];
//}
$myservsub=$_REQUEST['servsub'];
//$myservsub=$_POST['servsub'];
//if (empty($myservsub)) {
//	$myservsub=$_GET['servsub'];
//}

echo '<SCRIPT LANGUAGE="JavaScript">'."\n";
echo 'function loadrecord(uricode) {'."\n";
	//echo 'opener.location=opener.location.pathname+"?service='.$myservice.'&servsub='.$myservsub.'"+"&uricode="+uricode;'."\n";
	echo 'opener.location="loader2007.php?service='.$myservice.'&servsub='.$myservsub.'"+"&recorduri="+uricode;'."\n";
	//echo 'alert (opener.location+"?service='.$myservice.'?servsub='.$myservsub.'"+"&uricode="+uricode);'."\n";
	//if (childWindow.opener == null) childWindow.opener = self;
	echo 'self.close();';
echo '}'."\n";
echo '</SCRIPT>'."\n";

mysql_connect(localhost,$username,$password);
@mysql_select_db($database) or die("Unable to select database");

if (empty($myservice)||empty($myservsub)) {
	echo "Error!<br>{$myservice}<br>{$myservsub}";
}
else {
	$query="SELECT preuri,prerec FROM prefill07 WHERE preserv='".$myservice."' and presub='".$myservsub."' ORDER BY preuri;";
	echo $query;
	$servicelist=mysql_query($query);
	$servicenum=mysql_numrows($servicelist);

	$i=0;
	while ($i < $servicenum) {
		echo '<a href="#" onclick=loadrecord("'.mysql_result($servicelist,$i,"preuri").'");>'.mysql_result($servicelist,$i,"prerec")."</a>\n";
		//echo '<option value ="'.mysql_result($nameresult,$i,"username").'">'.mysql_result($nameresult,$i,"realname").'</option>';
		$i++;
	}

/*	echo '<form action="'.$_SERVER['PHP_SELF'].'" method=post>';
	echo '<input type="hidden" name="service" value="" />';
	echo '<input type="hidden" name="servsub" value="" />';
	echo '<input type="submit" name="action" value="Service Select" />';
	echo '</form>';
	if ($_POST['action']=='Submit') {
		$itr_description=$_POST['itr_description'];
		//if (!empty($itr_description)) {
			//var_dump ($_POST);
			//var_dump ($hidearray);
			$postdata="";
			$rectime=microtime();
			set_item('recorduri', generate_uri($rectime));
			set_item('serviceid', $myservice);
			set_item('servicesub', $myservsub);
			set_item('calltime', time_long($rectime));
			set_item('loadtime', time_long($rectime));
			//	'exporttime' LEAVE NULL
			//	'senttime' LEAVE NULL
			set_item('loadagent', $agentinit);
			foreach ($_POST["item"] as $fishkey=>$fishvalue) {
				set_item($fishkey, $fishvalue);
			}
			//echo "----------------------------------------\n";
			//echo "Submit-{$query}\n";
			$query = "insert into itr_requests set itr_description='".mysql_real_escape_string($itr_description)."', itr_raised=now(), itr_raisedby='{$agentinit}';";
			//mysql_query($query);
			//echo "Submit-{$query}\n";
	
			$itr_record=mysql_insert_id();
			$itr_id=substr($itr_record,-3);
			$query = "update itr_requests set itr_id='{$itr_id}' where itr_record='{$itr_record}' limit 1;";
			//mysql_query($query);
		//}
	}
	echo '<form action="'.$_SERVER['PHP_SELF'].'" method=post>';
	echo '<input type="hidden" name="service" value="'.$myservice.'" />';
	echo '<input type="hidden" name="servsub" value="'.$myservsub.'" />';
	//echo '<br />';
	echo '<table width="100%">';

	echo '<input type="button" value="Get Record" onClick="openChild(\'record2007.php\',\'getrecord\')">';
  	foreach ($fieldarray as $key=>$value) {
		if (!in_array($value, $hidearray)) {
		//if (!array_key_exists($key, $hidearray)) {
			//echo '<br /><INPUT type=text name="item['.$value.']" value="'.$key.'" size=120 /></td>';
			add_entry($value, "", "", 0);
		}
	}
	add_entry("moomin", "Moomin Count", "N", 250);
	//echo '<tr><td>Moomin</td><td><INPUT type=text name="item[moomin]" value="" size=120 /></td></tr>';
	//echo '<br />';
	echo '<tr><td></td><td align="right"><input type="submit" name="action" value="Submit" />&nbsp;';
	echo '&nbsp;<input type="reset" name="action" value="Reset" /></td></tr>';
	echo '</table>';

	echo '</form>';

	if (!empty($postdata)) {
		echo "<table border=1 cellspacing=1 bgcolor=white><tr><td><pre>";
		echo $postdata;
		echo "</pre></td></tr></table>\n";
	}
}
*/

mysql_close();

/*}
		$artist=$_POST['artist'];
		$artistfree=$_POST['artistfree'];
		if (!empty($artistfree)) {
			$artist=$artistfree;
		}
		$item=$_POST['item'];
		if (!(empty($format) or empty($artist) or empty($item))) {
			//echo "Format:{$format}";
			//echo "Artist:{$artist}";
			//echo "Item:{$item}";
			//echo $query;
			mysql_query($query);
		}
	}
	if ($_POST['action']=='gotitem') {
		$gotdate=date('Y-m-d', strtotime($_POST['gotdate']));
		$thanks=$_POST['thanks'];
		$entryid=$_POST['entryid'];
		if (!(($gotdate<1) or empty($thanks) or empty($entryid))) {
			//echo 'Gotdate:'.$gotdate;
			//echo "<br>Thanks:{$thanks}";
			//echo "<br>EntryID:{$entryid}<br>";
			//$query = "INSERT INTO contacts VALUES ('','$first','$last','$phone','$mobile','$fax','$email','$web')";
			//echo $query;
			mysql_query($query);
		}
	}

$result=mysql_query($query);

$num=mysql_numrows($result);

	if ($mypage) {
		$artistresult=mysql_query($query);
		$artistnum=mysql_numrows($artistresult);

	}


	echo "<TABLE>";	
	echo "<TR>";
	if ($mypage or $notmypage) {
		echo "<th></th>";
	}
	echo "	<th>Format</th>";
	echo "	<th>Artist</th>";
	echo "	<th>Item</th>";
	echo "	<th>Got</th>";
	echo "	<th>Thanks To</th>";
	echo "</TR>";

	$i=0;
	while ($i < $num) {
		$format=mysql_result($result,$i,"format");
		$artist=mysql_result($result,$i,"artist");
		$item=mysql_result($result,$i,"item");
		$gotdate=mysql_result($result,$i,"gotdate");
		$thanks=mysql_result($result,$i,"thanks");
		echo "<TR>";
		if ($mypage or $notmypage) {
			echo '<td><input type=radio name="entryid" value="'.mysql_result($result,$i,'entryid').'" /></td>';
		}
		echo "	<td>$format</td>";
		echo "	<td>$artist</td>";
		echo "	<td>$item</td>";
		if ($gotdate>"1") {
			echo "	<td>$gotdate</td>";
		}
		else {
			echo "	<td></td>";
		}
		echo "	<td>$thanks</td>";
		echo "</TR>\n";
		$i++;
	}
	if ($mypage) {
		echo '<tr><td></td>';

	echo '	<td><select name="format">';
		echo '<option value ="cd">cd</option>';
		echo '<option value ="dvd">dvd</option>';
		echo '<option value ="pccd">pccd</option>';
	echo "	</select></td>";

	echo '	<td><select name="artist">';
		echo "<option value =''>-Select or Enter Below-</option>";
		$i=0;
		while ($i < $artistnum) {
			$thisartist=mysql_result($artistresult,$i,"artist");
			echo "<option value ='{$thisartist}'>{$thisartist}</option>";
			$i++;
		}
	echo "	</select>";

	echo '<td><INPUT type=text name="item" value="" size=20 /></td>';
	//if ($gotdate>"1") {
	//	echo "	<td>$gotdate</td>";
	//}
	//else {
	//	echo "	<td></td>";
	//}
	//echo "	<td>$thanks</td>";

	echo '</tr>';

	echo '<tr>';
	//echo '<td></td>';
	//echo '<td></td>';
	echo '<td></td>';
	echo '<td></td>';

	echo '<td><INPUT type=text name="gotdate" value="today" size=15 /></td>';

	echo '	<td><select name="thanks">';
		$i=0;
		while ($i < $namenum) {
			echo '<option value ="'.mysql_result($nameresult,$i,"username").'">'.mysql_result($nameresult,$i,"realname").'</option>';
			$i++;
		}
	echo "	</select></td>";

	echo '<td><INPUT type=submit name="action" value="gotitem" /></td>';
	echo '</tr>';
	echo "</table>";
	}
	if ($mypage or $notmypage) {
	}

*/
}
?>
