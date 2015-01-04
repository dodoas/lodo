-- Legacy: Set default value to 0 since earlier this field was reset to 0 (migration 094).
ALTER TABLE `voucher` ALTER matched_by SET DEFAULT 0;