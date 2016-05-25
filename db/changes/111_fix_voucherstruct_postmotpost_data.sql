-- Set all 'ruined' vouchers to default empty value
UPDATE voucher v SET matched_by = 0 WHERE matched_by = '';

-- Try to recreate the correct value of matched_by for all closed vouchers
UPDATE voucher v,
(
SELECT vs.ParentVoucherID, vs.ChildVoucherID,
v1.VoucherID as VoucherID1, v1.InvoiceID as InvoiceID1, v1.KID as KID1, vm1.MatchNumber as MatchNumber1, v1.matched_by as matched_by1,
v2.VoucherID as VoucherID2, v2.InvoiceID as InvoiceID2, v2.KID as KID2, vm2.MatchNumber as MatchNumber2, v2.matched_by as matched_by2,
IF(v1.InvoiceID = v2.InvoiceID AND v1.InvoiceID <> '' AND v1.InvoiceID IS NOT NULL AND v2.InvoiceID <> '' AND v2.InvoiceID IS NOT NULL, 'true', 'false') as match_invoice,
IF(v1.KID = v2.KID AND v1.KID <> '' AND v1.KID IS NOT NULL AND v2.KID <> '' AND v2.KID IS NOT NULL, 'true', 'false') as match_kid,
IF(vm1.MatchNumber = vm2.MatchNumber AND vm1.MatchNumber <> 0 AND vm1.MatchNumber IS NOT NULL AND vm2.MatchNumber <> 0 AND vm2.MatchNumber IS NOT NULL, 'true', 'false') as match_matchnumber
FROM voucherstruct vs, voucher v1, voucher v2, vouchermatch vm1, vouchermatch vm2
WHERE vs.ParentVoucherID = v1.VoucherID AND vs.ChildVoucherID = v2.VoucherID AND vs.ParentVoucherID = vm1.VoucherID AND vs.ChildVoucherID = vm2.VoucherID AND (v1.matched_by = 0 OR v2.matched_by = 0)
) t
SET matched_by = IF(t.match_invoice = 'true', 'invoice', IF(t.match_kid = 'true', 'kid', IF(t.match_matchnumber = 'true', 'match', 0) ) )
WHERE v.VoucherID = t.ChildVoucherID OR v.VoucherID = t.ParentVoucherID
