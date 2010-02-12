<?
  # $Id: form_elements.inc,v 1.13 2005/04/08 11:17:47 thomasek Exp $ form_elements.inc,v 1.1.1.1 2001/11/08 18:14:05 thomasek Exp $
  # Based on EasyComposer technology
  # Copyright Thomas Ekdahl, 1994-2003, thomas@ekdahl.no, http://www.ekdahl.no

class form {
  var $_dbh;
  var $_dsn;
  var $_sess;
  var $_SETUP;

  function form($args){
    #Init
    $this->_sess    = $args['_sess'];
    $this->_dbh     = $this->_sess->dbh;
    $this->_dsn     = $args['_dsn'];
    $this->_SETUP   = $args['_SETUP'];
  }

  function checkbox($name, $value, $form_name, $db_table, $where) {
      # Checkboxes has always 1/0 result
      print "<input type=\"checkbox\" name=\"$name\" value=\"1\"";
      if($value) print " checked ";
      print " onChange=\"Auto_Save('$form_name','$db_table','$name','$where','checkbox')\">";
  }

  function radiobutton($name, $choice, $value, $form_name, $db_table, $where) {
      # Radiobuttons has always 1/0 result
      print "<input type=\"radio\" name=\"$name\" value=\"$value\"";
      if($choice == $value) print " checked=\"checked\" ";
      print " onChange=\"Auto_Save('$form_name','$db_table','$name','$where','radiobutton')\">";
  }

  function Company_menu($name, $value, $form_name, $where, $db_table) {
      $query = "select CompanyID, CompanyName from company order by CompanyName";
      $result = $this->_dbh[$this->_dsn]->db_query($query);

      print "<select name=\"$name\" onChange=\"Auto_Save('$form_name','$db_table','$name','$where')\">\n";
      print "<option value=\"\">Not choosen";
      while($_row = $this->_dbh[$this->_dsn]->db_fetch_object($result)) {
          if($_row->CompanyID == $value)
              print "<option value=\"$_row->CompanyID\" selected>$_row->CompanyName \n";
          else
              print "<option value=\"$_row->CompanyID\">$_row->CompanyName \n";
      }

      print "</select>\n";
      #$this->_dbh[$this->_dsn]->db_free_result($result);
      ?>

      <a href="../Company/company_edit.php?CompanyID=<? print "$value"; ?>" title="Click to view all info about customer">C</a>
  <? }

  function CompanyContactMenu($name, $value, $company_id, $form_name, $where, $db_table) {
      $query = "select P.PersonID, P.FirstName, P.LastName from person as P, companypersonstruct as S where S.CompanyID='$company_id' and P.PersonID=S.PersonID order by FirstName";
      $result = $this->_dbh[$this->_dsn]->db_query($query);

      print "<select name=\"$name\" onChange=\"Auto_Save('$form_name','$db_table','$name','$where')\">\n";
      print "<option value=\"\">Not Choosen";
      while($_row = $this->_dbh[$this->_dsn]->db_fetch_object($result)) {
          if($_row->PersonID == $value)
              print "<option value=\"$_row->PersonID\" selected>$_row->FirstName $_row->LastName \n";
          else
              print "<option value=\"$_row->PersonID\">$_row->FirstName $_row->LastName \n";
      }

      print "</select>\n";
      #$this->_dbh[$this->_dsn]->db_free_result($result);

      ?>

      <a href="../Persons/person_edit.php?PersonID=<? print "$value"; ?>" title="Click to view all info about person">P</a>
  <? }

  function Supplier_menu($name, $value, $form_name, $where, $db_table) {
      $query = "select SupplierID, CompanyName from suppliers";
      $result = $this->_dbh[$this->_dsn]->db_query($query);

      print "<select name=\"$name\" onChange=\"Auto_Save('$form_name','$db_table','$name','$where')\">\n";
      print "<option value=\"\">Not choosen";
      while($_row = $this->_dbh[$this->_dsn]->db_fetch_object($result)) {
          if($_row->SupplierID == $value)
              print "<option value=\"$_row->SupplierID\" selected>$_row->CompanyName \n";
          else
              print "<option value=\"$_row->SupplierID\">$_row->CompanyName \n";
      }

      print "</select>\n";
      #$this->_dbh[$this->_dsn]->db_free_result($result);
      ?>

      <a href="../Suppliers/supplier.php?SupplierID=<? print "$value"; ?>" title="Click to view all info about supplier">S</a>
  <? }

  function Avd_menu($name, $value, $form_name, $where, $db_table) {
      $query = "select CompanyDepartmentID, DepartmentName from companydepartment";
      $result = $this->_dbh[$this->_dsn]->db_query($query);

      print "<select name=\"$name\" onChange=\"Auto_Save('$form_name','$db_table','$name','$where')\">\n";
      print "<option value=\"\">Not choosen";
      while($_row = $this->_dbh[$this->_dsn]->db_fetch_object($result)) {
          if($_row->AvdleingsID == $value)
              print "<option value=\"$_row->CompanyDepartmentID\" selected>$_row->DepartmentName \n";
          else
              print "<option value=\"$_row->CompanyDepartmentID\">$_row->DepartmentName \n";
      }

      print "</select>\n";
      #$this->_dbh[$this->_dsn]->db_free_result($result);
      ?>

      <a href="../Misc/avd/avd.php?AvdleingID=<? print "$value"; ?>" title="Click to view all info about department">A</a>
  <? }

  function Type_menu($name, $value, $type, $form_name, $where, $db_table) {
      $query = "select MenuValue, MenuChoice from confmenues where MenuName='$type' order by MenuChoice";
      $result = $this->_dbh[$this->_dsn]->db_query($query);
      print "<select class=\"navigation\" name=\"$name\" onChange=\"Auto_Save('$form_name','$db_table','$name','$where')\">\n";
      print "<option value=\"\">Not choosen";
      while($_row = $this->_dbh[$this->_dsn]->db_fetch_object($result)) {
          if($_row->MenuValue == $value)
              print "<option value=\"$_row->MenuValue\" selected>$_row->MenuChoice \n";
          else
              print "<option value=\"$_row->MenuValue\">$_row->MenuChoice \n";
      }

      print "</select>\n";
      #$this->_dbh[$this->_dsn]->db_free_result($result);

  }

  function Account_menu($name, $value, $form_name, $where, $db_table) {
      $query = "select AccountID, AccountDescription, AccountNumber from account order by Active";
      $result = $this->_dbh[$this->_dsn]->db_query($query);
      print "<select name=\"$name\">\n";
      print "<option value=\"all\">All";
      print "<option value=\"Active\">All Active";
      print "<option value=\"closed\">All closed";
      while($_row = $this->_dbh[$this->_dsn]->db_fetch_object($result)) {
          if($_row->AccountID == $value)
              print "<option value=\"$_row->AccountID\" selected>$_row->AccountNumber - $_row->AccountDescription\n";
          else
              print "<option value=\"$_row->AccountID\">$_row->AccountNumber - $_row->AccountDescription\n";
      }

      print "</select>\n";
      #$this->_dbh[$this->_dsn]->db_free_result($result);
      ?>
  <? }

  function accountplan_menu($name, $value, $form_name, $where, $db_table) {
      $query = "select AccountNumber, AccountName from accountplan";
      $result = $this->_dbh[$this->_dsn]->db_query($query);
      print "<select name=\"$name\">\n";
      while($_row = $this->_dbh[$this->_dsn]->db_fetch_object($result)) {
          if($_row->AccountNumber == $value)
              print "<option value=\"$_row->AccountNumber\" selected>$_row->AccountNumber - $_row->AccountName\n";
          else
              print "<option value=\"$_row->AccountNumber\">$_row->AccountNumber - $_row->AccountName\n";
      }

      print "</select>\n";
      #$this->_dbh[$this->_dsn]->db_free_result($result);
      ?>
  <? }

  function ExpenceHead_menu($name, $value, $form_name, $where, $db_table) {
      $query = "select ExpenceHeadID, ExpenceDescription from expencehead order by Active";
      $result = $this->_dbh[$this->_dsn]->db_query($query);
      print "<select name=\"$name\">\n";
      print "<option value=\"all\">All";
      print "<option value=\"Active\">All Active";
      print "<option value=\"closed\">All closed";
      while($_row = $this->_dbh[$this->_dsn]->db_fetch_object($result)) {
          if($_row->ExpenceHeadID == $value)
              print "<option value=\"$_row->ExpenceHeadID\" selected>$_row->ExpenceDescription\n";
          else
              print "<option value=\"$_row->ExpenceHeadID\">$_row->ExpenceDescription\n";
      }

      print "</select>\n";
      #$this->_dbh[$this->_dsn]->db_free_result($result);
      ?>
  <? }

  function Product_menu($name, $value, $form_name, $where, $db_table) {
      $query = "select ProductID, ProductNumber, ProductName from product order by ProductNumber asc";
      $result = $this->_dbh[$this->_dsn]->db_query($query);
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
      while($_row = $this->_dbh[$this->_dsn]->db_fetch_object($result)) {
          if($_row->ExpenceHeadID == $value)
                  print "<option value=\"$_row->ProductID\" selected>$_row->ProductNumber $_row->ProductName\n";
          else
                  print "<option value=\"$_row->ProductID\">$_row->ProductNumber $_row->ProductName\n";
      }

          print "</select>\n";
          #$this->_dbh[$this->_dsn]->db_free_result($result);
          ?>
  <? }
}
?>
