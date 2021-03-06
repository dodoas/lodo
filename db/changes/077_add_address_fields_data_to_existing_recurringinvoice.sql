update recurringout i set `SName` = (SELECT IName from company c where c.CompanyID=i.FromCompanyID);
update recurringout i set `SAddress` = (SELECT IAddress from company c where c.CompanyID=i.FromCompanyID);
update recurringout i set `SCity` = (SELECT ICity from company c where c.CompanyID=i.FromCompanyID);
update recurringout i set `SBankAccount` = (SELECT BankAccount from company c where c.CompanyID=i.FromCompanyID);
update recurringout i set `SZipCode` = (SELECT IZipCode from company c where c.CompanyID=i.FromCompanyID);
update recurringout i set `SCountryCode` = (SELECT ICountryCode from company c where c.CompanyID=i.FromCompanyID);
update recurringout i set `SPhone` = (SELECT Phone from company c where c.CompanyID=i.FromCompanyID);
update recurringout i set `SFax` = (SELECT Fax from company c where c.CompanyID=i.FromCompanyID);
update recurringout i set `SMobile` = (SELECT Mobile from company c where c.CompanyID=i.FromCompanyID);
update recurringout i set `SEmail` = (SELECT Email from company c where c.CompanyID=i.FromCompanyID);
update recurringout i set `SWeb` = (SELECT WWW from company c where c.CompanyID=i.FromCompanyID);
update recurringout i set `SOrgNo` = (SELECT OrgNumber from company c where c.CompanyID=i.FromCompanyID);
update recurringout i set `SVatNo` = (SELECT VatNumber from company c where c.CompanyID=i.FromCompanyID);

update recurringout i set `IOrgNo` = (SELECT OrgNumber from accountplan a where a.AccountPlanID=i.CustomerAccountPlanID);
update recurringout i set `IVatNo` = (SELECT VatNumber from accountplan a where a.AccountPlanID=i.CustomerAccountPlanID);
update recurringout i set `IMobile` = (SELECT Mobile from accountplan a where a.AccountPlanID=i.CustomerAccountPlanID);
update recurringout i set `IWeb` = (SELECT Web from accountplan a where a.AccountPlanID=i.CustomerAccountPlanID);
update recurringout i set `Phone` = (SELECT Phone from accountplan a where a.AccountPlanID=i.CustomerAccountPlanID) WHERE `Phone`="" OR `Phone` IS NULL;
update recurringout i set `IAddress` = (SELECT IAddress from accountplan a where a.AccountPlanID=i.CustomerAccountPlanID) WHERE `IAddress`="" OR `IAddress` IS NULL;
update recurringout i set `IZipCode` = (SELECT IZipCode from accountplan a where a.AccountPlanID=i.CustomerAccountPlanID) WHERE `IZipCode`="" OR `IZipCode` IS NULL;

-- Did update existing field phone, since it seems to not get any value saved anywhere in Lodo, as long as the value is empty
