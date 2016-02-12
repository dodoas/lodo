-- Add an additional field in the table to check if the data
-- we recieve from the report form is from the correct company
ALTER TABLE inbarbeidsgiveravgift
ADD db_name varchar(256) DEFAULT NULL;
