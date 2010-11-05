<?
$_QUERY['form']['companycontact']           = "select P.PersonID, P.FirstName, P.LastName from person as P, companypersonstruct as S where S.CompanyID='" . $args['company_id'] . "' and P.PersonID=S.PersonID order by FirstName";
$_QUERY['form']['typeclassificationmenu']   = "select MenuValue, MenuChoice from confmenues where MenuName='ClassificationID' order by MenuChoice";
$_QUERY['form']['typeaccesslevelmenu']      = "select MenuValue, MenuChoice from confmenues where MenuName='AccessLevel'      order by MenuChoice";
$_QUERY['form']['typerequesttypemenu']      = "select MenuValue, MenuChoice from confmenues where MenuName='RequestType'      order by MenuChoice";
$_QUERY['form']['typerequestimgalignmenu']  = "select MenuValue, MenuChoice from confmenues where MenuName='ImgAlign'      order by MenuChoice";
$_QUERY['form']['typerequeststatusmenu']    = "select MenuValue, MenuChoice from confmenues where MenuName='RequestStatus'    order by MenuChoice";
$_QUERY['form']['typerequestprioritymenu']  = "select MenuValue, MenuChoice from confmenues where MenuName='RequestPriority'  order by MenuChoice";
$_QUERY['form']['typevatperiodmenu']    = "select MenuValue, MenuChoice from confmenues where MenuName='VatPeriod'  order by MenuChoice";
$_QUERY['form']['typeregionmenu']           = "select MenuValue from confmenues where MenuName='Region' order by MenuChoice";
$_QUERY['form']['typesalespersonmenu']      = "select p.PersonID, p.FirstName, p.LastName from person p, personparameter as pp where p.PersonID=pp.PersonID and pp.Type=4 and p.Active=1 order by FirstName asc";

$_QUERY['form']['typeslastatussearch']      = "select MenuValue, MenuChoice from confmenues where MenuName='SlaStatusSearch' order by MenuChoice";
$_QUERY['form']['typeslaeffortchoice']      = "select MenuValue, MenuChoice from confmenues where MenuName='UseEffort' order by MenuChoice";
$_QUERY['form']['typeslastatusmenu']        = "select MenuValue, MenuChoice from confmenues where MenuName='SlaStatus' order by MenuChoice";
$_QUERY['form']['typeslafrequencymenu']     = "select MenuValue, MenuChoice from confmenues where MenuName='SlaFrequency' order by MenuChoice";
$_QUERY['form']['typeslaregionmenu']        = "select distinct s.Region from sla as s, slagoalmonthly as g where s.SlaID=g.SlaID and s.Region is not null and s.Region != '' order by s.Region";
$_QUERY['form']['typeslacompanymenu']       = "select distinct s.CompanyID, c.VName from sla as s left join company as c on s.CompanyID=c.ExternalID, slagoalmonthly as g where s.SlaID=g.SlaID and s.CompanyID > 0 order by s.CompanyID";
$_QUERY['form']['typeslasalespersonmenu']   = "select p.PersonID, p.FirstName, p.LastName from person p, personparameter as pp, sla as s, slagoalmonthly as g where s.SlaID=g.SlaID and p.PersonID=pp.PersonID and p.PersonID=s.SalesPersonID and pp.Type=4 and p.Active=1 group by p.PersonID order by p.FirstName asc";
$_QUERY['form']['typeslaeffortcodemenu']    = "select cm.MenuValue, cm2.MenuChoice, cm.MenuChoice from confmenues as cm, confmenues as cm2 where (cm2.MenuName='SlaType' and cm2.MenuValue=substring(cm.MenuName, 8,1)) and (cm.MenuName='SlaType1' or cm.MenuName='SlaType2' or cm.MenuName='SlaType3' or cm.MenuName='SlaType4' or cm.MenuName='SlaType5' or cm.MenuName='SlaType6' or cm.MenuName='SlaType7' or cm.MenuName='SlaType8') group by cm.MenuValue order by cm2.MenuChoice";
$_QUERY['form']['typeslasalecompanymenu']   = "select distinct department.SaleCompanyID, customer.VName from company as department left join company as customer on department.SaleCompanyID=customer.ExternalID where department.SaleCompanyID > 0 order by department.SaleCompanyID";
$_QUERY['form']['typesla1menu']             = "select MenuValue, MenuChoice from confmenues where MenuName='SlaType1' order by MenuChoice";
$_QUERY['form']['typesla2menu']             = "select MenuValue, MenuChoice from confmenues where MenuName='SlaType2' order by MenuChoice";
$_QUERY['form']['typesla3menu']             = "select MenuValue, MenuChoice from confmenues where MenuName='SlaType3' order by MenuChoice";
$_QUERY['form']['typesla4menu']             = "select MenuValue, MenuChoice from confmenues where MenuName='SlaType4' order by MenuChoice";
$_QUERY['form']['typesla5menu']             = "select MenuValue, MenuChoice from confmenues where MenuName='SlaType5' order by MenuChoice";
$_QUERY['form']['typesla6menu']             = "select MenuValue, MenuChoice from confmenues where MenuName='SlaType6' order by MenuChoice";
$_QUERY['form']['typesla7menu']             = "select MenuValue, MenuChoice from confmenues where MenuName='SlaType7' order by MenuChoice";
$_QUERY['form']['typesla8menu']             = "select MenuValue, MenuChoice from confmenues where MenuName='SlaType8' order by MenuChoice";

$_QUERY['form']['faqLangMenu']              = "select MenuValue, MenuChoice from confmenues where MenuName='FaqLang' order by MenuChoice";
$_QUERY['form']['faqCategoryMenu']          = "select MenuValue, MenuChoice from confmenues where MenuName='FaqCategory' order by MenuChoice";

$_QUERY['form']['MenuReportID']             = "select MenuValue, MenuChoice from confmenues where MenuName='MenuReportID' order by MenuChoice";
$_QUERY['form']['VoucherDescriptionID']     = "select MenuID, MenuChoice from confmenues where MenuName='VoucherDescriptionID' order by MenuChoice";
$_QUERY['form']['DebitColor']               = "select MenuValue, MenuChoice from confmenues where MenuName='DebitColor' order by MenuChoice";
$_QUERY['form']['CreditColor']              = "select MenuValue, MenuChoice from confmenues where MenuName='CreditColor' order by MenuChoice";
$_QUERY['form']['VoucherType']              = "select MenuValue, MenuChoice from confmenues where MenuName='VoucherType' order by MenuChoice";
$_QUERY['form']['AccountPlanType']          = "select MenuValue, MenuChoice from confmenues where MenuName='AccountPlanType' order by MenuChoice";
$_QUERY['form']['VoucherSearchType']        = "select MenuValue, MenuChoice from confmenues where MenuName='VoucherSearchType' order by MenuChoice";
$_QUERY['form']['ImageAlignMenu']           = "select MenuValue, MenuChoice from confmenues where MenuName='ImageAlignMenu' order by MenuChoice";
$_QUERY['form']['InvoiceCommentCustomerPosition'] = "select MenuValue, MenuChoice from confmenues where MenuName='InvoiceCommentCustomerPosition' order by MenuChoice";
$_QUERY['form']['PaymentMeans']             = "select MenuValue, MenuChoice from confmenues where MenuName='PaymentMeans' and LanguageID='no' order by MenuChoice";
$_QUERY['form']['BankAccount']              = "select AccountNumber, concat(AccountNumber, ' - ' , AccountDescription) from account where Active=1 order by Sort";


$_QUERY['form']['avdmenu']                  = "select CompanyDepartmentID, DepartmentName from companydepartment where Active=1";
$_QUERY['form']['sonemenu']                 = "select Code, concat('Kode: ', Code, ' - ',Percent, '%') from arbeidsgiveravgift";
$_QUERY['form']['kommunemenu']              = "select KommuneID, KommuneNumber, KommuneName from kommune";
$_QUERY['form']['periodmenu']               = "select substring(Period,1,4), substring(Period,1,4) from accountperiod group by Period order by Period desc";
$_QUERY['form']['productmenu']              = "select ProductID, ProductName from product where Active='1' order by ProductNumber asc";
$_QUERY['form']['vatmenu']                  = "select * from vat where Active=1 ";
$_QUERY['form']['vatSalesMenu']             = "select * from vat where Percent>=0 and VatID<20 and Active=1 ";
$_QUERY['form']['vatBuyMenu']               = "select * from vat where Percent>=0 and VatID>=20 and Active=1 ";
$_QUERY['form']['periodallmenu']            = "select Period from accountperiod order by Period desc";
$_QUERY['form']['periodaccess2menu']        = "select Period from accountperiod where (Status=2 or Status=3) order by Period desc";
$_QUERY['form']['periodaccessmenu']         = "select Period from accountperiod where Status=2 order by Period desc";
$_QUERY['form']['PosibleSalaryYears']       = "select distinct substring(Period, 1, 4) from accountperiod where Period is not null order by Period";
$_QUERY['form']['interfacemenu']            = "select rta.Interface from roleperson as rp, roletemplateaccess as rta where rp.PersonID=".$_sess->login_id." and rp.RoleID=rta.RoleID and rta.Interface is not null and rta.Interface != '' group by rta.Interface order by rta.Interface";
$_QUERY['form']['modulemenu']               = "select rta.Module from roleperson as rp, roletemplateaccess as rta where rp.PersonID=".$_sess->login_id." and rp.RoleID=rta.RoleID and rta.Module is not null and rta.Module != '' group by rta.Module order by rta.Module";
$_QUERY['form']['templatemenu']             = "select rta.Template from roleperson as rp, roletemplateaccess as rta where rp.PersonID=".$_sess->login_id." and rp.RoleID=rta.RoleID and rta.Template is not null and rta.Template != '' group by rta.Template order by rta.Template";

$_QUERY['company']['menu']                  = "select CompanyID, VName from company order by VName asc";
$_QUERY['person']['menu']                   = "select PersonID, FirstName, LastName from person order by FirstName asc";
$_QUERY['mediacategory']['menu']            = "select MediaCategoryID, Name from mediacategory order by MediaCategoryID asc";
$_QUERY['mediakeyword']['menu']             = "select MediaKeywordID, Name from mediakeyword order by MediaKeywordID asc";
$_QUERY['slaimprovement']['avdmenu']        = "select c.CompanyID, c.VName from company as c, companystruct as tr, companystruct as ra where tr.ParentCompanyID=1 and tr.ChildCompanyID=ra.ParentCompanyID and ra.ChildCompanyID=c.CompanyID order by c.CompanyID";
$_QUERY['installation']['menu']             = "select InstallName from installation where Active=1 order by InstallName";

#RSS format on queryes for general access
# Title - 0, Description - , Publishdate - 2, URL/link - 3, Author - 4, Creator - 5
$_QUERY['rss']['birthday']              = "select concat(FirstName, ' ', LastName), concat(FirstName, ' ', LastName), BirthDate, concat('http://$_SERVER[HTTP_HOST]/$_SETUP[DISPATCH]t=person.edit&amp;a=htaccess&amp;interf=intranett&amp;PersonID=', PersonID) from person where EXTRACT(MONTH from birthdate) = EXTRACT(MONTH from NOW()) order by EXTRACT(DAY from birthdate) asc";
$_QUERY['rss']['news']                  = "select Heading, Ingress, ValidFrom, concat('http://$_SERVER[HTTP_HOST]/$_SETUP[DISPATCH]t=publish.edit&amp;a=htaccess&amp;interf=intranett&amp;PublishContentID=', PublishContentID), UpdatedByID, CreatedByID FROM publishcontent where ChapterType = 'news' and PublishContentID != PublishContentParentID order by ValidFrom desc limit 2 ";
$_QUERY['rss']['calendar']              = "select TimeStart, Subject, TimeAlarm, concat('http://$_SERVER[HTTP_HOST]/$_SETUP[DISPATCH]t=calendar.edit&amp;a=htaccess&amp;interf=intranett&amp;CalendarEventID=', CalendarEventID) from calendarevents where TO_DAYS(TimeAlarm) - TO_DAYS(NOW()) <= 3 and TO_DAYS(TimeAlarm) - TO_DAYS(NOW()) >= 0 and Subject != '' order by TimeAlarm asc";
$_QUERY['rss']['todo']                  = "select Heading, Description, TimeCreated, concat('http://$_SERVER[HTTP_HOST]/$_SETUP[DISPATCH]t=todo.edit&amp;a=htaccess&amp;interf=intranett&amp;ToDoID=', ToDoID), PersonID, PersonID from todoes order by Priority desc";
$_QUERY['rss']['project']               = "select heading, Description, ValidFrom, concat('http://$_SERVER[HTTP_HOST]/$_SETUP[DISPATCH]t=project.edit&amp;a=htaccess&amp;interf=intranett&amp;ProjectID=', ProjectID), RegisteredByID, SalesPersonID from project order by ProjectID desc";
$_QUERY['rss']['request']               = "select Heading, Heading, DateCreated, concat('http://$_SERVER[HTTP_HOST]/$_SETUP[DISPATCH]t=request.edit&amp;a=htaccess&amp;interf=intranett&amp;RequestID=', RequestID), PersonID, PersonID from request order by RequestID desc";
$_QUERY['rss']['timeliste']             = "select Description, concat(Description, ' Project: ', ProjectID, ' ProjectActivity: ', ProjectActivityID, ' Progress: ', Progress), Date, concat('http://$_SERVER[HTTP_HOST]/$_SETUP[DISPATCH]t=timeliste.edit&amp;a=htaccess&amp;interf=intranett&amp;this_Date=', Date),PersonID, PersonID from timeliste order by TS desc limit 20";
$_QUERY['rss']['drive']                 = "select DriveFrom, concat(DriveComment, ' ', DriveFrom, '-', DriveTo, ' km: ', DriveDistance) CreatedDate, concat('http://$_SERVER[HTTP_HOST]/$_SETUP[DISPATCH]t=drivedistance.edit&amp;a=htaccess&amp;DriveDistanceID=', DriveDistanceID), DrivePersonID, DrivePersonID from drivedistance order by DriveDate desc limit 20";
$_QUERY['rss']['expences']              = "select ExpenceLineDescription, concat(ExpenceLineDescription, 'Kost:', Amount), ExpenceLineDate, concat('http://$_SERVER[HTTP_HOST]/$_SETUP[DISPATCH]t=expence.edit&amp;a=htaccess&amp;interf=intranett&amp;ExpenceID=', ExpenceID), ExpencePersonID, ExpencePersonID from expenceline order by ExpenceLineDate desc limit 20";
$_QUERY['rss']['Resultat']              = "select sum(AmountIn) - sum(AmountOut), sum(AmountIn) - sum(AmountOut), NOW(), concat('http://$_SERVER[HTTP_HOST]/$_SETUP[DISPATCH]t=journal.edit&amp;a=htaccess&amp;interf=intranett&amp;JounralID=', JournalID) from voucher where AccountplanID >= 1000 and  AccountplanID <= 2999";
$_QUERY['rss']['Kontoendringer']        = "select AccountDescription, concat(AccountDescription, ' sum inn: ', AmountIn, ' sum ut:', AmountOut), InterestDate, concat('http://$_SERVER[HTTP_HOST]/$_SETUP[DISPATCH]t=account.edit&amp;a=htaccess&amp;interf=intranett&amp;AccountID=', AccountID)  from accountline where AccountID=11 order by InterestDate desc limit 20";
$_QUERY['rss']['kunde_ordre']           = "select DName, concat(OrderStatus, ' ', OrderComment, ' Bestilt sum: ', TotalOrdered), OrderDate,
concat('http://$_SERVER[HTTP_HOST]/$_SETUP[DISPATCH]t=order.edit&amp;a=htaccess&amp;interf=intranett&amp;OrderID=', OrderID), ContactID, ResponsibleID from ordercust order by OrderDate desc limit 20";
$_QUERY['rss']['ut_fakturaer']          = "select DName, concat(ProjectName, OutInvoiceDescription, ' sum: ', InvoiceTotalRounded),
concat('http://$_SERVER[HTTP_HOST]/$_SETUP[DISPATCH]t=expence.edit&amp;a=htaccess&amp;interf=intranett&amp;ExpenceID=', ExpenceID), InvoiceContactID, CreatedByID from invoiceout order by OutInvoiceIDate desc limit 20";
$_QUERY['rss']['sist_endrede_firmaer']  = "select VName, VName, TS,
concat('http://$_SERVER[HTTP_HOST]/$_SETUP[DISPATCH]t=company.edit&amp;a=htaccess&amp;interf=intranett&amp;CompanyID=', CompanyID), ChangedByPersonID, CreatedByID from company order by TS desc limit 20";
$_QUERY['rss']['sist_endrede_personer'] = "select concat(FirstName, '', LastName), concat(FirstName, '', LastName), TS,
concat('http://$_SERVER[HTTP_HOST]/$_SETUP[DISPATCH]t=person.edit&amp;a=htaccess&amp;interf=intranett&amp;PersonID=', PersonID), ChangedByPersonID, CreatedByID from person order by TS desc limit 20";

$_QUERY['rss_extranett']['timeliste']         = "select t.Description, concat(t.Description, ' Project: ', t.ProjectID, ' ProjectActivity: ', t.ProjectActivityID, ' Progress: ',
t.Progress),
t. Date, concat('http://$_SERVER[HTTP_HOST]/$_SETUP[DISPATCH]t=timeliste.edit&amp;a=htaccess&amp;interf=intranett&amp;this_Date=', Date),PersonID, PersonID from timeliste as t,
project as p where t.ProjectID=p.ProjectID and p.CompanyID=$_sess->company_id order by TS desc limit 20";
$_QUERY['rss_extranett']['request']           = "select Heading, Heading, DateCreated,
concat('http://$_SERVER[HTTP_HOST]/$_SETUP[DISPATCH]t=request.edit&amp;a=htaccess&amp;interf=intranett&amp;RequestID=', RequestID), PersonID, PersonID from request where
CompanyID=$_sess->company_id order by RequestID desc";
$_QUERY['rss_extranett']['requestcomment']    = "select Heading, Heading, DateCreated,
concat('http://$_SERVER[HTTP_HOST]/$_SETUP[DISPATCH]t=request.edit&amp;a=htaccess&amp;interf=intranett&amp;RequestID=', r.RequestID), c.Description, c.PersonID from request as r,
requestcomment as c where r.RequestID=c.RequestID and r.CompanyID=$_sess->company_id order by c.TS desc";
?>
