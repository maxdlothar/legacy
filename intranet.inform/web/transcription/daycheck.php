<HTML>
	<head>
		<title>Transcriptions</title>
	</head>
	<BODY>
		<?
			require_once('../includes/connect_db.php');
			$midnight=date('Y-m-d', time()-(date('N')=='1'?259200:86400));
			//$username="root";
			//$password="password";
			//$database="system";
			//mysql_connect("192.168.1.209",$username,$password);
			//@mysql_select_db($database) or die('Unable to select database');
			//$query="SELECT * FROM oldload ORDER BY loaddesc, loadsort ASC";
			//$oldresult=mysql_query($query);
			//mysql_close();

			//$database="loader07";
			//mysql_connect("192.168.1.209",$username,$password);
			//@mysql_select_db($database) or die( "Unable to select database");

			//$query="SELECT serviceid, servicesub, COUNT(recorduri) AS recordsum, DATE_FORMAT(calltime,'%Y-%m-%d') AS calldate, DATE_FORMAT(loadtime,'%Y-%m-%d') AS loaddate, exporttime FROM records WHERE exporttime>'".date('Ymd',time()-86400*2)."' GROUP BY exporttime DESC, serviceid, servicesub, exportset, calldate, loaddate";
			//THIS ONE! $query="SELECT serviceid, servicesub, COUNT(recorduri) AS recordsum, DATE_FORMAT(exporttime,'%d') AS exportday, DATE_FORMAT(exporttime,'%H') AS exporthour FROM records WHERE exporttime>'".date('Ymd',time()-86400*2)."' GROUP BY exportday desc,serviceid, servicesub, exporthour";
			$query="SELECT serviceid, servicesub, COUNT(recorduri) AS recordsum, DATE_FORMAT(exporttime,'%d') AS exportday, DATE_FORMAT(exporttime,'%H') AS exporthour FROM loader07.records WHERE exporttime>'".date('Ymd',time()-86400*7)."' AND (loadempty IS NULL OR loadempty='0') AND (loadexclude IS NULL OR loadexclude='0') GROUP BY exportday desc, exporthour, serviceid, servicesub";
			//echo $query;
			$result=mysql_query($query);
			//echo mysql_error();
			$num=mysql_numrows($result);

			/*function GetAName($serviceid, $servicesub) {
				global $NameList;
				$NameCode=$serviceid."/".$servicesub;
				$RetName=$NameList{$NameCode};
				if (empty($RetName)) {
					$query="SELECT servicename FROM services WHERE service='".$serviceid."' and servsub='".$servicesub."' and servicestart<=now() and serviceend>=now()";
					//echo $query;
					$longresult=mysql_query($query);
					if (mysql_numrows($longresult)>0) {
						$RetName=mysql_result($longresult,0,'servicename');
						$NameList{$NameCode}=$RetName;
					}
				}
				return (empty($RetName)?'{'.$serviceid.'/'.$servicesub.'}':$RetName);
			}*/

			//TOO MUCH WORK TO DO!!!!!
			echo "<h3>Exported (Last Seven Days)</h3>\n";
			//echo "<table border=1>\n";
			//echo "<th>Day</th><th>Client/Service</th><th>Time</th><th>Count</th>\n";
				$i=0;
				while ($i < $num) {
					$hourlist{mysql_result($result,$i,'exporthour')}=1;
					$i++;
				}
				ksort($hourlist);
				//global $result;
				//global $num;
				//echo $banner_agent['agentgrp']."=".$grouptest."\n";
				//var_dump($banner_agent);
				$i=0;
				while ($i < $num) {
//					$loaddate=mysql_result($result,$i,'loaddate');
					//	echo "<tr>";
						$serviceid=mysql_result($result,$i,'serviceid');
						$servicesub=mysql_result($result,$i,'servicesub');
//						$exportset=mysql_result($result,$i,'exportset');
						$recordsum=mysql_result($result,$i,'recordsum');
						$ExportHour=mysql_result($result,$i,'exporthour');
						$ExportDay=mysql_result($result,$i,'exportday');

						$NameCode="{$serviceid}/{$servicesub}";
						$LongName=isset($NameList{$NameCode})?$NameList{$NameCode}:'';
						if (empty($LongName)) {
							//$query="SELECT servicename FROM services WHERE service='".$serviceid."' and servsub='".$servicesub."' and servicestart<=now() and serviceend>=now()";
							//echo $query;
							//$longresult=mysql_query($query);
							//if (mysql_numrows($longresult)>0) {
							//	$LongName=mysql_result($longresult,0,'servicename');
							//}
							//else {
								$LongName="{".$NameCode."}";
							//}
							$NameList{$NameCode}=$LongName;
						}
						$rowlist{"{$ExportDay}-{$LongName}"}='';
						$datacell{"{$ExportDay}-{$LongName}"}{$ExportHour}=$recordsum;
						//echo "<td>".$strongon.$LongName.$strongoff."</td><td>".$strongon.$exportset.$strongoff."</td><td>".$strongon.$recordsum.$strongoff."</td><td>".$strongon.$calldate.$strongoff."</td><td>".$loaddate."</td><td>".$exporttime."</td>\n";
						//echo "<td>".$ExportDay."</td><td>".$LongName."</td><td>".$ExportHour."</td><td>".$recordsum."</td>\n";
						//echo "</tr>";
					//}
					$i++;
				}
			//echo "</table>\n";
			echo "<table border=1 width=99% cellpadding=2 cellspacing=0>\n";
			echo "<tr>";
			echo "<th width=1>Day/Client/Service</th>\n";
				foreach ($hourlist as $hour => $ignore) {
						echo "<td>".$hour."</td>\n";
				}
				echo "</tr>";
				krsort($rowlist);
			//foreach ($formlab as $key => $value) {
				foreach ($rowlist as $row => $ignore) {
						echo "<tr>";
						echo "<td>".str_replace(' ','&nbsp;',$row)."</td>\n";
						foreach ($hourlist as $hour => $ignore) {
								echo "<td>".(isset($datacell{$row}{$hour})?$datacell{$row}{$hour}:'&nbsp;')."</td>\n";
						}
						echo "</tr>";
				}
				//var_dump($rowlist);
				//return strpos($banner_agent['agentgrp'],$grouptest)!==FALSE;
			echo "</table>\n";

			mysql_close();

			//$num=mysql_numrows($result);
		?>
	</BODY>
</HTML>
