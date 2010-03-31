<?php
class model_bank_accountlineimport {
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
			'ThousandSep'     => ''
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

	function __construct($AccountID, $Period) {
		global $_lib;
		
		$linesA                 = array();
        $duplicatetransaction   = 0;
        $ignoredtransactions    = 0;
		
		if(!$AccountID) {
			$_lib['message']->add("Mangler kontonummer");
			return;
		}
					
		if(!$Period) {
			$_lib['message']->add("Mangler periode");
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