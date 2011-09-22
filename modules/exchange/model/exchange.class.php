<?php

/*************************************
 * Created 2010-02-08 by Tomas Skipa *
 ************************************/

class exchange {

    function __construct() { }

    private function getCurrencyCode() {
		global $_lib;

        $query_setup    = "select name, value from setup where Name like '%localcurrency%'";
        $setup          = $_lib['storage']->get_hash(array('query' => $query_setup, 'key' => 'name', 'value' => 'value'));

        return $setup['localcurrency'];
    }

    function getLocalCurrency() {
		global $_lib;

        return self::getCurrencyCode();
    }

	/**
	 * @param String $currency Currency ISO code
	 * @param float $amount Amount in foreign currency
	 * @return int amount in local currency
	 */
	function convertToLocal($currency, $amount, $round=false) {
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
	function getInactiveCurrencies() {
		global $_lib;

		#Retrieve active currencies
		$query = "SELECT * FROM currency WHERE `CurrencyISO` NOT IN (SELECT `CurrencyID` FROM `exchange` WHERE CurrencyID IS NOT NULL) ORDER BY `CurrencyISO`";
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
	function getAllCurrencies() {
		global $_lib;

		#Retrieve currencies
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
		$query = "SELECT c.CurrencyISO, c.CurrencyID, e.Amount FROM currency c, exchange e WHERE c.`CurrencyISO`=`e`.`CurrencyID` ORDER BY c.`CurrencyISO`";
		$result_currency = $_lib['db']->db_query($query);
		$currencies = array();

		while($tmp = $_lib['db']->db_fetch_object($result_currency)) {
			if ($tmp)
				$currencies[$tmp->CurrencyISO] = $tmp;
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

    function validateForeignCurrencyFields($args) {
        $amount_key = 'voucher_ForeignAmount';
        $rate_key = 'voucher_ForeignConvRate';
        $currency_id_key = 'voucher_ForeignCurrencyID';

        if (empty($args[$amount_key])) {
            return false;
        }

        if (empty($args[$rate_key])) {
            return false;
        }

        if (empty($args[$currency_id_key])) {
            return false;
        }
        
        return true;
    }

	/**
	 * @return string Conversion rate
	 */
	function updateVoucherForeignCurrency($use_selection_var = true) {
		global $_lib;
        if (empty($_POST['voucher_VoucherID']) || 
            !is_numeric($_POST['voucher_VoucherID'])) {
            return false;
        }
        
        $voucher_id = $_POST['voucher_VoucherID'];

        if ($use_selection_var) {
            $currency_id_key = 'voucher_ForeignCurrencyIDSelection';
        } else {
            $currency_id_key = 'voucher_ForeignCurrencyID';
        }
        $amount_key = 'voucher_ForeignAmount';
        $rate_key = 'voucher_ForeignConvRate';

        $args = array(
                      'voucher_VoucherID' => $_POST['voucher_VoucherID'],
                      'voucher_ForeignCurrencyID' => $_POST[$currency_id_key],
                      $amount_key => $_POST[$amount_key],
                      $rate_key => $_POST[$rate_key],
                      );

        if (!self::validateForeignCurrencyFields($args)) {
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
	 * @param  float  $voucher_foreign_rate The conversion rate based in 100 of local currency
	 * @param  String $voucher_foreign_currency The currency ISO code
	 * @param  String $action_url The form action attribute
	 * @return string HTML form inside a div block. Div is initially hidden (display:none)
	 */
	function getFormVoucherForeignCurrency($voucher_id, $voucher_foreign_amount, $voucher_foreign_rate, $voucher_foreign_currency, $action_url='', $has_save_button = true, $has_direction_radio = true) {
        if ($voucher_id == "") {
            $voucher_id_text = "newvoucher"; // set to new to make js work
        } else {
            $voucher_id_text = "";
        }

        if ($action_url == '')
			$action_url = 'lodo.php?'. $_SERVER['QUERY_STRING'];
		$currencies = self::getActiveCurrencies();

        if (empty($currencies)) {
            return "";
        }

        $select_options = '<option value="">Standard</option>';
        foreach ($currencies as $currency) {
            if ($voucher_foreign_currency && $currency->CurrencyISO == $voucher_foreign_currency)
                $select_options .= '<option value="'. $currency->CurrencyISO .'" selected="selected" onchange="onCurrencyChange(this, \''. $voucher_id_text . '\')" onclick="onCurrencyChange(this, \''. $voucher_id_text . '\')">'. $currency->CurrencyISO .'</option>';
            else
                $select_options .= '<option value="'. $currency->CurrencyISO .'" onchange="onCurrencyChange(this, \''. $voucher_id_text . '\')" onclick="onCurrencyChange(this, \''. $voucher_id_text . '\')">'. $currency->CurrencyISO .'</option>';
        }

        // we create one currency array for each voucher to allow for specific rates to be tied to specific accountplans or dates (which might be needed in the future) in reports
        $currency_js = '<script type="text/javascript">';
        // $currency_js .= 'if (!window.currency_rates) var currency_rates = new Object();';

        $currency_js .= 'window.currency_rates[\''. $voucher_id_text . '\'] = new Object();';
        foreach ($currencies as $currency) {
            $currency_js .= 'window.currency_rates[\''. $voucher_id_text . '\'][\'' . $currency->CurrencyISO . '\'] = ' . $currency->Amount . ';';
        } 
        $currency_js .= '</script>';
        $ch_curr .= $currency_js;        

        $block_return = 'onKeyPress="return disableEnterKey(event)"';
        $ch_curr  .= '<div style="display:none;" class="vouchercurrencywrapper" id="voucher_currency_div_'. $voucher_id_text .'">';
        $ch_curr .= 'Valuta: <select name="voucher.ForeignCurrencyID" ' . $block_return . '>'. $select_options .'"</select><br />';
        $ch_curr .= 'Verdi: <input class="number" type="text" name="voucher.ForeignAmount" size="10" value="'. $voucher_foreign_amount .'" ' . $block_return . ' style="margin-bottom: 3px;"/>';
        if ($has_direction_radio) {
            $ch_curr .= '<input type="radio" name="voucher_ForeignCurrencyDirection" value="in">Inn';
            $ch_curr .= '<input type="radio" name="voucher_ForeignCurrencyDirection" value="out" checked>Ut';
        }
        $ch_curr .= '<br />';
        $ch_curr .= 'Rate: <input class="number" type="text" name="voucher.ForeignConvRate" size="10" value="'. $voucher_foreign_rate .'" ' . $block_return . ' /> =100' . self::getLocalCurrency();
        $ch_curr .= ' <a href="#" onclick="exchangeFindRate(this)" style="display: inline">finn kurs </a><br />';
        $ch_curr .= '<input class="number" type="hidden" name="voucher.VoucherID" value="'. $voucher_id .'" />';
        $ch_curr .= '<input class="number" type="hidden" name="voucher.ForeignCurrencyIDSelection" value="" />';
        if ($has_save_button) {
            $ch_curr .= '<input type="hidden" name="action_postmotpost_save_currency" value="1" />';
            $ch_curr .= '<input type="button" name="action_postmotpost_save_currency_button" onclick="return voucherCurrencyChange(this, \'' . $action_url . '\'); " value="Lagre" />';
        }
        $ch_curr .= '</div>';
		
		return $ch_curr;
	}


	/**
	 * @param  int    $voucher_id The voucher id
	 * @param  String $link_txt
	 * @return string HTML anchor to use with exchange::getFormVoucherForeignCurrency()
	 */
	function getAnchorVoucherForeignCurrency($voucher_id, $link_txt='Velg valuta') {
        if ($voucher_id == "") {
            $voucher_id_text = "newvoucher"; // set to new to make js work
        }

        return '<a href="#" onClick="toggle(\'voucher_currency_div_'. $voucher_id_text .'\');return false;">'. $link_txt .'</a>';
	}

    /**
     * queries Google for the current exchange rate, doesn't need cURL
     * 
     * @param mixed  $amount
     * @param string $currency
     * @param string $exchangeIn
     * @return mixed
     * @author Adapted from Tudor Barbu
     * @copyright MIT 
     */
    static function googleExchangeRateUrl($amount, $currency, $exchangeIn)
    {

        $googleQuery = $amount . ' ' . $currency . ' in ' . $exchangeIn;
        $googleQuery = urlEncode( $googleQuery );
        return 'http://www.google.com/search?q=' . $googleQuery;
    }
}
?>