<?
//require_once $_SETUP['HOME_DIR']."/code/lodo/lib/naeringsoppgave2.class";
require_once $_SETUP['HOME_DIR']."/code/lodo/lib/selvangivelsenaeringsdrivende.class";

//$NaeringsOppgave2 = new NaeringsOppgave2(array('fromPeriod'=>'2004-01', 'toPeriod'=>'2004-12', 'enableLastYear'=>'1', 'report'=>'5'));

$SelvangivelseNaeringsdrivende = new SelvangivelseNaeringsdrivende(array('fromPeriod'=>'2004-01', 'toPeriod'=>'2004-12', 'enableLastYear'=>'1', 'report'=>'1'));

//print_r($NaeringsOppgave2->getOridArray());
print_r($SelvangivelseNaeringsdrivende->getOridArray());

//print_r($GeneralReport->GetReport());
//print_r($GeneralReport->_sumHash);
//print_r($GeneralReport->_altinnMapping->ORID);
//print_r($GeneralReport->_altinnMapping->Account['FromPeriod']);
?>