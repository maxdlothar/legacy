<?
			//$midnight=date('Y-m-d', time()-(date('N')=='1'?259200:86400));
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

			//$query="SELECT serviceid, servicesub, COUNT(recorduri) AS recordsum, DATE_FORMAT(calltime,'%Y-%m-%d') AS calldate, DATE_FORMAT(loadtime,'%Y-%m-%d') AS loaddate, exporttime FROM records WHERE exporttime>'".date('Ymd',time()-86400*2)."' GROUP BY exporttime DESC, serviceid, servicesub, exportset, calldate, loaddate";
			//THIS ONE! $query="SELECT serviceid, servicesub, COUNT(recorduri) AS recordsum, DATE_FORMAT(exporttime,'%d') AS exportday, DATE_FORMAT(exporttime,'%H') AS exporthour FROM records WHERE exporttime>'".date('Ymd',time()-86400*2)."' GROUP BY exportday desc,serviceid, servicesub, exporthour";
			$LoadStart="{$_REQUEST['loadstart']}000000";
			$LoadEnd="{$_REQUEST['loadend']}235959";

echo <<<HEAD
<HTML>
<head>
<title>Agent Loading {$LoadStart}-{$LoadEnd}</title>
</head>
<BODY>
HEAD;

			//$LoadStart=date('Ymd',time()-86400*15);
			//$LoadEnd=date('Ymd',time()-86400*1);
			$query="SELECT COUNT(DISTINCT DATE_FORMAT(loadtime,'%Y-%m-%d')) AS loaddays, COUNT(DISTINCT DATE_FORMAT(loadtime,'%Y-%m-%d %H')) AS loadhours, loadagent FROM records WHERE loadtime between '{$LoadStart}' AND '{$LoadEnd}' GROUP BY loadagent";
			//echo $query;
			$result=mysql_query($query);
			//echo mysql_error();
			$num=mysql_numrows($result);

			$i=0;
			while ($i < $num) {
				$agentlist{mysql_result($result,$i,'loadagent')}=mysql_result($result,$i,'loaddays');
				$agenthours{mysql_result($result,$i,'loadagent')}=mysql_result($result,$i,'loadhours');
				$i++;
			}
			ksort($agentlist);

			$query="SELECT serviceid, servicesub, COUNT(recorduri) AS recordsum, loadagent, (((not loadempty) or (loadempty is null)) and ((not loadexclude) or (loadexclude is null))) AS loaded FROM records WHERE loadtime between '{$LoadStart}' AND '{$LoadEnd}' GROUP BY loadagent, serviceid, servicesub, loaded";
			//echo $query;
			$result=mysql_query($query);
			//echo mysql_error();
			$num=mysql_numrows($result);
			//TOO MUCH WORK TO DO!!!!!
			//echo "<h3>Exported (Last Seven Days)</h3>\n";
			//echo "<table border=1>\n";
			//echo "<th>Day</th><th>Client/Service</th><th>Time</th><th>Count</th>\n";
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
						$loaded=mysql_result($result,$i,'loaded');
						$loadagent=mysql_result($result,$i,'loadagent');

						$NameCode=$serviceid."/".$servicesub;//."/".$loaded;
						$LongName=isset($NameList{$NameCode})?$NameList{$NameCode}:'';
						if (empty($LongName)) {
							//$query="SELECT servicename FROM services WHERE service='".$serviceid."' and servsub='".$servicesub."' and servicestart<=now() and serviceend>=now()";
							//echo $query;
							//$longresult=mysql_query($query);
							//if (mysql_numrows($longresult)>0) {
								//$LongName=mysql_result($longresult,0,'servicename');
								$LongName="{$serviceid} ({$servicesub})";
								$NameList{$NameCode}=$LongName;
							//}
						}
						$rowlist{$LongName}='';
						if ($loaded=='1') {
							$dataload{$LongName}{$loadagent}=$recordsum;
						}
						else {
							$dataempty{$LongName}{$loadagent}=$recordsum;
						}
//echo "$NameCode-$recordsum<br>\n";
						//echo "<td>".$strongon.$LongName.$strongoff."</td><td>".$strongon.$exportset.$strongoff."</td><td>".$strongon.$recordsum.$strongoff."</td><td>".$strongon.$calldate.$strongoff."</td><td>".$loaddate."</td><td>".$exporttime."</td>\n";
						//echo "<td>".$ExportDay."</td><td>".$LongName."</td><td>".$ExportHour."</td><td>".$recordsum."</td>\n";
						//echo "</tr>";
					//}
					$i++;
				}
			//echo "</table>\n";
			echo "<table border=1 width=99% cellpadding=2 cellspacing=0 RULES=GROUPS FRAME=BOX>\n";
			echo '<colgroup style="background-color:#FFFDFD"></colgroup>';
			for ($x=0;++$x<10;) {
				echo '<colgroup span=2 style="background-color:#FDFFFF;"></colgroup>';
				echo '<colgroup span=2 style="background-color:#FFFFFD"></colgroup>';
			}
			//echo "<tr>";
			//echo '<colgroup></colgroup>';
			//echo "</tr>";

			//echo "<tr>";
			//echo '<col style="background-color:red"></colgroup>';
			//echo "</tr>";
//<COLGROUP SPAN=3></COLGROUP>
			echo "<thead>";
			echo "<tr>";
			echo "<th width=1>Service</th>\n";
			foreach ($agentlist as $agent => $ignore) {
					echo "<th colspan=2>{$agent}</th>\n";
					$agentload{$agent}=0;
					$agentempty{$agent}=0;
			}
			echo "<th colspan=2>TOTAL</th>\n";
			echo "</tr>";
			ksort($rowlist);
			echo "</thead>";

			echo "<tbody>";

				global $rowload, $rowempty;
			//foreach ($formlab as $key => $value) {
				//foreach ($rowlist as $row) {
				foreach ($rowlist as $row => $ignore) {
						$rowload=0;
						$rowempty=0;
						echo "<tr>";
						echo "<td>".str_replace(' ','&nbsp;',$row)."</td>\n";
						foreach ($agentlist as $agent => $ignore) {
						//foreach ($agentlist as $agent) {
								$didload=isset($dataload{$row}{$agent})?($dataload{$row}{$agent}):0;
								$didempty=isset($dataempty{$row}{$agent})?($dataempty{$row}{$agent}):0;
								$agentload{$agent}+=$didload;
								$agentempty{$agent}+=$didempty;
								putcell($didload, $didempty);
						}
						putcell($rowload, $rowempty);
						echo "</tr>";
				}
				echo "</tbody>";

				echo "<tbody>";
				$rowload=0;
				$rowempty=0;
				echo "<tr>";
				echo "<td>TOTAL</td>\n";
				foreach ($agentlist as $agent => $ignore) {
				//foreach ($agentlist as $agent) {
						putcell($agentload{$agent}, $agentempty{$agent});
				}
				putcell($rowload, $rowempty);
				echo "</tr>";
				echo "</tbody>";

				echo "<tbody>";
				$rowload=0;
				$rowempty=0;
				echo "<tr>";
				echo "<td>DAYS</td>\n";
				foreach ($agentlist as $agent => $loaddays) {
				//foreach ($agentlist as $agent) {
						putcell($loaddays, 0);
				}
				putcell($rowload, $rowempty);
				echo "</tr>";

				$rowload=0;
				$rowempty=0;
				echo "<tr>";
				echo "<td>AVERAGE</td>\n";
				foreach ($agentlist as $agent => $loaddays) {
				//foreach ($agentlist as $agent) {
						putcell(round($agentload{$agent}/$loaddays,0), round($agentempty{$agent}/$loaddays),0);
				}
				putcell($rowload, $rowempty);
				echo "</tr>";
				echo "</tbody>";

				echo "<tbody>";
				$rowload=0;
				$rowempty=0;
				echo "<tr>";
				echo "<td>HOURS</td>\n";
				foreach ($agenthours as $agent => $loadhours) {
				//foreach ($agentlist as $agent) {
						putcell($loadhours, 0);
				}
				putcell($rowload, $rowempty);
				echo "</tr>";
				//var_dump($rowlist);
				//return strpos($banner_agent['agentgrp'],$grouptest)!==FALSE;

				$rowload=0;
				$rowempty=0;
				echo "<tr>";
				echo "<td>AVERAGE</td>\n";
				foreach ($agentlist as $agent => $loaddays) {
				//foreach ($agentlist as $agent) {
						$loadtime=$agenthours{$agent};
//echo "<td>moop</td>";
						putcell(round($agentload{$agent}/$loadtime,0), round($agentempty{$agent}/$loadtime),0);
				}
				putcell($rowload, $rowempty);
				echo "</tr>";
				echo "</tbody>";

			echo "</table>\n";

			mysql_close();

			//$num=mysql_numrows($result);
			function putcell($loaded, $empty) {
				global $rowload, $rowempty;
				$rowload+=$loaded;
				$rowempty+=$empty;
				$percentage=($empty==0)?'':('&nbsp;('.floor(100*$empty/($loaded+$empty)).'%)');
				echo '<td align=right>'.(($loaded>0)?"<b>{$loaded}</b>":'&nbsp;').(($empty>0)?"</td><td><small><small>+{$empty}{$percentage}</small></small>":'</td><td>')."</td>\n";
			}
		?>
	</BODY>
</HTML>
