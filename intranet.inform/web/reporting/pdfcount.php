<?
$page_title=$page_p='Inform Communications Intranet';
include '../includes/connect_idb.php';
include '../includes/header.php';
require_once "../techservices/loadershare.php";
echo "<h2>".$page_title."</h2>";

// Create an array of the PDF names
$pdfarray = array(
  "AshfordBCCaseStudyPDF.pdf",
  "CityOfEdinburghCaseStudyPDF.pdf",
  "LondonBoroughofTowerHamletsCaseStudy.pdf",
  "SandwellCouncilCaseStudyPDF.pdf",
  "TorfaenCaseStudy.pdf"
);


  $task="totals";
  if(isset($_GET['task'])) $task=$_GET['task'];

  echo ($task=="totals"?"Totals":"<a href='pdfcount.php?task=totals'>Totals</a>");
  foreach($pdfarray as &$value) {
    echo ($task==$value?" | ".$value:" | <a href='pdfcount.php?task=".$value."'>".$value."</a>");
  }

  mysql_connect("192.168.1.209",$username,$password);
  @mysql_select_db("reporting") or die( "Unable to select database");
  if($task=="totals") {
    $query="SELECT pdfname,count(*) as pdfcount FROM pdfcount group by pdfname";
  } else {
	  $query="SELECT pdfname,ip_address,UNIX_TIMESTAMP(timestamp) as pdftime FROM pdfcount where pdfname='".$task."' order by timestamp asc";
  }
  $result=mysql_query($query) or die($query."<br/><br/>".mysql_error());
  mysql_close();

  if($task!="totals") {
    echo "<br><br>PDF count for ".$task;
  }
?>
<br><br>


<TABLE BORDER=1 CELLPADDING=4 CELLSPACING=0>
<?
if($task=="totals") {
?>
<tr style="font-weight: bold;">
  <td>PDF name</td>
  <td>Count</td>
</tr>
<?
  $total=0;
  while ($row = mysql_fetch_array($result)) {
    echo "<tr>";
    echo "<td>";
    echo "<a href='pdfcount.php?task=".$row["pdfname"]."'>".$row["pdfname"]."</a>";
    echo "</td>";
    echo "<td style='text-align: right;'>";
    echo $row["pdfcount"];
    echo "</td>";
    echo "</tr>";
    $total+=$row["pdfcount"];
  }
?>
<tr style="font-weight: bold;">
  <td>TOTAL</td>
  <td style='text-align: right;'><? echo $total;?></td>
</tr>

<?
} else { // Not totals - pdf count
?>
<tr style="font-weight: bold;">
  <td>Date/time</td>
  <td>Region</td>
  <td>City</td>
  <td>Country (just for fun)</td>
</tr>
<?
  $total=0;

  while ($row = mysql_fetch_array($result)) {
    $location = file_get_contents('http://freegeoip.net/json/'.$row["ip_address"]);
    $parsedJson  = json_decode($location);
    echo "<tr>";
    echo "<td>";
    echo date("D, F jS g:i a",$row["pdftime"]);
    echo "</td>";
    echo "<td>";
    echo $parsedJson->region_name;
    echo "</td>";
    echo "<td>";
    echo $parsedJson->city;
    echo "</td>";
    echo "<td>";
    echo $parsedJson->country_name;
    echo "</td>";
    echo "</tr>";
    $total++;
  }

?>
  <tr style="font-weight: bold;">
    <td>TOTAL</td>
    <td colspan=3 style="text-align: center;"><?echo $total;?></td>
  </tr>
<?
}
?>
</table>

<?
include("../includes/footer.php");
?>

