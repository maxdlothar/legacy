<?

//require_once "agent_info.php";

$banner_agent=function_exists('agent_inform')?agent_inform():agent_info();

echo "<table width=100% bgcolor=black>";
echo "<tr>";
echo "<td align=right>";
echo "<font face=arial color=white size=-1><b>{$banner_agent['agentname']}</b></font>";
echo "</td>";
echo "</tr>";
echo "</table>";

?>
