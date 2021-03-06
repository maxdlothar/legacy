<?
$page_title='Export file list';
$page_style[]='https://secure.informcommunications.plc.uk/shared/corporate/css/clients_export_list.css';
include('/usr/local/apache/htdocs/secure.informcommunications.plc.uk/shared/corporate/header.php');
?>
  <h2>
    <span id="h2options">Current view: <?=empty($_GET['display']) ? '1 Month | <a href="?display=older">3 Months</a>' : '<a href=".">1 Month</a> | 3 Months'?></span>
    Export file list, generated <?=date('l, j F Y, H:i')?>
  </h2>
<?
function microtime_float() {
  list($usec, $sec)=explode(' ', microtime());
  return ((float)$usec + (float)$sec);
}

$start_time=microtime_float();

define('PATH', '.');  // Path to the files
define('MAXAGE', $_GET['display']=='older' ? strtotime('3 months ago') : strtotime('4 weeks ago'));  // Initial maximum age of files to display
define('FILESPEC', '.*\.txt|.*\.csv|.*\.html$');  // filespec is regular expression, so escape full stops and other such characters
define('CASESENSITIVE', false);  // Turn on/off filename case sensitivity

$filelist=scandir(PATH);
$regexp_modifiers=CASESENSITIVE ? NULL : 'i';

foreach($filelist as $filename) {
  if ($filename=='.' || $filename=='..') continue;
  $regexp="/" . FILESPEC . "/$regexp_modifiers";
  // Disregard files that don't match FILESPEC or don't have a valid date (YYYYMMDD) in the filename
  if ((preg_match($regexp, $filename) && preg_match('/(\d{4})(\d{2})(\d{2})/', $filename, $matches)) &&
      (checkdate($matches[2], $matches[3], $matches[1]) && $matches[1]<=date('Y')) &&
      (mktime(0, 0, 0, $matches[2], $matches[3], $matches[1])>MAXAGE)) {
    $files[$filename]['date_unix']=mktime(0, 0, 0, $matches[2], $matches[3], $matches[1]);
    $files[$filename]['date_nice']=date('l, j M Y', mktime(0, 0, 0, $matches[2], $matches[3], $matches[1]));
    $files[$filename]['size']=sprintf('%.2fK', filesize(PATH . "/$filename") / 1024);
    $files[$filename]['url']=PATH . "/$filename";

    // A bit strange, but used below by array_multisort()
    $file_list[$filename]=$filename;
    $file_dates_unix[$filename]=$files[$filename]['date_unix'];

  }
}

if (!is_array($files)) {?>
  <p>No files to display!</p>
<?if (empty($_GET['display'])) {?>
  <p>You may have <a href="?display=older">older files</a> available.</p>
<?}?>
</body>
</html>
<?
  exit(1);
}

array_multisort($file_dates_unix, SORT_DESC, SORT_NUMERIC, $file_list, SORT_ASC, SORT_STRING, $files);
$x=0;

?>
  <table id="file_list">
    <tr>
      <th class="heading_filename">Filename</th>
      <th>Date</th>
      <th>Size</th>
    </tr>
<?foreach($files as $filename=>$info) { $x++?>
    <tr class="<?=$x % 2 ? 'light' : 'dark'?>"><td><a href="<?=$info[url]?>" class="tick" title="Download file"><?=$filename?></a></td><td><?=$info[date_nice]?></td><td><?=$info[size]?></td></tr>
<?}?>
  </table>
<?/*
<?if (empty($_GET['display'])) {?>
  <p>Displaying files uploaded within the last <strong><?=round(((time()-MAXAGE)/(60*60*24)))?></strong> days. You can <a href="?display=older">view all files</a> from the past three months.</p>
<?} else{ ?>
  <p>Displaying files uploaded within the last <strong>three months</strong>, the oldest of which is <strong><?=round(((time()-$info['date_unix'])/(60*60*24)))?></strong> days old.</p>
*/?>
<?if (!empty($_GET['display'])) {?>
  <div class="data_protection_notice">
    <p>Please note that in accordance with data protection policies, files older than <strong><?=date('l, j F Y', strtotime('3 months ago'))?></strong> have not been retained.</p>
  </div>
</p>
<?}?>
  <p class="footer">
    <?printf("Located and sorted %d files in %.4f seconds", sizeof($files), microtime_float() - $start_time);?><br />
    Copyright &copy; 2006-<?=date('Y')?> Inform Communications Plc
  </p>
<?
include('/usr/local/apache/htdocs/secure.informcommunications.plc.uk/shared/corporate/footer.php');
?>
