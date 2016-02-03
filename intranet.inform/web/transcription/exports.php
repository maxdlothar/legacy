<HTML>
	<head>
		<title>Transcriptions</title>
	</head>
	<BODY>
		<?
			$midnight=date('Y-m-d', time()-(date('N')=='1'?259200:86400));
			$username="root";
			$password="password";

			$database="loader07";
			mysql_connect("192.168.1.209",$username,$password);
			@mysql_select_db($database) or die( "Unable to select database");

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

				$i=0;
				while ($i < $num) {
					$loaddate=mysql_result($result,$i,'loaddate');
					echo "<tr>";
					$serviceid=mysql_result($result,$i,'serviceid');
					$servicesub=mysql_result($result,$i,'servicesub');
					$exportset=mysql_result($result,$i,'exportset');
					$recordsum=mysql_result($result,$i,'recordsum');
					$calldate=mysql_result($result,$i,'calldate');
					$exporttime=mysql_result($result,$i,'exporttime');

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
					echo "<td>{$LongName}</td><td>{$NameCode}</td><td>{$recordsum}</td><td>{$calldate}</td><td>{$loaddate}</td><td><a href='exportcontent.php?exporttime={$exporttime}'>{$exporttime}</td>\n";
					echo "</tr>";
					$i++;
				}
			}

			//TOO MUCH WORK TO DO!!!!!
			echo "<h3>Exported (Last Seven Days)</h3>\n";
			echo "<table width=100% border=0>\n";
			echo "<th>Client/Service</th><th>Export</th><th>Count</th><th>Call</th><th>Loaded</th><th>Exported</th>\n";
			//show_load(true);
			show_load(false);
			echo "</table>\n";

			mysql_close();

		?>
	</BODY>
</HTML>
