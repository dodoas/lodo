-- Remove the faulty invoices created when sending Altinn AGA/FTR invoices to Fakturabank
DELETE FROM invoiceout
WHERE InsertedDateTime IS NULL AND
      LockedAt IS NOT NULL AND
      LockedBy IS NOT NULL AND
      Locked = 1 AND
      Status = 0 AND
      Active = 0 AND
      FakturabankPersonID IS NOT NULL AND
      FakturabankDateTime IS NOT NULL;

-- Test query used to determine if it is only fetching the faulty ones
-- SELECT * 
-- FROM invoiceout
-- WHERE InsertedDateTime IS NULL AND
--       LockedAt IS NOT NULL AND
--       LockedBy IS NOT NULL AND
--       Locked = 1 AND
--       Status = 0 AND
--       Active = 0 AND
--       FakturabankPersonID IS NOT NULL AND
--       FakturabankDateTime IS NOT NULL;
