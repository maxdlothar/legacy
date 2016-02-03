<?php
// 209 has loader07
define('DB_HOSTNAME', '192.168.1.209');
define('DB_USERNAME', 'root');
define('DB_PASSWORD', 'password');
define('DB_DATABASE', 'system');
define('DB_LOGFILE', 'error_sql.log');

$db_con=@mysqli_connect(DB_HOSTNAME, DB_USERNAME, DB_PASSWORD);
// If the connection failed..
if ($db_con==FALSE) {
   db_error('Sorry, couldn\'t connect to the SQL server.');
   exit(0);
} else {
   mysqli_select_db($db_con, DB_DATABASE);
}
?>
