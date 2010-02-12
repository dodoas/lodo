<?
# $Id: edit.php,v 1.22 2005/10/14 13:15:40 thomasek Exp $ person_list.php,v 1.3 2001/11/20 18:04:43 thomasek Exp $
# Based on EasyComposer technology
# Copyright Thomas Ekdahl, 1994-2005, thomas@ekdahl.no, http://www.ekdahl.no

$db_table = "arbeidsgiveravgift";
$Code = $_REQUEST['Code'];
require_once "record.inc";

/* S¿kestreng */
$query_vat  = "select * from $db_table limit 200";
$result_vat = $_lib['db']->db_query($query_vat);

?>

<? print $_lib['sess']->doctype ?>
<head>
    <title>Empatix - vat</title>
    <meta name="cvs"                content="$Id: edit.php,v 1.22 2005/10/14 13:15:40 thomasek Exp $" />
    <? includeinc('head') ?>
</head>
<body>

<? includeinc('top') ?>
<? includeinc('left') ?>

<table>
<? if($_lib['sess']->get_person('AccessLevel') >= 2) { ?>
 <form name="new" action="<? print $MY_SELF ?>" method="post">
    <? print $_lib['form3']->Input(array('type'=>'text', 'name'=>'Code')) ?>
   <input type="submit" name="action_arbeidsgiveravgift_new" value="Ny arbeidsgiveravgift" />
 </form>
<? } ?>

  <tr class="result">
    <th>Kode</th>
    <th>Prosent%</th>
    <th></th>
    <th></th>
  </tr>
    <?
    $counter=0;
    while($row = $_lib['db']->db_fetch_object($result_vat))
    {
        $counter++;
        $i++;
        if (!($i % 2)) { $sec_color = "BGColorLight"; } else { $sec_color = "BGColorDark"; };
        ?>
          <tr>
              <form name="<? print 'line'.$counter ?>" action="<? print $MY_SELF ?>" method="post">
                <input type="hidden" name="Code" value="<? print $row->Code ?>" />
                <td class="menu"><? print $row->Code ?></td>
                <td><input type="text" name="arbeidsgiveravgift.Percent"   value="<? print $_lib['format']->Amount(array('value'=> $row->Percent, 'return'=>'value')) ?>" size="5" class="number" />%</td>
                <td>
                <? if($_lib['sess']->get_person('AccessLevel') >= 2) { ?>
                  <input type="submit" name="action_arbeidsgiveravgift_update" value="Lagre" accesskey="S" />
                <? } ?>
                </td>
                <td>
                <? if($_lib['sess']->get_person('AccessLevel') >= 2) { ?>
                  <input type="submit" name="action_arbeidsgiveravgift_delete" value="Slett" accesskey="D" />
                <? } ?>
                </td>
              </form>
          </tr>
        <?
    }
    ?>

</table>

<? includeinc('bottom') ?>

</body>
</html>
