<?
$AccountID  = $_lib['input']->getProperty('AccountID');
$Period     = $_lib['input']->getProperty('Period');

includelogic('fakturabank/fakturabankvoting');
includelogic('fakturabank/reconciliationreason');

#$bank2           = new framework_logic_bank($_lib['input']->request);
#$bank2->init();

if($_lib['input']->getProperty('action_bank_new')) {
  $postmain['account_ValidFrom']   = $_lib['sess']->get_session('Date');
  $postmain['account_Active']      = 1;
  $postmain['account_VoucherType'] = 'B';
  $AccountID = $_lib['db']->db_new_hash($postmain, 'account');
}
elseif($_lib['input']->getProperty('action_period_update')) {
    $_lib['storage']->update($_lib['input']->get_data());
}
elseif($_lib['input']->getProperty('action_bank_update')) {
    do_bank_update($AccountID, $Period);
}
elseif($_lib['input']->getProperty('action_bank_votinglinenewtillegg')) {
  $Period = $_lib['input']->getProperty('Period');
  $postmain['bankvotingline_AccountID']            = $AccountID;
  $postmain['bankvotingline_VoucherDate']          = "$Period-01";
  $postmain['bankvotingline_VoucherPeriod']        = $Period;
  $postmain['bankvotingline_InsertedDateTime']     = 'NOW()';
  $postmain['bankvotingline_Type']                 = 'tillegg';
  $postmain['bankvotingline_InsertedByPersonID']   = $_lib['sess']->get_person('PersonID');
  #print_r($postmain);
  $postmain['bankvotingline_AccountLineID']        = $_lib['db']->db_new_hash($postmain, 'bankvotingline');
}
elseif($_lib['input']->getProperty('action_bank_votinglinenewtilbake')) {
  $Period = $_lib['input']->getProperty('Period');
  $postmain['bankvotingline_AccountID']            = $AccountID;
  $postmain['bankvotingline_VoucherDate']          = "$Period-01";
  $postmain['bankvotingline_VoucherPeriod']        = $Period;
  $postmain['bankvotingline_InsertedDateTime']     = 'NOW()';
  $postmain['bankvotingline_Type']                 = 'tilbake';
  $postmain['bankvotingline_InsertedByPersonID']   = $_lib['sess']->get_person('PersonID');
  #print_r($postmain);
  $postmain['bankvotingline_AccountLineID']        = $_lib['db']->db_new_hash($postmain, 'bankvotingline');
}
elseif($_lib['input']->getProperty('action_bank_accountlinedelete')) {

    $sql_delete  = "update accountline set Active=0 where AccountLineID=" . (int) $_lib['input']->getProperty('AccountLineID');
    print "$sql_delete<br>\n";
    $_lib['db']->db_delete($sql_delete);
}
elseif($_lib['input']->getProperty('action_bank_accountlinenew')) {

    $bank->AddAccountLines($_lib['input']->getProperty('numnewlines'));
}
elseif($_lib['input']->getProperty('action_bank_zerojournalid')) {
    $bank->ZeroAccountLineJournalID();
}
elseif($_lib['input']->getProperty('action_bank_journal')) {
    $bank->init();
    $bank->journal();
}
elseif($_lib['input']->getProperty('action_bank_cardadd')) {
    $bankaccount->cardadd($_lib['input']->getProperty('AccountID'));
}
elseif($_lib['input']->getProperty('action_bank_cardremove')) {
    $bankaccount->cardremove($_lib['input']->getProperty('BankAccountCardID'));
}
elseif($_lib['input']->getProperty('action_bank_periodremove')) {
    $bankaccount->periodremove();
}
elseif($_lib['input']->getProperty('action_bank_periodlock')) {
    $bankaccount->periodlock();
}
elseif($_lib['input']->getProperty('action_bank_commentupdate')) {
	#print_r($_POST);
	$tables['bankvotingperiod']    = 'BankVotingPeriodID';
	$_lib['storage']->db_update_multi_table($_POST, $tables);
}


function do_bank_update($AccountID, $Period) {
    global $_lib, $accounting;

    $debugline = 99999999999999999;

    #print_r($_input->get_data());

    $PrevPeriod     = $_lib['date']->get_prev_period(array('value' => $Period . '-01', 'realPeriod' => 1));
    $PrevPrevPeriod = $_lib['date']->get_prev_period(array('value' => $PrevPeriod . '-01', 'realPeriod' => 1));
    $moneyflow  = new moneyflow(array('StartDate' =>  $PrevPrevPeriod . '-01'));
  
    #Automatically update and increment JournalIDs thats missing
    $DataH = $_lib['input']->get_data();
    #print_r($DataH);

	// get fakturabank
	$fbvoting         = new lodo_fakturabank_fakturabankvoting();
	$fbreconcilationreason = new lodo_fakturabank_reconciliationreason();

    foreach($DataH as $server_name => $server_data) {
        foreach($server_data as $database_name => $database_data) {
            foreach($database_data as $table_name => $table_data) {
                #print_r($table_data);
                foreach($table_data as $pk_name => $pk_data) {
                
                    #Most of the logic could probably have been here for speed
                
                    foreach($pk_data as $field => $value) {
                    
						if($field == 'JournalID') {
							if($value > 0) {
								if($value > $PrevJournalID) {
									$PrevJournalID = $value;
								}
							} elseif($PrevJournalID > 0) {
								$DataH[$server_name][$database_name][$table_name][$pk_name][$field] = $PrevJournalID + 1;
								$PrevJournalID++;
							}
						}							
					}

                    $accountplan = null;

					#Speedier to have it here? Could we throw away the inner loop?
                   	if($table_name == 'accountline') { #We are just working on accountline?
                        # Allow unsetting a reskontro
                        # We do this via a flag to ignore auto-matching of the account
                        if (!isset($DataH[$server_name][$database_name][$table_name][$pk_name]['ReskontroAccountPlanID'])) {
                            $UnsetReskontroAccountPlanID = true;
                            # set value to "" to not trigger errors like:
                            # get_accountplan_object(): Mangler AccountplanID
                            $DataH[$server_name][$database_name][$table_name][$pk_name]['ReskontroAccountPlanID'] = "";
                        } else {
                            $UnsetReskontroAccountPlanID = false;
                        }

                        # Allow unsetting a resultaccount
                        # We do this via a flag to ignore auto-matching of the account
                        if (!isset($DataH[$server_name][$database_name][$table_name][$pk_name]['ResultAccountPlanID'])) {
                            $UnsetResultAccountPlanID = true;
                            # set value to "" to not trigger errors like:
                            # get_accountplan_object(): Mangler AccountplanID
                            $DataH[$server_name][$database_name][$table_name][$pk_name]['ResultAccountPlanID'] = "";
                        } else {
                            $UnsetResultAccountPlanID = false;
                        }

						if($DataH[$server_name][$database_name][$table_name][$pk_name]['Priority'] == $debugline) print_r($DataH[$server_name][$database_name][$table_name][$pk_name]);

                        #These fields are generated from a drop down with alternatives
                        if($DataH[$server_name][$database_name][$table_name][$pk_name]['KIDandInvoiceIDandAccountPlanID']) {
                            if($DataH[$server_name][$database_name][$table_name][$pk_name]['Priority'] == $debugline) print "P: " . $DataH[$server_name][$database_name][$table_name][$pk_name]['Priority'] . "KIDandAccountPlanID<br>";
                            list($KID, $InvoiceNumber, $ReskontroAccountPlanID) = explode('#', $DataH[$server_name][$database_name][$table_name][$pk_name]['KIDandInvoiceIDandAccountPlanID']);
                            if($DataH[$server_name][$database_name][$table_name][$pk_name]['Priority'] == $debugline) print "Resultat fra meny: KIDandInvoiceIDandAccountPlanID<br>\n";
                            #print_r($DataH[$server_name][$database_name][$table_name][$pk_name]['KIDandInvoiceIDandAccountPlanID']);


                            if(!$DataH[$server_name][$database_name][$table_name][$pk_name]['ReskontroAccountPlanID']) {
					            $DataH[$server_name][$database_name][$table_name][$pk_name]['ReskontroAccountPlanID'] = $ReskontroAccountPlanID;
                            }
						    if(!$DataH[$server_name][$database_name][$table_name][$pk_name]['KID']) {
						        $DataH[$server_name][$database_name][$table_name][$pk_name]['KID'] = $KID;
                            }
						    if(!$DataH[$server_name][$database_name][$table_name][$pk_name]['InvoiceNumber']) {
    						    $DataH[$server_name][$database_name][$table_name][$pk_name]['InvoiceNumber']          = $InvoiceNumber;
    						}
						    #print_r($DataH[$server_name][$database_name][$table_name][$pk_name]);
                        }

                        unset($DataH[$server_name][$database_name][$table_name][$pk_name]['KIDandInvoiceIDandAccountPlanID']); #Does not exist in db.

						#Add a zero of the day is only one char.
						if($DataH[$server_name][$database_name][$table_name][$pk_name]['Day'])
							$DataH[$server_name][$database_name][$table_name][$pk_name]['Day']  = str_pad($DataH[$server_name][$database_name][$table_name][$pk_name]['Day'], 2, '0', STR_PAD_LEFT);;

                        /**************************************************
                         *
                         * Automatching code begins
                         *
                         */

                        /* if we are missing account then look for matches in bank transaction relations */
						if(
                           ($DataH[$server_name][$database_name][$table_name][$pk_name]['AmountIn'] > 0 || $DataH[$server_name][$database_name][$table_name][$pk_name]['AmountOut'] > 0) &&
                           // ((!$DataH[$server_name][$database_name][$table_name][$pk_name]['KID'] && !$DataH[$server_name][$database_name][$table_name][$pk_name]['InvoiceNumber'] && !$DataH[$server_name][$database_name][$table_name][$pk_name]['ReskontroAccountPlanID']) || 
                           (!$DataH[$server_name][$database_name][$table_name][$pk_name]['ReskontroAccountPlanID'] && !$DataH[$server_name][$database_name][$table_name][$pk_name]['ResultAccountPlanID'])) {
							# KID, invoicenumber or reskontro missing. Find match from fakturabank data or moneyflow

                            if($DataH[$server_name][$database_name][$table_name][$pk_name]['Priority'] == $debugline) print "P: " . $DataH[$server_name][$database_name][$table_name][$pk_name]['Priority'] . "Ikke KID ikke ReskontroAccountPlanID<br>";


							if($DataH[$server_name][$database_name][$table_name][$pk_name]['AmountIn'] > 0) {
								$incoming = 0;
								$field = 'AmountIn';
								$value = $DataH[$server_name][$database_name][$table_name][$pk_name]['AmountIn'];
							} else {
								$incoming = 1;
								$field = 'AmountOut';
								$value = $DataH[$server_name][$database_name][$table_name][$pk_name]['AmountOut'];
							}
							

                            $accountline = $DataH[$server_name][$database_name][$table_name][$pk_name];

                            # find fakturabank data
                            if ($match = $fbvoting->findpaycheckmatch(array('Amount' => $value, 'VoucherDate' => $Period . '-' . $accountline['Day'], 'BankAccountID' => $AccountID, 'Incoming' => $incoming, 'id' => $pk_name, 'accountline' => $DataH[$server_name][$database_name][$table_name][$pk_name], 'period' => $Period))) { // look for paycheck
                                if(!$DataH[$server_name][$database_name][$table_name][$pk_name]['ReskontroAccountPlanID']) {
                                    $DataH[$server_name][$database_name][$table_name][$pk_name]['ReskontroAccountPlanID'] = $match['AccountPlanID'];
                                }
                            } elseif (($match = $fbvoting->findinvoicematch(array('Amount' => $value, 'VoucherDate' => $Period . '-' . $accountline['Day'], 'BankAccountID' => $AccountID, 'Incoming' => $incoming, 'id' => $pk_name, 'accountline' => $DataH[$server_name][$database_name][$table_name][$pk_name], 'period' => $Period)))  && 
                                !empty($match['AccountPlanID']) && // only invoice matches (i.e. with orgnr)
                                !empty($match['AccountPlanOrgNumber'])) {

									if(!$DataH[$server_name][$database_name][$table_name][$pk_name]['ReskontroAccountPlanID']) {
										$DataH[$server_name][$database_name][$table_name][$pk_name]['ReskontroAccountPlanID'] = $match['AccountPlanID'];
									}
									if(!$DataH[$server_name][$database_name][$table_name][$pk_name]['ReskontroOrgNumber']) {
										$DataH[$server_name][$database_name][$table_name][$pk_name]['ReskontroOrgNumber']     = $match['AccountPlanOrgNumber'];
									}
									if(!$DataH[$server_name][$database_name][$table_name][$pk_name]['KID'] && !empty($match['KID'])) {
										$DataH[$server_name][$database_name][$table_name][$pk_name]['KID']                    = $match['KID'];
									}

									if(!$DataH[$server_name][$database_name][$table_name][$pk_name]['InvoiceNumber'] && !empty($match['InvoiceNumber'])) {
										$DataH[$server_name][$database_name][$table_name][$pk_name]['InvoiceNumber']          = $match['InvoiceNumber'];
									}
							} else {
                                # find possible matches on non-invoice reconcilation reasons
                                $match = $fbvoting->findnoninvoicematch(array('Amount' => $value, 'VoucherDate' => $Period . '-' . $accountline['Day'], 'BankAccountID' => $AccountID, 'Incoming' => $incoming, 'id' => $pk_name, 'accountline' => $DataH[$server_name][$database_name][$table_name][$pk_name], 'period' => $Period));
                                $reconciliation_accountplan_id = null;
                                $reconciliation_accountplan = null;
                                if ($match && 
                                    //                                    empty($DataH[$server_name][$database_name][$table_name][$pk_name]['ResultAccountPlanID']) &&
                                    !empty($match['FakturabankBankTransactionAccountID'])) {
                                    $reconciliation_accountplan = $fbreconcilationreason->bank_reconciliation_reason_to_accountplan($match['FakturabankBankTransactionAccountID']);
                                }

                                if (!empty($reconciliation_accountplan)) {
									if(!$DataH[$server_name][$database_name][$table_name][$pk_name]['ResultAccountPlanID']) {
										$DataH[$server_name][$database_name][$table_name][$pk_name]['ResultAccountPlanID'] = $reconciliation_accountplan['AccountPlanID'];
									}
                                } else {
                                    #If no KID and no Reskontro and in or out amount - try an automatic kid match to update data
                                    #Always look up reskontro on kid - no we only do it if kid is missing - and on the first run.
                                    #print "Ikke kjør denne nå<br>\n";

                                    $match = $moneyflow->findmatch(array($field => $value, 'VoucherDate' => $Period . '-' . $line->Day));
                                    if(count($match) == 1) {
                                        #Fant eksakt en match på beløp.
                                        #print_r($match[0]);
								
                                        if(!$DataH[$server_name][$database_name][$table_name][$pk_name]['ReskontroAccountPlanID']) {
                                            $DataH[$server_name][$database_name][$table_name][$pk_name]['ReskontroAccountPlanID'] = $match[0]->AccountPlanID;
                                        }
                                        if(!$DataH[$server_name][$database_name][$table_name][$pk_name]['ReskontroOrgNumber']) {
                                            $DataH[$server_name][$database_name][$table_name][$pk_name]['ReskontroOrgNumber']     = $match[0]->OrgNumber;
                                        }
                                        if(!$DataH[$server_name][$database_name][$table_name][$pk_name]['KID']) {
                                            $DataH[$server_name][$database_name][$table_name][$pk_name]['KID']                    = $match[0]->KID;
                                        }
                                        if(!$DataH[$server_name][$database_name][$table_name][$pk_name]['InvoiceNumber']) {
                                            $DataH[$server_name][$database_name][$table_name][$pk_name]['InvoiceNumber']          = $match[0]->InvoiceID;
                                        }
                                    }
                                }								
							}
						}
						elseif($DataH[$server_name][$database_name][$table_name][$pk_name]['KID'] > 0 && !$DataH[$server_name][$database_name][$table_name][$pk_name]['ReskontroAccountPlanID']) {
                            #Finn kontoplan til bankavstemminglinje basert på KID
                            if($DataH[$server_name][$database_name][$table_name][$pk_name]['Priority'] == $debugline) print "P: " . $DataH[$server_name][$database_name][$table_name][$pk_name]['Priority'] . "Finn kontoplan til bankavstemminglinje basert på KID<br>\n";

							#print "Finn kid: " . $DataH[$server_name][$database_name][$table_name][$pk_name]['KID'] . "<br />";
							$voucher = $accounting->FindJournalWithKid($DataH[$server_name][$database_name][$table_name][$pk_name]['KID']);
							#print_r($voucher);
							if(!$DataH[$server_name][$database_name][$table_name][$pk_name]['ReskontroAccountPlanID']) {
							    $DataH[$server_name][$database_name][$table_name][$pk_name]['ReskontroAccountPlanID'] = $voucher->AccountPlanID;
                            }
                            if(!$DataH[$server_name][$database_name][$table_name][$pk_name]['ReskontroOrgNumber']) {
							    $DataH[$server_name][$database_name][$table_name][$pk_name]['ReskontroOrgNumber']     = $voucher->OrgNumber;
                            }
						}
						elseif($DataH[$server_name][$database_name][$table_name][$pk_name]['InvoiceNumber'] > 0 && !$DataH[$server_name][$database_name][$table_name][$pk_name]['ReskontroAccountPlanID']) {
    						#print "Finn kontoplan til bankavstemminglinje basert p&aring; Fakturanummer: " . $DataH[$server_name][$database_name][$table_name][$pk_name]['InvoiceNumber'] . "<br>\n";

                            if($DataH[$server_name][$database_name][$table_name][$pk_name]['Priority'] == $debugline) print "P: " . $DataH[$server_name][$database_name][$table_name][$pk_name]['Priority'] . "Finn kontoplan til bankavstemminglinje basert på Fakturanummer<br>\n";
							if($DataH[$server_name][$database_name][$table_name][$pk_name]['Priority'] == $debugline) print "Finn fakturanummer: " . $DataH[$server_name][$database_name][$table_name][$pk_name]['InvoiceNumber'] . "<br />";
							
							$voucher = $accounting->FindJournalWithInvoiceID($DataH[$server_name][$database_name][$table_name][$pk_name]['InvoiceNumber']);
							#print_r($voucher);
							if(!$DataH[$server_name][$database_name][$table_name][$pk_name]['ReskontroAccountPlanID']) {
							    $DataH[$server_name][$database_name][$table_name][$pk_name]['ReskontroAccountPlanID'] = $voucher->AccountPlanID;
                            }
                            if(!$DataH[$server_name][$database_name][$table_name][$pk_name]['ReskontroOrgNumber']) {
							    $DataH[$server_name][$database_name][$table_name][$pk_name]['ReskontroOrgNumber']     = $voucher->OrgNumber;
                            }
						}
						
						if($DataH[$server_name][$database_name][$table_name][$pk_name]['AutoResultAccount'] && $DataH[$server_name][$database_name][$table_name][$pk_name]['ReskontroAccountPlanID']) {
                            if($DataH[$server_name][$database_name][$table_name][$pk_name]['Priority'] == $debugline) print "P: " . $DataH[$server_name][$database_name][$table_name][$pk_name]['Priority'] . "AutoResultAccount<br>\n";
							#Reset to the 
							$reskontroaccountplan   = $accounting->get_accountplan_object($DataH[$server_name][$database_name][$table_name][$pk_name]['ReskontroAccountPlanID']);
                            if($reskontroaccountplan->MotkontoResultat1) {
    							$DataH[$server_name][$database_name][$table_name][$pk_name]['ResultAccountPlanID'] = $reskontroaccountplan->MotkontoResultat1;
    					    } else {
    							$DataH[$server_name][$database_name][$table_name][$pk_name]['ResultAccountPlanID'] = $reskontroaccountplan->MotkontoBalanse1;    					    
    					    }
						}

						if($DataH[$server_name][$database_name][$table_name][$pk_name]['Approved'] && !$DataH[$server_name][$database_name][$table_name][$pk_name]['ReskontroAccountPlanID'] && !$DataH[$server_name][$database_name][$table_name][$pk_name]['ResultAccountPlanID']) {
							#If no reskontro and no result accountplan - set Approved to 0
                            if($DataH[$server_name][$database_name][$table_name][$pk_name]['Priority'] == $debugline) print "P: " . $DataH[$server_name][$database_name][$table_name][$pk_name]['Priority'] . "Remove Approved<br>";
							#sprint "Remove approved<br>\n";
							$DataH[$server_name][$database_name][$table_name][$pk_name]['Approved'] = 0;
						}

						#Used for simple bankavstemming
						#print "ReskontroAccountPlanID: " . $DataH[$server_name][$database_name][$table_name][$pk_name]['ReskontroAccountPlanID'] . " && ReskontroOrgNumber: " . !$DataH[$server_name][$database_name][$table_name][$pk_name]['ReskontroOrgNumber'] . "<br>\n";
						#Hvis Reskontrorgnummer ikke er satt og rekontrokonto er satt- så setter vi det nå (uansett)
						if($DataH[$server_name][$database_name][$table_name][$pk_name]['ReskontroAccountPlanID'] && !$DataH[$server_name][$database_name][$table_name][$pk_name]['ReskontroOrgNumber']) {
							if($DataH[$server_name][$database_name][$table_name][$pk_name]['Priority'] == $debugline) print "P: " . $DataH[$server_name][$database_name][$table_name][$pk_name]['Priority'] . "ReskontroAccountPlanID og ikke ReskontroOrgNummer<br>\n";

							$accountplan = $accounting->get_accountplan_object($DataH[$server_name][$database_name][$table_name][$pk_name]['ReskontroAccountPlanID']);
							$DataH[$server_name][$database_name][$table_name][$pk_name]['ReskontroOrgNumber']     	= $accountplan->OrgNumber; #$match[0]->OrgNumber;
						
						} elseif($DataH[$server_name][$database_name][$table_name][$pk_name]['ReskontroOrgNumber'] && !$DataH[$server_name][$database_name][$table_name][$pk_name]['ReskontroAccountPlanID']) {
							if($DataH[$server_name][$database_name][$table_name][$pk_name]['Priority'] == $debugline) print "P: " . $DataH[$server_name][$database_name][$table_name][$pk_name]['Priority'] . "ReskontroOrgNummer og ikke ReskontroAccountPlanID<br>\n";

							#Hvis reskontro er satt og ikke reskontrokontoorgnummer er satt - så setter vi den nå
							$accountplan = $accounting->getAccountPlanFromOrgNumber($DataH[$server_name][$database_name][$table_name][$pk_name]['ReskontroOrgNumber']);
							$DataH[$server_name][$database_name][$table_name][$pk_name]['ReskontroAccountPlanID']   = $accountplan->AccountPlanID;
						} else {
							#We know nothing about this reskontro.
						}
                        					

                        if($DataH[$server_name][$database_name][$table_name][$pk_name]['Day'] < 1 || $DataH[$server_name][$database_name][$table_name][$pk_name]['Day'] > 31) {
                            $DataH[$server_name][$database_name][$table_name][$pk_name]['Approved'] = 0;
                            $_lib['message']->add('Dag ikke mellom 1 og 31 - fjerner ok flagg');
                        }
                        if($DataH[$server_name][$database_name][$table_name][$pk_name]['JournalID'] < 1) {
                            $DataH[$server_name][$database_name][$table_name][$pk_name]['Approved'] = 0;
                            $_lib['message']->add('Bilagsnummer mindre enn 0 - fjerner ok flagg');
                        }

                        if($DataH[$server_name][$database_name][$table_name][$pk_name]['Priority'] == $debugline) print_r($DataH[$server_name][$database_name][$table_name][$pk_name]);


                        # if unset flag, then unset ReskontroAccountPlanID                        
                        if ($UnsetReskontroAccountPlanID) {
                            $DataH[$server_name][$database_name][$table_name][$pk_name]['ReskontroAccountPlanID'] = DB_NULL_PLACEHOLDER;
                            $UnsetReskontroAccountPlanID = false;
                        }   

                        # if unset flag, then unset ResultAccountPlanID
                        if ($UnsetResultAccountPlanID) {
                            $DataH[$server_name][$database_name][$table_name][$pk_name]['ResultAccountPlanID'] = DB_NULL_PLACEHOLDER;
                            $UnsetResultAccountPlanID = false;
                        }   
                    }
                }
            }
        }
    }
  
    #print_r($DataH);
    $_lib['storage']->update($DataH);

}
?>
