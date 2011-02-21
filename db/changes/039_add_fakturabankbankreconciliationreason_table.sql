CREATE TABLE IF NOT EXISTS `fakturabankbankreconciliationreason` (
  `FakturabankBankReconciliationReasonID` bigint(20) NOT NULL,
  `FakturabankBankReconciliationReasonCode` varchar(10) default NULL,
  `FakturabankBankReconciliationReasonName` varchar(255) default NULL,
  `AccountPlanID` bigint(20) NOT NULL,
  PRIMARY KEY  (`FakturabankBankReconciliationReasonID`)
) ENGINE=MyISAM  DEFAULT CHARSET=latin1;
