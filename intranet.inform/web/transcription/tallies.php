<HTML>
	<head>
		<title>Transcriptions</title>
<style type="text/css">
td.cellpad { 
	padding-left:5px;
	padding-right:5px;
}
table.times {
	border:1px solid red;
}
</style>
	</head>
	<BODY style="font-family: Verdana, Arial, Helvetica, sans-serif; font-size:12pt;">
		<?
			$midnight=date('Y-m-d', time()-(date('N')=='1'?259200:86400));
			$today=date('Y-m-d', time());
			$username="root";
			$password="password";

			$database="loader07";
			mysql_connect("192.168.1.209",$username,$password);
			@mysql_select_db($database) or die( "Unable to select database");

			$query="SELECT messageservice,messagetime FROM system.messages WHERE messageservice='Export08' ORDER BY messagetime";
			$result=mysql_query($query);
			if (mysql_numrows($result)>0) echo "<p style='color:red;'>There Are Export Errors:".mysql_result($result,0,'messagetime')."</p>";

			$query="SELECT serviceid, records.exportset, COUNT(recorduri) AS recordsum, DATE_FORMAT(calltime,'%Y-%m-%d') AS calldate, MIN(calltime) AS callfirst, MAX(calltime) AS calllast, loadclass, loadurgent, loadinto, exportdesc FROM records LEFT JOIN exports ON (exports.service=serviceid AND exports.exportset=records.exportset AND (NOW() BETWEEN exportfrom AND exportuntil)) WHERE loadtime IS NULL GROUP BY serviceid, loadinto, calldate, loadurgent, loadclass ORDER BY callfirst, serviceid, loadinto, calldate ASC";

			//echo $query;
			$result=mysql_query($query);
			//echo mysql_error();
			$num=mysql_numrows($result);
			if ($num>0) {
				$i=0;
				echo "<h3>Not Yet Loaded</h3>\n";
				echo "<table width=100% border=0>\n";
				echo "<th width='10%'>Earliest</th><th width='10%'>Latest</th><th width='5%'>Count</th><th>Client/Service</th>\n";

				while ($i < $num) {
					echo "<tr>";
					$serviceid=mysql_result($result,$i,'serviceid');
					//$servicesub=mysql_result($result,$i,'servicesub');
					$exportset=mysql_result($result,$i,'exportset');
					$recordsum=mysql_result($result,$i,'recordsum');
					$calldate=mysql_result($result,$i,'calldate');
					$callfirst=mysql_result($result,$i,'callfirst');
					$calllast=mysql_result($result,$i,'calllast');
					$LoadClass=mysql_result($result,$i,'loadclass');
					$loadurgent=mysql_result($result,$i,'loadurgent');
					//$loadtest=mysql_result($result,$i,'loadtest');
					$loadinto=mysql_result($result,$i,'loadinto');
					if (($exportdesc=mysql_result($result,$i,'exportdesc'))=='') {
						$exportdesc="{{$serviceid}/{$exportset}}";
					}
					$LongName="{$serviceid}/{$loadinto}";

					if ($loadurgent=='1') {
						$strongon='&nbsp;<strong><font color=red>';
						$strongoff='</font></strong>&nbsp;';
					}
					else {
						switch ($LoadClass) {
							case 'T':
								$strongon="&nbsp;<strong><font color=blue>";
								$strongoff="</font></strong>&nbsp;";
								break;
							case 'I':
								$strongon="&nbsp;<strong><font color='#00C000'>";
								$strongoff="</font></strong>&nbsp;";
								break;
							case 'C':
								$strongon="&nbsp;<strong><font color=purple>";
								$strongoff="</font></strong>&nbsp;";
								break;
							default:
								$strongon='&nbsp;<strong><font color="#008000">';
								$strongoff='</font></strong>&nbsp;';
						}
					}
					echo "<td align=right>{$strongon}".substr($callfirst,8,8)."{$strongoff}-</td><td>{$strongon}".substr($calllast,11,5).$strongoff."</td><td align=right>{$strongon}{$recordsum}{$strongoff}</td><td>{$strongon}{$LongName}{$strongoff}</td><td>{$strongon}{$exportdesc}{$strongoff}</td>\n";
					echo "</tr>";
					$i++;
				}
				echo "</table>\n";
			}

			//$query="SELECT serviceid, servicesub, exportset, loadclass, COUNT(recorduri) AS recordsum, DATE_FORMAT(calltime,'%Y-%m-%d') AS calldate, DATE_FORMAT(loadtime,'%Y-%m-%d') AS loaddate, loadinto FROM records WHERE senttime IS NULL AND loadtime IS NOT NULL GROUP BY serviceid, exportset, calldate, loaddate, loadclass ORDER BY serviceid, exportset, calldate, loaddate ASC";
			$query="SELECT records.serviceid, records.servicesub, records.exportset, records.loadclass, COUNT(records.recorduri) AS recordsum, DATE_FORMAT(records.calltime,'%Y-%m-%d') AS calldate, DATE_FORMAT(records.loadtime,'%Y-%m-%d') AS loaddate, records.loadinto, exports.exporttimes FROM records INNER join exports on records.serviceid = exports.service AND records.exportset = exports.exportset WHERE senttime IS NULL AND loadtime IS NOT NULL GROUP BY serviceid, exportset, calldate, loaddate, loadclass ORDER BY serviceid, exportset, calldate, loaddate ASC";
			//echo $query;
			$result=mysql_query($query);
			//echo mysql_error();
			$num=mysql_numrows($result);
			if ($num>0) {
				$i=0;
				echo "<h3>Not Yet Sent</h3>\n";
				echo "<table width=70% border=0>\n";
				echo "<tr style='background-color:#585858;color:white;'>";
				echo "<th style='text-align:left;'>&nbsp;&nbsp;Client/Service</th><th style='text-align:right;'>serviceid&nbsp;&nbsp;</th><th style='text-align:right;'>exportset&nbsp;&nbsp;</th><th style='text-align:right;'>Count&nbsp;&nbsp;</th><th style='text-align:right;'>exporttimes&nbsp;&nbsp;</th><th style='text-align:right;'>Call&nbsp;&nbsp;</th><th style='text-align:right;'>Loaded&nbsp;&nbsp;</th>\n";
				echo "</tr>";

				$zigzag="zig";
				while ($i < $num) {
					$loaddate=mysql_result($result,$i,'loaddate');

						if($zigzag==="zig") {
							echo "<tr style='background-color:white'>";
							$zigzag="zag";
						} else {
							echo "<tr style='background-color:#f2f2f2'>";
							$zigzag="zig";
						}
						$serviceid=mysql_result($result,$i,'serviceid');
						$servicesub=mysql_result($result,$i,'servicesub');
						$exportset=mysql_result($result,$i,'exportset');
						$recordsum=mysql_result($result,$i,'recordsum');
						$calldate=mysql_result($result,$i,'calldate');
						//$loadtest=mysql_result($result,$i,'loadtest');
						$LoadClass=mysql_result($result,$i,'loadclass');
						$LongName=GetAName($serviceid, $exportset, $servicesub);
						$exporttimes=mysql_result($result,$i,'exporttimes');

						if ((($loaddate<$midnight) or (strpos($LongName, '}'))) and ($LoadClass!='I')) {
							$strongon='<strong><font_color=red>';
							$strongoff='</font></strong>&nbsp;';
						}
						else {
							switch ($LoadClass) {
								case 'T':
									$strongon="&nbsp;<strong><font_color=blue>";
									$strongoff='</font></strong>&nbsp;';
									break;
								case 'I':
									$strongon="&nbsp;<font_color=green>";
									$strongoff='</font>&nbsp;';
									break;
								case 'C':
									$strongon="&nbsp;<strong><font_color=purple>";
									$strongoff='</font></strong>&nbsp;';
									break;
								default:
									$strongon='&nbsp;';
									$strongoff='&nbsp;';
							}
						}
						echo str_replace('_', ' ', str_replace(' ', '&nbsp;', "<td>{$strongon}{$LongName}{$strongoff}</td><td_align=right>{$strongon}{$serviceid}{$strongoff}</td><td_align=right>{$strongon}{$exportset}{$strongoff}</td><td_align=right>{$strongon}{$recordsum}{$strongoff}</td><td_align=right>{$strongon}{$exporttimes}{$strongoff}</td>"));

						$strongontemp	=	$strongon;
						$strongofftemp	=	$strongoff;

						// calldate
						if($calldate < $today) {
							$strongontemp="&nbsp;<span_style='background-color:yellow;'>";
							$strongofftemp='</span>&nbsp;';
							//$strongontemp="&nbsp;<font_color=red>";
							//$strongofftemp='</font>&nbsp;';
						}
						echo str_replace('_', ' ', str_replace(' ', '&nbsp;', "<td_align=right>{$strongontemp}{$calldate}{$strongofftemp}</td>\n"));
						//echo "<td_align=right>{$strongontemp}{$calldate}{$strongofftemp}</td>\n";

						// loaddate
						if($loaddate < $today) {
							$strongon="&nbsp;<span_style='background-color:yellow;'>";
							$strongoff='</span>&nbsp;';
						}
						echo str_replace('_', ' ', str_replace(' ', '&nbsp;', "<td_align=right>{$strongon}{$loaddate}{$strongoff}</td>\n"));
						echo '</tr>';
					//}
					$i++;
				}
				echo "</table>\n";
			}

			// Table for export times
			echo "&nbsp;\n";
			echo "<table width=70%>";
			echo "	<tr style='background-color:#585858;color:white;text-align:center;'>";
			echo "		<td colspan=12>Export Times</td>";
			echo "	</tr>";
			echo "	<tr style='text-align:center;background-color:#f2f2f2;'>";
			echo "		<td>H</td><td>I</td><td>J</td><td>K</td><td>L</td><td>M</td><td>N</td><td>O</td><td>P</td><td>Q</td><td>R</td><td>S</td>";
			echo "	</tr>";
			echo "	<tr style='text-align:center;'>";
			echo "		<td class='cellpad'>07:00</td><td class='cellpad'>08:00</td><td class='cellpad'>09:00</td><td class='cellpad'>10:00</td><td class='cellpad'>11:00</td><td class='cellpad'>12:00</td><td class='cellpad'>13:00</td><td class='cellpad'>14:00</td><td class='cellpad'>15:00</td><td class='cellpad'>16:00</td><td class='cellpad'>17:00</td><td class='cellpad'>18:00</td>";
			echo "	<tr style='text-align:center;background-color:#f2f2f2;'>";
			echo "		<td>h</td><td>i</td><td>j</td><td>k</td><td>l</td><td>m</td><td>n</td><td>o</td><td>p</td><td>q</td><td>r</td><td>s</td>";
			echo "	</tr>";
			echo "	<tr style='text-align:center;'>";
			echo "		<td>07:30</td><td>08:30</td><td>09:30</td><td>10:30</td><td>11:30</td><td>12:30</td><td>13:30</td><td>14:30</td><td>15:30</td><td>16:30</td><td>17:30</td><td>16:30</td>";
			echo "	</tr>";
			echo "</table>";
			echo "&nbsp;\n";

			function GetAName($serviceid, $exportset, $servicesub) {
				global $NameList;
				$NameCode=$serviceid."/".$exportset;
				$RetName=isset($NameList{$NameCode})?$NameList{$NameCode}:'';
				if (empty($RetName)) {
					$query="SELECT exportdesc,exporttimes FROM exports WHERE service='{$serviceid}' and exportset in ('{$exportset}', '@split{$exportset}') and exportfrom<=now() and exportuntil>=now() and exportdesc>'' and exporttimes<>'@'";
					//echo $query;
					$longresult=mysql_query($query);
					if (mysql_numrows($longresult)>0) {
						$RetSingle=(strpos(mysql_result($longresult,0,'exporttimes'), 'KkLlMmNnOoPpQ')!==FALSE);
						$RetName=mysql_result($longresult,0,'exportdesc');
						$NameList{$NameCode}=($RetSingle)?("<b>{$RetName}</b>"):($RetName);
					}
					else {
						$NameList{$NameCode}="{{$NameCode}}";
					}
				}
				return ($NameList{$NameCode});
			}

			mysql_close();

		?>
	</BODY>
</HTML>
