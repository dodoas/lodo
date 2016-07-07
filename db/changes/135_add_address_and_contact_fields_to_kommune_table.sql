-- Add more columns to kommune table for contact and address info
ALTER TABLE kommune
ADD Telephone varchar(255) DEFAULT '',
ADD Telefax varchar(255) DEFAULT '',
ADD Email varchar(255) DEFAULT '',
ADD Mobile varchar(255) DEFAULT '',
ADD Webpage varchar(255) DEFAULT '',
ADD Address1 varchar(255) DEFAULT '',
ADD Address2 varchar(255) DEFAULT '',
ADD Address3 varchar(255) DEFAULT '',
ADD ZipCode varchar(255) DEFAULT '',
ADD City varchar(255) DEFAULT '';
