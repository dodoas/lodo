<?
# $Id: list.php,v 1.4 2005/10/28 17:59:40 thomasek Exp $ product_list.php,v 1.2 2001/11/20 17:55:12 thomasek Exp $
# Based on EasyComposer technology
# Copyright Thomas Ekdahl, 1994-2005, thomas@ekdahl.no, http://www.ekdahl.no

$db_table = "linetextmap";

require_once "record.inc";

$query = "select * from $db_table order by Line asc";
$result_map = $_lib['db']->db_query($query);
$db_total = $_lib['db']->db_numrows($result_map);
?>
<html xmlns="http://www.w3.org/1999/xhtml">
<? print $_lib['sess']->doctype ?>
<head>
    <title>Empatix - product list</title>
    <meta name="cvs"                content="$Id: list.php,v 1.4 2005/10/28 17:59:40 thomasek Exp $">
    <? includeinc('head'); ?>
</head>

<body>
<?
    includeinc('top');
    includeinc('left');
?>
    <table class="lodo_data">
        <thead>
                    <?
                    if($_lib['sess']->get_person('AccessLevel') >= 3)
                    {
                    ?>
            <tr>
                <th colspan="4">Tilgjengelige rapporter
            <tr>
                <th class="sub">Rapport nr</th>
                <th class="sub">Rapport navn</th>
            </tr>
        </thead>
        <tbody>
<?php
$i = 1;
?>
                    <tr>
                        <td align="center"><a href="<? print $_lib['sess']->dispatch ?>t=linetextmap.edit&RapportID=<?php print $i; ?>"><?php print $i; ?></a></td>
                        <td><a href="<? print $_lib['sess']->dispatch ?>t=linetextmap.edit&RapportID=<?php print $i; ?>">Offisielt regnskap</a></td>
                    </tr>
<?php
$i++;
?>
                    <tr>
                        <td align="center"><a href="<? print $_lib['sess']->dispatch ?>t=linetextmap.edit&RapportID=<?php print $i; ?>"><?php print $i; ?></a></td>
                        <td><a href="<? print $_lib['sess']->dispatch ?>t=linetextmap.edit&RapportID=<?php print $i; ?>">Selvangivelse for n&aelig;ringsdrivende</a></td>
                    </tr>
<?php
$i++;
?>
                    <tr>
                        <td align="center"><a href="<? print $_lib['sess']->dispatch ?>t=linetextmap.edit&RapportID=<?php print $i; ?>"><?php print $i; ?></a></td>
                        <td><a href="<? print $_lib['sess']->dispatch ?>t=linetextmap.edit&RapportID=<?php print $i; ?>">N&aelig;ringsoppgave 1</a></td>
                    </tr>
<?php
$i++;
?>
                    <tr>
                        <td align="center"><a href="<? print $_lib['sess']->dispatch ?>t=linetextmap.edit&RapportID=<?php print $i; ?>"><?php print $i; ?></a></td>
                        <td><a href="<? print $_lib['sess']->dispatch ?>t=linetextmap.edit&RapportID=<?php print $i; ?>">Selvangivelse for aksjeselskap</a></td>
                    </tr>
<?php
$i++;
?>
                    <tr>
                        <td align="center"><a href="<? print $_lib['sess']->dispatch ?>t=linetextmap.edit&RapportID=<?php print $i; ?>"><?php print $i; ?></a></td>
                        <td><a href="<? print $_lib['sess']->dispatch ?>t=linetextmap.edit&RapportID=<?php print $i; ?>">N&aelig;ringsoppgave 2</a></td>
                    </tr>
<?php
$i++;
?>
                    <tr>
                        <td align="center"><a href="<? print $_lib['sess']->dispatch ?>t=linetextmap.edit&RapportID=<?php print $i; ?>"><?php print $i; ?></a></td>
                        <td><a href="<? print $_lib['sess']->dispatch ?>t=linetextmap.edit&RapportID=<?php print $i; ?>">Rapport <?php print $i; ?></a></td>
                    </tr>
<?php
$i++;
?>
                    <tr>
                        <td align="center"><a href="<? print $_lib['sess']->dispatch ?>t=linetextmap.edit&RapportID=<?php print $i; ?>"><?php print $i; ?></a></td>
                        <td><a href="<? print $_lib['sess']->dispatch ?>t=linetextmap.edit&RapportID=<?php print $i; ?>">Rapport <?php print $i; ?></a></td>
                    </tr>
<?php
$i++;
?>
                    <tr>
                        <td align="center"><a href="<? print $_lib['sess']->dispatch ?>t=linetextmap.edit&RapportID=<?php print $i; ?>"><?php print $i; ?></a></td>
                        <td><a href="<? print $_lib['sess']->dispatch ?>t=linetextmap.edit&RapportID=<?php print $i; ?>">Rapport <?php print $i; ?></a></td>
                    </tr>
<?php
$i++;
?>
                    <tr>
                        <td align="center"><a href="<? print $_lib['sess']->dispatch ?>t=linetextmap.edit&RapportID=<?php print $i; ?>"><?php print $i; ?></a></td>
                        <td><a href="<? print $_lib['sess']->dispatch ?>t=linetextmap.edit&RapportID=<?php print $i; ?>">Rapport <?php print $i; ?></a></td>
                    </tr>
<?php
$i++;
?>
                    <tr>
                        <td align="center"><a href="<? print $_lib['sess']->dispatch ?>t=linetextmap.edit&RapportID=<?php print $i; ?>"><?php print $i; ?></a></td>
                        <td><a href="<? print $_lib['sess']->dispatch ?>t=linetextmap.edit&RapportID=<?php print $i; ?>">Rapport <?php print $i; ?></a></td>
                    </tr>
<?php
$i = 100;
?>
                    <tr>
                        <td align="center"><a href="<? print $_lib['sess']->dispatch ?>t=linetextmap.edit&RapportID=<?php print $i; ?>"><?php print $i; ?></a></td>
                        <td><a href="<? print $_lib['sess']->dispatch ?>t=linetextmap.edit&RapportID=<?php print $i; ?>">Kortfattet Rapport</a></td>
                    </tr>
<?php
$i++;
?>
                    <?
                }
            ?>
        </tbody>
    </table>
</body>
</html>


