<?
# $Id: import.php,v 1.16 2005/10/24 11:50:24 svenn Exp $ account_import.php,v 1.3 2001/11/20 17:55:12 thomasek Exp $
# Based on EasyComposer technology
# Copyright Thomas Ekdahl, 1994-2005, thomas@ekdahl.no, http://www.ekdahl.no

$AccountNumber  = $_REQUEST['AccountNumber'];
$AccountID      = $_REQUEST['AccountID'];
$Bank      		= $_REQUEST['Bank'];

if($_REQUEST['Period']) {
	$Period     = $_REQUEST['Period'];
} else {
	$Period     = $_lib['date']->get_prev_period(array('value' => $_lib['sess']->get_session('LoginFormDate'), 'realPeriod' => 1));
}

$db_table = "accountline";
$upload_dir = "/tmp";

$query   = "select * from account where AccountID=$AccountID";
$account = $_lib['storage']->get_row(array('query' => $query));

# Import bank account files in format: Skandiabanken
# Skb:  BookKeepingDate;InterestDate;UseDate;ArchiveRef;AccountCategory;AccountDescription;AmountOut;AmountIn
# Spb1: Bet.dato;Beskrivelse;Rentedato;Ut/Inn;
# Input: AccountNumber

# Http file upload

if($_FILES['bankaccountfile']['size'] > 0 ) {

	
	$import = new accountline_import($Bank, $AccountID, $Period, $_FILES['bankaccountfile']['tmp_name']);

}


class accountline_import {
	private $debug = false;

	private $format = array(
		'spb1bedrift1' => array(
			'BookKeepingDate' => 0,
			'InterestDate'    => 1,
			'Description'     => 2,
			'AmountOut'       => 3,
			'AmountIn'        => 4,
		    'AmountInOut'     => -1,
			'Comment'         => 5,
			'Num.Ref'         => 6,
			'Separator'		  => ';',
			'Sort'			  => 'reverse',
			'IgnoreLines'     => 0,
			'ArchiveRef'      => -1,
			'ThousandSep'     => '.'
			),
		'spb1privat1' => array(
			'BookKeepingDate' => 0,
			'InterestDate'    => 2,
			'Description'     => 1,
			'AmountOut'       => -1,
			'AmountIn'        => -1,
		    'AmountInOut'     => 3,
			'Comment'         => -1,
			'Num.Ref'         => -1,
			'Separator'		  => ';',
			'Sort'			  => 'reverse',
			'IgnoreLines'     => 0,
			'ArchiveRef'      => -1,
			'ThousandSep'     => ''
			),
		'skandiabanken1' => array(
			'BookKeepingDate' => 0,
			'InterestDate'    => 1,
			'Description'     => 4,
			'AmountOut'       => 5,
			'AmountIn'        => 6,
			'AmountInOut'     => -1,
			'Comment'         => 3,
			'Num.Ref'         => -1,
			'Separator'		  => '	',
			'Sort'			  => 'reverse',
	        'IgnoreLines'     => 0,
	        'ArchiveRef'      => 2,
	        'ThousandSep'     => ''
			),
		'skandiabanken2' => array(
			'BookKeepingDate' => 0,
			'InterestDate'    => 1,
			'Description'     => 4,
			'AmountOut'       => 5,
			'AmountIn'        => 6,
			'AmountInOut'     => -1,
			'Comment'         => 3,
			'Num.Ref'         => -1,
			'Separator'		  => ';',
			'Sort'			  => 'reverse',
	        'IgnoreLines'     => 0,
	        'ArchiveRef'      => 2,
	        'ThousandSep'     => ''
			),
		'dnbnor1bedrift' => array(
			'BookKeepingDate' => 0,
			'InterestDate'    => 4,
			'Description'     => 2,
			'AmountOut'       => 5,
			'AmountIn'        => 6,
			'AmountInOut'     => -1,
			'Comment'         => 1,
			'Num.Ref'         => -1,
			'Separator'		  => ';',
			'Sort'			  => 'normal',
			'IgnoreLines'     => 5,
			'ArchiveRef'      => 7,
			'ThousandSep'     => '.'
			),
		'dnbnor1privat1' => array(
			'BookKeepingDate' => 0,
			'InterestDate'    => 2,
			'Description'     => 1,
			'AmountOut'       => 4,
			'AmountIn'        => 3,
			'AmountInOut'     => -1,
			'Comment'         => -1,
			'Num.Ref'         => -1,
			'Separator'		  => ';',
			'Sort'			  => 'reverse',
			'IgnoreLines'     => -1,
			'ArchiveRef'      => -1,
			'ThousandSep'     => ''
			),
		'dnbnor1privat2' => array(
			'BookKeepingDate' => 0,
			'InterestDate'    => 2,
			'Description'     => 1,
			'AmountOut'       => 4,
			'AmountIn'        => 3,
			'AmountInOut'     => -1,
			'Comment'         => -1,
			'Num.Ref'         => -1,
			'Separator'		  => ',',
			'Sort'			  => 'reverse',
			'IgnoreLines'     => -1,
			'ArchiveRef'      => -1,
			'ThousandSep'     => '.'
			),
		'dnbnor1privatbedrift' => array(
			'BookKeepingDate' => 0,
			'InterestDate'    => 2,
			'Description'     => 1,
			'AmountOut'       => 3,
			'AmountIn'        => 4,
			'AmountInOut'     => -1,
			'Comment'         => -1,
			'Num.Ref'         => -1,
			'Separator'		  => ';',
			'Sort'			  => 'reverse',
			'IgnoreLines'     => -1,
			'ArchiveRef'      => -1,
			'ThousandSep'     => '.'
			),

		'oest' => array(
			'BookKeepingDate' => 0,
			'InterestDate'    => -1,
			'Description'     => 1,
			'AmountOut'       => 2,
			'AmountIn'        => 3,
			'AmountInOut'     => -1,
			'Comment'         => -1,
			'Num.Ref'         => -1,
			'Separator'		  => '	',
			'Sort'			  => 'reverse',
			'IgnoreLines'     => 0,
			'ArchiveRef'      => -1,
			'ThousandSep'     => ''
			),
		'terra' => array(
			'BookKeepingDate' => 0,
			'InterestDate'    => 1,
			'Description'     => 2,
			'AmountOut'       => -1,
			'AmountIn'        => -1,
			'AmountInOut'     => 3,
			'Comment'         => -1,
			'Num.Ref'         => -1,
			'Separator'		  => ',',
			'Sort'			  => 'reverse',
			'IgnoreLines'     => 0,
			'ArchiveRef'      => -1,
			'ThousandSep'     => ','
			),
		'strommensparebank1' => array(
			'BookKeepingDate' => 0,
			'InterestDate'    => 1,
			'Description'     => 2,
			'AmountOut'       => -1,
			'AmountIn'        => -1,
			'AmountInOut'     => 3,
			'Comment'         => -1,
			'Num.Ref'         => -1,
			'Separator'		  => ',',
			'Sort'			  => 'reverse',
			'IgnoreLines'     => 0,
			'ArchiveRef'      => -1,
			'ThousandSep'     => ','
			)

		);
		
		#Sort = '' - Use the sorting from the file
		#ThousandSep = '' Do not touch, but if given the separator will be removed before processing

	function __construct($Bank, $AccountID, $Period, $filename) {
		global $_lib;
		
		ini_set('auto_detect_line_endings', true);
		
		$linesA                 = array();
        $duplicatetransaction   = 0;
        $ignoredtransactions    = 0;
		
		$fp = fopen($filename,"r");
	
		if(!$AccountID) {
			$_lib['message']->add("Mangler kontonummer");
			return;
		}
					
		if(!$Period) {
			$_lib['message']->add("Mangler periode");
			return;
		}

		if(!$Bank) {
			$_lib['message']->add("Importformat ikke spesifisert");
			return;
		}

		# The first line header is removed
		if($this->debug) print "Bank: $Bank, Period: $Period<br>\n";
	
		while ($data = fgetcsv ($fp, 1000, $this->format[$Bank]['Separator'])) {
			$lineH 	= array();
			$num 	= count ($data);
			
			$transactionstotal++;

			#print_r($data);

			$lineH['BookKeepingDate'] 	= $_lib['convert']->Date($data[$this->format[$Bank]['BookKeepingDate']]);

			#Sjekk at dato er i riktig periode - noen av kontoutskriftene blander dette.
			
			
			if($Period == substr($lineH['BookKeepingDate'],0,7)) {
				$transactionsimported++;	
	
				# Fiks date formats to iso standard
				$lineH['AccountID'] 		= $_lib['db']->db_escape($AccountID);
				$lineH['Period'] 			= $_lib['db']->db_escape($Period);
				$lineH['Active'] 			= 1;
				$lineH['InterestDate'] 		= $_lib['db']->db_escape($_lib['convert']->Date($data[$this->format[$Bank]['InterestDate']]));
				$lineH['Day'] 				= $_lib['db']->db_escape(substr($lineH['BookKeepingDate'], 8 , 2));
				
				#Kunne hatt delvis automatisk reskontro match basert pŒ beskrivelse.
				$lineH['Description'] 		= $_lib['db']->db_escape(str_replace("  ", " ", $data[$this->format[$Bank]['Description']]));
				$lineH['ArchiveRef'] 		= $data[$this->format[$Bank]['ArchiveRef']];


				if($this->format[$Bank]['AmountIn'] != -1) {
    				$lineH['AmountIn'] 		= str_replace($this->format[$Bank]['ThousandSep'], "", $data[$this->format[$Bank]['AmountIn']]);
	    			$lineH['AmountOut']     = str_replace($this->format[$Bank]['ThousandSep'], "", $data[$this->format[$Bank]['AmountOut']]);

					$lineH['AmountIn'] 		= abs($_lib['convert']->Amount($lineH['AmountIn']));
					$lineH['AmountOut'] 	= abs($_lib['convert']->Amount($lineH['AmountOut']));
				} else {

	    			$AmountInOut        = str_replace($this->format[$Bank]['ThousandSep'], "", $data[$this->format[$Bank]['AmountInOut']]);
					$AmountInOut        = $_lib['convert']->Amount($AmountInOut);

					if($AmountInOut < 0) {
						$lineH['AmountOut'] = abs($AmountInOut);
					
					} elseif($AmountInOut > 0) {
						$lineH['AmountIn']  = abs($AmountInOut);
					}
				}
		
		        #Only add lines with a amount
				if($lineH['AmountIn'] > 0 || $lineH['AmountOut'] > 0) {


                    #Check if ArciveRef is on same account, same period - then it is a duplicate and not imported
                    if(strlen($lineH['ArchiveRef'])) {
                        $sql_archiveref  = "select * from accountline where AccountID=$AccountID and Period = '$Period' and ArchiveRef != '' and ArchiveRef='" . $_lib['db']->db_escape($lineH['ArchiveRef']) . "'";
                        #print "$sql_days<br>\n";
                        $exist   	     = $_lib['storage']->get_row(array('query' => $sql_archiveref));
                        if($exist) {
            				$duplicatetransaction++;                
                        } else {
    					    $linesA[] = $lineH;                    
                        }
                    } else {
                        #If we do not have archive ref - we have to insert the line since it is not possible to check for duplicates
					    $linesA[] = $lineH;
                    }
				}
			} else {
				$ignoredtransactions++;
			}
		}
		
		fclose ($fp);

		#print_r($linesA);

		if($this->format[$Bank]['Sort'] == 'reverse') {
			krsort($linesA);
		}
		
		#print "sort: " . $this->format[$Bank]['Sort'] . "<br>\n"
		#print_r($linesA);

		foreach($linesA as $lineH) { 				
				$Priority++;


				$lineH['Priority'] 		 = $Priority;
				#print_r($lineH);
				$postvl['AccountLineID'] = $_lib['storage']->store_record(array('table' => 'accountline', 'data' => $lineH, 'debug' => $this->debug));
				
				#Do we really need voucheraccountline - or could we throw it away????
		        $_lib['db']->store_record(array('data' => $postvl, 'table' => 'voucheraccountline', 'debug' => $this->debug));
		}

		$_lib['message']->add("Transactions importert: $transactionsimported. Transaksjoner utenfor periode: $ignoredtransactions, duplikattransaksjoner: $duplicatetransaction<br>");
	}
}
?>

<? print $_lib['sess']->doctype ?>
<head>
    <title>Empatix - <? print $_lib['sess']->get_companydef('CompanyName') ?> : <? print $_lib['sess']->get_person('FirstName') ?> <? print $_lib['sess']->get_person('LastName') ?> - bankaccount import</title>
    <meta name="cvs"                content="$Id: import.php,v 1.16 2005/10/24 11:50:24 svenn Exp $" />
    <? includeinc('head') ?>
</head>

<body>

<? includeinc('top') ?>
<? includeinc('left') ?>

<? print $_lib['form3']->url(array('description' => 'Avstemming f&oslash;rst i m&aring;neden',      'url' => $_lib['sess']->dispatch . 't=bank.tabstatus'       . '&amp;AccountID=' . $AccountID . '&amp;Period=' . $Period)) ?> | 
<? print $_lib['form3']->url(array('description' => 'Kontoutskrift',    'url' => $_lib['sess']->dispatch . 't=bank.tabbankaccount'  . '&amp;AccountID=' . $AccountID . '&amp;Period=' . $Period)) ?> | 
<? print $_lib['form3']->url(array('description' => 'Bilagsf&oslash;r/Avstemming i slutten av m&aring;neden',          'url' => $_lib['sess']->dispatch . 't=bank.tabjournal'      . '&amp;AccountID=' . $AccountID . '&amp;Period=' . $Period)) ?> |
<? print $_lib['form3']->url(array('description' => 'Enkel',          'url' => $_lib['sess']->dispatch . 't=bank.tabsimple'      . '&amp;AccountID=' . $AccountID . '&amp;Period=' . $Period)) ?> | 
<? print $_lib['form3']->url(array('description' => 'Import',          'url' => $_lib['sess']->dispatch . 't=bank.import'      . '&amp;AccountID=' . $AccountID . '&amp;Period=' . $Period)) ?>

<h2><? print $_lib['message']->get() ?></h2>

<h2><? print $account->AccountNumber ?> - 
<? print $account->AccountDescription ?>
</h2>
<form enctype="multipart/form-data" method="post" action="<? print $MY_SELF ?>" name="pages">
Format: 
<select name="Bank">
<option value="spb1bedrift1">Sparebank1 Bedriftsnettbank (semikolon separert, komma desimaltegn, ingen tusenskilletegn, sorteres stigende)</option>
<option value="spb1privat1">Sparebank1 Privatnettbank (semikolon separert, komma desimaltegn, ingen tusenskilletegn, sorteres stigende)</option>
<option value="skandiabanken1">Skandiabanken - default - alternativ til excel (tabulator separert, komma desimaltegn, ingen tusenskilletegn, sorteres stigende)</option>
<option value="skandiabanken2">Skandiabanken  - alternativ til excel (semikolon separert, komma desimaltegn, ingen tusenskilletegn, sorteres stigende)</option>
<option value="dnbnor1bedrift">DNB NOR - Bedrift (semikolon separert, komma desimaltegn, punktum tusenskilletegn, sorteres som i fila (stigende))</option>
<option value="dnbnor1privat1">DNB NOR - Privat (semikolon separert, komma desimaltegn, punktum tusenskilletegn, sorteres som i fila (stigende))</option>
<option value="dnbnor1privat2">DNB NOR - Privat -> Bedrift (komma separert, komma desimaltegn, punktum tusenskilletegn, sorteres stigende)</option>
<option value="dnbnor1privatbedrift">DNB NOR - Privat -> Bedrift (semikolon separert, komma desimaltegn, punktum tusenskilletegn, sorteres stigende)</option>
<option value="terra">Terra - (semikolon separert, punktum desimaltegn, komma tusenskilletegn, sorteres stigende)</option>
<option value="oest">Sparebanken &Oslash;st (, sorteres synkende)</option>
<option value="strommensparebank1">Str&oslash;mmen sparebank (komma separert, komma tusenskilletegn, sorteres stigende)</option>

</select>
<input type="hidden"    name="AccountID"        value="<? print $AccountID ?>">
<input type="hidden"    name="AccountNumber"    value="<? print $account->AccountNumber ?>">
<br />
Periode: <? print $_lib['form3']->AccountPeriod_menu3(array('name' => 'Period', 'pk' => $AccountID, 'value' => $Period, 'access' => $_lib['sess']->get_person('AccessLevel'), 'accesskey' => 'P', 'required'=> true)); ?>
<br />
Fil: <input type="file"      name="bankaccountfile"       size="50">
<br />
<? if($_lib['sess']->get_person('AccessLevel') >= 2) { ?>
<input type="submit" name="action_bank_import"  value="Importer">
<? } ?>
</form>
</body>
</html>
<pre>
