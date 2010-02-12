<?
class budget_Action
{
    private $_tables;
    private $_totalOut;
    private $_totanIn;

    private $_query;
    private $_row;
    private $_row2;
    private $_row3;
    private $_year;
    private $_value;
    private $_post;
    private $_post2;
    private $_ResultBudgetID;
    private $_LiquidityBudgetID;
    private $_budgetLinesResult;
    private $_budgetLinesLiquidity;
    private $_budgetRow;
    private $_BudgetLineId;

    ############################################

    public function __construct($args)
    {
    }

    ############################################

    public function action_budget_removeLine($args)
    {
        global $_lib;
        
        $this->_query = "update ".$args['db_table']." set Active=0 where BudgetLinesID=".$args['BudgetLinesID'];
        $this->_row   = $_lib['db']->db_update($this->_query);
    }

    ############################################

    #periodYear'=>$periodYear, 'budgetType'=>$budgetType, 'AccountPlanID'=>$value   'db_table'
    public function action_budget_addLine($args)
    {
        global $_lib;
        
        $this->_query = "select BudgetID from budget where PeriodYear='".$args['periodYear']."' and Type='".$args['budgetType']."'";
        $this->_row = $_lib['storage']->get_row(array('query' => $this->_query));

        $this->_post2['budgetline_BudgetID'] = $this->_row->BudgetID;
        $this->_post2['budgetline_AccountPlanID'] = $args['AccountPlanID'];
        $this->_BudgetLineId = $_lib['storage']->db_new_hash($this->_post2, $args['db_table']);
    }

    ############################################

    public function action_budget_setActive($args)
    {
        global $_lib;
        $this->_query = "update ".$args['db_table']." set Active=1 where BudgetLinesID=".$args['BudgetLinesID'];
        $this->_row = $_lib['db']->db_update($this->_query);
    }

    ############################################

    #$args['db_table', 'db_table2', '_POST']
    public function action_budget_update($args)
    {
        global $_lib;
        $this->_tables[$args['db_table']] = 'BudgetID';
        $this->_tables[$args['db_table2']] = 'BudgetLinesID';

        for($j=1; $j<=$_POST['numberofrows']; $j++)
        {
            $this->_totalOut = 0;
            $this->_totalIn = 0;

            for($i=1; $i<=12; $i++)
            {
                $tmphash = $_lib['convert']->Amount(array('value'=>$args['_POST']['budgetline_Period'.$i.'Out_'.$args['_POST'][$j]]));
                $this->_totalOut += $tmphash['value'];

                $tmphash = $_lib['convert']->Amount(array('value'=>$args['_POST']['budgetline_Period'.$i.'In_'.$args['_POST'][$j]]));
                $this->_totalIn += $tmphash['value'];
            }

            $args['_POST']['budgetline_SumOut_'.$args['_POST'][$j]] = $this->_totalOut;
            $args['_POST']['budgetline_SumIn_'.$args['_POST'][$j]] = $this->_totalIn;

            unset($args['_POST'][$j]);
        }

        unset($args['_POST']['numberofrows']);

        $_lib['storage']->db_update_multi_table($args['_POST'], $this->_tables);
    }

    ############################################

    #$args['db_table', 'db_table2']
    public function action_budget_new($args)
    {
        global $_lib;
        
        /*  Getting initial values  */
        if($args['year']) {
            $this->_year = $args['year'];
        } else {
            $this->_query = "select PeriodYear from budget order by PeriodYear desc";
            $this->_row = $_lib['storage']->get_row(array('query' => $this->_query));
            $this->_year = $this->_row->PeriodYear;

            if(!$this->_year)
                $this->_year = $_lib['date']->get_this_year($_lib['sess']->get_session('LoginFormDate'));
            else
                $this->_year++;
        }

        /*  Creating Reusult header  */
        $this->_post['budget_Type'] = 'result';
        $this->_post['budget_PeriodYear'] = $this->_year;
        $this->_ResultBudgetID = $_lib['storage']->db_new_hash($this->_post, $args['db_table']);

        /*  Creating liquidity header  */
        $this->_post['budget_Type'] = 'liquidity';
        $this->_LiquidityBudgetID = $_lib['storage']->db_new_hash($this->_post, $args['db_table']);

        $this->_query = "select AccountPlanID from accountplan where EnableBudgetResult=1";
        $this->_row = $_lib['storage']->get_hash(array('query'=>$this->_query, 'key'=>'AccountPlanID', 'value'=>'AccountPlanID'));

        $this->_query = "select AccountPlanID from accountplan where EnableBudgetLikviditet=1";
        $this->_row2 = $_lib['storage']->get_hash(array('query'=>$this->_query, 'key'=>'AccountPlanID', 'value'=>'AccountPlanID'));

        $this->_year--;

        foreach($this->_row as $this->_value)
        {
            $this->_post2['budgetline_BudgetID'] = $this->_ResultBudgetID;
            $this->_post2['budgetline_AccountPlanID'] = $this->_value;

            for($i=1; $i<=12; $i++)
            {
                $this->_query = "select V.AccountPlanID, sum(V.AmountOut) as sumout, sum(V.AmountIn) as sumin, V.VoucherPeriod from voucher V, accountplan A where A.accountplanID=V.AccountPlanID and A.AccountPlanID='$this->_value' and substring(V.VoucherPeriod,1,4)='$this->_year' and substring(V.VoucherPeriod,6,2)=$i group by V.VoucherPeriod order by V.VoucherPeriod";
                $this->_row3 = $_lib['storage']->get_row(array('query' => $this->_query));

                if(!isset($this->_row3->sumin))
                    $this->_row3->sumin = 0;
                if(!isset($this->_row3->sumout))
                    $this->_row3->sumout = 0;

                $this->_post2['budgetline_Period'.$i.'In'] = $this->_row3->sumin;
                $this->_post2['budgetline_Period'.$i.'Out'] = $this->_row3->sumout;
            }

            $this->_BudgetLineId = $_lib['storage']->db_new_hash($this->_post2, $args['db_table2']);
        }

        foreach($this->_row2 as $this->_value)
        {
            $this->_post2['budgetline_BudgetID'] = $this->_LiquidityBudgetID;
            $this->_post2['budgetline_AccountPlanID'] = $this->_value;

            for($i=1; $i<=12; $i++)
            {
                $this->_query = "select V.AccountPlanID, sum(V.AmountOut) as sumout, sum(V.AmountIn) as sumin, V.VoucherPeriod from voucher V, accountplan A where A.accountplanID=V.AccountPlanID and A.AccountPlanID='$this->_value' and substring(V.VoucherPeriod,1,4)='$this->_year' and substring(V.VoucherPeriod,6,2)=$i group by V.VoucherPeriod order by V.VoucherPeriod";
                $this->_row3 = $_lib['storage']->get_row(array('query' => $this->_query));

                if(!isset($this->_row3->sumin))
                    $this->_row3->sumin = 0;
                if(!isset($this->_row3->sumout))
                    $this->_row3->sumout = 0;

                $this->_post2['budgetline_Period'.$i.'In'] = $this->_row3->sumin;
                $this->_post2['budgetline_Period'.$i.'Out'] = $this->_row3->sumout;
            }

            $this->_BudgetLineId = $_lib['storage']->db_new_hash($this->_post2, $args['db_table2']);
        }
    }
}