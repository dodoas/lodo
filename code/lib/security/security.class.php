<?
#Acess functions wil always exit
#Allow will just check and return if you are allowed

class security
{
    var $_sess;
    var $_template;
    var $dbh;

    function security($args)
    {
        #Init
        $this->_sess        = $args['_sess'];
        $this->_template    = $args['_template'];
        $this->dbh          = $this->_sess->dbh;
    }

    #only allow internal users, all else exits
    function company_access($onlyinternal)
    {
        global $_log;
        $_sess = $this->_sess;
        #print "OI: $onlyinternal, " . $_sess->get_companydef('CompanyID') . " != " . $_sess->get_company('CompanyID') . "<br>\n";
        if($onlyinternal and $this->recurciv_company_check(array('DefCompanyID'=>$_sess->get_companydef('CompanyID'), 'CompanyID'=>$_sess->get_company('CompanyID'))) and 10 == 11)
        {
            print "<!-- Emaptix Security manager denies you access to this information -->";
            #print "Bare interne: $onlyinternal, companydef: " . $_sess->get_companydef('CompanyID') . " Against: " .  $_sess->get_company('CompanyID') . "<br>";
            #Logg access breach, access, search words
            $args['Message'] = "External user from company: ". $_sess->get_company('CompanyID') . " trying to access internal information for company " . $_sess->get_companydef('CompanyID');
            $_log->accessdenied($_sess, $this->_template, $args);
            exit;
        }
        #All else access is granted
    }

    #function to recurscivly check if CompanyID is a member of DefCompanyID
    #This needs to be more powerfull to support advanced tree structure
    function recurciv_company_check($args)
    {
	global $_dsn, $_dbh;
        $DefCompanyID = $args['DefCompanyID'];
        $CompanyID = $args['CompanyID'];

        if($DefCompanyID == $CompanyID)
        {
            return 0;
        }

        #Simple check, does not support advanced tree struckture
        $query = "select ParentCompanyID, ChildCompanyID from companystruct where ParentCompanyID='$DefCompanyID'";
        $row = $_lib['db']->get_row(array('query' => $query));

        while($row->ChildCompanyID > 0)
        {
            if($row->ChildCompanyID==$CompanyID)
            {
                return 1;
            }
            $query = "select ParentCompanyID, ChildCompanyID from companystruct where ParentCompanyID='$row->ChildCompanyID'";
            $row = $_lib['db']->get_row(array('query' => $query));
        }

        return 0;
    }

    function template_allow()
    {
        #Does the logged in user have access to this template
        #Used to hide links you can not click on.
        if($this->_template)
        {
            return true;
        }
        else
        {
            return false;
        }
    }

    function internuser_allow()
    {
        $_sess = $this->_sess;
        if($_sess->get_companydef('CompanyID') == $_sess->get_company('CompanyID'))
        {
            return true;
        }
        else
        {
            return false;
        }
    }
}
?>
