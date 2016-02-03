<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.1//EN" "http://www.w3.org/TR/xhtml11/DTD/xhtml11.dtd">
<html xmlns="http://www.w3.org/1999/xhtml" xml:lang="en" >
<head>
  <title><?if (!empty($page_title)) print "$page_title-"?>Inform Communications Plc</title>
  <link href="/css/cleanbase.css" rel="stylesheet" type="text/css" />
  <!--link href="https://secure.latestinfo.co.uk/shared/corporate/css/base.css" rel="stylesheet" type="text/css" /-->
  <link href="/clients/css/files_list.css" rel="stylesheet" type="text/css" />
  <link href="/shared/corporate/css/print.css" media="print" rel="stylesheet" type="text/css" />
  <link href="/shared/corporate/css/style.css" rel="stylesheet" type="text/css" />
<?
if (!empty($page_style) && is_array($page_style)) {
  foreach($page_style as $style) {
  print "  <link href=\"$style\" rel=\"stylesheet\" type=\"text/css\" />\n";
  }
}
if (!empty($page_js) && is_array($page_js)) {
  foreach($page_js as $js) {
    print "  <script src=\"$js\" type=\"text/javascript\"></script>\n";
  }
}
?>
</head>
<body>

  <h1><span></span>Inform Communications Plc</h1>
<?/*<img src="https://secure.latestinfo.co.uk/shared/corporate/images/inform_banner_1024x60_faded.png" width="1024" height="60" alt="Inform Communications Plc" /></h1>*/?>

<?
$baseserver='192.168.1.106';
//$baseserver='192.168.1.209';
include("agent_info.php");
$banner_agent=agent_info();
?>
<div id="navtop">
   <ul class="breadcrumbs">
      <li class="first_crumb"><a href="http://intranet.inform/">Intranet Home</a><div style="float:right;"><? echo $banner_agent['agentname'];?></div></li>
   </ul>
</div>

  <div id="content">

<!-- AGENT GROUPS
<? echo $banner_agent['agentgrp']; ?>
-->
