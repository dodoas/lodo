<?
/* $Id: template.php,v 1.46 2005/10/28 17:59:41 thomasek Exp $ main.php,v 1.12 2001/11/20 17:55:12 thomasek Exp $ */
$SalaryConfID = $_REQUEST['SalaryConfID'];

$db_table = 'salaryconf';
$db_table2 = 'salaryconfline';

includelogic('accounting/accounting');
$accounting = new accounting();
require_once "record.inc";

$query_head     = "select * from $db_table where SalaryConfID = '$SalaryConfID'";
$head           = $_lib['storage']->get_row(array('query' => $query_head));
$ishovedmal = $head->SalaryConfID;

$query_salary   = "select * from $db_table2 where SalaryConfID = '$SalaryConfID' order by LineNumber asc";
$result_salary  = $_lib['db']->db_query($query_salary);

print $_lib['sess']->doctype ?>

<head>
    <title>Empatix - customer</title>
    <meta name="cvs"                content="$Id: template.php,v 1.46 2005/10/28 17:59:41 thomasek Exp $" />
    <? includeinc('head') ?>

    <script>
    function switchActive(id)
    {
       var value = $(document.getElementById('active.' + id));
       value.val( value.val() == '1' ? '0' : '1' );
    }
    </script>

</head>
<? print $message ?>
<body>

<? includeinc('top') ?>
<? includeinc('left') ?>

<form name="template_update" action="<? print $MY_SELF ?>" method="post">
<input type="hidden" name="SalaryConfID" value="<? print $SalaryConfID ?>" size="7" class="number">
<table class="lodo_data">
  <tr class="result">
    <th colspan="16">Lønnsmal: <? if($ishovedmal == 1) { print "hovedmal"; } else { print $head->SalaryConfID; } ?>
  <?
  if($ishovedmal != 1)
  {
    ?>
    <tr>
      <th>Ansatt</th>
      <th  colspan="3">
      <?
        $aconf = array('table'=>'salaryconf', 'field'=>'AccountPlanID', 'pk'=>$head->SalaryConfID, 'value'=>$head->AccountPlanID, 'tabindex'=>'', 'accesskey'=>'K', 'type' => array(0 => 'employee'));
        print $_lib['form3']->accountplan_number_menu($aconf);
  }
  ?>
      </th>
      <th colspan="9"></th>
  <tr>
    <th class="sub">Aktiv</th>
    <th class="sub">Linje</th>
    <th class="sub">Tekst</th>
    <th class="sub">Antall denne periode</th>
    <th class="sub">Sats</th>
    <th class="sub">Bel&oslash;p denne periode</th>
    <th class="sub">Konto</th>
    <th class="sub">Avdeling</th>
    <th class="sub">Prosjekt</th>
    <th class="sub">Arb. giv. avg.</th>
    <th class="sub">Ferie.Gr</th>
    <th class="sub">L&oslash;nnskode</th>
    <th class="sub" colspan="2"></th>
  </tr>

<?
   $counter = 0;
   while($line = $_lib['db']->db_fetch_object($result_salary))
   {
        if($ishovedmal != 1)
        {
            $query = "select * from $db_table2 where LineNumber=$line->LineNumber and AccountPlanID=$line->AccountPlanID and SalaryConfID=1";
            $mainHead = $_lib['storage']->get_row(array('query' => $query));
        }
   ?>
   <tr>
    <td>
    <?
        if($ishovedmal != 1)
        {
            ?><input type="hidden" name="salaryconfline.Active.<? print $line->SalaryConfLineID ?>" id="active.<? print $line->SalaryConfLineID ?>" value="<? print $line->Active ?>" /><?
            ?><input type="checkbox" <? print ($line->Active ? 'checked="checked"' : '') ?> onchange="switchActive(<? print $line->SalaryConfLineID ?>)" /><?
        }
    ?>
    </td>
    <td>
    <?
        if($ishovedmal == 1 and ($_lib['sess']->get_person('AccessLevel') >= 3))
        {
            ?><input type="hidden" name="<? print $counter ?>" value="<? print $line->SalaryConfLineID ?>"><?
            ?><input type="text" name="salaryconfline.LineNumber.<? print $line->SalaryConfLineID ?>" value="<? print $line->LineNumber ?>" size="3" class="number"><?
        }
        else
        {
            print $line->LineNumber;
        }
    ?>
    </td>
    <td>
    <?
        if($ishovedmal == 1)
        {
            ?><input type="text" name="salaryconfline.SalaryText.<? print $line->SalaryConfLineID ?>" value="<? print $line->SalaryText ?>" size="30" class="number"><?
        }
        else
        {
            print $line->SalaryText;
        }
    ?>
    </td>
    <?
    if(($line->NumberInPeriod != $mainHead->NumberInPeriod) and ($ishovedmal != 1))
    {
        print "<td class=\"debitred\">";
    }
    else
    {
        print "<td>";
    }
    ?>
        <input type="text" name="salaryconfline.NumberInPeriod.<? print $line->SalaryConfLineID ?>"     value="<? print $_lib['format']->Amount(array('value'=>$line->NumberInPeriod, 'return'=>'value')) ?>"     size="3" class="number">
    </td>
    <?
    if(($line->Rate != $mainHead->Rate) and ($ishovedmal != 1))
    {
        print "<td class=\"debitred\">";
    }
    else
    {
        print "<td>";
    }
    ?>
        <input type="text" name="salaryconfline.Rate.<? print $line->SalaryConfLineID ?>"               value="<? print $_lib['format']->Amount(array('value'=>$line->Rate, 'return'=>'value')) ?>"               size="4" class="number">
    </td>
    <?
    if(($line->AmountThisPeriod != $mainHead->AmountThisPeriod) and ($ishovedmal != 1))
        print "<td class=\"debitred\">";
    else
        print "<td>";
    ?>
        <input type="text" name="salaryconfline.AmountThisPeriod.<? print $line->SalaryConfLineID ?>"   value="<? print $_lib['format']->Amount(array('value'=>$line->AmountThisPeriod, 'return'=>'value')) ?>"   size="7" class="number">
    </td>
    <td>
    <?
        if(($ishovedmal == 1) and ($line->LineNumber == 100))
        {
            print $_lib['form3']->hidden(array('table'=>'salaryconfline', 'field'=>'AccountPlanID', 'pk'=>$line->SalaryConfLineID, 'value' => $line->AccountPlanID));
            print 'Velges på delmal';
        }
        elseif($ishovedmal == 1)
        {
            $aconf = array('table'=>'salaryconfline', 'field'=>'AccountPlanID', 'pk'=>$line->SalaryConfLineID, 'value'=>$line->AccountPlanID, 'tabindex'=>'', 'accesskey'=>'K', 'type' => array(0 => 'hovedbokwemployee'), 'allaccounts' => 1);
            print $_lib['form3']->accountplan_number_menu($aconf);
        }
        elseif(($ishovedmal != 1) and ($line->LineNumber != 100))
        {
            $aconf = array('table'=>'salaryconfline', 'field'=>'AccountPlanID', 'pk'=>$line->SalaryConfLineID, 'value'=>$line->AccountPlanID, 'tabindex'=>'', 'accesskey'=>'K', 'type' => array(0 => 'hovedbokwemployee'), 'allaccounts' => 1);
            print $_lib['form3']->accountplan_number_menu($aconf);
        }
        else
        {
            print $line->AccountPlanID;
        }
        $accountplan = $accounting->get_accountplan_object($line->AccountPlanID);
    ?>
    </td>
    <?
        if(($line->AmountThisPeriod != $mainHead->AmountThisPeriod) and ($ishovedmal != 1))
            print "<td class=\"debitred\">";
        else
            print "<td>";
        if($accountplan->EnableDepartment == 1)
        {
            print $_lib['form3']->Avd_menu3(array('table'=>$db_table2, 'field'=>'DepartmentID', 'pk'=>$line->SalaryConfLineID, 'value'=>$line->DepartmentID));
        }
    ?>
    </td>
    <?
        if(($line->AmountThisPeriod != $mainHead->AmountThisPeriod) and ($ishovedmal != 1))
            print "<td class=\"debitred\">";
        else
            print "<td>";
        if($accountplan->EnableProject == 1)
        {
            print $_lib['form3']->project_menu(array('table'=>$db_table2, 'field'=>'ProjectID', 'pk'=>$line->SalaryConfLineID, 'value'=>$line->ProjectID));
        }
    ?>
    </td>
    <td>
    <?
        if($ishovedmal == 1)
        {
            $_lib['form2']->checkbox2('salaryconfline', "EnableEmployeeTax", $line->EnableEmployeeTax, $line->SalaryConfLineID);
        }
        else
        {
            if($line->EnableEmployeeTax) { print "Ja"; };
        }
    ?>
    </td>
    <td>
    <?
        if($ishovedmal == 1)
        {
            $_lib['form2']->checkbox2('salaryconfline', "EnableVacationPayment", $line->EnableVacationPayment, $line->SalaryConfLineID);
        }
        else
        {
            if($line->EnableVacationPayment) { print "Ja"; };
        }
    ?>
    </td>
    <td>
    <?
        if($ishovedmal == 1)
        {
            print $_lib['form3']->text(array('table'=>'salaryconfline', 'field'=>'SalaryCode', 'pk'=>$line->SalaryConfLineID, 'value'=>$line->SalaryCode));
        }
        else
        {
            print $line->SalaryCode;
        }
    ?>
    </td>
    <td>
    <?
        if(($_lib['sess']->get_person('AccessLevel') >= 4) and ($ishovedmal == 1))
        {
            ?><a href="<? print $MY_SELF ?>&amp;SalaryConfID=<? print $SalaryConfID ?>&amp;SalaryConfLineID=<? print $line->SalaryConfLineID ?>&amp;action_salaryconfline_delete=1" class="button">Slett</a><?
        }
    ?>
    </td>
    <td>
        <nobr><? if($_lib['sess']->get_person('AccessLevel') >= 2) { ?><a href="<? print $_lib['sess']->dispatch ?>t=salary.template&amp;SalaryConfID=<? print $SalaryConfID ?>&amp;SalaryConfLineID=<? print $line->SalaryConfLineID ?>&amp;action_salaryconfline_new=1" class="button">Ny linje nr <? print $line->LineNumber ?></a><?}?>
    </td>
   <?
    $counter++;
   }
   ?><input type="hidden" name="row_count" value="<? print $counter ?>"><?
?>
<tr>
  <td colspan="13"><br \>
</tr>
<tr>

  <td colspan="10"></td>
  <td><nobr><? if(($_lib['sess']->get_person('AccessLevel') >= 3) and ($ishovedmal == 1)) { ?><a href="<? print $_lib['sess']->dispatch ?>t=salary.template&amp;SalaryConfID=<? print $SalaryConfID ?>&amp;action_salarymainconfline_new=1" class="button">Ny linje</a><? } ?></nobr></td>
  <td colspan="2" align="right">
  <? if(($_lib['sess']->get_person('AccessLevel') >= 2) or ($ishovedmal == 1)) { ?><input type="submit" name="action_salaryconf_update"  value="Lagre l&oslash;nns konfigurasjon (S)" accesskey="S" /><?}?>
  </td>

</tr>
</table>
</form>
<table>
    <tr>
        <td>
            <fieldset>
                <legend>Linjenr forklaringer</legend>
                <b>linje 10 - 69 = Inntekt</b><br />
                <b>linje 70-100 = Utgift</b><br />
                <br />
                <b>linje 11 = Timel&oslash;nn</b><br />
                <b>linje 12 = Fastl&oslash;nn</b><br />
                <b>linje 90 = Skattetrekk tabell</b><br />
                <b>linje 91 = Skattetrekk prosent</b><br />
                <b>linje 92 = Skattetrekk ekstra</b><br />
                <br />
                <b>linje 100 = Forskudd</b><br />
                <br />
                Linjene 11 og 91 blir automatisk fylt ut fra Empatix timel&oslash;nn hvis denne brukes.<br />
                Linjene 50, 51, 52, 53,54,55, 56, 58, 59, 64, 65 fylles automatisk ut fra Empatix reiseregning hvis denne brukes.
            </fieldset>
        </td>
    </tr>
</table>
<a href="http://www.skatteetaten.no/upload/PDFer/Kodeoversikt2006_2.pdf" target="_blank">Kodeoversikt p&aring; skatteetaten</a>
<? if($_lib['sess']->get_person('AccessLevel') >= 3) { ?><a href="<? print $_lib['sess']->dispatch ?>t=salary.template&amp;SalaryConfID=<? print $SalaryConfID ?>&amp;action_salary_updatetemplatecode=1" class="button">Hent kode/feriepenger/arbeidsgiveravgift flagg fra hovedmal</a><?}?>


<? includeinc('bottom') ?>
</body>
</html>
