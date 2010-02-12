
<? print $_lib['sess']->doctype ?>
<head>
	<title>Internsystem - blank</title>
	<meta name="cvs"     		    content="$Id: main.php,v 1.6 2005/10/28 17:59:41 thomasek Exp $" />
	<? require_once $_SETUP['HOME_DIR'] . "/code/lib/html/head.inc"; ?>
<script language="Javascript">

function birthday(){
<? 

#Check todays todos
$query = "select * from calendarevents where TO_DAYS(TimeAlarm) - TO_DAYS(NOW()) <= 3 and TO_DAYS(TimeAlarm) - TO_DAYS(NOW()) >= 0 and Subject != '' order by TimeAlarm asc";
$result = $_lib['db']->db_query($query);
while($row = $_lib['db']->db_fetch_object($result)) {
     $day = substr($row->BirthDate,0,10);
     $text .=  "Calender: $row->TimeStart $row->Subject\\n";
}

#Check if somebdy has a birthday. 1 week each way from brithdate
$query = "select * from person where EXTRACT(MONTH from BirthDate) = EXTRACT(MONTH from NOW()) order by EXTRACT(DAY from BirthDate) asc";
$result = $_lib['db']->db_query($query);
while($row = $_lib['db']->db_fetch_object($result)) {
     $day = substr($row->BirthDate,0,10);
     $text .=  "Birthday: $row->FirstName $row->LastName $day\\n";
}

if($text){
  print "alert(\"$text\");\n";

}

?>
}
</script>
</head>
<body>
<?
if(!ini_get('register_globals')) {
   print "<b>Fatal:</b> Empatix requires that register_globals in php.ini is enabled.<br>";
   exit;
}
?>
<h2 class="groupheader">Hurtiglinker Intranett</h2>
 <div class="leftmenu">
  <div class="group">
	<a href="<? print $_lib['sess']->dispatch ?>t=sla.list" class="button">Service Level Agreement</a>
	<a href="<? print $_lib['sess']->dispatch ?>t=process.list" class="button">Kvalitetsprosess - i arbeid</a>
	<a href="<? print $_lib['sess']->dispatch ?>t=survey.list" class="button">Markedsunders&oslash;kelse</a>
	<a href="<? print $_lib['sess']->dispatch ?>t=urlalias.list" class="button">URL Alias</a>
	<a href="<? print $_lib['sess']->dispatch ?>t=person.list" class="button">Brukere</a>
  </div>
 </div>
</body>
</html>
