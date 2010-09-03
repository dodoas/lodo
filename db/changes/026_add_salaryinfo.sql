SET SQL_MODE="NO_AUTO_VALUE_ON_ZERO";

CREATE TABLE IF NOT EXISTS `salaryinfo` (
  `SalaryInfoID` int(11) NOT NULL auto_increment,
  `SalaryConfID` int(11) NOT NULL,
  `amount` char(128) NOT NULL,
  PRIMARY KEY  (`SalaryInfoID`)
) ENGINE=MyISAM  DEFAULT CHARSET=latin1 AUTO_INCREMENT=1 ;
