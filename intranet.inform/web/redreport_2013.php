<?
define('DATE_BBC', 'l, j F Y, G:i T');
define('DATE_SHORT', 'D, j M Y, G:i T');
$page_style[]='style.css';
include('includes/header.php');
include('includes/connect_db.php');
include('includes/functions_db.php');
$page_title='ODIS Designer';
$start=date('YmdHis',$_POST['start']);
$end=date('YmdHis',$_POST['end']);

// Validate dates
if(empty($start)) { echo "<br>Start date is empty."; exit(); }
if(empty($end)) { echo "<br>End date is empty."; exit(); }
if ($start >= $end) {   echo '<br>The start date must be before the end date.'; exit (); }

?>
  <h3>Export Stats</h3>
<?
echo "Between ".date('d/m/Y H:i:s',$_POST['start'])." and ".date('d/m/Y H:i:s',$_POST['end'])."<br>";
?>
<table style="border: 1px solid black; border-collapse:collapse;">
  <tr>
    <th>Service</th>
    <th>Sub-service</th>
    <th colspan='3'>Full</th>
    <th colspan='3'>Partial</th>    
    <th>&nbsp;Totals&nbsp;</th>    
  </tr>
  <tr>
    <th colspan='2'></th>
      <th>&nbsp;&nbsp;Phone&nbsp;&nbsp;</th>
      <th>&nbsp;&nbsp;Web&nbsp;&nbsp;</th>
      <th>&nbsp;&nbsp;Total&nbsp;&nbsp;</th>
      <th>&nbsp;&nbsp;Phone&nbsp;&nbsp;</th>
      <th>&nbsp;&nbsp;Web&nbsp;&nbsp;</th>
      <th>&nbsp;&nbsp;Total&nbsp;&nbsp;</th>
    <th></th>    
  </tr>
<?
// Get a list of all current services
$sql="SELECT service, servsub FROM loader07.services" .
" WHERE serviceend>now()" .
" AND (service NOT LIKE '%demo%' && service NOT LIKE '%inform%' && service NOT LIKE '%generic%' && service NOT LIKE '%service%')" .
" ORDER BY service, servsub ASC";
$dbr=db_do($sql);
$start1=time();
$f_fonetotal=$f_odistotal=$p_fonetotal=$p_odistotal=$rowcount=0;

$zebra=1;
foreach($dbr as $record) {
  $rowcount++;
  $sqlcount="SELECT loadagent,loadempty,loadpartial FROM loader07.records" .
//  " where serviceid = 'ashford'" .
//  " AND servicesub = 'revben'" .
  " where serviceid = '{$record['service']}'" .
  " AND servicesub = '{$record['servsub']}'" .
  " AND exporttime >= $start " .
  " AND exporttime <= $end ";
echo "<br>".$sqlcount;
  $cnt=db_do($sqlcount);

  $f_fonecount=$f_odiscount=$p_fonecount=$p_odiscount=0;
  foreach ($cnt as $reccount) {
    if($reccount['loadempty']=="1" || $reccount['loadpartial']=="1") {    
      if($reccount['loadagent']=="odis") {
        $p_odiscount++;
      } else {
        $p_fonecount++;
      }
    } else {
      if($reccount['loadagent']=="odis") {
        $f_odiscount++;
      } else {
        $f_fonecount++;
      }
    }
  }
  $f_subtotal=$f_fonecount+$f_odiscount;
  $f_fonetotal+=$f_fonecount;
  $f_odistotal+=$f_odiscount;
  $p_subtotal=$p_fonecount+$p_odiscount;
  $p_fonetotal+=$p_fonecount;
  $p_odistotal+=$p_odiscount;
  $total=$f_subtotal+$p_subtotal;
  echo "<tr style='background:" . ($zebra++ % 2 ? '#e6e6e6' : '#ffffff') . ";'>";
  echo "<td>".$record['service']."</td>";
  echo "<td>".$record['servsub']."&nbsp;&nbsp;</td>";
  echo "<td>".$f_fonecount."</td>";
  echo "<td>".$f_odiscount."</td>";
  echo "<td style='font-weight: bold;'>".$f_subtotal."</td>";
  echo "<td>".$p_fonecount."</td>";
  echo "<td>".$p_odiscount."</td>";
  echo "<td style='font-weight: bold;'>".$p_subtotal."</td>";
  echo "<td style='font-weight: bold;'>".$total."</td>";
  echo "</tr>";
  
  if($rowcount==20) {
    $rowcount=0;
?>
    <tr>
      <th colspan='2'></th>
      <th>&nbsp;&nbsp;Phone&nbsp;&nbsp;</th>
      <th>&nbsp;&nbsp;Web&nbsp;&nbsp;</th>
      <th>&nbsp;&nbsp;Total&nbsp;&nbsp;</th>
      <th>&nbsp;&nbsp;Phone&nbsp;&nbsp;</th>
      <th>&nbsp;&nbsp;Web&nbsp;&nbsp;</th>
      <th>&nbsp;&nbsp;Total&nbsp;&nbsp;</th>
      <th></th>    
    </tr>
<?
  }
}
$end1=time();
echo "Report took ".date('i:s',$end1-$start1)." seconds<br><br>";

$f_grandtotal=$f_fonetotal+$f_odistotal;
$p_grandtotal=$p_fonetotal+$p_odistotal;
$totaltotal=$f_grandtotal+$p_grandtotal;
echo "<tr style='font-weight:bold;border-top: 1px solid #666;'>";
echo "<td colspan='2'>Grand Totals</td>";
echo "<td>".$f_fonetotal."</td>";
echo "<td>".$f_odistotal."</td>";
echo "<td>".$f_grandtotal."</td>";
echo "<td>".$p_fonetotal."</td>";
echo "<td>".$p_odistotal."</td>";
echo "<td>".$p_grandtotal."</td>";
echo "<td>".$totaltotal."</td>";
echo "</tr>";
?>
</table>
