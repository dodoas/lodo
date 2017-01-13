ALTER TABLE salaryline ADD COLUMN FreeCarID bigint(20);
ALTER TABLE car
    ADD COLUMN Carpool boolean DEFAULT 0,
    ADD COLUMN OfficalPrice decimal(16,2);
