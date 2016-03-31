-- Add an option to turn off printing giro info on invoice pdf
ALTER TABLE company
ADD InvoicePDFPrintGiroInfo smallint(1) DEFAULT 1;
