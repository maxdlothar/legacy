<?
define('DATE_BBC', 'l, j F Y, G:i T');
define('DATE_SHORT', 'D, j M Y, G:i T');
include('includes/header.php');
include('includes/connect_db.php');
$page_title='ODIS Designer';
$page_style[]='/css/base.css';
?>
	<head><title>ODIS Web Service List</title></head>
  <h3>Web/Email service overview</h3>

	<p><a href='http://odisdesigner.inform/'>ODIS Designer</a></p>

  <p style="font-weight: bold;">Skinned (not neccessarily live) : </p>
  <table>
    <tr>
      <th style="min-width : 20em;">Name</th>
      <th style="min-width : 20em;">URL</th>
      <th>id</th>
    </tr>
  <tr><td>Ashford Revben</td><td><a href="http://ashfordrb.latestinfo.co.uk">ashfordrb.latestinfo.co.uk</a><br></td><td>51</td></tr>
    <tr><td>Ashford Environment</td><td><a href="http://ashforden.latestinfo.co.uk">ashforden.latestinfo.co.uk</a><br></td><td>54</td></tr>
  <tr><td>Demo Revben</td><td><a href="http://demorb.latestinfo.co.uk">demorb.latestinfo.co.uk</a><br></td><td>70</td></tr>
  <tr><td>Demo Election</td><td><a href="http://electiondemo.latestinfo.co.uk">electiondemo.latestinfo.co.uk</a><br></td><td>32</td></tr>
  <tr><td>East Lothian Business Rates</td><td><a href="http://eastlothiannndr.latestinfo.co.uk">eastlothiannndr.latestinfo.co.uk</a><br></td><td>74</td></tr>
  <tr><td>East Lothian Revben</td><td><a href="http://eastlothianrb.latestinfo.co.uk">eastlothianrb.latestinfo.co.uk</a><br></td><td>26</td></tr>
  <tr><td>Edinburgh Revben</td><td><a href="http://edinburghrb.latestinfo.co.uk">edinburghrb.latestinfo.co.uk</a><br></td><td>56</td></tr>
  <tr><td>Enfield Revben</td><td><a href="http://enfieldrb.latestinfo.co.uk">enfieldrb.latestinfo.co.uk</a><br></td><td>50</td></tr>
  <tr><td>Environment demo (skinned)</td><td><a href="http://environmentdemo.latestinfo.co.uk">environmentdemo.latestinfo.co.uk</a><br></td><td>38</td></tr>
  <tr><td>Fife Revben</td><td><a href="http://fiferb.latestinfo.co.uk">fiferb.latestinfo.co.uk</a><br></td><td>65</td></tr>
  <tr><td>Forms Demo</td><td><a href="http://formsdemo.latestinfo.co.uk">formsdemo.latestinfo.co.uk</a><br></td><td>78</td></tr>
  <tr><td>Guildford Revben</td><td><a href="http://guildfordrb.latestinfo.co.uk">guildfordrb.latestinfo.co.uk</a><br></td><td>62</td></tr>
  <tr><td>Haringey Revben</td><td><a href="http://haringeyrb.latestinfo.co.uk">haringeyrb.latestinfo.co.uk</a><br></td><td>64</td></tr>
  <tr><td>Hartlepool DEMO Revben</td><td><a href="http://hartlepooldemorb.latestinfo.co.uk">hartlepooldemorb.latestinfo.co.uk</a><br></td><td>75</td></tr>
  <tr><td>Hartlepool Revben</td><td><a href="http://hartlepoolrb.latestinfo.co.uk">hartlepoolrb.latestinfo.co.uk</a><br></td><td>23</td></tr>
  <tr><td>Sandwell Revben</td><td><a href="http://sandwellinfo.latestinfo.co.uk">sandwellinfo.latestinfo.co.uk</a><br></td><td>69</td></tr>
  <tr><td>Newark Revben</td><td><a href="http://newarkrb.latestinfo.co.uk">newarkrb.latestinfo.co.uk</a><br></td><td>57</td></tr>  
  <tr><td>Spelthorne Revben</td><td><a href="http://spelthornerb.latestinfo.co.uk">spelthornerb.latestinfo.co.uk</a><br></td><td>58</td></tr>
  <tr><td>Surrey Heath Revben</td><td><a href="http://surreyheathrb.latestinfo.co.uk">surreyheathrb.latestinfo.co.uk</a><br></td><td>28</td></tr>
  <tr><td>Tandridge Environment</td><td><a href="http://envirotandridge.latestinfo.co.uk">envirotandridge.latestinfo.co.uk</a><br></td><td>76</td></tr>
  <tr><td>Torfaen Revben</td><td><a href="http://torfaenrb.latestinfo.co.uk">torfaenrb.latestinfo.co.uk</a><br></td><td>55</td></tr>
</table>
<?

$results="SELECT id,service, subservice, title FROM odis.services WHERE end>now() ORDER BY id DESC";
$result=mysql_query($results)or die(mysql_error());
if (empty($results)) {
  print "  <p>No services.</p>\n";
} else {
?>
  <br>
  <p style="font-weight: bold;">Standard : </p>
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
