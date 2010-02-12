<?
$sql_period = "select Period from $db_table order by Period desc limit 1";
$row = $_lib['storage']->get_row(array('query' => $sql_period));
$nextperiod = $_lib['date']->get_next_period(array('value'=>$row->Period));

$sql_period = "select Period from $db_table order by Period asc limit 1";
$row = $_lib['storage']->get_row(array('query' => $sql_period));
$prevperiod = $_lib['date']->get_prev_period(array('value'=>$row->Period));

if(!isset($prevperiod))
    $prevperiod=$_lib['date']->get_this_period($_lib['sess']->get_session('LoginFormDate'));

if(!isset($nextperiod))
    $nextperiod=$_lib['date']->get_next_period(array('value'=>$prevperiod));

if($_REQUEST['action_accountperiod_update'])
{
  #Possible to extend or alter parameters here
  $primarykey = "AccountPeriodID";

  #We have to loop through and look at each element for access control
  foreach ($_POST as $key => $value)
  {
     if(preg_match("/^$db_table\_/", $key))
     {
         preg_match('{.*_(.*)$}', $key, $m); #Find the pk value  (text or int)
         $pk_new = $m[1];
         $key = preg_replace("/\_$pk_new/i",  "", $key); #Remove primary key
         $newinput = array();
         $newinput[$key] = $value;

         $pk[$primarykey] = $pk_new;

         #need to check what field
         if(preg_match("/status/i", $key))
         {
            #Check the old saved value, not to be overridden in sertain states
            $sql_check_period = "select Status from $db_table where $primarykey=$pk_new";
            $row = $_lib['storage']->get_row(array('query' => $sql_check_period));

            if($value > $row->Status)
            {
                if($key)
                {
                  $_lib['storage']->db_update_hash($newinput, $db_table, $pk);
                }
                $pk_old = $pk_new;
            }
         }

         elseif(preg_match("/payed/i", $key))
         {
            $_lib['storage']->db_update_hash($newinput, $db_table, $pk);
         }

         unset($newinput);
      }
   }
}
elseif($_REQUEST['action_accountperiod_newBefore'])
{
    $_post['accountperiod_Status'] = 1;
    $_post['accountperiod_Payed'] = 0;
    $_post['accountperiod_Period'] = $prevperiod;
    $AccountPeriodID = $_lib['storage']->db_new_hash($_post, $db_table);
    $prevperiod = $_lib['date']->get_prev_period(array('value'=>$prevperiod));
}
elseif($_REQUEST['action_accountperiod_newAfter'])
{
    $_post['accountperiod_Status'] = 1;
    $_post['accountperiod_Payed'] = 0;
    $_post['accountperiod_Period'] = $nextperiod;
    $AccountPeriodID = $_lib['storage']->db_new_hash($_post, $db_table);
    $nextperiod = $_lib['date']->get_next_period(array('value'=>$nextperiod));
}
?>
