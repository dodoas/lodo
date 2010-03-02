<?php

/*************************************
 * Created 2010-02-08 by Tomas Skipa *
 ************************************/

class exchange {

    function __construct() { }


	/**
	 * @param String $currency Currency ISO code
	 * @param float $amount Amount in foreign currency
	 * @return int amount in NOK
	 */
	function convertToNOK($currency, $amount, $round=false) {
		global $_lib;

		$query = "SELECT Amount FROM exchange WHERE CurrencyID = '$currency'";
		$result = $_lib['db']->db_query($query);
		if ($rate = $_lib['db']->db_fetch_object($result)) {
			if ($rate->Amount > 0 && $round) {
				$sum = round($amount / $rate->Amount * 100, 0);
			} elseif ($rate->Amount > 0 && !$round) {
				$sum = round($amount / $rate->Amount * 100, 2);
			} else {
				return false;
			}
			return (float)$sum;
		} else {
			return false;
		}
	}


	/**
	 * @return mixed array Array with currencies
	 */
	function getCurrencies() {
		global $_lib;

		#Retrieve active currencies
		$query = "SELECT * FROM currency ORDER BY `CurrencyISO`";
		$result_currency = $_lib['db']->db_query($query);
		$currencies = array();

		while($tmp = $_lib['db']->db_fetch_object($result_currency)) {
			if ($tmp)
				$currencies[] = $tmp;
		}
		
		return $currencies;
	}


	/**
	 * @return mixed array Array with currencies
	 */
	function getActiveCurrencies() {
		global $_lib;

		#Retrieve active currencies
		$query = "SELECT * FROM currency WHERE `CurrencyISO` NOT IN (SELECT `CurrencyID` FROM `exchange`) ORDER BY `CurrencyISO`";
		$result_currency = $_lib['db']->db_query($query);
		$currencies = array();

		while($tmp = $_lib['db']->db_fetch_object($result_currency)) {
			if ($tmp)
				$currencies[] = $tmp;
		}
		
		return $currencies;
	}


	/**
	 * @param String $currency_iso
	 * @return string Conversion rate
	 */
	function getConversionRate($currency_iso) {
		global $_lib;

		#Retrieve active currencies
		$query = "SELECT Amount FROM exchange WHERE `CurrencyID` = '$currency_iso'";
		$result_currency = $_lib['db']->db_query($query);

		$tmp = $_lib['db']->db_fetch_object($result_currency);
		if ($tmp && $tmp->Amount > 0)
			return (float)$tmp->Amount;
		else
			return false;
	}


	/**
	 * @return string Conversion rate
	 */
	function updateVoucherForeignCurrency() {
		global $_lib;

		$_POST['voucher_ForeignAmount'] = str_replace(',', '.', $_POST['voucher_ForeignAmount']);
		$_POST['voucher_ForeignConvRate'] = str_replace(',', '.', $_POST['voucher_ForeignConvRate']);
		$data = $_lib['input']->get_data();
		$query_update = " UPDATE voucher SET ".
						   "  ForeignCurrencyID='". (($_POST['voucher_ForeignCurrencyID']) ? $_POST['voucher_ForeignCurrencyID'] : '') ."'".
						   ", ForeignAmount="     . (($_POST['voucher_ForeignAmount'])     ? $_POST['voucher_ForeignAmount']     : 'NULL').
						   ", ForeignConvRate="   . (($_POST['voucher_ForeignAmount'])     ? $_POST['voucher_ForeignConvRate']   : 'NULL').
						" WHERE VoucherID = ". $_POST['voucher_VoucherID'];
		$_lib['db']->db_update($query_update);
	}


	/**
	 * @param  int    $voucher_id The voucher id
	 * @param  float  $voucher_foreign_amount The amount in foreign currency
	 * @param  float  $voucher_foreign_rate The conversion rate based in NOK 100,-
	 * @param  String $voucher_foreign_currency The currency ISO code
	 * @param  String $action_url The form action attribute
	 * @return string HTML form inside a div block. Div is initially hidden (display:none)
	 */
	function getFormVoucherForeignCurrency($voucher_id, $voucher_foreign_amount, $voucher_foreign_rate, $voucher_foreign_currency, $action_url='') {
        if ($action_url != '')
			$action_url = 'lodo.php?'. $_SERVER['QUERY_STRING'];
		$currencies = self::getCurrencies();
        $select_options = '<option value="">Velg valuta</option>';
        foreach ($currencies as $currency) {
            if ($currency->CurrencyISO == $voucher_foreign_currency)
                $select_options .= '<option value="'. $currency->CurrencyISO .'" selected="selected">'. $currency->CurrencyISO .'</option>';
            else
                $select_options .= '<option value="'. $currency->CurrencyISO .'">'. $currency->CurrencyISO .'</option>';
        }

        //$ch_curr = '<a href="#" onClick="toggle(\'div_'. $voucher->VoucherID .'\');return false;">'. $tmp_foreign .'</a>';
        $ch_curr  = '<div style="display:none;" id="div_'. $voucher_id .'"><form method="post" action="'. $action_url .'">';
        $ch_curr .= 'Valuta: <select name="voucher.ForeignCurrencyID">'. $select_options .'"</select><br />';
        $ch_curr .= 'Verdi: <input class="number" type="text" name="voucher.ForeignAmount" size="10" value="'. $voucher_foreign_amount .'" /><br />';
        $ch_curr .= 'Rate: <input class="number" type="text" name="voucher.ForeignConvRate" size="10" value="'. $voucher_foreign_rate .'" /><br />';
        $ch_curr .= '<input class="number" type="hidden" name="voucher.VoucherID" value="'. $voucher_id .'" />';
        $ch_curr .= '<input type="submit" name="action_postmotpost_save_currency" value="Lagre" />';
        $ch_curr .= '</form></div>';
		
		return $ch_curr;
	}


	/**
	 * @param  int    $voucher_id The voucher id
	 * @param  String $link_txt
	 * @return string HTML anchor to use with exchange::getFormVoucherForeignCurrency()
	 */
	function getAnchorVoucherForeignCurrency($voucher_id, $link_txt='Velg valuta') {
        return '<a href="#" onClick="toggle(\'div_'. $voucher_id .'\');return false;">'. $link_txt .'</a>';
	}
}
?>