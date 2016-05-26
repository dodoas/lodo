-- Set all 'ruined' vouchers to default empty value
UPDATE voucher v SET matched_by = 0 WHERE matched_by = '';

-- Try to recreate the correct value of matched_by for all closed vouchers
-- First priority is InvoiceID
UPDATE voucher v,
(
SELECT vs.ParentVoucherID, vs.ChildVoucherID
FROM voucherstruct vs JOIN voucher v1 ON vs.ParentVoucherID = v1.VoucherID JOIN voucher v2 ON vs.ChildVoucherID = v2.VoucherID
WHERE v1.InvoiceID = v2.InvoiceID AND v1.InvoiceID <> '' AND v1.InvoiceID IS NOT NULL AND v2.InvoiceID <> '' AND v2.InvoiceID IS NOT NULL AND (v1.matched_by = '0' OR v2.matched_by = '0')
) t
SET matched_by = 'invoice'
WHERE v.VoucherID = t.ChildVoucherID OR v.VoucherID = t.ParentVoucherID;

-- Second priority is KID
UPDATE voucher v,
(
SELECT vs.ParentVoucherID, vs.ChildVoucherID
FROM voucherstruct vs JOIN voucher v1 ON vs.ParentVoucherID = v1.VoucherID JOIN voucher v2 ON vs.ChildVoucherID = v2.VoucherID
WHERE v1.KID = v2.KID AND v1.KID <> '' AND v1.KID IS NOT NULL AND v2.KID <> '' AND v2.KID IS NOT NULL AND (v1.matched_by = '0' OR v2.matched_by = '0')
) t
SET matched_by = 'kid'
WHERE v.VoucherID = t.ChildVoucherID OR v.VoucherID = t.ParentVoucherID;

-- Last priority is MatchNumber
UPDATE voucher v,
(
SELECT vs.ParentVoucherID, vs.ChildVoucherID
FROM voucherstruct vs JOIN voucher v1 ON vs.ParentVoucherID = v1.VoucherID JOIN voucher v2 ON vs.ChildVoucherID = v2.VoucherID JOIN vouchermatch vm1 ON vs.ParentVoucherID = vm1.VoucherID JOIN vouchermatch vm2 ON vs.ChildVoucherID = vm2.VoucherID
WHERE vm1.MatchNumber = vm2.MatchNumber AND vm1.MatchNumber <> 0 AND vm1.MatchNumber IS NOT NULL AND vm2.MatchNumber <> 0 AND vm2.MatchNumber IS NOT NULL AND (v1.matched_by = '0' OR v2.matched_by = '0')
) t
SET matched_by = 'match'
WHERE v.VoucherID = t.ChildVoucherID OR v.VoucherID = t.ParentVoucherID;
