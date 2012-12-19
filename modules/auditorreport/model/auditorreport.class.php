<?php

class lodo_auditorreport_auditorreport
{

    function __construct($args)
    {
        global $_lib;
        #Init
        $this->PeriodYear = $args['PeriodYear'];

        if($this->PeriodYear)
        {
          $auditorReportQuery = "select * from auditorreport where PeriodYear=$this->PeriodYear";
          $this->auditorReportRow = $_lib['storage']->get_row(array('query' => $auditorReportQuery));
          $this->AuditorReportID = $this->auditorReportRow->AuditorReportID;

          $this->fix(); #Insert missing lines for newly added accounts
        }
    }


    ##############################################################
    #Function that assures that all account plans have lines
    function fix() {
        global $_lib;
        
        $balance_accounts = lodo_auditorreport_auditorreport::getBalanceAccounts($this->PeriodYear);
        while($account = $_lib['storage']->db_fetch_object($balance_accounts))
        {
            $line_query = "select count(*) as cnt from auditorreportline where AuditorReportID='$this->AuditorReportID' AND AccountPlanID = '$account->AccountPlanID'";
            $auditorReportRow = $_lib['storage']->get_row(array('query' => $line_query));
            if ($auditorReportRow->cnt == 0) { // None found so create new one
                $line_post['auditorreportline_AuditorReportID'] = $this->AuditorReportID;
                $line_post['auditorreportline_AuditAmount'] = 0;
                $line_post['auditorreportline_AccountPlanID'] = $account->AccountPlanID;

                $_lib['storage']->db_new_hash($line_post, "auditorreportline");
            }            
        }
    }

##############################################################

    function action_auditorreport_new($args)
    {
        global $_lib;

        $PeriodYear = $args['PeriodYear'];

        $post['auditorreport_PeriodYear'] = $PeriodYear;
        $auditorReportID = $_lib['storage']->db_new_hash($post, "auditorreport");

        $balance_accounts = lodo_auditorreport_auditorreport::getBalanceAccounts($_REQUEST['PeriodYear']);

        while($account = $_lib['storage']->db_fetch_object($balance_accounts))
        {
            $line_post['auditorreportline_AuditorReportID'] = $auditorReportID;
            $line_post['auditorreportline_AuditAmount'] = 0;
            $line_post['auditorreportline_AccountPlanID'] = $account->AccountPlanID;

            $_lib['storage']->db_new_hash($line_post, "auditorreportline");
        }

        return new lodo_auditorreport_auditorreport(array('PeriodYear' => $_REQUEST['PeriodYear']));
    }

    static function getBalanceAccounts($PeriodYear) {
        global $_lib;

        $_from_period   = $PeriodYear . "-01";
        $_to_period     = $PeriodYear . "-13";

        $safe_from_period = mysql_escape_string($_from_period);
        $safe_to_period = mysql_escape_string($_to_period);

        /* henter ut aktive kontoer og kontoer satt til uaktiv, men allikevel har en voucher registrert --m */
        $query_balance = "SELECT 
				A.AccountPlanID, A.AccountName, 
				A.EnableBudgetResult 
			FROM
				accountplan A,
				voucher V
			WHERE 
				( A.Active = 1 AND A.AccountPlanType = 'balance' ) 
				
				OR 
				
				( 
					-- A.Active = 0 AND 
					A.AccountPlanType = 'balance' AND
					V.AccountPlanID = A.AccountPlanID AND 
					V.VoucherPeriod >= '$safe_from_period' AND
					V.VoucherPeriod < '$safe_to_period' 
				)
			GROUP BY
				A.AccountPlanID 
			ORDER BY 
				A.AccountPlanID ASC";

        return $balance_accounts = $_lib['db']->db_query($query_balance);
    }

    function getReportLines() {
        global $_lib;
        $query = "SELECT rl.*, a.AccountName from auditorreportline rl, accountplan a WHERE rl.AuditorReportID = '" . $this->AuditorReportID . "' AND rl.AccountPlanID = a.AccountPlanID";

        return $_lib['db']->db_query($query);
    }
    
}
