<?
$page_title=$page_h2='Current Personnel List';
include('includes/connect_idb.php');
include('includes/header.php');
$noemail=array("ARY","ART","MBF","NS","SMW","TJV","RSG","DW","KS","RSG");
$retired=array("ARY");

echo "<h2>".$page_title."</h2>";

$today=date("Y-m-d H:l:s");
$query="SELECT agentname, agentinit,agentpos, agenttel, agenthome, agenthomeshow, agentmob, agentmobshow,agentip,agentinitedit FROM agent where '$today' <= agentto and '$today' >= agentfrom order by agentname";
$result=mysqli_query($db_con, $query)or die(mysqli_error());
$emaillist=array();
?>

<div id="maintable">
<table style="border-collapse: collapse;">
   <tr>
      <td class="title">Name (click for init)</td>
      <td class="title">Position</td>
      <td class="title">Ext</td>
      <td class="title">Home Tel</td>
      <td class="title">Mobile</td>
      <td class="title">Email</td>
      <td class="title">IP</td>
   <tr>
<?php
$zebra=0;

while ($row=mysqli_fetch_array($result, MYSQL_ASSOC)) {
   $agentinit=($row['agentinitedit']==NULL)?$row['agentinit']:$row['agentinitedit'];
   $initupper=strtoupper($agentinit);
   echo '<tr ';echo($zebra==1)?'class="zebra"':'';echo '>';

      if (in_array($initupper,$retired)) {
         // do not display retired people
      } else {
         // if the agent has email then add them to the email everyone list
         if (!in_array($initupper,$noemail)) $emaillist[]=$agentinit;

         echo "<td style='width:15em'>";
         echo "<em style=\"cursor: pointer\" onclick=\"document.getElementById('password_{$row['agentname']}').style.display='inline'\">{$row['agentname']}<span id=\"password_{$row['agentname']}\" style=\"display: none\"> / {$initupper}</span></em>";
         echo '</td>';
         echo '<td>';echo (!empty($row['agentpos']))?$row['agentpos']:'&nbsp';echo '</td>';
         echo '<td>';echo (!empty($row['agenttel']))?$row['agenttel']:'&nbsp';echo '</td>';
         echo '<td>';echo ($row['agenthomeshow']==1)?$row['agenthome']:'&nbsp;';echo '</td>';
         echo '<td>';echo ($row['agentmobshow']==1)?$row['agentmob']:'&nbsp;';echo '</td>';
         echo '<td>';echo (in_array($initupper,$noemail))?'':'<a href="mailto:'.strtolower($agentinit).'">Email</a>';echo '</td>';

         echo '<td>';
         if (!empty($row['agentip'])) {
            $iparray = explode(".", $row['agentip']);
            $trimmed = $iparray[3];
            echo $trimmed;
         } else {
            echo '&nbsp';
         }
         echo '</td>';
         echo "</tr>";
         $zebra=($zebra==1?0:1);
      }
}
?>
</table>
</div>
<?
$count=0;
$mailtolist="mailto:";
while (isset($emaillist[$count])) {
   if($count!=0) $mailtolist=$mailtolist.",";
   $mailtolist=strtolower($mailtolist.$emaillist[$count]);
   $count++;
}

// echo "<br>To send an email to EVERYONE - <a href='".$mailtolist."'>click here</a><br>";
?>
<br>
For any amendments please send an email to <a href="mailto:mfa">mfa</a>.
<? include("includes/footer.php");?>
