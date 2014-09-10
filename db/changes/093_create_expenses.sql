CREATE TABLE IF NOT EXISTS `expense_lines` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `department_id` int(11) NOT NULL,
  `expense_period_id` int(11) NOT NULL,
  `supplier_name` varchar(255) NOT NULL DEFAULT  '',
  `beer_purchased` float(16, 2) NOT NULL DEFAULT  '0',
  `wine_purchased` float(16, 2) NOT NULL DEFAULT  '0',
  `spirits_purchased` float(16, 2) NOT NULL DEFAULT  '0',
  PRIMARY KEY  (`id`)

) ENGINE=MyISAM  DEFAULT CHARSET=utf8;

CREATE TABLE IF NOT EXISTS `expense_groups` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `group_id` int(11) NOT NULL,
  `department_id` int(11) NOT NULL,
  `expense_period_id` int(11) NOT NULL,
  `stock_level_start_year` float(16, 2) NOT NULL DEFAULT  '0',
  `stock_level_end_year` float(16, 2) NOT NULL DEFAULT  '0',
  `expected_stock_level_this_year` float(16, 2) NOT NULL DEFAULT  '0',
  `expected_stock_level_next_year` float(16, 2) NOT NULL DEFAULT  '0',
  PRIMARY KEY  (`id`)

) ENGINE=MyISAM  DEFAULT CHARSET=utf8;

CREATE TABLE IF NOT EXISTS `expense_projects` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `project_id` int(11) NOT NULL,
  `department_id` int(11) NOT NULL,
  `expense_period_id` int(11) NOT NULL,
  `stock_level_start_year` float(16, 2) NOT NULL DEFAULT  '0',
  `stock_level_end_year` float(16, 2) NOT NULL DEFAULT  '0',
  PRIMARY KEY  (`id`)

) ENGINE=MyISAM  DEFAULT CHARSET=utf8;

CREATE TABLE IF NOT EXISTS `expense_periods` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `year` int(11) NOT NULL,
  PRIMARY KEY  (`id`)

) ENGINE=MyISAM  DEFAULT CHARSET=utf8;

-- No idea what does this do, but i think that this is required...
INSERT INTO `roletemplate` (`Interface`, `Module`, `Template`, `Cust`, `AccessLevel`, `AuthType`, `TS`, `Log`, `LogReferer`, `LogUserAgent`, `OnlyAllowInternUser`, `Active`, `InterfaceExtends`) VALUES
('lodo', 'report', 'expense', 0, 1, 'web', '2005-05-13 02:05:49', 1, 1, 1, 1, 0, 'lodo'),
('lodo', 'report', 'expenseprint', 0, 1, 'web', '2005-05-12 20:35:49', 1, 1, 1, 1, 0, 'lodo');
