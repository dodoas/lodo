<? print $_lib['sess']->doctype ?>
<head>
    <title>Empatix - MVA avstemming liste</title>
    <meta name="cvs"                content="$Id: list.php,v 1.14 2005/11/03 15:33:11 thomasek Exp $" />
    <? includeinc('head') ?>
</head>
<body>

<? includeinc('top') ?>
<? includeinc('left') ?>

<?php
set_time_limit (180);
global $_dbh, $_dsn;
$qry0 = "SELECT DISTINCT JournalID FROM voucher";
$dbh0 = $_dbh[$_dsn]->db_query($qry0);
while ($data0 = $_dbh[$_dsn]->db_fetch_object($dbh0))
{
	$qry1   = "select VoucherID, AutomaticVatVoucherID from voucher where JournalID='" . $data0->JournalID . "';";
	$dbh1 = $_dbh[$_dsn]->db_query($qry1);
	while ($data = $_dbh[$_dsn]->db_fetch_object($dbh1))
	{
		$myID =  $data->VoucherID;
		$id[] = $data->VoucherID;
		if ($data->AutomaticVatVoucherID != "")
			$aid[$myID] = $data->AutomaticVatVoucherID;
	}
	if (is_array($aid))
	foreach ($aid as $vid => $refnr)
	{
		print "Sjekker postering: " . $vid . "<br>";
		$delete = true;
		for ($i = 0; $i < count($id); $i++)
		{
			if ($id[$i] == $refnr)
				$delete = false;
		}
		if ($delete)
		{
			// Slett dette her
			print "Oppdaterer postering: " . $vid . "<br>";
			$qry2   = "update voucher set AutomaticVatVoucherID = NULL where VoucherID = '" . $vid . "';";
			$dbh2 = $_dbh[$_dsn]->db_query($qry2);
		}
	}
	unset($aid);
}

?>


</body>
</html>
