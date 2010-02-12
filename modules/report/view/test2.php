<?
require_once $_SETUP['HOME_DIR']."/code/lodo/lib/naeringsoppgave2.class";

$NaeringsOppgave2 = new NaeringsOppgave2(array('fromPeriod'=>'2004-01', 'toPeriod'=>'2004-12'));
?>

<br><br><br>

<?
print_r($NaeringsOppgave2->getOridArray());
?>