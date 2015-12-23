-- Add InvoiceOut to fbdownloadedinvoicereasons table
ALTER TABLE fbdownloadedinvoicereasons
ADD InvoiceOut boolean NOT NULL DEFAULT 0;

