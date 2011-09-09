<?
# $Id: list.php,v 1.44 2005/10/28 17:59:41 thomasek Exp $ person_list.php,v 1.3 2001/11/20 18:04:43 thomasek Exp $
# Based on EasyComposer technology
# Copyright Thomas Ekdahl, 1994-2005, thomas@ekdahl.no, http://www.ekdahl.no

$db_table = "weeklysale";

includelogic('accounting/accounting');
$accounting = new accounting();
require_once  "record.inc";

/* sortering og gruppering av data */
if (!$SORT || $SORT == "ASC") { $SORT = "DESC"; } else { $SORT = "ASC"; }
if(!$_SETUP[DB_START][0]) { $_SETUP[DB_START][0] = 0; }
if(!$CompanyID)   { $CompanyID = 1; }
if (!$order_by)   { $order_by  = "AccountNumber"; }
$db_stop = $_SETUP[DB_START][0] + $_SETUP[DB_OFFSET][0];

/* S¿kestreng */
$query_week     = "select * from $db_table order by Period desc, JournalID desc";
$result_week    = $_lib['db']->db_query($query_week);

$query_conf     = "select * from weeklysaleconf";
$result_conf    = $_lib['db']->db_query($query_conf);
?>

<? print $_lib['sess']->doctype ?>
<head>
    <title>Empatix - <? print $_lib['sess']->get_companydef('CompanyName') ?> : <? print $_lib['sess']->get_person('FirstName') ?> <? print $_lib['sess']->get_person('LastName') ?> - project list</title>
    <meta name="cvs"                content="$Id: list.php,v 1.44 2005/10/28 17:59:41 thomasek Exp $" />
    <? includeinc('head') ?>
    <script>
function createCookie(name,value,days) {
	if (days) {
		var date = new Date();
		date.setTime(date.getTime()+(days*24*60*60*1000));
		var expires = "; expires="+date.toGMTString();
	}
	else var expires = "";
	document.cookie = name+"="+value+expires+"; path=/";
}

function readCookie(name) {
	var nameEQ = name + "=";
	var ca = document.cookie.split(';');
	for(var i=0;i < ca.length;i++) {
		var c = ca[i];
		while (c.charAt(0)==' ') c = c.substring(1,c.length);
		if (c.indexOf(nameEQ) == 0) return c.substring(nameEQ.length,c.length);
	}
	return null;
}

function eraseCookie(name) {
	createCookie(name,"",-1);
}
   
function swap_checkbox(n) {
	name = "weeklysale_checkbox_" + n;

	if(readCookie(name) == null) {
		createCookie(name, "1", 1);
        }
	else {
		eraseCookie(name);
	}
}

function set_period_cookies(setup_id) {
    set_cookie('<?php echo $_SESSION['DB_NAME'] ?>_weeklysale_period_' + setup_id, 'init_periode_' + setup_id);
    set_cookie('<?php echo $_SESSION['DB_NAME'] ?>_weeklysale_init_week_' + setup_id, 'init_week_' + setup_id);
    set_cookie_simple('<?php echo $_SESSION['DB_NAME'] ?>_weeklysale_init_date_' + setup_id, 'init_date_' + setup_id);
    set_cookie_simple('<?php echo $_SESSION['DB_NAME'] ?>_weeklysale_checkbox_' + setup_id, 'checkbox_' + setup_id);
}

function set_cookie_simple(name, elid)
{
	var el = document.getElementById(elid);
	var value = el.value;
	createCookie(name, value, 1);
}

function set_cookie(name, elid)
{
	var el = document.getElementById(elid);
	var value = el.options[ el.selectedIndex ].value;
	createCookie(name, value, 1);
}

    </script>
</head>

<body>

<? includeinc('top') ?>
<? includeinc('left') ?>

<table cellspacing="0">

<? if($_lib['sess']->get_person('AccessLevel') >= 2) { ?>

<table class="lodo_data">
  <tr>
    <th>Avdelingsnummer</th>
    <th>Navn</th>
    <th>Bilagsart</th>
    <th>Bilagsnummer</th>
    <th></th>
    <th>Uke</th>
    <th>Bilagsdato</th>
    <th>Periode</th>
    <th>Opprett ny uke</th>
    <th>Oppstart</th>
    <th>Slutt</th>
    <th></th>
    <th></th>
<tbody>
<?
while($row = $_lib['db']->db_fetch_object($result_conf))
{
    list($nextJournalID, $nextMessage) = $accounting->get_next_available_journalid(array('type'=>$row->VoucherType, 'available' => true));
    $i++;
    if (!($i % 2)) { $sec_color = "BGColorLight"; } else { $sec_color = "BGColorDark"; };
    ?>
    <tr class="<? print "$sec_color"; ?>">
      <td><a href="<? print $_lib['sess']->dispatch ?>t=weeklysale.template&WeeklySaleConfID=<? print $row->WeeklySaleConfID ?>"><? print $row->DepartmentID; ?></a></td>
      <td><a href="<? print $_lib['sess']->dispatch ?>t=weeklysale.template&WeeklySaleConfID=<? print $row->WeeklySaleConfID ?>"><? print $row->Name; ?></a></td>
      <td style="text-align: right;"><a href="<? print $_lib['sess']->dispatch ?>t=weeklysale.template&WeeklySaleConfID=<? print $row->WeeklySaleConfID ?>"><? print $row->VoucherType; ?></a></td>

      <form action="<? print $_lib['sess']->dispatch ?>t=weeklysale.edit&WeeklySaleConfID=<? print $row->WeeklySaleConfID ?>&action_weeklysale_new=1" method="post">
      <td>
        <input type="text" name="init_bilagsnummer" size="4" value="<?= $nextJournalID ?>" id="init_bilagsnummer_<? print $row->WeeklySaleConfID ?>_<? print $row->VoucherType; ?>" class="bilagsnummer">
      </td>
      <td>
        <? 
          $checked = ""; 
          if(isset($_COOKIE[$_SESSION['DB_NAME'] . '_weeklysale_checkbox_' . $row->WeeklySaleConfID]) 
              && $_COOKIE[$_SESSION['DB_NAME'] . '_weeklysale_checkbox_' . $row->WeeklySaleConfID] )
            $checked = "checked"; 
        ?>
        <input type="checkbox" id="checkbox_<?= $row->WeeklySaleConfID ?>" onclick="swap_checkbox('<?= $row->WeeklySaleConfID ?>')" <?= $checked ?>/>
      </td>
      <td>
       <?
          $week_checked = false;
          if(isset($_COOKIE[$_SESSION['DB_NAME'] . '_weeklysale_init_week_' . $row->WeeklySaleConfID])) {
             $week_checked = $_COOKIE[$_SESSION['DB_NAME'] . '_weeklysale_init_week_' . $row->WeeklySaleConfID];
          }
        ?>
        <select name="init_week" id="init_week_<?php echo $row->WeeklySaleConfID ?>">
        <?php
	  for($i = 0; $i <= 53; $i++) {
            if(strlen("$i") < 2)
              $wk = "0$i";
            else
              $wk = $i;

            if( ($week_checked !== false && $wk == $week_checked)
                || ($week_checked === false && $wk == date("W")) )
              printf("<option value='%d' selected>%s</option>", $i, $wk);
            else
              printf("<option value='%d'>%s</option>", $i, $wk);
          }
        ?>
        </select>
      </td>
      <td>
        <input type="text" id="init_date_<?php echo $row->WeeklySaleConfID ?>" name="init_date" size="10" class='bilagsnummer' value="<?php 
if (empty($_COOKIE[$_SESSION['DB_NAME'] . '_weeklysale_init_date_' . $row->WeeklySaleConfID])) {
echo date("Y-m-d", strtotime("sunday"));
} else {
echo $_COOKIE[$_SESSION['DB_NAME'] . '_weeklysale_init_date_' . $row->WeeklySaleConfID];
}
?>">
      </td>
      <td>
	<?php
	echo $_lib['form3']->AccountPeriod_menu3(array('table' => 'voucher', 'field' => 'period', 'value' => $_COOKIE[$_SESSION['DB_NAME'] . '_weeklysale_period_' . $row->WeeklySaleConfID], 'access' => $_lib['sess']->get_person('AccessLevel'), 'accesskey' => 'P', 'required'=> true, 'tabindex' => '', 'name' => 'init_periode', 'id' => 'init_periode_' . $row->WeeklySaleConfID, 'class' => 'bilagsnummer'));
	?>
        <input value="Lagre periode" type="button" onclick="set_period_cookies('<?php echo $row->WeeklySaleConfID ?>')">
      </td>
      <td>
        <input id='new_weeklysale_<? print $row->WeeklySaleConfID ?>' type="submit" value="Ny ukeomsetning">
        <?php
	/*
	  <a href="<? print $_lib['sess']->dispatch ?>t=weeklysale.edit&WeeklySaleConfID=<? print $row->WeeklySaleConfID ?>&action_weeklysale_new=1" class="action">Ny ukeomsetning for avdeling <? print $row->DepartmentID; ?></a>
	*/
	?>
        </form>
      </td>

      <td>
	<? print $row->StartDate ?>
      </td>

      <td>
	<? print $row->EndDate ?>
      </td>

      <td>
      <? if($_lib['sess']->get_person('AccessLevel') >= 4) { ?>
        <a href="<? print $_lib['sess']->dispatch ?>t=weeklysale.list&amp;WeeklySaleConfID=<? print $row->WeeklySaleConfID ?>&amp;action_weeklysaleconf_delete=1" class="button">Slett</a>
      <? } ?>
      </td>
      <td>
        <span style='color:red;' id='error_<? print $row->WeeklySaleConfID ?>'></span>

<?
}
?>

		</td>
	</tr>
</tbody>
<? if($_lib['sess']->get_person('AccessLevel') > 2) { ?>
  <tr>
    <td>
    <td align="right" colspan="3">
      <form name="project_search" action="<? print $_lib['sess']->dispatch ?>t=weeklysale.template" method="post">
      <input type="submit" name="action_weeklysaleconf_new" value="Ny avdelings konfigurasjon (N)" accesskey="N" />
      </form>
  </tr>
<? } ?>
</table>

<? } /* accesslevel 2 */ ?>


<br />
<table class="lodo_data">
<thead>
  <tr>
    <th>Bilagsnr</th>
    <th>Bilagsdato</th>
    <th>Periode</th>
    <th>Navn</th>
    <th>Uke</th>
    <th>Avdeling</th>
    <th>Kontant</th>
    <th>Salg</th>
    <th></th>
  </tr>
</thead>
</thead>

<tbody>
<?
$journalIDs = array();
while($row = $_lib['db']->db_fetch_object($result_week))
{
    $journalIDs[] = $row->VoucherType . $row->JournalID;
    $query = "select Period from weeklysale where WeeklySaleID='$row->WeeklySaleID'";
    $week = $_lib['storage']->get_row(array('query' => $query));

    $i++;
    if (!($i % 2)) { $sec_color = "BGColorLight"; } else { $sec_color = "BGColorDark"; };
    ?>
    <tr class="<? print "$sec_color"; ?>">
      <td><a href="<? print $_lib['sess']->dispatch ?>t=weeklysale.edit&WeeklySaleID=<? print $row->WeeklySaleID ?>"><? print $row->VoucherType ?><? print $row->JournalID ?></a>
      <td><a href="<? print $_lib['sess']->dispatch ?>t=weeklysale.edit&WeeklySaleID=<? print $row->WeeklySaleID ?>"><? $hash = $_lib['format']->Date(array('value'=>$row->JournalDate)); print $hash['value']; ?></a>
      <td><a href="<? print $_lib['sess']->dispatch ?>t=weeklysale.edit&WeeklySaleID=<? print $row->WeeklySaleID ?>"><? print $row->Period ?></a>
      <td><a href="<? print $_lib['sess']->dispatch ?>t=weeklysale.edit&WeeklySaleID=<? print $row->WeeklySaleID ?>"><? print $row->Name ?></a>
      <td><a href="<? print $_lib['sess']->dispatch ?>t=weeklysale.edit&WeeklySaleID=<? print $row->WeeklySaleID ?>"><? print $row->Week ?></a>
      <td><a href="<? print $_lib['sess']->dispatch ?>t=weeklysale.edit&WeeklySaleID=<? print $row->WeeklySaleID ?>"><nobr><? $query2="select DepartmentName from companydepartment where CompanyDepartmentID='$row->DepartmentID'"; $row2=$_lib['storage']->get_row(array('query' => $query2)); print $row->DepartmentID." ".$row2->DepartmentName; ?></nobr></a>
      <td><? $hash = $_lib['format']->Amount(array('value'=>$row->TotalCash)); print $hash['value']; ?>
      <td><? $hash = $_lib['format']->Amount(array('value'=>$row->TotalAmount)); print $hash['value']; ?>
      <td>
      <? if( ($_lib['sess']->get_person('AccessLevel') >= 1 && !(int)$row->TotalCash) ||
              $_lib['sess']->get_person('AccessLevel') >= 4) {
        if($accounting->is_valid_accountperiod($week->Period, $_lib['sess']->get_person('AccessLevel')))
        {
            ?><a href="<? print $_lib['sess']->dispatch ?>t=weeklysale.list&amp;WeeklySaleID=<? print $row->WeeklySaleID ?>&amp;action_weeklysale_delete=1" class="button">Slett</a><?
        }
      }
}
?>
</tbody>
</table>

<script>
$(document).ready(function(){
  var journalIDs = <?= json_encode($journalIDs); ?>;

  $.each($('.bilagsnummer'), function() {
    $(this).keyup(function(){
      var id_type = $(this).attr('id').split('_');
      var id = id_type[2];
      var type = id_type[3];

      var el = $('#new_weeklysale_' + id);
      var val = $(this).val();
      var err = $('#error_' + id);
 
      if(id_type.length >= 4) {
        if(parseInt(val) != val) {
          el.attr("disabled", "disabled");
          err.html('Bilagsnummeret er ikke et tall');
        }
        else if(val.length > 10) { 
          el.attr("disabled", "disabled");
          err.html('Bilagsnummeret er for h&oslash;yt');
        }
        else if(val == '' || val == 0) {
          el.attr("disabled", "disabled");
          err.html('Bilagsnummeret er for lavt');
        }
        else if($.inArray(type + val, journalIDs) != -1) {
          el.attr("disabled", "disabled");
          err.html('Bilagsnummeret eksisterer');
        }
        else {
          el.removeAttr("disabled");
          err.html('');
        }
      }
      else {
        var selected_period = $('#init_periode_' + id).val().split('-');
        var selected_date = $('#init_date_' + id).val().split('-');
  
        if(selected_period[0] != selected_date[0] 
           || selected_period[1] != selected_date[1]) {
          err.html('Periode og dato stemmer ikke');
        }
        else {
          err.html('');
        }
      }

    });  

    $(this).change(function(){
       $(this).trigger('keyup');
    });
    
    $(this).trigger('keyup');    
  });

});
</script>

</body>
</html>


