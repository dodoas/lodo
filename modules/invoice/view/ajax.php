<?
$InvoiceID = $_lib['input']->getProperty('InvoiceID');
$CustomerAccountPlanID  = (int) $_POST["invoiceout_CustomerAccountPlanID_$InvoiceID"];
$VoucherType            = 'S';

includelogic('invoice/invoice');
includelogic('accounting/accounting');
// needed in some of the invoice class actions
$accounting = new accounting();
$invoice = new invoice(array('CustomerAccountPlanID' => $CustomerAccountPlanID, 'VoucherType' => $VoucherType, 'InvoiceID' => $InvoiceID));

function generateLineControlVoucherSQL($amount) {
  global $voucher_ids, $InvoiceID;

  $voucher_restrict_part = '';
  if (!empty($voucher_ids)) {
    $voucher_restrict_part = " v.VoucherID NOT IN (" . implode(', ', $voucher_ids) . ") AND ";
  }
  $query_lines = "
    SELECT
      *
    FROM
      voucher v
        JOIN
        accountplan ap
        ON
          ap.AccountPlanID = v.AccountPlanID AND
          ap.EnableReskontro = 0
    WHERE
      $voucher_restrict_part
      (
        v.AmountIn = $amount OR
        v.AmountOut = $amount
      ) AND
      v.JournalID = $InvoiceID AND
      v.VoucherType = 'S' AND
      v.Active = 1
  ";
  return $query_lines;
}

if($_lib['input']->getProperty('action_invoice_outlinedelete')) {
    $LineID = $_lib['input']->getProperty('LineID');
    $invoice->linedelete($LineID);
    // set who updated and when, since we removed an invoice line
    $invoice->update(array('invoiceout_UpdatedByPersonID_' . $InvoiceID => $_lib['sess']->get_person('PersonID'), 'invoiceout_UpdatedAt_' . $InvoiceID => strftime("%F %T")));
}
elseif($_lib['input']->getProperty('action_invoice_linenew')) {
    // set who updated and when, since we added an invoice line
    $invoice->update(array('invoiceout_UpdatedByPersonID_' . $InvoiceID => $_lib['sess']->get_person('PersonID'), 'invoiceout_UpdatedAt_' . $InvoiceID => strftime("%F %T")));
    $NewLineID = $invoice->linenew();
    echo "<span id=\"line_id\">$NewLineID</span>";
}
// Remove an allowance/charge from invoice line
elseif($_lib['input']->getProperty('action_invoiceline_allowance_charge_delete')) {
    $InvoiceLineAllowanceChargeID = $_lib['input']->getProperty('InvoiceLineAllowanceChargeID');
    $invoice->line_allowance_charge_delete($InvoiceLineAllowanceChargeID);
    // set who updated and when, since we removed an invoice line allowance/charge
    $invoice->update(array('invoiceout_UpdatedByPersonID_' . $InvoiceID => $_lib['sess']->get_person('PersonID'), 'invoiceout_UpdatedAt_' . $InvoiceID => strftime("%F %T")));
}
// Remove an allowance/charge from invoice
elseif($_lib['input']->getProperty('action_invoice_allowance_charge_delete')) {
    $InvoiceAllowanceChargeID = $_lib['input']->getProperty('InvoiceAllowanceChargeID');
    $invoice->allowance_charge_delete($InvoiceAllowanceChargeID);
    // set who updated and when, since we removed an allowance/charge
    $invoice->update(array('invoiceout_UpdatedByPersonID_' . $InvoiceID => $_lib['sess']->get_person('PersonID'), 'invoiceout_UpdatedAt_' . $InvoiceID => strftime("%F %T")));
}
// Add an allowance/charge to invoice
elseif($_lib['input']->getProperty('action_invoice_allowance_charge_new')) {
    // set who updated and when, since we added an allowance/charge
    $invoice->update(array('invoiceout_UpdatedByPersonID_' . $InvoiceID => $_lib['sess']->get_person('PersonID'), 'invoiceout_UpdatedAt_' . $InvoiceID => strftime("%F %T")));
    $InvoiceAllowanceChargeID = $invoice->allowance_charge_new();
    echo "<span id=\"allowance_charge_id\">$InvoiceAllowanceChargeID</span>";
}
// Add an allowance/charge to invoice line
elseif($_lib['input']->getProperty('action_invoiceline_allowance_charge_new')) {
    $InvoiceLineID = $_lib['input']->getProperty('InvoiceLineID');
    // set who updated and when, since we added a line allowance/charge
    $invoice->update(array('invoiceout_UpdatedByPersonID_' . $InvoiceID => $_lib['sess']->get_person('PersonID'), 'invoiceout_UpdatedAt_' . $InvoiceID => strftime("%F %T")));
    $InvoiceLineAllowanceChargeID = $invoice->line_allowance_charge_new($InvoiceLineID);
    echo "<span id=\"line_allowance_charge_id\">$InvoiceLineAllowanceChargeID</span>";
}
// Line control detailed check
elseif($_lib['input']->getProperty('action_invoice_detailed_line_control_check')) {
  $voucher_ids = array();
  $query_journal_vouchers = "
    SELECT
      v.*
    FROM
      voucher v
      JOIN
      accountplan ap
      ON
        v.AccountPlanID = ap.AccountPlanID
    WHERE
      -- Exclude the hovedbok lines
      ap.EnableReskontro = 0 AND
      v.VoucherType = 'S' AND
      v.Active = 1 AND
      v.JournalID = $InvoiceID
    ";
  $result_journal_vouchers = $_lib['db']->db_query($query_journal_vouchers);
  $journal_vouchers = array();
  while($voucher = $_lib['db']->db_fetch_object($result_journal_vouchers)){
    $journal_vouchers[$voucher->VoucherID] = $voucher;
  }
  $messages = array();

  // Check if total differs
  $query_line_control_total_differs = "
    SELECT
      v.VoucherID,
      IF(i.TotalCustPrice = v.AmountIn OR i.TotalCustPrice = v.AmountOut, 0, 1) AS TotalDiffers
    FROM
      invoiceout i
      JOIN
      voucher v
      ON
        i.InvoiceID = $InvoiceID AND
        i.InvoiceID = v.JournalID AND
        v.VoucherType = 'S' AND
        v.Active = 1
      JOIN
      accountplan ap
      ON
        v.AccountPlanID = ap.AccountPlanID AND
        ap.AccountPlanType = 'customer' AND
        ap.EnableReskontro = 0
    ORDER BY
      VoucherID
    LIMIT 1
  ";
  $line_control_total_differs = $_lib['db']->get_row(
    array(
      'query' => $query_line_control_total_differs
    )
  );
  if ($line_control_total_differs->TotalDiffers) {
    // Total differs
    array_push($messages, 'Totaldifferanse');
  } else {
    // TOTAL Matches
    array_push($voucher_ids, $line_control_total_differs->VoucherID);
    unset($journal_vouchers[$line_control_total_differs->VoucherID]);
  }

  // Check if lines and their VAT differ
  $lines_differ = false;
  $vat_lines_differ = false;
  $query_invoice_lines = "
    SELECT
      il.*,
      ROUND(
        (
          SELECT
            IFNULL(
              SUM(
                IF(
                  ilac.ChargeIndicator = 1,
                  ilac.Amount,
                  -ilac.Amount
                )
              ),
              0
            )
          FROM
            invoicelineallowancecharge ilac
          WHERE
            ilac.AllowanceChargeType = 'line' AND
            ilac.InvoiceType = 'out' AND
            ilac.InvoiceLineID = il.LineID
        ),
        2
      ) AS InvoiceLineAllowanceChargeTotal
    FROM
      invoiceoutline il
    WHERE
      il.Active = 1 AND
      il.InvoiceID = $InvoiceID
    ";
  $result_invoice_lines = $_lib['db']->db_query($query_invoice_lines);
  while($invoice_line = $_lib['db']->db_fetch_object($result_invoice_lines)){
    $line_amount = round($invoice_line->QuantityDelivered * $invoice_line->UnitCustPrice + $invoice_line->InvoiceLineAllowanceChargeTotal, 2);
    $line_vat_amount = round($invoice_line->Vat/100 * $line_amount, 2);
    $line_amount = round($line_amount + $line_vat_amount, 2);
    $query_line = generateLineControlVoucherSQL($line_amount);
    $line = $_lib['db']->get_row(
      array(
        'query' => $query_line
      )
    );
    if ($line) {
      // LINE Matches
      array_push($voucher_ids, $line->VoucherID);
      unset($journal_vouchers[$line->VoucherID]);
    } else {
      $lines_differ = true;
    }
    $query_vat_lines = generateLineControlVoucherSQL($line_vat_amount);
    $result_vat_lines = $_lib['db']->db_query($query_vat_lines);
    $vat_line_found = false;
    $counterpart_vat_line_found = false;
    while($vat_line = $_lib['db']->db_fetch_object($result_vat_lines)){
      if ($vat_line) {
        // LINE VAT Matches
        if (!$vat_line_found && $vat_line->AmountIn > 0) {
          $vat_line_found = true;
        }
        if (!$counterpart_vat_line_found && $vat_line->AmountOut > 0) {
          $counterpart_vat_line_found = true;
        }
        array_push($voucher_ids, $vat_line->VoucherID);
        unset($journal_vouchers[$vat_line->VoucherID]);
        // Stop when both VAT line and its counterpart are found
        // Solves the issue if there are multiple lines with the same VAT amounts
        if ($vat_line_found && $counterpart_vat_line_found) {
          break;
        }
      }
    }
    if (!$vat_line_found || !$counterpart_vat_line_found) {
      $vat_lines_differ = true;
    }
  }
  if ($lines_differ) {
    // One or more lines differ
    array_push($messages, 'En eller flere linjer er forskjellige');
  }
  if ($vat_lines_differ) {
    // One or more VAT lines differ
    array_push($messages, 'En eller flere MVA linjer er forskjellige');
  }

  // Check if invoice allowances/charges and their VAT differ
  $allowances_charges_differ = false;
  $allowances_charges_vat_differ = false;
  $query_allowances_charges = "
    SELECT
      iac.*
    FROM
      invoiceallowancecharge iac
    WHERE
      iac.InvoiceType = 'out' AND
      iac.InvoiceID = $InvoiceID
    ";
  $result_allowances_charges = $_lib['db']->db_query($query_allowances_charges);
  while($allowance_charge = $_lib['db']->db_fetch_object($result_allowances_charges)){
    $ac_amount = abs(round($allowance_charge->Amount, 2));
    $ac_vat_amount = round($allowance_charge->VatPercent/100 * $ac_amount, 2);
    $ac_amount = round($ac_amount + $ac_vat_amount, 2);
    $query_line = generateLineControlVoucherSQL($ac_amount);
    $line = $_lib['db']->get_row(
      array(
        'query' => $query_line
      )
    );
    if ($line) {
      // AC Matches
      array_push($voucher_ids, $line->VoucherID);
      unset($journal_vouchers[$line->VoucherID]);
    } else {
      $allowances_charges_differ = true;
    }
    $query_vat_lines = generateLineControlVoucherSQL($ac_vat_amount);
    $result_vat_lines = $_lib['db']->db_query($query_vat_lines);
    $vat_line_found = false;
    $counterpart_vat_line_found = false;
    while($vat_line = $_lib['db']->db_fetch_object($result_vat_lines)){
      if ($vat_line) {
        // AC VAT Matches
        if (!$vat_line_found && $vat_line->AmountIn > 0) {
          $vat_line_found = true;
        }
        if (!$counterpart_vat_line_found && $vat_line->AmountOut > 0) {
          $counterpart_vat_line_found = true;
        }
        array_push($voucher_ids, $vat_line->VoucherID);
        unset($journal_vouchers[$vat_line->VoucherID]);
        // Stop when both VAT line and its counterpart are found
        // Solves the issue if there are multiple lines with the same VAT amounts
        if ($vat_line_found && $counterpart_vat_line_found) {
          break;
        }
      }
    }
    if (!$vat_line_found || !$counterpart_vat_line_found) {
      $allowances_charges_vat_differ = true;
    }
  }
  if ($allowances_charges_differ) {
    // One or more allowances/charges differ
    array_push($messages, 'En eller flere rabatt/kostnader er forskjellige');
  }
  if ($allowances_charges_vat_differ) {
    // One or more VATs for allowances/charges differ
    array_push($messages, 'En eller flere MVA rabatt/kostnader er forskjellige');
  }
  if (!empty($journal_vouchers)) {
    // Extra lines on journal
    array_push($messages, 'Ekstra Linjer p&aring; billaget');
  }
  if (empty($messages)) {
    // Line control error
    array_push($messages, 'Linjekontrollfeil');
  }
  echo '<span id="line_control_details">' . implode("\n", $messages) . '</span>';
}
?>
