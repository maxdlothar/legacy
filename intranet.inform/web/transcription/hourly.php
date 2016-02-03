<?
$page_title=$page_p='Inform Communications Intranet';
include '../includes/header.php';
include '../includes/connect_db.php';
require_once "../techservices/loadershare.php";
echo "<h2>".$page_title."</h2>";

	$today=date("md");
  $arunatotal=$naheedtotal=$sharontotal=$michelletotal=$calltotal=0;
?>
	<TR>
		<TD VALIGN=TOP>
<?
  mysql_connect("192.168.1.209",$username,$password);
  @mysql_select_db("loader07") or die( "Unable to select database");

  $freq="day";
  if(isset($_GET['freq'])) $freq=$_GET['freq'];
  echo ($freq=="day"?"Yesterday":"<a href='hourly.php?freq=day'>Yesterday</a>")." | ";
  echo ($freq=="week"?"Previous 7 days":"<a href='hourly.php?freq=week'>Previous 7 days</a>")." | ";
  echo ($freq=="month"?"Previous 30 days":"<a href='hourly.php?freq=month'>Previous 30 days</a>");
  echo "<br><br>";

  // Define the start and end times for the date range dropdown boxes
  // Each day will be set to 00:00:00 -> 23:59:59 below
  $date_end=date('Ymd000000', (strtotime('today, 04:00:00')));
  if($freq=="day") {
    $date_start=date('Ymd000000', (strtotime('1 days ago, 04:00:00')));
  } else if($freq=="week") {
    $date_start=date('Ymd000000', (strtotime('7 days ago, 04:00:00')));  
  } else {
    $date_start=date('Ymd000000', (strtotime('30 days ago, 04:00:00')));  
  }
  
  $michellelist=gettranscribed("michellesawyer",$date_start,$date_end);
  $sharonlist=gettranscribed("smw",$date_start,$date_end);
  $arunalist=gettranscribed("art",$date_start,$date_end);
  $naheedlist=gettranscribed("ns",$date_start,$date_end);
  
 
	$query="SELECT DATE_FORMAT( calltime, '%H' ) AS hourly, COUNT( * ) ".
  "FROM `records` ".
  "WHERE calltime > ".$date_start.
  " AND calltime < ".$date_end.
  " AND (loadagent != 'odis' AND loadagent != 'XXX') ".
  "GROUP BY hourly";
  $result=mysql_query($query);
  mysql_close();
  if($freq=="day") {
    echo "Yesterdays calls";
  } else if($freq=="week") {
    echo "Previous 7 days calls";
  } else {
    echo "Previous 30 days calls";
  }

  echo "<br><br>";
?>

<TABLE BORDER=1 CELLPADDING=4 CELLSPACING=0>
<tr>
  <td>Time</td>
  <td>Calls</td>
  <td>Aruna</td>
  <td>Naheed</td>
  <td>Michelle</td>
  <td>Sharon</td>  
  <td>TOTALS</td>
</tr>  
<?
  while ($row = mysql_fetch_array($result)) {
    $total=0;
    echo "<tr>";
    echo "<td>".$row[0].":00</td>";
    echo "<td bgcolor='#EEEEEE'>".$row[1]."</td>";
    echo "<td>";
    if(isset($arunalist[$row[0]])) {
      echo $arunalist[$row[0]];
      $total+=$arunalist[$row[0]];
      $arunatotal+=$arunalist[$row[0]];
    }
    echo "</td>";
    echo "<td>";
    if(isset($naheedlist[$row[0]])) {
      echo $naheedlist[$row[0]];
      $total+=$naheedlist[$row[0]];
      $naheedtotal+=$naheedlist[$row[0]];
    }
    echo "</td>";
    echo "<td>";
    if(isset($michellelist[$row[0]])) {
      echo $michellelist[$row[0]];
      $total+=$michellelist[$row[0]];
      $michelletotal+=$michellelist[$row[0]];
    }
    echo "</td>";
    echo "<td>";
    if(isset($sharonlist[$row[0]])) {
      echo $sharonlist[$row[0]];
      $total+=$sharonlist[$row[0]];
      $sharontotal+=$sharonlist[$row[0]];
    }
    echo "</td>";
    echo "<td bgcolor='#EEEEEE'>".$total."</td>";
    echo "</tr>";    
    $calltotal+=$row[1];
  }
  $totaltotal=$arunatotal+$naheedtotal+$michelletotal+$sharontotal;
?>
  <tr>
    <td>TOTALS</td>
    <td><?echo $calltotal;?></td>
    <td><?echo $arunatotal;?></td>
    <td><?echo $naheedtotal;?></td>
    <td><?echo $michelletotal;?></td>
    <td><?echo $sharontotal;?></td>
    <td><?echo $totaltotal;?></td>
  </tr>
</table>

<?
function gettranscribed($agentname,$date_start,$date_end) {
  $agentlist = array();

	$query="SELECT DATE_FORMAT( loadtime, '%H' ) AS hourly, COUNT( * ) ".
  "FROM `records` ".
  "WHERE loadtime > ".$date_start.
  " AND loadtime < ".$date_end.
  " AND loadagent = '$agentname' ".
  "GROUP BY hourly";

  $result=mysql_query($query);
  while ($row = mysql_fetch_array($result)) {
    $agentlist[$row[0]]=$row[1];
  }  
  return $agentlist;
}  

include("../includes/footer.php");
?>

