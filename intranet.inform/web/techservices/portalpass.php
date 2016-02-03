<?php
$page_title=$page_p='Inform Communications Intranet';
require_once '../../../includes/password_gen/password_gen.php';
include '../includes/connect_idb.php';
include '../includes/header.php';
require_once "./techshare.php";
//require_once "../includes/agent_info.php";
//require_once "../includes/agent_banner.php";

$agentdet=agent_info();
$agentinit=$agentdet{'agentinit'};
$agentlogin=$agentdet{'agentlogin'};
$agentalias=$agentdet{'loginalias'};

//mysql_connect(localhost,$username,$password);
//@mysql_select_db('system') or die( "Unable to select database");
//$db_con
#!/usr/bin/php
//echo "HI<br>\n";
$limitclause=(agent_ingroup('S'))?'':" AND agentinit='{$agentinit}'";
$query="SELECT * FROM agent WHERE (LENGTH(agentlogin)>5 OR LENGTH(loginalias)>5) AND NOW() BETWEEN agentfrom AND agentto {$limitclause} ORDER BY agentname";
//echo $query;
$result=mysqli_query($db_con, $query);
$num=mysqli_num_rows($result);

$lastservice=$lasttitle='';
echo "<table><tr><th>Name</th><th>Login</th><th>Password</th><th>2014 Password</th></tr>";
for ($i=0; $i<$num; ++$i) {
	$row=mysqli_fetch_array($result);
	if (strlen($passname=$row{'agentlogin'})<6) $passname=$row{'loginalias'};
/*	if ($lastservice!=$service) {
		$lastservice=$service;
	//echo '<span class="newshead">'.str_replace("_", "&nbsp;", date('l, j_F_Y', strtotime($newsdate))).'</span>';
		echo "<p><p>{$service}</p></p>";
	}
	if ($lasttitle!=$messagetitle) {
		$lasttitle=$messagetitle;
	//echo '<span class="newshead">'.str_replace("_", "&nbsp;", date('l, j_F_Y', strtotime($newsdate))).'</span>';
		echo "<p><h3>{$messagetitle}</h3></p>";
	}
*/
	$thisname=$row{'agentname'};
	echo "<tr><td>{$thisname}</td><td>{$passname}</td><td>".password_gen($passname,'2014')."</td><td>".password_gen($passname,'2014')."</td></tr>";
	//echo "<tr><td>{$thisname}</td><td>{$passname}</td><td>".password_gen($passname,date('Y'))."</td><td>".password_gen($passname,'2014')."</td></tr>";
}
//echo "$agentlogin";
if (array_search($agentlogin, array('colinhemming', 'julianmead', 'bobcoppack', 'donnawilliams'))!==FALSE)  {
	echo '<tr><td colspan=3><b>Client Logins</b></td></tr>';


	//this query will list all the services we currently have with customers

	$query="SELECT DISTINCT featureuser FROM portal_general.features WHERE LENGTH(featureuser)=5 AND NOW() BETWEEN featurestart AND featureend ORDER BY featureuser";
	//echo $query;
	$result=mysqli_query($db_con, $query);
	$num=mysqli_num_rows($result);
	for ($i=0; $i<$num; ++$i) {
		$row=mysqli_fetch_array($result);
		$ThisService=$row{'featureuser'};
		
		echo "<tr><td>{$ThisService}</td><td>{$ThisService}_{$agentlogin}</td><td>".password_gen("{$ThisService}_{$agentlogin}",'2014')."</td><td>".password_gen("{$ThisService}_{$agentlogin}",'2014')."</td></tr>";
		//echo "<tr><td>{$ThisService}</td><td>{$ThisService}_{$agentlogin}</td><td>".password_gen("{$ThisService}_{$agentlogin}",date('Y'))."</td><td>".password_gen("{$ThisService}_{$agentlogin}",'2014')."</td></tr>";
	}

	//foreach (array('brien'=>'Bristol Environment', 'brirb'=>'Bristol Revben', 'fifrb'=>'Fife Revben') as $thiscli=>$thisfull) {
	//	echo "<tr><td>{$thisfull}</td><td>{$thiscli}_{$agentlogin}</td><td>".password_gen("{$thiscli}_{$agentlogin}",date('Y'))."</td><td>".password_gen("{$thiscli}_{$agentlogin}",'2014')."</td></tr>";
	//}
}
//if (array_search($agentalias, array('jam', 'chrisowen'))!==FALSE)  {
//	echo '<tr><td colspan=3><b>Temporary Client Logins</b></td></tr>';
//	foreach (array('brien'=>'Bristol Environment', 'brirb'=>'Bristol Revben', 'fifrb'=>'Fife Revben') as $thiscli=>$thisfull) {
//		echo "<tr><td>{$thisfull}</td><td>{$thiscli}_{$agentalias}</td><td>".password_gen("{$thiscli}_{$agentalias}",date('Y'))."</td><td>".password_gen("{$thiscli}_{$agentalias}",'2014')."</td></tr>";
//	}
//}

echo "</table>";
//mysql_close();

function passsay($id, $unique) {
		echo "{$id}=".password_gen($id, $unique, false)."<br>\n";
	}
?>
