<? print $_lib['sess']->doctype ?>
<head>
    <title>Empatix - accountplan list</title>
    <meta name="cvs"                content="$Id: list.php,v 1.57 2005/10/28 17:59:40 thomasek Exp $" />
    <? includeinc('head') ?>
</head>

<body>
<?
includeinc('top');
includeinc('left');
?>

<h1>Hovedmaler for kontoer</h1>

<table class="group">
<?

$q = "SELECT AccountPlanID, AccountPlanType FROM accountplantemplate";
$r = $_lib['db']->db_query($q);

while( ($row = $_lib['db']->db_fetch_assoc($r)) ) {

$AccountPlanID = $row['AccountPlanID'];
$AccountPlanType = $row['AccountPlanType'];

switch($AccountPlanType) {
    case 'customer':
      $n = 'Kunde';
      $t = 'reskontro'; break;
    case 'supplier':
      $n = 'Leverand&oslash;r';
      $t = 'reskontro'; break;
    case 'employee':
      $n = 'Ansatt';
      $t = 'employee'; break;
    case 'result':
      $n = 'Resultat';
      $t = 'hovedbok'; break;
    case 'balance':
      $n = 'Balanse';
      $t = 'hovedbok'; break;
}

printf('
<tr>
  <td>
    <a href="%st=accountplantemplate.%s&accountplantemplate.AccountPlanID=%d&accountplan_type=%s">%s</a>
  </td>
</tr>
', 
$_lib['sess']->dispatch,
$t, $AccountPlanID, $AccountPlanType, $n
);

}
?>
</table>

</body>
</html>
