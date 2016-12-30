ALTER TABLE accountline
ADD COLUMN Currency varchar(3) DEFAULT 'NOK';

UPDATE accountline SET Currency = (SELECT Currency FROM fakturabanktransaction WHERE ID = accountline.FakturabankTransactionLodoID);



