<?

   includelogic('weeklysale/weeklysaletemplate');
   $t = "weeklysale.edittemplate";

?>
<? print $_lib['sess']->doctype ?>
<head>
  <title>Empatix - <? print $_lib['sess']->get_companydef('CompanyName') ?> : <? print $_lib['sess']->get_person('FirstName') ?> <? print $_lib['sess']->get_person('LastName') ?> - Ukeomsetning</title>
  <meta name="cvs"                content="$Id: edit.php,v 1.67 2005/10/24 11:54:33 thomasek Exp $" />
  <? includeinc('head') ?>
  <? includeinc('javascript') ?>
</head>
<body>
<? includeinc('top') ?>
<? includeinc('left') ?>
       
<?php
    $years = WeeklysaleTemplate::listYears();
    $configs = WeeklysaleTemplate::listConfigs();
    $selected_year = (isset($_REQUEST["year"]) ? $_REQUEST["year"] : null);
    $selected_config = (isset($_REQUEST["config"]) ? $_REQUEST["config"] : null);
   ?>

<p>
  <form action="<? print $_SETUP['DISPATCH'] ?>&t=<?= $t ?>" method="post">
    &Aring;r
    <select name="year">
      <?php 
         foreach($years as $y) {
             printf("<option value='%d'>%d</option>", $y, $y);
         }
         ?>
    </select>

    Konfigurasjon:
    <select name="config">
      <? foreach($configs as $id => $name) { ?>
        <? if($id == $selected_config) { ?>
          <option value="<?= $id ?>" selected><?= $name ?></option>
        <? } else { ?>
          <option value="<?= $id ?>"><?= $name ?></option>
        <? } ?>
      <? } ?>
    </select>

    <input type="submit" value="Velg">
  </form>
</p>

<?
   if($selected_year === null) {
       exit;
   }

   $template = new WeeklysaleTemplate($selected_year, $selected_config);
   require_once('record_template.php');

   $control_row = <<<EOT
    <tr>
      <td colspan="4">
        <input type="submit" value="Lagre alle" name="template_save" />
        <input type="submit" value="+" name="template_add_blank_entry" />
      </td>
      <td colspan="4">
        <input type="submit" value="Opprett markerte" name="template_create_weeklysales" onclick="return confirm('Opprett?')" />
        <input type="submit" value="Slett markerte" name="template_delete_marked" onclick="return confirm('Slett?')" />
      </td>
    </tr>
EOT;

   ?>
<?if($_lib['message']->get()) { ?> <div class="warning"><? print $_lib['message']->get() ?></div><br><? } ?>
 
<?php $journalid_tab = 10000; ?>
<form action="<? print $_SETUP['DISPATCH'] ?>&t=<?= $t ?>" method="post" enctype="multipart/form-data">
  <input type="hidden" name="year" value="<?= $selected_year ?>" />
  <input type="hidden" name="config" value="<?= $selected_config ?>" />
  <table>
    <tr>
      <th>Id</th>
      <th>Uke</th>
      <th>F&oslash;rste dag</th>
      <th>Siste dag</th>
      <th>Periode</th>
      <th>Type</th>
      <th>Bilagsnr</th>
      <th></th>
    </tr>
    <?
       $entries = $template->listEntries();
       setlocale('LC_ALL', 'no_NO', 'no', 'no_NB');
       $i = 0; $n = count($entries);
       foreach($entries as $entry) {

           $first_day = strftime("%A", strtotime($entry['FirstDate']));
           $last_day = strftime("%A", strtotime($entry['LastDate']));
           $open = $entry['WeeklySaleID'] == 0;

           $i++;
           if($i % 20 == 0 && $n - $i > 5) echo $control_row;
    ?>

    <? if($open) { ?>
    <tr>
      <td><?= $entry["WeeklySaleTemplateID"] ?></td>
      <td><input type="text" name="weeklysaletemplate.WeekNo.<?= $entry["WeeklySaleTemplateID"] ?>" size="2" value="<?= $entry['WeekNo'] ?>" /></td>
      <td><input type="text" name="weeklysaletemplate.FirstDate.<?= $entry["WeeklySaleTemplateID"] ?>" size="10" value="<?= $entry['FirstDate'] ?>" /> <?= $first_day ?></td>
      <td><input type="text" name="weeklysaletemplate.LastDate.<?= $entry["WeeklySaleTemplateID"] ?>" size="10" value="<?= $entry['LastDate'] ?>" /> <?= $last_day ?></td>
      <td><input type="text" name="weeklysaletemplate.Period.<?= $entry["WeeklySaleTemplateID"] ?>" size="7" value="<?= $entry['Period'] ?>" /></td>
      <td><input type="text" name="weeklysaletemplate.VoucherType.<?= $entry["WeeklySaleTemplateID"] ?>" size="1" value="<?= $entry['VoucherType'] ?>" /></td>

      <? if(!$entry["journalInUse"]) { ?>
        <td><input type="text" name="weeklysaletemplate.JournalID.<?= $entry["WeeklySaleTemplateID"] ?>" size="11" value="<?= $entry['JournalID'] ?>" class="lodoreqfelt" tabindex="<?= ++$journalid_tab ?>" /></td>
        <td><input type="checkbox" name="template_selected[]" value="<?= $entry["WeeklySaleTemplateID"] ?>" /></td>
      <? } else { ?>
        <td><input type="text" name="weeklysaletemplate.JournalID.<?= $entry["WeeklySaleTemplateID"] ?>" size="11" value="<?= $entry['JournalID'] ?>" style="background-color: red" tabindex="<?= ++$journalid_tab ?>" /></td>
        <td><input type="checkbox" name="template_selected[]" value="<?= $entry["WeeklySaleTemplateID"] ?>" disabled='disabled' /></td>
      <? } ?>
    </tr>
    <? } else { ?>
    <tr>
      <td><?= $entry["WeeklySaleTemplateID"] ?>: 
        <a href="<? print $_SETUP['DISPATCH'] ?>t=weeklysale.edit&WeeklySaleID=<?= $entry["WeeklySaleID"] ?>"><?= $entry["WeeklySaleID"] ?></a>
      </td>
      <td><input type="text" name="weeklysaletemplate.WeekNo.<?= $entry["WeeklySaleTemplateID"] ?>" size="2" value="<?= $entry['WeekNo'] ?>" disabled='disabled' /></td>
      <td><input type="text" name="weeklysaletemplate.FirstDate.<?= $entry["WeeklySaleTemplateID"] ?>" size="10" value="<?= $entry['FirstDate'] ?>" disabled='disabled' /> <?= $first_day ?></td>
      <td><input type="text" name="weeklysaletemplate.LastDate.<?= $entry["WeeklySaleTemplateID"] ?>" size="10" value="<?= $entry['LastDate'] ?>" disabled='disabled' /> <?= $last_day ?></td>
      <td><input type="text" name="weeklysaletemplate.Period.<?= $entry["WeeklySaleTemplateID"] ?>" size="7" value="<?= $entry['Period'] ?>" disabled='disabled' /></td>
      <td><input type="text" name="weeklysaletemplate.VoucherType.<?= $entry["WeeklySaleTemplateID"] ?>" size="1" value="<?= $entry['VoucherType'] ?>" disabled='disabled' /></td>
      <td><input type="text" name="weeklysaletemplate.JournalID.<?= $entry["WeeklySaleTemplateID"] ?>" size="11" value="<?= $entry['JournalID'] ?>" disabled='disabled' tabindex="<?= ++$journalid_tab ?>" /></td>
      <td><input type="checkbox" name="template_selected[]" value="<?= $entry["WeeklySaleTemplateID"] ?>" disabled='disabled' /></td>
    </tr>
    <? } ?>


    <?
       }
       ?>

    <?= $control_row ?>

    <tr>
      <td colspan="3">
        <a href="<? print $_SETUP['DISPATCH'] ?>&t=weeklysale.exporttemplate&year=<?= $selected_year ?>&config=<?= $selected_config ?>">exporter</a>
      </td>
      <td colspan="5">
        <input type="file" name="fileimport" />
        <input type="submit" name="template_import_file" value="importer"/>
      </td>
    </tr>
    <tr><td></td></tr>
    <tr><td></td></tr>
    <tr><td></td></tr>
    <tr>
      <td colspan="6">
        <input type="submit" name="template_add_defaults" value="Lag standardverdier" onclick="return confirm('Lag standardverdier?')"/>
      </td>
    </tr>
  </table>
</form>


  
</body>
