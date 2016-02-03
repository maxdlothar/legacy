<?

function generate_uri($timeuse=NULL) {  // v1.00
	if (empty($timeuse)) $timeuse=microtime();

	list($usec, $sec)=explode(" ", $timeuse);
	$time=base_convert($sec, 10, 36);
	$utime=sprintf("%04s", base_convert(intval($usec * 10000), 10, 36));
	return strtoupper($time . $utime);
}

function time_long($timeuse=NULL) {  // v1.00
	if (empty($timeuse)) $timeuse=microtime();
	list($usec, $sec)=explode(" ", $timeuse);
	return (date("YmdHis", $sec));
}
?>
