RENAME TABLE companydepartment TO department;

ALTER TABLE department
CHANGE CompanyDepartmentID DepartmentID INT( 11 ) NOT NULL DEFAULT 0;

ALTER TABLE product
CHANGE CompanyDepartmentID DepartmentID INT( 11 ) NULL DEFAULT NULL;

ALTER TABLE timesheets
CHANGE CompanyDepartment Department INT( 11 ) NOT NULL;

ALTER TABLE person
CHANGE CompanyDepartmentID DepartmentID INT( 11 ) NOT NULL DEFAULT 0,
CHANGE CompanyDepartment Department varchar( 255 ) NOT NULL DEFAULT 0;
