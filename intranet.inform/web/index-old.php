<html>
<?
require_once "techservices/techshare.php";
require_once "includes/agent_banner.php";
//	$username="root";
//	$password="password";
//	//$database="system";
?>
<head>
	<title>Inform Communications Intranet</title>
	<style media="all" type="text/css">
		body { font-size: normal }
		h1 { font-size: 140% }
		h2 { font-size: 120% }
		li h2 { margin: 0px; margin-top: 6px }
	</style>
</head>
<body>

<?
	mysql_connect("192.168.1.209",$username,$password);
	@mysql_select_db("system") or die( "Unable to select database");
	$today=date("md"); //,strtotime("20070305000000"));


	$query="SELECT * FROM webpix WHERE ((startday<='{$today}' and endday>='{$today}')) ORDER BY RAND() limit 1 ";
	//echo $query;
	$result=mysql_query($query);
	//echo $result;

	mysql_close();

	$num=mysql_numrows($result);
	if ($num>0) {
		echo '<TABLE BORDER=0 CELLPADDING=4 CELLSPACING=0 WIDTH=100% background="wallpaper/'.mysql_result($result,$i,'picname').'">';
	}
	else {
		echo '<TABLE BORDER=0 CELLPADDING=4 CELLSPACING=0 WIDTH=100%>';
	}
?>
	<TR BGCOLOR="silver">
		<TD COLSPAN=3>
			<B><FONT SIZE=+2>Inform Communications Intranet</FONT></B>
		</TD>
	</TR>
	<TR>
		<TD VALIGN=TOP>
			<h1>Quick links</h1>
		
			<h2>General</h2>
			<ul>
			<li><a href="http://192.168.1.209/contacts/council/" target="_top">Council contacts</a></li>
			<li><a href="http://192.168.1.209/tools/stockcontrol/" target="_top">Stock control</a></li>
<!--			<li><a href="http://192.168.1.209/stockcontrol/editstock.php?db=loader-spain04a" target="_top">Stock control, Spain (Old)</a></li>-->
<!--			<li><a href="http://192.168.1.209/stockcontrol/editstock.php?db=loader-thai06a" target="_top">Stock control, Thailand</a></li>-->
<!--			<li><a href="http://192.168.1.209/whereseen/whereseen.php?db=loader-mertonapp06a" target="_top">Where&nbsp;seen&nbsp;(Merton)</a></li>-->
<!--			<li><a href="http://192.168.1.209/payments/manual/manual-cao.php" target="_top">CAO&nbsp;payment&nbsp;system</a></li>-->
			</li>
			</ul>
<?if (agent_ingroup('R')) {?>
			<h2>Reports</h2>
			<ul>
				<li><a href="https://secure.informcommunications.plc.uk/clients/">Secure client area</a></li>
				<li><a href="http://192.168.9.80/local/reports/webstats/">Basic website stats</a></li>
			</ul>
<?}?>
	
			<h2>Information&hellip;</h2>
			<ul>
			<li><a href="http://192.168.1.209/intranet/forums">Forums</a></li>
			</ul>
		
			<h2>Transcription Tallies, Etc&hellip;</h2>
			<ul>
				<!--<li><a href="file:///n:/INTRANET/HTML/TRANSTAL.HTM">Transcription Tallies</a></li>-->
				<li><a href="transcription/tallies.php">Transcription Tallies</a></li>
				<li><a href="html/RECNTEXP.HTM">Recent Exports</a></li>
				
			</ul>
		
			<h2>External links</h2>
			<ul>
				<li><a href="http://www.immigration.govt.nz/" target="_top">NZIS Website</a></li>
				<li><a href="http://tourismthailand.co.uk/" target="_top">Thailand Website</a></li>
			</ul>
		</TD>
		<TD VALIGN=TOP>
			<h2>Old Navigation</h2>
			<ul>
				<li><A HREF="file:///n:/INTRANET/HTML/10000038.htm">Personnel&nbsp;Extension&nbsp;&amp; Contact&nbsp;Numbers</A></li>
				<li><A HREF="file:///n:/INTRANET/HTML/sales/sales.htm">Sales</A></li>
			</ul>

<?
				if (agent_ingroup("A")) {
?>
					<h2>Additional Services</h2>
					<ul>
<!--						<li><a href="https://companyweb:50443/exchweb/bin/auth/owalogon.asp?url=https://companyweb:50443/exchange&reason=0">Web Mail</a></li>-->
						<li><a href="https://192.168.1.105/exchange/">Webmail</a></li>
					</ul>
<?
				}
				if (agent_ingroup("L")) {
?>
					<h2>Loaders</h2>
					<ul>
						<li><a href="http://intranet.inform/intranet/loader2007/transsetup08.php">Transcription Setup</a></li>
					</ul>
<?
				}
				if (agent_ingroup("K")) {
?>
					<h2>Accounts</h2>
					<ul>
						<li><a href="http://intranet.inform/intranet/reporting/transcriptions.php">Transcriptions</a></li>
					</ul>
<?
				}
				if (agent_ingroup("C")) {
?>
					<h2>Technical Services</h2>
					<ul>
						<li><a href="http://intranet.inform/intranet/techservices/techservices_itr_new.php">New ITR Request</a></li>
					</ul>

					<h2>Server Uptime</h2>
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
				mysql_connect("192.168.1.209",$username,$password);
				@mysql_select_db("system") or die( "Unable to select database");
			
				$query="SELECT * FROM messages ORDER BY messageservice, messagetime, messagecontent DESC";
				$result=mysql_query($query);

				mysql_close();
			
				$num=mysql_numrows($result);

				$lastservice='';
				$lasttitle='';
				$i=0;
				while ($i < $num) {
					$service=mysql_result($result,$i,'messageservice');
					$messagetime=mysql_result($result,$i,'messagetime');
					$messagetitle=mysql_result($result,$i,'messagetitle');
					$message=mysql_result($result,$i,'messagecontent');
					if ($lastservice!=$service) {
						$lastservice=$service;
					//echo '<span class="newshead">'.str_replace("_", "&nbsp;", date('l, j_F_Y', strtotime($newsdate))).'</span>';
						echo "<p><h2>{$service}</h2></p>";
					}
					if ($lasttitle!=$messagetitle) {
						$lasttitle=$messagetitle;
					//echo '<span class="newshead">'.str_replace("_", "&nbsp;", date('l, j_F_Y', strtotime($newsdate))).'</span>';
						echo "<p><h3>{$messagetitle}</h3></p>";
					}
					echo "<p>{$messagetime}<br>{$message}</p>";
					$i++;
				}
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

</body>
</html>

