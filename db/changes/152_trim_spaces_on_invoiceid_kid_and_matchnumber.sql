-- Trim spaces on InvoiceID and KID on voucher table and MatchNumber on vouchermatch table
UPDATE voucher SET InvoiceID = TRIM(InvoiceID), KID = TRIM(KID);
UPDATE vouchermatch SET MatchNumber = TRIM(MatchNumber);

