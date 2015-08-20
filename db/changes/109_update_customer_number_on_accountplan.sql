UPDATE accountplan SET CustomerNumber = IF(AccountplanType = 'customer', AccountPlanID, 0);
