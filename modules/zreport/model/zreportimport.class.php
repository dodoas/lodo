<?
/** ****************************************************************************
* Lodo functionality
*
* @package lodo_logic_zreportimport
* @version  $Id:
* @author Svenn Salvesen, Empatix AS
* @copyright http://www.empatix.no/ Empatix AS, 2005-2006, post@empatix.no
*/

includelogic('simplexml/simplexml');
includelogic('weeklysale/weeklysale');
includelogic('accounting/accounting');

class lodo_logic_zreportimport
{
    public $status          = 1;
    public $count           = 0;  #Number of spesific hits in database

    private $dayH = array(
        'mon'=>'1',
        'tue'=>'2',
        'wed'=>'3',
        'thu'=>'4',
        'fri'=>'5',
        'sat'=>'6',
        'sun'=>'7',
    );

    /***************************************************************************
    * Class Constructor
    * @param array(DAddress, DAddressNumber, DAddressLetter, DAddressCombined, DZipCode, DCity);
    * @return Define return og function
    */
    function __construct(array $args)
    {
        global $_lib;
        foreach($args as $key => $value)
        {
            //print "$key - $value <br>\n";
            $this->{$key} = $value;
        }
    }

    /***************************************************************************
    * Initialize
    * @param Define input parameters
    * @return Define return og function
    */
    function Execute($args)
    {
        global $_lib, $_SETUP;

        $this->xmlin = simplexml_load_string($this->xml, XMLElement);
        $zreportxml = $this->xmlin->Body->ZReport;
        #print_r($zreportxml);
        #print "<hr>";

        $_SETUP['DB_NAME']['0'] = (string)utf8_decode($zreportxml->Inst);
        $_lib['storage'] = $_lib['db'] = $_dblodo = $_dbh[$_dsn] = & new db_mysql(array('host' => $_SETUP['DB_SERVER']['0'], 'database' => $_SETUP['DB_NAME']['0'], 'username' => $_SETUP['DB_USER']['0'], 'password' => $_SETUP['DB_PASSWORD']['0'], '_sess' => $_sess));

        $this->CreateWeeklysale($zreportxml);

        if(count($errors) > 0)
        {
            $xml = new XMLElement("<ZReport></ZReport>");

            return $xml;
        }
        else
        {
            return $zreportxml->children();
        }
    }

    /***************************************************************************
    * Find the nearest servicepartners based on pythagoras.
    * @param (UTM33x, UTM33y)
    * @return hash(CompanyID => Distance)
    */
    function CreateWeeklysale($zreportxml)
    {
        global $_lib;

        $accounting = new accounting();

        $VoucherType = 'K';
        $WeeklySaleTemplate = (string)utf8_decode($zreportxml->Template);
        
        $DayID              = strtolower((string)utf8_decode($zreportxml->Day));
        $Week               = (string)utf8_decode($zreportxml->Week);
        $Year               = (string)utf8_decode($zreportxml->Year);
        $Date               = (string)utf8_decode($zreportxml->Date);
        $Time               = (string)utf8_decode($zreportxml->Time);
        $AccountingPeriod   = (string)utf8_decode($zreportxml->AccountingPeriod);
        $ZReportID          = (string)utf8_decode($zreportxml->ZReportID);
        $TotalAmount        = (string)utf8_decode($zreportxml->TotalAmount);

        if(strlen($AccountingPeriod) == 6 || strlen($AccountingPeriod) == 7) {
            list($year, $month) = split('-', $AccountingPeriod);
            if(strlen($month) == 1)
                $AccountingPeriod = "$year-0$month";
        } else {
            $_lib['message']->add(array('message' => "Periode format ulovlig, det skal v¾re YYYY-MM"));
        }
        
        #sjekke om hode finnes
## Her holder det ikke å sjekke på ukenr, vi må sjekke på år eller dato også?
        $query_weeklysale = "select * from weeklysale where Week='".$Week."' and Year='".$Year."' and Period='".$AccountingPeriod ."' and Name='$WeeklySaleTemplate'";
        #print "$query_weeklysale<br />\n";
        $result_weeklysale  = $_lib['db']->db_query($query_weeklysale);
        $weeklysaleRow      = $_lib['db']->db_fetch_object($result_weeklysale);
        if($weeklysaleRow->WeeklySaleID > 0)
            $weeklysaleExist = true;
        else
            $weeklysaleExist = false;

        $query_sale_conf  = "select * from weeklysaleconf where Name='$WeeklySaleTemplate'";
        $result_sale_conf = $_lib['db']->db_query($query_sale_conf);
        $sale_conf        = $_lib['db']->db_fetch_object($result_sale_conf);

        $weeklysale = new weeklysale($weeklysaleRow->WeeklySaleID, $sale_conf->WeeklySaleConfID);

        if($weeklysale->isUniqueZnr($WeeklySaleTemplate, $ZReportID) > 0) {
            $_lib['message']->add(array('message' => "Dette Znr: $ZReportID er brukt tidligere i denne malen"));
        }
        
        $postmain['weeklysale_Name']              = $sale_conf->Name;
        $postmain['weeklysale_PermanentCash']     = $sale_conf->PermanentCash;
        $postmain['weeklysale_DepartmentID']      = $sale_conf->DepartmentID;
        $postmain['weeklysale_Period']            = $AccountingPeriod;
        $postmain['weeklysale_CreatedDate']       = $_lib['sess']->get_session('Date');
        $postmain['weeklysale_CreatedByPersonID'] = $_lib['sess']->get_person('PersonID');
        $postmain['weeklysale_ChangedByPersonID'] = $_lib['sess']->get_person('PersonID');
        $postmain['weeklysale_WeeklySaleConfID']  = $sale_conf->WeeklySaleConfID;
        $postmain['weeklysale_JournalDate']       = $Date;
        $postmain['weeklysale_Week']              = $Week;
        $postmain['weeklysale_Year']              = $Year;

        #print_r($postmain);

        if($weeklysaleExist)
        {
            #print "Finnes<br>";
            $WeeklySaleID = $weeklysaleRow->WeeklySaleID;
            $this->VoucherPeriodOld = $weeklysaleRow->Period;

            $primarykey['WeeklySaleID'] = $WeeklySaleID;
            $postmain['weeklysale_JournalID'] = $weeklysaleRow->JournalID;

            $_lib['storage']->db_update_hash($postmain, 'weeklysale', $primarykey);
        }
        else
        {
            #print "Finnes ikke<br>";

            $this->VoucherPeriodOld = '';

            list($JournalID, $message) = $accounting->get_next_available_journalid($_lib['sess'], array('available' => true, 'update' => true, 'type' => $VoucherType));

            $postmain['weeklysale_JournalID'] = $JournalID;

            #Possible to extend or alter parameters here
            $WeeklySaleID = $_lib['storage']->db_new_hash($postmain, 'weeklysale');
        }
//print_r($postmain);

        #sjeke om dag / linje finnes
        $query_weeklysaleday = "select * from weeklysaleday where Type=1 and WeeklySaleID='$WeeklySaleID' and DayID='".$this->dayH[$DayID]."' and Znr='$ZReportID'";
        //print $query_weeklysaleday;
        $result_weeklysaleday = $_lib['db']->db_query($query_weeklysaleday);
        $weeklysaledayRow = $_lib['db']->db_fetch_object($result_weeklysaleday);
        if($weeklysaledayRow->WeeklySaleID > 0)
            $weeklysaledayExist = true;
        else
            $weeklysaledayExist = false;

        #type=1
        $postsub = array();
        $postsub['weeklysaleday_WeeklySaleID']  = $WeeklySaleID;
        $postsub['weeklysaleday_Type']          = 1;  #Inntekt
        $postsub['weeklysaleday_DayID']         = $this->dayH[$DayID];
        $postsub['weeklysaleday_Day']           = substr($Date, 8, 10);
        $postsub['weeklysaleday_Znr']           = $ZReportID;
        $postsub['weeklysaleday_ZnrTotalAmount']= $TotalAmount;
        $postsub['weeklysaleday_Datetime']      = $Date . ' ' . $Time;
        
        $checksalegroup = array();
        foreach($zreportxml->Groups->Group as $key => $value)
        {
            $query_conf = "select * from weeklysalegroupconf where WeeklySaleConfID='$sale_conf->WeeklySaleConfID' and Type=1 limit 1";
            $result_conf = $_lib['db']->db_query($query_conf);
            $conf = $_lib['db']->db_fetch_object($result_conf);

            if($weeklysale->salehead['groups'][utf8_decode($value->Name)]) {
                $checksalegroup[utf8_decode($value->Name)] = $weeklysale->salehead['groups'][utf8_decode($value->Name)];

                for($i=0;$i<=20;$i++)
                {
                    //print "(".$conf->{"Group".$i."Name"}." == ".(string)$value->Name.")\n";
                    if($conf->{"Group".$i."Name"} == (string)utf8_decode($value->Name))
                    {
                        $postsub['weeklysaleday_Group'.$i.'Amount'] = (string)utf8_decode($value->Amount);
                        $sumsale += $postsub['weeklysaleday_Group'.$i.'Amount'];
                        break;
                    }
                }
            } else {
                $_lib['message']->add(array('message' => "Salgsgruppen $value->Name som er oppgitt i XML finnes ikke i ukeomsetning"));
            }
        }
                
#print_r($postsub);
//print $weeklysaledayExist;
//exit;
        if($weeklysaledayExist)
        {
            $primarykey['WeeklySaleDayID'] = $weeklysaledayRow->WeeklySaleDayID;
            $_lib['storage']->db_update_hash($postsub, 'weeklysaleday', $primarykey);
        }
        else
        {
            $WeeklySaleDayID = $_lib['storage']->db_new_hash($postsub, 'weeklysaleday');
            $_lib['db']->db_update("update weeklysaleday set ParentWeeklySaleDayID=$WeeklySaleDayID where WeeklySaleDayID=$WeeklySaleDayID");
        }


        #sjeke om dag / linje finnes
        $query_weeklysaleday = "select * from weeklysaleday where Type=2 and WeeklySaleID='$WeeklySaleID' and DayID='".$this->dayH[$DayID]."' and Znr='$ZReportID'";
        $result_weeklysaleday = $_lib['db']->db_query($query_weeklysaleday);
        $weeklysaledayRow = $_lib['db']->db_fetch_object($result_weeklysaleday);
        if($weeklysaledayRow->WeeklySaleID > 0)
            $weeklysaledayExist = true;
        else
            $weeklysaledayExist = false;

        #type=2
        $postsub = array();
        $postsub['weeklysaleday_WeeklySaleID']          = $WeeklySaleID;
        $postsub['weeklysaleday_ParentWeeklySaleDayID'] = $WeeklySaleDayID;
        $postsub['weeklysaleday_Type']                  = 2;  #Likvidkonto
        $postsub['weeklysaleday_DayID']                 = $this->dayH[$DayID];
        $postsub['weeklysaleday_Day']                   = substr($Date, 8, 10);
        $postsub['weeklysaleday_Znr']                   = $ZReportID;
        $postsub['weeklysaleday_ZnrTotalAmount']        = $TotalAmount;

        $checkrevenuegroup = array();
        foreach($zreportxml->Payments->Group as $key => $value)
        {
            $query_conf = "select * from weeklysalegroupconf where WeeklySaleConfID='$sale_conf->WeeklySaleConfID' and Type=2 limit 1";
            $result_conf = $_lib['db']->db_query($query_conf);
            $conf = $_lib['db']->db_fetch_object($result_conf);

            if($weeklysale->revenuehead['groups'][utf8_decode($value->Name)] || utf8_decode($value->Name) == 'Kontant') {
                $checkrevenuegroup[utf8_decode($value->Name)] = $weeklysale->revenuehead['groups'][utf8_decode($value->Name)];

                for($i=0;$i<=20;$i++)
                {
                    if($conf->{"Group".$i."Name"} == (string)utf8_decode($value->Name))
                    {
                        $postsub['weeklysaleday_Group'.$i.'Amount'] = (string)utf8_decode($value->Amount);
                        $sumlikvid +=  (string)utf8_decode($value->Amount);
                        break;
                    } 
                }
                
                if(utf8_decode($value->Name) == 'Kontant') {
                    $likvidcash = (string)utf8_decode($value->Amount);
                }
                
            } else {
                $_lib['message']->add(array('message' => "Likvidgruppen #$value->Name# som er oppgitt i XML finnes ikke i ukeomsetning"));
            }
        }

        if($weeklysaledayExist)
        {
            $primarykey['WeeklySaleDayID'] = $weeklysaledayRow->WeeklySaleDayID;
            $_lib['storage']->db_update_hash($postsub, 'weeklysaleday', $primarykey);
        }
        else
        {
            $_lib['storage']->db_new_hash($postsub, 'weeklysaleday');
        }

        #kaller på sub funksjoner
        #list($sumsale, $sumlikvid) = $this->RecalculateWeeklysale(array('WeeklySaleID'=>$WeeklySaleID));
        $this->JournalWeeklysale(array('WeeklySaleID'=>$WeeklySaleID));

        if($TotalAmount != $sumsale) {
            #Summen er forskjellig fra den oppgitte sŒ det mŒ v¾re en feil
            $_lib['message']->add(array('message' => "Znr total ($TotalAmount) er forskjellig fra summen av salg ($sumsale)"));
        }

        #Kan ikke ha denne feilmeldingen da den vil feile hvis det er kontanter i kassen.
        if($TotalAmount != ($sumlikvid + $likvidcash)) {
            #Summen er forskjellig fra den oppgitte sŒ det mŒ v¾re en feil
            $_lib['message']->add(array('message' => "Znr total ($TotalAmount) er forskjellig fra summen av likvider ($sumlikvid + $likvidcash)"));
        }

        if($sumsale != $sumlikvid+$likvidcash) {
            #Summen er forskjellig fra den oppgitte sŒ det mŒ v¾re en feil
            $_lib['message']->add(array('message' => "Sum salg ($sumsale) er forskjellig fra summen av likvider ($sumlikvid + $likvidcash)"));
        }

        $salegroupdiff      = array_diff($weeklysale->salehead['groups'],     $checksalegroup);
                
        $revenuegroupdiff   = array_diff($weeklysale->revenuehead['groups'],  $checkrevenuegroup); 

        if(count($salegroupdiff)) {
            foreach($salegroupdiff as $name => $value) $groupssale .= "#$name#, ";
            #Summen er forskjellig fra den oppgitte sŒ det mŒ v¾re en feil
            $_lib['message']->add(array('message' => "Ubrukte salgsgrupper: $groupssale"));
        }

        if(count($revenuegroupdiff)) {
            foreach($revenuegroupdiff as $name => $value) $groupslikvid .= "#$name#, ";
            #Summen er forskjellig fra den oppgitte sŒ det mŒ v¾re en feil
            $_lib['message']->add(array('message' => "Ubrukte likvidgrupper: $groupslikvid"));
        }
    }

    function RecalculateWeeklysale($args)
    {
        global $_lib;

        $WeeklySaleID = $args['WeeklySaleID'];

        $query_sale  = "select * from weeklysaleday where WeeklySaleID='".$WeeklySaleID."' and Type=1 order by DayID asc limit 7";
        $result_sale = $_lib['db']->db_query($query_sale);

        $query_revenue  = "select * from weeklysaleday where WeeklySaleID='".$WeeklySaleID."' and Type=2 order by DayID asc limit 7";
        $result_revenue = $_lib['db']->db_query($query_revenue);

        $sumtotal = 0;
        $sumSale = 0;
        $cashIn = 0;
        $cashOut = 0;
        while($sale = $_lib['db']->db_fetch_object($result_sale))
        {
            $sum = 0;

            $revenue = $_lib['db']->db_fetch_object($result_revenue);
            for($i=1; $i<=20; $i++)
            {
                $tmp = $sale->{"Group".$i."Amount"};
                $sum += $tmp;
                $sumSale += $tmp;

                if($i < 18)
                {
                    $tmp = $revenue->{"Group".$i."Amount"};
                    $sum -= $tmp;
                }
                elseif($i == 19)
                {
                    $tmp = $revenue->{"Group".$i."Amount"};
                    $cashIn += $tmp;
                }
                elseif($i == 20)
                {
                    $tmp = $revenue->{"Group".$i."Amount"};
                    $cashOut += $tmp;
                }
            }
            $query = "update weeklysaleday set Group18Amount='$sum' where WeeklySaleDayID='$revenue->WeeklySaleDayID'";
            $_lib['db']->db_update($query);
            $sumtotal += $sum;
            //print $sum."\n";
        }

        #Update weeklysale
        $pk['WeeklySaleID'] = $WeeklySaleID;
        $post = array();
        $post['weeklysale_TotalSale'] = $sumSale;
        $post['weeklysale_TotalAmount'] = ($sumtotal + $cashIn - $cashOut);
        $post['weeklysale_TotalCash'] = $sumtotal;
        $_lib['storage']->db_update_hash($post, 'weeklysale', $pk);
        return array($sumSale, $sumtotal);
    }

    function JournalWeeklysale($args)
    {
        global $_lib;

        includelogic('accounting/accounting');
        $accounting = new accounting();

        $WeeklySaleID = $args['WeeklySaleID'];
        $VoucherType = 'K';

        $query_week         = "select * from weeklysale where WeeklySaleID = '$WeeklySaleID'";
        $result_week        = $_lib['db']->db_query($query_week);
        $week               = $_lib['db']->db_fetch_object($result_week);

        $query_conf  = "select * from weeklysaleconf where WeeklySaleConfID = '$week->WeeklySaleConfID'";
        $result_conf = $_lib['db']->db_query($query_conf);
        $conf        = $_lib['db']->db_fetch_object($result_conf);

        $query_sale_conf    = "select * from weeklysalegroupconf where WeeklySaleConfID = '$week->WeeklySaleConfID' and Type=1";
        $result_sale_conf   = $_lib['db']->db_query($query_sale_conf);
        $sale_conf          = $_lib['db']->db_fetch_object($result_sale_conf);

        $query_revenue_conf  = "select * from weeklysalegroupconf where WeeklySaleConfID = '$week->WeeklySaleConfID' and Type=2";
        $result_revenue_conf = $_lib['db']->db_query($query_revenue_conf);
        $revenue_conf        = $_lib['db']->db_fetch_object($result_revenue_conf);

        #Før billag
        $dep_sale[1]  = $sale_conf->Group1ProjectID;
        $dep_sale[2]  = $sale_conf->Group2ProjectID;
        $dep_sale[3]  = $sale_conf->Group3ProjectID;
        $dep_sale[4]  = $sale_conf->Group4ProjectID;
        $dep_sale[5]  = $sale_conf->Group5ProjectID;
        $dep_sale[6]  = $sale_conf->Group6ProjectID;
        $dep_sale[7]  = $sale_conf->Group7ProjectID;
        $dep_sale[8]  = $sale_conf->Group8ProjectID;
        $dep_sale[9]  = $sale_conf->Group9ProjectID;
        $dep_sale[10] = $sale_conf->Group10ProjectID;
        $dep_sale[11] = $sale_conf->Group11ProjectID;
        $dep_sale[12] = $sale_conf->Group12ProjectID;
        $dep_sale[13] = $sale_conf->Group13ProjectID;
        $dep_sale[14] = $sale_conf->Group14ProjectID;
        $dep_sale[15] = $sale_conf->Group15ProjectID;
        $dep_sale[16] = $sale_conf->Group16ProjectID;
        $dep_sale[17] = $sale_conf->Group17ProjectID;
        $dep_sale[18] = $sale_conf->Group18ProjectID;
        $dep_sale[19] = $sale_conf->Group19ProjectID;
        $dep_sale[20] = $sale_conf->Group20ProjectID;

        $dep_rev[1]  = $revenue_conf->Group1ProjectID;
        $dep_rev[2]  = $revenue_conf->Group2ProjectID;
        $dep_rev[3]  = $revenue_conf->Group3ProjectID;
        $dep_rev[4]  = $revenue_conf->Group4ProjectID;
        $dep_rev[5]  = $revenue_conf->Group5ProjectID;
        $dep_rev[6]  = $revenue_conf->Group6ProjectID;
        $dep_rev[7]  = $revenue_conf->Group7ProjectID;
        $dep_rev[8]  = $revenue_conf->Group8ProjectID;
        $dep_rev[9]  = $revenue_conf->Group9ProjectID;
        $dep_rev[10] = $revenue_conf->Group10ProjectID;
        $dep_rev[11] = $revenue_conf->Group11ProjectID;
        $dep_rev[12] = $revenue_conf->Group12ProjectID;
        $dep_rev[13] = $revenue_conf->Group13ProjectID;
        $dep_rev[14] = $revenue_conf->Group14ProjectID;
        $dep_rev[15] = $revenue_conf->Group15ProjectID;
        $dep_rev[16] = $revenue_conf->Group16ProjectID;
        $dep_rev[17] = $revenue_conf->Group17ProjectID;
        $dep_rev[18] = $revenue_conf->Group18ProjectID;
        $dep_rev[19] = $revenue_conf->Group19ProjectID;
        $dep_rev[20] = $revenue_conf->Group20ProjectID;

        $query_sale     = "select * from weeklysaleday where WeeklySaleID = '$WeeklySaleID' and Type=1";
        $result_sale    = $_lib['db']->db_query($query_sale);

        $query_revenue  = "select * from weeklysaleday where WeeklySaleID = '$WeeklySaleID' and Type=2";
        $result_revenue = $_lib['db']->db_query($query_revenue);

        $sum_sale = array();

        while($sale = $_lib['db']->db_fetch_object($result_sale))
        {
            #Sum vertical
            if($sale_conf->Group1Name) { $sum_sale[$sale_conf->Group1Account]  += $sale->Group1Amount; }
            if($sale_conf->Group2Name) { $sum_sale[$sale_conf->Group2Account]  += $sale->Group2Amount; }
            if($sale_conf->Group3Name) { $sum_sale[$sale_conf->Group3Account]  += $sale->Group3Amount; }
            if($sale_conf->Group4Name) { $sum_sale[$sale_conf->Group4Account]  += $sale->Group4Amount; }
            if($sale_conf->Group5Name) { $sum_sale[$sale_conf->Group5Account]  += $sale->Group5Amount; }
            if($sale_conf->Group6Name) { $sum_sale[$sale_conf->Group6Account]  += $sale->Group6Amount; }
            if($sale_conf->Group7Name) { $sum_sale[$sale_conf->Group7Account]  += $sale->Group7Amount; }
            if($sale_conf->Group8Name) { $sum_sale[$sale_conf->Group8Account]  += $sale->Group8Amount; }
            if($sale_conf->Group9Name) { $sum_sale[$sale_conf->Group9Account]  += $sale->Group9Amount; }
            if($sale_conf->Group10Name) { $sum_sale[$sale_conf->Group10Account] += $sale->Group10Amount; }
            if($sale_conf->Group11Name) { $sum_sale[$sale_conf->Group11Account] += $sale->Group11Amount; }
            if($sale_conf->Group12Name) { $sum_sale[$sale_conf->Group12Account] += $sale->Group12Amount; }
            if($sale_conf->Group13Name) { $sum_sale[$sale_conf->Group13Account] += $sale->Group13Amount; }
            if($sale_conf->Group14Name) { $sum_sale[$sale_conf->Group14Account] += $sale->Group14Amount; }
            if($sale_conf->Group15Name) { $sum_sale[$sale_conf->Group15Account] += $sale->Group15Amount; }
            if($sale_conf->Group16Name) { $sum_sale[$sale_conf->Group16Account] += $sale->Group16Amount; }
            if($sale_conf->Group17Name) { $sum_sale[$sale_conf->Group17Account] += $sale->Group17Amount; }
            if($sale_conf->Group18Name) { $sum_sale[$sale_conf->Group18Account] += $sale->Group18Amount; }
            if($sale_conf->Group19Name) { $sum_sale[$sale_conf->Group19Account] += $sale->Group19Amount; }
            if($sale_conf->Group20Name) { $sum_sale[$sale_conf->Group20Account] += $sale->Group20Amount; }
        }

        $sum_rev = array();

        while($revenue = $_lib['db']->db_fetch_object($result_revenue))
        {
           #Sum vertical
           if($revenue_conf->Group1Name) { $sum_rev[$revenue_conf->Group1Account]  += $revenue->Group1Amount; }
           if($revenue_conf->Group2Name) { $sum_rev[$revenue_conf->Group2Account]  += $revenue->Group2Amount; }
           if($revenue_conf->Group3Name) { $sum_rev[$revenue_conf->Group3Account]  += $revenue->Group3Amount; }
           if($revenue_conf->Group4Name) { $sum_rev[$revenue_conf->Group4Account]  += $revenue->Group4Amount; }
           if($revenue_conf->Group5Name) { $sum_rev[$revenue_conf->Group5Account]  += $revenue->Group5Amount; }
           if($revenue_conf->Group6Name) { $sum_rev[$revenue_conf->Group6Account]  += $revenue->Group6Amount; }
           if($revenue_conf->Group7Name) { $sum_rev[$revenue_conf->Group7Account]  += $revenue->Group7Amount; }
           if($revenue_conf->Group8Name) { $sum_rev[$revenue_conf->Group8Account]  += $revenue->Group8Amount; }
           if($revenue_conf->Group9Name) { $sum_rev[$revenue_conf->Group9Account]  += $revenue->Group9Amount; }
           if($revenue_conf->Group10Name) { $sum_rev[$revenue_conf->Group10Account] += $revenue->Group10Amount; }
           if($revenue_conf->Group11Name) { $sum_rev[$revenue_conf->Group11Account] += $revenue->Group11Amount; }
           if($revenue_conf->Group12Name) { $sum_rev[$revenue_conf->Group12Account] += $revenue->Group12Amount; }
           if($revenue_conf->Group13Name) { $sum_rev[$revenue_conf->Group13Account] += $revenue->Group13Amount; }
           if($revenue_conf->Group14Name) { $sum_rev[$revenue_conf->Group14Account] += $revenue->Group14Amount; }
           if($revenue_conf->Group15Name) { $sum_rev[$revenue_conf->Group15Account] += $revenue->Group15Amount; }
           if($revenue_conf->Group16Name) { $sum_rev[$revenue_conf->Group16Account] += $revenue->Group16Amount; }
           if($revenue_conf->Group17Name) { $sum_rev[$revenue_conf->Group17Account] += $revenue->Group17Amount; }
           #if($revenue_conf->Group18Name) { $sum_rev[$revenue_conf->Group18Account] += $revenue->Group18Amount; }
           #$sum_rev[$revenue_conf->Group19Account] += $revenue->Group19Amount;
           #$sum_rev[$revenue_conf->Group20Account] -= $revenue->Group20Amount;
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
                //list($JournalID, $message) = $accounting->get_next_available_journalid($_sess, array('available' => true, 'update' => true, 'type' => $VoucherType));
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
            $JournalID = $week->JournalID;

            #Delete old accounting
            #vi må bare slette posteringslinjene hvor autoflagg er satt
            $accounting->delete_auto_weeklysale($week->WeeklySaleID, $week->JournalID, $VoucherType);
        }
        #hvis ikke ukeomsettningen har fått bilagsnr enda
        else
        {
            #Get a new available journal id
            list($JournalID, $message) = $accounting->get_next_available_journalid($_sess, array('available' => true, 'update' => true, 'type' => $VoucherType));

            #Update journal id on weekly sales
            $pk['WeeklySaleID'] = $WeeklySaleID;
            $weekfield['weeklysale_JournalID'] = $JournalID;
            $_lib['storage']->db_update_hash($weekfield, 'weeklysale', $pk);
        }

        $fields = array();
        ###########################
        #oppdatere posteringer ut i fra form data?????????????????????? stemmer denne kommentaren i recordfila?
        #
        $fields['voucher_JournalID']      = $JournalID;
        //$fields['voucher_JournalID']      = $week->JournalID;
        $fields['voucher_VoucherPeriod']  = $week->Period;
        $fields['voucher_VoucherDate']    = $week->JournalDate;
        $fields['voucher_Active']         = 1;
        $fields['voucher_AutomaticReason'] = "Ukeomsetning: $WeeklySaleID";
        $fields['voucher_DepartmentID']   = $week->DepartmentID;
        $fields['voucher_AutoFromWeeklySale'] = $WeeklySaleID;

        $i=0;
        foreach($sum_sale as $account => $amount)
        {
            if(isset($fields['voucher_AmountIn']))
                unset($fields['voucher_AmountIn']);
            if(isset($fields['voucher_AmountOut']))
                unset($fields['voucher_AmountOut']);
            $i++;
            if($account > 0)
            {
                if($dep_sale[$i] > 0)
                {
                    $fields['voucher_ProjectID'] = $dep_sale[$i];
                }
                if($amount != 0) #We do not insert posteringer with 0
                {
                    if($amount < 0)
                    {
                        $fields['voucher_AmountIn']   = abs($amount);
                    }
                    else
                    {
                        $fields['voucher_AmountOut']  = abs($amount);
                    }
                    //print_r($fields);
                    //print "sale $i";
                    $accounting->insert_voucher_line(array('post'=>$fields, 'accountplanid'=>$account, 'VoucherType'=>$VoucherType));
                    #print_r($fields);
                    #print $_lib['message']->get() . "<br>"; 

                }
            }
        }

        $i=0;
        foreach($sum_rev as $account => $amount)
        {
            if(isset($fields['voucher_AmountIn']))
                unset($fields['voucher_AmountIn']);
            if(isset($fields['voucher_AmountOut']))
                unset($fields['voucher_AmountOut']);
            $i++;
            if($account > 0)
            {
                if($dep_rev[$i] > 0)
                {
                    $fields['voucher_ProjectID'] = $dep_rev[$i];
                }
                if($amount != 0) #We do not insert posteringer with 0
                {
                    if($amount > 0)
                    {
                        $fields['voucher_AmountIn']  = abs($amount);
                    }
                    else
                    {
                        $fields['voucher_AmountOut'] = abs($amount);
                    }
                    
                    
                    //print "buy $i";
                    $accounting->insert_voucher_line(array('post'=>$fields, 'accountplanid'=>$account, 'VoucherType'=>$VoucherType));
                    #print_r($fields);
                    #print $_lib['message']->get() . " konto: $account<br>"; 
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

        //print_r($fields);
        $accounting->insert_voucher_line(array('post'=>$fields, 'accountplanid'=>$revenue_conf->Group18Account, 'VoucherType'=>$VoucherType));
        #print_r($fields);
        #print $_lib['message']->get() . "<br>"; 


        $accounting->correct_journal_balance($fields, $JournalID, $VoucherType);

        #Automatically update motkonto resultat og balanse for perioden (Always on all changes)
        $post = array();
        $post['voucher_VoucherPeriod'] = $fields['voucher_VoucherPeriod'];
        $post['voucher_VoucherDate'] = $fields['voucher_VoucherDate'];
        $accounting->set_journal_motkonto(array('post'=>$post, 'VoucherType'=>$VoucherType));

        #oppdatere motkontoer hvis vi har byttet periode
        if($this->VoucherPeriodOld != $week->Period)
        {
            $post = array();
            $post['voucher_VoucherPeriod'] = $this->VoucherPeriodOld;
            $post['voucher_VoucherDate'] = $week->JournalDate;
            $accounting->set_journal_motkonto(array('post'=>$post, 'VoucherType'=>$VoucherType));
        }
    }
}
?>