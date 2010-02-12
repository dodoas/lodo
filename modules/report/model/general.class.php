<?
includealogic('selvangivelsenaeringsdrivendekalk');
includealogic('selvangivelseaksjeselskapkalk');
includealogic('naeringsoppgave2kalk');
includealogic('naeringsoppgave1kalk');
includealogic('personinntektkalk');
includealogic('avskrivningerkalk');
includealogic('bilbruksopplysningerkalk');
includealogic('forskjellerregnskapogskattekalk');
includealogic('egenkapitalavstemmingkalk');
includealogic('tilleggsskjema1kalk');
includealogic('tilleggsskjema2kalk');
includealogic('gevinstogtapskontokalk');
includealogic('riskberegningkalk');
includealogic('selskapsoppgavekalk');
includealogic('deltakerensoppgavekalk');
includealogic('terminoppgavekalk');
includealogic('momssoppgavekalk');
includealogic('realisasjonavaksjeoppgavekalk');
includealogic('registrerteoginnberettedekalk');
includealogic('aksjonaerregisteroppgavekalk');
includealogic('spekavinntektogfradragutlandetkalk');
includealogic('lonnogtrekkoppgavekalk');
includealogic('aarsoppgavekalk');
includealogic('oppstillingsplansmastoreselskaperkalk');


################################################################################
class GeneralReport
{
    private $_report;
    private $_reportResultHash          = array();
    private $_reportResultHashLastYear  = array();
    private $_reportBalanceHash         = array();
    private $_reportBalanceHashLastYear = array();
    private $_sumLine                   = array();
    private $_sumPart                   = array();
    private $_sumGroup                  = array();
    private $_sumTotal                  = array();
    private $_Total                     = 0;
    private $_altinnMapping;

    private $_tmpSum = 0;

    private $_fromPeriod;
    private $_toPeriod;
    private $_enableLastYear;
    
    /* General reports */
    private $_OFFISIELTREGNSKAP                 = 1;
    private $_SELVANGIVELSENAERINGSDRIVENDE     = 2;
    private $_NAERINGSOPPGAVE1                  = 3;
    private $_SELVANGIVELDEAKSJESELSKAP         = 4;
    private $_NAERINGSOPPGAVE2                  = 5;
    private $_GENERAL_REPORT_6                  ='6';
    private $_GENERAL_REPORT_7                  ='7';
    private $_GENERAL_REPORT_8                  ='8';
    private $_GENERAL_REPORT_9                  ='9';
    private $_GENERAL_REPORT_10                 ='10';

    private $_MOMSOPPGAVE                       = '0002';  
    private $_AVSKRIVNINGER                     = '1084';
    //var $_PERSONINNTEKT1                  = 66;
    //var $_PERSONINNTEKT2                  = 6;
    private $_PERSONINNTEKT1                    ='1224';
    private $_PERSONINNTEKT2                    ='2224';
    private $_BILBRUKSOPPLYSNINGER              ='1125';
    private $_FORSKJELLERREGNSKAPOGSKATTE       ='1217';
    private $_TILLEGGSSKJEMA1                   ='1122';
    private $_EGENKAPITALAVSTEMMING             ='1052';
    private $_TILLEGGSSKJEMA2                   ='1223';
    private $_GEVINSTOGTAPSKONTO                ='1219';
    private $_REALISASJONAVAKSJEOPPGAVE         ='1061';
    private $_BEREGNINGAVRISK                   ='1239';
    private $_SELSKAPSOPPGAVE                   ='1215';
    private $_DELTAKERENSOPPGAVE                ='1221';
    private $_TERMINOPPGAVE                     ='1037';
    private $_AARSOPPGAVE                       ='1025';
    private $_SPEKAVINNTEKTOGFRADRAGUTLANDET    ='1231';
    private $_REGISTRERTEOGINNBERETTEDE         ='1022';
    private $_AKSJONAERREGISTEROPPGAVE          ='1086';
    
    private $_companyType                       ='AS';
    
    private $SelvangivelseNrKalk                =null;
    private $SelvangivelseASKalk                =null;
    private $NaeringsOppgave1Kalk               =null;
    private $NaeringsOppgave2Kalk               =null;
    private $PersonInntektKalk                  =null;
    private $AvskrivningerKalk                  =null;
    private $BilbruksOpplysningerKalk           =null;
    private $ForskjellerRegnskapOgSkatteKalk    =null;
    private $Tilleggsskjema1Kalk                =null;
    private $EgenkapitalAvstemmingKalk          =null;
    private $Tilleggsskjema2Kalk                =null;
    private $GevinstOgTapskontoKalk             =null;
    private $AksjeRealisasjonsKalk              =null;
    private $RISKBeregningsKalk                 =null;
    private $SelskapOppgaveKalk                 =null;
    private $DeltakerensOppgaveKalk             =null;
    private $TerminOppgaveKalk                  =null;
    private $AArgsoppgaveKalk                   =null;
    private $InntektOgFradragUtlandetKalk       =null;
    private $RegistrerteOgInnberettedeKalk      =null;
    private $AksjonaerRegisterKalk              =null;
    
    

    ############################################################################
    function __construct($args)
    {
        if(isset($args['fromPeriod']))
            $this->_fromPeriod = $args['fromPeriod'];
        if(isset($args['toPeriod']))
            $this->_toPeriod = $args['toPeriod'];
        if(isset($args['enableLastYear']))
            $this->_enableLastYear = $args['enableLastYear'];
        if(isset($args['report']))
            $this->_report = $args['report'];
            
        //print ("Report : " . $args['report']);
        
        //The arguments input from the schema_general_class.class
        if (!$args['company_type'] || $args['company_type']<=0)
        	$this->companyType='';
        	
        
            
        
    }

    ############################################################################
    function BuildReport()
    {
        global $_lib;

        $ThisYear = $_lib['date']->get_this_year($this->_toPeriod);
        #print "Dette: $Year ($this->_toPeriod)<br>";
        
        switch ($this->_report) {
            case $this->_GENERAL_REPORT_6;
            case $this->_GENERAL_REPORT_7;
            case $this->_GENERAL_REPORT_8;
            case $this->_GENERAL_REPORT_9;
            case $this->_GENERAL_REPORT_10;
            case $this->_OFFISIELTREGNSKAP;
        	case $this->_NAERINGSOPPGAVE1:
        	case $this->_NAERINGSOPPGAVE2:
        	case $this->_SELVANGIVELSENAERINGSDRIVENDE:
        	case $this->_SELVANGIVELDEAKSJESELSKAP:
        	case $this->_PERSONINNTEKT1:
        	case $this->_PERSONINNTEKT2:
        	    //print("Anh this year: ".$this->_report);
		        $this->result($this->_fromPeriod, $this->_toPeriod, $ThisYear);
		        $this->balance($this->_toPeriod, $ThisYear);
		
		        $this->HeadLogic($ThisYear); #Special calculations
		
		        if($this->_enableLastYear == 1)
		        {
		            #For report 3 and 5
		            $PrevYear = $_lib['date']->get_this_year($_lib['date']->get_this_period_last_year($this->_toPeriod));
		            
		            #print "Forrige: $PrevYear<br>";
		            $this->result($_lib['date']->get_this_period_last_year($this->_fromPeriod), $_lib['date']->get_this_period_last_year($this->_toPeriod), $PrevYear);
		            $this->balance($_lib['date']->get_this_period_last_year($this->_toPeriod), $PrevYear);
		            $this->HeadLogic($PrevYear); #Special calculations
		            
		            //Special calculation for the personinntekt report
		            if ($this->_report == $this->_PERSONINNTEKT1 || $this->_report == $this->_PERSONINNTEKT2) {
		            	$this->_sumPart['2.10'][$ThisYear]['saldo']=$this->_sumPart['2.9'][$ThisYear]['saldo']+
		            	                                            $this->_sumPart['2.9'][$PrevYear]['saldo'];
		            	                                            
		                $this->_sumPart['3'][$ThisYear]['saldo']=$this->_sumGroup['1.21'][$ThisYear]['saldo']-
		                											$this->_sumPart['2.12'][$ThisYear]['saldo'];
		            }
		     
		        }//if $this->_enableLastYear == 1
		     break;
		     default: break;
        }//switch 
    }

    ############################################################################
    function HeadLogic($ThisYear)
    {
        global $_sess, $_lib;

        //print "Year: " . $ThisYear . ": report: " . $this->_report . ": companytype: " . $this->_companyType . "<br>";

        switch ($this->_report) {
        	case $this->_NAERINGSOPPGAVE1:
        		//Start the calculation of the 'N�ringsoppgaver1' report 
        		if ($this->NaeringsOppgave1Kalk==null)
        			$this->NaeringsOppgave1Kalk = new NaeringsOppgave1Kalk(array());
        		
        		$this->NaeringsOppgave1Kalk->calculate($ThisYear, $this->_sumPart, $this->_sumGroup);
        	
        		//Get the result after the calculations
        		$this->_sumPart=$this->NaeringsOppgave1Kalk->getSumPart();
        		$this->_sumGroup=$this->NaeringsOppgave1Kalk->getSumGroup();
        	     break;
        	case $this->_NAERINGSOPPGAVE2:
        		//Start the calculation of the 'N�ringsoppgaver2' report 
        		if ($this->NaeringsOppgave2Kalk==null)
        			$this->NaeringsOppgave2Kalk = new NaeringsOppgave2Kalk(array());
        		$this->NaeringsOppgave2Kalk->calculate($ThisYear, $this->_sumPart, $this->_sumGroup);
        		$this->_sumPart=$this->NaeringsOppgave2Kalk->getSumPart();
        		$this->_sumGroup=$this->NaeringsOppgave2Kalk->getSumGroup();
        	     break;
        	case $this->_SELVANGIVELSENAERINGSDRIVENDE:
        			//Start the calculation of the 'selvangivelse for aksjeselskap' report 
        		if ($this->SelvangivelseNrKalk==null)
	    			$this->SelvangivelseNrKalk= new SelvangivelseNrKalk(array());
	    		$this->SelvangivelseNrKalk->calculate($ThisYear, $this->_sumPart, $this->_sumGroup);
	    	
	    		//Get the result after the calculations
	    		$this->_sumPart=$this->SelvangivelseNrKalk->getSumPart();
	    		$this->_sumGroup=$this->SelvangivelseNrKalk->getSumGroup();
        	  break;
        	case $this->_SELVANGIVELDEAKSJESELSKAP:
        	    //Start the calculation of the 'selvangivelse for aksjeselskap' report 
            	if ($this->SelvangivelseASKalk==null)
	    			$this->SelvangivelseASKalk= new SelvangivelseASKalk(array());
	    		
	    		$this->SelvangivelseASKalk->calculate($ThisYear, $this->_sumPart, $this->_sumGroup, $_lib['sess']->get_companydef('ShareNumber'));
	    	
	    			//Get the result after the calculations
	    		$this->_sumPart=$this->SelvangivelseASKalk->getSumPart();
	    		$this->_sumGroup=$this->SelvangivelseASKalk->getSumGroup();
        	     break;
        	case $this->_PERSONINNTEKT1:
        		//Start the calculation of the 'N�ringsoppgaver1' report 
        		if ($this->NaeringsOppgave1Kalk==null)
        			$this->NaeringsOppgave1Kalk = new NaeringsOppgave1Kalk(array());
        		
        		$this->NaeringsOppgave1Kalk->calculate($ThisYear, $this->_sumPart, $this->_sumGroup);
        	
        		//Get the result after the calculations
        		$this->_sumPart=$this->NaeringsOppgave1Kalk->getSumPart();
        		$this->_sumGroup=$this->NaeringsOppgave1Kalk->getSumGroup();
            
            	//If this is a personinntekt report then start the .
            	//Start the calculation of the 'PersonInntekt' report 
            	if ($this->PersonInntektKalk==null)
	        		$this->PersonInntektKalk = new PersonInntektKalk(array());
	        	$this->PersonInntektKalk->calculateNr($ThisYear, $this->_sumPart, $this->_sumGroup);
	        	
	        	//Get the result after the calculations
	        	$this->_sumPart=$this->PersonInntektKalk->getSumPart();
	        	$this->_sumGroup=$this->PersonInntektKalk->getSumGroup();
            
        	     break;
        	case $this->_PERSONINNTEKT2:
        		//Start the calculation of the 'N�ringsoppgaver2' report 
        		if ($this->NaeringsOppgave2Kalk==null)
        			$this->NaeringsOppgave2Kalk = new NaeringsOppgave2Kalk(array());
        		$this->NaeringsOppgave2Kalk->calculate($ThisYear, $this->_sumPart, $this->_sumGroup);
        		$this->_sumPart=$this->NaeringsOppgave2Kalk->getSumPart();
        		$this->_sumGroup=$this->NaeringsOppgave2Kalk->getSumGroup();
        	
        		//Get the result after the calculations
            
             	//If this is a personinntekt report then start the ..
            	//if ($this->_report == $this->_PERSONINNTEKT2) {
            		//Start the calculation of the 'PersonInntekt' report 
            		if ($this->PersonInntektKalk==null)
	        			$this->PersonInntektKalk = new PersonInntektKalk(array());
	        		$this->PersonInntektKalk->calculateAS($ThisYear, $this->_sumPart, $this->_sumGroup);
	        	
	        		//Get the result after the calculations
	        		$this->_sumPart=$this->PersonInntektKalk->getSumPart();
	        		$this->_sumGroup=$this->PersonInntektKalk->getSumGroup();
            	//}//if report==personinntekt
        	     break;
        	case $this->_AVSKRIVNINGER:
        		//Start the calculation of the 'avskrivninger' report 
            	if ($this->AvskrivningerKalk==null)
	    			$this->AvskrivningerKalk= new AvskrivningerKalk(array());
	    		
	    		$this->AvskrivningerKalk->calculate($ThisYear, $this->_sumPart, $this->_sumGroup, $_lib['sess']->get_companydef('ShareNumber'));
	    	
	    		//Get the result after the calculations
	    		$this->_sumPart=$this->AvskrivningerKalk->getSumPart();
	    		$this->_sumGroup=$this->AvskrivningerKalk->getSumGroup();
        	    break;
        	case $this->_BILBRUKSOPPLYSNINGER:
        		//Start the calculation of the 'bilbruksopplysninger' report 
            	if ($this->BilbruksOpplysningerKalk==null)
	    			$this->BilbruksOpplysningerKalk= new BilbruksOpplysningerKalk(array());
	    		
	    		$this->BilbruksOpplysningerKalk->calculate($ThisYear, $this->_sumPart, $this->_sumGroup, $_lib['sess']->get_companydef('ShareNumber'));
	    	
	    		//Get the result after the calculations
	    		$this->_sumPart=$this->BilbruksOpplysningerKalk->getSumPart();
	    		$this->_sumGroup=$this->BilbruksOpplysningerKalk->getSumGroup();
        	     break;
        	case $this->_FORSKJELLERREGNSKAPOGSKATTE:
        		//Start the calculation of the 'forskjeller mellom regnskap og skatte' report 
            	if ($this->ForskjellerRegnskapOgSkatteKalk==null)
	    			$this->ForskjellerRegnskapOgSkatteKalk= new ForskjellerRegnskapOgSkatteKalk(array());
	    		
	    		$this->ForskjellerRegnskapOgSkatteKalk->calculate($ThisYear, $this->_sumPart, $this->_sumGroup, $_lib['sess']->get_companydef('ShareNumber'));
	    	
	    		//Get the result after the calculations
	    		$this->_sumPart=$this->ForskjellerRegnskapOgSkatteKalk->getSumPart();
	    		$this->_sumGroup=$this->ForskjellerRegnskapOgSkatteKalk->getSumGroup();
        	     break;
        	case $this->_TILLEGGSSKJEMA1:
        		//Start the calculation of the 'forskjeller mellom regnskap og skatte' report 
            	if ($this->Tilleggsskjema1Kalk==null)
	    			$this->Tilleggsskjema1Kalk= new Tilleggsskjema1Kalk(array());
	    		
	    		$this->Tilleggsskjema1Kalk->calculate($ThisYear, $this->_sumPart, $this->_sumGroup, $_lib['sess']->get_companydef('ShareNumber'));
	    	
	    		//Get the result after the calculations
	    		$this->_sumPart=$this->Tilleggsskjema1Kalk->getSumPart();
	    		$this->_sumGroup=$this->Tilleggsskjema1Kalk->getSumGroup();
        	     break;
        	case $this->_TILLEGGSSKJEMA2:
        	     break;
        	case $this->_EGENKAPITALAVSTEMMING:
        	     //Start the calculation of the 'N�ringsoppgaver2' report 
        		if ($this->NaeringsOppgave2Kalk==null)
        			$this->NaeringsOppgave2Kalk = new NaeringsOppgave2Kalk(array());
        		$this->NaeringsOppgave2Kalk->calculate($ThisYear, $this->_sumPart, $this->_sumGroup);
        		$this->_sumPart=$this->NaeringsOppgave2Kalk->getSumPart();
        		$this->_sumGroup=$this->NaeringsOppgave2Kalk->getSumGroup();
        	
        		//Get the result after the calculations
            
             	//If this is a personinntekt report then start the ..
            	
            		//Start the calculation of the 'egenkapitalavstemming' report 
            	if ($this->EgenkapitalAvstemmingKalk==null)
	        		$this->EgenkapitalAvstemmingKalk = new EgenkapitalAvstemmingKalk(array());
	        		
	            $this->EgenkapitalAvstemmingKalk->calculate($ThisYear, $this->_sumPart, $this->_sumGroup);
	        	
	            //Get the result after the calculations
	        	$this->_sumPart=$this->EgenkapitalAvstemmingKalk->getSumPart();
	        	$this->_sumGroup=$this->EgenkapitalAvstemmingKalk->getSumGroup();
                 break;
            case $this->_TILLEGGSSKJEMA2:
                 //Start the calculation of the 'forskjeller mellom regnskap og skatte' report 
            	if ($this->Tilleggsskjema2Kalk==null)
	    			$this->Tilleggsskjema2Kalk= new Tilleggsskjema2Kalk(array());
	    		
	    		$this->Tilleggsskjema2Kalk->calculate($ThisYear, $this->_sumPart, $this->_sumGroup, $_lib['sess']->get_companydef('ShareNumber'));
	    	
	    		//Get the result after the calculations
	    		$this->_sumPart=$this->Tilleggsskjema2Kalk->getSumPart();
	    		$this->_sumGroup=$this->Tilleggsskjema2Kalk->getSumGroup();
                 break;
            case $this->_GEVINSTOGTAPSKONTO:
                 break;
            case $this->_REALISASJONAVAKSJEOPPGAVE:
                 break;
           	case $this->_BEREGNINGAVRISK:
                 break;
           	case $this->_SELSKAPSOPPGAVE:
                 break;
           	case $this->_DELTAKERENSOPPGAVE:
                 break;
            case $this->_TERMINOPPGAVE:   
                 break;
            case $this->_AARSOPPGAVE:
                 break;
            case $this->_SPEKAVINNTEKTOGFRADRAGUTLANDET:
                 break;
            case $this->_REGISTRERTEOGINNBERETTEDE:
                 break;
            case $this->_AKSJONAERREGISTEROPPGAVE:
                 break;
            default: break;
        	  	  
        }
        
      
    }//function HeadLogic


    ############################################################################
    function result($FromPeriod, $ToPeriod, $Year)
    {
        global $_sess, $_dbh, $_dsn, $_date, $_lib;

        $query_report_resultat  = "
            select
                A.AccountName,
                A.AccountPlanID,
                A.Report{$this->_report}Line as Line,
                sum(AmountIn) as AmountIn, sum(AmountOut) as AmountOut
            from
                voucher as V,
                accountplan as A
            where
                V.VoucherPeriod >= '$FromPeriod' and
                V.VoucherPeriod <= '$ToPeriod' and
                A.AccountplanType='result' and
                A.AccountPlanID=V.AccountPlanID and
                EnableReport{$this->_report} > 0 and
                V.Active = 1
            group by A.AccountPlanID
            order by Line asc, A.AccountPlanID asc
        ";

        #A.Report{$this->_report}Line >= 3000 and
        #A.Report{$this->_report}Line < 8700 and

        #print "<h2>Result Year: $Year</h2>$query_report_resultat<br>";
        $this->_reportResultHash = $_lib['storage']->get_hashhash(array('query'=>$query_report_resultat, 'key'=>'AccountPlanID'));

        $oldLine = -1;
        foreach($this->_reportResultHash as $AccountPlanID => $reportHash)
        {
            if($oldLine != -1 and $oldLine != $reportHash['Line'])
            {
                $oldLine = $reportHash['Line'];
                if(!isset($this->_sumPart[$oldLine][$Year]['saldo']))
                    $this->_sumPart[$oldLine][$Year]['saldo'] = 0;

                if(!isset($this->_sumPart[$oldLine][$Year]['in']))
                    $this->_sumPart[$oldLine][$Year]['in'] = 0;

                if(!isset($this->_sumPart[$oldLine][$Year]['out']))
                    $this->_sumPart[$oldLine][$Year]['out'] = 0;
            }
            if($oldLine == -1)
            {
                $oldLine = $reportHash['Line'];
            }

            if(strlen($reportHash['AccountName']) > 0)
            {
                $this->_sumLine[$oldLine][$AccountPlanID]['name']       =  $reportHash['AccountName'];
            }
            $this->_sumLine[$oldLine][$AccountPlanID][$Year]['in']      =  $reportHash['AmountIn'];
            $this->_sumLine[$oldLine][$AccountPlanID][$Year]['out']     =  $reportHash['AmountOut'];
            $this->_sumLine[$oldLine][$AccountPlanID][$Year]['saldo']   =  $reportHash['AmountIn'] - $reportHash['AmountOut'];
            $this->_sumPart[$oldLine][$Year]['in']                      += $reportHash['AmountIn'];
            $this->_sumPart[$oldLine][$Year]['out']                     += $reportHash['AmountOut'];
            $this->_sumPart[$oldLine][$Year]['saldo']                   += $reportHash['AmountIn'] - $reportHash['AmountOut'];
            $this->_sumTotal[$Year]['in']                               += $reportHash['AmountIn'];
            $this->_sumTotal[$Year]['out']                              += $reportHash['AmountOut'];
            $this->_sumTotal[$Year]['saldo']                            += $reportHash['AmountIn'] - $reportHash['AmountOut'];
            
            $this->sumGroupLogic_($oldLine, $Year, $reportHash);
        	
            //$this->sumGroupLogic($oldLine, $Year, $reportHash);
            
           
        }//foreach
        
    }//function result

    ############################################################################
    function balance($ToPeriod, $Year)
    {
        global $_sess, $_dbh, $_dsn, $_date, $_lib;

        $year = $_lib['date']->get_this_year($ToPeriod);

        $query_report_balanse  = "
        select
          A.AccountName,
          A.AccountPlanID,
          A.Report{$this->_report}Line as Line,
          sum(AmountIn) as AmountIn,
          sum(AmountOut) as AmountOut
        from
          voucher as V,
          accountplan as A
        where
          V.VoucherPeriod <= '$ToPeriod' and
          V.Active=1 and
          A.Active=1 and
          A.AccountPlanType='balance' and
          A.AccountPlanID=V.AccountPlanID and
          EnableReport{$this->_report} > 0 and
          V.Active = 1
        group by A.AccountPlanID
        order by Line asc, A.AccountPlanID asc
        ";

        #A.Report{$this->_report}Line >= 1000 and
        #A.Report{$this->_report}Line < 3000 and

        #print "<h2>Balanse: $Year</h2>$query_report_balanse<br>";
        $this->_reportBalanceHash = $_lib['storage']->get_hashhash(array('query'=>$query_report_balanse, 'key'=>'AccountPlanID'));

        $oldLine = -1;
        foreach($this->_reportBalanceHash as $AccountPlanID => $reportHash)
        {
            if($oldLine != -1 and $oldLine != $reportHash['Line'])
            {
                $oldLine = $reportHash['Line'];
                if(!isset($this->_sumPart[$oldLine][$Year]['saldo']))
                    $this->_sumPart[$oldLine][$Year]['saldo'] = 0;

                if(!isset($this->_sumPart[$oldLine][$Year]['in']))
                    $this->_sumPart[$oldLine][$Year]['in'] = 0;

                if(!isset($this->_sumPart[$oldLine][$Year]['out']))
                    $this->_sumPart[$oldLine][$Year]['out'] = 0;
            }
            if($oldLine == -1)
            {
                $oldLine = $reportHash['Line'];
            }

            if(strlen($reportHash['AccountName']) > 0)
            {
                $this->_sumLine[$oldLine][$AccountPlanID]['name']       =  $reportHash['AccountName'];
            }
            $this->_sumLine[$oldLine][$AccountPlanID][$Year]['in']      =  $reportHash['AmountIn'];
            $this->_sumLine[$oldLine][$AccountPlanID][$Year]['out']     =  $reportHash['AmountOut'];
            $this->_sumLine[$oldLine][$AccountPlanID][$Year]['saldo']   =  $reportHash['AmountIn'] - $reportHash['AmountOut'];
            $this->_sumPart[$oldLine][$Year]['in']                      += $reportHash['AmountIn'];
            $this->_sumPart[$oldLine][$Year]['out']                     += $reportHash['AmountOut'];
            $this->_sumPart[$oldLine][$Year]['saldo']                   += $reportHash['AmountIn'] - $reportHash['AmountOut'];
            $this->_sumTotal[$Year]['in']                               += $reportHash['AmountIn'];
            $this->_sumTotal[$Year]['out']                              += $reportHash['AmountOut'];
            $this->_sumTotal[$Year]['saldo']                            += $reportHash['AmountIn'] - $reportHash['AmountOut'];

             $this->sumGroupLogic_($oldLine, $Year, $reportHash);
          
            //$this->sumGroupLogic($oldLine, $Year, $reportHash);
        }

        $this->_Total = $this->_sumTotal[$Year]['saldo'];
    }//function balance
    
    ############################################################################
    function getAvskrivningerData($ToPeriod, $Year)
    {
       
    }//getAvskrivninger
    
     ############################################################################
    function getBrukAvBilOpplysningerData($ToPeriod, $Year)
    {	
       
    }//getTerminOppgave
    
    ############################################################################
    function getSelskapOppgaveData($ToPeriod, $Year)
    {
       
    }//getTerminOppgave
    
     ############################################################################
    function getTerminOppgaveData($ToPeriod, $Year)
    {
       
    }//getTerminOppgave

    ############################################################################
    function sumGroup($LineNum, $Year, $reportHash)
    {
        $this->_sumGroup[$LineNum][$Year]['in']     += $reportHash['AmountIn'];
        $this->_sumGroup[$LineNum][$Year]['out']    += $reportHash['AmountOut'];
        $this->_sumGroup[$LineNum][$Year]['saldo']  += $reportHash['AmountIn'] - $reportHash['AmountOut'];
    }
    
    ############################################################################
    function sumGroupLogic_($Line, $Year, $reportHash) {
    	switch ($this->_report){
        	case $this->_SELVANGIVELDEAKSJESELSKAP:
        	     if ($this->SelvangivelseASKalk == null)
        	         $this->SelvangivelseASKalk=new SelvangivelseASKalk(array());  
        		 $this->SelvangivelseASKalk->sumGroupLogic($Line, $Year, $reportHash, $this->_sumGroup);
        		 $this->_sumGroup=$this->SelvangivelseASKalk->getSumGroup();
                 break;
            case $this->_SELVANGIVELSENAERINGSDRIVENDE:
        	     if ($this->SelvangivelseNrKalk == null)
        	         $this->SelvangivelseNrKalk=new SelvangivelseNrKalk(array());
        	         
        		 $this->SelvangivelseNrKalk->sumGroupLogic($Line, $Year, $reportHash, $this->_sumGroup);
        		 $this->_sumGroup =$this->SelvangivelseNrKalk->getSumGroup();
                 break;
            case $this->_NAERINGSOPPGAVE1:
        	     if ($this->NaeringsOppgave1Kalk == null)
        	         $this->NaeringsOppgave1Kalk=new NaeringsOppgave1Kalk(array());
        	         
        		 $this->NaeringsOppgave1Kalk->sumGroupLogic($Line, $Year, $reportHash, $this->_sumGroup);
        		 $this->_sumGroup =$this->NaeringsOppgave1Kalk->getSumGroup();
                 break;
             case $this->_NAERINGSOPPGAVE2:
        	     if ($this->NaeringsOppgave2Kalk == null)
        	         $this->NaeringsOppgave2Kalk=new NaeringsOppgave2Kalk(array());
        	         
        		 $this->NaeringsOppgave2Kalk->sumGroupLogic($Line, $Year, $reportHash, $this->_sumGroup);
        		 $this->_sumGroup =$this->NaeringsOppgave2Kalk->getSumGroup();
        				 
                 break;
             case $this->_PERSONINNTEKT1 :
             	 if ($this->NaeringsOppgave1Kalk == null)
            	 	$this->NaeringsOppgave1Kalk=new NaeringsOppgave1Kalk(array());
            	         
	             $this->NaeringsOppgave1Kalk->sumGroupLogic($Line, $Year, $reportHash, $this->_sumGroup);
	             $this->_sumGroup =$this->NaeringsOppgave1Kalk->getSumGroup();
	             
	             if ($this->PersonInntektKalk == null)
        	         $this->PersonInntektKalk=new PersonInntektKalk(array());
        	         
        		  $this->PersonInntektKalk->sumGroupLogic($Line, $Year, $reportHash, $this->_sumGroup);
        		  $this->_sumGroup =$this->PersonInntektKalk->getSumGroup();
             	 break;
             case $this->_PERSONINNTEKT2 :
             	 
                  if ($this->NaeringsOppgave2Kalk == null)
            	  		$this->NaeringsOppgave2Kalk=new NaeringsOppgave2Kalk(array());
            	      
	              $this->NaeringsOppgave2Kalk->sumGroupLogic($Line, $Year, $reportHash, $this->_sumGroup);
	              
	              $this->_sumGroup =$this->NaeringsOppgave2Kalk->getSumGroup();
                 
                  if ($this->PersonInntektKalk == null)
        	         $this->PersonInntektKalk=new PersonInntektKalk(array());
        	         
        		  $this->PersonInntektKalk->sumGroupLogic($Line, $Year, $reportHash, $this->_sumGroup);
        		  $this->_sumGroup =$this->PersonInntektKalk->getSumGroup();
                 break;
            case $this->_AVSKRIVNINGER:
                  if ($this->AvskrivningerKalk == null)
        	         $this->AvskrivningerKalk=new AvskrivningerKalk(array());
        	         
        		 $this->AvskrivningerKalk->sumGroupLogic($Line, $Year, $reportHash, $this->_sumGroup);
        		 $this->_sumGroup =$this->AvskrivningerKalk->getSumGroup();
                 break;
            case $this->_BILBRUKSOPPLYSNINGER:
                  if ($this->BilbruksOpplysningerKalk == null)
        	         $this->BilbruksOpplysningerKalk=new BilbruksOpplysningerKalk(array());
        	         
        		 $this->BilbruksOpplysningerKalk->sumGroupLogic($Line, $Year, $reportHash, $this->_sumGroup);
        		 $this->_sumGroup =$this->BilbruksOpplysningerKalk->getSumGroup();
                 break;
            case $this->_FORSKJELLERREGNSKAPOGSKATTE:
                  if ($this->ForskjellerRegnskapOgSkatteKalk == null)
        	         $this->ForskjellerRegnskapOgSkatteKalk=new ForskjellerRegnskapOgSkatteKalk(array());
        	         
        		 $this->ForskjellerRegnskapOgSkatteKalk->sumGroupLogic($Line, $Year, $reportHash, $this->_sumGroup);
        		 $this->_sumGroup =$this->ForskjellerRegnskapOgSkatteKalk->getSumGroup();
                 break;
            case $this->_TILLEGGSSKJEMA1:
                  if ($this->Tilleggsskjema1Kalk == null)
        	         $this->Tilleggsskjema1Kalk=new Tilleggsskjema1Kalk(array());
        	         
        		 $this->Tilleggsskjema1Kalk->sumGroupLogic($Line, $Year, $reportHash, $this->_sumGroup);
        		 $this->_sumGroup =$this->Tilleggsskjema1Kalk->getSumGroup();
                 break;
            case $this->_EGENKAPITALAVSTEMMING:
                  if ($this->EgenkapitalAvstemmingKalk == null)
        	         $this->EgenkapitalAvstemmingKalk=new EgenkapitalAvstemmingKalk(array());
        	         
        		 $this->EgenkapitalAvstemmingKalk->sumGroupLogic($Line, $Year, $reportHash, $this->_sumGroup);
        		 $this->_sumGroup =$this->EgenkapitalAvstemmingKalk->getSumGroup();
                 break;
            case $this->_TILLEGGSSKJEMA2:
                  if ($this->Tilleggsskjema2Kalk == null)
        	         $this->Tilleggsskjema2Kalk=new Tilleggsskjema2Kalk(array());
        	         
        		 $this->Tilleggsskjema2Kalk->sumGroupLogic($Line, $Year, $reportHash, $this->_sumGroup);
        		 $this->_sumGroup =$this->Tilleggsskjema2Kalk->getSumGroup();
                 break;
            case $this->_GEVINSTOGTAPSKONTO:
                  if ($this->GevinstOgTapskontoKalk == null)
        	         $this->GevinstOgTapskontoKalk=new GevinstOgTapskontoKalk(array());
        	         
        		 $this->GevinstOgTapskontoKalk->sumGroupLogic($Line, $Year, $reportHash, $this->_sumGroup);
        		 $this->_sumGroup =$this->GevinstOgTapskontoKalk->getSumGroup();
                 break;
            case $this->_REALISASJONAVAKSJEOPPGAVE:
                  if ($this->AksjeRealisasjonsKalk == null)
        	         $this->AksjeRealisasjonsKalk=new AksjeRealisasjonsKalk(array());
        	         
        		 $this->AksjeRealisasjonsKalk->sumGroupLogic($Line, $Year, $reportHash, $this->_sumGroup);
        		 $this->_sumGroup =$this->AksjeRealisasjonsKalk->getSumGroup();
                 break;
           case $this->_BEREGNINGAVRISK:
                  if ($this->RISKBeregningsKalk == null)
        	         $this->RISKBeregningsKalk=new RISKBeregningsKalk(array());
        	         
        		 $this->RISKBeregningsKalk->sumGroupLogic($Line, $Year, $reportHash, $this->_sumGroup);
        		 $this->_sumGroup =$this->RISKBeregningsKalk->getSumGroup();
                 break;
           case $this->_SELSKAPSOPPGAVE:
                  if ($this->SelskapOppgaveKalk == null)
        	         $this->SelskapOppgaveKalk=new SelskapOppgaveKalk(array());
        	         
        		 $this->SelskapOppgaveKalk->sumGroupLogic($Line, $Year, $reportHash, $this->_sumGroup);
        		 $this->_sumGroup =$this->SelskapOppgaveKalk->getSumGroup();
                 break;
           case $this->_DELTAKERENSOPPGAVE:
                  if ($this->DeltakerensOppgaveKalk == null)
        	         $this->DeltakerensOppgaveKalk=new DeltakerensOppgaveKalk(array());
        	         
        		 $this->DeltakerensOppgaveKalk->sumGroupLogic($Line, $Year, $reportHash, $this->_sumGroup);
        		 $this->_sumGroup =$this->DeltakerensOppgaveKalk->getSumGroup();
                 break;
            case $this->_TERMINOPPGAVE:
                  if ($this->TerminOppgaveKalk == null)
        	         $this->TerminOppgaveKalk=new TerminOppgaveKalk(array());
        	         
        		 $this->TerminOppgaveKalk->sumGroupLogic($Line, $Year, $reportHash, $this->_sumGroup);
        		 $this->_sumGroup =$this->TerminOppgaveKalk->getSumGroup();
                 break;
             case $this->_AARSOPPGAVE:
                  if ($this->AArgsoppgaveKalk == null)
        	         $this->AArgsoppgaveKalk=new AArgsoppgaveKalk(array());
        	         
        		 $this->AArgsoppgaveKalk->sumGroupLogic($Line, $Year, $reportHash, $this->_sumGroup);
        		 $this->_sumGroup =$this->AArgsoppgaveKalk->getSumGroup();
                 break;
             case $this->_SPEKAVINNTEKTOGFRADRAGUTLANDET:
                  if ($this->InntektOgFradragUtlandetKalk == null)
        	         $this->InntektOgFradragUtlandetKalk=new InntektOgFradragUtlandetKalk(array());
        	         
        		 $this->InntektOgFradragUtlandetKalk->sumGroupLogic($Line, $Year, $reportHash, $this->_sumGroup);
        		 $this->_sumGroup =$this->InntektOgFradragUtlandetKalk->getSumGroup();
                 break;
              case $this->_REGISTRERTEOGINNBERETTEDE:
                  if ($this->RegistrerteOgInnberettedeKalk == null)
        	         $this->RegistrerteOgInnberettedeKalk=new RegistrerteOgInnberettedeKalk(array());
        	         
        		 $this->RegistrerteOgInnberettedeKalk->sumGroupLogic($Line, $Year, $reportHash, $this->_sumGroup);
        		 $this->_sumGroup =$this->RegistrerteOgInnberettedeKalk->getSumGroup();
                 break;
              case $this->_AKSJONAERREGISTEROPPGAVE:
                  if ($this->AksjonaerRegisterKalk == null)
        	         $this->AksjonaerRegisterKalk=new AksjonaerRegisterKalk(array());
        	         
        		 $this->AksjonaerRegisterKalk->sumGroupLogic($Line, $Year, $reportHash, $this->_sumGroup);
        		 $this->_sumGroup =$this->AksjonaerRegisterKalk->getSumGroup();
                 break;
            default: break;
            
        }//witch case
    	
    }//function sumGroupLogic_

    ############################################################################
    function sumGroupLogic($Line, $Year, $reportHash)
    {
        if ($this->_report == $this->_NAERINGSOPPGAVE1 ||
            ($this->_report == $this->_PERSONINNTEKT1 and $this->_companyType != 'AS')){
        	if($Line == "0110" || $Line == "0120" || $Line == "0130" || $Line == "0140" ||
	               $Line == "0150"  || $Line == "0160" )
	            {
	                $this->sumGroup('0170', $Year, $reportHash);
	        } 
        	elseif($Line >= 3000 and $Line <= 3900)
            {
                $this->sumGroup('9900', $Year, $reportHash);
            } 
            elseif($Line >= 4005 and $Line <= 7897)
            {
                $this->sumGroup('9910', $Year, $reportHash);
            }
            elseif(($Line >= 8060 and $Line <= 8099) ||  $Line == 8285)
            {
                $this->sumGroup('9925', $Year, $reportHash);
            }
            elseif($Line >= 8160 and $Line <= 8288)
            {
                $this->sumGroup('9928', $Year, $reportHash);
            }
            elseif($Line >= 1000 and $Line <= 1950)
            {
                $this->sumGroup('9950', $Year, $reportHash);
            }
            elseif($Line >= 2015 and $Line <= 2080)
            {
                $this->sumGroup('9960', $Year, $reportHash);
            }
            elseif($Line >= 2095 and $Line <= 2097)
            {
                $this->sumGroup('9970', $Year, $reportHash);
            }
            elseif($Line >= 2220 and $Line <= 2995)
            {
                $this->sumGroup('9990', $Year, $reportHash);
            }
        }
        elseif($this->_report == $this->_NAERINGSOPPGAVE2 || 
        		($this->_report == $this->_PERSONINNTEKT2 and $this->_companyType == 'AS'))
        {   if($Line == "0110" || $Line == "0120" || $Line == "0130" || $Line == "0140" ||
	               $Line == "0150"  || $Line == "0160" ){
	                $this->sumGroup('0170', $Year, $reportHash);
	        }
            elseif($Line >= 1000 and $Line < 1400)
            {
                $this->sumGroup('9300', $Year, $reportHash);
            }
            elseif($Line >= 1400 and $Line < 2000)
            {
                $this->sumGroup('9350', $Year, $reportHash);
            }
            elseif($Line >= 2000 and $Line < 2100)
            {
                $this->sumGroup('9450', $Year, $reportHash);
            }
            elseif($Line >= 2100 and $Line < 2300)
            {
                $this->sumGroup('9500', $Year, $reportHash);
            }
            elseif($Line >= 2300 and $Line < 3000)
            {
                $this->sumGroup('9550', $Year, $reportHash);
            }
            elseif($Line >= 3000 and $Line < 4000)
            {
                $this->sumGroup('9000', $Year, $reportHash);
            }
            elseif($Line >= 4000 and $Line < 8000)
            {
                $this->sumGroup('9010', $Year, $reportHash);
            }
            elseif($Line >= 8000 and $Line < 8100 and $Line != 8006)
            {
                $this->sumGroup('9060', $Year, $reportHash);
            }
            elseif($Line >= 8100 and $Line < 8300 and $Line != 8005)
            {
                $this->sumGroup('9070', $Year, $reportHash);
            }
            elseif($Line >= 8300 and $Line < 8400)
            {
                //$this->sumGroup('9150', $Year, $reportHash);
            }
            elseif($Line >= 8400 and $Line < 8700)
            {
                //$this->sumGroup('9200', $Year, $reportHash);
            }
            
            //find out if this  a personinntekt report that has been associated with
            //the n�ringsoppgave2
             //If this is a personinntekt report then start the ..
            if ($this->_report == $this->_PERSONINNTEKT2) {
            	if ($Line == '1.11' || $Line == '1.12' || $Line == '1.13' || $Line == '1.14' ||
            	    $Line == '1.15' || $Line == '1.16' || $Line == '1.17' || $Line == '1.18' ||
            	    $Line == '1.19' || $Line == '1.20'){
            	    $this->sumGroup('1.21', $Year, $reportHash);
            	}elseif($Line == '2.1a' || $Line == '2.1b' || $Line == '2.1c' || $Line == '2.1d'||
                   $Line == '2.1e' || $Line == '2.1f' || $Line == '2.1g' || $Line == '2.1h' ||
                   $Line == '2.1j' || $Line == '2.2' || $Line == '2.3' || $Line == '2.4' ||
                   $Line == '2.5' || $Line == '2.6'){
                 	$this->sumGroup('2.7', $Year, $reportHash);	  
                 }
            	 
            	
            }
        }//
        ########################################################################
        elseif($this->_report ==$this->_SELVANGIVELSENAERINGSDRIVENDE)
        {
            if($Line == "2.7.1" || $Line == "2.7.2" || $Line == "2.7.3" || $Line == "2.7.4")
            {
                $this->sumGroup('2.7.5', $Year, $reportHash);
            }
            elseif($Line == "4.4.1" || $Line == "4.4.2" || $Line == "4.4.3" || $Line == "4.4.4")
            {
                $this->sumGroup('4.4.5', $Year, $reportHash);
            }
        }
        ########################################################################
        elseif($this->_report == $this->_SELVANGIVELDEAKSJESELSKAP )
        {
            if($Line == "202" || $Line == "206" || $Line == "207" || $Line == "208" || $Line == "209b" || $Line == "210")
            {
                $this->sumGroup('220', $Year, $reportHash);
            }
            elseif($Line == "222" || $Line == "223" || $Line == "224b" || $Line == "225")
            {
                $this->sumGroup('230', $Year, $reportHash);
            }
            elseif($Line == "261" || $Line == "262" || $Line == "263" || $Line == "264")
            {
                $this->sumGroup('265', $Year, $reportHash);
            }
            elseif($Line == "402a" || $Line == "402b" || $Line == "402c" || $Line == "403" || $Line == "404" || $Line == "405" || $Line == "406" || $Line == "407" || $Line == "408" || $Line == "409f" || $Line == "410" || $Line == "411" || $Line == "412")
            {
                $this->sumGroup('420', $Year, $reportHash);
            }
            elseif($Line == "420" || $Line == "421" || $Line == "422b")
            {
                //$this->sumGroup('430', $Year, $reportHash);
            }
            elseif($Line == "461" || $Line == "462" || $Line == "463")
            {
                $this->sumGroup('470', $Year, $reportHash);
            }
            
            
        }//
        
       
        
    }//sumGroupLogic

    ############################################################################
    function GetReport($args)
    {

        #print_r($args);
        foreach($args as $key => $value) {
            $this->{'_' . $key} = $value;
        }
        if(!$this->_report) {
          $this->_report = 1;
        }
        #print_r($this);
        $this->BuildReport();

        #print_r($this->_sumLine);
        #print_r($this->_sumPart);
        #print_r($this->_sumPart);
        return array($this->_sumLine, $this->_sumPart, $this->_sumGroup, $this->_sumTotal, $this->_Total);
    }

    function GetAccountByLine($lineID)
    {
        global $_dbh, $_dsn, $_lib;
        $query = "select AccountPlanID from accountplan where Report".$this->_report."Line='".$lineID."' and EnableReport".$this->_report."=1 and Active=1";
        #print "$query<br>\n";
        $row = $_lib['db']->get_row(array('query'=>$query));
        return $row->AccountPlanID;
    }
}
?>
