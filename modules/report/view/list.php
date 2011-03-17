<?
/* $Id: list.php,v 1.89 2005/11/03 15:33:11 thomasek Exp $ main.php,v 1.12 2001/11/20 17:55:12 thomasek Exp $ */

$db_table = "setup";
$setup = array();

$query_setup    = "select name, value from setup";
$result_setup   = $_lib['db']->db_query($query_setup);

while($row = $_lib['db']->db_fetch_object($result_setup)) {
  $setup[$row->name] = $row->value;
}

includelogic('accounting/accounting');
//includelogic('timesheets/timesheet');
$accounting = new accounting();

$thisDate            	= $_lib['sess']->get_session('LoginFormDate');
$thisYear            	= substr($thisDate,0,4);
$firstPeriodThisYear 	= $_lib['date']->get_this_year($thisDate).'-01';
$lastPeriodThisYear 	= $accounting->get_last_accountperiod_this_year($thisDate);

//$timesheet = new timesheet_user();
//$timesheet_projects = $timesheet->list_projects();

print $_lib['sess']->doctype ?>

<head>
    <title>Empatix - <? print $_lib['sess']->get_companydef('CompanyName') ?> : <? print $_lib['sess']->get_person('FirstName') ?> <? print $_lib['sess']->get_person('LastName') ?></title>
    <meta name="cvs"                content="$Id: list.php,v 1.89 2005/11/03 15:33:11 thomasek Exp $" />
    <? includeinc('head') ?>
</head>
<body>

<? includeinc('top') ?>
<? includeinc('left') ?>

<h1>Rapporter:</h1>
<table class="lodo_data" width="100%">
    <form class="voucher" name="<? print $form_name ?>" action="<? print $_lib['sess']->dispatch ?>t=report.hovedboksaldoliste" method="post" target="_blank">
    <? print $_lib['form3']->hidden(array('table'=>'report', 'field'=>'type', 'value'=>'hovedbok')) ?>
    <tr>
    	<th rowspan="7">
    	<b>H<br />O<br />V<br />E<br />D<br />B<br />O<br />K<br /></b>
    	</th>
    	<td rowspan="3">Saldobalanse</td>
        <td>Fra periode</td>
        <td>
            <?
                print $_lib['form3']->AccountPeriod_menu3(array('table' => 'report', 'field' => 'FromPeriod', 'noaccess' => true, 'value'=>$firstPeriodThisYear));
            ?>
        </td>
        <td>Til periode</td>
        <td>
            <?
                print $_lib['form3']->AccountPeriod_menu3(array('table' => 'report', 'field' => 'ToPeriod', 'noaccess' => true, 'value'=>$lastPeriodThisYear));
            ?>
        </td>
        <td>Resultat fra</td>
        <td>
            <? print $_lib['form3']->AccountPeriod_menu3(array('table' => 'report', 'field' => 'ResultFromPeriod', 'value' => $firstPeriodThisYear, 'noaccess' => true)); ?>
        </td>
    </tr>
    <tr>
        <td>Avdeling</td>
        <td><?
            $aconf = array();
            $aconf['table']         = 'report';
            $aconf['field']         = 'DepartmentID';
            $aconf['accesskey']     = 'D';
            $_lib['form2']->department_menu2($aconf);
            ?>
        </td>
        <td>Prosjekt</td>
        <td><?
            $aconf = array();
            $aconf['table']         = 'report';
            $aconf['field']         = 'ProjectID';
            $aconf['accesskey']     = 'P';
            $_lib['form2']->project_menu2($aconf);
            ?>
        </td>
        <td>Art</td>
        <td><? $_lib['form2']->Type_menu2(array('field' => 'VoucherType', 'type' => 'VoucherType', 'table' => 'report')) ?></td>
    </tr>
    <tr>
      <td>Vis bare aktive konto</td>
      <td><? print $_lib['form3']->checkbox(array('table'=>'accountplan', 'field'=>'Active', 'value'=>'1')) ?></td>
      <td>Fjor&aring;rets tall  <? print $_lib['form3']->checkbox(array('table'=>'report', 'field'=>'EnableLastYear',  'value' => 1)) ?></td>
      <td>Budsjett              <? print $_lib['form3']->checkbox(array('table'=>'report', 'field'=>'EnableBudget',    'value' => 0)) ?></td>
      <td></td>
      <td align="right"><input type="submit" name="show_report_search" value="Kj&oslash;r rapport"  class="button"></td>
    </tr>
</form>
    <tr class="r0">
        <form class="voucher" name="<? print "$form_name"; ?>" action="<? print $_lib['sess']->dispatch ?>t=report.hovedbokvoucherprint" method="post" target="_blank">
        <input type="hidden" name="report.Type" value="hovedbok">
        <input type="hidden" name="report.Sort" value="VoucherDate">

		<td rowspan="4">
			Bilagsutskrift
		</td>

        <td>Fra konto</td>
        <td>
            <?
                print $_lib['form3']->accountplan_number_menu(array('table' => 'report', 'field' => 'FromAccount', 'type' => array(0 => 'hovedbok')));
            ?>
        </td>
        <td>Til Konto</td>
        <td>
            <?
                print $_lib['form3']->accountplan_number_menu(array('table' => 'report', 'field' => 'ToAccount', 'type' => array(0 => 'hovedbok')));
            ?>
        </td>
        <td>Art</td>
        <td><? $_lib['form2']->Type_menu2(array('field' => 'VoucherType', 'type' => 'VoucherType', 'table' => 'report')) ?></td>
    </tr>
    <tr class="r0">
        <td>Fra periode</td>
        <td>
            <?
                print $_lib['form3']->AccountPeriod_menu3(array('table'=>'report', 'field'=>'FromPeriod', 'noaccess'=>true, 'value'=>$firstPeriodThisYear));
            ?>
        </td>
        <td>Til periode</td>
        <td>
            <?
                print $_lib['form3']->AccountPeriod_menu3(array('table' => 'report', 'field' => 'ToPeriod','noaccess' => true, 'value'=>$lastPeriodThisYear));
            ?>
        </td>
        <td>Resultat fra</td>
        <td>
            <? print $_lib['form3']->AccountPeriod_menu3(array('table' => 'report', 'field' => 'ResultFromPeriod', 'value' => $firstPeriodThisYear, 'noaccess' => true)); ?>
        </td>
    </tr>
    <tr class="r0">
        <td>Avdeling</td>
        <td><?
            $aconf = array();
            $aconf['table']         = 'report';
            $aconf['field']         = 'DepartmentID';
            $aconf['accesskey']     = 'D';
            $_lib['form2']->department_menu2($aconf);
            ?>
        </td>
        <td>Prosjekt</td>
        <td><?
            $aconf = array();
            $aconf['table']         = 'report';
            $aconf['field']         = 'ProjectID';
            $aconf['accesskey']     = 'P';
            $_lib['form2']->project_menu2($aconf);
            ?>
        </td>
	    <td></td>
    	<td align="right"><input type="submit" name="show_report_search" value="Kj&oslash;r rapport"  class="button"></td>
    </tr>
    </form>
</table>

<hr>

<table class="lodo_data" width="100%">
  <form class="voucher" name="<? print $form_name ?>" action="<? print $_lib['sess']->dispatch ?>t=report.hovedboksaldoliste" method="post" target="_blank">
  <? print $_lib['form3']->hidden(array('table'=>'report', 'field'=>'type', 'value'=>'reskontro')) ?>
  <tr>
  	<th rowspan="11">
  	R<br />E<br />S<br />K<br />O<br />N<br />T<br />R<br />O<br />
  	</th>
	<td rowspan="4">Saldobalanse</td>
    <td>Reskontro fra hovedbok kontonummer</td>
    <td><?
        $aconf = array();
        $aconf['table']           = 'report';
        $aconf['field']           = 'selectedAccount';
        $aconf['type'][] 		  = 'hovedbokwreskontro';
        $aconf['tabindex']        = '';
        $aconf['accesskey']       = 'K';
        $aconf['required']        = true;
        print $_lib['form3']->accountplan_number_menu($aconf);
        ?>
    </td>
      <td>Reskontro fra</td>
      <td>
      <? print $_lib['form3']->accountplan_number_menu(array('table' => 'report', 'field' => 'ReskontroFromAccount', 'type' => array(0 => 'reskontro', 1 => 'employee'))) ?>
      </td>
      <td>Reskontro til</td>
      <td><? print $_lib['form3']->accountplan_number_menu(array('table' => 'report', 'field' => 'ReskontroToAccount', 'type' => array(0 => 'reskontro', 1 => 'employee'))) ?>
      </td>
  </tr>
  <tr>
    <td>Fra periode</td>
    <td>
        <?
            print $_lib['form3']->AccountPeriod_menu3(array('table' => 'report', 'field' => 'FromPeriod', 'noaccess' => true, 'value'=>$firstPeriodThisYear));
        ?>
    </td>
    <td>Til periode</td>
    <td>
        <?
            print $_lib['form3']->AccountPeriod_menu3(array('table' => 'report', 'field' => 'ToPeriod', 'noaccess' => true, 'value'=>$lastPeriodThisYear));
        ?>
    </td>
    <td>Resultat fra</td>
    <td>
        <?
            print $_lib['form3']->AccountPeriod_menu3(array('table' => 'report', 'field' => 'ResultFromPeriod', 'value' => $firstPeriodThisYear, 'noaccess' => true));
        ?>
    </td>
    </tr>
    <tr>
        <td>Avdeling</td>
        <td><?
            $aconf = array();
            $aconf['table']         = 'report';
            $aconf['field']         = 'DepartmentID';
            $aconf['accesskey']     = 'D';
            $_lib['form2']->department_menu2($aconf);
            ?>
        </td>
        <td>Prosjekt</td>
        <td><?
            $aconf = array();
            $aconf['table']         = 'report';
            $aconf['field']         = 'ProjectID';
            $aconf['accesskey']     = 'P';
            $_lib['form2']->project_menu2($aconf);
            ?>
        </td>
        <td>Art</td>
        <td><? $_lib['form2']->Type_menu2(array('field' => 'VoucherType', 'type' => 'VoucherType', 'table' => 'report')) ?></td>
    </tr>
    <tr>
      <td>Vis bare aktive konto</td>
      <td><? print $_lib['form3']->checkbox(array('table'=>'accountplan', 'field'=>'Active', 'value'=>'1')) ?></td>
      <td colspan="3"></td>
      <td align="right"><input type="submit" name="show_report_search" value="Kj&oslash;r rapport"  class="button"></td>
    </tr>
</form>
<form class="voucher" name="<? print $form_name ?>" action="<? print $_lib['sess']->dispatch ?>" method="get" target="_blank">
<input type="hidden" name="t"           value="report.reskontrovoucherprint">
<input type="hidden" name="report.Type" value="reskontro">
<input type="hidden" name="report.Sort" value="VoucherDate">
  <tr class="r0">
	<td rowspan="4">bilagsutskrift</td>
    <td>Reskontro fra hovedbok kontonummer</td>
    <td><?
        $aconf = array();
        $aconf['table']           = 'report';
        $aconf['field']           = 'selectedAccount';
        $aconf['type'][] 		  = 'hovedbokwreskontro';
        $aconf['tabindex']        = '';
        $aconf['accesskey']       = 'K';
        $aconf['required']        = true;
        print $_lib['form3']->accountplan_number_menu($aconf);
        ?>
    </td>
    <td>Fra konto</td>
    <td>
        <?
            print $_lib['form3']->accountplan_number_menu(array('table' => 'report', 'field' => 'FromAccount', 'type' => array(0 => 'reskontro', 1 => 'employee')));
        ?>
    </td>
    <td>Til Konto</td>
    <td>
        <?
            print $_lib['form3']->accountplan_number_menu(array('table' => 'report', 'field' => 'ToAccount', 'type' => array(0 => 'reskontro', 1 => 'employee')));
        ?>
    </td>
</tr>
<tr class="r0">
    <td>Fra periode.</td>
    <td><? print $_lib['form3']->AccountPeriod_menu3(array('table' => 'report', 'field' => 'FromPeriod', 'noaccess' => true, 'value'=>$firstPeriodThisYear)) ?></td>
    <td>Til periode</td>
    <td><? print $_lib['form3']->AccountPeriod_menu3(array('table' => 'report', 'field' => 'ToPeriod', 'noaccess' => true, 'value'=>$lastPeriodThisYear)) ?></td>
    <td>Art</td>
    <td><? $_lib['form2']->Type_menu2(array('field' => 'VoucherType', 'type' => 'VoucherType', 'table' => 'report')) ?></td>
</tr>
<tr class="r0">
	<td colspan="6">Hvis du &oslash;nsker at rapporten skal stemme med hovedbok m&aring; du kj&oslash;re ut fra du startet regnskapet</td>
</tr>
<tr class="r0">
    <td>Avdeling</td>
    <td><?
        $aconf = array();
        $aconf['table']         = 'report';
        $aconf['field']         = 'DepartmentID';
        $aconf['accesskey']     = 'D';
        $_lib['form2']->department_menu2($aconf);
        ?>
    </td>
    <td>Prosjekt</td>
    <td><?
        $aconf = array();
        $aconf['table']         = 'report';
        $aconf['field']         = 'ProjectID';
        $aconf['accesskey']     = 'P';
        $_lib['form2']->project_menu2($aconf);
        ?>
    </td>
    <td></td>
    <td align="right"><input type="submit" name="show_report_search" value="Kj&oslash;r rapport"  class="button"></td>
  </tr>
</form>
    <tr>
    <td rowspan="2">&aring;pne poster</td>
    <form class="voucher" name="<? print $form_name ?>" action="<? print $_SETUP['DISPATCH'] ?>" method="get" target="_blank">
    <input type="hidden" name="t"           value="postmotpost.list">
    <input type="hidden" name="report_Sort" value="JournalID">
        <td>Reskontro fra hovedbok kontonummer</td>
        <td><?
            $aconf = array();
            $aconf['name']            = 'AccountPlanID';
	        $aconf['type'][] 		  = 'hovedbokwreskontro';
            $aconf['tabindex']        = '';
            $aconf['accesskey']       = 'K';
            $aconf['required']        = true;
            print $_lib['form3']->accountplan_number_menu($aconf);
            ?>
        </td>
        <td>Fra</td>
        <td>
        <? print $_lib['form3']->accountplan_number_menu(array('name' => 'ReskontroFromAccount', 'type' => array(0 => 'reskontro', 1 => 'employee'))) ?>
        </td>
        <td>Til</td>
        <td>
        <? print $_lib['form3']->accountplan_number_menu(array('name' => 'ReskontroToAccount', 'type' => array(0 => 'reskontro', 1 => 'employee'))) ?>
        </td>
        </tr>
        <tr>
        <td>Avdeling</td>
        <td><?
            $aconf = array();
            $aconf['table']         = 'report';
            $aconf['field']         = 'DepartmentID';
            $aconf['accesskey']     = 'D';
            $_lib['form2']->department_menu2($aconf);
            ?>
        </td>
        <td>Prosjekt</td>
        <td><?
            $aconf = array();
            $aconf['table']         = 'report';
            $aconf['field']         = 'ProjectID';
            $aconf['accesskey']     = 'P';
            $_lib['form2']->project_menu2($aconf);
            ?>
        </td>
        <td></td>
        <td align="right"><input type="submit" name="show_report_search" value="Kj&oslash;r rapport"  class="button"></td>
    </tr>
    </form>
</table>

<hr>

<table class="lodo_data" width="100%">
<form class="voucher" name="<? print $form_name ?>" action="<? print $_lib['sess']->dispatch ?>t=report.terminoppgave" method="post" target="_blank">
<input type="hidden" name="report.Type" value="reskontro">
<input type="hidden" name="report.Sort" value="VoucherDate">

<tr>
	<th rowspan="5">L<br />&Oslash;<br />N<br />N<br /></th>
    <td>Terminoppgave skattetrekk og arbeidsgiveravgift</td>
    <td>Fra periode</td>
    <td>
        <?
            print $_lib['form3']->AccountPeriod_menu3(array('name' => 'FromPeriod', 'noaccess' => true, 'value'=>$firstPeriodThisYear));
        ?>
    <td>Til periode</td>
    <td>
        <?
            print $_lib['form3']->AccountPeriod_menu3(array('name' => 'ToPeriod', 'noaccess' => true, 'value'=>$lastPeriodThisYear));
        ?>

    <td align="right"><input type="submit" name="show_report_search" value="Kj&oslash;r rapport"  class="button"></td>
</tr>
</form>
<form class="voucher" name="<? print $form_name ?>" action="<? print $_lib['sess']->dispatch ?>t=arbeidsgiveravgift.salaryreport" method="post" target="_blank">
<tr class="r0">
    <td>L&oslash;nns- og trekkoppgave for</td>
    <td>Velg &aring;r</td>
    <td>
        <?
            print $_lib['form3']->Type_menu3(array('table' => 'report', 'field' => 'Year', 'type'=>'PosibleSalaryYears', 'required'=>true, 'value' => $thisYear));
        ?>
    <td>For ansatt:</td>
    <td>
        <?
        $aconf = array('table'=>'report', 'field'=>'Employee', 'type' => array(0 => 'employee'), 'required'=>true);
        print $_lib['form3']->accountplan_number_menu($aconf);
        ?>

    <td align="right"><input type="submit" name="show_report_search" value="Kj&oslash;r rapport"  class="button"></td>
</tr>
</form>

<form class="voucher" name="<? print $form_name ?>" action="<? print $_lib['sess']->dispatch ?>t=arbeidsgiveravgift.inberarbeidsgiveravgift" method="post" target="_blank">
<tr>
    <td>Innberettet arbeidsgiveravgift</td>
    <td>Velg &aring;r</td>
    <td>
        <?
            print $_lib['form3']->Type_menu3(array('table' => 'report', 'field' => 'Year', 'type'=>'PosibleSalaryYears', 'required'=>true, 'value' => $thisYear));
        ?>
    <td colspan="2">&nbsp;</td>
    <td align="right"><input type="submit" name="show_report_search" value="Kj&oslash;r rapport"  class="button"></td>
</tr>
</form>

<form class="voucher" name="<? print $form_name ?>" action="<? print $_lib['sess']->dispatch ?>t=feriepenger.feriepenger" method="post" target="_blank">
<tr class="r0">
    <td>Avsettning feriepenger og arbeidsgiver avgift</td>
    <td>Velg &aring;r</td>
    <td>
        <?
            print $_lib['form3']->Type_menu3(array('table' => 'report', 'field' => 'Year', 'type'=>'PosibleSalaryYears', 'required'=>true, 'value' => $thisYear));
        ?>
    <td colspan="2">&nbsp;</td>
    <td align="right"><input type="submit" name="show_report_search" value="Kj&oslash;r rapport"  class="button"></td>
</tr>
</form>

<form class="voucher" name="<? print $form_name ?>" action="<? print $_lib['sess']->dispatch ?>t=timesheets.report_proxy" method="post" target="_blank">
<tr>
    <td>Prosjektsrapport</td>
    <td>Prosjekt</td>
    <td><?
            $aconf = array();
            $aconf['table']         = 'report';
            $aconf['field']         = 'ProjectID';
            $aconf['accesskey']     = 'P';
            $_lib['form2']->project_menu2($aconf);
            ?>
    </td>

    <td>Periode</td>
    <td><?
           $timesheet_period_sql = "SELECT CONCAT(YEAR(date), '-', MONTH(date)) as one, CONCAT(YEAR(date), '-', MONTH(date)) as two FROM timesheets GROUP BY one";
           print $_lib['form3']->_MakeSelect(array('query' => $timesheet_period_sql, 'name' => 'report_Project'));
            ?>
    </td>

    <td align="right"><input type="submit" name="show_report_search" value="Kj&oslash;r rapport"  class="button"></td>
</tr>
</form>

</table>

<hr>

<table class="lodo_data" width="100%">
<tr>
	<th rowspan="4">R<br />A<br />P<br />P<br />O<br />R<br />T<br />E<br />R<br /></th>
	<td><a href="<? print $_lib['sess']->dispatch ?>t=mvaavstemming.list" target="_blank">MVA Avstemming</a></td
    <td><a href="<? print $_lib['sess']->dispatch ?>t=report.verify_consistency&report_Type=balancenotok&report_Sort=VoucherID" target="_blank">Bilags, balanse, resultat, reskontro, dato og periode kontroll</a></td>
</tr>
<tr>
    <td><a href="<? print $_lib['sess']->dispatch ?>t=report.regnskapsrapport" target="_blank">Kortfattet rapport / Regnskapsrapport</a></td>
    <td><a href="<? print $_lib['sess']->dispatch ?>t=report.pengeflyt" target="_blank">Pengeflyt</a></td>
</tr>
<tr>
    <td><a href="<? print $_lib['sess']->dispatch ?>t=report.privatforbruk" target="_blank">Privat forbruk</a></td>
    <td><a href="<? print $_lib['sess']->dispatch ?>t=budget.list" target="_blank">Resultatsbudsjett og likviditetsbudsjett</a></td>
</tr>
<tr>
    <td><a href="<? print $_lib['sess']->dispatch ?>t=varelager.list">Varelager</a></td>
    <td><a href="<? print $_lib['sess']->dispatch ?>t=report.tellbilagrapport" target="_blank">Antall bilag og posteringer</a></td>
</tr>
</table>

<hr>

<table class="lodo_data" width="100%">
<form class="voucher" name="<? print $form_name ?>" action="<? print $_lib['sess']->dispatch ?>t=report.dagbok" method="post" target="_blank">
<input type="hidden" name="report.Type" value="dagbok">
<tr>
	<th rowspan="10">D<br />A<br />G<br />B<br />O<br />K<br /></th>
    <td>Fra dato (YYYY-MM-DD)</td>
    <td><? print $_lib['form3']->text(array('table' => 'report', 'field' => 'FromDate', 'width' => 10)) ?></td>
    <td>Til dato (YYYY-MM-DD)</td>
    <td><? print $_lib['form3']->text(array('table' => 'report', 'field' => 'ToDate', 'width' => 10)) ?></td>
</tr>
  <tr>
    <td>Fra periode</td>
    <td>
        <?
            print $_lib['form3']->AccountPeriod_menu3(array('table' => 'report', 'field' => 'FromPeriod', 'noaccess' => true));
        ?>
    </td>
    <td>Til periode</td>
    <td>
        <?
            print $_lib['form3']->AccountPeriod_menu3(array('table' => 'report', 'field' => 'ToPeriod', 'noaccess' => true));
        ?>
    </td>
  </tr>
<tr>
    <td>MVA Kode</td>
    <td><input type="text" name="report.VatID" value=""  size="4" class="number"></td>
    <td>MVA %</td>
    <td><input type="text" name="report.Vat" value=""  size="4" class="number"></td>
</tr>
<tr>
    <td>Fra kontonummer</td>
    <td><?
        $aconf = array();
        $aconf['table']         = 'report';
        $aconf['field']         = 'FromAccount';
        $aconf['accesskey']     = 'K';
        $aconf['type'][] 		= 'employee';
        $aconf['type'][] 		= 'hovedbok';
        $aconf['type'][] 		= 'reskontro';
        print $_lib['form3']->accountplan_number_menu($aconf);
        ?>
    </td>
    <td>Til kontonummer</td>
    <td><?
        $aconf = array();
        $aconf['table']         = 'report';
        $aconf['field']         = 'ToAccount';
        $aconf['accesskey']     = '';
        $aconf['type'][] 		= 'employee';
        $aconf['type'][] 		= 'hovedbok';
        $aconf['type'][] 		= 'reskontro';

        print $_lib['form3']->accountplan_number_menu($aconf);
        ?>
    </td>
</tr>
<tr>
    <td>Avdeling
    <td><?
        $aconf = array();
        $aconf['table']         = 'report';
        $aconf['field']         = 'DepartmentID';
        $aconf['accesskey']     = 'D';
        $_lib['form2']->department_menu2($aconf);
        ?>
     </td>
     <td>Prosjekt</td>
     <td><?
         $aconf = array();
         $aconf['table']         = 'report';
         $aconf['field']         = 'ProjectID';
         $aconf['accesskey']     = 'P';
         $_lib['form2']->project_menu2($aconf);
         ?>
    </td>
</tr>
<tr>
    <td>Fra bilagsnummer</td>
    <td><input type="text" name="report.FromJournal" value=""  size="5" class="number"></td>
    <td>Til bilagsnummer</td>
    <td><input type="text" name="report.ToJournal" value=""  size="5" class="number"></td>
</tr>
<tr>
    <td>Fritekst (Kidreferanse/Tekst)</td>
    <td><input type="text" name="report.Search" value=""  size="5" class="number"></td>
    <td>Sorter etter</td>
    <td>
        <select name="report.Sort">
        <option value="JournalID"     <? if($_REQUEST['report_Sort'] == 'PeriodID')      print "selected"; ?>>Bilagsnummer
        <option value="VoucherDate"   <? if($_REQUEST['report_Sort'] == 'VoucherDate')   print "selected"; ?>>Dato
        <option value="AccountPlanID" <? if($_REQUEST['report_Sort'] == 'AccountPlanID') print "selected"; ?>>Kontonummer
        </select>
    </td>
</tr>
<tr>
    <td>Art</td>
    <td><? $_lib['form2']->Type_menu2(array('field' => 'VoucherType', 'type' => 'VoucherType', 'table' => 'report')) ?></td>
    <td>Bel&oslash;p</td>
    <td><input type="text" name="report.Amount" value=""  size="6" class="number"></td>
</tr>
<tr>
    <td>KID</td>
    <td><input type="text" name="report.KID" value=""  size="6" class="number"></td>
    <td>Fakturanummer</td>
    <td><input type="text" name="report.InvoiceID" value=""  size="6" class="number"></td>
</tr>

<tr>
    <td></td>
    <td></td>
    <td></td>
    <td align="right"><input type="submit" name="show_report_search" value="Kj&oslash;r rapport"  class="button"></td>
</tr>

</form>
</table>

<hr>

<table class="lodo_data" width="100%">
<?
$firstPeriod = $_lib['date']->get_this_year($_lib['sess']->get_session('LoginFormDate'))."-01";
$thisPeriod = $_lib['date']->get_this_period($_lib['sess']->get_session('LoginFormDate'));
?>
<form class="voucher" name="<? print $form_name ?>" action="<? print $_lib['sess']->dispatch ?>t=report.general&amp;report_FromPeriod=<? print $firstPeriod ?>&amp;report_ToPeriod=<? print $thisPeriod ?>" method="post" target="_blank">
<tr>
	<th rowspan="10">R<br />A<br />P<br />P<br />O<br />R<br />T<br />E<br />R<br /></th>
    <td>Fra periode
    <td><? print $_lib['form3']->AccountPeriod_menu3(array('table' => 'report', 'field' => 'FromPeriod', 'noaccess' => true, 'value'=>$firstPeriodThisYear)); ?></td>
    <td>Til periode</td>
    <td><? print $_lib['form3']->AccountPeriod_menu3(array('table' => 'report', 'field' => 'ToPeriod', 'noaccess' => true, 'value'=>$lastPeriodThisYear)); ?></td>
    <td>Resultat fra</td>
    <td><? print $_lib['form3']->AccountPeriod_menu3(array('table' => 'report', 'field' => 'ResultFromPeriod', 'value' => $firstPeriodThisYear, 'noaccess' => true)); ?></td>
    <td>Fjord&aring;retstall</td>
    <td><? print $_lib['form3']->checkbox(array('table'=>'report', 'field'=>'EnableLastYear',  'value' => 0)) ?></td>
    <td>Vis Bare delsummer</td>
    <td><? print $_lib['form3']->checkbox(array('table'=>'report', 'field'=>'EnableOnlyPartSum',  'value' => 0)) ?></td>
</tr>
<tr>
    <td><input type="submit" name="show_report1" value="Offisielt regnskap"                       class="button"></td>
    <td colspan="2"><input type="submit" name="show_report2" value="Selvangivelse for n&aelig;ringsdrivende"  class="button"></td>
    <td><input type="submit" name="show_report3" value="N&aelig;ringsoppgave 1"                   class="button"></td>
    <td colspan="2"><input type="submit" name="show_report4" value="Selvangivelse for aksjeselskap"           class="button"></td>
    <td><input type="submit" name="show_report5" value="N&aelig;ringsoppgave 2"                   class="button"></td>
</tr>
<tr>
    <td><input type="submit" name="show_report6" value="Rapport 6"  class="button"></td>
    <td colspan="2"><input type="submit" name="show_report7" value="Rapport 7"  class="button"></td>
    <td><input type="submit" name="show_report8" value="Rapport 8"  class="button"></td>
    <td colspan="2"><input type="submit" name="show_report9" value="Rapport 9"  class="button"></td>
    <td><input type="submit" name="show_report10" value="Rapport 10"  class="button"></td>
</tr>
</form>
</table>

<hr>

<table class="lodo_data" width="100%">
<form class="voucher" name="<? print $form_name ?>" action="<? print $_lib['sess']->dispatch ?>t=borettslag.raport" method="post" target="_blank">
<tr>
	<th>B<br />O<br />R<br />E<br />T<br />T<br />S<br />L<br />A<br />G<br /></th>
    <td>&Aring;rstall
    <td colspan="2">
  <select name="arstall" size="1">
  <option value="">Ikke valgt</option>
<?
$query = "select Arstall from borettslagarsoppgjor order by Arstall desc;";
$query_handler = $_lib['db']->db_query($query);
while ($bRow = $_lib['db']->db_fetch_object($query_handler))
{
?>
    <option value="<? print $bRow->Arstall; ?>"><? print $bRow->Arstall; ?></option>
<?
}
?>
  </select>
    <!-- <input type="text" name="report.FromDate" value=""  size="10" class="number"> -->
    <td align="right"><input type="submit" name="show_report_search" value="Kj&oslash;r rapport"  class="button"></td>
</tr>
</form>
</table>



<? includeinc('bottom') ?>
</body>
</html>
