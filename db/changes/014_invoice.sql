alter table invoicein  add FakturabankID bigint;
alter table invoiceout add FakturabankID bigint;

alter table invoicein  add ExternalID bigint;
alter table invoiceout add ExternalID bigint;

alter table invoicein  add VoucherType char(3);
alter table invoiceout add VoucherType char(3);

alter table voucher add ExternalID bigint;

alter table shortreport add VouchersDeliveredForScanning smallint;
alter table shortreport add VouchersScanned smallint;
