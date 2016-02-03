<HTML>
	<head>
		<title>Transcriptions Totals</title>
	</head>
	<BODY>
		<?
			$username="root";
			$password="password";
			$database="system";

			$database="loader07";

			mysql_connect("192.168.1.209",$username,$password);
			@mysql_select_db($database) or die( "Unable to select database");
		
			$query="SELECT DATE_FORMAT(exporttime,\"%Y/%m\") AS exportdate, serviceid, exportset, COUNT(*) AS exporttally FROM records WHERE EXPORTTIME IS NOT NULL AND loadempty IS NOT NULL GROUP BY exportdate, serviceid, exportset ORDER BY exportdate DESC, serviceid, exportset;";
			//exporttime>="'+M.TQStart+'"AND exporttime<="'+M.TQEnd+'" AND 
			//echo $query;
			$result=mysql_query($query);
			//echo mysql_error();
			$num=mysql_numrows($result);
			echo "<table border=0>\n";
			echo "<th>Month</th><th>Client</th><th>Set</th><th>Exported</th>\n";
				$i=0;
				while ($i < $num) {
					//$loaddate=mysql_result($result,$i,'loaddate');
					//if (empty($loaddate)==$groupon) {
						echo "<tr>";
						$exportdate=mysql_result($result,$i,'exportdate');
						$serviceid=mysql_result($result,$i,'serviceid');
						$exportset=mysql_result($result,$i,'exportset');
						$exporttally=mysql_result($result,$i,'exporttally');

						echo "<th>".$exportdate."</th><th>".$serviceid."</th><th>".$exportset."</th><th>".$exporttally."</th>\n";
						//echo "<td>".$strongon.$serviceid.$strongoff."</td><td>".$strongon.$servicesub.$strongoff."</td><td>".$strongon.$recordsum.$strongoff."</td><td>".$strongon.$calldate.$strongoff."</td><td>".$loaddate."</td><td>".$exporttime."</td>\n";
						echo "</tr>";
					//}
					$i++;
				}
			echo "</table>\n";

			mysql_close();
		
			$num=mysql_numrows($result);
		?>
	</BODY>
</HTML>
