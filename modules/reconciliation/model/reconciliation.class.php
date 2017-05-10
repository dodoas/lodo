<?
class reconciliation {

    // Total sum
    public  $Sum;
    // Sum by account
    public  $SumAccountH = array();
    // All open vouchers
    public  $VoucherH = array();
    // All matches
    public  $MatchH = array();
    // Accounts that should not be included
    public  $HiddingAccounts = array();


    // Matched vouchers - reconcilable
    private $Matched = array();
    // HTML for Highlight links for each voucher
    private $VoucherHighlightLinksHTML = array();
    // Map of all vouchers reconciled against each other (ie. md5($VoucherID1 . "#" . $VoucherID2)
    private $CloseAgainst = array();
    // Vouchers closed while running the close algorithm
    private $ClosedVouchers = array();

    // Input paramters
    public $AccountPlanID = 0;
    public $ReskontroFromAccount = 0;
    public $ReskontroToAccount = 0;
    public $DepartmentID = 0;
    public $ProjectID = 0;

    function __construct($args)
    {
        foreach ($args as $key => $value) {
            $this->{$key} = $value;
        }
    }

    /**
     * Find unreconciled vouchers with specified KID number
     * Does not include the voucher with specified voucher id
     */
    function findOpenPostKid($KID, $VoucherID) {
      global $_lib;

      $OpenVouchers = array();
      $Count = 0;
      $Status = 0;

      if ($KID > 0) {
        $OpenVouchersQuery = "
          SELECT
            v.JournalID,
            v.VoucherID,
            v.AmountIn,
            v.AmountOut,
            v.AccountPlanID,
            v.VoucherDate,
            v.KID,
            v.InvoiceID
          FROM
            voucher v
            JOIN
            (
              SELECT *
              FROM accountplan
              WHERE
              EnablePostPost = 1 AND
              EnableReskontro = 0
            ) ap
            ON v.AccountPlanID = ap.AccountPlanID
          WHERE
            v.KID = '" . $KID . "' AND
            v.VoucherID != $VoucherID AND
            v.VoucherReconciliationID IS NULL AND
            v.Active = 1
          ORDER BY ap.AccountName ASC";
        $OpenVouchersResult = $_lib['db']->db_query($OpenVouchersQuery);
        while ($Voucher = $_lib['db']->db_fetch_assoc($OpenVouchersResult)) {
          $OpenVouchers[$Voucher['JournalID']][$Voucher['VoucherID']] = $Voucher;
          $Count++;
        }

        if ($Count == 0) {
          $_lib['message']->add('Ingen KID funnet p&aring; noen &aring;pne bilag');
        } elseif ($Count == 1) {
          $_lib['message']->add('Fant eksakt KID matchKIDH p&aring; bilag');
          $Status = 1;
        } else {
          $_lib['message']->add('Fant flere matchKIDHende KID p&aring; ulike bilag');
        }
      } else {
        $_lib['message']->add('Ingen KID oppgitt');
      }
      return array($Status, $OpenVouchers);
    }

    /**
     * Get counter voucher data for the supplied open voucher(s)
     */
    function getKIDInfo($OpenVouchers) {
      global $_lib, $accounting;

      $Count = 0;
      $Status = 0;
      $AccountPlanID = 0;
      $AmountIn = 0;
      $AmountOut = 0;

      foreach ($OpenVouchers as $JournalID => $Journal) {
        foreach ($Journal as $VoucherID => $Voucher) {
          $AccountPlan = $accounting->get_accountplan_object($Voucher['AccountPlanID']);
          if ($AccountPlan->AccountPlanType == 'customer'
            || $AccountPlan->AccountPlanType == 'supplier')
          {
            $Count++;
            $AccountPlanID = $Voucher['AccountPlanID'];
            // Switch the amount sides to make a counter voucher
            $AmountIn = $Voucher['AmountOut'];
            $AmountOut = $Voucher['AmountIn'];
          }
        }
      }

      if ($Count == 0) {
        $_lib['message']->add('Ingen kundereskontro funnet p&aring; den oppgitte KID referansen');
      } elseif ($Count == 1) {
        $_lib['message']->add('En kundereskontro funnet p&aring; den oppgitte KID referansen');
        $Status = 1;
      } else {
        $_lib['message']->add('Flere kundereskontro funnet p&aring; den oppgitte KID referansen. Tilfeldig valg ble gjort');
      }
      return array($AccountPlanID, $AmountIn, $AmountOut, $JournalID, $VoucherID, $Status);
    }

    function generateVoucherQuery($Where, $WhereExtra, $AccountPlanRestrinctionExtra) {
      return "
        SELECT
          v.VoucherID,
          v.JournalID,
          v.AccountPlanID,
          v.InvoiceID,
          v.KID,
          v.MatchNumber,
          v.AmountIn,
          v.AmountOut,
          v.ForeignAmount,
          v.matched_by,
          v.VoucherType,
          v.VoucherDate,
          v.VoucherPeriod,
          v.Vat as VAT,
          v.Quantity,
          v.DepartmentID,
          v.ProjectID,
          v.DueDate,
          v.DescriptionID,
          v.Description,
          v.ForeignCurrencyID,
          v.ForeignConvRate
        FROM
          (
            SELECT
              *
            FROM
              voucher
            WHERE
              Active = 1 AND
              VoucherReconciliationID IS NULL
          ) v,
          accountplan ap
        WHERE
          v.AccountPlanID = ap.AccountPlanID AND
          $AccountPlanRestrinctionExtra
          $Where
          $WhereExtra
          ap.EnablePostPost = 1
        ORDER BY
          v.AccountPlanID ASC,
          v.VoucherPeriod ASC,
          v.VoucherDate ASC,
          v.VoucherID ASC";
    }

    /**
     * Gathers all unreconciled vouchers
     */
    function getopenpost($OnlyAccountPlanID = 0) {
      global $_lib, $accounting;

      // Clean old totals
      $this->total['total']->Name = 'Total';
      $this->total['total']->AmountIn = 0;
      $this->total['total']->AmountOut = 0;
      $this->total['total']->FAmountIn = 0;
      $this->total['total']->FAmountOut = 0;
      $this->VoucherH = array();
      $this->MatchH = array();
      $this->SumAccountH = array();
      $this->HiddingAccounts = array();

      if ($this->AccountPlanID) {
        $AccountPlan = $accounting->get_accountplan_object($this->AccountPlanID);
        $Where = '';
        $WhereExtra = '';
        if ($this->DepartmentID > 0) {
          $WhereExtra .= ' v.DepartmentID = ' . (int) $this->DepartmentID . ' AND ';
        }
        if ($this->ProjectID > 0) {
          $WhereExtra .= ' v.ProjectID = ' . (int) $this->ProjectID . ' AND ';
        }

        if ($this->ReskontroFromAccount) {
          $Where .= " v.AccountPlanID >= '" . $this->ReskontroFromAccount . "' AND ";
        }
        if ($this->ReskontroToAccount) {
          $Where .= " v.AccountPlanID <= '" . $this->ReskontroToAccount . "' AND ";
        }
        $Where .= " ap.AccountPlanType = '" . $AccountPlan->ReskontroAccountPlanType . "' AND ";

        $VoucherQuery = self::generateVoucherQuery($Where, $WhereExtra, '');

        // Building VoucherQuery, the most time consuming part starts here(tested with benchmark)
        if ($OnlyAccountPlanID) {
          // Populate HiddingAccounts
          $HiddingAccountsQuery = "
            SELECT
              ap.AccountName,
              ap.AccountPlanID
            FROM
              accountplan ap,
              (
                SELECT
                  AccountPlanID,
                  VoucherID
                FROM voucher
                WHERE
                  Active = 1 AND
                  VoucherReconciliationID IS NULL
              ) v
            WHERE
              v.AccountPlanID = ap.AccountPlanID AND
              $Where
              $WhereExtra
              ap.EnablePostPost = 1
            GROUP BY ap.AccountPlanID";

          $HiddingAccountsResult = $_lib['db']->db_query($HiddingAccountsQuery);
          while (($Account = $_lib['db']->db_fetch_assoc($HiddingAccountsResult))) {
            $this->HiddingAccounts[] = $Account;
          }
          $VoucherQuery = self::generateVoucherQuery($Where, $WhereExtra, " v.AccountPlanID = $OnlyAccountPlanID AND ");
        }
        $VoucherResult = $_lib['db']->db_query($VoucherQuery);
        $accounting->cache_all_accountplans();

        // Loop and calculate all data
        $VouchersProcessed = array();
        while ($Voucher = $_lib['db']->db_fetch_object($VoucherResult)) {
          if ($VouchersProcessed[$Voucher->VoucherID]) {
            continue;
          }
          $VouchersProcessed[$Voucher->VoucherID] = true;

          $AccountPlanID = $Voucher->AccountPlanID;

          // Initialize for possible groups for KID, InvoiceID and MatchNumber
          if ($Voucher->KID
            && !isset($this->MatchH[$AccountPlanID]['KID'][$Voucher->KID]))
          {
            $this->MatchH[$AccountPlanID]['KID'][$Voucher->KID] = 0;
            $this->MatchH[$AccountPlanID]['KIDJournals'][$Voucher->KID] = array();
          }
          if ($Voucher->InvoiceID
            && !isset($this->MatchH[$AccountPlanID]['InvoiceID'][$Voucher->InvoiceID]))
          {
            $this->MatchH[$AccountPlanID]['InvoiceID'][$Voucher->InvoiceID] = 0;
            $this->MatchH[$AccountPlanID]['InvoiceIDJournals'][$Voucher->InvoiceID] = array();
          }
          // If there is no MatchNumber, set to 0
          if (!$Voucher->MatchNumber) {
            $Voucher->MatchNumber = 0;
          }
          if ($Voucher->MatchNumber
            && !isset($this->MatchH[$AccountPlanID]['MatchNumber'][$Voucher->MatchNumber]))
          {
            $this->MatchH[$AccountPlanID]['MatchNumber'][$Voucher->MatchNumber] = 0;
            $this->MatchH[$AccountPlanID]['MatchNumberJournals'][$Voucher->MatchNumber] = array();
          }


          // Sum up for the total
          $this->total['total']->AmountIn += $Voucher->AmountIn;
          $this->total['total']->AmountOut += $Voucher->AmountOut;
          if ($Voucher->AmountIn > 0) {
            $this->total['total']->FAmountIn += $Voucher->ForeignAmount;
          }
          if ($Voucher->AmountOut > 0) {
            $this->total['total']->FAmountOut += $Voucher->ForeignAmount;
          }

          // Initialize for each new account plan
          if (!isset($this->SumAccountH[$AccountPlanID]->Name)) {
            $TmpAccountPlan = $accounting->get_accountplan_object($Voucher->AccountPlanID);
            $this->SumAccountH[$AccountPlanID]->Name = $TmpAccountPlan->AccountPlanID." - ".$TmpAccountPlan->AccountName;
          }
          $this->SumAccountH[$AccountPlanID]->AmountIn += $Voucher->AmountIn;
          $this->SumAccountH[$AccountPlanID]->AmountOut += $Voucher->AmountOut;
          if ($Voucher->AmountIn > 0) {
            $this->SumAccountH[$AccountPlanID]->FAmountIn += $Voucher->ForeignAmount;
          }
          if ($Voucher->AmountOut > 0) {
            $this->SumAccountH[$AccountPlanID]->FAmountOut += $Voucher->ForeignAmount;
          }

          // Automatic matching
          // Try and match by KID
          if ($Voucher->KID
            && ($Voucher->matched_by == '0'
            || $Voucher->matched_by == 'kid'))
          {
            $this->MatchH[$AccountPlanID]['KID'][$Voucher->KID] =
              round($this->MatchH[$AccountPlanID]['KID'][$Voucher->KID], 3) +
              ($Voucher->AmountIn - $Voucher->AmountOut);
            $this->MatchH[$AccountPlanID]['KIDJournals'][$Voucher->KID][] =
              array(
                'JournalID' => $Voucher->JournalID,
                'VoucherID' => $Voucher->VoucherID,
                'Match' => 'KID'
              );
          }
          // Try to match by InvoiceID
          if ($Voucher->InvoiceID
            && ($Voucher->matched_by == '0'
            || $Voucher->matched_by == 'invoice'))
          {
            $this->MatchH[$AccountPlanID]['InvoiceID'][$Voucher->InvoiceID] =
              round($this->MatchH[$AccountPlanID]['InvoiceID'][$Voucher->InvoiceID], 3) +
              ($Voucher->AmountIn - $Voucher->AmountOut);
            $this->MatchH[$AccountPlanID]['InvoiceIDJournals'][$Voucher->InvoiceID][] =
              array(
                'JournalID' => $Voucher->JournalID,
                'VoucherID' => $Voucher->VoucherID,
                'Match' => 'InvoiceID'

              );
          }
          // Try to match by MatchNumber, if MatchNumber is 0 this will be skipped
          if ($Voucher->MatchNumber
            && ($Voucher->matched_by == '0'
            || $Voucher->matched_by == 'match'))
          {
            $this->MatchH[$AccountPlanID]['MatchNumber'][$Voucher->MatchNumber] =
              round($this->MatchH[$AccountPlanID]['MatchNumber'][$Voucher->MatchNumber], 3) +
              ($Voucher->AmountIn - $Voucher->AmountOut);
            $this->MatchH[$AccountPlanID]['MatchNumberJournals'][$Voucher->MatchNumber][] =
              array(
                'JournalID' => $Voucher->JournalID,
                'VoucherID' => $Voucher->VoucherID,
                'Match' => 'MatchNumber'

              );
          }

          // Data from each voucher
          if ($Voucher->AmountIn > 0) {
            $Voucher->ForeignAmountIn = $Voucher->ForeignAmount;
          }
          if ($Voucher->AmountOut > 0) {
            $Voucher->ForeignAmountOut = $Voucher->ForeignAmount;
          }
          $this->VoucherH[$AccountPlanID][$Voucher->VoucherID] = $Voucher;
        }

        // Account sum, what is actually recorded on the general ledger account
        $SaldoQuery = "
          SELECT 
            SUM(v.AmountIn) AS SumIn,
            SUM(v.AmountOut) AS SumOut,
            SUM(IF(v.AmountIn > 0, v.ForeignAmount, 0)) AS FSumIn,
            SUM(IF(v.AmountOut > 0, v.ForeignAmount, 0)) AS FSumOut
          FROM
            voucher v
          WHERE
            v.AccountPlanID = '" . $this->AccountPlanID . "' AND
            $WhereExtra
            v.Active = 1";
        $Saldo = $_lib['storage']->get_row(array('query' => $SaldoQuery));

        $this->total['account']->Name = $AccountPlan->AccountPlanID." - ".$AccountPlan->AccountName;
        $SumSaldo  = $Saldo->SumIn  - $Saldo->SumOut;
        $SumFSaldo = $Saldo->FSumIn - $Saldo->FSumOut;

        if ($SumSaldo > 0) {
          $this->total['account']->AmountIn = $SumSaldo;
          $this->total['account']->AmountOut = 0;
        } elseif ($SumSaldo < 0) {
          $this->total['account']->AmountOut = abs($SumSaldo);
          $this->total['account']->AmountIn = 0;
        }
        if ($SumFSaldo > 0) {
          $this->total['account']->FAmountIn = $SumFSaldo;
        } elseif ($SumFSaldo < 0) {
          $this->total['account']->FAmountOut = abs($SumFSaldo);
        }

        foreach ($this->SumAccountH as $AccountPlanID => $Account) {
          $this->SumAccountH[$AccountPlanID]->Diff = $Account->AmountIn  - $Account->AmountOut;
          $this->SumAccountH[$AccountPlanID]->FDiff = $Account->FAmountIn - $Account->FAmountOut;
        }

        $this->total['total']->Diff = $this->total['total']->AmountIn - $this->total['total']->AmountOut;
        $this->total['total']->FDiff = $this->total['total']->FAmountIn - $this->total['total']->FAmountOut;
        $this->calculateDiff();
      } else {
        print "Ingen kontoer er valgt<br>";
      }
      $this->markAsUsed();
    }

    /**
     * This is to find out if there is a difference between ledger and general ledger sum
     */
    function calculateDiff() {
      $this->total['diff']->Name = 'Differanse';
      $this->total['diff']->AmountIn =
        $this->total['account']->AmountIn - $this->total['total']->AmountIn;
      $this->total['diff']->AmountOut =
        $this->total['account']->AmountOut - $this->total['total']->AmountOut;
      $this->total['diff']->FAmountIn =
        $this->total['account']->FAmountIn - $this->total['total']->FAmountIn;
      $this->total['diff']->FAmountOut =
        $this->total['account']->FAmountOut - $this->total['total']->FAmountOut;

      $this->total['diff']->Diff =
        round($this->total['diff']->AmountIn - $this->total['diff']->AmountOut, 2);
      $this->total['diff']->FDiff =
        round($this->total['diff']->FAmountIn - $this->total['diff']->FAmountOut, 2);

      $this->total['account']->Diff =
        round($this->total['account']->AmountIn - $this->total['account']->AmountOut, 2);
      $this->total['account']->FDiff =
        round($this->total['account']->FAmountIn - $this->total['account']->FAmountOut, 2);
    }

    /**
     * Goes through the potential matches and if the sum of the match group is 0 and
     * all of the vouchers for this group are not marked as used, mark them
     */
    function markAsUsed() {
      $MatchOrder = array("InvoiceID", "KID", "MatchNumber");

      // for each account
      foreach ($this->MatchH as $AccountPlanID => $Matches) {
        foreach ($MatchOrder as $MatchedBy) {
          // If nothing set for this group, skip it
          if (!isset($this->MatchH[$AccountPlanID][$MatchedBy])) {
            continue;
          }

          foreach ($Matches[$MatchedBy] as $MatchKey => $MatchKeySum) {
            // If the sum is set and it is 0
            if (isset($this->MatchH[$AccountPlanID][$MatchedBy][$MatchKey])
              && round($MatchKeySum, 2) == 0)
            {
              // Check if any of the vouchers were used already and set their highlight link HTML string
              $Used = false;
              $HighlightLinkHTML = array();
              foreach ($Matches[$MatchedBy."Journals"][$MatchKey] as $VoucherListEntry) {
                $JournalID = $VoucherListEntry['JournalID'];
                $VoucherID = $VoucherListEntry['VoucherID'];
                if ($this->Matched[$VoucherID]) {
                  $Used = true;
                }
                $HighlightLinkHTML[] = "<span class=\"navigate to\" id=\"$VoucherID\">$JournalID</span>";
              }
              // None of the vouchers in the group were used previously
              if ($Used === false) {
                // Mark all the vouchers in the group as used
                foreach ($Matches[$MatchedBy."Journals"][$MatchKey] as $VoucherListEntry) {
                  $JournalID = $VoucherListEntry['JournalID'];
                  $VoucherID = $VoucherListEntry['VoucherID'];
                  $this->Matched[$VoucherID] = true;
                  $this->VoucherHighlightLinksHTML[$VoucherID] .= "$MatchedBy ( " . implode(' ', $HighlightLinkHTML) . " )";
                  $this->CloseAgainst[$VoucherID] = $Matches[$MatchedBy."Journals"][$MatchKey];
                }
                // Some of the vouchers were previously used so add a double match info and links
              } else {
                foreach ($Matches[$MatchedBy."Journals"][$MatchKey] as $VoucherListEntry) {
                  $JournalID = $VoucherListEntry['JournalID'];
                  $VoucherID = $VoucherListEntry['VoucherID'];
                  if (!$this->Matched[$VoucherID]) {
                    $this->VoucherHighlightLinksHTML[$VoucherID] .= "Dobbelmatch$MatchedBy ( " . implode(' ', $HighlightLinkHTML) . " )";
                  }
                }
              }
            }
          }
        }
      }
    }

    /**
     * Returns true if the group determined by any of the supplied parameters is closable/matchable
     */
    function isClosable($AccountPlanID, $KID, $InvoiceID, $MatchNumber = NULL, $Voucher = NULL) {
      $Success = false;
      $KID = trim($KID);
      $InvoiceID = trim($InvoiceID);
      $MatchNumber = trim($MatchNumber);

      // If the sum for the supplied InvoiceID is set and 0 the group is closable by InvoiceID
      if ($InvoiceID && !$Success) {
        $Success = isset($this->MatchH[$AccountPlanID]['InvoiceID'][$InvoiceID])
          && round($this->MatchH[$AccountPlanID]['InvoiceID'][$InvoiceID], 2) == 0;
      }
      // If the sum for the supplied KID is set and 0 the group is closable by KID
      if ($KID && !$Success) {
        $Success = isset($this->MatchH[$AccountPlanID]['KID'][$KID])
          && round($this->MatchH[$AccountPlanID]['KID'][$KID], 2) == 0;
      }
      // If the sum for the supplied MatchNumber is set and 0 the group is closable by MatchNumber
      if ($MatchNumber && !$Success) {
        $Success = isset($this->MatchH[$AccountPlanID]['MatchNumber'][$MatchNumber])
          && round($this->MatchH[$AccountPlanID]['MatchNumber'][$MatchNumber], 2) == 0;
      }

      // If the Voucher is supplied it is closeable if its matched_by field is any
      // of the following values ('invoice', 'kid', 'match')
      if (!is_null($Voucher)) {
        if (in_array($Voucher->matched_by, array('invoice', 'kid', 'match'))) {
          $Success = true;
        }
      }
      return $Success;
    }

    /**
     * Returns a number if the group determined by any of the supplied parameters is closable/matchable
     * 1 for InvoiceID
     * 2 for KID
     * 3 for MatchNumber
     */
    function isMatchable($AccountPlanID, $KID, $InvoiceID, $MatchNumber, $Voucher) {
      $Success = 0;
      $KID = trim($KID);
      $InvoiceID = trim($InvoiceID);
      $MatchNumber = trim($MatchNumber);

      // If Voucher's matched_by field is set it will override any automatic match
      // to the selected matched_by's return value
      if (in_array($Voucher->matched_by, array('invoice', 'kid', 'match'))) {
        switch ($Voucher->matched_by) {
          case 'invoice':
            $Success = 1;
            break;
          case 'kid':
            $Success = 2;
            break;
          case 'match':
            $Success = 3;
            break;
        }
      // Else check if there is a match by any of the types
      } else {
        // If the sum for the supplied InvoiceID is set and 0 the group is closable by InvoiceID
        if ($InvoiceID && !$Success) {
          if (isset($this->MatchH[$AccountPlanID]['InvoiceID'][$InvoiceID])
            && round($this->MatchH[$AccountPlanID]['InvoiceID'][$InvoiceID], 2) == 0)
          {
            $Success = 1;
          }
        }
        // If the sum for the supplied KID is set and 0 the group is closable by KID
        if ($KID && !$Success) {
          if (isset($this->MatchH[$AccountPlanID]['KID'][$KID])
            && round($this->MatchH[$AccountPlanID]['KID'][$KID], 2) == 0)
          {
            $Success = 2;
          }
        }
        // If the sum for the supplied MatchNumber is set and 0 the group is closable by MatchNumber
        if ($MatchNumber && !$Success) {
          if (isset($this->MatchH[$AccountPlanID]['MatchNumber'][$MatchNumber])
            && round($this->MatchH[$AccountPlanID]['MatchNumber'][$MatchNumber], 2) == 0)
          {
            $Success = 3;
          }
        }
      }

      return $Success;
    }

    /**
     * Is the Voucher already matched
     */
    function isClosableVoucher($VoucherID) {
      return $this->Matched[$VoucherID];
    }

    /**
     * Get all highlight links for the VoucherID
     */
    function voucherMessage($VoucherID) {
      return $this->VoucherHighlightLinksHTML[$VoucherID];
    }


    /**
     * Get diff for a group with the supplied paramters
     */
    function getDiff($AccountPlanID, $KID, $InvoiceID, $MatchNumber = NULL, $Voucher = NULL) {
      $KID = trim($KID);
      $InvoiceID = trim($InvoiceID);
      $MatchNumber = trim($MatchNumber);

      // If a Voucher is supplied get the diff for only the selected matched_by option if set,
      // if not set just get the amount diff for that voucher
      if (!is_null($Voucher)) {
        if ($KID && $Voucher->matched_by == 'kid') {
          $value = $this->MatchH[$AccountPlanID]['KID'][$KID];
        } elseif ($InvoiceID && $Voucher->matched_by == 'invoice') {
          $value = $this->MatchH[$AccountPlanID]['InvoiceID'][$InvoiceID];
        } elseif ($MatchNumber && $Voucher->matched_by == 'match') {
          $value = $this->MatchH[$AccountPlanID]['MatchNumber'][$MatchNumber];
        } else {
          $value = $Voucher->AmountIn - $Voucher->AmountOut;
        }
      }

      return $value;
    }

    /**
     * Unreconcile the voucher with the supplied VoucherID and all other vouchers it is reconciled against
     */
    function openPost($VoucherID) {
      global $_lib;

      if ($VoucherID > 0) {
        $query_select_reconciliation_id = "
          SELECT
            VoucherReconciliationID
          FROM
            voucher
          WHERE
            VoucherID = $VoucherID";
        $result_reconciliation_id = $_lib['db']->db_query($query_select_reconciliation_id);
        $row = $_lib['db']->db_fetch_object($result_reconciliation_id);
        $reconciliation_id = $row->VoucherReconciliationID;
        if ($reconciliation_id) {
          // Remove reconciliation links on all vouchers in this reconciliation
          $update_Voucher_reconciliation_query = "
            UPDATE
              voucher
            SET
              VoucherReconciliationID = NULL
            WHERE
              VoucherReconciliationID = $reconciliation_id";
          $_lib['db']->db_update($update_Voucher_reconciliation_query);
          // Remove the reconciliation
          $query_delete_reconciliation = "
            DELETE FROM
              voucherreconciliation
            WHERE
              ID = $reconciliation_id";
          $_lib['db']->db_delete($query_delete_reconciliation);
        }
      } else {
        // print "openPost linjenummer ikke angitt";
      }
    }

    /**
     * Unreconcile all vouchers that belong to a journal and the vouchers they are reconciled against
     */
    function openPostJournal($JournalID, $VoucherType) {
      global $_lib;

      $VoucherQuery = "
        SELECT
          VoucherID
        FROM
          voucher
        WHERE
          JournalID = $JournalID AND
          VoucherType = '$VoucherType' AND
          Active = 1";
      $VoucherResult = $_lib['db']->db_query($VoucherQuery);
      while ($Voucher = $_lib['db']->db_fetch_object($VoucherResult)){
        $this->openPost($Voucher->VoucherID);
      }
    }

    /**
     * Reconcile a voucher only if its group sums up to 0
     */
    function closePost($MatchAccountPlanID, $KID, $InvoiceID) {
      global $_lib;
      // Set to anything different from 0 (here we chose 1) since if Balance is 0 the group can be reconciled
      $Balance = 1;

      // If no KID or InvoiceID, add error message
      if (!$KID && !$InvoiceID) {
        $_lib['message']->add('Ingen KID eller fakturanummer');
        return false;
      }

      if ($KID && $Balance != 0) {
        $VoucherQuery = "
          SELECT
            *
          FROM
            voucher
          WHERE
            AccountPlanID = $MatchAccountPlanID AND
            KID = '$KID' AND
            VoucherReconciliationID IS NULL AND
            Active = 1
          ORDER BY
            VoucherDate DESC";
        // Order by date to always choose the newest if there is more than one
        list($Balance, $AmountInH, $AmountOutH) = $this->closeDataStructure($VoucherQuery);
      }

      // If balance is 0 we don't need to try to close it based on InvoiceID since it is closed with KID already
      if ($InvoiceID && $Balance != 0) {
        $VoucherQuery = "
          SELECT
            *
          FROM
            voucher
          WHERE
            AccountPlanID = $MatchAccountPlanID AND
            VoucherReconciliationID IS NULL AND
            InvoiceID = '$InvoiceID' AND
            Active = 1
          ORDER BY
            VoucherDate DESC";
        // Order by date to always choose the newest if there is more than one
        list($Balance, $AmountInH, $AmountOutH) = $this->closeDataStructure($VoucherQuery);
      }

      if ($Balance == 0 &&
          (count($AmountInH) + count($AmountOutH)) > 1)
      {
        if (count($AmountInH) >= count($AmountOutH)) {
          foreach ($AmountOutH as $VoucherID1 => $ValueOut) {
            foreach ($AmountInH as $VoucherID2 => $ValueIn) {
              $this->closePostSQL($VoucherID1, $VoucherID2);
            }
          }
        } elseif (count($AmountInH) < count($AmountOutH)) {
          foreach ($AmountInH as $VoucherID1 => $ValueIn) {
            foreach ($AmountOutH as $VoucherID2 => $ValueOut)
            {
              $this->closePostSQL($VoucherID1, $VoucherID2);
            }
          }
        }
      }
    }

    /**
     * Reconcile voucher against its matches
     */
    function closeVoucher($AccountPlanID, $VoucherID) {
      // If no info in CloseAgainst, get all unreconciled vouchers
      if (count($this->CloseAgainst) == 0) {
        $this->getopenpost();
      }

      foreach ($this->CloseAgainst[$VoucherID] as $VoucherListEntry) {
        $VoucherID2 = $VoucherListEntry['VoucherID'];

        // Don't close against itself
        if ($VoucherID2 == $VoucherID) {
          continue;
        }

        $hash1 = md5($VoucherID . "#" . $VoucherID2);
        $hash2 = md5($VoucherID2 . "#" . $VoucherID);

        // Don't reclose/rereconcile vouchers
        if (isset($this->ClosedVouchers[$hash1])
          || isset($this->ClosedVouchers[$hash2]))
        {
          continue;
        }

        // Register as closed/reconciled
        $this->ClosedVouchers[$hash1] = true;
        $this->ClosedVouchers[$hash2] = true;

        $this->closePostSQL($VoucherID, $VoucherID2);
        switch ($VoucherListEntry['Match']) {
          case 'KID':
            $ClosedWith = 'kid';
            break;
          case 'InvoiceID':
            $ClosedWith = 'invoice';
            break;
          case 'MatchNumber':
            $ClosedWith = 'match';
            break;
          default:
            $ClosedWith = '0';
            break;
        }
        $this->updateClosedWith($VoucherID, $VoucherID2, $ClosedWith);
      }
    }

    /**
     * Get balance and in and out amounts for vouchers fetched by the supplied query
     */
    private function closeDataStructure($VoucherQuery) {
      global $_lib;

      $VoucherResult = $_lib['db']->db_query($VoucherQuery);
      $AmountInH  = array();
      $AmountOutH = array();
      while ($Voucher = $_lib['db']->db_fetch_object($VoucherResult)) {
        if ($Voucher->AmountIn != 0) {
          $AmountInH[$Voucher->VoucherID]  += $Voucher->AmountIn;
        }
        if ($Voucher->AmountOut != 0) {
          $AmountOutH[$Voucher->VoucherID] += $Voucher->AmountOut;
        }
        $Balance += ($Voucher->AmountIn - $Voucher->AmountOut);
      }

      // We have to round the amount because of imprecision in PHP calculations
      $Balance = round($Balance, 3);
      return array($Balance, $AmountInH, $AmountOutH);
    }

    /**
     * Run SQL to close/reconcile two vouchers
     */
    private function closePostSQL($VoucherID1, $VoucherID2) {
      global $_lib;

      $VoucherQuery = "
        SELECT
          *
        FROM
          voucher
        WHERE
          VoucherID IN ($VoucherID1, $VoucherID2) AND
          VoucherReconciliationID IS NOT NULL";
      $VoucherResult = $_lib['storage']->db_query($VoucherQuery);
      if ($_lib['storage']->db_numrows($VoucherResult) < 2) {
        if ($_lib['storage']->db_numrows($VoucherResult) == 0) {
          $ReconciliationInsertQuery = "
            INSERT INTO
              voucherreconciliation(CreatedAt, CreatedBy)
            VALUES
              (NOW(), " . $_lib['sess']->get_person('PersonID') . ")";
          $ReconciliationID = $_lib['db']->db_insert($ReconciliationInsertQuery);
        } else {
          $ReconciliationIDQuery = "
            SELECT
              VoucherReconciliationID
            FROM
              voucher
            WHERE
              VoucherID IN ($VoucherID1, $VoucherID2) AND
              VoucherReconciliationID IS NOT NULL";
          $ReconciliationIDRow = $_lib['storage']->get_row(array('query' => $ReconciliationIDQuery));
          $ReconciliationID = $ReconciliationIDRow->VoucherReconciliationID;
        }
        $UpdateVoucherReconciliationIDQuery = "
          UPDATE
            voucher
          SET
            VoucherReconciliationID = $ReconciliationID
          WHERE
            VoucherID IN ($VoucherID1, $VoucherID2)";
        $_lib['db']->db_update($UpdateVoucherReconciliationIDQuery);
      } else {
        // $_lib['message']->add("Duplikatlukking: $VoucherID1:$VoucherID2");
      }
    }

    /**
     * Unreconcile all vouchers for a specific account plan.
     */
    public function openAllPostsAccount($AccountPlanID) {
      global $_lib;

      $DeleteReconciliationsQuery = "
        DELETE vr
        FROM
          voucherreconciliation vr
          INNER JOIN
          voucher v
          ON vr.ID = v.VoucherReconciliationID
        WHERE
          v.Active = 1 AND
          v.AccountPlanID = $AccountPlanID";
      $_lib['db']->db_delete($DeleteReconciliationsQuery);
      $UpdateVouchersQuery = "
        UPDATE
          voucher v
        SET
          v.VoucherReconciliationID = NULL
        WHERE
          v.Active = 1 AND
          v.AccountPlanID = $AccountPlanID";
      $_lib['db']->db_update($UpdateVouchersQuery);
    }

    /**
     * Reconcile all vouchers for a spacific account plan
     */
    public function closeAllPostsAccount($AccountPlanID) {
      global $_lib;

      if (empty($this->VoucherH[$AccountPlanID])) {
        $this->getopenpost($AccountPlanID);
      }
      $ClosableH = array();

      $Account = $this->VoucherH[$AccountPlanID];

      if (count($Account) > 0){
        foreach ($Account as $Voucher) {
          if ($this->isClosableVoucher($Voucher->VoucherID)) {
            $Close = new stdClass();
            $Close->matchAccountPlanID = $Voucher->AccountPlanID;
            $Close->matchKid = $Voucher->KID;
            $Close->matchInvoiceID = $Voucher->InvoiceID;
            $Close->VoucherID = $Voucher->VoucherID;
            $Close->AccountPlanID = $this->AccountPlanID;
            $ClosableH[] = $Close;
          }
        }
      }

      $ClosableCount = count($ClosableH);
      $_lib['message']->add("Lukker $ClosableCount bilag p&aring; $AccountPlanID som g&aring;r i null");
      if ($ClosableCount) {
        foreach ($ClosableH as $Close) {
          $this->closeVoucher($Close->matchAccountPlanID, $Close->VoucherID);
        }
      }
    }

    /**
     * Get all the main bookkeeping acounts for a specified account plan
     */
    function findMotKonto($AccountPlanID) {
      global $_lib;

      $MotkontosQuery = "
        SELECT
          MotkontoResultat1,
          MotkontoResultat2,
          MotkontoResultat3,
          MotkontoBalanse1,
          MotkontoBalanse2,
          MotkontoBalanse3
        FROM
          accountplan
        WHERE
          AccountPlanID = $AccountPlanID";
      $MotkontosResult = $_lib['db']->db_query($MotkontosQuery);
      return $_lib['db']->db_fetch_assoc($MotkontosResult);
    }

    /**
     * Reconcile all vouchers
     */
    public function closeAllPosts() {
      global $_lib;

      if (empty($this->VoucherH)) {
        $this->getopenpost();
      }

      $ClosableH = array();

      if (count($this->VoucherH) > 0){
        foreach ($this->VoucherH as $AccountPlanID => $Account) {
          foreach ($Account as $Voucher) {
            if ($this->isClosableVoucher($Voucher->VoucherID)) {
              $Close = new stdClass();
              $Close->matchAccountPlanID = $Voucher->AccountPlanID;
              $Close->matchKid = $Voucher->KID;
              $Close->matchInvoiceID = $Voucher->InvoiceID;
              $Close->VoucherID = $Voucher->VoucherID;
              $Close->AccountPlanID = $this->AccountPlanID;
              $ClosableH[] = $Close;
            }
          }
        }
      }

      $ClosableCount = count($ClosableH);
      $_lib['message']->add("Lukker $ClosableCount bilag som g&aring;r i null");
      if ($ClosableCount) {
        foreach ($ClosableH as $Close) {
          $this->closeVoucher($Close->matchAccountPlanID, $Close->VoucherID);
        }
      }
    }

    /**
     * Update what the voucher was closed with
     */
    private function updateClosedWith($VoucherID1, $VoucherID2, $ClosedWith) {
      global $_lib;

      $Values = array(
        "voucher_matched_by_$VoucherID1" => $ClosedWith,
        "voucher_matched_by_$VoucherID2" => $ClosedWith
      );
      $TablePrimaryKeys = array(
        'voucher' => 'VoucherID'
      );
      $_lib['db']->db_update_multi_table($Values, $TablePrimaryKeys);
    }

}
