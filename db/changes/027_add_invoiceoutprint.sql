CREATE TABLE IF NOT EXISTS `invoiceoutprint` (
  `InvoiceID` bigint(20) NOT NULL,
  `InvoicePrintDate` date NOT NULL,
  PRIMARY KEY  (`InvoiceID`)
) ENGINE=MyISAM DEFAULT CHARSET=latin1;

