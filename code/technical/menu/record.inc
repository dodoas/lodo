<?
# Based on EasyComposer technology
# Copyright Thomas Ekdahl, 1994-2003, thomas@ekdahl.no, http://www.ekdahl.no

if($record_new)
{
    $query = "INSERT INTO confmenues SET MenuName='$MenuName', Language='$Language1'";
    $result = $_lib['db']->db_query($query);

}
elseif($record2_new)
{
    $query = "INSERT INTO confmenues SET MenuName='$MenuName', Language='$Language1'";
        #print "$query";
    $result = $_lib['db']->db_query($query);

}
elseif ($record_delete)
{
    $query = "update confmenues set Active='0' WHERE MenuID='$MenuID'";
    $result = $_lib['db']->db_query($query);

}


/* OBS må alltid returnere $chapter_parent for å kunne liste det som skal vises */
?>












