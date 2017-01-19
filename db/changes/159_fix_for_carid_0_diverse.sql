SET @old_sql_mode := '';
SELECT @old_sql_mode = @@sql_mode;
SET SESSION sql_mode = 'NO_AUTO_VALUE_ON_ZERO'; -- so we can save with CarID = 0

REPLACE INTO car(CarID, CarName, CarCode, ValidFrom)
VALUES(0, 'Diverse', '0', '2000-01-01');

SET SESSION sql_mode = @old_sql_mode; -- set sql_mode to previous value
