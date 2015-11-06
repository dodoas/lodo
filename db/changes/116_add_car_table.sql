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
Fuel decimal(16,2) DEFAULT '0',
PRIMARY KEY (CarID)
);

CREATE TABLE IF NOT EXISTS carmilage (
CarID int(11) NOT NULL,
MilageYear smallint(6) NOT NULL DEFAULT '0',
StartMilage int(11) DEFAULT '0',
EndMilage int(11) DEFAULT '0',
PRIMARY KEY (CarID, MilageYear)
);

-- Add Car ID to all tables that need it
ALTER TABLE accountplan
ADD EnableCar smallint(6) DEFAULT NULL,
ADD CarID bigint(20) DEFAULT 0;

ALTER TABLE voucher
ADD CarID bigint(20) DEFAULT NULL;

ALTER TABLE salaryconfline
ADD CarID bigint(20) DEFAULT NULL;

ALTER TABLE salaryline
ADD CarID bigint(20) DEFAULT NULL;

ALTER TABLE accountline
ADD CarID bigint(20) DEFAULT NULL;

ALTER TABLE invoiceinline
ADD CarID bigint(20) DEFAULT NULL;
