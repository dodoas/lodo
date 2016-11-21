-- Add indicator if this period should show in dropdown for read only user
ALTER TABLE accountperiod
ADD ShowForReadOnly int(1) DEFAULT 1;
