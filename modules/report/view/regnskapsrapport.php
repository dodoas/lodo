<?
# $Id: regnskapsrapport.php,v 1.15 2005/11/18 07:35:46 thomasek Exp $ invoice_edit.php,v 1.7 2001/11/20 17:55:12 thomasek Exp $
# Based on EasyComposer technology
# Copyright Thomas Ekdahl, 1994-2005, thomas@ekdahl.no, http://www.ekdahl.no

$Period         = $_REQUEST['Period'];
$detail         = $_REQUEST['detail'];
$DepartmentID   = $_REQUEST['report_DepartmentID'];
$ProjectID      = $_REQUEST['report_ProjectID'];

$db_table = "shortreport";
require_once "record.inc";
// Rettet av Geir 28.11.2005
includelogic('linetextmap/linetextmap');
includelogic('report/regnskapsrapport');

$rapport = new framework_logic_regnskapsrapport(array('Period' => $Period, 'LineID' => $_REQUEST['LineID'], 'DepartmentID' => $DepartmentID, 'ProjectID'=> $ProjectID));
?>
<? print $_lib['sess']->doctype ?>
<head>
    <title>Empatix - <? print $_lib['sess']->get_companydef('CompanyName') ?> : <? print $_lib['sess']->get_person('FirstName') ?> <? print $_lib['sess']->get_person('LastName') ?> - Kortfattet rapport (ReportID=100)</title>
    <meta name="cvs"                content="$Id: regnskapsrapport.php,v 1.15 2005/11/18 07:35:46 thomasek Exp $" />
    <? includeinc('head') ?>
</head>
<body>

<? includeinc('top') ?>
<? includeinc('left') ?>

<form name="velg" action="<? print $MY_SELF ?>" method="post">
    <table border="0" cellspacing="0">
        <thead>
            <tr>
                <th>Periode</th>
                <th>Avdeling</th>
                <th>Prosjekt</th>
                <th>Detaljer</th>
                <th>Til m&aring;ned</th>
            </tr>
            <tr>
                <th><? print $_lib['form3']->AccountPeriod_menu3(array('name'=>'Period', 'value'=>$rapport->Period, 'noaccess'=>'1')) ?></th>
                <th><?
                    $aconf = array();
                    $aconf['table']         = 'report';
                    $aconf['field']         = 'DepartmentID';
                    $aconf['accesskey']     = 'D';
                    $aconf['value']         = $DepartmentID;
                    $_lib['form2']->department_menu2($aconf);
                    ?>
                </th>
                <th><?
                    $aconf = array();
                    $aconf['table']         = 'report';
                    $aconf['field']         = 'ProjectID';
                    $aconf['accesskey']     = 'P';
                    $aconf['value']         = $ProjectID;
                    $_lib['form2']->project_menu2($aconf);
                    ?>
                </th>
                <th><? print $_lib['form3']->checkbox(array('name' => 'detail', 'value' => $detail)); ?></th>
                <th><input type="submit" value="Velg periode (V)" name="velg_periode" accesskey="V"></th>
            </tr>
    </table>
</form>
<br><br>
<form name="update" action="<? print $MY_SELF ?>&Period=<? print $rapport->Period ?>" method="post">
<? print $_lib['form3']->hidden(array('name' => 'detail',               'value' => $detail)) ?>
<? print $_lib['form3']->hidden(array('name' => 'Period',               'value' => $Period)) ?>
<? print $_lib['form3']->hidden(array('name' => 'report_DepartmentID',  'value' => $DepartmentID)) ?>
<? print $_lib['form3']->hidden(array('name' => 'report_ProjectID',     'value' => $ProjectID)) ?>
<table border="0" cellspacing="0">
    <thead>
        <tr>
            <th>Konto</th>
            <th>Kontonavn</th>
            <th>Fra <? print $rapport->thisStartPeriod ?> til <? print $rapport->thisEndPeriod ?></th>
            <th>Prosent</th>
            <th>Fra <? print $rapport->prevStartPeriod ?> til <? print $rapport->prevEndPeriod ?></th>
            <th>Prosent</th>
            <th>&Aring;ret <? print $rapport->prevYear ?></th>
            <th>Prosent</th>
            <th>Beregn prosent fra</th>
    <tbody>
        <?
        foreach($rapport->lineSumH as $lineH) { ?>
            <tr>
                <th class="sub"><? print $lineH['LineID'] ?></td>
                <th class="sub"><? print $lineH['LineText'] ?></td>
                <td class="number"><? print $_lib['format']->Amount($lineH['ThisYearAmount']) ?></td>
                <td class="number"><? print $_lib['format']->Amount($lineH['ThisYearPercent']) ?>%</td>
                <td class="number"><? print $_lib['format']->Amount($lineH['LastYearAmount']) ?></td>
                <td class="number"><? print $_lib['format']->Amount($lineH['LastYearPercent']) ?>%</td>
                <td class="number"><? print $_lib['format']->Amount($lineH['Year']) ?></td>
                <td class="number"><? print $_lib['format']->Amount($lineH['Percent']) ?>%</td>
                <td class="number"><? print $_lib['form3']->radiobutton(array('name' => 'LineID', 'value' => $lineH['LineID'], 'choice' => $rapport->LineID)); ?></td>
            </tr>
            
            <?
            if($detail) {
                foreach($rapport->lineH[$lineH['LineID']] as $AccountH)
                {
                    ?>
                    <tr>
                    <td><? print $AccountH['AccountPlanID'] ?></td>
                    <td><? print $AccountH['AccountName'] ?></td>
                    <td align="right"><? print $_lib['format']->Amount($AccountH['ThisYearAmount']) ?></td>
                    <td align="right"></td>
                    <td align="right"><? print $_lib['format']->Amount($AccountH['LastYearAmount']) ?></td>
                    <td align="right"></td>
                    <td align="right"><? print $_lib['format']->Amount($AccountH['Year']) ?></td>
                    <td align="right"></td>
                    </tr>
                    <?
                }
            }
        }
        ?>
    <tr>
        <td colspan="8"></td>
        <td colspan="3" align="right"><? print $_lib['form3']->submit(array('name'=>'show_percentcalculation', 'value'=>'Kalkuler (K)', 'accesskey'=>'K')) ?></td>
    </tr>
</table>
</form>
<br/><br/>

<form name="update" action="<? print $MY_SELF ?>&Period=<? print $rapport->Period ?>" method="post">
<? print $_lib['form3']->hidden(array('name' => 'detail', 'value' => $detail)) ?>
<? print $_lib['form3']->hidden(array('name' => 'Period', 'value' => $Period)) ?>
    <table border="0" cellspacing="0" cellpadding="0">
        <tbody>
            <tr>
                <td></td>
                <td align="center">Ja</td>
                <td align="center">Nei</td>
                <td align="center">Ikke aktuell</td>
            </tr>
            <tr>
                <td>Levert papirene i rett tid</td>
                <td align="center"><? print $_lib['form3']->radiobutton(array('table'=>$rapport->db_table, 'field'=>'DeliveredOnTime', 'value'=>'1', 'choice'=>$rapport->info->DeliveredOnTime)) ?></td>
                <td align="center"><? print $_lib['form3']->radiobutton(array('table'=>$rapport->db_table, 'field'=>'DeliveredOnTime', 'value'=>'0', 'choice'=>$rapport->info->DeliveredOnTime)) ?></td>
                <td align="center"><? print $_lib['form3']->checkbox(array('table'=>$rapport->db_table, 'field'=>'DeliveredOnTimeNo', 'value'=>$rapport->info->DeliveredOnTimeNo)) ?></td>
            </tr>
            <tr>
                <td>Levert papirene riktig<br>sortert/nummerert</td>
                <td align="center"><? print $_lib['form3']->radiobutton(array('table'=>$rapport->db_table, 'field' => 'DeliveredSorted',  'value' => '1', 'choice'=>$rapport->info->DeliveredSorted)) ?></td>
                <td align="center"><? print $_lib['form3']->radiobutton(array('table'=>$rapport->db_table, 'field' => 'DeliveredSorted',  'value' => '0', 'choice'=>$rapport->info->DeliveredSorted)) ?></td>
                <td align="center"><? print $_lib['form3']->checkbox(array('table'=>$rapport->db_table,    'field' => 'DeliveredSortedNo','value'  => $rapport->info->DeliveredSortedNo)) ?></td>
            </tr>

            <tr>
                <td>Bilag levert til scanning</td>
                <td align="center"><? print $_lib['form3']->checkbox(array('table'=>$rapport->db_table, 'field' => 'VouchersDeliveredForScanning', 'value' => $rapport->info->VouchersDeliveredForScanning)) ?></td>
                <td align="center"></td>
                <td align="center"></td>
            </tr>

            <tr>
                <td>Bilag scannet</td>
                <td align="center"><? print $_lib['form3']->checkbox(array('table'=>$rapport->db_table, 'field' => 'VouchersScanned', 'value' => $rapport->info->VouchersScanned)) ?></td>
                <td align="center"></td>
                <td align="center"></td>
            </tr>

            <tr>
                <td>Betale ekstra for</td>
                <td align="center"><? print $_lib['form3']->text(array('table'=>$rapport->db_table, 'field'=>'PayExtra1', 'value'=>$rapport->info->PayExtra1, 'width'=>'10')) ?></td>
                <td align="center"><? print $_lib['form3']->text(array('table'=>$rapport->db_table, 'field'=>'PayExtra2', 'value'=>$rapport->info->PayExtra2, 'width'=>'10')) ?></td>
                <td align="center"><? print $_lib['form3']->text(array('table'=>$rapport->db_table, 'field'=>'PayExtra3', 'value'=>$rapport->info->PayExtra3, 'width'=>'10')) ?></td>
            </tr>
            <tr>
                <td>Momenter</td>
                <td colspan="3"><? print $_lib['form3']->textarea(array('table'=>$rapport->db_table, 'field'=>'Elements', 'value'=>$rapport->info->Elements, 'width'=>'50')) ?></td>
            </tr>
            <tr>
                <td>Ros</td>
                <td colspan="3"><? print $_lib['form3']->textarea(array('table'=>$rapport->db_table, 'field'=>'Praise', 'value'=>$rapport->info->Praise, 'width'=>'50')) ?></td>
            </tr>
            <tr>
                <td>Forbedringer</td>
                <td colspan="3"><? print $_lib['form3']->textarea(array('table'=>$rapport->db_table, 'field'=>'Improvements', 'value'=>$rapport->info->Improvements, 'width'=>'50')) ?></td>
            </tr>
            <? if($_lib['sess']->get_person('AccessLevel') >= 3) { ?>
            <tr height="10">
                <td colspan="4"></td>
            </tr>
            <tr>
                <td colspan="1"></td>
                <td colspan="3" align="right"><? print $_lib['form3']->submit(array('name'=>'action_shortreport_update', 'value'=>'Lagre/Oppdater rapporten (S)', 'accesskey'=>'S')) ?></td>
            </tr>
            <? } ?>
    </table>
</form>

</body>
</html>