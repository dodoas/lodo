<?php
require_once $_SETUP['HOME_DIR']."/code/lodo/altinn/_include_proxy/class_skjemamaker.php";

$lodoActive = true;
if ( $lodoActive )
{
//	include_once $_SETUP['HOME_DIR']."/code/lodo/mvaavstemming/mvaavstemming.class";
//	require_once 'class_mva.php';
}

class SchemaClass
{
	var $lodo;
	var $mva;
	var $db;
	var $config;

	var $reported;

	var $data_lodo;
	var $data;

	var $SCHEMA_NUMBER;
	var $SCHEMA_REVISION;
	var $SCHEMA_NAME;
	var $ASSOCIATED_SCHEMA_NUMBER=0;
	
	var $AKSJESELSKAP=1;
	var $SELVSTENDIGNAERING=2;
	
	var $_comanyInfo ;
	
	
	function SchemaClass( $db, $lodo, $config, $mySCHEMA_NUMBER = 0)
	{
		$this->db = $db;
		$this->lodo = $lodo;
		$this->config = $config;

		$this->reported = null;

		$sqlStr = 'SELECT * FROM `altinnschemalist` WHERE fagsystemid=' . $mySCHEMA_NUMBER . ';';
		$rs = $this->db->Query( $sqlStr );
		$row = $this->db->NextRow( $rs );

		$this->SCHEMA_NUMBER = $mySCHEMA_NUMBER;
		$this->SCHEMA_REVISION = $row["revision"];
		$this->SCHEMA_NAME = $row["name"];
	}

	function LoadSchemaFromLodo( $year, $termin, $terminitem=0)
	{

		global $_SETUP;
		// M� sjekkes
		includealogic("momssoppgave");
		includealogic("naeringsoppgave1");
		includealogic("naeringsoppgave2");
		includealogic("selvangivelseaksjeselskap");
		includealogic("selvangivelsenaeringsdrivende");
		includealogic("personinntekt");
		includealogic("avskrivninger");
		includealogic("bilbruksopplysninger");
		includealogic("forskjellerregnskapogskatte");
		includealogic("egenkapitalavstemming");
		includealogic("tilleggsskjema1");
		includealogic("tilleggsskjema2");
		includealogic("gevinstogtapskonto");
		includealogic("riskberegning");
		includealogic("selskapsoppgave");
		includealogic("deltakerensoppgave");
		includealogic("terminoppgave");
		includealogic("realisasjonavaksjeoppgave");
		includealogic("registrerteoginnberettede");
		includealogic("aksjonaerregisteroppgave");
		includealogic("spekavinntektogfradragutlandet");
		includealogic("lonnogtrekkoppgave");
		includealogic("aarsoppgave");
		includealogic("oppstillingsplansmastoreselskaper");
/*
		require_once $_SETUP['HOME_DIR']."/code/lodo/lib/momssoppgave.class";
		require_once $_SETUP['HOME_DIR']."/code/lodo/lib/naeringsoppgave1.class";
		require_once $_SETUP['HOME_DIR']."/code/lodo/lib/naeringsoppgave2.class";
		require_once $_SETUP['HOME_DIR']."/code/lodo/lib/selvangivelseaksjeselskap.class";
		require_once $_SETUP['HOME_DIR']."/code/lodo/lib/selvangivelsenaeringsdrivende.class";
		require_once $_SETUP['HOME_DIR']."/code/lodo/lib/personinntekt.class";
		require_once $_SETUP['HOME_DIR']."/code/lodo/lib/avskrivninger.class";
		require_once $_SETUP['HOME_DIR']."/code/lodo/lib/bilbruksopplysninger.class";
		require_once $_SETUP['HOME_DIR']."/code/lodo/lib/forskjellerregnskapogskatte.class";
		require_once $_SETUP['HOME_DIR']."/code/lodo/lib/egenkapitalavstemming.class";
		require_once $_SETUP['HOME_DIR']."/code/lodo/lib/tilleggsskjema1.class";
		require_once $_SETUP['HOME_DIR']."/code/lodo/lib/tilleggsskjema2.class";
		require_once $_SETUP['HOME_DIR']."/code/lodo/lib/gevinstogtapskonto.class";
		require_once $_SETUP['HOME_DIR']."/code/lodo/lib/riskberegning.class";
		require_once $_SETUP['HOME_DIR']."/code/lodo/lib/selskapsoppgave.class";
		require_once $_SETUP['HOME_DIR']."/code/lodo/lib/deltakerensoppgave.class";
		require_once $_SETUP['HOME_DIR']."/code/lodo/lib/terminoppgave.class";
		require_once $_SETUP['HOME_DIR']."/code/lodo/lib/realisasjonavaksjeoppgave.class";
		require_once $_SETUP['HOME_DIR']."/code/lodo/lib/registrerteoginnberettede.class";
		require_once $_SETUP['HOME_DIR']."/code/lodo/lib/aksjonaerregisteroppgave.class";
		require_once $_SETUP['HOME_DIR']."/code/lodo/lib/spekavinntektogfradragutlandet.class";
		require_once $_SETUP['HOME_DIR']."/code/lodo/lib/lonnogtrekkoppgave.class";
		require_once $_SETUP['HOME_DIR']."/code/lodo/lib/aarsoppgave.class";
		require_once $_SETUP['HOME_DIR']."/code/lodo/lib/oppstillingsplansmastoreselskaper.class";
*/		
		//print ("Anh anh:". $this->SCHEMA_NUMBER. "<br>");
		
		switch($this->SCHEMA_NUMBER){
			case 212: //(RF-0002) Alminnelig omsetningsoppgave
			    $GeneralReport = new MOMSOppgave(array('termin'=>$termin, 'terminitem'=>$terminitem, 'year'=>$year, 'enableLastYear'=>'0', 'report'=>'0002', 'db'=>$this->db));
			    break;
			case 1://(RF-1167) N�ringsoppgave for aksjeselskap
				$GeneralReport = new NaeringsOppgave2(array('fromPeriod'=>$year . '-01', 'toPeriod'=>$year . '-12', 'enableLastYear'=>'1', 'report'=>'5'));
				break;
		    case 2: //(RF-1175) N�ringsoppgave for n�ringsdrivende	
		        $GeneralReport = new NaeringsOppgave1(array('fromPeriod'=>$year . '-01', 'toPeriod'=>$year . '-12', 'enableLastYear'=>'1', 'report'=>'3'));
		    	break;
		    case 99: //(RF-1028) Selvangivelse for aksjeselskap
		        $GeneralReport =new SelvangivelseAksjeselskap(array('fromPeriod'=>$year . '-01', 'toPeriod'=>$year . '-12', 'enableLastYear'=>'0', 'report'=>'4'));
		    	break;
		    case 179://(RF-1027) Selvangivelse for n�ringsdrivende
		        $GeneralReport =new SelvangivelseNaeringsdrivende(array('fromPeriod'=>$year . '-01', 'toPeriod'=>$year . '-12', 'enableLastYear'=>'0', 'report'=>'2'));
		    	break;
		    case 80: //(RF-1224) Skjema for beregning av personinntekt
		         $GeneralReport =new PersonInntekt (array('fromPeriod'=>$year . '-01', 'toPeriod'=>$year . '-12', 'enableLastYear'=>'1', 'report'=>'1224'));
		        break;
		    case 3:  //(RF-1084) Avskrivningsskjema for saldo og line�re
		          $GeneralReport =new Avskrivninger (array('fromPeriod'=>$year . '-01', 'toPeriod'=>$year . '-12', 'enableLastYear'=>'0', 'report'=>'1084'));
		        break;
		    case 245: //(RF-1125)Opplysninger om bruk av bil
		    	  $GeneralReport =new BilbruksOpplysninger (array('fromPeriod'=>$year . '-01', 'toPeriod'=>$year . '-12', 'enableLastYear'=>'0', 'report'=>'1125', 'db'=>$this->db));
		        break;
		    case 98: //(RF-1217) Forskjeller mellon regnskap- og skatte verdier
		          $GeneralReport =new ForskjellerRegnskapOgSkatte (array('fromPeriod'=>$year . '-01', 'toPeriod'=>$year . '-12', 'enableLastYear'=>'1', 'report'=>'1217'));
		        break;
		    case 74: //(RF-1122)Tilleggsskjema for overnattings- og serveringssteder (�l, vin og brennevin)
		           $GeneralReport =new Tilleggsskjema1 (array('fromPeriod'=>$year . '-01', 'toPeriod'=>$year . '-12', 'enableLastYear'=>'0', 'report'=>'1122'));
		        break;
		    case 754: //(RF-1052) Avstemming av egenkapital
		           $GeneralReport =new EgenkapitalAvstemming (array('fromPeriod'=>$year . '-01', 'toPeriod'=>$year . '-12', 'enableLastYear'=>'0', 'report'=>'1052'));
		        break;
		    case 72: //(RF-1223) Tilleggsskjema for drosje og lastebiln�ring
		    	   $GeneralReport =new Tilleggsskjema2 (array('fromPeriod'=>$year . '-01', 'toPeriod'=>$year . '-12', 'enableLastYear'=>'0', 'report'=>'1223'));	
		        break;
		    case 62: //(RF-1219) Gevinst og tapskonto
		           $GeneralReport =new GevinstOgTapskonto (array('fromPeriod'=>$year . '-01', 'toPeriod'=>$year . '-12', 'enableLastYear'=>'0', 'report'=>'1219'));
		        break;
		    case 70://(RF-1061) Oppgave over realisasjon av aksje mv
		           $GeneralReport =new RealisasjonAvAksjeOppgave (array('fromPeriod'=>$year . '-01', 'toPeriod'=>$year . '-12', 'enableLastYear'=>'0', 'report'=>'1061'));
		        break;
		    case 79: //(RF-1239) Beregning av RISK
		          $GeneralReport =new BeregningAvRISK (array('fromPeriod'=>$year . '-01', 'toPeriod'=>$year . '-12', 'enableLastYear'=>'0', 'report'=>$this->SCHEMA_NUMBER));
		    case 238://(RF-1215)Selskapsoppgave for ansvarlig selskap
		           $GeneralReport =new SelskapsOppgave (array('fromPeriod'=>$year . '-01', 'toPeriod'=>$year . '-12', 'enableLastYear'=>'0', 'report'=>'1215'));
		        break;
		    case 239://(RF-1221)Delatkerens oppgave over formue og inntekt i ANS mv
		            $GeneralReport =new DeltakerensOppgave (array('fromPeriod'=>$year . '-01', 'toPeriod'=>$year . '-12', 'enableLastYear'=>'0', 'report'=>'1221'));
		        break;
		    case 669://(RF-1037) Terminoppgave for arbeidsgiveravgift og forskuddstrekk
		            $GeneralReport =new TerminOppgave (array('report'=>'1037', 'termin'=>$termin, 'year'=>$year, 'db'=>$this->db));
		        break;
		       
		    case 735://(RF-1025) �rsoppgave for arbeidsgiveravgift - F�lgeskriv til l�nns - og trekkoppgave
		           $GeneralReport =new AArsOppgave (array('fromPeriod'=>$year . '-01', 'toPeriod'=>$year . '-12', 'enableLastYear'=>'0', 'report'=>'1025', 'db'=>$this->db));
		        break;
		    case 748:// (RF-1231B) Spesifikasjon av innskudd i utenlandsk bank mv. og USB-sparing
		             // i annen E�S-stat
		           $GeneralReport =new SpekAvInntektOgFradragUtlandet (array('fromPeriod'=>$year . '-01', 'toPeriod'=>$year . '-12', 'enableLastYear'=>'0', 'report'=>'1231B'));
		    	break; 
		    case 769://(RF-1022)Kontroll over registrerte og innberettede bel�p for 2004
		            $GeneralReport =new RegistrerteOgInnberettede (array('fromPeriod'=>$year . '-01', 'toPeriod'=>$year . '-12', 'enableLastYear'=>'0', 'report'=>'1022', 'db'=>$this->db));
		        break;
		    case 890://(RF-1086) Aksjon�rregister oppgaven
		           $GeneralReport =new AksjonaerRegisterOpggave (array('fromPeriod'=>$year . '-01', 'toPeriod'=>$year . '-12', 'enableLastYear'=>'0', 'report'=>'1086'));
		        break;
		        
		     case 758://(RR-0002) Oppstlinngsplan fir sm� og store selskaper
		           $GeneralReport =new OppstillingsPlan (array('fromPeriod'=>$year . '-01', 'toPeriod'=>$year . '-12', 'enableLastYear'=>'0', 'report'=>'0002B'));
		        break;
		    
		    default:break;
		} 
		
		$this->data = $GeneralReport->getOridArray();
		
		//print ("Anh data:");
		//print_r ($this->data);
		return($this->data);
	}

	function GetData()
	{
		//print_r ($this->data);
		return( $this->data );
	}

	function CreateNewPackage( $year, $termin )
	{

	}

	function ToXML( $year, $termin, $extraParams = null){
		global $_SETUP,$Constants, $_REQUEST;
		
		includelogic('company/companyinfo');
		$this->_comanyInfo     = new ComanyInfo(array());
		
		$companyType=$this->_comanyInfo->CustomerCompany->ShareValue+$this->_comanyInfo->CustomerCompany->ShareNumber;
		
		//Set the OrgNumber as default for participantID
		$participantID=$this->_comanyInfo->CustomerCompany->OrgNumber2;
		
		// print("Anh ParticipantID:".$this->_comanyInfo->CustomerCompany->OrgNumber2);
        
		
		$parentsRefTemp=split("&", $extraParams["parentsRef"]);	
		    //$extraParams["parentsRef"]=parentsRefTemp;
		    
	    for($i=0; $i< count($parentsRefTemp); $i++) {
	    	switch ($i){
	    		case 0: $extraParams["parentsRef"]=$parentsRefTemp[$i];
	    			break;
	    		case 1: $this->ASSOCIATED_SCHEMA_NUMBER=$parentsRefTemp[$i];
	    			break;
	    		default: break;
	    	}
        }//for
        
        if ($this->SCHEMA_NUMBER == "179" || $this->SCHEMA_NUMBER == "2")
			$participantID = $this->_comanyInfo->CustomerPerson->SocialSecurityID;
		elseif ($this->SCHEMA_NUMBER == "99" || $this->SCHEMA_NUMBER == "1")
			$participantID = $this->_comanyInfo->CustomerCompany->OrgNumber2;
		elseif  ($this->SCHEMA_NUMBER == "758")
		    $participantID = $this->_comanyInfo->CustomerCompany->OrgNumber2;
        elseif ($this->ASSOCIATED_SCHEMA_NUMBER == "179")
			$participantID = $this->_comanyInfo->CustomerPerson->SocialSecurityID;
		elseif  ($this->ASSOCIATED_SCHEMA_NUMBER == "99")
			$participantID = $this->_comanyInfo->CustomerCompany->OrgNumber2;
# Denne linjen bytter man på når det veksles mellom enkeltmannsforetak og AS
#	if ($this->_comanyInfo->CustomerCompany->ShareValue > 0 )
#	    $participantID = $this->_comanyInfo->CustomerPerson->SocialSecurityID;
#	else
           $participantID = $this->_comanyInfo->CustomerCompany->OrgNumber2;

	    if ($_REQUEST["fagsystemtype"]) {
	    	$fagsystemtype=$_REQUEST["fagsystemtype"];
	     	
	     	settype($fagsystemtype, "integer");
			if ($fagsystemtype==$this->AKSJESELSKAP){
			    $participantID = $this->_comanyInfo->CustomerCompany->OrgNumber2;
			}
			else $participantID = $this->_comanyInfo->CustomerPerson->SocialSecurityID;
		}
		
		if ($_REQUEST["selectParticipantID"] != ""){
				if ($_REQUEST["selectParticipantID"] == "person")
					$participantID = $this->_comanyInfo->CustomerPerson->SocialSecurityID;
				elseif ($_REQUEST["selectParticipantID"] == "org")
					 $participantID = $this->_comanyInfo->CustomerCompany->OrgNumber2;
	    }//if
			//$participantID = $this->_comanyInfo->RegnskapCompany->OrgNumber;
			//$participantID = $this->config->GetConfig($this->config->TYPE_ORGNO);
			
		//print("Anh schema:".$this->SCHEMA_NUMBER);
		//Termin oppgave "669"
		//if ($this->SCHEMA_NUMBER=="669") { 
			//if ($participantID == "" && $companyType>0)
				//$participantID=$this->_comanyInfo->CustomerCompany->OrgNumber;
			//else $participantID=$this->_comanyInfo->CustomerPerson->SocialSecurityID;
		//}//if
		
		if ($this->SCHEMA_NUMBER=="212" || $this->SCHEMA_NUMBER=="669") { 
			$participantID=$this->_comanyInfo->CustomerCompany->OrgNumber2;
		}//if
		    	
		if ($extraParams["useProxy"]){		
/*
			foreach ( $this->data as $key => $value )
			{
				if ( $retData != '' ) {$retData .= '&';}
				$retData .= urlencode( $key ) . '=' . urlencode( $value );
			}
*/

			foreach ( $this->data as $key => $value )
			{
				if (is_array($value))
				{
					foreach ($value as $v)
					{
						if ( $retData != '' ) {$retData .= '&';}
						$retData .= urlencode( $key ) . '=' . urlencode( $v );
					}
				}
				else
				{
					if ( $retData != '' ) {$retData .= '&';}
					$retData .= urlencode( $key ) . '=' . urlencode( $value );
				}
			}
			
			// $this->config->GetConfig($this->config->TYPE_ORGNO)
			
			
			// $participantID = "970131450";
			//$participantID = "00755222675";
			
			$sendPost = "orgnr=" . $participantID;
			//$sendPost .= "&batchId=" . $this->config->GetConfig($this->config->TYPE_ORGNO) . "-" . sprintf("%02d", $this->config->GetConfig($this->config->TYPE_BATCHSUBNO)) . "-" . $extraParams["packageID"];
			$sendPost .= "&batchId=" . str_replace(" ", "", $this->_comanyInfo->CustomerCompany->OrgNumber) . "-" . sprintf("%02d", $this->config->GetConfig($this->config->TYPE_BATCHSUBNO)) . "-" . $extraParams["packageID"];
			$sendPost .= "&systemID=" .  $this->config->GetConfig($this->config->TYPE_FAGSYSTEMID);
			$sendPost .= "&sendersRef=" . $extraParams["sendersRef"];
			$sendPost .= "&parentsRef=" . $extraParams["parentsRef"]; 
			$sendPost .= "&passord=" . $this->config->GetConfig($this->config->TYPE_PASSWORD);
			$sendPost .= "&fagsystemID=" . $this->SCHEMA_NUMBER;
			if ($extraParams["portal"] != "")
				$sendPost .= "&portal=" . $extraParams["portal"];
			$sendPost .= "&data=" . urlencode( $retData ); 
			return $sendPost;
		}
		//use proxy
		else
		{
			
			global $_SETUP,$Constants;
			$filepathprefix = $_SETUP['HOME_DIR'] . "/code/lodo/altinn/_include/";
			
			/* --- Make the xml document with input data --- */
			$schemaMaker= new skjemaMaker(array('fagsystemid' => $this->SCHEMA_NUMBER, 'db' => $this->db));
			$schemaMaker->addData($this->data);
			$schemaMaker->makeSkjema();
			// $inputData["orgnr"] = $this->config->GetConfig($this->config->TYPE_ORGNO);
			$inputData["orgnr"] = $this->config->GetConfig($this->config->TYPE_ORGNO);
			$inputData["batchId"] = $inputData["orgnr"] . "-" . sprintf("%02d", $this->config->GetConfig($this->config->TYPE_BATCHSUBNO)) . "-" . $extraParams["packageID"];;
			$inputData["systemID"] = $this->config->GetConfig($this->config->TYPE_FAGSYSTEMID);
			$inputData["sendersRef"] = $extraParams["sendersRef"];
			$inputData["parentsRef"] = $extraParams["parentsRef"]; 
			 
			$schemaMaker->makeEnvelope($inputData);
			return $schemaMaker->getXML();
		}
	}

	function GetReportData( $report, $data )
	{
	}

	function DisplaySchemaCurrent( $status )
	{
		$this->DisplaySchema( $this->data, $status );
	}
	function DisplaySchema( $data, $status )
	{   
		$file= 'schema_' . $this->SCHEMA_REVISION .  '.php';
		//print ("file: ".$file. "<br>" );
		include $file;
	}

	function GetTerminReport( $terminType, $terminItem )
	{

	}

	function GetMonthReport( $month )
	{

	}

	function ReadSchemaForm(  )
	{
		global $_REQUEST;
		$data = array();
		foreach ($_REQUEST as $key => $val)
		{
			$data[$key] = intval($val);
		}
		$this->data = $data;
	}

	function TranslateLodoData( $lodoData )
	{
		return $lodoData; 
	}

	function AddReports( $report1, $report2 )
	{

	}

	function RetrieveYear( $year )
	{

	}
}
?>
