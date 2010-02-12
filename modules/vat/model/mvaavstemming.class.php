<?

##################################
#
# Ikke endre mva oppsett for valgt �r etter at mvaavstemming er opprettet
#
##################################

class mva_avstemming
{
    var $_query;
    var $_row;
    var $_tables;
    var $_retval;
    var $undefinedVatAccountPLanID = 0;

    var $_year;
    var $_mvaAvstemmingID;
    var $_mvaAvstemmingLineID;
    var $_mvaAvstemmingLineFieldID = array();

    var $_outAccountPlanID = array();
    var $_inAccountPlanID = array();

    var $registered = array();
    var $reported   = array();
    var $diff       = array();
    var $year       = '';
    var $vathash    = array();

    var $_post  = array();
    var $_post2 = array();
    var $_post3 = array();

    function mva_avstemming($args)
    {
        #Init
        $this->_sess    = $args['_sess'];
        $this->_dbh     = $args['_dbh'];
        $this->_dsn     = $args['_dsn'];
        $this->_date    = $args['_date'];
        $this->year     = $args['year'];

        $this->_query = "select distinct Vat from voucher where VoucherPeriod like '$this->year%' and Vat > 0 and Active=1 order by Vat desc";
        $this->vathash = $this->_dbh[$this->_dsn]->get_hash(array('query' => $this->_query, 'key' => 'Vat', 'value' => 'Vat'));

        $sql_undefinedVatAccount            = "select * from vat where VatID=40"; #FInd the account with vat code 10, no time limit
        $row_undefinedVatAccount            = $this->_dbh[$this->_dsn]->get_row(array('query' => $sql_undefinedVatAccount));
        #print_r($row_undefinedVatAccount);
        $this->undefinedVatAccountPlanID    = $row_undefinedVatAccount->AccountPlanID;
        #print "<br>Undef: $this->undefinedVatAccountPlanID<br>\n";

        foreach($this->vathash as $vatpercent => $tmp)
        {
            #$this->_query   = "select AccountPlanID from vat where Percent='$vatpercent' order by VatID asc";
            $this->_query    = "select c.AccountPlanID from voucher as c, voucher as m where m.VoucherPeriod like '" . $this->year . "%' and m.AutomaticVatVoucherID=c.VoucherID and m.Vat='" . $vatpercent . "' and m.VatID >= 10 and m.VatID <= 39 and m.Active=1 and c.Active=1 group by c.AccountPlanID"; # and AmountOut > 0
            //print "XXXX: $this->_query<br>";

            $this->_row     = $this->_dbh[$this->_dsn]->get_row(array('query' => $this->_query));
            $this->_outAccountPlanID[$vatpercent] = $this->_row->AccountPlanID;

            #$this->_query   = "select AccountPlanID from vat where Percent='$vatpercent' order by VatID desc";
            $this->_query    = "select c.AccountPlanID from voucher as c, voucher as m where m.VoucherPeriod like '" . $this->year . "%' and m.AutomaticVatVoucherID=c.VoucherID and m.Vat='" . $vatpercent . "' and m.VatID >= 40 and m.VatID <= 60 and m.Active=1 and c.Active=1 group by c.AccountPlanID"; # and AmountOut > 0

            $this->_row     = $this->_dbh[$this->_dsn]->get_row(array('query' => $this->_query));
            $this->_inAccountPlanID[$vatpercent] = $this->_row->AccountPlanID;
        }

        /***********************************************************************
        * Special handking on account $this->undefinedVatAccountPlanID without VAT
        */
        if($this->year)
        {
          $avstemmingQuery          = "select * from mvaavstemming where PeriodYear=$this->year";
          $this->avstemmingRow      = $this->_dbh[$this->_dsn]->get_row(array('query' => $avstemmingQuery));
          $this->MvaAvstemmingID    = $this->avstemmingRow->MvaAvstemmingID;

          $this->fix(); #Insert missing records if vat has changed

          #Make all numbers available
          $this->reported(array());
          //print "Test1";
          $this->registered(array());
          //print "Test2";
          $this->diff(array());
          //print "Test3";
        }
    }

    ##############################################################
    #I f�lge bokf�rt regnskap
    function registered($args)
    {
        for($i=1; $i<=12; $i++) #for all months
        {
            $Period = $this->year."-".sprintf("%02d",$i);
            $sumRegisteredOut = 0;
            $sumRegisteredIn = 0;
            $sumRegisteredGrunnlag = 0;

            $this->registered[$i]['FreeOmsettning']     = $this->FreeMva(array('FromPeriod'=>$Period, 'ToPeriod'=>$Period));
            $this->registered[$i]['NoVatOmsettning']     = $this->NoMva(array('FromPeriod'=>$Period, 'ToPeriod'=>$Period));

            #print_r($this->_inAccountPlanID);
            foreach($this->_inAccountPlanID as $vat => $account)
            {
                #print "Dette er en konto: $account, vat: $vat<br>\n";
                //$this->registered[$i]['Out'.$vat.'Mva']      = $this->outMva(array('FromPeriod'=>$Period, 'ToPeriod'=>$Period, 'Vat'=>$vat));
                //$this->registered[$i]['Grunnlag'.$vat.'Mva'] = $this->grlMva(array('FromPeriod'=>$Period, 'ToPeriod'=>$Period, 'Vat'=>$vat));
                $this->registered[$i]['In'.$vat.'Mva']       = $this->inMva(array('FromPeriod'=>$Period, 'ToPeriod'=>$Period, 'Vat'=>$vat));

                $tmpGrlOut = $this->grloutMva(array('FromPeriod'=>$Period, 'ToPeriod'=>$Period, 'Vat'=>$vat));
                $this->registered[$i]['Out'.$vat.'Mva'] = $tmpGrlOut['out'];
                $this->registered[$i]['Grunnlag'.$vat.'Mva'] = $tmpGrlOut['grl'];

                $sumRegisteredOut      += $this->registered[$i]['Out'.$vat.'Mva'];
                $sumRegisteredIn       += $this->registered[$i]['In'.$vat.'Mva'];
                $sumRegisteredGrunnlag += $this->registered[$i]['Grunnlag'.$vat.'Mva'];

                if($vat == '25') {
                  //print_r($this->registered[$i]);
                }
            }
            $this->registered[$i]['TotalOmsettning']    = $this->registered[$i]['FreeOmsettning'] + $sumRegisteredGrunnlag;
            $this->registered[$i][$this->undefinedVatAccountPlanID] = $this->UndefinedVatAccount(array('FromPeriod'=>$Period, 'ToPeriod'=>$Period, 'AccountPlanID' => $this->undefinedVatAccountPlanID));

                    /* print "Dette er en konto: $account, vat: $vat<br>\n";
                    $this->registered[$i]['Out'.$vat.'Mva']      = $this->outMva(array('FromPeriod'=>$Period, 'ToPeriod'=>$Period, 'AccountPlanID' => $account));
                    $this->registered[$i]['In'.$vat.'Mva']       = $this->inMva( array('FromPeriod'=>$Period, 'ToPeriod'=>$Period, 'AccountPlanID' => $account));
                    $this->registered[$i]['Grunnlag'.$vat.'Mva'] = $this->grlMva(array('FromPeriod'=>$Period, 'ToPeriod'=>$Period, 'AccountPlanID' => $account));

                    $sumRegisteredOut      += $this->registered[$i]['Out'.$vat.'Mva'];
                    $sumRegisteredIn       += $this->registered[$i]['In'.$vat.'Mva'];
                    $sumRegisteredGrunnlag += $this->registered[$i]['Grunnlag'.$vat.'Mva'];
                    */
            $this->registered[$i]['SumMva'] = $sumRegisteredOut + $sumRegisteredIn + $this->registered[$i][$this->undefinedVatAccountPlanID];

            $this->registered['total']['TotalOmsettning']   += $this->registered[$i]['TotalOmsettning'];
            $this->registered['total']['FreeOmsettning']    += $this->registered[$i]['FreeOmsettning'];
            $this->registered['total']['NoVatOmsettning']    += $this->registered[$i]['NoVatOmsettning'];
            $this->registered['total'][$this->undefinedVatAccountPlanID]              += $this->registered[$i][$this->undefinedVatAccountPlanID];

            foreach($this->_inAccountPlanID as $vat => $account)
            {
                $this->registered['total']['Out'.$vat.'Mva']      += $this->registered[$i]['Out'.$vat.'Mva'];
                $this->registered['total']['In'.$vat.'Mva']       += $this->registered[$i]['In'.$vat.'Mva'];
                $this->registered['total']['Grunnlag'.$vat.'Mva'] += $this->registered[$i]['Grunnlag'.$vat.'Mva'];
            }
            $this->registered['total']['SumMva']            += $this->registered[$i]['SumMva'];
        }

        foreach($this->_inAccountPlanID as $vat => $account)
        {
            $this->registered['percent']['Grunnlag'.$vat.'Mva'] * ($vat / 100);
        }
    }

    ##############################################################
    #If�lge innsendte oppgaver
    function reported($args)
    {
        $avstemmingLineQuery = "select * from mvaavstemmingline where MvaAvstemmingID=".$this->MvaAvstemmingID." order by Period";
        $this->insertmissinglines($args);
        $result_mva = $this->_dbh[$this->_dsn]->db_query($avstemmingLineQuery);
        $period = 0;

        while($row = $this->_dbh[$this->_dsn]->db_fetch_object($result_mva))
        {
            $avstemmingLineFieldQuery = "select * from mvaavstemminglinefield where MvaAvstemmingLineID=".$row->MvaAvstemmingLineID;
            $lineFields = $this->_dbh[$this->_dsn]->get_hashhash(array('query'=>$avstemmingLineFieldQuery, 'key'=>'Name'));
            //print_r($lineFields);
            $period++;

            #Monthly sum
            $this->reported[$period]['TotalOmsettning']     = $row->TotalOmsettning;
            $this->reported[$period]['FreeOmsettning']      = $row->FreeOmsettning;

            $tmpSumOut = 0;
            $tmpSumIn = 0;
            foreach($this->vathash as $vat => $tmp)
            {

                $namegrl    = 'Grunnlag'.$vat.'Mva';
                $nameout    = 'Out'.$vat.'Mva';
                $namein     = 'In'.$vat.'Mva';

                $this->reported[$period][$namegrl]  = $lineFields[$namegrl]['Value'];
                $this->reported[$period][$nameout]  = $lineFields[$nameout]['Value'];
                $this->reported[$period][$namein]   = $lineFields[$namein]['Value'];

                $tmpSumOut += $lineFields[$nameout]['Value'];
                $tmpSumIn  += $lineFields[$namein]['Value'];

                $this->reported[$period]['LineFieldID'][$namegrl] = $lineFields[$namegrl]['MvaAvstemmingLineFieldID'];
                $this->reported[$period]['LineFieldID'][$nameout] = $lineFields[$nameout]['MvaAvstemmingLineFieldID'];
                $this->reported[$period]['LineFieldID'][$namein]  = $lineFields[$namein]['MvaAvstemmingLineFieldID'];
            }
            $this->reported[$period]['LineID']              = $row->MvaAvstemmingLineID;
            $this->reported[$period][$this->undefinedVatAccountPlanID]                = 0;
            $this->reported[$period]['SumMva']              = ($tmpSumOut + $tmpSumIn);
            #Her
            #$this->reported[$period]['SumMva']              = 99999;

            #Calculate yearly sum
            $this->reported['total']['TotalOmsettning']     += $row->TotalOmsettning;
            $this->reported['total']['FreeOmsettning']      += $row->FreeOmsettning;

            foreach($this->_inAccountPlanID as $vat => $account)
            {
                $this->reported['total']['Grunnlag'.$vat.'Mva'] += $lineFields['Grunnlag'.$vat.'Mva']['Value'];
                $this->reported['total']['Out'.$vat.'Mva']      += $lineFields['Out'.$vat.'Mva']['Value'];
                $this->reported['total']['In'.$vat.'Mva']       += $lineFields['In'.$vat.'Mva']['Value'];
            }
            $this->reported['total'][$this->undefinedVatAccountPlanID]    = 0;
            $this->reported['total']['SumMva'] += $this->reported[$period]['SumMva'];
        }

        $this->ReportedPeriod1 = $this->reported[1]['SumMva']  + $this->reported[2]['SumMva'];
        $this->ReportedPeriod2 = $this->reported[3]['SumMva']  + $this->reported[4]['SumMva'];
        $this->ReportedPeriod3 = $this->reported[5]['SumMva']  + $this->reported[6]['SumMva'];
        $this->ReportedPeriod4 = $this->reported[7]['SumMva']  + $this->reported[8]['SumMva'];
        $this->ReportedPeriod5 = $this->reported[9]['SumMva']  + $this->reported[10]['SumMva'];
        $this->ReportedPeriod6 = $this->reported[11]['SumMva'] + $this->reported[12]['SumMva'];

        $this->between = $this->ReportedPeriod1 + $this->avstemmingRow->Period1Payed;
        $this->between += $this->ReportedPeriod2 + $this->avstemmingRow->Period2Payed;
        $this->between += $this->ReportedPeriod3 + $this->avstemmingRow->Period3Payed;
        $this->between += $this->ReportedPeriod4 + $this->avstemmingRow->Period4Payed;
        $this->between += $this->ReportedPeriod5 + $this->avstemmingRow->Period5Payed;
        $this->between += $this->ReportedPeriod6 + $this->avstemmingRow->Period6Payed;

        foreach($this->_inAccountPlanID as $vat => $account)
        {
            $this->reported['percent']['Grunnlag'.$vat.'Mva']  = $this->reported['total']['Grunnlag'.$vat.'Mva'] * ($vat / 100);
        }
    }

    ##############################################################
    #FUnction that assures that all mvaavstemminglinefield for all the different VATs is in place

    function fix() {
      $avstemmingLineQuery = "select * from mvaavstemmingline where MvaAvstemmingID=".$this->MvaAvstemmingID." order by Period";
      $result_mva = $this->_dbh[$this->_dsn]->db_query($avstemmingLineQuery);
      $period = 0;

      while($row = $this->_dbh[$this->_dsn]->db_fetch_object($result_mva))
      {
        $avstemmingLineFieldQuery = "select * from mvaavstemminglinefield where MvaAvstemmingLineID=".$row->MvaAvstemmingLineID;
        $lineFields = $this->_dbh[$this->_dsn]->get_hashhash(array('query'=>$avstemmingLineFieldQuery, 'key'=>'Name'));
        $period++;

        foreach($this->vathash as $vat => $tmp)
        {

          $namegrl = 'Grunnlag'.$vat.'Mva';
          $nameout = 'Out'.$vat.'Mva';
          $namein = 'In'.$vat.'Mva';

          $this->mvaavstemminglinefield($lineFields, $row->MvaAvstemmingLineID, $namegrl, $vat, $period);
          $this->mvaavstemminglinefield($lineFields, $row->MvaAvstemmingLineID, $nameout, $vat, $period);
          $this->mvaavstemminglinefield($lineFields, $row->MvaAvstemmingLineID, $namein, $vat, $period);
        }
      }
    }

    function mvaavstemminglinefield($lineFields, $id, $name, $percent, $period) {

      if(!isset($lineFields[$name]['Name'])) {
        $fields = array();
        $fields['mvaavstemminglinefield_MvaAvstemmingLineID'] = $id;
        $fields['mvaavstemminglinefield_Name']      = $name;
        $fields['mvaavstemminglinefield_Value']     = '';
        $id_new = $this->_dbh[$this->_dsn]->db_new_hash($fields, 'mvaavstemminglinefield');
        $this->_mvaAvstemmingLineFieldID[$name]     = $this->_dbh[$this->_dsn]->get_hash(array('query' => "select * from mvaavstemminglinefield where MvaAvstemmingLineFieldID=$id_new"));
        $this->reported[$period][$name]             = 0;
      }
    }

    ##############################################################
    #Differanse mellom registrert og rapportert
    function diff($args)
    {
        $this->diff['TotalOmsettning']          = $this->registered['total']['TotalOmsettning']     - $this->reported['total']['TotalOmsettning'];
        $this->diff['FreeOmsettning']           = $this->registered['total']['FreeOmsettning']      - $this->reported['total']['FreeOmsettning'];

        foreach($this->_inAccountPlanID as $vat => $account)
        {
            $this->diff['Out'.$vat.'Mva']       = $this->registered['total']['Out'.$vat.'Mva']      - $this->reported['total']['Out'.$vat.'Mva'];
            $this->diff['In'.$vat.'Mva']        = $this->registered['total']['In'.$vat.'Mva']       - $this->reported['total']['In'.$vat.'Mva'];
            $this->diff['Grunnlag'.$vat.'Mva']  = $this->registered['total']['Grunnlag'.$vat.'Mva'] - $this->reported['total']['Grunnlag'.$vat.'Mva'];
        }
        $this->diff[$this->undefinedVatAccountPlanID]                     = $this->registered['total'][$this->undefinedVatAccountPlanID]                - $this->reported['total'][$this->undefinedVatAccountPlanID];
        $this->diff['SumMva']                   = $this->registered['total']['SumMva']              - $this->reported['total']['SumMva'];

        $this->DiffReportedMva = $this->avstemmingRow->LastYearDiff + $this->diff['SumMva'];

        $this->between = $this->DiffReportedMva;

        // Lagt til for å at Gjeld fra fjordåret skal oppdaterers. Geir 28.11.2005. 
        $this->between  += $this->avstemmingRow->LastYearMva;

        $this->between += $this->ReportedPeriod1 + $this->avstemmingRow->Period1Payed;
        $this->between += $this->ReportedPeriod2 + $this->avstemmingRow->Period2Payed;
        $this->between += $this->ReportedPeriod3 + $this->avstemmingRow->Period3Payed;
        $this->between += $this->ReportedPeriod4 + $this->avstemmingRow->Period4Payed;
        $this->between += $this->ReportedPeriod5 + $this->avstemmingRow->Period5Payed;
        $this->between += $this->ReportedPeriod6 + $this->avstemmingRow->Period6Payed;
    }

##############################################################

    function action_avstemming_new($args)
    {
        $this->_year = $args['year'];

        $this->_post['mvaavstemming_PeriodYear'] = $this->_year;
        $this->_mvaAvstemmingID = $this->_dbh[$this->_dsn]->db_new_hash($this->_post, $args['db_table']);

        for($i=1; $i<=12; $i++)
        {
            $this->_post2['mvaavstemmingline_MvaAvstemmingID'] = $this->_mvaAvstemmingID;
            $this->_post2['mvaavstemmingline_Period'] = $this->_year."-".sprintf("%02d", $i);

            $this->_mvaAvstemmingLineID = $this->_dbh[$this->_dsn]->db_new_hash($this->_post2, $args['db_table2']);

            $query_vat = "select distinct Percent from vat where Percent > 0 and Percent != '' and Percent is not null";
            $result_vat = $this->_dbh[$this->_dsn]->db_query($query_vat);
            while($row = $this->_dbh[$this->_dsn]->db_fetch_object($result_vat))
            {
                $this->_post3['mvaavstemminglinefield_MvaAvstemmingLineID'] = $this->_mvaAvstemmingLineID;

                $this->_post3['mvaavstemminglinefield_Name']  = 'Grunnlag'.$row->Percent.'Mva';
                $this->_post3['mvaavstemminglinefield_Value'] = '';
                $this->_mvaAvstemmingLineFieldID['Grunnlag'.$row->Percent.'Mva'] = $this->_dbh[$this->_dsn]->db_new_hash($this->_post3, $args['db_table3']);

                $this->_post3['mvaavstemminglinefield_Name']  = 'Out'.$row->Percent.'Mva';
                $this->_post3['mvaavstemminglinefield_Value'] = '';
                $this->_mvaAvstemmingLineFieldID['Out'.$row->Percent.'Mva'] = $this->_dbh[$this->_dsn]->db_new_hash($this->_post3, $args['db_table3']);

                $this->_post3['mvaavstemminglinefield_Name']  = 'In'.$row->Percent.'Mva';
                $this->_post3['mvaavstemminglinefield_Value'] = '';
                $this->_mvaAvstemmingLineFieldID['In'.$row->Percent.'Mva'] = $this->_dbh[$this->_dsn]->db_new_hash($this->_post3, $args['db_table3']);
            }
        }


        $undefinedH['mvaavstemminglinefield_Name']  = $this->undefinedVatAccountPlanID;
        $undefinedH['mvaavstemminglinefield_Value'] = '';
        $this->_dbh[$this->_dsn]->db_new_hash($undefinedH, $args['db_table3']);
    }

##############################################################

    function FreeMva($args)
    {

        $query = "select sum(AmountIn) as sumin, sum(AmountOut) as sumout from voucher where VoucherPeriod >= '".$args['FromPeriod']."' and VoucherPeriod <= '".$args['ToPeriod']."' and VatID='30' and Active=1 group by VoucherPeriod"; # and AmountOut > 0
        $this->_row = $this->_dbh[$this->_dsn]->get_row(array('query' => $query));

        return ($this->_row->sumin - $this->_row->sumout);
    }

##############################################################

    function NoMVA($args)
    {

        $query = "select sum(AmountIn) as sumin, sum(AmountOut) as sumout from voucher where VoucherPeriod >= '".$args['FromPeriod']."' and VoucherPeriod <= '".$args['ToPeriod']."' and VatID='32' and Active=1 group by VoucherPeriod"; # and AmountOut > 0
        $this->_row = $this->_dbh[$this->_dsn]->get_row(array('query' => $query));

        return ($this->_row->sumin - $this->_row->sumout);
    }


##############################################################
    # denne har tatt over for grlMva og outMva funksjonene, da det mest sansynlig kan regnes ut riktige verdier for begge to her
    function grloutMva($args)
    {
        if($args['AccountPlanID']) {
            $query = "select sum(c.AmountIn) as sumin, sum(c.AmountOut) as sumout from voucher as c where c.VoucherPeriod >= '".$args['FromPeriod']."' and c.VoucherPeriod <= '".$args['ToPeriod']."' and c.AccountPlanID='".$args['AccountPlanID']."' and c.Vat > 0 and c.Active=1 group by c.VoucherPeriod"; # and AmountOut > 0
        } else {
            $query = "select sum(c.AmountIn) as sumin, sum(c.AmountOut) as sumout from voucher as c where c.VoucherPeriod >= '".$args['FromPeriod']."' and c.VoucherPeriod <= '".$args['ToPeriod']."' and c.Vat='".$args['Vat']."' and c.VatID >= 10 and c.VatID < 30 and c.Active=1 group by c.VoucherPeriod"; # and AmountOut > 0
        }

        //print "GrlOut: $query<br>\n";

        $this->_row = $this->_dbh[$this->_dsn]->get_row(array('query' => $query));

        $retval = array(
            'grl'=>(($this->_row->sumin - $this->_row->sumout) / (($args['Vat'] + 100) / 100)),
            'out'=>(($this->_row->sumin - $this->_row->sumout) - (($this->_row->sumin - $this->_row->sumout) / (($args['Vat'] + 100) / 100)))
        );

        return $retval;
    }

##############################################################
    #byttet ut med grloutMva
    function grlMva($args)
    {
        if($args['AccountPlanID']) {
            $query = "select sum(c.AmountIn) as sumin, sum(c.AmountOut) as sumout from voucher as c where c.VoucherPeriod >= '".$args['FromPeriod']."' and c.VoucherPeriod <= '".$args['ToPeriod']."' and c.AccountPlanID='".$args['AccountPlanID']."' and c.Vat > 0  and c.Active=1 group by c.VoucherPeriod"; # and AmountOut > 0
        } else {
            $query = "select sum(c.AmountIn) as sumin, sum(c.AmountOut) as sumout from voucher as c where c.VoucherPeriod >= '".$args['FromPeriod']."' and c.VoucherPeriod <= '".$args['ToPeriod']."' and c.Vat='".$args['Vat']."' and c.VatID >= 10 and c.VatID < 30  and c.Active=1 group by c.VoucherPeriod"; # and AmountOut > 0
        }

        #print "GRL: $query<br>\n";

        $this->_row = $this->_dbh[$this->_dsn]->get_row(array('query' => $query));

        return ($this->_row->sumin - $this->_row->sumout)/(($args['Vat'] + 100) / 100); #1.06
    }

##############################################################

    function inMva($args)
    {
        if($args['AccountPlanID']) {
            $query = "select sum(c.AmountIn) as sumin, sum(c.AmountOut) as sumout from voucher as c where c.VoucherPeriod >= '".$args['FromPeriod']."' and c.VoucherPeriod <= '".$args['ToPeriod']."' and c.AccountPlanID='".$args['AccountPlanID']."' and c.Vat > 0  and c.Active=1 group by c.VoucherPeriod"; # and AmountOut > 0
        } else {
            $query = "select sum(c.AmountIn) as sumin, sum(c.AmountOut) as sumout from voucher as c, voucher as m where c.VoucherPeriod >= '".$args['FromPeriod']."' and c.VoucherPeriod <= '".$args['ToPeriod']."' and c.AccountPlanID='".$this->_inAccountPlanID[$args['Vat']]."' and m.AutomaticVatVoucherID=c.VoucherID and m.Vat='".$args['Vat']."' and c.Active=1 group by c.VoucherPeriod"; # and AmountIn > 0
        }
        #print "$query<br>\n";
        $this->_row = $this->_dbh[$this->_dsn]->get_row(array('query' => $query));

        return ($this->_row->sumin - $this->_row->sumout);
    }

##############################################################
    #byttet ut med grloutMva
    function outMva($args)
    {
        //print_r($args);
        //print ':'.($args['AccountPlanID']).';';
        if($args['AccountPlanID']) {
            $query = "select sum(c.AmountIn) as sumin, sum(c.AmountOut) as sumout from voucher as c where c.VoucherPeriod >= '".$args['FromPeriod']."' and c.VoucherPeriod <= '".$args['ToPeriod']."' and c.AccountPlanID='".$args['AccountPlanID']."' and c.Vat > 0 and c.Active=1  group by c.VoucherPeriod"; # and AmountOut > 0
        } else {
            $query = "select sum(c.AmountIn) as sumin, sum(c.AmountOut) as sumout from voucher as c, voucher as m where c.VoucherPeriod >= '".$args['FromPeriod']."' and c.VoucherPeriod <= '".$args['ToPeriod']."' and c.AccountPlanID='".$this->_outAccountPlanID[$args['Vat']]."' and m.AutomaticVatVoucherID=c.VoucherID and m.Vat='".$args['Vat']."' and c.Active=1 group by c.VoucherPeriod"; # and AmountOut > 0
        }
        #print "UTG: $query<br>\n";

        $this->_row = $this->_dbh[$this->_dsn]->get_row(array('query' => $this->_query));

        return ($this->_row->sumin - $this->_row->sumout);
    }

##############################################################

    function Account($args)
    {
       $this->_query = "select sum(AmountIn) as sumin, sum(AmountOut) as sumout from voucher where VoucherPeriod >= '".$args['FromPeriod']."' and VoucherPeriod <= '".$args['ToPeriod']."' and AccountPlanID = '".$args['AccountPlanID']."' and Active=1 group by VoucherPeriod"; # and AmountOut > 0
       #print "$this->_query<br>";
       $this->_row = $this->_dbh[$this->_dsn]->get_row(array('query' => $this->_query));

        return ($this->_row->sumin - $this->_row->sumout);
    }
    
    function UndefinedVatAccount($args)
    {
       $this->_query = "select sum(AmountIn) as sumin, sum(AmountOut) as sumout from voucher where VoucherPeriod >= '".$args['FromPeriod']."' and VoucherPeriod <= '".$args['ToPeriod']."' and AccountPlanID = '".$args['AccountPlanID']."' and Active=1 and (AutomaticVatVoucherID is null or AutomaticVatVoucherID = 0) group by VoucherPeriod"; # and AmountOut > 0
       $this->_query = "select sum(AmountIn) as sumin, sum(AmountOut) as sumout from voucher where VoucherPeriod >= '".$args['FromPeriod']."' and VoucherPeriod <= '".$args['ToPeriod']."' and AccountPlanID = '".$args['AccountPlanID']."' and Active=1 group by VoucherPeriod"; # and AmountOut > 0

       #print "$this->_query<br>";
       $this->_row = $this->_dbh[$this->_dsn]->get_row(array('query' => $this->_query));

        return ($this->_row->sumin - $this->_row->sumout);
    }
    

    function insertmissinglines($args) {
      $avstemmingLineQuery  = "select * from mvaavstemmingline where MvaAvstemmingID=".$this->MvaAvstemmingID." order by Period";
      //print "$avstemmingLineQuery<br>";

      #$lineFields          = $this->_dbh[$this->_dsn]->get_hashhash(array('query'=>$avstemmingLineFieldQuery, 'key'=>'Name'));


    }
}
?>
