<?
/* $Id: edit.php,v 1.31 2005/10/28 17:59:40 thomasek Exp $ main.php,v 1.12 2001/11/20 17:55:12 thomasek Exp $ */
$ProjectID = (int) $_REQUEST['project_ProjectID'];

$db_table = "project";
require_once "record.inc";

#Input parameters should be validated - also against roles
$query   = "select * from $db_table where ProjectID = '" . $ProjectID . "'";
$project = $_lib['storage']->get_row(array('query' => $query));
?>

<? print $_lib['sess']->doctype ?>
<head>
    <title>Empatix - prosjekter</title>
    <meta name="cvs"                content="$Id: edit.php,v 1.31 2005/10/28 17:59:40 thomasek Exp $" />
    <? includeinc('head') ?>
</head>
<body>

<? includeinc('top') ?>
<? includeinc('left') ?>

<? print $_lib['message']->get() ?>

<form name="<? print "$form_name"; ?>" action="<? print $MY_SELF ?>" method="post">
<input type="hidden" name="project_ProjectID" value="<? print "$project->ProjectID"; ?>">
<table class="lodo_data">
    <tr class="result">
        <th colspan="4">Prosjekter
    <tr>
        <td class="menu">Prosjekt
        <td><? print $project->ProjectID  ?>
    <tr>
        <td class="menu">Prosjekt navn
        <td><input type="text" name="project.Heading" value="<? print $project->Heading  ?>" size="60">
    <tr>
        <td class="menu">Aktiv
        <td colspan="3"><? print $_lib['form3']->checkbox(array('table'=>$db_table, 'field'=>'Active', 'value'=>$project->Active)) ?>
    <tr>
        <td class="menu">Adresse
        <td><input type="text" name="project.Address" value="<? print $project->Address  ?>" size="60">
    <tr>
        <td class="menu">Postnummer
        <td><input type="text" name="project.ZipCode" value="<? print $project->ZipCode  ?>" size="60">
    
    <? // Lagt til 6/1-2005 ?>
    <tr>
        <td class="menu">Poststed</td>
        <td><input type="text" name="project.City" value="<? print $project->City ?>" size="60"></td>
    </tr>
    <!-- <tr>
        <td class="menu">Avsluttes pr 31/12
        <td colspan="3"><? $_lib['form2']->checkbox2($db_table, "EnableZeroYearEnd", $project->EnableZeroYearEnd,''); ?><br> -->
    <tr>
        <td class="menu">Annen informasjon
        <td colspan="3"><input type="text" name="project.Description" value="<? print $project->Description  ?>" size="60">
    
    <tr>
        <td colspan="4" align="right"><input type="submit" name="action_project_update" value="Lagre prosjekt (S)" accesskey="S" />
</form>
<form name="delete" action="<? print $_lib['sess']->dispatch ?>t=project.list" method="post">
  <tr>
    <? print $_lib['form3']->hidden(array('name'=>'ProjectID', 'value'=>$ProjectID)) ?>
    <td colspan="4" align="right"><input type="submit" name="action_project_delete" value="Slett prosjekt" onclick='return confirm("Er du sikker?")' />
</form>
</table>
<? includeinc('bottom') ?>
</body>
</html>
