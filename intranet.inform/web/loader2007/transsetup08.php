<html>
<?
require_once "../techservices/techshare.php";
require_once "../includes/agent_banner.php";
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
	if (agent_ingroup("L")) {
		echo '<TD VALIGN=TOP BGCOLOR=#906060>';
		mysql_connect("192.168.1.209",$username,$password);
		@mysql_select_db("loader07") or die( "Unable to select database");
	
		$query="SELECT service, servsub, servicename, servicestart, serviceend, oldconvert FROM services WHERE serviceend>date_sub(now(), interval 14 day) ORDER BY servicename";
		$result=mysql_query($query);

		mysql_close();
	
		$num=mysql_numrows($result);

		$lastservice='';
		$i=0;
		while ($i < $num) {
			//servicestart, 
			$servicename=mysql_result($result,$i,'servicename');
			$service=mysql_result($result,$i,'service');
			$servsub=mysql_result($result,$i,'servsub');
			$oldconvert=mysql_result($result,$i,'oldconvert');
			$serviceend=mysql_result($result,$i,'serviceend');
			$servicestart=mysql_result($result,$i,'servicestart');
			//$messagetitle=mysql_result($result,$i,'messagetitle');
			//$message=mysql_result($result,$i,'messagecontent');

			//if ($lastservice!=$service) {
			//	$lastservice=$service;
			////echo '<span class="newshead">'.str_replace("_", "&nbsp;", date('l, j_F_Y', strtotime($newsdate))).'</span>';
			//	echo "<p><h2>{$messagetitle}</h2></p>";
			//}
			echo '<p>';
			$outtime=($oldconvert) or ($servicestart>date('Y-m-d')) or ($serviceend<date('Y-m-d'));
			if ($outtime) {
				echo '<em><span style="opacity:0.2">';
			}
			echo "<strong>{$servicename}</strong> <small>{$service}/{$servsub}";
			if ($servicestart>date('Y-m-d')) {
				echo " <small>start {$servicestart}...</small>";
			}
			if ($serviceend<'9999-12-31') {
				echo " <small>ends {$serviceend}...</small></small>";
			}
			else {
				echo '</small>';
			}
			if ($outtime) {
				echo '</span></em>';
			}
			echo "</p>\n";
			$i++;
		}
	}

?>
</body>
</html>
