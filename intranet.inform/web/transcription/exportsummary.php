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

			//$query="SELECT serviceid, servicesub, exportset, COUNT(recorduri) AS recordsum, DATE_FORMAT(calltime,'%Y-%m-%d') AS calldate, DATE_FORMAT(loadtime,'%Y-%m-%d') AS loaddate, exporttime, LEFT(DATE_FORMAT(exporttime,'%Y'),1) AS groupdate FROM records WHERE exporttime>'".date('Ymd',time()-86400*28)."' OR isnull(exporttime) GROUP BY serviceid, servicesub, exportset, calldate, loaddate ORDER BY groupdate, serviceid, servicesub, exportset, calldate, loaddate, exporttime ASC";
			//$query="SELECT serviceid, servicesub, exportset, COUNT(recorduri) AS recordsum, DATE_FORMAT(calltime,'%Y-%m-%d') AS calldate, DATE_FORMAT(loadtime,'%Y-%m-%d') AS loaddate, exporttime, LEFT(DATE_FORMAT(exporttime,'%Y'),1) AS groupdate FROM records WHERE exporttime>'".date('Ymd',time()-86400*7)."' GROUP BY serviceid, servicesub, exportset, calldate, loaddate ORDER BY groupdate, serviceid, servicesub, exportset, calldate, loaddate ASC";
			$query="SELECT serviceid, servicesub, COUNT(recorduri) AS recordsum, DATE_FORMAT(exporttime,'%Y-%m-%d') AS exporttime FROM loader07.records WHERE exporttime>'".date('Ymd',time()-86400*32)."' and ((not loadempty) or (loadempty is null)) and ((not loadexclude) or (loadexclude is null)) GROUP BY exporttime DESC, serviceid, servicesub";
			//echo $query;
			$result=mysql_query($query);
			//echo mysql_error();
			$num=mysql_num_rows($result);

			function GetAName($serviceid, $servicesub) {
				global $NameList;
				$NameCode=$serviceid."/".$servicesub;
				$RetName=isset($NameList{$NameCode})?$NameList{$NameCode}:'';
				//if (empty($RetName)) {
					//$query="SELECT servicename FROM services WHERE service='".$serviceid."' and servsub='".$servicesub."' and servicestart<=now() and serviceend>=now()";
					//echo $query;
					//$longresult=mysql_query($query);
					//if (mysql_numrows($longresult)>0) {
						//$RetName=mysql_result($longresult,0,'servicename');
						$NameList{$NameCode}=$RetName;
					//}
				//}
				return (empty($RetName)?'{'.$serviceid.'/'.$servicesub.'}':$RetName);
				//return (empty($RetName)?'{'.$serviceid.'/'.$servicesub.'}':$RetName);
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
					//if (empty($loaddate)==$groupon) {
						echo "<tr>";
						$serviceid=mysql_result($result,$i,'serviceid');
						$servicesub=mysql_result($result,$i,'servicesub');
						$recordsum=mysql_result($result,$i,'recordsum');
						$exporttime=mysql_result($result,$i,'exporttime');
						//$groupdate=mysql_result($result,$i,'groupdate');

						$LongName=GetAName($serviceid,$servicesub);
						/*$NameList{$serviceid."/".$servicesub};
						if (empty($LongName)) {
							$query="SELECT servicename FROM services WHERE service='".$serviceid."' and servsub='".$servicesub."' and servicestart<=now() and serviceend>=now()";
							//echo $query;
							$longresult=mysql_query($query);
							if (mysql_numrows($longresult)>0) {
								$LongName=mysql_result($longresult,0,'servicename');
								$NameList{$serviceid."/".$servicesub}=$LongName;
							}
						}*/
						//echo "<td>".$strongon.$LongName.$strongoff."</td><td>".$strongon.$exportset.$strongoff."</td><td>".$strongon.$recordsum.$strongoff."</td><td>".$strongon.$calldate.$strongoff."</td><td>".$loaddate."</td><td>".$exporttime."</td>\n";
						echo "<td>".$exporttime."</td><td>".$recordsum."</td><td>".$LongName."</td>\n";
						echo "</tr>";
					//}
					$i++;
				}
				//return strpos($banner_agent['agentgrp'],$grouptest)!==FALSE;
			}


			//TOO MUCH WORK TO DO!!!!!
			echo "<h3>Exported (Last Seven Days)</h3>\n";
			echo "<table width=100% border=0>\n";
			echo "<th>Exported</th><th>Exported</th><th>Client/Service</th>\n";
			//show_load(true);
			show_load(false);
			echo "</table>\n";

			mysql_close();

			//$num=mysql_numrows($result);
		?>
	</BODY>
</HTML>
