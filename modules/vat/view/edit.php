<?
# $Id: edit.php,v 1.40 2005/10/28 14:18:38 thomasek Exp $ person_list.php,v 1.3 2001/11/20 18:04:43 thomasek Exp $
# Based on EasyComposer technology
# Copyright Thomas Ekdahl, 1994-2005, thomas@ekdahl.no, http://www.ekdahl.no

$VatID = $_REQUEST['VatID'];
assert(!is_int($VatID)); #All main input should be int

$db_table = "vat";
includelogic('accounting/accounting');
$accounting = new accounting();
require_once "record.inc";

/* Sokestreng */
$date = $_lib['sess']->get_session('LoginFormDate');
$query_vat  = "select * from vat where VatID <= 62 and ValidFrom <= '$date' and ValidTo >= '$date' order by VatID asc";
//print "$query_vat<br>\n";
$result_vat = $_lib['db']->db_query($query_vat);
?>

<? print $_lib['sess']->doctype ?>
<head>
    <title>Empatix - vat</title>
    <meta name="cvs"                content="$Id: edit.php,v 1.40 2005/10/28 14:18:38 thomasek Exp $" />
    <? includeinc('head') ?>
</head>
<body>

<? includeinc('top') ?>
<? includeinc('left') ?>

    <h2>MVA-registeret</h2>
    <table class="lodo_data">
        <tr class="result">
            <th>Kode</th>
            <th>Mva%</th>
            <th>Konto</th>
            <th>Aktive</th>
            <th>Overst</th>
            <th>Gyldig fra</th>
            <th>Gyldig til</th>
            <th></th>
            <th></th>
            <th>Endret</th>
        </tr>
        <?
        while($vat = $_lib['db']->db_fetch_object($result_vat))
        {
            $i++;
            if (!($i % 2)) { $sec_color = "BGColorLight"; } else { $sec_color = "BGColorDark"; };
            if($vat->VatID == 40)
            {
                $edit = false;
                ?>
                <tr>
                    <th colspan="10" class="sub">Kj&oslash;p</th>
                </tr>
                <?
            }
            if($vat->VatID == 10)
            {
                $edit = true;
                ?>
                <tr>
                    <th colspan="10" class="sub">Salg</th>
                </tr>
                <?
            }
            ?>
            <tr>
                <form name="<? print $form_name ?>" action="<? print $MY_SELF ?>" method="post">
                    <input type="hidden" name="ID"      value="<? print $vat->ID ?>">
                    <input type="hidden" name="vat_VatID"   value="<? print $vat->VatID ?>">
                    <td class="menu"><? print $vat->VatID ?></td>
                    <td>
                        <?
                        if($vat->VatID < 30 and $vat->VatID > 10)
                        {
                            ?><input type="text" name="vat.Percent" value="<? print $vat->Percent ?>" size="5" class="number">%<?
                        }
                        elseif( ($vat->VatID == 10) || ($vat->VatID == 40) )
                        {
                            print $_lib['form3']->hidden(array('name'=>'vat.Percent', 'value'=>'0')) ?>Udefinert<?
                        }
                        elseif($vat->VatID >= 40 and $vat->VatID < 60)
                        {
                            print $vat->Percent."%";
                        }
                        elseif($vat->VatID == 60)
                        {
                            print "Kj&oslash;p avg. fritt <a href=\"#here\" title=\"Eksempel: Kj&oslash;p fra utland.\">[?]</a>";
                        }
                        elseif($vat->VatID == 62)
                        {
                            print "Kj&oslash;p u/mva <a href=\"#here\" title=\"Eksempel: Kj&oslash;p av bolig.\">[?]</a>";
                        }
                        elseif($vat->VatID == 30)
                        {
                            print "Salg avg. fritt <a href=\"#here\" title=\"Eksempel: Salg til utland.\">[?]</a>";
                        }
                        elseif($vat->VatID == 32)
                        {
                            print "Salg u/mva <a href=\"#here\" title=\"Eksempel: Utleie av bolig.\">[?]</a>";
                        }
                        ?>
                    </td>
                    <td>
                        <?
                        if($vat->VatID == 30 || $vat->VatID == 32 || $vat->VatID == 60 || $vat->VatID == 62)
                        {
                            print "Ingen konto";
                        }
                        else
                        {
                            print $_lib['form3']->accountplan_number_menu(array('table' => 'vat', 'field' => 'AccountPlanID', 'value' => $vat->AccountPlanID, 'type' => array(0 => 'balance')));
                        }
                        ?>

                    </td>
                    <?
                    if($vat->Type == 'sale')
                    {
                        ?>
                        <td><? print $_lib['form3']->checkbox(array('table'=>'vat', 'field' => 'Active',           'value' => $vat->Active)) ?></td>
                        <td><? print $_lib['form3']->checkbox(array('table'=>'vat', 'field' => 'EnableVatOverride',    'value' => $vat->EnableVatOverride)) ?></td>
                        <td><? print $_lib['form3']->date(array('table'=>'vat', 'field' => 'ValidFrom',            'value' => $vat->ValidFrom, 'width' => 10)) ?></td>
                        <td><? print $_lib['form3']->date(array('table'=>'vat', 'field' => 'ValidTo',          'value' => $vat->ValidTo, 'width' => 10)) ?></td>
                        <?
                    }
                    else
                    {
                        ?>
                        <td><? if($vat->Active) { print 'X'; } ?></td>
                        <td><? if($vat->EnableVatOverride) { print 'X'; } ?></td>
                        <td><? print $vat->ValidFrom ?></td>
                        <td><? print $vat->ValidTo ?></td>
                        <?
                    }
                    ?>
                    <td><? if($_lib['sess']->get_person('AccessLevel') >= 2) { ?><input type="submit" name="action_vat_update" value="Lagre" /><?}?></td>
                    <td><? if($_lib['sess']->get_person('AccessLevel') >= 2) { ?><input type="submit" name="action_vat_new" value="Ny" /><?}?></td>
                    <td><? print $vat->TS ?></td>
                </form>
            </tr>
            <?
        }
        ?>
        <tr>
            <th colspan="10" class="sub">MVA satser med annet gyldighetsomr&aring;de enn i forhold til dato innlogget</th>
        </tr>
        <?
        $query_vat  = "select * from vat where VatID <= 62 and ('$date' < ValidFrom  or '$date' > ValidTo) order by VatID, PairID asc";
        #print "$query_vat<br>\n";
        $result_vat = $_lib['db']->db_query($query_vat);
        while($vat = $_lib['db']->db_fetch_object($result_vat))
        {
            ?>
            <tr>
                <form name="<? print $form_name ?>" action="<? print $MY_SELF ?>" method="post">
                    <input type="hidden" name="ID"      value="<? print $vat->ID ?>">
                    <input type="hidden" name="vat_VatID"   value="<? print $vat->VatID ?>">
                    <td class="menu"><? print $vat->VatID ?></td>
                    <td>
                        <?
                        if($vat->VatID < 20 and $vat->VatID > 10)
                        {
                            ?><input type="text" name="vat.Percent" value="<? print $vat->Percent ?>" size="5" class="number">%<?
                        }
                        elseif( ($vat->VatID == 10) || ($vat->VatID == 40) )
                        {
                            print $_lib['form3']->hidden(array('name'=>'vat.Percent', 'value'=>'0')) ?>Udefinert
                        <?
                        }
                        elseif($vat->VatID >= 40 and $vat->VatID < 60)
                        {
                            print $vat->Percent."%";
                        }
                        ?>
                    </td>
                    <td>
                        <? print $_lib['form3']->accountplan_number_menu(array('table' => 'vat', 'field' => 'AccountPlanID', 'value' => $vat->AccountPlanID, 'type' => array(0 => 'balance'))); ?>
                    </td>
                    <?
                    if($vat->Type == 'sale')
                    {
                        ?>
                        <td><? print $_lib['form3']->checkbox(array('table'=>'vat', 'field' => 'Active',               'value' => $vat->Active)) ?></td>
                        <td><? print $_lib['form3']->checkbox(array('table'=>'vat', 'field' => 'EnableVatOverride',    'value' => $vat->EnableVatOverride)) ?></td>
                        <td><? print $_lib['form3']->date(array('table'=>'vat', 'field' => 'ValidFrom',            'value' => $vat->ValidFrom, 'width' => 10)) ?></td>
                        <td><? print $_lib['form3']->date(array('table'=>'vat', 'field' => 'ValidTo',          'value' => $vat->ValidTo, 'width' => 10)) ?></td>
                        <?
                    }
                    else
                    {
                        ?>
                        <td><? if($vat->Active) { print 'X'; } ?></td>
                        <td><? if($vat->EnableVatOverride) { print 'X'; } ?></td>
                        <td><? print $vat->ValidFrom ?></td>
                        <td><? print $vat->ValidTo ?></td>
                        <?
                    }
                    ?>
                    <td><? if($_lib['sess']->get_person('AccessLevel') >= 2) { ?><input type="submit" name="action_vat_new" value="Ny" /><?}?></td>
                    <td><? if($_lib['sess']->get_person('AccessLevel') >= 2) { ?><input type="submit" name="action_vat_update" value="Lagre" /><?}?></td>
                    <td><? print $vat->TS ?></td>
                </form>
            </tr>
            <?
        }
?>

 <tr>
   <td colspan="9">Oppgj&oslash;rskonto <? print $_lib['sess']->get_companydef('AccountVat') ?> merverdiavgift oppgis i firmaopplysning.<br />
   <td>

</table>
<a href="<? print $MY_SELF ?>&amp;action_vataccount_update=1">Oppdater oppgj&oslash;rskonto</a>
<? includeinc('bottom') ?>
</body>
</html>
