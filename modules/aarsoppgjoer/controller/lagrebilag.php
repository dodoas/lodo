<?php
/*
 * Created on 05.sep.2005
 *
 * To change the template for this generated file go to
 * Window - Preferences - PHPeclipse - PHP - Code Templates
 */

$journal = "both";
if (isset($_GET['journal'])) {
    if ($_GET['journal'] == "one") {
        $journal = "one";
    } else if ($_GET['journal'] == "two") {
        $journal = "two";
    }
}

	$error = null;
	$a = "savesuccessful";
	
	try {
        if ($journal == "one" || $journal == "both") {
            $b1 = new Bilag();
	
            $tmp = new Bilag();
            $tmp->loadTmp(1, $db);
            if ($tmp->getCount() > 0) {
                $b1 = $tmp;
            }
            
            $b1->lagre();            
        }

        if ($journal == "two" || $journal == "both") {
            $b2 = new Bilag();

            $tmp = new Bilag();
            $tmp->loadTmp(2, $db);
            if ($tmp->getCount() > 0) {
                $b2 = $tmp;
            }
            $b2->lagre();	
        }
	
	 } catch (Exception $e) {
 		$error = "Det oppsto en feil under lagring. " . $e;
 		$a = "error";
 		die($error);
 	}

print str_replace("VoucherPeriod wrong in function update_motkonto: ##. Aborting update<br />", "", $_lib['message']->get());
?>
