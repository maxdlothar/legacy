<HTML>
	<head>
		<title>Unsent Records</title>
	</head>
	<BODY>
		<?
			$midnight=date('Y-m-d', time()-(date('N')=='1'?259200:86400));
			$username="root";
			$password="password";

			$database="loader07";
			mysql_connect("192.168.1.209",$username,$password);
			@mysql_select_db($database) or die( "Unable to select database");

			$query="SELECT serviceid, exportset, COUNT(recorduri) AS recordsum, exporttime,'%Y-%m-%d' FROM records WHERE senttime is null and exporttime is not null GROUP BY exporttime DESC, serviceid, exportset";
			$result=mysql_query($query);
			$num=mysql_numrows($result);

			function GetAName($serviceid, $exportset) {
				global $NameList;
				$NameCode=$serviceid."/".$exportset;
				$RetName=isset($NameList{$NameCode})?$NameList{$NameCode}:'';
				//if (empty($RetName)) {
				//	$query="SELECT servicename FROM services WHERE service='".$serviceid."' and servsub='".$exportset."' and servicestart<=now() and serviceend>=now()";
				//	//echo $query;
				//	$longresult=mysql_query($query);
				//	if (mysql_numrows($longresult)>0) {
				//		$RetName=mysql_result($longresult,0,'servicename');
						$NameList{$NameCode}=$RetName;
				//	}
				//}
				//return (empty($RetName)?'{'.$serviceid.'/'.$exportset.'}':$RetName);
				return (empty($RetName)?"{$serviceid}/{$exportset}":$RetName);
			}


			function show_load($groupon) {
				global $result;
				global $num;
				$i=0;
				while ($i < $num) {
					echo "<tr>";
					$serviceid=mysql_result($result,$i,'serviceid');
					$exportset=mysql_result($result,$i,'exportset');
					$recordsum=mysql_result($result,$i,'recordsum');
					$exporttime=mysql_result($result,$i,'exporttime');

					$LongName=GetAName($serviceid,$exportset);
					echo "<td>".$exporttime."</td><td>".$recordsum."</td><td>".$LongName."</td>\n";
					echo "</tr>";
					$i++;
				}
			}


			//TOO MUCH WORK TO DO!!!!!
			echo "<h3>Unsent Records</h3>\n";
			echo "<table width=100% border=0>\n";
			echo "<th>Exported</th><th>Exported</th><th>Client/Service</th>\n";
			show_load(false);
			echo "</table>\n";

			mysql_close();
		?>
	</BODY>
</HTML>
