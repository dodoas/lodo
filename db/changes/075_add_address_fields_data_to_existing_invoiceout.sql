update invoiceout i set `SName` = (SELECT IName from Company c where c.CompanyID=i.FromCompanyID);
update invoiceout i set `SAddress` = (SELECT IAddress from Company c where c.CompanyID=i.FromCompanyID);
update invoiceout i set `SCity` = (SELECT ICity from Company c where c.CompanyID=i.FromCompanyID);
update invoiceout i set `SBankAccount` = (SELECT BankAccount from Company c where c.CompanyID=i.FromCompanyID);
update invoiceout i set `SZipCode` = (SELECT IZipCode from Company c where c.CompanyID=i.FromCompanyID);
update invoiceout i set `SCountryCode` = (SELECT ICountryCode from Company c where c.CompanyID=i.FromCompanyID);
update invoiceout i set `SPhone` = (SELECT Phone from Company c where c.CompanyID=i.FromCompanyID);
update invoiceout i set `SFax` = (SELECT Fax from Company c where c.CompanyID=i.FromCompanyID);
update invoiceout i set `SMobile` = (SELECT Mobile from Company c where c.CompanyID=i.FromCompanyID);
update invoiceout i set `SEmail` = (SELECT Email from Company c where c.CompanyID=i.FromCompanyID);
update invoiceout i set `SWeb` = (SELECT WWW from Company c where c.CompanyID=i.FromCompanyID);
update invoiceout i set `SOrgNo` = (SELECT OrgNumber from Company c where c.CompanyID=i.FromCompanyID);
update invoiceout i set `SVatNo` = (SELECT VatNumber from Company c where c.CompanyID=i.FromCompanyID);

update invoiceout i set `IOrgNo` = (SELECT OrgNumber from accountplan a where a.AccountPlanID=i.CustomerAccountPlanID);
update invoiceout i set `IVatNo` = (SELECT VatNumber from accountplan a where a.AccountPlanID=i.CustomerAccountPlanID);
update invoiceout i set `IMobile` = (SELECT Mobile from accountplan a where a.AccountPlanID=i.CustomerAccountPlanID);
update invoiceout i set `IWeb` = (SELECT Web from accountplan a where a.AccountPlanID=i.CustomerAccountPlanID);
update invoiceout i set `Phone` = (SELECT Phone from accountplan a where a.AccountPlanID=i.CustomerAccountPlanID) WHERE `Phone`="" OR `Phone` IS NULL;
update invoiceout i set `IAddress` = (SELECT Address from accountplan a where a.AccountPlanID=i.CustomerAccountPlanID) WHERE `IAddress`="" OR `IAddress` IS NULL;
update invoiceout i set `IZipCode` = (SELECT ZipCode from accountplan a where a.AccountPlanID=i.CustomerAccountPlanID) WHERE `IZipCode`="" OR `IZipCode` IS NULL;

update invoiceout i set `SFax` = (SELECT Fax from accountplan a where a.AccountPlanID=i.CustomerAccountPlanID);
-- Did update existing field phone, since it seems to not get any value saved anywhere in Lodo, as long as the value is empty
