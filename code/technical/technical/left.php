<?
if($lang=='no'){
  $sql              = "SELECT * FROM pages WHERE struc_parent='36' and struc_type='catalog'";
} else {
  $sql              = "SELECT * FROM pages WHERE struc_parent='36' and struc_type='catalog'";
}
$result           = $_lib['db']->db_query($sql);

?>


<? print $_lib['sess']->doctype ?>
<head>
	<title>Empatix- navigation</title>
	<meta name="cvs"     		    content="$Id: left.php,v 1.3 2005/10/14 13:15:43 thomasek Exp $" />
	<? require_once $_SETUP['HOME_DIR'] . "/code/lib/html/head.inc"; ?>
</head>

<body>
<ul class="tabletab" cellspacing="0" id="linav"><? if($lang == "no"){ ?><li class="inaktivtab"><a href="../index.php?" target="_top"  title="English version"><span class="tabfix">English</span></a><li class="inaktivtab"><a href="../index.php?" target="_top" title="Norsk versjon"><span class="tabfix">Norsk</span></a><? } else { ?><li class="inaktivtab"><a href="../index.php?" target="_top" title="Norsk versjon"><span class="tabfix">Norsk</span></a><li class="inaktivtab"><a href="../index.php?" target="_top"  title="English version" ><span class="tabfix">English</span></a><? } ?>
</ul>
<table>
<tr>
<td class="BGColorDark">
  <!--************About ourchildhood.no************-->
  <h3><? if($lang == "no"){ ?><? print $idCompany ?><? } else { ?><? print $idCompany ?><? } ?></h3>
  <p class="menu">
<?
while($row = $_lib['db']->db_fetch_object($result)){
?>
<a href="../template/pages/public-standard.php?chapter_parent=<? print "$row->chapter_parent"; ?>" target="main" class=""><? print "$row->struc_description"?></a><br />
<? } ?>
</p>

<h3>
<? if($lang=='no') { ?>S&oslash;k etter tekst<? } else { ?> Search for text <? } ?>
</h3>
<p class="menu"><form name="searchtxt" action="../template/pages/search.php" method="post" target="main">    
   		<input type="hidden" value="<? print "$lang"; ?>" name="lang"> 
			<input type="text" size="15" value="" name="searchstring"> 
			<input type="submit" name="submit" value="<? if($lang=='no') { ?>S&oslash;k<? } else {?>Search<? } ?>">  </form></p>
</td>
</tr>
</table>
</body>
</html>

