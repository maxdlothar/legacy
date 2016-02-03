<?
require_once "./loadshare.php";
require_once "../includes/system_time.php";
require_once "../includes/agent_info.php";
require_once "../includes/agent_banner.php";

$agentdet=agent_info();
$agentinit=strtolower($agentdet['agentinit']);

?>
<SCRIPT LANGUAGE="JavaScript">
function openChild(file,window) {
    childWindow=open(file,window,'resizable=no,width=200,height=400');
    if (childWindow.opener == null) childWindow.opener = self;
    }
</SCRIPT>
<?
mysql_connect(localhost,$username,$password);
@mysql_select_db($database) or die("Unable to select database");

$fieldarray=array('recordid',
	'recorduri',
	'serviceid',
	'servicesub',
	'calltime',
	'loadtime',
	'exporttime',
	'senttime',
	'loadagent',
	'loadempty',
	'loadpartial',
	'salutation',
	'forename',
	'surname',
	'property',
	'street',
	'locality',
	'town',
	'county',
	'country',
	'postcode',
	'phonenum',
	'email'
);

$hidearray=array('recordid',
	'recorduri',
	'serviceid',
	'servicesub',
	'calltime',
	'loadtime',
	'exporttime',
	'senttime',
	'loadagent'
);

function set_item($fishkey, $fishvalue) {
	global $postdata;
	global $recordupdate;
	global $additives;
	global $fieldarray;
	global $storearray;

	if (!empty($fishvalue)) {
		$storearray[$fishkey]=$fishvalue;
		$postdata=$postdata.str_pad($fishkey,25,'.').$fishvalue."\n";

		if (in_array($fishkey, $fieldarray)) {
			if (empty($recordupdate)) {
				$recordupdate='insert into records set '.$fishkey."='".mysql_real_escape_string($fishvalue)."'";
			}
			else {
				$recordupdate=$recordupdate.', '.$fishkey."='".mysql_real_escape_string($fishvalue)."'";
			}
		}
		else {
			$additives[$fishkey]=$fishvalue;
		}
	}
}

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
$recorduri=$_REQUEST['recorduri'];

if (empty($myservice)||empty($myservsub)) {
	$query="SELECT service,servsub,servicename FROM services WHERE servicestart<=now() AND serviceend>=now() ORDER BY servicename;";
	$servicelist=mysql_query($query);
	$servicenum=mysql_numrows($servicelist);

	$i=0;
	while ($i < $servicenum) {
		echo '<a href="'.$_SERVER['PHP_SELF'].'?service='.mysql_result($servicelist,$i,"service").'&servsub='.mysql_result($servicelist,$i,"servsub").'">'.mysql_result($servicelist,$i,"servicename")."</a><br>\n";
		//echo '<option value ="'.mysql_result($nameresult,$i,"username").'">'.mysql_result($nameresult,$i,"realname").'</option>';
		$i++;
	}
}
else {
	echo '<form action="'.$_SERVER['PHP_SELF'].'" method=post>';
	echo '<input type="hidden" name="service" value="" />';
	echo '<input type="hidden" name="servsub" value="" />';
	echo '<input type="submit" name="action" value="Service Select" />';
	echo '</form>';
	if ($_POST['action']=='Submit') {
		$itr_description=$_POST['itr_description'];
		//if (!empty($itr_description)) {
			//var_dump ($_POST);
			//var_dump ($hidearray);
			$additives=array();
			$storearray=array();
			$postdata="";
			$recordupdate="";
			$rectime=microtime();
			if (empty($recorduri)) {
				set_item('recorduri', generate_uri($rectime));
			}
			//else {
			//	set_item('recorduri', generate_uri($rectime));
			//}
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
			$recorduri='';
		//}
	}
	echo '<form action="'.$_SERVER['PHP_SELF'].'" method=post>';
	echo '<input type="hidden" name="service" value="'.$myservice.'" />';
	echo '<input type="hidden" name="servsub" value="'.$myservsub.'" />';
	if (!empty($recorduri)) {
		echo '<input type="hidden" name="recorduri" value="'.$recorduri.'" />';
	}
	//echo '<br />';
	echo '<table width="100%">';

	echo '<input type="button" value="Get Record" onClick="openChild(\'record2007.php?service='.$myservice.'&servsub='.$myservsub.'\',\'getrecord\')">';
	if (!empty($recorduri)) {
		echo ' '.$recorduri."\n";
	}
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
		echo $postdata."\n";
		echo $recordupdate."\n";
		//print_r($additives);
	  	foreach ($additives as $key=>$value) {
			$addupdate="insert into additional set recorduri='".$storearray['recorduri']."', addfield='".$key."', addvalue='".mysql_real_escape_string($value)."'";
			echo $addupdate."\n";
	  	}
		echo "</pre></td></tr></table>\n";
	}
}

function add_entry($fieldid, $label, $type, $width) {
	$cr="\n";
	if (empty($type)) {
		$type="C";
		switch ($fieldid) {
			case "loadempty":
				$label="Empty Record";
				$type="X";
				break;
			case "loadpartial":
				$label="Partial Record";
				$type="X";
				break;
			case "salutation":
				$label="Salutation";
				$type="L";
				$listdata="Mr;Mrs;Miss";
				$width=10;
				break;
			case "forename":
				$label="Forename";
				break;
			case "property":
				$label="Property";
				break;
			case "street":
				$label="Street";
				break;
			case "surname":
				$label="Surname";
				break;
			case "locality":
				$label="Locality";
				break;
			case "town":
				$label="Town";
				break;
			case "county":
				$label="County";
				break;
			case "country":
				$label="Country";
				$listdata="GB'United Kingdom";
				$type="L";
				break;
			case "postcode":
				$label="Post Code";
				$width=15;
				break;
			case "phonenum":
				$label="Telephone";
				$width=30;
				break;
			case "email":
				$label="E-Mail";
				break;
			default:
				$label='"'.$fieldid.'"';
		}
	}
	if (empty($width)) {
		$width=120;
	}
	switch ($type) {
		case "X":
			echo '<tr><td>'.$label.'</td><td><INPUT type="checkbox" name="item['.$fieldid.']" value="1" /></td></tr>'.$cr;
			break;
		case "N":
			echo '<tr><td valign="top">'.$label.'</td><td><textarea name="item['.$fieldid.']" cols=100 rows=4></textarea></td></tr>'.$cr;
			break;
		case "L":
			$pulllist=explode(";", $listdata);
		  	//foreach ($pulllist as $key=>$value) {
			echo '<tr><td>'.$label.'</td><td><select name="item['.$fieldid.']">'.$cr;
		  	foreach ($pulllist as $value) {
				$lablist=explode("'", $value);
				if (empty($lablist[1])) {
					$lablist[1]=$lablist[0];
				}
				echo $label.'<option value="'.$lablist[0].'">'.$lablist[1].'</option>'.$cr;
		  	}
			echo '</select></td></tr>'.$cr;
			break;
		default:
			echo '<tr><td>'.$label.'</td><td><INPUT type="text" name="item['.$fieldid.']" value="" size='.$width.' /></td></tr>'.$cr;
	}
}

mysql_close();

?>
