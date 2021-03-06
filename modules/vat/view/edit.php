<?
# $Id: edit.php,v 1.40 2005/10/28 14:18:38 thomasek Exp $ person_list.php,v 1.3 2001/11/20 18:04:43 thomasek Exp $
# Based on EasyComposer technology
# Copyright Thomas Ekdahl, 1994-2005, thomas@ekdahl.no, http://www.ekdahl.no

$VatID = $_REQUEST['VatID'];
assert(!is_int($VatID)); #All main input should be int

$db_table = "vat";
$form_name = 'vat_edit';
includelogic('accounting/accounting');
$accounting = new accounting();
require_once "record.inc";

/* Sokestreng */
$date = $_lib['sess']->get_session('LoginFormDate');
$query_vat  = "select v.*, p.LastName, p.FirstName from vat v left join person p on p.PersonID = v.UpdateBy where v.VatID <= 62 and v.ValidFrom <= '$date' and v.ValidTo >= '$date' order by VatID asc";
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

<script  type="text/javascript">
  function copyForm(button){
    var insterted_element;
    var fields = $(button).parents('tr')[0].querySelectorAll('input:not([type=submit]), select');
    var formHolder = button.parentElement.querySelector('.form_holder');
    for (var i = 0; i < fields.length; i++) {
      insterted_element = formHolder.appendChild(fields[i].cloneNode(true));
      if (fields[i].tagName === "SELECT"){
        insterted_element.value = fields[i].value
      }
    }
  }
</script>

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
            <th>Kategori</th>
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
                    <th colspan="11" class="sub">Kj&oslash;p</th>
                </tr>
                <?
            }
            if($vat->VatID == 10)
            {
                $edit = true;
                ?>
                <tr>
                    <th colspan="11" class="sub">Salg</th>
                </tr>
                <?
            }
            ?>
            <tr>
                    <td class="menu">
                      <input type="hidden" name="ID"      value="<? print $vat->ID ?>">
                      <input type="hidden" name="vat_VatID"   value="<? print $vat->VatID ?>">
                      <input type="hidden" name="vat_UpdateBy"   value="<? print $_SESSION['login_id'] ?>">
                      <? print $vat->VatID ?>
                    </td>
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
                          print $_lib['form3']->accountplan_number_menu(array('table' => 'vat', 'field' => 'AccountPlanID', 'value' => $vat->AccountPlanID, 'type' => array(0 => 'balance')));
                        ?>

                    </td>
                    <?
                    if($vat->Type == 'sale')
                    {
                        ?>
                        <td><? print $_lib['form3']->checkbox(array('table'=>'vat', 'field' => 'Active',           'value' => $vat->Active)) ?></td>
                        <td><? print $_lib['form3']->checkbox(array('table'=>'vat', 'field' => 'EnableVatOverride',    'value' => $vat->EnableVatOverride)) ?></td>
                        <td>
                          <!-- Need form for date picker -->
                          <form name="<? print $form_name.$vat->ID.'ValidFrom'; ?>" action="<? print $MY_SELF ?>" method="post">
                          <? print $_lib['form3']->date(array('table'=>'vat', 'field' => 'ValidFrom', 'form_name' => $form_name.$vat->ID.'ValidFrom', '_number' => $vat->ID, 'value' => $vat->ValidFrom, 'width' => 10)) ?>
                          </form>
                        </td>

                        <td>
                          <!-- Need form for date picker -->
                          <form name="<? print $form_name.$vat->ID.'ValidTo'; ?>" action="<? print $MY_SELF ?>" method="post">
                          <? print $_lib['form3']->date(array('table'=>'vat', 'field' => 'ValidTo', 'form_name' => $form_name.$vat->ID.'ValidTo', '_number' => $vat->ID, 'value' => $vat->ValidTo, 'width' => 10)) ?>
                          </form>
                        </td>
                        <td><? print $_lib['form3']->text(array('table'=>'vat', 'field'=>'Category', 'value'=>$vat->Category, 'width'=>'5')); ?></td>
                        <?
                    }
                    else
                    {
                        ?>
                        <td><? if($vat->Active) { print 'X'; } ?></td>
                        <td><? if($vat->EnableVatOverride) { print 'X'; } ?></td>
                        <td><? print $vat->ValidFrom ?></td>
                        <td><? print $vat->ValidTo ?></td>
                        <td><? print $vat->Category; ?></td>
                        <?
                    }
                    ?>
                    <td colspan="2">
                      <? if($_lib['sess']->get_person('AccessLevel') >= 3) { ?>
                        <form name="<? print $form_name.$vat->ID; ?>" action="<? print $MY_SELF ?>" method="post">
                          <!-- Onclick submit of the form we would append to this span -->
                          <span class='form_holder' style="display: none"></span>

                          <input type="submit" name="action_vat_update" value="Lagre" onclick="copyForm(this)" />
                          <input type="submit" name="action_vat_new" value="Ny" onclick="copyForm(this)" />
                        </form>
                      <? } ?>
                    </td>
                    <td><? print $vat->TS ?> <? print $vat->FirstName . " " . $vat->LastName ?></td>
            </tr>
            <?
        }
        ?>
        <tr>
            <th colspan="11" class="sub">MVA satser med annet gyldighetsomr&aring;de enn i forhold til dato innlogget</th>
        </tr>
        <?
        $query_vat  = "select * from vat where VatID <= 62 and ('$date' < ValidFrom or '$date' > ValidTo) order by VatID asc, ValidFrom desc, ValidTo desc";
        #print "$query_vat<br>\n";
        $result_vat = $_lib['db']->db_query($query_vat);
        while($vat = $_lib['db']->db_fetch_object($result_vat))
        {
            ?>
            <tr>
                    <input type="hidden" name="ID"      value="<? print $vat->ID ?>">
                    <input type="hidden" name="vat_VatID"   value="<? print $vat->VatID ?>">
                    <input type="hidden" name="vat_UpdateBy"   value="<? print $_SESSION['login_id'] ?>">
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
                        <td>
                          <!-- Need form for date picker -->
                          <form name="<? print $form_name.$vat->ID.'ValidFrom'; ?>" action="<? print $MY_SELF ?>" method="post">
                            <? print $_lib['form3']->date(array('table'=>'vat', 'field' => 'ValidFrom', 'form_name' => $form_name.$vat->ID.'ValidFrom', '_number' => $vat->ID, 'value' => $vat->ValidFrom, 'width' => 10)) ?>
                          </form>
                        </td>

                        <td>
                          <!-- Need form for date picker -->
                          <form name="<? print $form_name.$vat->ID.'ValidTo'; ?>" action="<? print $MY_SELF ?>" method="post">
                            <? print $_lib['form3']->date(array('table'=>'vat', 'field' => 'ValidTo', 'form_name' => $form_name.$vat->ID.'ValidTo', '_number' => $vat->ID, 'value' => $vat->ValidTo, 'width' => 10)) ?>
                          </form>
                        </td>
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
                    <td><? print $vat->Category; ?></td>
                    <td colspan="2">
                    <?
                    if($_lib['sess']->get_person('AccessLevel') >= 3) { ?>
                      <form name="<? print $form_name.$vat->ID; ?>" action="<? print $MY_SELF ?>" method="post">
                        <!-- Onclick submit of the form we would append to this span -->
                        <span class='form_holder' style="display: none"></span>

                        <input type="submit" name="action_vat_update" value="Lagre" onclick="copyForm(this)" />
                        <input type="submit" name="action_vat_new" value="Ny" onclick="copyForm(this)" />
                      </form>
                    <? } ?>
                    </td>
                    <td><? print $vat->TS ?> <? print $vat->FirstName . " " . $vat->LastName ?></td>
            </tr>
            <?
        }
?>

 <tr>
   <td colspan="9">Oppgj&oslash;rskonto <? print $_lib['sess']->get_companydef('AccountVat') ?> merverdiavgift oppgis i firmaopplysning.<br />
   <td>

</table>
<? if($_lib['sess']->get_person('AccessLevel') > 1){ ?>
<a href="<? print $MY_SELF ?>&amp;action_vataccount_update=1">Oppdater oppgj&oslash;rskonto</a>
<?}?>

<br/>
  <a href="https://vefa.difi.no/ehf/guide/invoice-and-creditnote/2.0/no/index.html#_merverdiavgift" target="blank">Om MVA kategorier</a>
<? includeinc('bottom') ?>
</body>
</html>
