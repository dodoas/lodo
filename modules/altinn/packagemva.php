<<<<<<< .mine
<html>
<head>
  <script language="JavaScript">
  var send_enabled=false;
<!--//
function Calculate()
{   
    var error='Alle poster som merkes r�dt er feil, m� rette p� dem f�r sending.<br />Summen av post 3, 4, 5 og 6 skal v�re lik post 2.';
    var readyToSend=document.forms.mvaf.READYTOSEND.value;
    var ready='Skjema er klar for sending.';
    

    //Accountplan 2701
    if (document.forms.mvaf.D10097.value!= '' && document.forms.mvaf.D10097.value!= '0') {
		tmp = parseInt(document.forms.mvaf.D10097.value) * parseInt(document.forms.mvaf.SATS2701.value) / 100;
			
		document.forms.mvaf.D10098.style.color = "black";
		if ( document.forms.mvaf.D10098.value == '' ||  document.forms.mvaf.D10098.value == '0') 
		     document.forms.mvaf.D10098.value = tmp;
		else if ( parseInt(document.forms.mvaf.D10098.value) != tmp) {
		    document.forms.mvaf.D10098.style.color = "red";
		   
		    if (document.all){
		        document.all('message').style.color='red';
		    	document.all('message').innerText=error;
		    }else {
		        var obj=document.getElementById ("message");
		        if (obj.innerText) {
		            obj.style.color='red';
		        	obj.innerText=error;
		        }if(obj.innerHTML) {
		            obj.style.color='red';
		        	obj.innerHTML=error;
		        }
		    
		    }
		    
		    readyToSend=false;
		    
		}
	}
    
    //Accountplan 2702
    if (document.forms.mvaf.D20319.value != '' && document.forms.mvaf.D20319.value!= '0') {
		tmp = parseInt(document.forms.mvaf.D20319.value) * parseInt(document.forms.mvaf.SATS2702.value) / 100;
		document.forms.mvaf.D20320.style.color = "black";
		if (document.forms.mvaf.D20320.value == '' || document.forms.mvaf.D20320.value == '0' ) 
		    document.forms.mvaf.D20320.value = tmp;
		else if ( parseInt(document.forms.mvaf.D20320.value) != tmp) {
		    document.forms.mvaf.D20320.style.color = "red";
		    
		    if (document.all){
		        document.all('message').style.color='red';
		    	document.all('message').innerText=error;
		    }else {
		        var obj=document.getElementById ("message");
		        if (obj.innerText) {
		            obj.style.color='red';
		        	obj.innerText=error;
		        }if(obj.innerHTML) {
		            obj.style.color='red';
		        	obj.innerHTML=error;
		        }
		    
		    }
		    
		    readyToSend=false;
		}
     }

    //Accountplan 2703
    if (document.forms.mvaf.D14360.value != '' && document.forms.mvaf.D14360.value != '0') {
		tmp = parseInt(document.forms.mvaf.D14360.value) * parseInt(document.forms.mvaf.SATS2703.value) / 100;
		document.forms.mvaf.D14361.style.color = "black";
		if (document.forms.mvaf.D14361.value == '' || document.forms.mvaf.D14361.value == '0' )
		   document.forms.mvaf.D14361.value = tmp;
		else if ( parseInt(document.forms.mvaf.D14361.value) != tmp) {
			document.forms.mvaf.D14361.style.color = "red";
			
			if (document.all){
		        document.all('message').style.color='red';
		    	document.all('message').innerText=error;
		    }else {
		        var obj=document.getElementById ("message");
		        if (obj.innerText) {
		            obj.style.color='red';
		        	obj.innerText=error;
		        }if(obj.innerHTML) {
		            obj.style.color='red';
		        	obj.innerHTML=error;
		        }
		    
		    }
		    
		    readyToSend=false;
	    }
	}

    //Accountplan 2704 not used
    if (document.forms.mvaf.D14362.value != '' && document.forms.mvaf.D14362.value != '0') {
		tmp = parseInt(document.forms.mvaf.D14362.value) * parseInt(document.forms.mvaf.SATS2704.value) / 100;
			document.forms.mvaf.D14363.style.color = "black";
		if (document.forms.mvaf.D14363.value == '' || document.forms.mvaf.D14363.value == '0' ) 
		    document.forms.mvaf.D14363.value = tmp;
		else if ( parseInt(document.forms.mvaf.D14363.value) != tmp) {
		    document.forms.mvaf.D14363.style.color = "red";
		    
		    if (document.all){
		        document.all('message').style.color='red';
		    	document.all('message').innerText=error;
		    }else {
		        var obj=document.getElementById ("message");
		        if (obj.innerText) {
		            obj.style.color='red';
		        	obj.innerText=error;
		        }if(obj.innerHTML) {
		            obj.style.color='red';
		        	obj.innerHTML=error;
		        }
		    
		    }
		    
		    readyToSend=false;
		}
	}

    base=0;
	if (document.forms.mvaf.D10096.value!= '')
		base += parseInt(document.forms.mvaf.D10096.value);
    if (document.forms.mvaf.D10097.value!= '')
		base += parseInt(document.forms.mvaf.D10097.value);
	if (document.forms.mvaf.D20319.value!= '')
		base += parseInt(document.forms.mvaf.D20319.value);
	if (document.forms.mvaf.D14360.value!= '')
		base += parseInt(document.forms.mvaf.D14360.value);

	document.forms.mvaf.D8446.style.color = "black";
	if (document.forms.mvaf.D8446.value == '' || document.forms.mvaf.D8446.value == '0' ) 
		document.forms.mvaf.D8446.value = base;
	else if ( parseInt(document.forms.mvaf.D8446.value) != base) {
		document.forms.mvaf.D8446.style.color = "red";
		
		if (document.all){
		        document.all('message').style.color='red';
		    	document.all('message').innerText=error;
		    }else {
		        var obj=document.getElementById ("message");
		        if (obj.innerText) {
		            obj.style.color='red';
		        	obj.innerText=error;
		        }if(obj.innerHTML) {
		            obj.style.color='red';
		        	obj.innerHTML=error;
		        }
		    
		    }
    }

	document.forms.mvaf.D10095.style.color = "black";
	if (document.forms.mvaf.D10095.value == '' || document.forms.mvaf.D10095.value == '0') 
		document.forms.mvaf.D10095.value = base;
	else if ( parseInt(document.forms.mvaf.D10095.value) != base) {
		document.forms.mvaf.D10095.style.color = "red";
		
		if (document.all){
		        document.all('message').style.color='red';
		    	document.all('message').innerText=error;
		 }else {
	        var obj=document.getElementById ("message");
	        if (obj.innerText) {
	            obj.style.color='red';
	        	obj.innerText=error;
	        }if(obj.innerHTML) {
	            obj.style.color='red';
	        	obj.innerHTML=error;
	        }
	    
	    }
		    
		readyToSend=false;
		
	}
    
    if (document.forms.mvaf.D10098.value != '')
		tmp = parseInt(document.forms.mvaf.D10098.value);
	if (document.forms.mvaf.D20320.value != '')
		tmp += parseInt(document.forms.mvaf.D20320.value);
	if (document.forms.mvaf.D14361.value != '')
		tmp += parseInt(document.forms.mvaf.D14361.value);
	if (document.forms.mvaf.D14363.value != '')
		tmp += parseInt(document.forms.mvaf.D14363.value);
	if (document.forms.mvaf.D8450.value != '')
		tmp -= parseInt(document.forms.mvaf.D8450.value);
    if (document.forms.mvaf.D20322.value != '')
		tmp -= parseInt(document.forms.mvaf.D20322.value);
	if (document.forms.mvaf.D14364.value != '')
		tmp -= parseInt(document.forms.mvaf.D14364.value);

	mtmp = parseInt(tmp);
	
	if (mtmp >= 0) {
		document.forms.mvaf.D8452.value = 0;
		document.forms.mvaf.D8453.value = mtmp;
	}
	else {
		document.forms.mvaf.D8452.value = mtmp * -1;
		document.forms.mvaf.D8453.value = 0;
	}
	
   if (readyToSend) {
       send_enabled=true;
       document.forms.mvaf.send.disabled=false;
       
   	   if (document.all){
		   document.all('message').style.color='blue';
		   document.all('message').innerText=ready;
	    }else {
	        var obj=document.getElementById ("message");
	        if (obj.innerText) {
	            obj.style.color='blue';
	        	obj.innerText=ready;
	        }if(obj.innerHTML) {
	            obj.style.color='blue';
	        	obj.innerHTML=ready;
	        }
	    
	    }
   }//if
   
   document.forms.mvaf.READYTOSEND.value=readyToSend;
	
}

function clearFields(){
	document.forms.mvaf.D10097.value='';
	document.forms.mvaf.D10098.value='';
	document.forms.mvaf.D20319.value='';
	document.forms.mvaf.D20320.value='';
	document.forms.mvaf.D14360.value='';
	document.forms.mvaf.D14361.value='';
	document.forms.mvaf.D14362.value='';
	document.forms.mvaf.D14363.value='';
	
	document.forms.mvaf.D10096.value='';
	document.forms.mvaf.D10097.value='';
	document.forms.mvaf.D20319.value='';
	document.forms.mvaf.D14360.value='';
	
	document.forms.mvaf.D8446.value='';
	document.forms.mvaf.D10095.value='';
	
	document.forms.mvaf.D10098.value='';
	document.forms.mvaf.D20320.value='';
	document.forms.mvaf.D14361.value='';
	document.forms.mvaf.D14363.value='';
	document.forms.mvaf.D8450.value='';
    document.forms.mvaf.D20322.value='';
	document.forms.mvaf.D14364.value='';
	
	document.forms.mvaf.D8452.value='';
	document.forms.mvaf.D8453.value='';
	
	send_enabled=false;
    document.forms.mvaf.send.disabled=true;
    document.forms.mvaf.READYTOSEND.value=true;
	
}

function setFagSystemType(obj){
	document.forms.mvaf.fagsystemtype.value=obj.value;
	//alert(document.forms.mvaf.fagsystemtype.value);
}


function setOppgaveType(obj){
    activeOppgave=document.forms.mvaf.D5659.value;
    //alert(activeOppgave);
    if (parseInt(activeOppgave)==1)
       document.forms.mvaf.hoved.checked=false;
    else if (parseInt(activeOppgave)==2)
       document.forms.mvaf.tillegg.checked=false;
    else if (parseInt(activeOppgave)==3) {
       document.forms.mvaf.korrigert.checked=false;
       //alert(3);
    }
        
    document.forms.mvaf.D5659.value=obj.value; 
    
    if (parseInt(obj.value)==1)
         document.forms.mvaf.hoved.checked=true;
    else if (parseInt(obj.value)==2)
       document.forms.mvaf.tillegg.checked=true;
    else if (parseInt(obj.value)==3)
       document.forms.mvaf.korrigert.checked=true;   
}
//-->
</script>
 
  

</head>
<?php

	include_once $_SETUP['HOME_DIR']."/code/lodo/mvaavstemming/mvaavstemming.class";



/****************************************************************************

** Copyright (c) 2005 Actra AS.

** All rights reserved!

**

** Developed by Gunnar Skeid (gunnar@actra.no)

** Total changed by Anh Le

** Handles package editing.

****************************************************************************/

	require_once '_include/class_lodo.php';
	require_once '_include/class_layout.php';
	require_once '_include/class_database.php';
	require_once '_include/class_mva.php';
	require_once '_include/class_config.php';
	//require_once '_include/class_package.php';
	require_once '_include/class_schema.php';
	require_once '_include/class_package_ge.php';

	include_once $_SETUP['HOME_DIR']."/code/lodo/mvaavstemming/mvaavstemming.class";

    $HOVEDOPPGAVE=1;
    $TILLEGGSOPPGAVE=2;
    $KORIGERTOPPGAVE=3;
    
    $mvaSatsTable= array();
    $antall_packages=0;
    $orid_data_array;
    $send_enabled=false;
    
    global $MY_SELF,$orid_data_array;
    

	$lodo = new lodo();

	$layout = new Layout( $lodo );

	$db = new Db( $lodo );
	
	if ( !$db->Connect() ) {
		$layout->PrintError("Kunne ikke koble til databasen.");
		die();
	}

	$mva = new Mva($db);
	$config = new Config($db);

	$package = new Package( array('db'=>$db, 'lodo'=>$lodo, 'config'=>$config,'layout'=>$layout) );
	//$package->setTermin (array ('terminitem'=>$_REQUEST['terminitem'],'terminlength'=>$_REQUEST['terminlength'] ));

	$year = $_REQUEST['year'];
	$termintype = $_REQUEST['termin'];
	$terminitem = $_REQUEST['terminitem'];
	$terminstr = $_REQUEST['terminstr'];
	$packageId = $_REQUEST['packageid'];
	
	//print ("termin: ". $termintype ."<br> terminItem:". $terminitem . "<br>");

	/* No packetid = create new packet */
	if ( !is_numeric( $packageId ) ){
		$status = 0;
		$year = $_REQUEST['year'];
		$terminItem = $_REQUEST['terminitem'];

		if ( !is_numeric($year) || !is_numeric($terminItem) ){
			$layout->PrintError('Det ble ikke oppgitt termin for pakken.');
			$db->Disconnect();
			die();
		}
		
		//Create a new package
		$packageId = $package->CreateNewPackage( $package->PACKAGETYPE_MVA, $year, $termintype, $terminItem );
		//print ("MVa anh packetid:".$packageId);
		
		$orid_data_array=$package->getOridArray();
		
		//print ("Anh Orid array:");
		//print_r($orid_data_array);
		
	}//if

	/* Edit old packet */
	else{
		if ($_REQUEST['delete']<>''){
			$sqlStr = 'DELETE FROM altinn_packet WHERE status=0 AND packet_id=' . $packageId;
			if ($db->Query($sqlStr)){
				$sqlStr = 'DELETE FROM altinn_schema WHERE packet_id=' . $packageId;

				$db->Query($sqlStr);

				$url = $lodo->LodoUrlGet( '', $lodo->LODOURLTYPE_HREF, 'altinn.termins' );

				$str = '<html><head><META HTTP-EQUIV="REFRESH" CONTENT="0; URL='. $url . '"></head><body><a href="' . $url . '">Neste</a></body></html>';

				echo($str);

				$db->EndQuery( $rs );

				$db->Disconnect();

				die();

			}else $layout->PrintWarning("Kunne ikke slette pakken!");

		}//if

		/* Get status */
		$status = -1;
		$sqlStr = 'SELECT status FROM altinn_packet WHERE packet_id=' . $packageId;

		if ( $rsStatus = $db->Query( $sqlStr )){
			if ( $rowStatus = $db->NextRow( $rsStatus ) )
				$status = $rowStatus['status'];

			$db->EndQuery( $rsStatus );
		}

		if ($status == -1){
			$layout->PrintError("Kunne ikke finne pakken.");
			$db->Disconnect();
			die();
		}


		$schemaInstance = $_REQUEST['schemainstance'];
		if ($schemaInstance == '') 
			$schemaInstance = 0;

		if ( $_REQUEST['schemainstance'] <> '' ){
			$currentschemainstance = $_REQUEST['currentschemainstance'];
			
			/* Find schema instance */
			$sqlStr = 'SELECT schematype,schemarevision,packet_id FROM altinn_schema WHERE instance_id=' . $currentschemainstance;

			if ($rs = $db->Query( $sqlStr )){
				if ( $row = $db->NextRow( $rs )){

					/* If this is a draft we should ask if the user wants to send the packet to AltInn.
					 * This is handeled by another page, so we redirect there. */

					if ( $status == -1 ) {

						$layout->PrintError("Kunne ikke hente status p� denne pakken.");
						$db->EndQuery( $rs );
						$db->Disconnect();

						die();
					}//if

					$schema = new Schema($db, $lodo, $config, $layout, $row['schematype'], $row['schemarevision'], $year);
					$schema->ReadSchemaForm();

					$schema->SaveSchema( $currentschemainstance, $row['packet_id'] );


					/* Continue */

					if ( $status == 0 ){
						if ( $_REQUEST['send']<>'' ){

							$url = $lodo->LodoUrlGet( '', $lodo->LODOURLTYPE_HREF, 'altinn.sendpacket' ) . '&packetid=' . $packageId;

							$str = '<html><head><META HTTP-EQUIV="REFRESH" CONTENT="0; URL='. $url . '"></head><body><a href="' . $url . '"></a></body></html>';

							echo($str);

							$db->EndQuery( $rs );

							$db->Disconnect();

							die();

						}
						elseif ($_REQUEST['draft']<>''){

							$url = $lodo->LodoUrlGet( '', $lodo->LODOURLTYPE_HREF, 'altinn.termins' );

							$str = '<html><head><META HTTP-EQUIV="REFRESH" CONTENT="0; URL='. $url . '"></head><body><a href="' . $url . '"></a></body></html>';

							echo($str);

							$db->EndQuery( $rs );

							$db->Disconnect();

							die();

						}//elseif
					}//if status=0

				}//if ( $row = $db->NextRow( $rs )

				$db->EndQuery( $rs );

			}//if ($rs = $db->Query( $sqlStr ))

		}//else if ( $_REQUEST['schemainstance'] <> '' )


		if ($_REQUEST['schemainstance'] < 0){

			$schemaInstance = $_REQUEST['currentschemainstance'];

			$schema = new Schema($db, $lodo, $config, $layout, 212, 3148, $year);

			$schema->LoadSchema( $schemaInstance );

			$schema->ToXML( $year, $termin );

		}
		else{

			if ( !$package->LoadPackage( $packageId ) ){

				$layout->PrintError("En feil oppstod under lasting av pakken.");

				$db->Disconnect();

				die();

			}

			$year = $package->package['year'];

			$terminItem = $package->package['termin'];
		}//else

	}//else EDit old package



	$schemaInstanceId = $_REQUEST['schemainstance'];

	if (!is_numeric($schemaInstanceId)) 
		$schemaInstanceId = 0;

	$layout->PrintHead( "AltInn" );

	if ( $lodo->inLodo ) {
		includeinc('top');
		includeinc('left');
	}

?>

<h1>Altinn skjema</h1>
<strong>Type:</strong> MVA<br/>
<strong>�r:</strong> <?php echo( $year );?><br/>
<strong>Termin:</strong> <?php echo( $terminstr ); ?><br/>

<style>
<!--
.m {background: #eeeeee;}
-->
</style>

<table>
<tr><th>Innhold</th></tr>

<tr valign="top">
<?php
	$schemaNumber = 0;
	$instanceNext = 0;
	$sqlStr = 'SELECT instance_id,schematype FROM altinn_schema WHERE packet_id=' . $package->package['packet_id'];
	
	//print ($sqlStr. "br");

	if ( $rs = $db->Query( $sqlStr ) ){
		while ( $row = $db->NextRow( $rs )){
			if ( $instanceNext == -1 )
				$instanceNext = $row['schematype'];

			if ( $schemaInstanceId < 1 ) 
				$schemaInstanceId = $row['instance_id'];

			if ( $schemaInstanceId == $row['instance_id'] ){
				$schemaNumber = $row['schematype'];
				$instanceNext = -1;
			}
		}//while

		$db->EndQuery( $rs );
	}//if
?>

<td>

<?php

  if ( $schemaNumber > 0 ){
		$schema = new Schema($db, $lodo, $config, $layout, $schemaNumber, $package->GetSchemaRevision($schemaNumber, $terminItem, $config->GetConfig($config->TERMIN_TYPE), $year));
		$schema->LoadSchema( $schemaInstanceId );

		/* Check if there is any other packets that has been sent */
		$hasBeenSent = false;		
		$oppgaveType=$HOVEDOPPGAVE;
		
		$sqlStr = 'SELECT COUNT(*) FROM altinn_packet WHERE customer_id=' . $lodo->lodoCurrentClientId . 
		' AND status>0' .
		' AND year=' . $year .
		' AND termin=' . $terminItem .
		' AND packettype=' . $_REQUEST['packettype'];
		
		
		
		if ( $rs = $db->Query( $sqlStr ) ){
			if ( $row = $db->NextRow( $rs ) ){
				$antall_packages = $row[0];
				$hasBeenSent = true;
			}
		
			$db->EndQuery( $rs );
		}
	

		//$sqlStr = 'SELECT status FROM altinn_packet WHERE status<>0 AND termin=' . $terminItem . ' AND termintype=' . $config->GetConfig($config->TERMIN_TYPE) . ' AND year=' . $year;
        //$oppgaveType=$HOVEDOPPGAVE;
        
		//if ( $rs = $db->Query( $sqlStr ) )//{
			//if ( $row = $db->NextRow( $rs )){
				//$hasBeenSent = true;
				//$oppgaveType= $KORIGERTOPPGAVE;

				//$schema->SetData( 5659, 3 );
			//}
			//$db->EndQuery( $rs );
		//}

		//print ("Anh: servername:".$_SERVER['SCRIPT_NAME']."<br>");
?>
    
	<h3><?php echo($schema->GetSchemaName()); ?></h3>

<!---form name="mvaf" action="<?php echo($_SERVER['SCRIPT_NAME'])?>" method="post"--->
<form name="mvaf" action="<?php print $MY_SELF; ?>" method="post" >

<?php

    $mvaSatsTable= array();
    //global $_sess, $db;
   
    $date = $_sess->get_session('LoginFormDate');
	$queryStr  = "SELECT AccountPlanID,Percent,VatID  FROM vat WHERE AccountPlanID>=2701 AND  AccountPlanID<=2704" .
			" AND ValidFrom <= '$date' AND ValidTo >= '$date' ORDER BY VatID asc";
	//print "$queryStr <br>\n";
	$rs = $db->Query($queryStr);
	
	 while ($row = $db->NextRow($rs )) {
	 	  $mvaSatsTable[$row['AccountPlanID']]=$row['Percent'];
	 	  //print ("account ". $row['AccountPlanID']. $row['Percent']. "br");
	 } 

	echo($lodo->LodoUrlSelf( '', $lodo->LODOURLTYPE_FORM ));
?>

<table>
<tr><td class="m">Antall sendte oppgaver</td>
	<td style="color:blue" align="right" > <?php  echo ($antall_packages); ?></td>
</tr>
<tr height="15"><td></td>
</tr>
<tr>
	<td class="m">Hovedoppgave</td>
	<td align="right"><input onClick="setOppgaveType(this)" type="radio" checked name="hoved" value="1"> </td>
</tr>
<tr>
	<td class="m">Korrigert oppgave</td>
	<td align="right"><input onClick="setOppgaveType(this)"  type="radio" name="korrigert" value="3"<?php if ( $orid_data_array['D5659'] == "3" ) {?> checked<?php }?><?php if ( $status>0 ) {?> disabled<?php }?>/></td>
</tr>
<tr>
	<td class="m">Tilleggsoppgave</td>
	<td align="right"><input onClick="setOppgaveType(this)" type="radio" name="tillegg" value="2"<?php if ( $orid_data_array['D5659'] == "2" ) {?> checked<?php }?><?php if ( $status>0 ) {?> disabled<?php }?>/></td>
</tr>
</table>

<table>
<tr>
	<td></td>
	<td></td>
	<th colspan ="1" width="100">Grunnlag</th>
	<th colspan ="2" width="120">Beregnet avgift</th>
</tr>
<tr>
	<td class="m"><b>1</b></td>
	<td class="m">Samlet omsetning og uttak innenfor og utenfor merverdiavgiftsloven (mva-loven)</td>
	<td><input size=7 name="D8446" value="<?php echo($orid_data_array[ 'D8446' ]);?>" align="right"<?php if ( $status>0 ) {?> disabled<?php }?> />,00</td>
</tr>
<tr>
	<td class="m"><b>2</b></td>
	<td class="m">Samlet omsetning og uttak innenfor mva-loven. Summen av post 3, 4, 5 og 6. Avgift ikke medregnet</td>

	<td><input size=7 name="D10095" value="<?php echo( $orid_data_array[ 'D10095' ] );?>" align="right"<?php if ( $status>0 ) {?> disabled<?php }?> />,00</td>
</tr>
<tr>
	<td class="m"><b>3</b></td>
	<td class="m">Omsetning og uttak i post 2 som er fritatt for merverdiavgift</td>
	<td><input size=7 name="D10096" value="<?php echo( $orid_data_array[ 'D10096' ] );?>" align="right"<?php if ( $status>0 ) {?> disabled<?php }?>/>,00</td>
</tr>
<tr>
	<td class="m"><b>4</b></td>

	<td class="m">Omsetning og uttak i post 2 med h�y sats. -- avgiftsats i kontor 2701 --  </td>
	<td><input size=7 name="D10097" id="D10097" value="<?php echo($orid_data_array[ 'D10097' ]);?>" align="right"<?php if ( $status>0 ) {?> disabled<?php }?> />,00 <?php echo($mvaSatsTable['2701']); ?>%</td>
	<td width="5">+</td>
	<td align="left"><input size=7 class="norm" name="D10098" value="<?php echo($orid_data_array[ 'D10098' ]);?>" align="right"<?php if ( $status>0 ) {?> disabled<?php }?> />,00</td>
</tr>
<tr>
	<td class="m"><b>5</b></td>
	<td class="m">Omsetning og uttak i post 2 med middels sats. -- avgiftsats i kontor 2702 --</td>

	<td><input size=7 name="D20319" value="<?php echo($orid_data_array[ 'D20319' ]);?>" align="right"<?php if ( $status>0 ) {?> disabled<?php }?> />,00 <?php echo($mvaSatsTable['2702']); ?>%</td>
	<td width="5">+</td>
	<td align="left"><input size=7 name="D20320" value="<?php echo($orid_data_array[ 'D20320' ]);?>" align="right"<?php if ( $status>0 ) {?> disabled<?php }?>/>,00</td>
</tr>
<tr>
	<td class="m"><b>6</b></td>
	<td class="m">Omsetning og uttak i post 2 med lav sats. -- avgiftsats i kontor 2703 --</td>
	<td><input size=7 name="D14360" value="<?php echo($orid_data_array[ 'D14360' ]);?>" align="right"<?php if ( $status>0 ) {?> disabled<?php }?>/>,00 <?php echo($mvaSatsTable['2703']); ?>%</td>
	<td width="5">+</td>
	<td align="left"><input size=7 name="D14361" value="<?php echo($orid_data_array[ 'D14361' ]);?>" align="right"<?php if ( $status>0 ) {?> disabled<?php }?>/>,00</td>
</tr>
<tr>
	<td class="m"><b>7</b></td>
	<td class="m">Tjenester kj�pt fra utlandet. -- avgiftsats i kontor 2704 --</td>
	<td><input size=7 name="D14362" value="<?php echo($orid_data_array[ 'D14362' ]);?>" align="right"<?php if ( $status>0 ) {?> disabled<?php }?>/>,00 <?php echo($mvaSatsTable['2704']); ?>%</td>
	<td width="5">+</td>
	<td align="left"><input size=7 name="D14363" value="<?php echo($orid_data_array[ 'D14363' ]);?>" align="right"<?php if ( $status>0 ) {?> disabled<?php }?>/>,00 </td>
</tr>
<tr>
	<td class="m"><b>8</b></td>
	<td class="m">Fradragsberettiget inng�ende avgift, h�y sats. -- avgiftsats i kontor 2711 --  </td>
	<td></td>
	<td width="5">-</td>
	<td align="left"><input size=7 name="D8450" value="<?php echo($orid_data_array[ 'D8450' ]);?>" align="right"<?php if ( $status>0 ) {?> disabled<?php }?>/>,00 <?php echo($mvaSatsTable['2701']); ?>%</td>
</tr>
<tr>
	<td class="m"><b>9</b></td>
	<td class="m">Fradragsberettiget inng�ende avgift, middels sats. -- avgiftsats i kontor 2712 --  </td>
	<td></td>
	<td width="5">-</td>
	<td align="left"><input size=7 name="D20322" value="<?php echo($orid_data_array[ 'D20322' ]);?>" align="right"<?php if ( $status>0 ) {?> disabled<?php }?>/>,00 <?php echo($mvaSatsTable['2702']); ?>%</td>
</tr>
<tr>
	<td class="m"><b>10</b></td>
	<td class="m">Fradragberettiget inng�ende avgift, lav sats. -- avgiftsats i kontor 2713 --  </td>
	<td></td>
	<td width="5">-</td>
	<td align="left"><input size=7 name="D14364" value="<?php echo($orid_data_array[ 'D14364' ]);?>" align="right"<?php if ( $status>0 ) {?> disabled<?php }?>/>,00 <?php echo($mvaSatsTable['2703']); ?>%</td>
</tr>
<tr>
	<td class="m"><b>11</b></td>
	<td class="m">Avgift til gode</td>
	<td></td>
	<td>=</td>
	<td align="left"><input size=7 name="D8452" value="<?php echo($orid_data_array[ 'D8452' ]);?>" align="right" disabled/>,00</td>
</tr>
<tr>
	<td class="m"><b></b></td>

	<td class="m">Avgift � betale</td>
	<td></td>
	<td>=</td>
	<td align="left"><input size=7 name="D8453" value="<?php echo($orid_data_array[ 'D8453' ]);?>" align="right" disabled/>,00</td>
</tr>
<tr height="20"><td colspan="4"><td></tr>
<tr><td colspan="4">Tilleggsopplysninger:</td></tr>
<tr height="20"><td colspan="3"><input size=106 name="D19684" /></td></tr>
<tr height="20"><td colspan="4"></td></tr>
<tr><td colspan="2" style="color:blue" width="200" name="message" id="message">Tast inn verdier og trykk p� beregn knappen.</td></tr>
</table>

<br/><br/>

<?php if ($status == 0) {?><input type="button" name="nop" value="Beregn" onClick="javascript:Calculate();" />&#160; 
<?php }?>

<input type="hidden" name="t" value="altinn.sendpacket_ge"/>
<input type="hidden" name="schemainstance" value="<?php echo($instanceNext);?>"/>
<input type="hidden" name="currentschemainstance" value="<?php echo($schemaInstanceId);?>"/>
<input type="hidden" name="packetid" value="<?php echo($packageId);?>"/>
<input type="hidden" name="D10094" value="<?php echo($year);?>"/>
<input type="hidden" name="D10092" value="Termin Type"/>
<input type="hidden" name="D10093" value="<?php echo("0".$terminItem."4");?>"/>
<input type="hidden" name="D5659" value="<?php echo($oppgaveType);?>"/>
<input type="hidden" name="schemacontrol" value="Y"/>
<input type="hidden" name="fagsystemtype" value="1"/>
<input type="hidden" name="READYTOSEND" value="true"/>

<input type="hidden" name="SATS2701" value="<?php echo($mvaSatsTable['2701']); ?>"/>
<input type="hidden" name="SATS2702" value="<?php echo($mvaSatsTable['2702']); ?>"/>
<input type="hidden" name="SATS2703" value="<?php echo($mvaSatsTable['2703']); ?>"/>
<input type="hidden" name="SATS2704" value="<?php echo($mvaSatsTable['2704']); ?>"/>
<input type="submit" name="send" value="Send til Altinn" <?php if (!$send_enabled) {?> disabled<?php }?> />

<?php if ($status < 1) { ?>&#160; &#160; &#160; 
   <input type="button" name="delete" value="Slett" onclick="clearFields();"><?php }?>

</form>

<p><a href="<?php echo($lodo->LodoUrlGet( '', $lodo->LODOURLTYPE_HREF,'altinn.schema_general' )); ?>">Tilbake til terminoversikt</a></p>

<?php
	}//if  ( $schemaNumber > 0 ) 
	else $layout->PrintError('Fant ikke det aktuelle skjemaet i pakken.');
	
	$db->Disconnect();
	?>
</td>
</tr>
</table>

</html>

=======
<<<<<<< .mine
<html>
<head>
  <script language="JavaScript">
  var send_enabled=false;
<!--//
function Calculate()
{   
    var error='Alle poster som merkes r�dt er feil, m� rette p� dem f�r sending.<br />Summen av post 3, 4, 5 og 6 skal v�re lik post 2.';
    var readyToSend=document.forms.mvaf.READYTOSEND.value;
    var ready='Skjema er klar for sending.';
    

    //Accountplan 2701
    if (document.forms.mvaf.D10097.value!= '' && document.forms.mvaf.D10097.value!= '0') {
		tmp = parseInt(document.forms.mvaf.D10097.value) * parseInt(document.forms.mvaf.SATS2701.value) / 100;
			
		document.forms.mvaf.D10098.style.color = "black";
		if ( document.forms.mvaf.D10098.value == '' ||  document.forms.mvaf.D10098.value == '0') 
		     document.forms.mvaf.D10098.value = tmp;
		else if ( parseInt(document.forms.mvaf.D10098.value) != tmp) {
		    document.forms.mvaf.D10098.style.color = "red";
		   
		    if (document.all){
		        document.all('message').style.color='red';
		    	document.all('message').innerText=error;
		    }else {
		        var obj=document.getElementById ("message");
		        if (obj.innerText) {
		            obj.style.color='red';
		        	obj.innerText=error;
		        }if(obj.innerHTML) {
		            obj.style.color='red';
		        	obj.innerHTML=error;
		        }
		    
		    }
		    
		    readyToSend=false;
		    
		}
	}
    
    //Accountplan 2702
    if (document.forms.mvaf.D20319.value != '' && document.forms.mvaf.D20319.value!= '0') {
		tmp = parseInt(document.forms.mvaf.D20319.value) * parseInt(document.forms.mvaf.SATS2702.value) / 100;
		document.forms.mvaf.D20320.style.color = "black";
		if (document.forms.mvaf.D20320.value == '' || document.forms.mvaf.D20320.value == '0' ) 
		    document.forms.mvaf.D20320.value = tmp;
		else if ( parseInt(document.forms.mvaf.D20320.value) != tmp) {
		    document.forms.mvaf.D20320.style.color = "red";
		    
		    if (document.all){
		        document.all('message').style.color='red';
		    	document.all('message').innerText=error;
		    }else {
		        var obj=document.getElementById ("message");
		        if (obj.innerText) {
		            obj.style.color='red';
		        	obj.innerText=error;
		        }if(obj.innerHTML) {
		            obj.style.color='red';
		        	obj.innerHTML=error;
		        }
		    
		    }
		    
		    readyToSend=false;
		}
     }

    //Accountplan 2703
    if (document.forms.mvaf.D14360.value != '' && document.forms.mvaf.D14360.value != '0') {
		tmp = parseInt(document.forms.mvaf.D14360.value) * parseInt(document.forms.mvaf.SATS2703.value) / 100;
		document.forms.mvaf.D14361.style.color = "black";
		if (document.forms.mvaf.D14361.value == '' || document.forms.mvaf.D14361.value == '0' )
		   document.forms.mvaf.D14361.value = tmp;
		else if ( parseInt(document.forms.mvaf.D14361.value) != tmp) {
			document.forms.mvaf.D14361.style.color = "red";
			
			if (document.all){
		        document.all('message').style.color='red';
		    	document.all('message').innerText=error;
		    }else {
		        var obj=document.getElementById ("message");
		        if (obj.innerText) {
		            obj.style.color='red';
		        	obj.innerText=error;
		        }if(obj.innerHTML) {
		            obj.style.color='red';
		        	obj.innerHTML=error;
		        }
		    
		    }
		    
		    readyToSend=false;
	    }
	}

    //Accountplan 2704 not used
    if (document.forms.mvaf.D14362.value != '' && document.forms.mvaf.D14362.value != '0') {
		tmp = parseInt(document.forms.mvaf.D14362.value) * parseInt(document.forms.mvaf.SATS2704.value) / 100;
			document.forms.mvaf.D14363.style.color = "black";
		if (document.forms.mvaf.D14363.value == '' || document.forms.mvaf.D14363.value == '0' ) 
		    document.forms.mvaf.D14363.value = tmp;
		else if ( parseInt(document.forms.mvaf.D14363.value) != tmp) {
		    document.forms.mvaf.D14363.style.color = "red";
		    
		    if (document.all){
		        document.all('message').style.color='red';
		    	document.all('message').innerText=error;
		    }else {
		        var obj=document.getElementById ("message");
		        if (obj.innerText) {
		            obj.style.color='red';
		        	obj.innerText=error;
		        }if(obj.innerHTML) {
		            obj.style.color='red';
		        	obj.innerHTML=error;
		        }
		    
		    }
		    
		    readyToSend=false;
		}
	}

    base=0;
	if (document.forms.mvaf.D10096.value!= '')
		base += parseInt(document.forms.mvaf.D10096.value);
    if (document.forms.mvaf.D10097.value!= '')
		base += parseInt(document.forms.mvaf.D10097.value);
	if (document.forms.mvaf.D20319.value!= '')
		base += parseInt(document.forms.mvaf.D20319.value);
	if (document.forms.mvaf.D14360.value!= '')
		base += parseInt(document.forms.mvaf.D14360.value);

	document.forms.mvaf.D8446.style.color = "black";
	if (document.forms.mvaf.D8446.value == '' || document.forms.mvaf.D8446.value == '0' ) 
		document.forms.mvaf.D8446.value = base;
	else if ( parseInt(document.forms.mvaf.D8446.value) != base) {
		document.forms.mvaf.D8446.style.color = "red";
		
		if (document.all){
		        document.all('message').style.color='red';
		    	document.all('message').innerText=error;
		    }else {
		        var obj=document.getElementById ("message");
		        if (obj.innerText) {
		            obj.style.color='red';
		        	obj.innerText=error;
		        }if(obj.innerHTML) {
		            obj.style.color='red';
		        	obj.innerHTML=error;
		        }
		    
		    }
    }

	document.forms.mvaf.D10095.style.color = "black";
	if (document.forms.mvaf.D10095.value == '' || document.forms.mvaf.D10095.value == '0') 
		document.forms.mvaf.D10095.value = base;
	else if ( parseInt(document.forms.mvaf.D10095.value) != base) {
		document.forms.mvaf.D10095.style.color = "red";
		
		if (document.all){
		        document.all('message').style.color='red';
		    	document.all('message').innerText=error;
		 }else {
	        var obj=document.getElementById ("message");
	        if (obj.innerText) {
	            obj.style.color='red';
	        	obj.innerText=error;
	        }if(obj.innerHTML) {
	            obj.style.color='red';
	        	obj.innerHTML=error;
	        }
	    
	    }
		    
		readyToSend=false;
		
	}
    
    if (document.forms.mvaf.D10098.value != '')
		tmp = parseInt(document.forms.mvaf.D10098.value);
	if (document.forms.mvaf.D20320.value != '')
		tmp += parseInt(document.forms.mvaf.D20320.value);
	if (document.forms.mvaf.D14361.value != '')
		tmp += parseInt(document.forms.mvaf.D14361.value);
	if (document.forms.mvaf.D14363.value != '')
		tmp += parseInt(document.forms.mvaf.D14363.value);
	if (document.forms.mvaf.D8450.value != '')
		tmp -= parseInt(document.forms.mvaf.D8450.value);
    if (document.forms.mvaf.D20322.value != '')
		tmp -= parseInt(document.forms.mvaf.D20322.value);
	if (document.forms.mvaf.D14364.value != '')
		tmp -= parseInt(document.forms.mvaf.D14364.value);

	mtmp = parseInt(tmp);
	
	if (mtmp >= 0) {
		document.forms.mvaf.D8452.value = 0;
		document.forms.mvaf.D8453.value = mtmp;
	}
	else {
		document.forms.mvaf.D8452.value = mtmp * -1;
		document.forms.mvaf.D8453.value = 0;
	}
	
   if (readyToSend) {
       send_enabled=true;
       document.forms.mvaf.send.disabled=false;
       
   	   if (document.all){
		   document.all('message').style.color='blue';
		   document.all('message').innerText=ready;
	    }else {
	        var obj=document.getElementById ("message");
	        if (obj.innerText) {
	            obj.style.color='blue';
	        	obj.innerText=ready;
	        }if(obj.innerHTML) {
	            obj.style.color='blue';
	        	obj.innerHTML=ready;
	        }
	    
	    }
   }//if
   
   document.forms.mvaf.READYTOSEND.value=readyToSend;
	
}

function clearFields(){
	document.forms.mvaf.D10097.value='';
	document.forms.mvaf.D10098.value='';
	document.forms.mvaf.D20319.value='';
	document.forms.mvaf.D20320.value='';
	document.forms.mvaf.D14360.value='';
	document.forms.mvaf.D14361.value='';
	document.forms.mvaf.D14362.value='';
	document.forms.mvaf.D14363.value='';
	
	document.forms.mvaf.D10096.value='';
	document.forms.mvaf.D10097.value='';
	document.forms.mvaf.D20319.value='';
	document.forms.mvaf.D14360.value='';
	
	document.forms.mvaf.D8446.value='';
	document.forms.mvaf.D10095.value='';
	
	document.forms.mvaf.D10098.value='';
	document.forms.mvaf.D20320.value='';
	document.forms.mvaf.D14361.value='';
	document.forms.mvaf.D14363.value='';
	document.forms.mvaf.D8450.value='';
    document.forms.mvaf.D20322.value='';
	document.forms.mvaf.D14364.value='';
	
	document.forms.mvaf.D8452.value='';
	document.forms.mvaf.D8453.value='';
	
	send_enabled=false;
    document.forms.mvaf.send.disabled=true;
    document.forms.mvaf.READYTOSEND.value=true;
	
}

function setFagSystemType(obj){
	document.forms.mvaf.fagsystemtype.value=obj.value;
	//alert(document.forms.mvaf.fagsystemtype.value);
}


function setOppgaveType(obj){
    activeOppgave=document.forms.mvaf.D5659.value;
    //alert(activeOppgave);
    if (parseInt(activeOppgave)==1)
       document.forms.mvaf.hoved.checked=false;
    else if (parseInt(activeOppgave)==2)
       document.forms.mvaf.tillegg.checked=false;
    else if (parseInt(activeOppgave)==3) {
       document.forms.mvaf.korrigert.checked=false;
       //alert(3);
    }
        
    document.forms.mvaf.D5659.value=obj.value; 
    
    if (parseInt(obj.value)==1)
         document.forms.mvaf.hoved.checked=true;
    else if (parseInt(obj.value)==2)
       document.forms.mvaf.tillegg.checked=true;
    else if (parseInt(obj.value)==3)
       document.forms.mvaf.korrigert.checked=true;   
}
//-->
</script>
 
  

</head>
<?php

	include_once $_SETUP['HOME_DIR']."/code/lodo/mvaavstemming/mvaavstemming.class";



/****************************************************************************

** Copyright (c) 2005 Actra AS.

** All rights reserved!

**

** Developed by Gunnar Skeid (gunnar@actra.no)

** Total changed by Anh Le

** Handles package editing.

****************************************************************************/

	require_once '_include/class_lodo.php';
	require_once '_include/class_layout.php';
	require_once '_include/class_database.php';
	require_once '_include/class_mva.php';
	require_once '_include/class_config.php';
	//require_once '_include/class_package.php';
	require_once '_include/class_schema.php';
	require_once '_include/class_package_ge.php';

	includelogic('mvaavstemming/mvaavstemming');

    $HOVEDOPPGAVE=1;
    $TILLEGGSOPPGAVE=2;
    $KORIGERTOPPGAVE=3;
    
    $mvaSatsTable= array();
    $antall_packages=0;
    $orid_data_array;
    $send_enabled=false;
    
    global $MY_SELF,$orid_data_array;
    

	$lodo = new lodo();

	$layout = new Layout( $lodo );

	$db = new Db( $lodo );
	
	if ( !$db->Connect() ) {
		$layout->PrintError("Kunne ikke koble til databasen.");
		die();
	}

	$mva = new Mva($db);
	$config = new Config($db);

	$package = new Package( array('db'=>$db, 'lodo'=>$lodo, 'config'=>$config,'layout'=>$layout) );
	//$package->setTermin (array ('terminitem'=>$_REQUEST['terminitem'],'terminlength'=>$_REQUEST['terminlength'] ));

	$year = $_REQUEST['year'];
	$termintype = $_REQUEST['termin'];
	$terminitem = $_REQUEST['terminitem'];
	$terminstr = $_REQUEST['terminstr'];
	$packageId = $_REQUEST['packageid'];
	
	//print ("termin: ". $termintype ."<br> terminItem:". $terminitem . "<br>");

	/* No packetid = create new packet */
	if ( !is_numeric( $packageId ) ){
		$status = 0;
		$year = $_REQUEST['year'];
		$terminItem = $_REQUEST['terminitem'];

		if ( !is_numeric($year) || !is_numeric($terminItem) ){
			$layout->PrintError('Det ble ikke oppgitt termin for pakken.');
			$db->Disconnect();
			die();
		}
		
		//Create a new package
		$packageId = $package->CreateNewPackage( $package->PACKAGETYPE_MVA, $year, $termintype, $terminItem );
		//print ("MVa anh packetid:".$packageId);
		
		$orid_data_array=$package->getOridArray();
		
		//print ("Anh Orid array:");
		//print_r($orid_data_array);
		
	}//if

	/* Edit old packet */
	else{
		if ($_REQUEST['delete']<>''){
			$sqlStr = 'DELETE FROM altinn_packet WHERE status=0 AND packet_id=' . $packageId;
			if ($db->Query($sqlStr)){
				$sqlStr = 'DELETE FROM altinn_schema WHERE packet_id=' . $packageId;

				$db->Query($sqlStr);

				$url = $lodo->LodoUrlGet( '', $lodo->LODOURLTYPE_HREF, 'altinn.termins' );

				$str = '<html><head><META HTTP-EQUIV="REFRESH" CONTENT="0; URL='. $url . '"></head><body><a href="' . $url . '">Neste</a></body></html>';

				echo($str);

				$db->EndQuery( $rs );

				$db->Disconnect();

				die();

			}else $layout->PrintWarning("Kunne ikke slette pakken!");

		}//if

		/* Get status */
		$status = -1;
		$sqlStr = 'SELECT status FROM altinn_packet WHERE packet_id=' . $packageId;

		if ( $rsStatus = $db->Query( $sqlStr )){
			if ( $rowStatus = $db->NextRow( $rsStatus ) )
				$status = $rowStatus['status'];

			$db->EndQuery( $rsStatus );
		}

		if ($status == -1){
			$layout->PrintError("Kunne ikke finne pakken.");
			$db->Disconnect();
			die();
		}


		$schemaInstance = $_REQUEST['schemainstance'];
		if ($schemaInstance == '') 
			$schemaInstance = 0;

		if ( $_REQUEST['schemainstance'] <> '' ){
			$currentschemainstance = $_REQUEST['currentschemainstance'];
			
			/* Find schema instance */
			$sqlStr = 'SELECT schematype,schemarevision,packet_id FROM altinn_schema WHERE instance_id=' . $currentschemainstance;

			if ($rs = $db->Query( $sqlStr )){
				if ( $row = $db->NextRow( $rs )){

					/* If this is a draft we should ask if the user wants to send the packet to AltInn.
					 * This is handeled by another page, so we redirect there. */

					if ( $status == -1 ) {

						$layout->PrintError("Kunne ikke hente status p� denne pakken.");
						$db->EndQuery( $rs );
						$db->Disconnect();

						die();
					}//if

					$schema = new Schema($db, $lodo, $config, $layout, $row['schematype'], $row['schemarevision'], $year);
					$schema->ReadSchemaForm();

					$schema->SaveSchema( $currentschemainstance, $row['packet_id'] );


					/* Continue */

					if ( $status == 0 ){
						if ( $_REQUEST['send']<>'' ){

							$url = $lodo->LodoUrlGet( '', $lodo->LODOURLTYPE_HREF, 'altinn.sendpacket' ) . '&packetid=' . $packageId;

							$str = '<html><head><META HTTP-EQUIV="REFRESH" CONTENT="0; URL='. $url . '"></head><body><a href="' . $url . '"></a></body></html>';

							echo($str);

							$db->EndQuery( $rs );

							$db->Disconnect();

							die();

						}
						elseif ($_REQUEST['draft']<>''){

							$url = $lodo->LodoUrlGet( '', $lodo->LODOURLTYPE_HREF, 'altinn.termins' );

							$str = '<html><head><META HTTP-EQUIV="REFRESH" CONTENT="0; URL='. $url . '"></head><body><a href="' . $url . '"></a></body></html>';

							echo($str);

							$db->EndQuery( $rs );

							$db->Disconnect();

							die();

						}//elseif
					}//if status=0

				}//if ( $row = $db->NextRow( $rs )

				$db->EndQuery( $rs );

			}//if ($rs = $db->Query( $sqlStr ))

		}//else if ( $_REQUEST['schemainstance'] <> '' )


		if ($_REQUEST['schemainstance'] < 0){

			$schemaInstance = $_REQUEST['currentschemainstance'];

			$schema = new Schema($db, $lodo, $config, $layout, 212, 3148, $year);

			$schema->LoadSchema( $schemaInstance );

			$schema->ToXML( $year, $termin );

		}
		else{

			if ( !$package->LoadPackage( $packageId ) ){

				$layout->PrintError("En feil oppstod under lasting av pakken.");

				$db->Disconnect();

				die();

			}

			$year = $package->package['year'];

			$terminItem = $package->package['termin'];
		}//else

	}//else EDit old package



	$schemaInstanceId = $_REQUEST['schemainstance'];

	if (!is_numeric($schemaInstanceId)) 
		$schemaInstanceId = 0;

	$layout->PrintHead( "AltInn" );

	if ( $lodo->inLodo ) {
		includeinc('top')
		includeinc('left')
	}

?>

<h1>Altinn skjema</h1>
<strong>Type:</strong> MVA<br/>
<strong>�r:</strong> <?php echo( $year );?><br/>
<strong>Termin:</strong> <?php echo( $terminstr ); ?><br/>

<style>
<!--
.m {background: #eeeeee;}
-->
</style>

<table>
<tr><th>Innhold</th></tr>

<tr valign="top">
<?php
	$schemaNumber = 0;
	$instanceNext = 0;
	$sqlStr = 'SELECT instance_id,schematype FROM altinn_schema WHERE packet_id=' . $package->package['packet_id'];
	
	//print ($sqlStr. "br");

	if ( $rs = $db->Query( $sqlStr ) ){
		while ( $row = $db->NextRow( $rs )){
			if ( $instanceNext == -1 )
				$instanceNext = $row['schematype'];

			if ( $schemaInstanceId < 1 ) 
				$schemaInstanceId = $row['instance_id'];

			if ( $schemaInstanceId == $row['instance_id'] ){
				$schemaNumber = $row['schematype'];
				$instanceNext = -1;
			}
		}//while

		$db->EndQuery( $rs );
	}//if
?>

<td>

<?php

  if ( $schemaNumber > 0 ){
		$schema = new Schema($db, $lodo, $config, $layout, $schemaNumber, $package->GetSchemaRevision($schemaNumber, $terminItem, $config->GetConfig($config->TERMIN_TYPE), $year));
		$schema->LoadSchema( $schemaInstanceId );

		/* Check if there is any other packets that has been sent */
		$hasBeenSent = false;		
		$oppgaveType=$HOVEDOPPGAVE;
		
		$sqlStr = 'SELECT COUNT(*) FROM altinn_packet WHERE customer_id=' . $lodo->lodoCurrentClientId . 
		' AND status>0' .
		' AND year=' . $year .
		' AND termin=' . $terminItem .
		' AND packettype=' . $_REQUEST['packettype'];
		
		
		
		if ( $rs = $db->Query( $sqlStr ) ){
			if ( $row = $db->NextRow( $rs ) ){
				$antall_packages = $row[0];
				$hasBeenSent = true;
			}
		
			$db->EndQuery( $rs );
		}
	

		//$sqlStr = 'SELECT status FROM altinn_packet WHERE status<>0 AND termin=' . $terminItem . ' AND termintype=' . $config->GetConfig($config->TERMIN_TYPE) . ' AND year=' . $year;
        //$oppgaveType=$HOVEDOPPGAVE;
        
		//if ( $rs = $db->Query( $sqlStr ) )//{
			//if ( $row = $db->NextRow( $rs )){
				//$hasBeenSent = true;
				//$oppgaveType= $KORIGERTOPPGAVE;

				//$schema->SetData( 5659, 3 );
			//}
			//$db->EndQuery( $rs );
		//}

		//print ("Anh: servername:".$_SERVER['SCRIPT_NAME']."<br>");
?>
    
	<h3><?php echo($schema->GetSchemaName()); ?></h3>

<!---form name="mvaf" action="<?php echo($_SERVER['SCRIPT_NAME'])?>" method="post"--->
<form name="mvaf" action="<?php print $MY_SELF; ?>" method="post" >

<?php

    $mvaSatsTable= array();
    //global $_sess, $db;
   
    $date = $_lib['sess']->get_session('LoginFormDate');
	$queryStr  = "SELECT AccountPlanID,Percent,VatID  FROM vat WHERE AccountPlanID>=2701 AND  AccountPlanID<=2704" .
			" AND ValidFrom <= '$date' AND ValidTo >= '$date' ORDER BY VatID asc";
	//print "$queryStr <br>\n";
	$rs = $db->Query($queryStr);
	
	 while ($row = $db->NextRow($rs )) {
	 	  $mvaSatsTable[$row['AccountPlanID']]=$row['Percent'];
	 	  //print ("account ". $row['AccountPlanID']. $row['Percent']. "br");
	 } 

	echo($lodo->LodoUrlSelf( '', $lodo->LODOURLTYPE_FORM ));
?>

<table>
<tr><td class="m">Antall sendte oppgaver</td>
	<td style="color:blue" align="right" > <?php  echo ($antall_packages); ?></td>
</tr>
<tr height="15"><td></td>
</tr>
<tr>
	<td class="m">Hovedoppgave</td>
	<td align="right"><input onClick="setOppgaveType(this)" type="radio" checked name="hoved" value="1"> </td>
</tr>
<tr>
	<td class="m">Korrigert oppgave</td>
	<td align="right"><input onClick="setOppgaveType(this)"  type="radio" name="korrigert" value="3"<?php if ( $orid_data_array['D5659'] == "3" ) {?> checked<?php }?><?php if ( $status>0 ) {?> disabled<?php }?>/></td>
</tr>
<tr>
	<td class="m">Tilleggsoppgave</td>
	<td align="right"><input onClick="setOppgaveType(this)" type="radio" name="tillegg" value="2"<?php if ( $orid_data_array['D5659'] == "2" ) {?> checked<?php }?><?php if ( $status>0 ) {?> disabled<?php }?>/></td>
</tr>
</table>

<table>
<tr>
	<td></td>
	<td></td>
	<th colspan ="1" width="100">Grunnlag</th>
	<th colspan ="2" width="120">Beregnet avgift</th>
</tr>
<tr>
	<td class="m"><b>1</b></td>
	<td class="m">Samlet omsetning og uttak innenfor og utenfor merverdiavgiftsloven (mva-loven)</td>
	<td><input size=7 name="D8446" value="<?php echo($orid_data_array[ 'D8446' ]);?>" align="right"<?php if ( $status>0 ) {?> disabled<?php }?> />,00</td>
</tr>
<tr>
	<td class="m"><b>2</b></td>
	<td class="m">Samlet omsetning og uttak innenfor mva-loven. Summen av post 3, 4, 5 og 6. Avgift ikke medregnet</td>

	<td><input size=7 name="D10095" value="<?php echo( $orid_data_array[ 'D10095' ] );?>" align="right"<?php if ( $status>0 ) {?> disabled<?php }?> />,00</td>
</tr>
<tr>
	<td class="m"><b>3</b></td>
	<td class="m">Omsetning og uttak i post 2 som er fritatt for merverdiavgift</td>
	<td><input size=7 name="D10096" value="<?php echo( $orid_data_array[ 'D10096' ] );?>" align="right"<?php if ( $status>0 ) {?> disabled<?php }?>/>,00</td>
</tr>
<tr>
	<td class="m"><b>4</b></td>

	<td class="m">Omsetning og uttak i post 2 med h�y sats. -- avgiftsats i kontor 2701 --  </td>
	<td><input size=7 name="D10097" id="D10097" value="<?php echo($orid_data_array[ 'D10097' ]);?>" align="right"<?php if ( $status>0 ) {?> disabled<?php }?> />,00 <?php echo($mvaSatsTable['2701']); ?>%</td>
	<td width="5">+</td>
	<td align="left"><input size=7 class="norm" name="D10098" value="<?php echo($orid_data_array[ 'D10098' ]);?>" align="right"<?php if ( $status>0 ) {?> disabled<?php }?> />,00</td>
</tr>
<tr>
	<td class="m"><b>5</b></td>
	<td class="m">Omsetning og uttak i post 2 med middels sats. -- avgiftsats i kontor 2702 --</td>

	<td><input size=7 name="D20319" value="<?php echo($orid_data_array[ 'D20319' ]);?>" align="right"<?php if ( $status>0 ) {?> disabled<?php }?> />,00 <?php echo($mvaSatsTable['2702']); ?>%</td>
	<td width="5">+</td>
	<td align="left"><input size=7 name="D20320" value="<?php echo($orid_data_array[ 'D20320' ]);?>" align="right"<?php if ( $status>0 ) {?> disabled<?php }?>/>,00</td>
</tr>
<tr>
	<td class="m"><b>6</b></td>
	<td class="m">Omsetning og uttak i post 2 med lav sats. -- avgiftsats i kontor 2703 --</td>
	<td><input size=7 name="D14360" value="<?php echo($orid_data_array[ 'D14360' ]);?>" align="right"<?php if ( $status>0 ) {?> disabled<?php }?>/>,00 <?php echo($mvaSatsTable['2703']); ?>%</td>
	<td width="5">+</td>
	<td align="left"><input size=7 name="D14361" value="<?php echo($orid_data_array[ 'D14361' ]);?>" align="right"<?php if ( $status>0 ) {?> disabled<?php }?>/>,00</td>
</tr>
<tr>
	<td class="m"><b>7</b></td>
	<td class="m">Tjenester kj�pt fra utlandet. -- avgiftsats i kontor 2704 --</td>
	<td><input size=7 name="D14362" value="<?php echo($orid_data_array[ 'D14362' ]);?>" align="right"<?php if ( $status>0 ) {?> disabled<?php }?>/>,00 <?php echo($mvaSatsTable['2704']); ?>%</td>
	<td width="5">+</td>
	<td align="left"><input size=7 name="D14363" value="<?php echo($orid_data_array[ 'D14363' ]);?>" align="right"<?php if ( $status>0 ) {?> disabled<?php }?>/>,00 </td>
</tr>
<tr>
	<td class="m"><b>8</b></td>
	<td class="m">Fradragsberettiget inng�ende avgift, h�y sats. -- avgiftsats i kontor 2711 --  </td>
	<td></td>
	<td width="5">-</td>
	<td align="left"><input size=7 name="D8450" value="<?php echo($orid_data_array[ 'D8450' ]);?>" align="right"<?php if ( $status>0 ) {?> disabled<?php }?>/>,00 <?php echo($mvaSatsTable['2701']); ?>%</td>
</tr>
<tr>
	<td class="m"><b>9</b></td>
	<td class="m">Fradragsberettiget inng�ende avgift, middels sats. -- avgiftsats i kontor 2712 --  </td>
	<td></td>
	<td width="5">-</td>
	<td align="left"><input size=7 name="D20322" value="<?php echo($orid_data_array[ 'D20322' ]);?>" align="right"<?php if ( $status>0 ) {?> disabled<?php }?>/>,00 <?php echo($mvaSatsTable['2702']); ?>%</td>
</tr>
<tr>
	<td class="m"><b>10</b></td>
	<td class="m">Fradragberettiget inng�ende avgift, lav sats. -- avgiftsats i kontor 2713 --  </td>
	<td></td>
	<td width="5">-</td>
	<td align="left"><input size=7 name="D14364" value="<?php echo($orid_data_array[ 'D14364' ]);?>" align="right"<?php if ( $status>0 ) {?> disabled<?php }?>/>,00 <?php echo($mvaSatsTable['2703']); ?>%</td>
</tr>
<tr>
	<td class="m"><b>11</b></td>
	<td class="m">Avgift til gode</td>
	<td></td>
	<td>=</td>
	<td align="left"><input size=7 name="D8452" value="<?php echo($orid_data_array[ 'D8452' ]);?>" align="right" disabled/>,00</td>
</tr>
<tr>
	<td class="m"><b></b></td>

	<td class="m">Avgift � betale</td>
	<td></td>
	<td>=</td>
	<td align="left"><input size=7 name="D8453" value="<?php echo($orid_data_array[ 'D8453' ]);?>" align="right" disabled/>,00</td>
</tr>
<tr height="20"><td colspan="4"><td></tr>
<tr><td colspan="4">Tilleggsopplysninger:</td></tr>
<tr height="20"><td colspan="3"><input size=106 name="D19684" /></td></tr>
<tr height="20"><td colspan="4"></td></tr>
<tr><td colspan="2" style="color:blue" width="200" name="message" id="message">Tast inn verdier og trykk p� beregn knappen.</td></tr>
</table>

<br/><br/>

<?php if ($status == 0) {?><input type="button" name="nop" value="Beregn" onClick="javascript:Calculate();" />&#160; 
<?php }?>

<input type="hidden" name="t" value="altinn.sendpacket_ge"/>
<input type="hidden" name="schemainstance" value="<?php echo($instanceNext);?>"/>
<input type="hidden" name="currentschemainstance" value="<?php echo($schemaInstanceId);?>"/>
<input type="hidden" name="packetid" value="<?php echo($packageId);?>"/>
<input type="hidden" name="D10094" value="<?php echo($year);?>"/>
<input type="hidden" name="D10092" value="Termin Type"/>
<input type="hidden" name="D10093" value="<?php echo("0".$terminItem."4");?>"/>
<input type="hidden" name="D5659" value="<?php echo($oppgaveType);?>"/>
<input type="hidden" name="schemacontrol" value="Y"/>
<input type="hidden" name="fagsystemtype" value="1"/>
<input type="hidden" name="READYTOSEND" value="true"/>

<input type="hidden" name="SATS2701" value="<?php echo($mvaSatsTable['2701']); ?>"/>
<input type="hidden" name="SATS2702" value="<?php echo($mvaSatsTable['2702']); ?>"/>
<input type="hidden" name="SATS2703" value="<?php echo($mvaSatsTable['2703']); ?>"/>
<input type="hidden" name="SATS2704" value="<?php echo($mvaSatsTable['2704']); ?>"/>
<input type="submit" name="send" value="Send til Altinn" <?php if (!$send_enabled) {?> disabled<?php }?> />

<?php if ($status < 1) { ?>&#160; &#160; &#160; 
   <input type="button" name="delete" value="Slett" onclick="clearFields();"><?php }?>

</form>

<p><a href="<?php echo($lodo->LodoUrlGet( '', $lodo->LODOURLTYPE_HREF,'altinn.schema_general' )); ?>">Tilbake til terminoversikt</a></p>

<?php
	}//if  ( $schemaNumber > 0 ) 
	else $layout->PrintError('Fant ikke det aktuelle skjemaet i pakken.');
	
	$db->Disconnect();
	?>
</td>
</tr>
</table>

</html>

=======
<html>
<head>
  <script language="JavaScript">
  var send_enabled=false;
<!--//
function Calculate()
{   
    var error='Alle poster som merkes r�dt er feil, m� rette p� dem f�r sending.<br />Summen av post 3, 4, 5 og 6 skal v�re lik post 2.';
    var readyToSend=document.forms.mvaf.READYTOSEND.value;
    var ready='Skjema er klar for sending.';
    

    //Accountplan 2701
    if (document.forms.mvaf.D10097.value!= '' && document.forms.mvaf.D10097.value!= '0') {
		tmp = parseInt(document.forms.mvaf.D10097.value) * parseInt(document.forms.mvaf.SATS2701.value) / 100;
			
		document.forms.mvaf.D10098.style.color = "black";
		if ( document.forms.mvaf.D10098.value == '' ||  document.forms.mvaf.D10098.value == '0') 
		     document.forms.mvaf.D10098.value = tmp;
		else if ( parseInt(document.forms.mvaf.D10098.value) != tmp) {
		    document.forms.mvaf.D10098.style.color = "red";
		   
		    if (document.all){
		        document.all('message').style.color='red';
		    	document.all('message').innerText=error;
		    }else {
		        var obj=document.getElementById ("message");
		        if (obj.innerText) {
		            obj.style.color='red';
		        	obj.innerText=error;
		        }if(obj.innerHTML) {
		            obj.style.color='red';
		        	obj.innerHTML=error;
		        }
		    
		    }
		    
		    readyToSend=false;
		    
		}
	}
    
    //Accountplan 2702
    if (document.forms.mvaf.D20319.value != '' && document.forms.mvaf.D20319.value!= '0') {
		tmp = parseInt(document.forms.mvaf.D20319.value) * parseInt(document.forms.mvaf.SATS2702.value) / 100;
		document.forms.mvaf.D20320.style.color = "black";
		if (document.forms.mvaf.D20320.value == '' || document.forms.mvaf.D20320.value == '0' ) 
		    document.forms.mvaf.D20320.value = tmp;
		else if ( parseInt(document.forms.mvaf.D20320.value) != tmp) {
		    document.forms.mvaf.D20320.style.color = "red";
		    
		    if (document.all){
		        document.all('message').style.color='red';
		    	document.all('message').innerText=error;
		    }else {
		        var obj=document.getElementById ("message");
		        if (obj.innerText) {
		            obj.style.color='red';
		        	obj.innerText=error;
		        }if(obj.innerHTML) {
		            obj.style.color='red';
		        	obj.innerHTML=error;
		        }
		    
		    }
		    
		    readyToSend=false;
		}
     }

    //Accountplan 2703
    if (document.forms.mvaf.D14360.value != '' && document.forms.mvaf.D14360.value != '0') {
		tmp = parseInt(document.forms.mvaf.D14360.value) * parseInt(document.forms.mvaf.SATS2703.value) / 100;
		document.forms.mvaf.D14361.style.color = "black";
		if (document.forms.mvaf.D14361.value == '' || document.forms.mvaf.D14361.value == '0' )
		   document.forms.mvaf.D14361.value = tmp;
		else if ( parseInt(document.forms.mvaf.D14361.value) != tmp) {
			document.forms.mvaf.D14361.style.color = "red";
			
			if (document.all){
		        document.all('message').style.color='red';
		    	document.all('message').innerText=error;
		    }else {
		        var obj=document.getElementById ("message");
		        if (obj.innerText) {
		            obj.style.color='red';
		        	obj.innerText=error;
		        }if(obj.innerHTML) {
		            obj.style.color='red';
		        	obj.innerHTML=error;
		        }
		    
		    }
		    
		    readyToSend=false;
	    }
	}

    //Accountplan 2704 not used
    if (document.forms.mvaf.D14362.value != '' && document.forms.mvaf.D14362.value != '0') {
		tmp = parseInt(document.forms.mvaf.D14362.value) * parseInt(document.forms.mvaf.SATS2704.value) / 100;
			document.forms.mvaf.D14363.style.color = "black";
		if (document.forms.mvaf.D14363.value == '' || document.forms.mvaf.D14363.value == '0' ) 
		    document.forms.mvaf.D14363.value = tmp;
		else if ( parseInt(document.forms.mvaf.D14363.value) != tmp) {
		    document.forms.mvaf.D14363.style.color = "red";
		    
		    if (document.all){
		        document.all('message').style.color='red';
		    	document.all('message').innerText=error;
		    }else {
		        var obj=document.getElementById ("message");
		        if (obj.innerText) {
		            obj.style.color='red';
		        	obj.innerText=error;
		        }if(obj.innerHTML) {
		            obj.style.color='red';
		        	obj.innerHTML=error;
		        }
		    
		    }
		    
		    readyToSend=false;
		}
	}

    base=0;
	if (document.forms.mvaf.D10096.value!= '')
		base += parseInt(document.forms.mvaf.D10096.value);
    if (document.forms.mvaf.D10097.value!= '')
		base += parseInt(document.forms.mvaf.D10097.value);
	if (document.forms.mvaf.D20319.value!= '')
		base += parseInt(document.forms.mvaf.D20319.value);
	if (document.forms.mvaf.D14360.value!= '')
		base += parseInt(document.forms.mvaf.D14360.value);

	document.forms.mvaf.D8446.style.color = "black";
	if (document.forms.mvaf.D8446.value == '' || document.forms.mvaf.D8446.value == '0' ) 
		document.forms.mvaf.D8446.value = base;
	else if ( parseInt(document.forms.mvaf.D8446.value) != base) {
		document.forms.mvaf.D8446.style.color = "red";
		
		if (document.all){
		        document.all('message').style.color='red';
		    	document.all('message').innerText=error;
		    }else {
		        var obj=document.getElementById ("message");
		        if (obj.innerText) {
		            obj.style.color='red';
		        	obj.innerText=error;
		        }if(obj.innerHTML) {
		            obj.style.color='red';
		        	obj.innerHTML=error;
		        }
		    
		    }
    }

	document.forms.mvaf.D10095.style.color = "black";
	if (document.forms.mvaf.D10095.value == '' || document.forms.mvaf.D10095.value == '0') 
		document.forms.mvaf.D10095.value = base;
	else if ( parseInt(document.forms.mvaf.D10095.value) != base) {
		document.forms.mvaf.D10095.style.color = "red";
		
		if (document.all){
		        document.all('message').style.color='red';
		    	document.all('message').innerText=error;
		 }else {
	        var obj=document.getElementById ("message");
	        if (obj.innerText) {
	            obj.style.color='red';
	        	obj.innerText=error;
	        }if(obj.innerHTML) {
	            obj.style.color='red';
	        	obj.innerHTML=error;
	        }
	    
	    }
		    
		readyToSend=false;
		
	}
    
    if (document.forms.mvaf.D10098.value != '')
		tmp = parseInt(document.forms.mvaf.D10098.value);
	if (document.forms.mvaf.D20320.value != '')
		tmp += parseInt(document.forms.mvaf.D20320.value);
	if (document.forms.mvaf.D14361.value != '')
		tmp += parseInt(document.forms.mvaf.D14361.value);
	if (document.forms.mvaf.D14363.value != '')
		tmp += parseInt(document.forms.mvaf.D14363.value);
	if (document.forms.mvaf.D8450.value != '')
		tmp -= parseInt(document.forms.mvaf.D8450.value);
    if (document.forms.mvaf.D20322.value != '')
		tmp -= parseInt(document.forms.mvaf.D20322.value);
	if (document.forms.mvaf.D14364.value != '')
		tmp -= parseInt(document.forms.mvaf.D14364.value);

	mtmp = parseInt(tmp);
	
	if (mtmp >= 0) {
		document.forms.mvaf.D8452.value = 0;
		document.forms.mvaf.D8453.value = mtmp;
	}
	else {
		document.forms.mvaf.D8452.value = mtmp * -1;
		document.forms.mvaf.D8453.value = 0;
	}
	
   if (readyToSend) {
       send_enabled=true;
       document.forms.mvaf.send.disabled=false;
       
   	   if (document.all){
		   document.all('message').style.color='blue';
		   document.all('message').innerText=ready;
	    }else {
	        var obj=document.getElementById ("message");
	        if (obj.innerText) {
	            obj.style.color='blue';
	        	obj.innerText=ready;
	        }if(obj.innerHTML) {
	            obj.style.color='blue';
	        	obj.innerHTML=ready;
	        }
	    
	    }
   }//if
   
   document.forms.mvaf.READYTOSEND.value=readyToSend;
	
}

function clearFields(){
	document.forms.mvaf.D10097.value='';
	document.forms.mvaf.D10098.value='';
	document.forms.mvaf.D20319.value='';
	document.forms.mvaf.D20320.value='';
	document.forms.mvaf.D14360.value='';
	document.forms.mvaf.D14361.value='';
	document.forms.mvaf.D14362.value='';
	document.forms.mvaf.D14363.value='';
	
	document.forms.mvaf.D10096.value='';
	document.forms.mvaf.D10097.value='';
	document.forms.mvaf.D20319.value='';
	document.forms.mvaf.D14360.value='';
	
	document.forms.mvaf.D8446.value='';
	document.forms.mvaf.D10095.value='';
	
	document.forms.mvaf.D10098.value='';
	document.forms.mvaf.D20320.value='';
	document.forms.mvaf.D14361.value='';
	document.forms.mvaf.D14363.value='';
	document.forms.mvaf.D8450.value='';
    document.forms.mvaf.D20322.value='';
	document.forms.mvaf.D14364.value='';
	
	document.forms.mvaf.D8452.value='';
	document.forms.mvaf.D8453.value='';
	
	send_enabled=false;
    document.forms.mvaf.send.disabled=true;
    document.forms.mvaf.READYTOSEND.value=true;
	
}

function setFagSystemType(obj){
	document.forms.mvaf.fagsystemtype.value=obj.value;
	//alert(document.forms.mvaf.fagsystemtype.value);
}


function setOppgaveType(obj){
    activeOppgave=document.forms.mvaf.D5659.value;
    //alert(activeOppgave);
    if (parseInt(activeOppgave)==1)
       document.forms.mvaf.hoved.checked=false;
    else if (parseInt(activeOppgave)==2)
       document.forms.mvaf.tillegg.checked=false;
    else if (parseInt(activeOppgave)==3) {
       document.forms.mvaf.korrigert.checked=false;
       //alert(3);
    }
        
    document.forms.mvaf.D5659.value=obj.value; 
    
    if (parseInt(obj.value)==1)
         document.forms.mvaf.hoved.checked=true;
    else if (parseInt(obj.value)==2)
       document.forms.mvaf.tillegg.checked=true;
    else if (parseInt(obj.value)==3)
       document.forms.mvaf.korrigert.checked=true;   
}
//-->
</script>
 
  

</head>
<?php

	include_once $_SETUP['HOME_DIR']."/code/lodo/mvaavstemming/mvaavstemming.class";



/****************************************************************************

** Copyright (c) 2005 Actra AS.

** All rights reserved!

**

** Developed by Gunnar Skeid (gunnar@actra.no)

** Total changed by Anh Le

** Handles package editing.

****************************************************************************/

	require_once '_include/class_lodo.php';
	require_once '_include/class_layout.php';
	require_once '_include/class_database.php';
	require_once '_include/class_mva.php';
	require_once '_include/class_config.php';
	//require_once '_include/class_package.php';
	require_once '_include/class_schema.php';
	require_once '_include/class_package_ge.php';

	include_once $_SETUP['HOME_DIR']."/code/lodo/mvaavstemming/mvaavstemming.class";

    $HOVEDOPPGAVE=1;
    $TILLEGGSOPPGAVE=2;
    $KORIGERTOPPGAVE=3;
    
    $mvaSatsTable= array();
    $antall_packages=0;
    $orid_data_array;
    $send_enabled=false;
    
    global $MY_SELF,$orid_data_array;
    

	$lodo = new lodo();

	$layout = new Layout( $lodo );

	$db = new Db( $lodo );
	
	if ( !$db->Connect() ) {
		$layout->PrintError("Kunne ikke koble til databasen.");
		die();
	}

	$mva = new Mva($db);
	$config = new Config($db);

	$package = new Package( array('db'=>$db, 'lodo'=>$lodo, 'config'=>$config,'layout'=>$layout) );
	//$package->setTermin (array ('terminitem'=>$_REQUEST['terminitem'],'terminlength'=>$_REQUEST['terminlength'] ));

	$year = $_REQUEST['year'];
	$termintype = $_REQUEST['termin'];
	$terminitem = $_REQUEST['terminitem'];
	$terminstr = $_REQUEST['terminstr'];
	$packageId = $_REQUEST['packageid'];
	
	//print ("termin: ". $termintype ."<br> terminItem:". $terminitem . "<br>");

	/* No packetid = create new packet */
	if ( !is_numeric( $packageId ) ){
		$status = 0;
		$year = $_REQUEST['year'];
		$terminItem = $_REQUEST['terminitem'];

		if ( !is_numeric($year) || !is_numeric($terminItem) ){
			$layout->PrintError('Det ble ikke oppgitt termin for pakken.');
			$db->Disconnect();
			die();
		}
		
		//Create a new package
		$packageId = $package->CreateNewPackage( $package->PACKAGETYPE_MVA, $year, $termintype, $terminItem );
		//print ("MVa anh packetid:".$packageId);
		
		$orid_data_array=$package->getOridArray();
		
		//print ("Anh Orid array:");
		//print_r($orid_data_array);
		
	}//if

	/* Edit old packet */
	else{
		if ($_REQUEST['delete']<>''){
			$sqlStr = 'DELETE FROM altinn_packet WHERE status=0 AND packet_id=' . $packageId;
			if ($db->Query($sqlStr)){
				$sqlStr = 'DELETE FROM altinn_schema WHERE packet_id=' . $packageId;

				$db->Query($sqlStr);

				$url = $lodo->LodoUrlGet( '', $lodo->LODOURLTYPE_HREF, 'altinn.termins' );

				$str = '<html><head><META HTTP-EQUIV="REFRESH" CONTENT="0; URL='. $url . '"></head><body><a href="' . $url . '">Neste</a></body></html>';

				echo($str);

				$db->EndQuery( $rs );

				$db->Disconnect();

				die();

			}else $layout->PrintWarning("Kunne ikke slette pakken!");

		}//if

		/* Get status */
		$status = -1;
		$sqlStr = 'SELECT status FROM altinn_packet WHERE packet_id=' . $packageId;

		if ( $rsStatus = $db->Query( $sqlStr )){
			if ( $rowStatus = $db->NextRow( $rsStatus ) )
				$status = $rowStatus['status'];

			$db->EndQuery( $rsStatus );
		}

		if ($status == -1){
			$layout->PrintError("Kunne ikke finne pakken.");
			$db->Disconnect();
			die();
		}


		$schemaInstance = $_REQUEST['schemainstance'];
		if ($schemaInstance == '') 
			$schemaInstance = 0;

		if ( $_REQUEST['schemainstance'] <> '' ){
			$currentschemainstance = $_REQUEST['currentschemainstance'];
			
			/* Find schema instance */
			$sqlStr = 'SELECT schematype,schemarevision,packet_id FROM altinn_schema WHERE instance_id=' . $currentschemainstance;

			if ($rs = $db->Query( $sqlStr )){
				if ( $row = $db->NextRow( $rs )){

					/* If this is a draft we should ask if the user wants to send the packet to AltInn.
					 * This is handeled by another page, so we redirect there. */

					if ( $status == -1 ) {

						$layout->PrintError("Kunne ikke hente status p� denne pakken.");
						$db->EndQuery( $rs );
						$db->Disconnect();

						die();
					}//if

					$schema = new Schema($db, $lodo, $config, $layout, $row['schematype'], $row['schemarevision'], $year);
					$schema->ReadSchemaForm();

					$schema->SaveSchema( $currentschemainstance, $row['packet_id'] );


					/* Continue */

					if ( $status == 0 ){
						if ( $_REQUEST['send']<>'' ){

							$url = $lodo->LodoUrlGet( '', $lodo->LODOURLTYPE_HREF, 'altinn.sendpacket' ) . '&packetid=' . $packageId;

							$str = '<html><head><META HTTP-EQUIV="REFRESH" CONTENT="0; URL='. $url . '"></head><body><a href="' . $url . '"></a></body></html>';

							echo($str);

							$db->EndQuery( $rs );

							$db->Disconnect();

							die();

						}
						elseif ($_REQUEST['draft']<>''){

							$url = $lodo->LodoUrlGet( '', $lodo->LODOURLTYPE_HREF, 'altinn.termins' );

							$str = '<html><head><META HTTP-EQUIV="REFRESH" CONTENT="0; URL='. $url . '"></head><body><a href="' . $url . '"></a></body></html>';

							echo($str);

							$db->EndQuery( $rs );

							$db->Disconnect();

							die();

						}//elseif
					}//if status=0

				}//if ( $row = $db->NextRow( $rs )

				$db->EndQuery( $rs );

			}//if ($rs = $db->Query( $sqlStr ))

		}//else if ( $_REQUEST['schemainstance'] <> '' )


		if ($_REQUEST['schemainstance'] < 0){

			$schemaInstance = $_REQUEST['currentschemainstance'];

			$schema = new Schema($db, $lodo, $config, $layout, 212, 3148, $year);

			$schema->LoadSchema( $schemaInstance );

			$schema->ToXML( $year, $termin );

		}
		else{

			if ( !$package->LoadPackage( $packageId ) ){

				$layout->PrintError("En feil oppstod under lasting av pakken.");

				$db->Disconnect();

				die();

			}

			$year = $package->package['year'];

			$terminItem = $package->package['termin'];
		}//else

	}//else EDit old package



	$schemaInstanceId = $_REQUEST['schemainstance'];

	if (!is_numeric($schemaInstanceId)) 
		$schemaInstanceId = 0;

	$layout->PrintHead( "AltInn" );

	if ( $lodo->inLodo ) {
		includeinc('head');
		includeinc('left');
	}

?>

<h1>Altinn skjema</h1>
<strong>Type:</strong> MVA<br/>
<strong>�r:</strong> <?php echo( $year );?><br/>
<strong>Termin:</strong> <?php echo( $terminstr ); ?><br/>

<style>
<!--
.m {background: #eeeeee;}
-->
</style>

<table>
<tr><th>Innhold</th></tr>

<tr valign="top">
<?php
	$schemaNumber = 0;
	$instanceNext = 0;
	$sqlStr = 'SELECT instance_id,schematype FROM altinn_schema WHERE packet_id=' . $package->package['packet_id'];
	
	//print ($sqlStr. "br");

	if ( $rs = $db->Query( $sqlStr ) ){
		while ( $row = $db->NextRow( $rs )){
			if ( $instanceNext == -1 )
				$instanceNext = $row['schematype'];

			if ( $schemaInstanceId < 1 ) 
				$schemaInstanceId = $row['instance_id'];

			if ( $schemaInstanceId == $row['instance_id'] ){
				$schemaNumber = $row['schematype'];
				$instanceNext = -1;
			}
		}//while

		$db->EndQuery( $rs );
	}//if
?>

<td>

<?php

  if ( $schemaNumber > 0 ){
		$schema = new Schema($db, $lodo, $config, $layout, $schemaNumber, $package->GetSchemaRevision($schemaNumber, $terminItem, $config->GetConfig($config->TERMIN_TYPE), $year));
		$schema->LoadSchema( $schemaInstanceId );

		/* Check if there is any other packets that has been sent */
		$hasBeenSent = false;		
		$oppgaveType=$HOVEDOPPGAVE;
		
		$sqlStr = 'SELECT COUNT(*) FROM altinn_packet WHERE customer_id=' . $lodo->lodoCurrentClientId . 
		' AND status>0' .
		' AND year=' . $year .
		' AND termin=' . $terminItem .
		' AND packettype=' . $_REQUEST['packettype'];
		
		
		
		if ( $rs = $db->Query( $sqlStr ) ){
			if ( $row = $db->NextRow( $rs ) ){
				$antall_packages = $row[0];
				$hasBeenSent = true;
			}
		
			$db->EndQuery( $rs );
		}
	

		//$sqlStr = 'SELECT status FROM altinn_packet WHERE status<>0 AND termin=' . $terminItem . ' AND termintype=' . $config->GetConfig($config->TERMIN_TYPE) . ' AND year=' . $year;
        //$oppgaveType=$HOVEDOPPGAVE;
        
		//if ( $rs = $db->Query( $sqlStr ) )//{
			//if ( $row = $db->NextRow( $rs )){
				//$hasBeenSent = true;
				//$oppgaveType= $KORIGERTOPPGAVE;

				//$schema->SetData( 5659, 3 );
			//}
			//$db->EndQuery( $rs );
		//}

		//print ("Anh: servername:".$_SERVER['SCRIPT_NAME']."<br>");
?>
    
	<h3><?php echo($schema->GetSchemaName()); ?></h3>

<!---form name="mvaf" action="<?php echo($_SERVER['SCRIPT_NAME'])?>" method="post"--->
<form name="mvaf" action="<?php print $MY_SELF; ?>" method="post" >

<?php

    $mvaSatsTable= array();
    //global $_sess, $db;
   
    $date = $_lib['sess']->get_session('LoginFormDate');
	$queryStr  = "SELECT AccountPlanID,Percent,VatID  FROM vat WHERE AccountPlanID>=2701 AND  AccountPlanID<=2704" .
			" AND ValidFrom <= '$date' AND ValidTo >= '$date' ORDER BY VatID asc";
	//print "$queryStr <br>\n";
	$rs = $db->Query($queryStr);
	
	 while ($row = $db->NextRow($rs )) {
	 	  $mvaSatsTable[$row['AccountPlanID']]=$row['Percent'];
	 	  //print ("account ". $row['AccountPlanID']. $row['Percent']. "br");
	 } 

	echo($lodo->LodoUrlSelf( '', $lodo->LODOURLTYPE_FORM ));
?>

<table>
<tr><td class="m">Antall sendte oppgaver</td>
	<td style="color:blue" align="right" > <?php  echo ($antall_packages); ?></td>
</tr>
<tr height="15"><td></td>
</tr>
<tr>
	<td class="m">Hovedoppgave</td>
	<td align="right"><input onClick="setOppgaveType(this)" type="radio" checked name="hoved" value="1"> </td>
</tr>
<tr>
	<td class="m">Korrigert oppgave</td>
	<td align="right"><input onClick="setOppgaveType(this)"  type="radio" name="korrigert" value="3"<?php if ( $orid_data_array['D5659'] == "3" ) {?> checked<?php }?><?php if ( $status>0 ) {?> disabled<?php }?>/></td>
</tr>
<tr>
	<td class="m">Tilleggsoppgave</td>
	<td align="right"><input onClick="setOppgaveType(this)" type="radio" name="tillegg" value="2"<?php if ( $orid_data_array['D5659'] == "2" ) {?> checked<?php }?><?php if ( $status>0 ) {?> disabled<?php }?>/></td>
</tr>
</table>

<table>
<tr>
	<td></td>
	<td></td>
	<th colspan ="1" width="100">Grunnlag</th>
	<th colspan ="2" width="120">Beregnet avgift</th>
</tr>
<tr>
	<td class="m"><b>1</b></td>
	<td class="m">Samlet omsetning og uttak innenfor og utenfor merverdiavgiftsloven (mva-loven)</td>
	<td><input size=7 name="D8446" value="<?php echo($orid_data_array[ 'D8446' ]);?>" align="right"<?php if ( $status>0 ) {?> disabled<?php }?> />,00</td>
</tr>
<tr>
	<td class="m"><b>2</b></td>
	<td class="m">Samlet omsetning og uttak innenfor mva-loven. Summen av post 3, 4, 5 og 6. Avgift ikke medregnet</td>

	<td><input size=7 name="D10095" value="<?php echo( $orid_data_array[ 'D10095' ] );?>" align="right"<?php if ( $status>0 ) {?> disabled<?php }?> />,00</td>
</tr>
<tr>
	<td class="m"><b>3</b></td>
	<td class="m">Omsetning og uttak i post 2 som er fritatt for merverdiavgift</td>
	<td><input size=7 name="D10096" value="<?php echo( $orid_data_array[ 'D10096' ] );?>" align="right"<?php if ( $status>0 ) {?> disabled<?php }?>/>,00</td>
</tr>
<tr>
	<td class="m"><b>4</b></td>

	<td class="m">Omsetning og uttak i post 2 med h�y sats. -- avgiftsats i kontor 2701 --  </td>
	<td><input size=7 name="D10097" id="D10097" value="<?php echo($orid_data_array[ 'D10097' ]);?>" align="right"<?php if ( $status>0 ) {?> disabled<?php }?> />,00 <?php echo($mvaSatsTable['2701']); ?>%</td>
	<td width="5">+</td>
	<td align="left"><input size=7 class="norm" name="D10098" value="<?php echo($orid_data_array[ 'D10098' ]);?>" align="right"<?php if ( $status>0 ) {?> disabled<?php }?> />,00</td>
</tr>
<tr>
	<td class="m"><b>5</b></td>
	<td class="m">Omsetning og uttak i post 2 med middels sats. -- avgiftsats i kontor 2702 --</td>

	<td><input size=7 name="D20319" value="<?php echo($orid_data_array[ 'D20319' ]);?>" align="right"<?php if ( $status>0 ) {?> disabled<?php }?> />,00 <?php echo($mvaSatsTable['2702']); ?>%</td>
	<td width="5">+</td>
	<td align="left"><input size=7 name="D20320" value="<?php echo($orid_data_array[ 'D20320' ]);?>" align="right"<?php if ( $status>0 ) {?> disabled<?php }?>/>,00</td>
</tr>
<tr>
	<td class="m"><b>6</b></td>
	<td class="m">Omsetning og uttak i post 2 med lav sats. -- avgiftsats i kontor 2703 --</td>
	<td><input size=7 name="D14360" value="<?php echo($orid_data_array[ 'D14360' ]);?>" align="right"<?php if ( $status>0 ) {?> disabled<?php }?>/>,00 <?php echo($mvaSatsTable['2703']); ?>%</td>
	<td width="5">+</td>
	<td align="left"><input size=7 name="D14361" value="<?php echo($orid_data_array[ 'D14361' ]);?>" align="right"<?php if ( $status>0 ) {?> disabled<?php }?>/>,00</td>
</tr>
<tr>
	<td class="m"><b>7</b></td>
	<td class="m">Tjenester kj�pt fra utlandet. -- avgiftsats i kontor 2704 --</td>
	<td><input size=7 name="D14362" value="<?php echo($orid_data_array[ 'D14362' ]);?>" align="right"<?php if ( $status>0 ) {?> disabled<?php }?>/>,00 <?php echo($mvaSatsTable['2704']); ?>%</td>
	<td width="5">+</td>
	<td align="left"><input size=7 name="D14363" value="<?php echo($orid_data_array[ 'D14363' ]);?>" align="right"<?php if ( $status>0 ) {?> disabled<?php }?>/>,00 </td>
</tr>
<tr>
	<td class="m"><b>8</b></td>
	<td class="m">Fradragsberettiget inng�ende avgift, h�y sats. -- avgiftsats i kontor 2711 --  </td>
	<td></td>
	<td width="5">-</td>
	<td align="left"><input size=7 name="D8450" value="<?php echo($orid_data_array[ 'D8450' ]);?>" align="right"<?php if ( $status>0 ) {?> disabled<?php }?>/>,00 <?php echo($mvaSatsTable['2701']); ?>%</td>
</tr>
<tr>
	<td class="m"><b>9</b></td>
	<td class="m">Fradragsberettiget inng�ende avgift, middels sats. -- avgiftsats i kontor 2712 --  </td>
	<td></td>
	<td width="5">-</td>
	<td align="left"><input size=7 name="D20322" value="<?php echo($orid_data_array[ 'D20322' ]);?>" align="right"<?php if ( $status>0 ) {?> disabled<?php }?>/>,00 <?php echo($mvaSatsTable['2702']); ?>%</td>
</tr>
<tr>
	<td class="m"><b>10</b></td>
	<td class="m">Fradragberettiget inng�ende avgift, lav sats. -- avgiftsats i kontor 2713 --  </td>
	<td></td>
	<td width="5">-</td>
	<td align="left"><input size=7 name="D14364" value="<?php echo($orid_data_array[ 'D14364' ]);?>" align="right"<?php if ( $status>0 ) {?> disabled<?php }?>/>,00 <?php echo($mvaSatsTable['2703']); ?>%</td>
</tr>
<tr>
	<td class="m"><b>11</b></td>
	<td class="m">Avgift til gode</td>
	<td></td>
	<td>=</td>
	<td align="left"><input size=7 name="D8452" value="<?php echo($orid_data_array[ 'D8452' ]);?>" align="right" disabled/>,00</td>
</tr>
<tr>
	<td class="m"><b></b></td>

	<td class="m">Avgift � betale</td>
	<td></td>
	<td>=</td>
	<td align="left"><input size=7 name="D8453" value="<?php echo($orid_data_array[ 'D8453' ]);?>" align="right" disabled/>,00</td>
</tr>
<tr height="20"><td colspan="4"><td></tr>
<tr><td colspan="4">Tilleggsopplysninger:</td></tr>
<tr height="20"><td colspan="3"><input size=106 name="D19684" /></td></tr>
<tr height="20"><td colspan="4"></td></tr>
<tr><td colspan="2" style="color:blue" width="200" name="message" id="message">Tast inn verdier og trykk p� beregn knappen.</td></tr>
</table>

<br/><br/>

<?php if ($status == 0) {?><input type="button" name="nop" value="Beregn" onClick="javascript:Calculate();" />&#160; 
<?php }?>

<input type="hidden" name="t" value="altinn.sendpacket_ge"/>
<input type="hidden" name="schemainstance" value="<?php echo($instanceNext);?>"/>
<input type="hidden" name="currentschemainstance" value="<?php echo($schemaInstanceId);?>"/>
<input type="hidden" name="packetid" value="<?php echo($packageId);?>"/>
<input type="hidden" name="D10094" value="<?php echo($year);?>"/>
<input type="hidden" name="D10092" value="Termin Type"/>
<input type="hidden" name="D10093" value="<?php echo("0".$terminItem."4");?>"/>
<input type="hidden" name="D5659" value="<?php echo($oppgaveType);?>"/>
<input type="hidden" name="schemacontrol" value="Y"/>
<input type="hidden" name="fagsystemtype" value="1"/>
<input type="hidden" name="READYTOSEND" value="true"/>

<input type="hidden" name="SATS2701" value="<?php echo($mvaSatsTable['2701']); ?>"/>
<input type="hidden" name="SATS2702" value="<?php echo($mvaSatsTable['2702']); ?>"/>
<input type="hidden" name="SATS2703" value="<?php echo($mvaSatsTable['2703']); ?>"/>
<input type="hidden" name="SATS2704" value="<?php echo($mvaSatsTable['2704']); ?>"/>
<input type="submit" name="send" value="Send til Altinn" <?php if (!$send_enabled) {?> disabled<?php }?> />

<?php if ($status < 1) { ?>&#160; &#160; &#160; 
   <input type="button" name="delete" value="Slett" onclick="clearFields();"><?php }?>

</form>

<p><a href="<?php echo($lodo->LodoUrlGet( '', $lodo->LODOURLTYPE_HREF,'altinn.schema_general' )); ?>">Tilbake til terminoversikt</a></p>

<?php
	}//if  ( $schemaNumber > 0 ) 
	else $layout->PrintError('Fant ikke det aktuelle skjemaet i pakken.');
	
	$db->Disconnect();
	?>
</td>
</tr>
</table>

</html>

>>>>>>> .r60
>>>>>>> .r75
