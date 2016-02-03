<?
include('/usr/local/apache/htdocs/secure.informcommunications.plc.uk/shared/corporate/header.php');
?>
  <h2>Available options</h2>
<?
if (file_exists('exports') && is_dir('exports')) {
?>
  <ul>
    <li><a href="exports/">Your exports</a></li>
  </ul>
<?
include('/usr/local/apache/htdocs/secure.informcommunications.plc.uk/shared/corporate/footer.php');
}
?>
