<?
################
#
#  HUKS AT DENNE LOGIKK MÅ SENTRALISERES, NÅ ER DET TILSVARENDE LOGIKK I ZReport IMPORTEN
#
################

includelogic('weeklysale/weeklysale');

$znrtotaldiff   = array();
$znrwrong       = array();



#Start accounting
$primarykey['WeeklySaleID'] = $WeeklySaleID;


if($_REQUEST['action_weeklysale_update']) {
	#print_r($_POST);
	update_weeklysale($_sess, $_POST, $WeeklySaleID);
}
elseif($_REQUEST['action_weeklysale_new'])
{
	$query_sale_conf  = "select * from weeklysaleconf where WeeklySaleConfID = '$WeeklySaleConfID'";
	$result_sale_conf = $_lib['db']->db_query($query_sale_conf);
	$sale_conf        = $_lib['db']->db_fetch_object($result_sale_conf);
  
	list($JournalID, $tmp) = $accounting->get_next_available_journalid(array('available' => true, 'update' => true, 'type' => $sale_conf->VoucherType));
  
	$postmain['weeklysale_Name']              = $sale_conf->Name;
	$postmain['weeklysale_PermanentCash']     = $sale_conf->PermanentCash;
	$postmain['weeklysale_DepartmentID']      = $sale_conf->DepartmentID;
	$postmain['weeklysale_Period']            = $_lib['date']->get_this_period($_lib['sess']->get_session('LoginFormDate'));
	$postmain['weeklysale_InsertedDateTime']  = $_lib['sess']->get_session('Date');
	$postmain['weeklysale_InsertedByPersonID']= $_lib['sess']->get_person('PersonID');
	$postmain['weeklysale_UpdatedByPersonID'] = $_lib['sess']->get_person('PersonID');
	$postmain['weeklysale_WeeklySaleConfID']  = $WeeklySaleConfID;
	$postmain['weeklysale_JournalDate']       = $_lib['sess']->get_session('LoginFormDate');
	$postmain['weeklysale_JournalID']         = $JournalID;
	$postmain['weeklysale_VoucherType']       = $sale_conf->VoucherType;

	if(isset($_POST['init_bilagsnummer'])) {
		$postmain['weeklysale_JournalID']   = (int)$_POST['init_bilagsnummer'];
		$postmain['weeklysale_Period']      = $_POST['init_periode'];
		$postmain['weeklysale_JournalDate'] = $_POST['init_date'];
	}
  
	#Possible to extend or alter parameters here
	$WeeklySaleID = $_lib['storage']->db_new_hash($postmain, 'weeklysale');
  
	#print "WeeklySaleID: $WeeklySaleID<br>";
	$args['WeeklySaleID']    = $WeeklySaleID;
  
	$weekday = $_lib['date']->get_WeekDayNameHash();
	foreach($weekday as $num => $day) {

		$args['DayID'] = $num;
		new_weeklysaleday($args);
	}  
}
elseif($_REQUEST['action_weeklysale_delete'])
{
    $WeeklySaleID = $_REQUEST['WeeklySaleID'];

    #sletter bilaget til ukeomsettningen hvis det eksisterer
    $query 	= "select JournalID, Period, JournalDate, VoucherType from weeklysale where WeeklySaleID=$WeeklySaleID";
    $row 	= $_lib['storage']->get_row(array('query' => $query));

    $VoucherPeriod = $row->Period;
    $VoucherDate = $row->JournalDate;

    /******************************************************************/
    #Open all closed posts related to invoice. Could be optimized to only close reskontros as pr def
    if($row->JournalID) {
        includelogic('postmotpost/postmotpost');
        $postmotpost = new postmotpost(array());
        $postmotpost->openPostJournal($row->JournalID, $row->VoucherType);

        $accounting->delete_journal($row->JournalID, $row->VoucherType);
    }

    $post = array();
    $post['voucher_VoucherPeriod'] = $VoucherPeriod;
    $post['voucher_VoucherDate']   = $VoucherDate;
    if(strlen($VoucherPeriod)>0)
    {
        //print_r($post); print $VoucherType;
        $accounting->set_journal_motkonto(array('post' => $post, 'VoucherType' => $row->VoucherType));
    }

    #sletter ukeomsettningen
    $primarykey = array();
    $primarykey['WeeklySaleID'] = $WeeklySaleID;
    $_lib['storage']->db_delete_hash('weeklysale', $primarykey);
    $_lib['storage']->db_delete_hash('weeklysaleday', $primarykey);
}
elseif($_REQUEST['action_weeklysaleconf_delete'])
{
	$primarykey = array();
	$primarykey['WeeklySaleConfID'] = $_REQUEST['WeeklySaleConfID'];
	$_lib['storage']->db_delete_hash('weeklysaleconf', $primarykey);
}
elseif($_REQUEST['action_weeklysale_daynew']) {
    
	new_weeklysaleday($_REQUEST);
}
elseif($_REQUEST['action_weeklysaleconf_new']) {
	#Has to require_once department id?

	#Possible to extend or alter parameters here
	$_POST['weeklysaleconf_InsertedByPersonID'] = $login_id;
	$_POST['weeklysaleconf_InserteDateTime']    = $_lib['sess']->get_session('Date');
	$_POST['weeklysaleconf_UpdatedByPersonID']  = $login_id;

	$WeeklySaleConfID = $_lib['storage']->db_new_hash($_POST, 'weeklysaleconf');
  
	$postsub = array(); #to be shure that variable is empty, there might have been problems with this not being empty
  
	$postsub['weeklysalegroupconf_WeeklySaleConfID']  = $WeeklySaleConfID;
  
	$postsub['weeklysalegroupconf_Type']          = 1;  #Inntekt
	$_lib['storage']->db_new_hash($postsub, 'weeklysalegroupconf');
  
	$postsub['weeklysalegroupconf_Type']          = 2;  #Likvidkonto
	$_lib['storage']->db_new_hash($postsub, 'weeklysalegroupconf');

}
elseif($_REQUEST['action_weeklysaleconf_update'])
{
    $message = "";
    for($j=1; $j<=2; $j++)
    {
        for($i=1; $i<=20; $i++)
        {
            if(!($j==2 and ($i==19 or $i==20)))
            {

                if($j == 1) {
                  $groupname = "Inntekt";
                } else {
                  $groupname = "Likvidkonto";
                }
                if($i==18 and !isset($_POST['weeklysalegroupconf_Group'.$i.'Account_'.$_POST[$j]]))
                {
                    #dette er gruppe 18 og det er ikke satt konto
                    $_lib['message']->add(array('message' => "<font color=\"red\">Du må velge konto for kontant i $groupname</font><br>"));
                }
                elseif(strlen($_POST['weeklysalegroupconf_Group'.$i.'Name_'.$_POST[$j]])>0 and strlen($_POST['weeklysalegroupconf_Group'.$i.'Account_'.$_POST[$j]])==0)
                {
                    #det er satt navn på gruppe, men ikke konto
                    $_lib['message']->add(array('message' => "<font color=\"red\">Du må velge konto for gruppe $i i $groupname</font><br>"));
                }
                elseif($i !=18 and $j != 2 and strlen($_POST['weeklysalegroupconf_Group'.$i.'Name_'.$_POST[$j]])==0 and strlen($_POST['weeklysalegroupconf_Group'.$i.'Account_'.$_POST[$j]])>0)
                {
                    #det er satt navn på gruppe, men ikke konto
                    $_lib['message']->add(array('message' => "<font color=\"red\">Du må velge konto navn for gruppe $i i $groupname</font><br>"));
                }
            }
        }
    }
    #Possible to extend or alter parameters here
    $tables['weeklysaleconf']         = 'WeeklySaleConfID';
    $tables['weeklysalegroupconf']    = 'WeeklySaleGroupConfID';

    $_lib['storage']->db_update_multi_table($_POST, $tables);
}
elseif($_REQUEST['action_weeklysale_journal']) {

	#We should check for changes in period or VoucherType to update all subsystems correctly.
    $query_week  		= "select * from weeklysale where WeeklySaleID = '$WeeklySaleID'";
    $result_week 		= $_lib['db']->db_query($query_week);
    $week        		= $_lib['db']->db_fetch_object($result_week);
    $VoucherType 		= $week->VoucherType;
	#print "VoucherType: $VoucherType<br>\n";

    $query_conf  		= "select * from weeklysaleconf where WeeklySaleConfID = '$week->WeeklySaleConfID'";
    $result_conf 		= $_lib['db']->db_query($query_conf);
    $conf        		= $_lib['db']->db_fetch_object($result_conf);

    $query_sale_conf    = "select * from weeklysalegroupconf where WeeklySaleConfID = '$week->WeeklySaleConfID' and Type=1";
    $result_sale_conf   = $_lib['db']->db_query($query_sale_conf);
    $sale_conf          = $_lib['db']->db_fetch_object($result_sale_conf);

    $query_revenue_conf = "select * from weeklysalegroupconf where WeeklySaleConfID = '$week->WeeklySaleConfID' and Type=2";
    $result_revenue_conf= $_lib['db']->db_query($query_revenue_conf);
    $revenue_conf       = $_lib['db']->db_fetch_object($result_revenue_conf);

    if(!strlen($_POST['weeklysale_DepartmentID_'.$_POST['WeeklySaleID']])>0)
        $_POST['weeklysale_DepartmentID_'.$_POST['WeeklySaleID']] = $conf->DepartmentID;

    #Lagre
    #print "<h2>Kaller med: $WeeklySaleID</h2>\n";
    $_lib['message']->add(array('message' => update_weeklysale($_POST, $WeeklySaleID)));

    #Før billag
    $query_week         = "select * from weeklysale where WeeklySaleID = '$WeeklySaleID'";
    $result_week        = $_lib['db']->db_query($query_week);
    $week               = $_lib['db']->db_fetch_object($result_week);

    $sum_sale = array();
    $sum_rev = array();

    for($i = 1; $i <= 20; $i++) {
        $accountfield       = "Group" . $i . "Account";
        $departmentfield    = "Group" . $i . "DepartmentID";
        $projectfield       = "Group" . $i . "ProjectID";
        $namefield          = "Group" . $i . "Name";
        
        if($sale_conf->{$accountfield}) {
            $sum_sale[$i]->department   = $sale_conf->{$departmentfield};
            $sum_sale[$i]->project      = $sale_conf->{$projectfield};
            $sum_sale[$i]->account      = $sale_conf->{$accountfield};
        }
        
        if($revenue_conf->{$accountfield}) {
            $sum_rev[$i]->department    = $revenue_conf->{$departmentfield};
            $sum_rev[$i]->project       = $revenue_conf->{$projectfield};
            $sum_rev[$i]->account       = $revenue_conf->{$accountfield};
        }
    }

    #print_r($revenue_conf);
    #print_r($sum_rev);

    $query_sale         = "select * from weeklysaleday where WeeklySaleID = '$WeeklySaleID' and Type=1 order by DayID";
    $result_sale        = $_lib['db']->db_query($query_sale);

    $query_revenue  = "select * from weeklysaleday where WeeklySaleID = '$WeeklySaleID' and Type=2 order by DayID";
    $result_revenue = $_lib['db']->db_query($query_revenue);

    while($sale = $_lib['db']->db_fetch_object($result_sale)) {
        #Sum vertical
        for($i = 1; $i <= 20; $i++) {
            $namefield          = "Group" . $i . "Name";
            $amountfield        = "Group" . $i . "Amount";
            $quantityfield      = "Group" . $i . "Quantity";


            if($sale_conf->{$namefield}) {
                $sum_sale[$i]->amount   += $sale->{$amountfield};
                $sum_sale[$i]->quantity += $sale->{$quantityfield};
                #print "$i: qty: " . $sale->{$quantityfield} . "<br>\n";
            }
        }
    }

    while($revenue = $_lib['db']->db_fetch_object($result_revenue))
    {
       #Sum vertical
        for($i = 1; $i <= 17; $i++) {
            $accountfield       = "Group" . $i . "Account";
            $namefield          = "Group" . $i . "Name";
            $amountfield        = "Group" . $i . "Amount";
            $quantityfield      = "Group" . $i . "Quantity";

            if($revenue_conf->{$accountfield}) { 
                $sum_rev[$i]->amount   += $revenue->{$amountfield};
                $sum_rev[$i]->quantity += $revenue->{$quantityfield};
            }
        }
    }

  #sjekker om ukeomsettningen har fått bilagsnr,
  #hvis den har det må vi ta hensyn til et par ting
  if(isset($week->JournalID))
  {
    /*#sjekke om nytt bilagnr eksisterer
    #hvis det gjør, så legge til alle gamle linjer med flagg satt
    $query = "select JournalID from voucher where JournalID=$week->JournalID limit 1";
    $row   = $_lib['storage']->get_row(array('query' => $query));

    #hvis ikke nytt bilagnr eksisterer fra før, bare opprette på nytt
    if(!$row->JournalID)
    {
        //list($JournalID, $message) = $accounting->get_next_available_journalid(array('available' => true, 'update' => true, 'type' => $VoucherType));
    }

    if($JournalID)
    {
        #ta vare på alle gammle posteringslinjer.
        /*$query = "select * from voucher where JournalID=".$week->JournalID." and AutoFromWeeklySale=".$week->WeeklySaleID." and VoucherType='".$VoucherType."'";
        $result = $_lib['db']->db_query($query);
        $fields['voucher_JournalID']      = $JournalID;
        $fields['voucher_VoucherPeriod']  = $_lib['date']->get_this_period($_lib['sess']->get_session('Date'));
        $fields['voucher_VoucherDate']    = $_lib['sess']->get_session('Date');
        $fields['voucher_Active']         = 1;
        $fields['voucher_AutomaticReason'] = "Ukeomsetning: $WeeklySaleID";
        $fields['voucher_DepartmentID']   = $week->DepartmentID;
        $fields['voucher_AutoFromWeeklySale'] = $week->WeeklySaleID;

        while($row = $_lib['db']->db_fetch_object($result))
        {
            $fields['voucher_ProjectID'] = $row->ProjectID;
            $fields['voucher_AmountIn']   = $row->AmountIn;
            $fields['voucher_AmountOut']  = $row->AmountOut;
            $fields['voucher_DepartmentID'] = $row->DepartmentID;
            $fields['voucher_AccountPlanID'] = $row->AccountPlanID;
            $accounting->insert_voucher_line(array('post'=>$fields, 'accountplanid'=>$row->AccountPlanID, 'type'=>'result1', 'VoucherType'=>$VoucherType));
        }
    }*/

    includelogic('postmotpost/postmotpost');
    $postmotpost = new postmotpost(array());
    $postmotpost->openPostJournal($week->JournalID, $VoucherType);

    #Delete old accounting
    #vi må bare slette posteringslinjene hvor autoflagg er satt
    $accounting->delete_auto_weeklysale($week->WeeklySaleID, $week->JournalID, $VoucherType);
  }
  #hvis ikke ukeomsettningen har fått bilagsnr enda
  else
  {
    #Get a new available journal id
    list($JournalID, $message) = $accounting->get_next_available_journalid(array('available' => true, 'update' => true, 'type' => $VoucherType));
    if($message) $_lib['message']->add(array('message' => $message));

    #Update journal id on weekly sales
    $pk['WeeklySaleID'] = $WeeklySaleID;
    $weekfield['weeklysale_JournalID'] = $JournalID;
    $_lib['storage']->db_update_hash($weekfield, 'weeklysale', $pk);
  }

  ###########################
  #oppdatere posteringer ut i fra form data
  #
  $fields['voucher_JournalID']      = $JournalID;
  $fields['voucher_VoucherPeriod']  = $week->Period;
  $fields['voucher_VoucherDate']    = $week->JournalDate;
  $fields['voucher_Active']         = 1;
  $fields['voucher_AutomaticReason'] = "Ukeomsetning: $WeeklySaleID";
  $fields['voucher_AutoFromWeeklySale'] = $WeeklySaleID;
  $fields['voucher_JournalID'] = $week->JournalID;

  $i=0;
  foreach($sum_sale as $i => $line)
  {
    if(isset($fields['voucher_AmountIn']))
        unset($fields['voucher_AmountIn']);
    if(isset($fields['voucher_AmountOut']))
        unset($fields['voucher_AmountOut']);
    $i++;
    if($line->account > 0) {
		if($line->department > 0) {
		  $fields['voucher_DepartmentID'] = $line->department;
		}
		if($line->project > 0) {
		  $fields['voucher_ProjectID']    = $line->project;
		}
		if($line->quantity > 0) {
		  $fields['voucher_Quantity']    = $line->quantity;
		}
		
		if($line->amount != 0) { #We do not insert posteringer with 0
		  if($line->amount < 0)
		  {
			$fields['voucher_AmountIn']   = abs($line->amount);
		  }
		  else
		  {
			$fields['voucher_AmountOut']  = abs($line->amount);
		  }
  
		  $accounting->insert_voucher_line(array('post'=>$fields, 'accountplanid'=>$line->account, 'VoucherType'=>$VoucherType));
		}
    }
  }

  if(isset($fields['voucher_AmountIn']))
    unset($fields['voucher_AmountIn']);
  if(isset($fields['voucher_AmountOut']))
    unset($fields['voucher_AmountOut']);

    $i=0;
    foreach($sum_rev as $i => $line)
    {
    
        if(isset($fields['voucher_AmountIn']))
            unset($fields['voucher_AmountIn']);
        if(isset($fields['voucher_AmountOut']))
            unset($fields['voucher_AmountOut']);
        if(isset($fields['voucher_DepartmentID']))
            unset($fields['voucher_DepartmentID']);
        if(isset($fields['voucher_ProjectID']))
            unset($fields['voucher_ProjectID']);
        if(isset($fields['voucher_Quantity']))
            unset($fields['voucher_Quantity']);

        $i++;
        if($line->account > 0) {

            if($line->department > 0) {
                $fields['voucher_DepartmentID'] = $line->department;
            }
            if($line->project > 0) {
                $fields['voucher_ProjectID']    = $line->project;
            }

            if($line->quantity > 0) {
                $fields['voucher_Quantity']    = $line->quantity;
            }

       
            if($line->amount != 0) { #We do not insert posteringer with 0
                if($line->amount > 0)
                {
                    $fields['voucher_AmountIn']  = abs($line->amount);
                }
                else
                {
                    $fields['voucher_AmountOut'] = abs($line->amount);
                }
                
                $fields['voucher_AccountPlanID'] = $line->account;
                
                //print "buy $i";
                $accounting->insert_voucher_line(array('post'=>$fields, 'accountplanid'=>$line->account, 'VoucherType'=>$VoucherType));
            }
        }
    }

  $fields['voucher_AccountPlanID'] = $revenue_conf->Group18Account;

  if(isset($fields['voucher_AmountIn']))
      unset($fields['voucher_AmountIn']);
  if(isset($fields['voucher_AmountOut']))
      unset($fields['voucher_AmountOut']);
  if($week->TotalCash >= 0)
    $fields['voucher_AmountIn'] = $week->TotalCash;
  if($week->TotalCash < 0)
    $fields['voucher_AmountOut'] = abs($week->TotalCash);
    
  $fields['voucher_DepartmentID']   = $revenue_conf->Group18DepartmentID;
  $fields['voucher_ProjectID']      = $revenue_conf->Group18ProjectID;

  //print_r($fields);
  $accounting->insert_voucher_line(array('post'=>$fields, 'accountplanid'=>$revenue_conf->Group18Account, 'VoucherType'=>$VoucherType));

  $accounting->correct_journal_balance($fields, $week->JournalID, $VoucherType);

  #Automatically update motkonto resultat og balanse for perioden (Always on all changes)
  $post = array();
  $post['voucher_VoucherPeriod'] = $fields['voucher_VoucherPeriod'];
  $post['voucher_VoucherDate'] = $fields['voucher_VoucherDate'];
  $accounting->set_journal_motkonto(array('post'=>$post, 'VoucherType'=>$VoucherType));

  #oppdatere motkontoer hvis vi har byttet periode
  if($_POST['VoucherPeriodOld'] != $_POST['weeklysale_Period_'.$POST['WeeklySaleID']])
  {
      $post = array();
      $post['voucher_VoucherPeriod'] = $_POST['VoucherPeriodOld'];
      $post['voucher_VoucherDate'] = $_POST['weeklysale_JournalDate_'.$POST['WeeklySaleID']];
      $accounting->set_journal_motkonto(array('post'=>$post, 'VoucherType'=>$VoucherType));
  }
}

function update_weeklysale($post, $pk){
    global $_lib, $accounting, $znrtotaldiff, $znrwrong;
    
    #print "<h2>1: Kaller med: $pk</h2>\n";
    #print_r($post);

	#We should check for changes in period or VoucherType to update all subsystems correctly.
    $query_week  		= "select * from weeklysale where WeeklySaleID = '" . $pk . "'";
    $result_week 		= $_lib['db']->db_query($query_week);
    $week        		= $_lib['db']->db_fetch_object($result_week);
    $VoucherType 		= $week->VoucherType;
	#print "VoucherType: $VoucherType, pk: $pk<br>\n";
  
    $oldID = $post['weeklysale_JournalID_'.$post['WeeklySaleID']];
    #print_r($post);
    unset($message);
    list($JournalID, $message) = $accounting->get_next_available_journalid(array('verify' => true, 'JournalID' => $post['weeklysale_JournalID_'.$post['WeeklySaleID']], 'type' => $VoucherType, 'AutoFromWeeklySaleID'=>$post['WeeklySaleID']));
    if($message) $_lib['message']->add(array('message' => $message));
    $post['weeklysale_JournalID_'.$post['WeeklySaleID']] = $JournalID;
  
    if($JournalID != $oldID)
      $_lib['message']->add(array('message' => "Bilagsnr finnes allerede, du ble tildelt et nytt"));

    /*
     * In addition to checking if a record in the Voucher table is using $JournalID we must check 
     * if an existing weeklysale has already asked to use the $JournalID and in that 
     * case return, and not update. In the future, if we should find the next available $JournalID
     * based on a search in both Voucher and weeklysale tables instead.
     */
    $duplicates = $_lib['db']->db_query("SELECT WeeklySaleID FROM weeklysale WHERE JournalID = ".$JournalID." AND WeeklySaleID != " . $post['WeeklySaleID'] . " AND VoucherType = '" . $VoucherType . "'");
    if($_lib['db']->db_numrows($duplicates) >= 1) {
        printf('Ikke oppdater ukeomsetning siden det allerede finnes en ukeomsetning med bilagsnummer %d.', $JournalID);
        return false;
    }
  
    #Possible to extend or alter parameters here
    $tables['weeklysale']     = 'WeeklySaleID';
    $tables['weeklysaleday']  = 'WeeklySaleDayID';
  
    $query_sale  = "select * from weeklysaleday where WeeklySaleID='".$post['WeeklySaleID']."' and Type=1";
    $result_sale = $_lib['db']->db_query($query_sale);
  
    $query_revenue  = "select * from weeklysaleday where WeeklySaleID='".$post['WeeklySaleID']."' and Type=2";
    $result_revenue = $_lib['db']->db_query($query_revenue);
  
    $sumtotal = 0;
    $sumSale  = 0;
    $cashIn   = 0;
    $cashOut  = 0;
    while($sale = $_lib['db']->db_fetch_object($result_sale)){
        $sum                = 0;
        $saleValuesExist    = 0;
        $revenueValuesExist = 0;
        $rowExist           = 0;
    
        $revenue = $_lib['db']->db_fetch_object($result_revenue);
    
        if(isset($post["weeklysaleday_Locked_".$revenue->WeeklySaleDayID]) and strlen($post["weeklysaleday_Locked_".$revenue->WeeklySaleDayID]) > 0)
        {
            $post["weeklysaleday_Locked_".$sale->WeeklySaleDayID] = $post["weeklysaleday_Locked_".$revenue->WeeklySaleDayID];
        }
    
        #MŒ oppdatere dagid og znr fra topp linje basert pŒ parentweeklysaledayid. Denne algoritmen forusetter like mange pŒ begge sider for Œ fungere parentweeklysale kan brukes for Œ gj¿re det mer robust
        #MŒ i sŒ fall kj¿res til slutt etter at alt er lagret.
    
        if(isset($post["weeklysaleday_Znr_".$sale->WeeklySaleDayID]) and strlen($post["weeklysaleday_Znr_".$sale->WeeklySaleDayID]) > 0)
        {
            if($post["weeklysaleday_Znr_".$sale->WeeklySaleDayID] > 0) {
                $weeklysale = new weeklysale($post['WeeklySaleID'], $post['WeeklySaleConfID']);
                $query_template         = "select wsc.* from weeklysale as ws, weeklysaleconf as wsc where wsc.WeeklySaleConfID=ws.WeeklySaleConfID and ws.WeeklySaleID=" . (int) $post['WeeklySaleID'] . "";
                $result_template        = $_lib['db']->db_query($query_template);
                $template               = $_lib['db']->db_fetch_object($result_template);
                $countznr = $weeklysale->isUniqueZnr($template->Name, $post["weeklysaleday_Znr_".$sale->WeeklySaleDayID]);
                if($countznr > 1) {
                    $_lib['message']->add(array('message' => "Dette Znr: " . $post["weeklysaleday_Znr_".$sale->WeeklySaleDayID] . " er brukt $countznr ganger tidligere i denne malen"));
                    $znrwrong[$sale->WeeklySaleDayID] = "Dette Znr: " . $post["weeklysaleday_Znr_".$sale->WeeklySaleDayID] . " er brukt $countznr ganger tidligere i denne malen";
                }
            }
        }
    
        #print_r($sale); print "<br>";
    
        $linesalesum = 0;
        for($i=1; $i<=20; $i++)
        {
    
            $hash = $_lib['convert']->Amount(array('value'=>$post["weeklysaleday_Group".$i."Amount_".$sale->WeeklySaleDayID]));
            $tmp = $hash['value'];
            # print "Znr" . $post["weeklysaleday_Znr_".$sale->WeeklySaleDayID] . " Group".$i."Amount_$sale->WeeklySaleDayID: $tmp<br>";
    
        
            if($post["weeklysaleday_Group".$i."Amount_".$sale->WeeklySaleDayID] > 0) {
                $hash = $_lib['convert']->Amount(array('value'=>$post["weeklysaleday_Group".$i."Amount_".$sale->WeeklySaleDayID]));
                $tmp = $hash['value'];
                $_lib['message']->add(array('message' => $hash['error']));
                $linesalesum += $tmp;
                $sumSale += $tmp;
            }
    
            if($i < 18)
            {
                if($post["weeklysaleday_Group".$i."Amount_".$revenue->WeeklySaleDayID] > 0) {
                    $hash = $_lib['convert']->Amount(array('value'=>$post["weeklysaleday_Group".$i."Amount_".$revenue->WeeklySaleDayID]));
                    $tmp = $hash['value'];
                    $_lib['message']->add(array('message' => $hash['error']));
                    #$linesalesum -= $tmp;
                }
            }
            elseif($i == 19)
            {
                if($post["weeklysaleday_Group".$i."Amount_".$revenue->WeeklySaleDayID] > 0) {
                    $hash = $_lib['convert']->Amount(array('value'=>$post["weeklysaleday_Group".$i."Amount_".$revenue->WeeklySaleDayID]));
                    $tmp = $hash['value'];
                    $_lib['message']->add(array('message' => $hash['error']));
                    $cashIn += $tmp;
                }
            }
            elseif($i == 20)
            {
                if($post["weeklysaleday_Group".$i."Amount_".$revenue->WeeklySaleDayID] > 0) {
                    $hash = $_lib['convert']->Amount(array('value'=>$post["weeklysaleday_Group".$i."Amount_".$revenue->WeeklySaleDayID]));
                    $tmp = $hash['value'];
                    $_lib['message']->add(array('message' => $hash['error']));
                    $cashOut += $tmp;
                }
            }
    
            if(isset($post["weeklysaleday_Group".$i."Amount_".$sale->WeeklySaleDayID]) and $sale->Locked != 1)
                $rowExist = 1;
    
            if($post["weeklysaleday_Group".$i."Amount_".$sale->WeeklySaleDayID] > 0)
                $saleValuesExist = 1;
    
            if($i < 18)
            {
                if(isset($post["weeklysaleday_Group".$i."Amount_".$revenue->WeeklySaleDayID]) and $revenue->Locked != 1)
                    $rowExist = 1;
    
                if($post["weeklysaleday_Group".$i."Amount_".$revenue->WeeklySaleDayID] > 0)
                    $revenueValuesExist = 1;
            }
        }
        
        #print_r($_lib['input']->_dataH['localhost']['konsulentvikaren0']['weeklysaleday']);
    
        $post["weeklysaleday_Group18Amount_".$revenue->WeeklySaleDayID] = $linesalesum;
        $sumtotal += $linesalesum;
    
        
    //print "($saleValuesExist == 1 or $revenueValuesExist == 1) and $rowExist - L:$sale->Locked <br>";
        if(($saleValuesExist == 1 or $revenueValuesExist == 1) and $rowExist) // and !($sale->PersonID > 0)
        {
            //print "1<br>";
            $post["weeklysaleday_PersonID_".$sale->WeeklySaleDayID] = $_lib['sess']->get_person('PersonID');
            $post["weeklysaleday_PersonID_".$revenue->WeeklySaleDayID] = $_lib['sess']->get_person('PersonID');
    
            if(!$post["weeklysaleday_Znr_".$sale->WeeklySaleDayID]) {
                $_lib['message']->add(array('message' => 'N&aring;r du fyller ut bel&oslash;p p&aring; en linje m&aring; du ogs&aring; lagre med Znr'));
                $znrwrong[$sale->WeeklySaleDayID] = 'N&aring;r du fyller ut bel&oslash;p p&aring; en linje m&aring; du ogs&aring; lagre med Znr';
            }
    
        }
        elseif($rowExist == 1)
        {
            //print "2<br>";
            $post["weeklysaleday_PersonID_".$sale->WeeklySaleDayID] = 0;
            $post["weeklysaleday_PersonID_".$revenue->WeeklySaleDayID] = 0;
        }
        else
        {
            //print "3<br>";
            unset($post["weeklysaleday_PersonID_".$sale->WeeklySaleDayID]);
            unset($post["weeklysaleday_PersonID_".$revenue->WeeklySaleDayID]);
        }
    
    
    }
    $post['weeklysale_TotalSale_'.$_POST['WeeklySaleID']] = $sumSale;
    
    $post["weeklysale_UpdatedByPersonID_$pk"] = $_lib['sess']->get_person('PersonID');
    #print_r($post);
    $_lib['storage']->db_update_multi_table($post, $tables);
    
    $update_likvid    = "update weeklysaleday as wssale, weeklysaleday as wslikvid set wslikvid.Day=wssale.Day, wslikvid.Znr=wssale.Znr where wslikvid.ParentWeeklySaleDayID=wssale.WeeklySaleDayID";
    $_lib['db']->db_query($update_likvid);
  
    #Calculate the sum again.
    $weeklysale = new weeklysale($post['WeeklySaleID'], $WeeklySaleConfID);
    $weeklysale->presentation();
  
    $postsum['weeklysale_TotalAmount_'.$_POST['WeeklySaleID']]   = $weeklysale->head->saletotal;
    $postsum['weeklysale_TotalCash_'.$_POST['WeeklySaleID']]     = $weeklysale->head->sumcash;
  
    #Update with calculated values
    $_lib['storage']->db_update_multi_table($postsum, $tables);
  
    return $message2;
}

#input: hash with keys: WeeklySaleID, DayID, [Day] 
function new_weeklysaleday($args) {
	global $_lib;
	
  	if($args['DayID']) {
		$postsub['weeklysaleday_WeeklySaleID']      = $args['WeeklySaleID'];
	  
		$postsub['weeklysaleday_Day']             	= $args['Day'];  #dag	
		$postsub['weeklysaleday_DayID']             = $args['DayID'];  #dag	
		$postsub['weeklysaleday_Type']              = 1;  #Inntekt
		
		$WeeklySaleDayID = $_lib['storage']->db_new_hash($postsub, 'weeklysaleday');
		$_lib['db']->db_update("update weeklysaleday set ParentWeeklySaleDayID=$WeeklySaleDayID where WeeklySaleDayID=$WeeklySaleDayID");
		
		$postsub['weeklysaleday_ParentWeeklySaleDayID'] = $WeeklySaleDayID;
		$postsub['weeklysaleday_Type']                  = 2;  #Likvidkonto
		$_lib['storage']->db_new_hash($postsub, 'weeklysaleday');
	} else {
		print "Missing DayID parameter<br>\n";
	}
}

?>
