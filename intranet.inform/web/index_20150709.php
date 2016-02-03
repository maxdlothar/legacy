<?
$page_title=$page_p='Inform Communications Intranet';
include 'includes/header.php';
include 'includes/connect_db.php';
require_once "techservices/techshare.php";
echo "<h2>".$page_title."</h2>";

//	@mysql_select_db("system") or die( "Unable to select database");
	$today=date("md"); //,strtotime("20070305000000"));


	$query="SELECT * FROM webpix WHERE ((startday<='{$today}' and endday>='{$today}')) ORDER BY RAND() limit 1 ";
	//echo $query;
	$result=mysql_query($query);
	//echo $result;

	mysql_close();

	$num=mysql_numrows($result);
	if ($num>0) {
		echo '<TABLE BORDER=0 CELLPADDING=4 CELLSPACING=0 WIDTH=100% background="wallpaper/'.mysql_result($result,0,'picname').'">';
	}	else {
		echo '<TABLE BORDER=0 CELLPADDING=4 CELLSPACING=0 WIDTH=100%>';
	}
echo <<<STUFF
<TR>
	<TD VALIGN=TOP>
		<p class="bold">General</p>
		<ul>
		<li><a href="http://portal.latestinfo.co.uk">Portal Access</a></li>
		<ul><li><a href="/techservices/portalpass.php">Portal Password</a></li></ul>
		<li><a href="http://192.168.1.209/contacts/council/">Old Council Contacts</a></li>
		</ul>
STUFF;

if (agent_ingroup('R')) {
echo <<<STUFF
	<p class="bold">Reports</p>
	<ul>
		<li><a href="https://secure.latestinfo.co.uk/clients/">Secure client area</a></li>
	</ul>
STUFF;
/*MAJOR HEAVY PROCESSING, PLUS DOESN'T WORK!... <li><a href="http://192.168.9.80/local/reports/webstats/">Basic website stats</a></li>*/
}

if (agent_ingroup('M')) {
/*<!--			<li><a href="http://192.168.1.209/tools/stockcontrol/spain" target="_top">Stock control (Spain)</a></li>-->
<!--			<li><a href="http://192.168.1.209/tools/stockcontrol/crusa07" target="_top">Stock control (Crusa)</a></li>-->
<!--			<li><a href="http://192.168.1.209/stockcontrol/editstock.php?db=loader-spain04a" target="_top">Stock control, Spain (Old)</a></li>-->
<!--			<li><a href="http://192.168.1.209/stockcontrol/editstock.php?db=loader-thai06a" target="_top">Stock control, Thailand</a></li>-->
<!--			<li><a href="http://192.168.1.209/payments/manual/manual-cao.php" target="_top">CAO&nbsp;payment&nbsp;system</a></li>-->
<!--			<li><a href="https://secure.latestinfo.co.uk/crusa" target="_top">CrUSA Brochure Ordering</a></li>-->*/
echo <<<STUFF
	<p class="bold">Crisys</p>
	<ul>
		<li><a href="http://test.crisys.net/">Crisys Admin</a></li>
	</ul>
STUFF;
//		<li><A HREF="file:///n:/INTRANET/HTML/sales/crisys/pickman.htm">Old Crisys Web Admin</A></li>
}

//<li><a href="https://secure.latestinfo.co.uk/clients/login/login-oldstats.php">Historical Reports</a></li>

			//<p class="bold">Information&hellip;</p>
			//<ul>
			//<li><a href="http://192.168.1.209/intranet/forums">Forums</a></li>
			//</ul>?>

			<p class="bold">Transcription Tallies, Etc&hellip;</p>
			<ul>
				<!--<li><a href="file:///n:/INTRANET/HTML/TRANSTAL.HTM">Transcription Tallies</a></li>-->
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
<?/*			<p class="bold">External links</p>
			<ul>
				<li><a href="http://tourismthailand.co.uk/" target="_top">Thailand Website</a></li>
			</ul>*/?>
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
<!--				<li><a href="https://192.168.1.105:50443/exchange/">Webmail</a></li>-->
			</ul>
<?
				if (agent_ingroup("A")) {
?>
					<p class="bold">Additional Services</p>
					<ul>
<!--						<li><a href="https://companyweb:50443/exchweb/bin/auth/owalogon.asp?url=https://companyweb:50443/exchange&reason=0">Web Mail</a></li>
						<li><a href="https://192.168.1.105:50443/exchange/">Webmail</a></li>-->
						<li><a href="https://webmail.informcommunications.plc.uk:50443/exchange/">Webmail</a></li>
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
						<li><a href="http://192.168.1.209/intranet/techservices/techservices_itr_new.php">New ITR Request</a></li>
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
				mysql_connect('192.168.1.209',$username,$password);
				@mysql_select_db("system") or die( "Unable to select database");
				showmessages();
				mysql_connect('192.168.1.106', 'liveaccess', 'GreyFlatBox');
				@mysql_select_db("system") or die( "Unable to select database");
				showmessages();
				mysql_close();
			?>



		</TD>
<?
	}
?>
		<!--<TD VALIGN=TOP WIDTH=1>
			<IMG SRC="deadtree.gif">
		</TD>-->
	</TR>
</TABLE>
<? include("includes/footer.php");

function showmessages() {
	$query="SELECT min(messagetime) as mintime, max(messagetime) as maxtime, count(*) as countmess, messageservice, messagetitle, messagecontent FROM messages GROUP BY messageservice, messagetitle, messagecontent ORDER BY messageservice, maxtime, messagecontent DESC";
	$result=mysql_query($query);
	$num=mysql_numrows($result);

	$lastservice='';
	$lasttitle='';
	$i=0;
	while ($i < $num) {
		$service=mysql_result($result,$i,'messageservice');
		$mintime=mysql_result($result,$i,'mintime');
		$maxtime=mysql_result($result,$i,'maxtime');
		$messagetitle=mysql_result($result,$i,'messagetitle');
		$message=mysql_result($result,$i,'messagecontent');
		$countmess=mysql_result($result,$i,'countmess');
		if ($lastservice!=$service) {
			$lastservice=$service;
		//echo '<span class="newshead">'.str_replace("_", "&nbsp;", date('l, j_F_Y', strtotime($newsdate))).'</span>';
			echo "<p><p>{$service}</p></p>";
		}
		if ($lasttitle!=$messagetitle) {
			$lasttitle=$messagetitle;
		//echo '<span class="newshead">'.str_replace("_", "&nbsp;", date('l, j_F_Y', strtotime($newsdate))).'</span>';
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
?>
