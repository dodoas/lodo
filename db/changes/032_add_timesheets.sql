CREATE TABLE IF NOT EXISTS `timesheetpasswords` (
  `AccountPlanID` int(20) NOT NULL,
  `Password` char(41) NOT NULL,
  `LastLogin` datetime NOT NULL,
  UNIQUE KEY `AccountPlanID` (`AccountPlanID`),
  UNIQUE KEY `AccountPlanID_2` (`AccountPlanID`)
) ENGINE=MyISAM DEFAULT CHARSET=latin1;

CREATE TABLE IF NOT EXISTS `timesheetperiods` (
  `TimesheetPeriodID` int(11) NOT NULL auto_increment,
  `Period` char(7) NOT NULL,
  `AccountPlanID` int(11) NOT NULL,
  `Locked` int(1) NOT NULL,
  PRIMARY KEY  (`TimesheetPeriodID`),
  UNIQUE KEY `TimesheetPeriodID` (`TimesheetPeriodID`)
) ENGINE=MyISAM  DEFAULT CHARSET=latin1 AUTO_INCREMENT=1 ;

CREATE TABLE IF NOT EXISTS `timesheets` (
  `EntryID` int(20) NOT NULL auto_increment,
  `AccountPlanID` int(20) NOT NULL,
  `BeginTime` time NOT NULL,
  `EndTime` time NOT NULL,
  `SumTime` time NOT NULL,
  `Date` date NOT NULL,
  `Project` int(20) NOT NULL,
  `WorkType` int(20) NOT NULL,
  `Comment` text NOT NULL,
  PRIMARY KEY  (`EntryID`)
) ENGINE=MyISAM  DEFAULT CHARSET=latin1 AUTO_INCREMENT=1 ;

CREATE TABLE IF NOT EXISTS `timesheetsworktype` (
  `WorkTypeID` int(11) NOT NULL auto_increment,
  `Name` char(180) NOT NULL,
  PRIMARY KEY  (`WorkTypeID`),
  UNIQUE KEY `WorkTypeID` (`WorkTypeID`)
) ENGINE=MyISAM  DEFAULT CHARSET=latin1 AUTO_INCREMENT=1 ;
