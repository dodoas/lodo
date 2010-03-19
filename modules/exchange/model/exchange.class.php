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
        if (empty($_POST['voucher_currency_VoucherID']) || 
            !is_numeric($_POST['voucher_currency_VoucherID'])) {
            return false;
        }
        
        $voucher_id = $_POST['voucher_currency_VoucherID'];

        $currency_id_key = 'voucher_currency_ForeignCurrencyIDSelection';
        $amount_key = 'voucher_currency_ForeignAmount';
        $rate_key = 'voucher_currency_ForeignConvRate';
        
        if (empty($_POST[$amount_key])) {
            return false;
        }

        if (empty($_POST[$rate_key])) {
            return false;
        }

        if (empty($_POST[$currency_id_key])) {
            return false;
        }

		$foreign_amount = $_POST[$amount_key] = str_replace(',', '.', $_POST[$amount_key]);
		$rate = $_POST[$rate_key] = str_replace(',', '.', $_POST[$rate_key]);
        $currency_id = $_POST[$currency_id_key];

		$data = $_lib['input']->get_data();
		$query_update = " UPDATE voucher SET ".
            "  ForeignCurrencyID='". $_lib['db']->db_escape($currency_id) . "'" .
            ", ForeignAmount='"     . $_lib['db']->db_escape($foreign_amount) . "'" .
            ", ForeignConvRate="   . "'" . $_lib['db']->db_escape($rate) . "'" .
						" WHERE VoucherID = '" . $voucher_id . "'";
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
        if ($action_url == '')
			$action_url = 'lodo.php?'. $_SERVER['QUERY_STRING'];
		$currencies = self::getCurrencies();
        $select_options = '<option value="">Velg valuta</option>';
        foreach ($currencies as $currency) {
            if ($voucher_foreign_currency && $currency->CurrencyISO == $voucher_foreign_currency)
                $select_options .= '<option value="'. $currency->CurrencyISO .'" selected="selected">'. $currency->CurrencyISO .'</option>';
            else
                $select_options .= '<option value="'. $currency->CurrencyISO .'">'. $currency->CurrencyISO .'</option>';
        }

        $block_return = 'onKeyPress="return disableEnterKey(event)"';
        $ch_curr  = '<div style="display:none;" class="vouchercurrencywrapper" id="voucher_currency_div_'. $voucher_id .'">';
        $ch_curr .= 'Valuta: <select name="voucher_currency.ForeignCurrencyID" ' . $block_return . '>'. $select_options .'"</select><br />';
        $ch_curr .= 'Verdi: <input class="number" type="text" name="voucher_currency.ForeignAmount" size="10" value="'. $voucher_foreign_amount .'" ' . $block_return . ' /><br />';
        $ch_curr .= 'Rate: <input class="number" type="text" name="voucher_currency.ForeignConvRate" size="10" value="'. $voucher_foreign_rate .'" ' . $block_return . ' /><br />';
        $ch_curr .= '<input class="number" type="hidden" name="voucher_currency.VoucherID" value="'. $voucher_id .'" />';
        $ch_curr .= '<input class="number" type="hidden" name="voucher_currency.ForeignCurrencyIDSelection" value="" />';
        $ch_curr .= '<input type="hidden" name="action_postmotpost_save_currency" value="1" />';
        $ch_curr .= '<input type="button" name="action_postmotpost_save_currency_button" onclick="return voucherCurrencyChange(this, \'' . $action_url . '\'); " value="Lagre" />';
        $ch_curr .= '</div>';
		
		return $ch_curr;
	}


	/**
	 * @param  int    $voucher_id The voucher id
	 * @param  String $link_txt
	 * @return string HTML anchor to use with exchange::getFormVoucherForeignCurrency()
	 */
	function getAnchorVoucherForeignCurrency($voucher_id, $link_txt='Velg valuta') {
        return '<a href="#" onClick="toggle(\'voucher_currency_div_'. $voucher_id .'\');return false;">'. $link_txt .'</a>';
	}
}
?>