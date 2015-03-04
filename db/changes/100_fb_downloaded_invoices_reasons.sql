CREATE TABLE `fbdownloadedinvoicereasons` (
`ID` bigint(20) NOT NULL AUTO_INCREMENT,
`FakturabankInvoiceInId` bigint(20) NOT NULL,
`LodoID` bigint(20),
`ClosingReasonId` bigint(20) NOT NULL,
`Amount` DECIMAL(16,5),
`IsCustomerClosingReason` smallint(6),
PRIMARY KEY (`ID`)
);