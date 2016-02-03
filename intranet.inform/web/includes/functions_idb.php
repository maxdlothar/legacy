<?
/*
= Changes =
[20090311] v0.13 - added TRUNCATE keyword
[20080710] v0.12 - added db_select()
[20080709] v0.11 - added SET and transactional keywords START, COMMIT and ROLLBACK
[20080409] v0.10 - added db_update_assoc()
[20080401] v0.9 - added db_insert_assoc() and db_connect()
[20081202] v0.8 - added persistent option and ALTER to db_do()
[20071107] v0.7 - added connection test
[20070813] v0.6 - added db_escape()

= Functions =
db_connect(contest) - connect to a database (generally used internally, unless contest=TRUE,
                      in which case a connection test is performed: -1 = con error, -2 = select error, 0 = okay)
db_escape(str, persistent) - escape an SQL string
db_do(sql, persistent, verbosity) - perform an sql query
db_insert_assoc(table, assoc) - insert an associative array into a table
db_update_assoc(table, assoc, where) - update a table with an associative array
*/

function db_connect($contest=FALSE) {

  global $db_con;

  // Connect to the server if a connection hasn't already been established
  if (empty($db_con)) {

    // Make the connection
    $db_con=@mysqli_connect(DB_HOSTNAME, DB_USERNAME, DB_PASSWORD);

    // If the connection failed..
    if ($db_con==FALSE) {
      if ($contest) {
        return -1;
      } else {
        db_error('Sorry, couldn\'t connect to the SQL server.');
      }
    }

    // Check whether we need to connect to a specific database
    if (defined('DB_DATABASE')) {
      $db_sel=db_select(DB_DATABASE, $contest);
      if ($contest && !$db_sel) {
        return -2;
      }
/*
      if ($db_sel==FALSE) {
        if ($contest) {
          return -2;
        } else {
          db_error('Sorry, couldn\'t select the SQL database.');
        }
      }
*/
    }

  }

  return 0;

}

function db_select($database, $contest=FALSE) {

  global $db_con;

  // Check whether we need to connect to a specific database
  $db_sel=mysqli_select_db(DB_DATABASE, $db_con);
  if ($db_sel==FALSE) {
    if ($contest) {
      return FALSE;
    } else {
      db_error('Sorry, couldn\'t select the SQL database.');
    }
  }
  return TRUE;
}

function db_escape($str, $persistent=TRUE) {

  global $db_con;

  // Inlcude the database access details
  if (!defined('DB_HOSTNAME')) {
    require_once('db_access.php');
  }

  // Connect to the server if a connection hasn't already been established
  db_connect();

  // Escape the string
  $str=mysqli_real_escape_string($str);

  // Close the connection if necessary (often we're going to run a query stright after this function has been called, so it's best to leave it)
  if (!$persistent) {
    mysqli_close($db_con);
    $db_con=NULL;
  }

  return $str;

}


function db_do($sql, $persistent=FALSE, $verbosity=1) {

  global $db_con;

  // Inlcude the database access details
  if (!defined('DB_HOSTNAME')) {
    require_once('db_access.php');
  }

  // Connect to the server if a connection hasn't already been established
  db_connect();

  // Find out what the action is
  if (strpos($sql, ' ') > 0) {
    $action=strtoupper(substr($sql, 0, strpos($sql, ' ')));
  } else {
    $action=strtoupper($sql);
  }

  // Process the query based on the type of action
  $results=NULL;
  switch($action) {
    case 'SELECT':
    case 'DESCRIBE':
      $data=mysqli_query($db_con, $sql) or db_error("Sorry, but the $action query failed.");
      while($results[]=mysqli_fetch_assoc($data));
      array_pop($results);
      mysqli_free_result($data);
     break;
    case 'INSERT':
    case 'UPDATE':
    case 'DELETE':
    case 'ALTER':
    case 'TRUNCATE':
      mysqli_query('SET time_zone = "Europe/London"');
      mysqli_query($sql);
      $results=mysqli_affected_rows();
      if ($results < 0 && $verbosity > 0) {
        db_error("Sorry, but the $action query failed.");
      }
      break;
    case 'SET':
    case 'START':
    case 'COMMIT':
    case 'ROLLBACK':
      $results=mysqli_query($sql) or db_error("Sorry, but the $action query failed.");
      break;
    default:
      db_error("db_do(): Unknown SQL action '$action'");
      break;
  }

  // Close the connection if necessary
  if (!$persistent) {
    mysqli_close($db_con);
    $db_con=NULL;
  }

  // Return the results
  return $results;

}


function db_insert_assoc($table, $assoc, $persistent=FALSE) {

  if (empty($assoc) || !is_array($assoc)) {
    db_error('db_insert_assoc(): Invalid array passed');
    return -1;
  }

  $sql="INSERT INTO $table SET ";
  foreach($assoc as $key=>$value) {
    if (empty($value) && strlen($value)==0) {
      $sql.="$key=NULL, ";
    } elseif ($value=='NOW()') {
      $sql.="$key=$value, ";
    } else {
      $sql.="$key='" . db_escape($value) . "', ";
    }
  }
  $sql=rtrim($sql, ', ');

  return db_do($sql, $persistent);

}


function db_update_assoc($table, $assoc, $condition, $persistent=FALSE) {

  if (empty($assoc) || !is_array($assoc)) {
    db_error('db_insert_assoc(): Invalid array passed');
    return -1;
  } elseif (empty($condition)) {
    db_error('db_insert_assoc(): No update condition passed');
    return -1;
  }

  $sql="UPDATE $table SET ";
  foreach($assoc as $key=>$value) {
    if (empty($value) && strlen($value)==0) {
      $sql.="$key=NULL, ";
    } elseif ($value=='NOW()') {
      $sql.="$key=$value, ";
    } else {
      $sql.="$key='" . db_escape($value) . "', ";
    }
  }
  $sql=rtrim($sql, ', ') . " $condition";

  return db_do($sql, $persistent);

}


function db_error($msg, $fatal=FALSE) {
	global $db_con;

	$error=NULL;
	$mysqli_errno=mysqli_errno($db_con);
	$mysqli_error=mysqli_error($db_con);
	if (!empty($mysqli_errno)) {
		$error=" [$mysqli_errno] {$mysqli_error}";
	}
	// Log the error if required
	if (defined('DB_LOGFILE')) {
		@file_put_contents(DB_LOGFILE, "[" . date('r') . "] $msg$error\n", FILE_APPEND);
	}
	trigger_error($msg . $error, (!empty($fatal) && $fatal==TRUE) ? E_USER_ERROR : E_USER_WARNING);
}

?>
