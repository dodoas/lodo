<?
class form2 {
  public $_dbh;
  public $_dsn;
  public $_SETUP;

  function form2($args){
    #Init
    $this->_dbh     = $_lib['sess']->dbh;
    $this->_dsn     = $args['_dsn'];
    $this->_SETUP   = $args['_SETUP'];

    #print "$this->_dbh, $this->_dsn<br>";
    #print_r($this->_dbh);
  }

  # $Id: form_elements2.inc,v 1.43 2005/04/08 11:17:47 thomasek Exp $ form_elements.inc,v 1.1.1.1 2001/11/08 18:14:05 thomasek Exp $
  # Based on EasyComposer technology
  # Copyright Thomas Ekdahl, 1994-2003, thomas@ekdahl.no, http://www.ekdahl.no

  function checkbox2($table, $field, $choice, $pk) {
      # Checkboxes has always 1/0 result
      #To be able to uncheck values (unchecked fields dos not contain values, we have to do this dirty tricks. Any better?
      if($pk) {
        print "<input type=\"hidden\" name=\"$table.$field.$pk\" value=\"0\"/>";
        print "<input type=\"checkbox\" name=\"$table.$field.$pk\" value=\"1\"";
        if($choice) print " checked ";
        print ">\n";
      } else {
        print "<input type=\"hidden\" name=\"$table.$field\" value=\"0\"/>";
        print "<input type=\"checkbox\" name=\"$table.$field\" value=\"1\"";
        if($choice) print " checked ";
        print ">\n";
      }
  }

  function radiobutton2($table, $field, $choice, $value) {
      # Radiobuttons has always 1/0 result
      print "<input type=\"radio\" name=\"$table.$field\" value=\"$value\"";
      if($choice == $value) print " checked=\"checked\" ";
      print ">";
  }

  function currency_menu2($table, $field, $value) {
      global $_lib;
        
      $query = "select CurrencyID, Amount from exchange order by CurrencyID";
      $result = $_lib['db']->db_query($query);

      print "<select name=\"$table.$field\">\n";
      print "<option value=\"\">Ikke valgt";
      while($_row = $_lib['db']->db_fetch_object($result)) {
          if($_row->Currency == $value)
              print "<option value=\"$_row->Currency\" selected>$_row->Currency \n";
          else
              print "<option value=\"$_row->Currency\">$_row->Currency \n";
      }

      print "</select>\n";
      #$_lib['db']->db_free_result($result);

  }

  function ArbeidsgiverAvgift_menu2($conf) {
      global $_lib;

      $query = "select * from arbeidsgiveravgift order by Code";
      $result = $_lib['db']->db_query($query);

      if($conf[pk]) {
        print "<select name=\"" . $conf[table] . "." . $conf[field] . "." . $conf['pk'] . "\" tabindex=\"" . $conf[tabindex] . "\" accesskey=\"" . $conf[accesskey] . "\">\n";
      } else {
        print "<select name=\"" . $conf[table] . "." . $conf[field] . "\" tabindex=\"" . $conf[tabindex] . "\" accesskey=\"" . $conf[accesskey] . "\">\n";
      }
      print "<option value=\"\">Ikke valgt";
      while($_row = $_lib['db']->db_fetch_object($result)) {
          if($_row->Code == $conf['value'])
              print "<option value=\"$_row->Code\" selected>$_row->Code - $_row->Percent%\n";
          else
              print "<option value=\"$_row->Code\">$_row->Code - $_row->Percent%\n";
      }

      print "</select>\n";
      #$_lib['db']->db_free_result($result);

  }

  function vat_menu2($table, $field, $value) {
      global $_lib;

      $query = "select * from vat order by VatID";
      $result = $_lib['db']->db_query($query);

      print "<select name=\"$table.$field\">\n";
      print "<option value=\"\">Ikke valgt";
      while($_row = $_lib['db']->db_fetch_object($result)) {
          if($_row->VatID == $value)
              print "<option value=\"$_row->VatID\" selected>$_row->VatID - $_row->Percent%\n";
          else
              print "<option value=\"$_row->VatID\">$_row->VatID - $_row->Percent% \n";
      }

      print "</select>\n";
      #$_lib['db']->db_free_result($result);

  }

  function Company_menu2($name, $value, $form_name, $where, $db_table) {
      global $_lib;

      $query = "select CompanyID, CompanyName from company order by CompanyName";
      $result = $_lib['db']->db_query($query);

      print "<select name=\"$name\" onChange=\"Auto_Save('$form_name','$db_table','$name','$where')\">\n";
      print "<option value=\"\">Not choosen";
      while($_row = $_lib['db']->db_fetch_object($result)) {
          if($_row->CompanyID == $value)
              print "<option value=\"$_row->CompanyID\" selected>$_row->CompanyName \n";
          else
              print "<option value=\"$_row->CompanyID\">$_row->CompanyName \n";
      }

      print "</select>\n";
      #$_lib['db']->db_free_result($result);
      ?>

      <a href="<? print $this->_SETUP[DISPATCH] ?>t=company.edit&CompanyID=<? print "$value"; ?>" title="Click to view all info about customer">C</a>
  <? }

  function CompanyContactMenu($args) {
      global $_lib;

      if(!$args[company_id]) { $args[company_id] = $_lib['sess']->get_companydef('CompanyID'); };
      $query = "select P.PersonID, P.FirstName, P.LastName from person as P, companypersonstruct as S where S.CompanyID='$args[company_id]' and P.PersonID=S.PersonID order by FirstName";
      #print "$query<br>";
      $result = $_lib['db']->db_query($query);

      if(isset($args['disabled']) && $args['disabled'] == true)
        $disabled="disabled=\"true\"";

      if($args[pk]){
        print "<select name=\"$args[table].$args[field].$args[pk]\" $disabled>\n";
      } else {
        print "<select name=\"$args[table].$args[field]\" $disabled>\n";
      }
      if($args[value]){
        print "<option value=\"\">Not Choosen";
      }
      while($_row = $_lib['db']->db_fetch_object($result)) {
          if($_row->PersonID == $args[value])
              print "<option value=\"$_row->PersonID\" selected>$_row->FirstName $_row->LastName \n";
          else
              print "<option value=\"$_row->PersonID\">$_row->FirstName $_row->LastName \n";
      }

      print "</select>\n";
      #$_lib['db']->db_free_result($result);
      if($args[value]){
      ?>
      <a href="<? print $this->_SETUP[DISPATCH] ?>t=person.edit&PersonID=<? print "$args[value]"; ?>" title="Click to view all info about person">P</a>
      <?
      }
  }

  function Supplier_menu2($name, $value, $form_name, $where, $db_table) {
      global $_lib;

      $query = "select SupplierID, CompanyName from suppliers";
      $result = $_lib['db']->db_query($query);

      print "<select name=\"$name\" onChange=\"Auto_Save('$form_name','$db_table','$name','$where')\">\n";
      print "<option value=\"\">Not choosen";
      while($_row = $_lib['db']->db_fetch_object($result)) {
          if($_row->SupplierID == $value)
              print "<option value=\"$_row->SupplierID\" selected>$_row->CompanyName \n";
          else
              print "<option value=\"$_row->SupplierID\">$_row->CompanyName \n";
      }

      print "</select>\n";
      #$_lib['db']->db_free_result($result);
      ?>

      <a href="<? print $this->_SETUP[DISPATCH] ?>t=supplier.edit&SupplierID=<? print "$value"; ?>" title="Click to view all info about supplier">S</a>
  <? }

  function Avd_menu2($name, $value, $form_name, $where, $db_table) {
      global $_lib;

      $query = "select CompanyDepartmentID, DepartmentName from companydepartment where Active=1";
      $result = $_lib['db']->db_query($query);

      print "<select name=\"$name\" onChange=\"Auto_Save('$form_name','$db_table','$name','$where')\">\n";
      print "<option value=\"\">Not choosen";
      while($_row = $_lib['db']->db_fetch_object($result)) {
          if($_row->AvdleingsID == $value)
              print "<option value=\"$_row->CompanyDepartmentID\" selected>$_row->DepartmentName \n";
          else
              print "<option value=\"$_row->CompanyDepartmentID\">$_row->DepartmentName \n";
      }

      print "</select>\n";
      #$_lib['db']->db_free_result($result);
      ?>

      <a href="<? print $this->_SETUP[DISPATCH] ?>t=/avd/avd.php?DepartmentID=<? print "$value"; ?>" title="Click to view all info about department">A</a>
  <? }

  #Valid hash input: type, table, field, pk, tabindex, accesskey, value
  function Type_menu2($args) {
      global $_lib;

      $query = "select MenuValue, MenuChoice from confmenues where MenuName='$args[type]' order by MenuChoice";
      $result = $_lib['db']->db_query($query);

     if($args['pk']) {
        print "<select name=\"$args[table].$args[field].$args[pk]\" tabindex=\"$args[tabindex]\" accesskey=\"$args[accesskey]\">\n";
      } else {
        print "<select name=\"$args[table].$args[field]\" tabindex=\"$args[tabindex]\" accesskey=\"$args[accesskey]\">\n";
      }

      print "<option value=\"\">Ikke valgt";
      while($_row = $_lib['db']->db_fetch_object($result)) {
          if($_row->MenuValue == $args[value])
              print "<option value=\"$_row->MenuValue\" selected>$_row->MenuChoice \n";
          else
              print "<option value=\"$_row->MenuValue\">$_row->MenuChoice \n";
      }

      print "</select>\n";
      #$_lib['db']->db_free_result($result);

  }

  function Account_menu2($name, $value, $form_name, $where, $db_table) {
      global $_lib;

      $query = "select AccountNumber, AccountDescription from account order by Active";
      $result = $_lib['db']->db_query($query);
      print "<select name=\"$name\">\n";
      print "<option value=\"all\">All";
      print "<option value=\"Active\">All Active";
      print "<option value=\"closed\">All closed";
      while($_row = $_lib['db']->db_fetch_object($result)) {
          if($_row->AccountNumber == $value)
              print "<option value=\"$_row->AccountNumber\" selected>$_row->AccountNumber - $_row->AccountDescription\n";
          else
              print "<option value=\"$_row->AccountNumber\">$_row->AccountNumber - $_row->AccountDescription\n";
      }

      print "</select>\n";
      #$_lib['db']->db_free_result($result);
  }

  #input: $able, $name, $value, $access, $tabindex, $accesskey, $pk, $noaccess
  function AccountPeriod_menu2($args) {
      global $_lib;

      if($args['noaccess'] > 3) {
        $query = "select Period from accountperiod order by Period asc";
      }
      elseif($args['access'] > 3) {
        $query = "select Period from accountperiod where Status=2 or Status=3 order by Period asc";
      } else {
        $query = "select Period from accountperiod where Status=2 order by Period asc";
      }
      $result = $_lib['db']->db_query($query);

      if($args['pk']) {
        print "<select name=\"$args[table].$args[field].$args[pk]\" tabindex=\"$args[tabindex]\" accesskey=\"$args[accesskey]\">\n";
      } else {
        print "<select name=\"$args[table].$args[field]\" tabindex=\"$args[tabindex]\" accesskey=\"$args[accesskey]\">\n";
      }
      while($_row = $_lib['db']->db_fetch_object($result)) {
          if($_row->Period == $args['value'])
            print "<option value=\"$_row->Period\" selected>$_row->Period\n";
          else
            print "<option value=\"$_row->Period\">$_row->Period\n";
      }

      print "</select>\n";
      #$_lib['db']->db_free_result($result);
  }

  function VatPeriod_menu2($db_table, $name, $value, $access, $tabindex, $accesskey) {
      global $_lib;

      $query = "select Period from accountperiod where Period not like '%-13' order by Period asc";
      $result = $_lib['db']->db_query($query);
      print "<select name=\"$db_table.$name\" tabindex=\"$tabindex\" accesskey=\"$accesskey\">\n";
      while($_row1 = $_lib['db']->db_fetch_object($result)) {
        $_row2 = $_lib['db']->db_fetch_object($result);
        print "<option value=\"$_row1->Period;$_row2->Period\">$_row1->Period;$_row2->Period\n";
      }

      print "</select>\n";
      #$_lib['db']->db_free_result($result);
  }


  function accountplan_number_menu2($conf) {
      global $_lib;

        return "form2 accountplan_number_menu2 deprecated";

      if(!$conf['from_account']) {
        $from_account = $_lib['sess']->get_companydef('AccountSaleFrom');
      } else {
        $from_account = $conf['from_account'];
      }
      if(!$conf['to_account']) {
        $to_account     = $_lib['sess']->get_companydef('AccountCreditTo');
      } else {
        $to_account     = $conf['to_account'];
      }

      if(isset($conf['disabled'])) {
        $disabled = "disabled";
      } else {
        $disabled = "";
      }

      $query = "select AccountPlanID, AccountName from accountplan where Active=1 and EnableReskontro=0 and AccountPlanID >= '$from_account' and AccountPlanID <= '$to_account'  order by AccountPlanID";
      #print "$query<br>";
      $result = $_lib['db']->db_query($query);
      if(!$conf['num_letters']) {
        $num_letters = '25';
      } else {
        $num_letters = $conf['num_letters'];
      }; #Default number of letters in menu

      if($conf[pk]) {
        print "<select name=\"" . $conf[table] . "." . $conf[field] . "." . $conf[pk] . "\" tabindex=\"" . $conf[tabindex] . "\" accesskey=\"" . $conf[accesskey] . "\" $disabled>\n";
      } else {
        print "<select name=\"" . $conf[table] . "." . $conf[field] . "\" tabindex=\"" . $conf[tabindex] . "\" accesskey=\"" . $conf[accesskey] . "\" $disabled>\n";
      }
      if($conf['value']) {
        print "<option value=\"\">" . substr('Velg konto',0, $num_letters);
      } else {
        print "<option value=\"\">" . substr('Velg konto',0, $num_letters);
      }
      while($_row = $_lib['db']->db_fetch_object($result)) {
          if($_row->AccountPlanID == $conf['value'])
              print "<option value=\"$_row->AccountPlanID\" selected>" . substr("$_row->AccountPlanID-$_row->AccountName",0,$num_letters) . "\n";
          else
              print "<option value=\"$_row->AccountPlanID\">" . substr("$_row->AccountPlanID-$_row->AccountName",0,$num_letters) . "\n";
      }

      $query = "select AccountPlanID, AccountName from accountplan where Active='1' and EnableReskontro='0' and AccountPlanID >= '$from_account' and AccountPlanID <= '$to_account' order by AccountName";
      $result = $_lib['db']->db_query($query);

      while($_row = $_lib['db']->db_fetch_object($result)) {
          print "<option value=\"$_row->AccountPlanID\">" . substr("$_row->AccountName-$_row->AccountPlanID",0,$num_letters) . "\n";
      }

      print "</select>\n";
      #$_lib['db']->db_free_result($result);
  }

  function salesperson_menu2($table, $field, $value, $company_id, $pk) {
      global $_lib;

      $query = "select p.PersonID, p.FirstName, p.LastName from person as p, personparameter as pa, companypersonstruct as cp where cp.CompanyID='$company_id' and cp.PersonID=p.PersonID and p.Active='1' and p.PersonID=pa.PersonID and pa.Type=4  order by FirstName";
      #print "PersonID = $_row->PersonID = $value<br>";
      $result = $_lib['db']->db_query($query);
      if($pk) {
        print "<select name=\"$table.$field.$pk\" tabindex=\"$tabindex\" accesskey=\"$accesskey\">\n";
      } else {
        print "<select name=\"$table.$field\" tabindex=\"$tabindex\" accesskey=\"$accesskey\">\n";
      }
      if(!$value) {
        print "<option value=\"\">Velg selger";
      } else {
        print "<option value=\"\">Selger finnes ikke: $value";
      }
      while($_row = $_lib['db']->db_fetch_object($result)) {
          if($_row->PersonID == $value)
              print "<option value=\"$_row->PersonID\" selected>$_row->FirstName $_row->LastName\n";
          else
              print "<option value=\"$_row->PersonID\">$_row->FirstName $_row->LastName\n";
      }

      print "</select>\n";
      #$_lib['db']->db_free_result($result);
  }

  #Has input: $table, $field, $value, $company_id, $pk
  function person_menu2($args) {
      global $_lib;

      $query = "select p.PersonID, p.FirstName, p.LastName from person as p, companypersonstruct as cp where cp.CompanyID='$args[company_id]' and cp.PersonID=p.PersonID and p.Active='1' order by FirstName";
      #print "PersonID = $_row->PersonID = $value<br>";
      $result = $_lib['db']->db_query($query);
      if($args[pk]) {
        print "<select name=\"$args[table].$args[field].$args[pk]\" tabindex=\"$args[tabindex]\" accesskey=\"$args[accesskey]\">\n";
      } else {
        print "<select name=\"$args[table].$args[field]\" tabindex=\"$args[tabindex]\" accesskey=\"$args[accesskey]\">\n";
      }
      if(!$value) {
        print "<option value=\"\">Velg ansatt";
      } else {
        print "<option value=\"\">Ansatt finnes ikke: $args[value]";
      }
      while($_row = $_lib['db']->db_fetch_object($result)) {
          if($_row->PersonID == $args[value])
              print "<option value=\"$_row->PersonID\" selected>$_row->FirstName $_row->LastName\n";
          else
              print "<option value=\"$_row->PersonID\">$_row->FirstName $_row->LastName\n";
      }

      print "</select>\n";
      #$_lib['db']->db_free_result($result);
  }

  #args input: , $args[table, $args[field, $args[value, $args[tabindex, $args[accesskey, $args[pk, $num_letters, company_id
  function project_menu2($args) {
      global $_lib;

      if($args['company_id']) {
        $query = "select ProjectID, Heading from project where Active='1' and CompanyID='$args[company_id]' order by ProjectID";
      } else {
        $query = "select ProjectID, Heading from project where Active='1' order by ProjectID";
      }
     $result = $_lib['db']->db_query($query);

      if(!$conf['num_letters']) {
        $num_letters = '20';
      } else {
        $num_letters = $conf['num_letters'];
      }; #Default number of letters in menu


      if($args['pk']) {
        print "<select name=\"$args[table].$args[field].$args[pk]\" tabindex=\"$args[tabindex]\" accesskey=\"$args[accesskey]\">\n";
      } else {
        print "<select name=\"$args[table].$args[field]\" tabindex=\"$args[tabindex]\" accesskey=\"$args[accesskey]\">\n";
      }
      if($conf['value']) {
        print "<option value=\"\">" . substr("Finnes ikke: . $conf[value]",0, $num_letters);
      } else {
        print "<option value=\"\">" . substr('Velg prosjekt',0, $num_letters);
      }
      while($_row = $_lib['db']->db_fetch_object($result)) {
          if($_row->ProjectID == $args[value])
              print "<option value=\"$_row->ProjectID\" selected>$_row->ProjectID - " . substr($_row->Heading,0,$num_letters) . "\n";
          else
              print "<option value=\"$_row->ProjectID\">$_row->ProjectID - " . substr($_row->Heading,0,$num_letters) . "\n";
      }

      print "</select>\n";
      #$_lib['db']->db_free_result($result);
      ?>
  <? }

  #$args[project_id], num_letters, table, field, value, tabindex, accesskey
  function projectactivity_menu2($args) {
      global $_lib;

     $query = "select ProjectActivityID, Description from projectactivity where Active='1' and ProjectID='$args[project_id]' order by ProjectID";
     $result = $_lib['db']->db_query($query);

      if(!$conf['num_letters']) {
        $num_letters = '20';
      } else {
        $num_letters = $conf['num_letters'];
      }; #Default number of letters in menu


      if($args['pk']) {
        print "<select name=\"$args[table].$args[field].$args[pk]\" tabindex=\"$args[tabindex]\" accesskey=\"$args[accesskey]\">\n";
      } else {
        print "<select name=\"$args[table].$args[field]\" tabindex=\"$args[tabindex]\" accesskey=\"$args[accesskey]\">\n";
      }
      if($conf['value']) {
        print "<option value=\"\">" . substr("Finnes ikke: . $conf[value]",0, $num_letters);
      } else {
        print "<option value=\"\">" . substr('Velg aktivitet',0, $num_letters);
      }
      while($_row = $_lib['db']->db_fetch_object($result)) {
          if($_row->ProjectActivityID == $args[value])
              print "<option value=\"$_row->ProjectActivityID\" selected>$_row->ProjectActivityID - " . substr($_row->Description,0,$num_letters) . "\n";
          else
              print "<option value=\"$_row->ProjectActivityID\">$_row->ProjectActivityID - " . substr($_row->Description,0,$num_letters) . "\n";
      }

      print "</select>\n";
      #$_lib['db']->db_free_result($result);
      ?>
  <? }

  function department_menu2($args) {
      global $_lib;

      $query = "select CompanyDepartmentID, DepartmentName from companydepartment where Active='1' order by CompanyDepartmentID";
      $result = $_lib['db']->db_query($query);
      if($args['num_letters']) {
        $num_letters = $args['num_letters'];
      } else {
        $num_letters = 10;
      }

      if($args[pk]) {
        print "<select name=\"$args[table].$args[field].$args[pk]\" tabindex=\"$args[tabindex]\" accesskey=\"$accesskey\">\n";
      } else {
        print "<select name=\"$args[table].$args[field]\" tabindex=\"$tabindex\" accesskey=\"$args[accesskey]\">\n";
      }
      print "Greit<br>";
      if($conf['value']) {
        print "<option value=\"\">" . substr("Finnes ikke: . $conf[value]",0, $num_letters);
      } else {
        print "<option value=\"\">" . substr('Velg avdeling',0, $num_letters);
      }
      while($_row = $_lib['db']->db_fetch_object($result)) {
          if($_row->CompanyDepartmentID == $args[value])
              print "<option value=\"$_row->CompanyDepartmentID\" selected>" . substr($_row->CompanyDepartmentID." - ".$_row->DepartmentName, 0, $num_letters) . "\n";
          else
              print "<option value=\"$_row->CompanyDepartmentID\">" . substr($_row->CompanyDepartmentID." - ".$_row->DepartmentName, 0, $num_letters) . "\n";
      }

      print "</select>\n";
      #$_lib['db']->db_free_result($result);
      ?>
  <? }

  function ExpenceHead_menu2($name, $value, $form_name, $where, $db_table) {
      global $_lib;

      $query = "select ExpenceHeadID, ExpenceDescription from expencehead order by Active";
      $result = $_lib['db']->db_query($query);
      print "<select name=\"$name\">\n";
      print "<option value=\"all\">All";
      print "<option value=\"Active\">All Active";
      print "<option value=\"closed\">All closed";
      while($_row = $_lib['db']->db_fetch_object($result)) {
          if($_row->ExpenceHeadID == $value)
              print "<option value=\"$_row->ExpenceHeadID\" selected>$_row->ExpenceDescription\n";
          else
              print "<option value=\"$_row->ExpenceHeadID\">$_row->ExpenceDescription\n";
      }

      print "</select>\n";
      #$_lib['db']->db_free_result($result);
      ?>
  <? }

  function Product_menu2($name, $value, $form_name, $where, $db_table) {
      global $_lib;

      $query = "select ProductID, ProductNumber, ProductName from product order by ProductNumber asc";
      $result = $_lib['db']->db_query($query);
      print "<select name=\"$name\"";
      if($value == '' || $value == '%') { print " selected"; };
      print "\n";
      print "<option value=\"%\">All products";
          if($value == '03') { print " selected"; };

      print "<option value=\"03\">03 - produktnummer";
          if($value == '04') { print " selected"; };
      print "\n";
      print "<option value=\"04\">04 - produktnummer";
          if($value == '05') { print " selected"; };
      print "\n";
      print "<option value=\"05\">05 - produktnummer";
      while($_row = $_lib['db']->db_fetch_object($result)) {
          if($_row->ExpenceHeadID == $value)
                  print "<option value=\"$_row->ProductID\" selected>$_row->ProductNumber $_row->ProductName\n";
          else
                  print "<option value=\"$_row->ProductID\">$_row->ProductNumber $_row->ProductName\n";
      }

          print "</select>\n";
          #$_lib['db']->db_free_result($result);
          ?>
  <? }
}
?>
