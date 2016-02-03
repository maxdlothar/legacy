<?
define('DATE_BBC', 'l, j F Y, G:i T');
define('DATE_SHORT', 'D, j M Y, G:i T');
include('includes/connect_idb.php');
include('includes/header.php');
$page_title='ODIS Designer';
$page_style[]='/css/base.css';

// Define the start and end times for the date range dropdown boxes
// Each day will be set to 00:00:00 -> 23:59:59 below
$date_start=strtotime('today, 04:00:00');
//$date_end=strtotime('3 months ago, 04:00:00');
$date_end=strtotime('18 months ago, 04:00:00');
?>
  <h3>Export Stats</h3>
  <form name="form" action="redreport.php" method="post">
    <fieldset>
      <legend>Date range</legend>
      <p>
        <label for="start">Start</label>
        <select name="start" id="start">
          <option value="">- Select -</option>
<?for ($time=$date_start; $time>=$date_end; $time-=86400) {?>
          <option value="<?=strtotime(date('Y-m-d 00:00:00', $time))?>"><?=date('D, j F Y', $time)?></option>
<?}?>
        </select>
        <label for="end">End</label>
        <select name="end" id="end">
          <option value="">- Select -</option>
<?for ($time=$date_start; $time>=$date_end; $time-=86400) {?>
          <option value="<?=strtotime(date('Y-m-d 23:59:59', $time))?>"<?if ($time==$date_start) print ' selected="selected"'?>><?=date('D, j F Y', $time)?></option>
<?}?>
        </select>
      </p>
    </fieldset>
    <fieldset class="buttons">
      <legend>Generate</legend>
      <input type="submit" value="Generate report">
    </fieldset>
  </form>

