<?
define('DATE_BBC', 'l, j F Y, G:i T');
define('DATE_SHORT', 'D, j M Y, G:i T');
$page_style[]='style.css';
include('includes/header.php');
include('includes/connect_db.php');
include('includes/functions_db.php');
$page_title='ODIS Designer';
$start=((strpos(':',$_REQUEST['start']))===FALSE)?(date('Y-m-d H:i:s',$_REQUEST['start'])):($_REQUEST['start']);
$end=((strpos(':',$_REQUEST['end']))===FALSE)?(date('Y-m-d H:i:s',$_REQUEST['end'])):($_REQUEST['end']);
//$end=date('Y-m-d H:i:s',$_POST['end']);
//$start=date('YmdHis',$_POST['start']);
//$end=date('YmdHis',$_POST['end']);

// Validate dates
if(empty($start)) { echo "<br>Start date is empty."; exit(); }
if(empty($end)) { echo "<br>End date is empty."; exit(); }
if ($start >= $end) {   echo "<br>The start date must be before the end date {$start} >= {$end}."; exit (); }

echo "<h3>Export Stats</h3>
Between {$start} and {$end}<br>
<table style='border: 1px solid black; border-collapse:collapse;'>
  <tr>
    <th>Service</th>
    <th>Export</th>
    <th colspan='3'>Full</th>
    <th colspan='3'>Partial</th>
    <th>&nbsp;Totals&nbsp;</th>
  </tr>
";

$sql="SELECT serviceid, exportset,
sum(if(loadagent='odis' and not loadpartial,1,0)) as sumodisfull,
sum(if(loadagent!='odis' and not loadpartial,1,0)) as sumfull,
sum(if(loadagent='odis' and loadpartial,1,0)) as sumodispart,
sum(if(loadagent!='odis' and loadpartial,1,0)) as sumpart
FROM loader07.records
WHERE exporttime BETWEEN '{$start}' AND '{$end}' and loadempty='0'
GROUP BY serviceid, exportset";

//echo $sql;
$dbr=db_do($sql);
$f_fonetotal=$f_odistotal=$p_fonetotal=$p_odistotal=$rowcount=0;

$rowcount=0;
$totalfull=0;
$totalodisfull=0;
$totalpart=0;
$totalodispart=0;

$lastclient='';
$headonnext=true;
foreach($dbr as $record) {
	$fullline=$record{'sumfull'}+$record{'sumodisfull'};
	$partline=$record{'sumpart'}+$record{'sumodispart'};
	$totalline=$fullline+$partline;

	$totalfull+=$record{'sumfull'};
	$totalodisfull+=$record{'sumodisfull'};
	$totalpart+=$record{'sumpart'};
	$totalodispart+=$record{'sumodispart'};
	if (($thisclient=substr($record{'serviceid'},0,3))!=$lastclient) {
		if (!($addgap=!$headonnext)) {
			headline();
			$rowcount=0;
			$headonnext=false;
		}
		$lastclient=$thisclient;
	}
	rowout('td', $record{'serviceid'}, $record{'exportset'}, $record{'sumfull'}, $record{'sumodisfull'}, $fullline,
		$record{'sumpart'}, $record{'sumodispart'}, $partline, $totalline, $addgap);
	if (($rowcount+=($addgap?2:1))>20) $headonnext=true;
	if ($addgap) $addgap=false;
}

$fullline=$totalfull+$totalodisfull;
$partline=$totalpart+$totalodispart;
$totalline=$fullline+$partline;
echo "<tr style='font-weight:bold;border-top: 1px solid #666;'><td colspan='2'>Grand Totals</td>
<td>{$totalfull}</td><td>{$totalodisfull}</td><td>{$fullline}</td>
<td>{$totalpart}</td><td>{$totalodispart}</td><td>{$partline}</td>
<td>{$totalline}</td>
</tr>
</table>
";

function rowout($coltag, $col1, $col2, $col3, $col4, $col5, $col6, $col7, $col8, $col9, $addgap) {
	if ($addgap) echo "<tr><td colspan='9'>&nbsp;</td></tr>";
	echo "<tr><{$coltag}>{$col1}</{$coltag}><{$coltag}>{$col2}</{$coltag}>
	<{$coltag}>{$col3}</{$coltag}><{$coltag}>{$col4}</{$coltag}><{$coltag}>{$col5}</{$coltag}>
	<{$coltag}>{$col6}</{$coltag}><{$coltag}>{$col7}</{$coltag}><{$coltag}>{$col8}</{$coltag}>
	<{$coltag}>{$col9}</{$coltag}>
	</tr>";
}
function headline() {
echo "<tr><th colspan='2'></th>
	<th>&nbsp;&nbsp;Phone&nbsp;&nbsp;</th>
	<th>&nbsp;&nbsp;Web&nbsp;&nbsp;</th>
	<th>&nbsp;&nbsp;Total&nbsp;&nbsp;</th>
	<th>&nbsp;&nbsp;Phone&nbsp;&nbsp;</th>
	<th>&nbsp;&nbsp;Web&nbsp;&nbsp;</th>
	<th>&nbsp;&nbsp;Total&nbsp;&nbsp;</th>
<th></th></tr>
";
}

?>
