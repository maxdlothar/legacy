<?
$page_title=$page_p='Inform Communications Intranet';
include 'includes/connect_idb.php';
include 'includes/header.php';
require_once "techservices/techshare.php";
echo "<h2>{$page_title}</h2>";

	$today=date("md"); //,strtotime("20070305000000"));

	$query="SELECT * FROM webpix WHERE ((startday<='{$today}' and endday>='{$today}')) ORDER BY RAND() limit 1 ";
	//echo $query;
	$result=mysqli_query($db_con, $query);
	//echo $result;

	//mysqli_close($db_con);

	//$num=($result===FALSE)?0:mysqli_num_rows($result);
	//if ($num>0) {
	//	echo '<TABLE BORDER=0 CELLPADDING=4 CELLSPACING=0 WIDTH=100% background="wallpaper/'.mysql_result($result,0,'picname').'">';
	//}	else {
		echo '<TABLE BORDER=0 CELLPADDING=4 CELLSPACING=0 WIDTH=100%>';
	//}
echo <<<STUFF
<TR>
	<TD VALIGN=TOP>
		<p class="bold">General</p>
		<ul>
		<li><a href="http://portal.latestinfo.co.uk">Portal Access</a></li>
		<ul><li><a href="/techservices/portalpass.php">Portal Password</a></li></ul>
		</ul>
STUFF;

if (agent_ingroup('R')) {
echo <<<STUFF
	<p class="bold">Reports</p>
	<ul>
		<li><a href="https://secure.latestinfo.co.uk/clients/">Secure client area</a></li>
	</ul>
STUFF;
}

if (agent_ingroup('M')) {
echo <<<STUFF
	<p class="bold">Crisys</p>
	<ul>
		<li><a href="http://test.crisys.net/">Crisys Admin</a></li>
	</ul>
STUFF;
}
?>

			<p class="bold">Transcription Tallies, Etc&hellip;</p>
			<ul>
				<li><a href="transcription/tallies.php">Transcription Tallies</a></li>
				<ul>
					<li><a href="transcription/clientlinks.php?client=brien">Bristol&nbsp;Web</a></li>
					<li><a href="transcription/clientlinks.php?client=smoen">Staffs&nbsp;Moorlands&nbsp;Web</a></li>
					<li><a href="transcription/clientlinks.php?client=hpeen">High&nbsp;Peak&nbsp;Web</a></li>
				</ul>
				<li><a href="transcription/exports.php">Exports (Last Two weeks)</a></li>
				<li><a href="transcription/exportsummary.php">Daily Export Summary</a></li>
				<li><a href="transcription/exportunsent.php">Unsent Exports</a></li>
				<li><a href="transcription/daycheck.php">Exports By Time</a></li>
<?
				if (agent_ingroup('R')) {
					echo '<li><a href="transcription/exportcontent.php?random=15">Random Transcription Sample</a></li>';
					echo '<li><a href="transcription/exportcontent.php?randomloaded=15">Random Unsent Sample</a></li>';
				}
				if (agent_ingroup('A')) {
					echo '<li><a href="transcription/exportcontent.php?agentinit">Own Loaded But Unsent</a></li>';
				}
				if (agent_ingroup('C')) {
					echo '<li><a href="transcription/fixfrauds.php">Fix Frauds</a></li>';
					echo '<li><a href="transcription/exportcontent.php?exportunsent">Waiting to Send</a></li>';
				}
?>
			</ul>
		</TD>
		<TD VALIGN=TOP>
			<p class="bold">Personnel List</p>
			<ul>
				<li><A HREF="contacts.php">Personnel&nbsp;Extension&nbsp;&amp; Contact&nbsp;Numbers</A></li>
			</ul>
			<p class="bold">Web &amp; Email Services / Links</p>
			<ul>
				<li><a href="weblist.php">Links to web &amp; email services </a></li>
        <li><a href='redstats.php'>Export statistics</a></li>
				<li><a href="http://google.com/analytics">Google analytics</a></li>
				<li><a href="reporting/pdfcount.php">PDF count report</a></li>
			</ul>
<?
				if (agent_ingroup("A")) {
?>
					<p class="bold">Additional Services</p>
					<ul>
						<li><a href="https://informcommunications.plc.uk:50443/exchange/">Webmail</a></li>
					</ul>
<?
				}
				if (agent_ingroup("L")) {
?>
					<p class="bold">Loaders</p>
					<ul>
						<li><a href="http://192.168.1.209/intranet/loader2007/transsetup08.php">Transcription Setup</a></li>
					</ul>
<?
				}
				if (agent_ingroup("K")) {
?>
					<p class="bold">Accounts</p>
					<ul>
						<li><a href="http://192.168.1.209/intranet/reporting/transcriptions.php">Transcriptions</a></li>
					</ul>
<?
				}

				if (agent_ingroup("C")) {
?>
					<p class="bold">Technical Services</p>
					<ul>
						<li><a href="/techservices/techservices_itr_new.php">New ITR Request</a></li>
					</ul>

					<p class="bold">Server Uptime</p>
					<ul>
						<li><a href="http://192.168.9.80/uptime.php">Emperor</a></li>
						<li><a href="http://192.168.1.209/uptime.php">Adelie</a></li>
					</ul>
<?
				}
?>

		</TD>
<?
	if (agent_ingroup("A")) {
?>
		<TD VALIGN=TOP BGCOLOR=#906060>
			<?
				//$CrsSchedTime=filemtime('/home/F-Migrate/LIVE/CRS/SYSTEML/LASTACT.DAT');
				//if (($LastCrs=floor((time()-$CrsSchedTime)/60))>13) {
				//	echo "<span style='color: red; font-weight: bold;'>CRISYS SCHEDULER ({$LastCrs} minutes ago)!:<br>".date('Y-m-d H:i:s', $CrsSchedTime).'</span>';
				//}
				//mysqli_connect('192.168.1.106', 'liveaccess', 'GreyFlatBox');
				//@mysqli_select_db($db_con, 'system') or die( "Unable to select database (106)");
				showmessages();
				$db_con=mysqli_connect('192.168.1.209',$username,$password);
				@mysqli_select_db($db_con, 'system') or die( "Unable to select database (209)");
				showmessages();
				mysqli_close($db_con);
			?>
		</TD>
<?
	}
?>
	</TR>
</TABLE>
<? include("includes/footer.php");

function showmessages() {
	global $db_con;
	$query="SELECT min(messagetime) as mintime, max(messagetime) as maxtime, count(*) as countmess, messageservice, messagetitle, messagecontent FROM messages GROUP BY messageservice, messagetitle, messagecontent ORDER BY messageservice, maxtime, messagecontent DESC";
	$result=mysqli_query($db_con, $query);
	$num=mysqli_num_rows($result);

	$lastservice='';
	$lasttitle='';
	$i=0;
	while ($i < $num) {
		$service=mysqli_result($result,$i,'messageservice');
		$mintime=mysqli_result($result,$i,'mintime');
		$maxtime=mysqli_result($result,$i,'maxtime');
		$messagetitle=mysqli_result($result,$i,'messagetitle');
		$message=mysqli_result($result,$i,'messagecontent');
		$countmess=mysqli_result($result,$i,'countmess');
		if ($lastservice!=$service) {
			$lastservice=$service;
			echo "<p><p>{$service}</p></p>";
		}
		if ($lasttitle!=$messagetitle) {
			$lasttitle=$messagetitle;
			echo "<p><h3>{$messagetitle}</h3></p>";
		}
		echo (($countmess>1)?"<p>{$mintime}-{$maxtime} x{$countmess}<br>":"<p>{$maxtime}<br>");
		if (($slen=strlen($message))<60) $crpos=FALSE;
			else if (($crpos=strrpos($message, "\n", max(-$slen,60-$slen)))===FALSE) $crpos=strpos($message, "\n", min(60,$slen));
		$outmessage=($crpos===FALSE)?("<b>{$message}</b>"):'<b>'.substr($message,0,$crpos).'</b><small>'.substr($message,$crpos).'</small>';
		echo str_replace("\n", '<br>', str_replace("\r", '<br>', ($outmessage)));
		echo '</b></p>';
		$i++;
	}
}

function mysqli_result($res, $row, $field=0) {
    $res->data_seek($row);
    $datarow = $res->fetch_array();
    return $datarow[$field];
}

?>
