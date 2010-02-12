<?
# $Id: record.inc,v 1.3 2005/11/18 07:35:46 thomasek Exp $ product_record.inc,v 1.2 2001/11/11 12:35:06 thomasek Exp $
# Based on EasyComposer technology
# Copyright Thomas Ekdahl, 1994-2003, thomas@ekdahl.no, http://www.ekdahl.no
includemodel("arbeidsgiveravgift/savetable");

if($_REQUEST['action_linetextmap_update']) {
	$textLines = split("\n", $_REQUEST["tekst"]);
	for ($i = 0; $i < count($textLines); $i++)
	{
		global $visKontonr;
		if ($visKontonr)
			list($myID, $myKontonr, $Linjenr, $Linjetext) = split("::", $textLines[$i]);
		else
			list($myID, $Linjenr, $Linjetext) = split("::", $textLines[$i]);
		$RAPPORTLINJER[$Linjenr]["text"] = $Linjetext;
		$RAPPORTLINJER[$Linjenr]["id"] = $myID;
	}

    if ($_REQUEST["linetextmap_ReportID"] == 100) {
        $myLine         = "ReportShort";
        $myEnableLine   = "EnableReportShort";
    }
    else {
        $myLine         = "Report" . $_REQUEST["linetextmap_ReportID"] . "Line";
        $myEnableLine   = "EnableReport" . $_REQUEST["linetextmap_ReportID"];
    }

	$query_list = "select distinct " . $myLine . " from accountplan where " . $myEnableLine . " = 1 order by AccountPlanID"; //  Active='1'
	#print "$query_list<br>\n";
	$result_list    = $_lib['db']->db_query($query_list);
	$db_total       = $_lib['db']->db_numrows($result_list);

	while ($row = $_lib['db']->db_fetch_object($result_list)) {

		if ($row->$myLine != "") {
			$query_map = "select LineTextMapID, Line, Text from $db_table where ReportID='" . $_REQUEST["linetextmap_ReportID"] . "' and Line = '" . $row->$myLine . "'";
			$row_map = $_lib['storage']->get_row(array('query' => $query_map));
			$myTab = new SaveTable($db_table, $row_map->LineTextMapID);
			$myFieldList = $myTab->getFields();

			foreach ($myFieldList as $field) {
				$fieldname = $field["name"];
				if ($row_map->LineTextMapID == "")
				{
					$myTab->set("InsertedByPersonID", $_lib['sess']->login_id);
					
					$myTab->set("InsertedDateTime", date("Y-m-d H:i:s", time()));
				}
				$myTab->set("Line", $row->$myLine);
				$myTab->set("LanguageID", "no");
				$myTab->set("UpdatedByPersonID", $_lib['sess']->login_id);
				$myTab->set("ReportID", $_REQUEST["linetextmap_ReportID"]);
				$myTab->set("Text", $RAPPORTLINJER[$row->$myLine]["text"]);

			}
			$id = $myTab->save();
		}
	}
}