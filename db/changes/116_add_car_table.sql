-- Create new tables for Car and Cars milage
CREATE TABLE IF NOT EXISTS car (
CarID int(11) NOT NULL AUTO_INCREMENT,
CarName varchar(50) NOT NULL DEFAULT '',
TS timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
Active tinyint(4) NOT NULL DEFAULT '0',
ValidFrom datetime NOT NULL DEFAULT '0000-00-00 00:00:00',
ValidTo datetime NOT NULL DEFAULT '0000-00-00 00:00:00',
Description text,
CarCode varchar(10) DEFAULT NULL,
PurchasePrice decimal(16, 2) DEFAULT '0',
SalePrice decimal(16, 2) DEFAULT '0',
EnableVAT smallint(6) DEFAULT NULL,
VehicleType varchar(50) DEFAULT '',
BrandAndModel varchar(100) DEFAULT NULL,
NumberOfSeats int(3),
RegistrationYear smallint(6) DEFAULT '0',
Fuel varchar(50) DEFAULT '',
PRIMARY KEY (CarID)
);

CREATE TABLE IF NOT EXISTS carmilage (
CarID int(11) NOT NULL,
MilageYear smallint(6) NOT NULL DEFAULT '0',
StartMilage decimal(16, 2) DEFAULT '0',
EndMilage decimal(16, 2) DEFAULT '0',
PRIMARY KEY (CarID, MilageYear)
);

-- Add Car ID to all tables that need it
ALTER TABLE accountplan
ADD EnableCar smallint(6) DEFAULT NULL,
ADD CarID bigint(20) DEFAULT 0;

ALTER TABLE voucher
ADD CarID bigint(20) DEFAULT 0;

ALTER TABLE salaryconfline
ADD CarID bigint(20) DEFAULT 0;

ALTER TABLE salaryline
ADD CarID bigint(20) DEFAULT 0;

ALTER TABLE accountline
ADD CarID bigint(20) DEFAULT 0;

ALTER TABLE invoiceinline
ADD CarID bigint(20) DEFAULT 0;

-- Add Diverse car
-- disable autoincrement id when id is set to 0
-- for this query since we want the id to be 0
SET SESSION sql_mode='NO_AUTO_VALUE_ON_ZERO';
INSERT INTO car(CarID, CarName, CarCode, Active, ValidFrom)
VALUES(0, 'Diverse', '0', 1, '2000-01-01');

-- Enable car on all car related accounts
UPDATE accountplan
SET EnableCar = 1
WHERE AccountPlanID BETWEEN 7000 AND 7099;

