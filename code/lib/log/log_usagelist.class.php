<?

function log_usagelist($args) {
    global $_sess, $_dbh, $_dsn;
	$query_log  = "select * from logusage where Interface='" . $args{interface} . "' and Module='" . $args{module} . "' and Template='" . $args{template} . "' and PkField = '" . $args{PkField} . "' and PkValue = '" . $args{PkValue} . "' order by TS desc";
    #print "$query_log<br>";
	$result_log = $_lib['db']->db_query($query_log);
	?>
	<h2 class="groupheader">Logg for <? print $args{interface} . "/" . $args{module} . "/" . $args{template} . " where " . $args{PkField} . " = " . $args{PkValue} ?></h2>

    <div class="group">
    <table cellspacing="0" width="100%" class="border">
      <tr class="SubHeading"> 
        <th>Dato/Tid
		<th>Tid imellom
		<th>Person
		<th>Referer
		<th>Nettleser
		<th>IP-Adresse
		<th>Session
	<?

	while($row = $_lib['db']->db_fetch_object($result_log)) {
	$i++;
	if (!($i % 2)) { $sec_color = "BGColorlight"; } else { $sec_color = "BGColorDark"; };
	?>
	<tr class="<? print "$sec_color" ?>">
	  <td><? print $row->TS        ?>
	  <td>
	  <?
	  $diff = $old_ts - $row->TS;
	  $old_ts = $row->TS;
	  print "$diff";
	  ?>
	  <td><a href="<? print $_sess->get_session('Dispatch') ?>t=person.edit&PersonID=<? print $row->PersonID ?>"><? print $row->PersonID  ?></a>
	  <td><? print $row->Referer   ?>
	  <td><? print $row->UserAgent ?>
	  <td><? print $row->IPAdress  ?>
	  <td><a href="<? print $_sess->get_session('Dispatch') ?>t=log.session&SessionID=<? print $row->SessionID ?>"><? print $row->SessionID ?></a>
	<?
	}
	?>
	<tr class="BGColorDark">
	  <td>Sum bes&oslash;kende
	  <td colspan="6"><? print $i ?>
</table>
</div>
<?
}
?>
