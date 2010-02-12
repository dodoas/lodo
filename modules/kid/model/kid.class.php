<?
class lodo_logic_kid {

    #Simple function that generates a modulus 10 KID of the invoiceid 
	function generate($args) {
	    global $_lib;
	
	    if($_lib['setup']->get_value('kid.accountplanid')) {
            $kid = $args['AccountPlanID'];
        } elseif($_lib['setup']->get_value('kid.invoiceid')) {
            $kid = $args['InvoiceID'];
        }
	    if($_lib['setup']->get_value('kid.pad')) {
		    $kid = str_pad($kid, $_lib['setup']->get_value('kid.pad'), "0", STR_PAD_LEFT);
        }
		$kidlengde = strlen($kid)+1;

		$strKidProdukter = '';
		// Bruker en algoritme som heter modulus-10
		for ($i = 1; $i <= $kidlengde - 1; $i++) 
		{
			if ($i % 2) 
			{
				$strKidProdukter .= substr($kid, -$i, 1) * 2;
			}
			else 
			{
				$strKidProdukter .= substr($kid, -$i, 1) * 1;
			}
		}
		// Alt maa legges sammen tallfortall
		$siffersum = 0;
		for ($i = 0; $i <= strlen($strKidProdukter)-1; $i++) 
		{
			$siffersum += $strKidProdukter[$i];
		}
		// Trekk fra 10
		// Er den 0 blir siffersum og 0
		if (substr($siffersum, -1, 1) == 0) 
		{
			$kontrollsiffer = 0;
		}
		else 
		{
			$kontrollsiffer = 10 - substr($siffersum, -1, 1);
		}
		$kid .= $kontrollsiffer;
		return $kid;
	}

	function gen_value_checksum($value)
	{
		$kidlengde = strlen($value)+1;
                $strKidProdukter = '';
                for ($i = 1; $i <= $kidlengde - 1; $i++)
                {
                        if ($i % 2)
                        {
                                $strKidProdukter .= substr($value, -$i, 1) * 2;
                        }
                        else
                        {
                                $strKidProdukter .= substr($value, -$i, 1) * 1;
                        }
                }
                $siffersum = 0;
                for ($i = 0; $i <= strlen($strKidProdukter)-1; $i++)
                {
                        $siffersum += $strKidProdukter[$i];
                }
                if (substr($siffersum, -1, 1) == 0)
                {
                        $kontrollsiffer = 0;
                }
                else
                {
                        $kontrollsiffer = 10 - substr($siffersum, -1, 1);
                }
                return $kontrollsiffer;
	}
}
?>
