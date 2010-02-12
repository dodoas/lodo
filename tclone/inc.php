<?php

function step_url($step, $sess)
{
    return "index.php?step=".$step."&amp;sess=".$sess;
}

function print_steps($step, $sess)
{
    $steps = array(
	0 => 'Select DB',
	1 => 'Select Table',
	2 => 'Manual config',
	3 => 'Output DB'
	);


    printf("<div id='steps'>");
    foreach($steps as $k => $v)
    {
	if($k > $step)
	    break;

	printf("<div class='step'><a href='%s'>%s</a></div>", step_url($k, $sess), $v);
    }
    printf("</div>");
}

function select_array($name, $options, $default = '')
{
    $ret = "<select name='$name'>";
    
    foreach($options as $opt)
    {
	if($opt == $default)
	    $ret .= "<option selected value='$opt'>$opt</option>";	   
	else
	    $ret .= "<option value='$opt'>$opt</option>";
    }
    
    $ret .= "</select>";

    return $ret;
}

function insert_confdbfields($sess, $fieldname, $formtype, $fieldtype, 
			     $validation)
{
    global $_SESSION;

    $table = $_SESSION[$sess]['table'];

    $sql = "
INSERT INTO `confdbfields` (
  `TableField`, `TableName`, `Active`, `FormType`, `FieldType`, 
  `InputValidation`, `OutputValidation`
) 
VALUES(
  '$fieldname', '$table', 1, '$formtype', '$fieldtype', '$validation',
  '$validation'
);";
    return $sql;
}