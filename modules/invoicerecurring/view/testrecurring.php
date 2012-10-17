<?
includelogic('invoicerecurring/recurring');

/* companydef needed for journaling as default values. No impact on invoices created. */
$sql = "SELECT * FROM company WHERE CompanyID=1";
$row = $_lib['db']->get_row(array('query' => $sql));
$_lib['sess']->companydef = $row;
            
/* check and send */
global $accounting;
            
$accounting = 0;

/* check_and_send does not check if startDate is in the future. 
   This is done here to save a query. */
$r =  $_lib['db']->db_query("SELECT * FROM recurring WHERE DATE_SUB(StartDate, INTERVAL `PrintInterval` DAY) <= NOW()") 
    or die("ERROR: " . mysql_error());
$recurring_array = array();	
while($row = $_lib['db']->db_fetch_assoc($r))
{   
    $recurring_array[] = $row;
}

foreach($recurring_array as $line)
{   
    $recurring = new recurring(array());
    $recurring->check_and_send($line);
}

echo "Done";