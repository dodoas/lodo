<?php

error_reporting(E_ALL);
session_start();

require_once('config.php');
require_once('sql.php');
require_once('inc.php');

if(isset($_GET['step']))
    $step = $_GET['step'];
else
    $step = 0;

if(isset($_GET["sess"]))
{
    $sess = $_GET["sess"];

    if(!isset($_SESSION[$sess]))
    {
	$_SESSION[$sess] = array();
	$_SESSION[$sess]['log']="";
	$step = 0;
    }
}
else
{
    $sess = "s_".time();

    $_SESSION[$sess] = array();
}

$next = step_url($step + 1, $sess);

foreach($_POST as $pk => $pv)
{
    if($pk == 'database')
    {
	$_SESSION[$sess]['database'] = mysql_escape_string($_POST['database']);
    }
    else if($pk == 'table')
    {
	$_SESSION[$sess]['table'] = mysql_escape_string($_POST['table']);
    }
    else if(strstr($pk, "form_type") || strstr($pk, "val_id"))
    {
	$_SESSION[$sess][$pk] = $pv;
    }
    else if($pk == 'out_db')
    {
	$_SESSION[$sess]['out_db'] = $pv;
    }
}

if(isset($_SESSION[$sess]['database']))
{
    mysql_select_db($_SESSION[$sess]['database']) or die(mysql_error());
}

function select_form_type($name)
{
    $options = array('text', 'checkbox', 'show', 'wysiwyg', 'select');
    
    return select_array($name, $options);
}

function select_validation($name, $default)
{
    $options = array('String', 'Int', 'Datetime', 'Amount');

    return select_array($name, $options, $default);
}

switch($step)
{
case 0:
    $page = 
	"<form action='$next' method='post'> ".
	" <select name='database'> ";

    $_SESSION[$sess]['log'] = "";    
    $r = mysql_list_dbs();
    while($row = mysql_fetch_assoc($r))
    {
	$name = $row['Database'];
	$page .= "<option value='$name'>$name</option>";
    }

    $page .="</select><input type='submit'></form>";
    $info = "Select database to fetch table from";

    break;

case 1:
    $page = 
	"<form action='$next' method='post'> ".
	" <select name='table'> ";

    $r = mysql_query(sprintf("SHOW TABLES FROM %s", 
			     $_SESSION[$sess]['database'])) 
	or die(mysql_error());

    while($row = mysql_fetch_row($r))
    {
	$name = $row[0];
	$page .= "<option value='$name'>$name</option>";
    }
    
    $page .= "</select><input type='submit'></form>";

    $info = "Select table to fetch columns from";

    break;

case 2:    
    $page = "<form action='$next' method='post'><table>";    

    $r = mysql_query(sprintf("SHOW COLUMNS FROM %s", 
			     $_SESSION[$sess]['table']))
	or die(mysql_error());

    $page .= 
	"<tr> " .
	" <td>Name</td> <td> Type </td> <td> FormType </td> <td> Validation </td> " .
	"</tr> ";

    while($row = mysql_fetch_assoc($r))
    {
	$field = $row['Field'];
	$type = $row['Type'];

	$_SESSION[sprintf("%s_field_%s", $_SESSION[$sess]['table'], $field)] = $type;

	if(strstr($type, "int"))
	    $val_def = "Int";
	else if(strstr($type, "time") || strstr($type, "date"))
	    $val_def = "Datetime";
	else if(strstr($type, "decimal") || strstr($type, "double") ||
		strstr($type, "float"))
	    $val_def = "Amount";
	else
	    $val_def = "String";

	$page .= 
	    "<tr> " .
	    " <td> $field </td> ".
	    " <td> $type </td> ". 
	    " <td> ".select_form_type("form_type_".$field)." </td> " .
	    " <td> ".select_validation("val_id_".$field, $val_def)." </td> " .
	    "</tr> ";
    }

    $page .= "</table><input type='submit'></form>";

    $info = "Manually check and update FormType and Validation fields";

    break;

case 3:
    $r = mysql_list_dbs();

    $len = mysql_num_rows($r);
    $page = 
	"<form action='$next&amp;n=0' method='post'>".
	" <select multiple='multiple' size='$len' style='width: 400px;' name='out_db[]'>";

    while($row = mysql_fetch_assoc($r))
    {
	$name = $row['Database'];

	if($name != $_SESSION[$sess]['database'] && $name != 'information_schema')
	    $page .= "<option value='$name' selected>$name</option>";
    }

    $page .= 
	" </select>".
	" <input type='submit' style='display: block'>".
	"</form>";

    $info = "Select databases to copy to. After submitting this field there is no way back.";
    break;

case 4:
    $page = "<h2>Do not close this window!</h2>";
    
/*    $link2 = mysql_connect($config['DB_HOST'], $config['DB_USER'],
			   $config['DB_PASS']);

			   $db2 = mysql_select_db();*/
    if(isset($_GET['n']))
	$n = $_GET['n'];
    else
	$n = 0;

    if(count($_SESSION[$sess]['out_db']) <= $n)
    {
	$page = "Completed ";

	if($_SESSION[$sess]['log'] != "")
	{
	    $page .= "with errors: <br/><div id='log'>";
	    $page .= $_SESSION[$sess]['log'];
	    $page .= "</div>";
	}
    }
    else
    {
	$database = $_SESSION[$sess]['out_db'][$n];
	$table = $_SESSION[$sess]['table'];

	$percent = (($n+1)/count( $_SESSION[$sess]['out_db']))*100;
	$page .= sprintf('<div id="progressbar"><div class="counter" style="width: %f%%"></div></div><br /><br />', $percent);
	
	//$page .= $table;

	$sql = "CREATE table $database.$table LIKE ".$_SESSION[$sess]['database'].".$table;";
	if(!mysql_query($sql))
	{
	    $page .= "Database `$database' already have table `$table'. This message has been logged<br/>";
	    $_SESSION[$sess]['log'] .= "Database `$database' already have table `$table'.<br/>";
	}
	else
	{
	    $page .= "Table created<br>";

	    mysql_select_db($database);

	    foreach($_SESSION as $k => $v)
	    {
		if(substr($k, 0, strlen($table) + 1 + strlen("table")) == sprintf("%s_field", $table))
		{
		    $field = substr($k, strlen($table) + 2 + strlen("table"));
		    
		    $sql = insert_confdbfields($sess, $field, $_SESSION[$sess]['form_type_'.$field], $v,
					       mysql_escape_string($_SESSION[$sess]['val_id_'.$field]), $database);
		    if(!mysql_query($sql))
		    {
			$_SESSION[$sess]['log'] .= "Table confdbfields doesn't exist in database `$database` (".mysql_error().")<br/>";
			break;
		    }

		    $page .= "Field `$field' added to confdb<br />";
		}
	    }
	}

	$page .= '<meta http-equiv="refresh" content="1;url='.step_url($step, $sess).'&n='.($n+1).'" />';
    }


    $info = "";

    break;
}

?>

<html>
<head>
<style>
body
{
    font-family: helvetica;
    background-color: #eee;
}

h1
{
    text-align: center;
    margin: 10;
}

a
{
    color: black;
}

#box
{
}

#main
{
    border: 1px solid black;
    padding: 10px;
    background-color: lightyellow;
    margin-left: auto;
    margin-right: auto;
}

#steps
{
    padding-top: 20px;
    padding-bottom: 20px;
    border-bottom: 1px solid black;
    border-top: 1px solid black;
    margin-bottom: 10px;
}

.step
{
    display: inline;
    text-align: left;
    font-weight: bold;
    margin-right: 20px;
}

select, input
{
    margin-right: 5px;
}

.step:before
{
    content: "\00BB \0020";
}

table
{
    width: 100%;
}

tr
{
    border-bottom: 1px solid black;
}

tr:hover
{
    background-color: darkred;
    color: white;
}

#progressbar
{
width: 300px;
height: 40px;
    background-color: lightgray;
    margin-left: auto;
    margin-right: auto;

}

#progressbar .counter
{
    background-color: blue;
color: white;
    text-align: center;
    vertical-align: middle;
height: 40px;
}

#info
{
    border-top: 1px solid black;
    font-family: arial;
    font-size: 10px;
    margin-top: 10px;
    padding-top: 15px;
    padding-left: 25px;
    height: 20px;
    background: url('info.gif') no-repeat center left;	
}

#log
{
    font-size: 12px;
    font-family: arial;
}

#info:before
{
    background-color: 
}

</style>
<title>LODO Table configure - step : <?= $step + 1 ?></title>
</head>
<body>

<div id="main">
    <h1>LODO Table configure</h1>
    <?= print_steps($step, $sess) ?>
    <?= $page ?>

    <?php if($info != "") { ?>
      <div id="info">
        <?= $info ?>
      </div>
    <?php } ?>
</div>
    <br />
    <? 
    //print_r($_SESSION); 
    ?>
</body>
</html>