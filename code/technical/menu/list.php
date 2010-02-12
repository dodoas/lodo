<?
# Based on EasyComposer technology
# Copyright Thomas Ekdahl, 1994-2005, thomas@ekdahl.no, http://www.ekdahl.no

$db_table  = "confmenues";

$query = "select DISTINCT(MenuName) from $db_table where Active='1'";
$result = $_lib['db']->db_query($query);

$query_lang = "select DISTINCT(Language) from $db_table where Active='1'";
$result_lang = $_lib['db']->db_query($query_lang);

$language = Array();
$i = 0;
while($row = $_lib['db']->db_fetch_object($result_lang)){
  $language[$i] = $row->Language;
  $i++;
}

if(!$lang) {
  $lang = 'no';
}
?>
<? print $_lib['sess']->doctype ?>
<head>
	<title>Empatix - order list</title>
	<meta name="cvs"     		    content="$Id: list.php,v 1.8 2005/10/28 17:59:41 thomasek Exp $" />
	<? require_once $_SETUP['HOME_DIR'] . "/code/lib/html/head.inc"; ?>
</head>

<body>	
<table>
  <tr class="Heading"> 
    <td>
      <H6><b>Menu name</b></H6>
    </td>
					
    <td align="right"> 
	<form name="menu" action="<? print $_lib['sess']->dispatch ?>t=menu.edit" method="post">
	Name: <input type="text" name="MenuName" value="meny name" size="15">
	</td>
	<td>
	Language: <input type="text" name="Lang" value="en" size="15">
	<input type="submit" value="New menu (N)" name="record_new" tabindex="1" accesskey="N">
	</form>
  </td>
</tr>
<tr>
<td colspan="4"></td></tr>
<?
while($row = $_lib['db']->db_fetch_object($result)) {
$i++;
if (!($i % 2)) {
       	$sec_color = "BGColorLight";
    }
    else {
        $sec_color = "BGColorDark";
        };
?>  
	  <form name="customer" action="<? print $MY_SELF ?>" method="post">
	  <input type="hidden" name="MenuID"	value="<? print $row->MenuID; ?>">
			
    <tr class="<? print $sec_color ?>"> 
      <td> 
        <a href="<? print $_lib['sess']->dispatch ?>t=menu.edit&MenuName=<? print $row->MenuName ?>&Language1=<? print $lang ?>"><? print $row->MenuName ?></a>
      <td colspan="2">
        <?
        foreach ($language as $i => $value) {
          ?>
          <a href="<? print $_lib['sess']->dispatch ?>t=menu.edit&MenuName=<? print $row->MenuName ?>&Language1=<? print $value ?>"><? print $value  ?></a>
          <?
        }
        ?>
	</tr>
	</form>
<? } ?>		
		</table>
	</body>

</html>
