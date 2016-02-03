<?
$username="web8_u1";
$password="database";
$database="web8_db1";

$mypage=($_SESSION['username']==$pageowner);
$notmypage=(($_SESSION['username']!=$pageowner) and (!empty($_SESSION['username'])));

mysql_connect(localhost,$username,$password);
@mysql_select_db($database) or die( "Unable to select database");

if ($mypage) {
	if ($_POST['action']=='additem') {
		$format=$_POST['format'];
		$artist=$_POST['artist'];
		$artistfree=$_POST['artistfree'];
		if (!empty($artistfree)) {
			$artist=$artistfree;
		}
		$item=$_POST['item'];
		if (!(empty($format) or empty($artist) or empty($item))) {
			//echo "Format:{$format}";
			//echo "Artist:{$artist}";
			//echo "Item:{$item}";
			$query = "INSERT INTO wishlist SET owner='{$pageowner}', format='{$format}', artist='{$artist}', item='{$item}';";
			//echo $query;
			mysql_query($query);
		}
	}
	if ($_POST['action']=='gotitem') {
		$gotdate=date('Y-m-d', strtotime($_POST['gotdate']));
		$thanks=$_POST['thanks'];
		$entryid=$_POST['entryid'];
		if (!(($gotdate<1) or empty($thanks) or empty($entryid))) {
			//echo 'Gotdate:'.$gotdate;
			//echo "<br>Thanks:{$thanks}";
			//echo "<br>EntryID:{$entryid}<br>";
			$query = "UPDATE wishlist SET gotdate='{$gotdate}', thanks='{$thanks}' WHERE entryid='{$entryid}' LIMIT 1;";
			//$query = "INSERT INTO contacts VALUES ('','$first','$last','$phone','$mobile','$fax','$email','$web')";
			//echo $query;
			mysql_query($query);
		}
	}
}

$query="SELECT * FROM wishlist WHERE owner='$pageowner' ORDER BY artist, item, format;"; /*WHERE now()<dateto";*/
$result=mysql_query($query);

$num=mysql_numrows($result);

/*if ($num>0) {
	echo "<b>Database Output</b><br>";*/

	if ($mypage) {
		$query="SELECT DISTINCT artist FROM wishlist WHERE owner='$pageowner'ORDER BY artist;"; /*WHERE now()<dateto";*/
		$artistresult=mysql_query($query);
		$artistnum=mysql_numrows($artistresult);

		$query="SELECT username, realname FROM logins WHERE username<>'{$pageowner}' ORDER BY realname;";
		$nameresult=mysql_query($query);
		$namenum=mysql_numrows($nameresult);
		echo "<form action='' method=post>";
	}

	mysql_close();

	echo "<TABLE>";	
	echo "<TR>";
	if ($mypage or $notmypage) {
		echo "<th></th>";
	}
	echo "	<th>Format</th>";
	echo "	<th>Artist</th>";
	echo "	<th>Item</th>";
	echo "	<th>Got</th>";
	echo "	<th>Thanks To</th>";
	echo "</TR>";

	$i=0;
	while ($i < $num) {
		$format=mysql_result($result,$i,"format");
		$artist=mysql_result($result,$i,"artist");
		$item=mysql_result($result,$i,"item");
		$gotdate=mysql_result($result,$i,"gotdate");
		$thanks=mysql_result($result,$i,"thanks");
		echo "<TR>";
		if ($mypage or $notmypage) {
			echo '<td><input type=radio name="entryid" value="'.mysql_result($result,$i,'entryid').'" /></td>';
		}
		echo "	<td>$format</td>";
		echo "	<td>$artist</td>";
		echo "	<td>$item</td>";
		if ($gotdate>"1") {
			echo "	<td>$gotdate</td>";
		}
		else {
			echo "	<td></td>";
		}
		echo "	<td>$thanks</td>";
		echo "</TR>\n";
		$i++;
	}
	if ($mypage) {
		echo '<tr><td></td>';

	echo '	<td><select name="format">';
		echo '<option value ="cd">cd</option>';
		echo '<option value ="dvd">dvd</option>';
		echo '<option value ="pccd">pccd</option>';
	echo "	</select></td>";

	echo '	<td><select name="artist">';
		echo "<option value =''>-Select or Enter Below-</option>";
		$i=0;
		while ($i < $artistnum) {
			$thisartist=mysql_result($artistresult,$i,"artist");
			echo "<option value ='{$thisartist}'>{$thisartist}</option>";
			$i++;
		}
	echo "	</select>";
	echo '<br><INPUT type=text name="artistfree" value="" size=20 /></td>';

	echo '<td><INPUT type=text name="item" value="" size=20 /></td>';
	//if ($gotdate>"1") {
	//	echo "	<td>$gotdate</td>";
	//}
	//else {
	//	echo "	<td></td>";
	//}
	//echo "	<td>$thanks</td>";

	echo '<td><INPUT type=submit name="action" value="additem" /></td>';
	echo '</tr>';

	echo '<tr>';
	//echo '<td></td>';
	//echo '<td></td>';
	echo '<td></td>';
	echo '<td></td>';

	echo '<td><INPUT type=text name="gotdate" value="today" size=15 /></td>';

	echo '	<td><select name="thanks">';
		$i=0;
		while ($i < $namenum) {
			echo '<option value ="'.mysql_result($nameresult,$i,"username").'">'.mysql_result($nameresult,$i,"realname").'</option>';
			$i++;
		}
	echo "	</select></td>";

	echo '<td><INPUT type=submit name="action" value="gotitem" /></td>';
	echo '</tr>';
	echo "</table>";
	}
	if ($mypage or $notmypage) {
		echo "</form>";
	}

/*}
*/
?>
