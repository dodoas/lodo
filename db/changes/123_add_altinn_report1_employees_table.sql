-- Add table to store links for employees included in report
DROP TABLE IF EXISTS altinnReport1employee;
CREATE TABLE IF NOT EXISTS altinnReport1employee (
AltinnReport1ID int(11) NOT NULL,
AccountPlanID int(11) NOT NULL,
PRIMARY KEY (AltinnReport1ID, AccountPlanID)
);
