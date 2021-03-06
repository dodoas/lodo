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
	static function getAllCurrencies() {
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
		$query = "SELECT c.CurrencyISO, c.CurrencyID, e.Amount FROM currency c, exchange e WHERE c.`CurrencyISO`=`e`.`CurrencyID` AND e.Amount != '' AND e.Amount != 0 AND e.Amount is not null ORDER BY c.`CurrencyISO`";
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

        if (empty($args[$amount_key]) && empty($args[$currency_id_key])) {
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
	function updateJournalForeignCurrency() {
        global $_lib;
        if (empty($_POST['voucher_JournalID']) ||
            !is_numeric($_POST['voucher_JournalID'])) {
            return false;
        }

        // added so journal currency update can work when we call it, after currency change is detected for journal on single voucher line save
        if (empty($_POST['voucher_VoucherType']) && !empty($_POST['VoucherType'])) $_POST['voucher_VoucherType'] = $_POST['VoucherType'];
        if (empty($_POST['voucher_VoucherType']) ||
            strlen($_POST['voucher_VoucherType']) != 1) {
            return false;
        }

        $journal_id = $_POST['voucher_JournalID'];
        $voucher_type = $_POST['voucher_VoucherType'];
        $currency_id = $_REQUEST['voucher_ForeignCurrencyID'];

        $rate = $_POST['voucher_ForeignConvRate'] = str_replace(',', '.', $_POST['voucher_ForeignConvRate']);
        $escaped_rate = $_lib['db']->db_escape($rate);

        $query_update = " UPDATE voucher SET ".
                        "  ForeignCurrencyID = '". $_lib['db']->db_escape($currency_id) . "'".
                        ", ForeignConvRate = '". $escaped_rate ."'".
                        ", ForeignAmount = IF(AmountIn > 0, AmountIn * ". $escaped_rate ." / 100, IF(AmountOut > 0, AmountOut * ". $escaped_rate ." / 100, 0))".
                        " WHERE JournalID = '". $journal_id ."'".
                        " AND VoucherType = '". $voucher_type ."'";

        $_lib['db']->db_update($query_update);
	}

	/**
	 * @return string Conversion rate
	 */
	function updateVoucherForeignCurrency() {
		global $_lib;

        if (empty($_POST['voucher_VoucherID']) ||
            !is_numeric($_POST['voucher_VoucherID'])) {
            return false;
        }

        $voucher_id = $_POST['voucher_VoucherID'];

        $currency_id_key = 'voucher_ForeignCurrencyID';
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
	function getFormVoucherForeignCurrency($voucher_id, $voucher_foreign_amount_in, $voucher_foreign_amount_out, $voucher_foreign_rate, $voucher_foreign_currency) {
        global $_lib;
        if ($voucher_id == "") {
            $voucher_id_text = "newvoucher"; // set to new to make js work
        } else {
            $voucher_id_text = "";
        }

        if (empty($voucher_foreign_currency)) {
            $display = "display:none;";
        } else {
            $display = "";
        }

        $block_return = 'onKeyPress="return disableEnterKey(event)"';
        $onchange_action_in = 'onChange="return calculateFromForeignAmount(this, true)"';
        $onchange_action_out = 'onChange="return calculateFromForeignAmount(this, false)"';
        $ch_curr  .= '<div style="' . $display . '" class="vouchercurrencywrapper" id="voucher_line_currency_div_'. $voucher_id_text .'">';
        $ch_curr .= 'Inn: <input class="number" type="text" name="voucher.ForeignAmountIn" size="10" value="'. $_lib['format']->Amount($voucher_foreign_amount_in) .'" ' . $block_return . ' ' . $onchange_action_in . ' style="margin-bottom: 3px;"/>';
        $ch_curr .= 'Ut: <input class="number" type="text" name="voucher.ForeignAmountOut" size="10" value="'. $_lib['format']->Amount($voucher_foreign_amount_out) .'" ' . $block_return . ' ' . $onchange_action_out . ' style="margin-bottom: 3px;"/>';

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

    function getCurrenciesInJS() {
      $currency_js = '';
      $currencies = self::getActiveCurrencies();
      if (!empty($currencies)) {            
        $currency_js .= '<script type="text/javascript">';
        $currency_js .= 'window.currency_rates = new Object();';
        foreach ($currencies as $currency) {
          $currency_js .= 'window.currency_rates[\'' . $currency->CurrencyISO . '\'] = ' . $currency->Amount . ';';
        }
        $currency_js .= '</script>';
      }
      return $currency_js;
    }
}
?>
