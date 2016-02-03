<HTML>
	<head>
		<title>Transcriptions</title>
	</head>
	<BODY>
		<?
			$midnight=date('Y-m-d', time()-(date('N')=='1'?259200:86400));
			$username="root";
			$password="password";
			//$database="system";
			//mysql_connect("192.168.1.209",$username,$password);
			//@mysql_select_db($database) or die('Unable to select database');
			//$query="SELECT * FROM oldload ORDER BY loaddesc, loadsort ASC";
			//$oldresult=mysql_query($query);
			//mysql_close();

			$database="loader07";
			mysql_connect("192.168.1.209",$username,$password);
			@mysql_select_db($database) or die( "Unable to select database");

/*			$query="SELECT serviceid, servicesub, exportset, COUNT(recorduri) AS recordsum, DATE_FORMAT(calltime,'%Y-%m-%d') AS calldate, MIN(calltime) AS callfirst FROM records WHERE loadtime IS NULL GROUP BY serviceid, servicesub, exportset, calldate ORDER BY callfirst, serviceid, servicesub, exportset, calldate ASC";
			//echo $query;
			$result=mysql_query($query);
			//echo mysql_error();
			$num=mysql_numrows($result);
			if ($num>0) {
				$i=0;
				echo "<h3>Not Yet Loaded</h3>\n";
				echo "<table width=100% border=0>\n";
				echo "<th width='20%'>Earliest&nbsp;Call</th><th width='5%'>Count</th><th>Client/Service</th><th width='10%'>Export</th>\n";
				while ($i < $num) {
					echo "<tr>";
					$serviceid=mysql_result($result,$i,'serviceid');
					$servicesub=mysql_result($result,$i,'servicesub');
					$exportset=mysql_result($result,$i,'exportset');
					$recordsum=mysql_result($result,$i,'recordsum');
					$calldate=mysql_result($result,$i,'calldate');
					$callfirst=mysql_result($result,$i,'callfirst');
					$LongName=GetAName($serviceid,$servicesub);

					$strongon="<strong><font color=green>";
					$strongoff="</font></strong>";
					echo "<td>".$strongon.$callfirst.$strongoff."</td><td>".$strongon.$recordsum.$strongoff."</td><td>".$strongon.$LongName.$strongoff."</td><td>".$strongon.$exportset.$strongoff."</td>\n";
					echo "</tr>";
					$i++;
				}
				echo "</table>\n";
			}
*/
/*			$query="SELECT serviceid, servicesub, exportset, COUNT(recorduri) AS recordsum, DATE_FORMAT(calltime,'%Y-%m-%d') AS calldate, DATE_FORMAT(loadtime,'%Y-%m-%d') AS loaddate FROM records WHERE exporttime IS NULL AND loadtime IS NOT NULL GROUP BY serviceid, servicesub, exportset, calldate, loaddate ORDER BY serviceid, servicesub, exportset, calldate, loaddate ASC";
			//echo $query;
			$result=mysql_query($query);
			//echo mysql_error();
			$num=mysql_numrows($result);
			if ($num>0) {
				$i=0;
				echo "<h3>Not Yet Sent</h3>\n";
				echo "<table width=100% border=0>\n";
				echo "<th>Client/Service</th><th>Export</th><th>Count</th><th>Call</th><th>Loaded</th>\n";
				while ($i < $num) {
					$loaddate=mysql_result($result,$i,'loaddate');
					if (empty($loaddate)==$groupon) {
						echo "<tr>";
						$serviceid=mysql_result($result,$i,'serviceid');
						$servicesub=mysql_result($result,$i,'servicesub');
						$exportset=mysql_result($result,$i,'exportset');
						$recordsum=mysql_result($result,$i,'recordsum');
						$calldate=mysql_result($result,$i,'calldate');

						$LongName=GetAName($serviceid,$servicesub);

						$strongon=($loaddate<$midnight)?"<strong><font color=red>":"";
						$strongoff=($loaddate<$midnight)?"</font></strong>":"";
						echo "<td>".$strongon.$LongName.$strongoff."</td><td>".$strongon.$exportset.$strongoff."</td><td>".$strongon.$recordsum.$strongoff."</td><td>".$strongon.$calldate.$strongoff."</td><td>".$strongon.$loaddate.$strongoff."</td>\n";
						echo "</tr>";
					}
					$i++;
				}
				echo "</table>\n";
			}
*/

			//$query="SELECT serviceid, servicesub, exportset, COUNT(recorduri) AS recordsum, DATE_FORMAT(calltime,'%Y-%m-%d') AS calldate, DATE_FORMAT(loadtime,'%Y-%m-%d') AS loaddate, exporttime, LEFT(DATE_FORMAT(exporttime,'%Y'),1) AS groupdate FROM records WHERE exporttime>'".date('Ymd',time()-86400*28)."' OR isnull(exporttime) GROUP BY serviceid, servicesub, exportset, calldate, loaddate ORDER BY groupdate, serviceid, servicesub, exportset, calldate, loaddate, exporttime ASC";
			//$query="SELECT serviceid, servicesub, exportset, COUNT(recorduri) AS recordsum, DATE_FORMAT(calltime,'%Y-%m-%d') AS calldate, DATE_FORMAT(loadtime,'%Y-%m-%d') AS loaddate, exporttime, LEFT(DATE_FORMAT(exporttime,'%Y'),1) AS groupdate FROM records WHERE exporttime>'".date('Ymd',time()-86400*7)."' GROUP BY serviceid, servicesub, exportset, calldate, loaddate ORDER BY groupdate, serviceid, servicesub, exportset, calldate, loaddate ASC";
			$query="SELECT serviceid, servicesub, exportset, COUNT(recorduri) AS recordsum, DATE_FORMAT(calltime,'%Y-%m-%d') AS calldate, DATE_FORMAT(loadtime,'%Y-%m-%d') AS loaddate, exporttime FROM records WHERE exporttime>'".date('Ymd',time()-86400*7)."' GROUP BY exporttime DESC, serviceid, servicesub, exportset, calldate, loaddate";
			//echo $query;
			$result=mysql_query($query);
			//echo mysql_error();
			$num=mysql_numrows($result);

			function GetAName($serviceid, $servicesub) {
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
			}


			function show_load($groupon) {
				global $result;
				global $num;
				//echo $banner_agent['agentgrp']."=".$grouptest."\n";
				//var_dump($banner_agent);
				//$strongon=$groupon?"<strong><font color=green>":"";
				//$strongoff=$groupon?"</font></strong>":"";
				$i=0;
				while ($i < $num) {
					$loaddate=mysql_result($result,$i,'loaddate');
					//if (empty($loaddate)==$groupon) {
						echo "<tr>";
						$serviceid=mysql_result($result,$i,'serviceid');
						$servicesub=mysql_result($result,$i,'servicesub');
						$exportset=mysql_result($result,$i,'exportset');
						$recordsum=mysql_result($result,$i,'recordsum');
						$calldate=mysql_result($result,$i,'calldate');
						$exporttime=mysql_result($result,$i,'exporttime');
						//$groupdate=mysql_result($result,$i,'groupdate');

						$NameCode=$serviceid."/".$exportset;
						$LongName=isset($NameList{$NameCode})?$NameList{$NameCode}:'';
						if (empty($LongName)) {
							$query="SELECT exportdesc FROM exports WHERE service='{$serviceid}' and exportset='{$exportset}' and '{$exporttime}' between exportfrom AND exportuntil";
							//echo $query;
							$longresult=mysql_query($query);
							if (mysql_numrows($longresult)>0) {
								$LongName=mysql_result($longresult,0,'exportdesc');
								//if (empty($LongName) or ($exporttime=='2014-01-07 17:30:02')) $LongName="{{$NameCode}}";
								if (empty($LongName)) {
									$query="SELECT exportdesc FROM exports WHERE service='{$serviceid}' and exportset='{$exportset}' AND exportdesc>'' ORDER by exportuntil DESC LIMIT 1";
									//echo $query;
									$longresult=mysql_query($query);
									if (mysql_numrows($longresult)>0) {
										$LongName='<i>'.mysql_result($longresult,0,'exportdesc').'</i>';
									}
								}
								$NameList{$NameCode}=$LongName;
							}
						}
						//echo "<td>".$strongon.$LongName.$strongoff."</td><td>".$strongon.$exportset.$strongoff."</td><td>".$strongon.$recordsum.$strongoff."</td><td>".$strongon.$calldate.$strongoff."</td><td>".$loaddate."</td><td>".$exporttime."</td>\n";
						echo "<td>{$LongName}</td><td>{$NameCode}</td><td>{$recordsum}</td><td>{$calldate}</td><td>{$loaddate}</td><td><a href='exportcontent.php?exporttime={$exporttime}'>{$exporttime}</td>\n";
						echo "</tr>";
					//}
					$i++;
				}
				//return strpos($banner_agent['agentgrp'],$grouptest)!==FALSE;
			}


			//TOO MUCH WORK TO DO!!!!!
			echo "<h3>Exported (Last Seven Days)</h3>\n";
			echo "<table width=100% border=0>\n";
			echo "<th>Client/Service</th><th>Export</th><th>Count</th><th>Call</th><th>Loaded</th><th>Exported</th>\n";
			//show_load(true);
			show_load(false);
			echo "</table>\n";

			mysql_close();

			//$num=mysql_numrows($result);
		?>
	</BODY>
</HTML>
