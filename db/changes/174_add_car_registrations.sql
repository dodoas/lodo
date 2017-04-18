CREATE TABLE IF NOT EXISTS carregistration (
  CarRegistrationID int(11) NOT NULL AUTO_INCREMENT PRIMARY KEY,
  CarID int(11) NOT NULL,
  RegistrationNumber varchar(250) NOT NULL,
  ActiveInAccountingUntil date
);

INSERT INTO carregistration (CarID, RegistrationNumber)
(SELECT car.CarID, car.CarCode
FROM car
WHERE car.CarCode != '' AND car.CarCode IS NOT NULL);

ALTER TABLE invoiceinline
ADD CarRegistrationID int(11);

UPDATE invoiceinline il
SET CarRegistrationID = (SELECT CarRegistrationID FROM carregistration WHERE CarID = il.CarID) WHERE CarID IS NOT NULL;

ALTER TABLE car
DROP CarCode;