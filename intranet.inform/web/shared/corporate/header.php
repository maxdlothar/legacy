<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.1//EN" "http://www.w3.org/TR/xhtml11/DTD/xhtml11.dtd">
<html xmlns="http://www.w3.org/1999/xhtml" xml:lang="en" >
<head>
  <title><?if (!empty($page_title)) print "$page_title - "?>Inform Communications Plc</title>
  <link href="https://secure.latestinfo.co.uk/shared/corporate/css/base.css" rel="stylesheet" type="text/css" />
  <link href="https://secure.latestinfo.co.uk/shared/corporate/css/print.css" media="print" rel="stylesheet" type="text/css" />
  <!--
  	Remote<?=strpos("x".$_SERVER['REMOTE_ADDR'],"192.168.1.")?>
  	Internal<?=ISINTERNAL?>
  	Current<?=SQL_AND_CURRENT?>
  -->
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
<?/*<img src="https://secure.informcommunications.plc.uk/shared/corporate/images/inform_banner_1024x60_faded.png" width="1024" height="60" alt="Inform Communications Plc" /></h1>*/?>
<?if (!empty($page_topnav) && is_array($page_topnav)) {?>
  <div id="navtop">
<?
if (!empty($page_topnav['content_right'])) {
  print "<span style=\"float: right\">{$page_topnav['content_right']}</span>\n";
}
if (!empty($page_topnav['breadcrumbs']) && is_array($page_topnav['breadcrumbs'])) {
  print "    <ul class=\"breadcrumbs\">\n";
  foreach($page_topnav['breadcrumbs'] as $i=>$crumb) {
    $style=($i==0) ? ' class="first_crumb"' : NULL;
    print "      <li$style>$crumb</li>\n";
  }
  print "    </ul>\n";
}
?>
  </div>
<?}?>
  <div id="content">
<?if (!empty($page_h2)) {?>
    <h2><?=htmlspecialchars($page_h2)?></h2>
<?}?>
