<?
define('DATE_BBC', 'l, j F Y, G:i T');
define('DATE_SHORT', 'D, j M Y, G:i T');
include('includes/header.php');
include('includes/connect_db.php');
$page_title='ODIS Designer';
$page_style[]='/css/base.css';
?>
  <h3>Web/Email service overview</h3>
<?
$results="SELECT id,service, subservice, title FROM odis.services WHERE end>now() ORDER BY id DESC";
$result=mysql_query($results)or die(mysql_error());
if (empty($results)) {
  print "  <p>No services.</p>\n";
} else {
?>
  <table>
    <tr>
      <th>Name</th>
      <th>URL</th>
      <th>id</th>
    </tr>
<?
  $now=time();
//  foreach($resultx as $result) {
  while ($row=mysql_fetch_array($result, MYSQL_ASSOC)) {
    if ($row['id']=="31") {
      $service_url="http://election.info.latestinfo.co.uk/elections";
    } else {
      $service_url="http://{$row['subservice']}.{$row['service']}.latestinfo.co.uk/";
    }
?>
    <tr<?=$class?>>
      <td><?=$row['title']?></td>
      <td><a href="<?=$service_url?>"><?=$service_url?></a></td>
      <td>&nbsp;&nbsp;&nbsp;<?=$row['id']?></td>
    </tr>
<?
  }
?>
  </table>
<?
}
?>
