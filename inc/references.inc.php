<?
$query  = "select * from installation where EnableReference=1";
print $query;
$result = $_lib['db']->db_query($query);

$query_num      = "select count(*) as TotalCustomers from installation 
where Active=1";
$row            = $_lib['db']->get_row(array('query' => $query_num));
$TotalCustomers = $row->TotalCustomers;
?>

<h2>Referanser</h2>
Vi har <? print $TotalCustomers ?> kunder som bruker lodo, her er noen 
av referansene.
<ul>
<? while($row = $_lib['db']->db_fetch_object($result)) { ?>
    <li><? print $row->VName; ?>, <? print 
$row->VAddress; ?>, <? print 
$row->VZipCode; ?>, <? print $row->VCity; ?></li>
<? } ?>
</ul>
