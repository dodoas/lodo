DROP TABLE IF EXISTS `filarkiv`;
CREATE TABLE `filarkiv` (
  `filarkiv_id` int(11) NOT NULL auto_increment,
  `filkategori_id` int(11) NOT NULL default '0',
  `ts_created` int(14) NOT NULL default '0',
  `ts_modified` int(14) NOT NULL default '0',
  `modified_by` varchar(100) NOT NULL default '',
  `navn` varchar(250) NOT NULL default 'ingen navn',
  `fildata` longblob default NULL,
  `mimetype` varchar(250) NOT NULL default '',
  `original_name` varchar(250) NOT NULL default '',
  `size` int(10) NOT NULL default '',
  `beskrivelse` text NOT NULL default '',
  `tilgjengeligFra` int(14) default NULL,
  `tilgjengeligTil` int(14) default NULL,
  `year` int(11) default NULL,
  PRIMARY KEY  (`filarkiv_id`)
) ENGINE=MyISAM DEFAULT CHARSET=latin1;

DROP TABLE IF EXISTS `filkategori`;
CREATE TABLE `filkategori` (
  `filkategori_id` int(11) NOT NULL auto_increment,
  `ts_created` int(11) NOT NULL default '0',
  `ts_modified` int(11) NOT NULL default '0',
  `modified_by` varchar(100) NOT NULL default '',
  `navn` varchar(250) NOT NULL default 'ingen navn',
  `beskrivelse` text NOT NULL default '',
  PRIMARY KEY  (`filkategori_id`)
) ENGINE=MyISAM DEFAULT CHARSET=latin1;

INSERT INTO `filkategori` VALUES ('', CURRENT_TIMESTAMP(), CURRENT_TIMESTAMP(), 'System default', 'Mine Dokumenter', 'Et sted å legge filer på');



INSERT INTO confdbfields (ConfDBFieldID, TableField, Active, TS, ValidFrom, ValidTo, TableName, PrimaryKey, FormHeight, FormWidth, DefaultValue, FormType, FieldType, FieldNull, FieldExtra, DefaultLink, InputValidation, OutputValidation, Required, FormTypeEdit, FieldExtraEdit) 
VALUES ('', 'filarkiv_id', 1, CURRENT_TIMESTAMP(), '2005-03-10 00:00:00', '9999-12-31 00:00:00', 'filarkiv', 1, 1, 70, '', 'hidden', 'int(11)', '', '', NULL, 'Int', 'Int', 0, '', '');
INSERT INTO confdbfields (ConfDBFieldID, TableField, Active, TS, ValidFrom, ValidTo, TableName, PrimaryKey, FormHeight, FormWidth, DefaultValue, FormType, FieldType, FieldNull, FieldExtra, DefaultLink, InputValidation, OutputValidation, Required, FormTypeEdit, FieldExtraEdit) 
VALUES ('', 'kategori_id', 1, CURRENT_TIMESTAMP(), '2005-03-10 00:00:00', '9999-12-31 00:00:00', 'filarkiv', 0, 1, 70, '', 'text', 'int(11)', '', '', NULL, 'Int', 'Int', 0, '', '');
INSERT INTO confdbfields (ConfDBFieldID, TableField, Active, TS, ValidFrom, ValidTo, TableName, PrimaryKey, FormHeight, FormWidth, DefaultValue, FormType, FieldType, FieldNull, FieldExtra, DefaultLink, InputValidation, OutputValidation, Required, FormTypeEdit, FieldExtraEdit) 
VALUES ('', 'ts_created', 0, CURRENT_TIMESTAMP(), '2005-03-10 00:00:00', '9999-12-31 00:00:00', 'filarkiv', 0, 1, 70, '', 'hidden', 'int(14)', '', '', NULL, 'Int', 'Int', 0, '', '');
INSERT INTO confdbfields (ConfDBFieldID, TableField, Active, TS, ValidFrom, ValidTo, TableName, PrimaryKey, FormHeight, FormWidth, DefaultValue, FormType, FieldType, FieldNull, FieldExtra, DefaultLink, InputValidation, OutputValidation, Required, FormTypeEdit, FieldExtraEdit) 
VALUES ('', 'ts_modified', 0, CURRENT_TIMESTAMP(), '2005-03-10 00:00:00', '9999-12-31 00:00:00', 'filarkiv', 0, 1, 70, '', 'hidden', 'int(14)', '', '', NULL, 'Int', 'Int', 0, '', '');
INSERT INTO confdbfields (ConfDBFieldID, TableField, Active, TS, ValidFrom, ValidTo, TableName, PrimaryKey, FormHeight, FormWidth, DefaultValue, FormType, FieldType, FieldNull, FieldExtra, DefaultLink, InputValidation, OutputValidation, Required, FormTypeEdit, FieldExtraEdit) 
VALUES ('', 'modified_by', 1, CURRENT_TIMESTAMP(), '2005-03-10 00:00:00', '9999-12-31 00:00:00', 'filarkiv', 0, 1, 70, '', 'text', 'varchar(100)', '', '', NULL, 'String', 'String', 0, '', '');
INSERT INTO confdbfields (ConfDBFieldID, TableField, Active, TS, ValidFrom, ValidTo, TableName, PrimaryKey, FormHeight, FormWidth, DefaultValue, FormType, FieldType, FieldNull, FieldExtra, DefaultLink, InputValidation, OutputValidation, Required, FormTypeEdit, FieldExtraEdit) 
VALUES ('', 'navn', 1, CURRENT_TIMESTAMP(), '2005-03-10 00:00:00', '9999-12-31 00:00:00', 'filarkiv', 0, 1, 70, '', 'text', 'varchar(250)', '', '', NULL, 'String', 'String', 0, '', '');
INSERT INTO confdbfields (ConfDBFieldID, TableField, Active, TS, ValidFrom, ValidTo, TableName, PrimaryKey, FormHeight, FormWidth, DefaultValue, FormType, FieldType, FieldNull, FieldExtra, DefaultLink, InputValidation, OutputValidation, Required, FormTypeEdit, FieldExtraEdit) 
VALUES ('', 'beskrivelse', 1, CURRENT_TIMESTAMP(), '2005-03-10 00:00:00', '9999-12-31 00:00:00', 'filarkiv', 0, 1, 70, '', 'text', 'varchar(250)', '', '', NULL, 'String', 'String', 0, '', '');
INSERT INTO confdbfields (ConfDBFieldID, TableField, Active, TS, ValidFrom, ValidTo, TableName, PrimaryKey, FormHeight, FormWidth, DefaultValue, FormType, FieldType, FieldNull, FieldExtra, DefaultLink, InputValidation, OutputValidation, Required, FormTypeEdit, FieldExtraEdit) 
VALUES ('', 'fildata', 1, CURRENT_TIMESTAMP(), '2005-03-10 00:00:00', '9999-12-31 00:00:00', 'filarkiv', 0, 1, 70, '', 'text', 'varchar(250)', '', '', NULL, 'String', 'String', 0, '', '');
INSERT INTO confdbfields (ConfDBFieldID, TableField, Active, TS, ValidFrom, ValidTo, TableName, PrimaryKey, FormHeight, FormWidth, DefaultValue, FormType, FieldType, FieldNull, FieldExtra, DefaultLink, InputValidation, OutputValidation, Required, FormTypeEdit, FieldExtraEdit) 
VALUES ('', 'mimetype', 1, CURRENT_TIMESTAMP(), '2005-03-10 00:00:00', '9999-12-31 00:00:00', 'filarkiv', 0, 1, 70, '', 'text', 'varchar(250)', '', '', NULL, 'String', 'String', 0, '', '');
INSERT INTO confdbfields (ConfDBFieldID, TableField, Active, TS, ValidFrom, ValidTo, TableName, PrimaryKey, FormHeight, FormWidth, DefaultValue, FormType, FieldType, FieldNull, FieldExtra, DefaultLink, InputValidation, OutputValidation, Required, FormTypeEdit, FieldExtraEdit) 
VALUES ('', 'original_name', 1, CURRENT_TIMESTAMP(), '2005-03-10 00:00:00', '9999-12-31 00:00:00', 'filarkiv', 0, 1, 70, '', 'text', 'varchar(250)', '', '', NULL, 'String', 'String', 0, '', '');
INSERT INTO confdbfields (ConfDBFieldID, TableField, Active, TS, ValidFrom, ValidTo, TableName, PrimaryKey, FormHeight, FormWidth, DefaultValue, FormType, FieldType, FieldNull, FieldExtra, DefaultLink, InputValidation, OutputValidation, Required, FormTypeEdit, FieldExtraEdit) 
VALUES ('', 'size', 1, CURRENT_TIMESTAMP(), '2005-03-10 00:00:00', '9999-12-31 00:00:00', 'filarkiv', 0, 1, 70, '', 'text', 'int(10)', '', '', NULL, 'Int', 'Int', 0, '', '');
INSERT INTO confdbfields (ConfDBFieldID, TableField, Active, TS, ValidFrom, ValidTo, TableName, PrimaryKey, FormHeight, FormWidth, DefaultValue, FormType, FieldType, FieldNull, FieldExtra, DefaultLink, InputValidation, OutputValidation, Required, FormTypeEdit, FieldExtraEdit) 
VALUES ('', 'tilgjengeligFra', 1, CURRENT_TIMESTAMP(), '2005-03-10 00:00:00', '9999-12-31 00:00:00', 'filarkiv', 0, 1, 70, '', 'text', 'datetime', '', '', NULL, 'date', 'date', 0, '', '');
INSERT INTO confdbfields (ConfDBFieldID, TableField, Active, TS, ValidFrom, ValidTo, TableName, PrimaryKey, FormHeight, FormWidth, DefaultValue, FormType, FieldType, FieldNull, FieldExtra, DefaultLink, InputValidation, OutputValidation, Required, FormTypeEdit, FieldExtraEdit) 
VALUES ('', 'tilgjengeligTil', 1, CURRENT_TIMESTAMP(), '2005-03-10 00:00:00', '9999-12-31 00:00:00', 'filarkiv', 0, 1, 70, '', 'text', 'datetime', '', '', NULL, 'date', 'date', 0, '', '');

INSERT INTO confdbfields (ConfDBFieldID, TableField, Active, TS, ValidFrom, ValidTo, TableName, PrimaryKey, FormHeight, FormWidth, DefaultValue, FormType, FieldType, FieldNull, FieldExtra, DefaultLink, InputValidation, OutputValidation, Required, FormTypeEdit, FieldExtraEdit) 
VALUES ('', 'filkategori_id', 1, CURRENT_TIMESTAMP(), '2005-03-10 00:00:00', '9999-12-31 00:00:00', 'filkategori', 1, 1, 70, '', 'hidden', 'int(11)', '', '', NULL, 'Int', 'Int', 0, '', '');
INSERT INTO confdbfields (ConfDBFieldID, TableField, Active, TS, ValidFrom, ValidTo, TableName, PrimaryKey, FormHeight, FormWidth, DefaultValue, FormType, FieldType, FieldNull, FieldExtra, DefaultLink, InputValidation, OutputValidation, Required, FormTypeEdit, FieldExtraEdit) 
VALUES ('', 'navn', 1, CURRENT_TIMESTAMP(), '2005-03-10 00:00:00', '9999-12-31 00:00:00', 'filkategori', 0, 1, 70, '', 'text', 'varchar(250)', '', '', NULL, 'String', 'String', 0, '', '');
INSERT INTO confdbfields (ConfDBFieldID, TableField, Active, TS, ValidFrom, ValidTo, TableName, PrimaryKey, FormHeight, FormWidth, DefaultValue, FormType, FieldType, FieldNull, FieldExtra, DefaultLink, InputValidation, OutputValidation, Required, FormTypeEdit, FieldExtraEdit) 
VALUES ('', 'beskrivelse', 1, CURRENT_TIMESTAMP(), '2005-03-10 00:00:00', '9999-12-31 00:00:00', 'filkategori', 0, 1, 70, '', 'text', 'varchar(250)', '', '', NULL, 'String', 'String', 0, '', '');
INSERT INTO confdbfields (ConfDBFieldID, TableField, Active, TS, ValidFrom, ValidTo, TableName, PrimaryKey, FormHeight, FormWidth, DefaultValue, FormType, FieldType, FieldNull, FieldExtra, DefaultLink, InputValidation, OutputValidation, Required, FormTypeEdit, FieldExtraEdit) 
VALUES ('', 'fildata', 1, CURRENT_TIMESTAMP(), '2005-03-10 00:00:00', '9999-12-31 00:00:00', 'filkategori', 0, 1, 70, '', 'text', 'varchar(250)', '', '', NULL, 'String', 'String', 0, '', '');
INSERT INTO confdbfields (ConfDBFieldID, TableField, Active, TS, ValidFrom, ValidTo, TableName, PrimaryKey, FormHeight, FormWidth, DefaultValue, FormType, FieldType, FieldNull, FieldExtra, DefaultLink, InputValidation, OutputValidation, Required, FormTypeEdit, FieldExtraEdit) 



INSERT INTO roletableaccess (RoleTableAccessID, TableName, TableAccess, RoleID, TS) VALUES ('', 'filkategori', 1, 0, CURRENT_TIMESTAMP());
INSERT INTO roletableaccess (RoleTableAccessID, TableName, TableAccess, RoleID, TS) VALUES ('', 'filarkiv', 3, 1, CURRENT_TIMESTAMP());

INSERT INTO roletemplate (Interface, Module, Template, Cust, AccessLevel, AuthType, TS, RoleTemplateID, Log, LogReferer, LogUserAgent, OnlyAllowInternUser) 
VALUES ('lodo', 'filarkiv', 'index', NULL, 1, 'web', CURRENT_TIMESTAMP(), '', 1, 1, 1, 1);
INSERT INTO roletemplate (Interface, Module, Template, Cust, AccessLevel, AuthType, TS, RoleTemplateID, Log, LogReferer, LogUserAgent, OnlyAllowInternUser) 
VALUES ('lodo', 'filarkiv', 'mappe', NULL, 1, 'web', CURRENT_TIMESTAMP(), '', 1, 1, 1, 1);
INSERT INTO roletemplate (Interface, Module, Template, Cust, AccessLevel, AuthType, TS, RoleTemplateID, Log, LogReferer, LogUserAgent, OnlyAllowInternUser) 
VALUES ('lodo', 'filarkiv', 'fil', NULL, 1, 'web', CURRENT_TIMESTAMP(), '', 1, 1, 1, 1);
INSERT INTO roletemplate (Interface, Module, Template, Cust, AccessLevel, AuthType, TS, RoleTemplateID, Log, LogReferer, LogUserAgent, OnlyAllowInternUser) 
VALUES ('lodo', 'filarkiv', 'lagre_mappe', NULL, 1, 'web', CURRENT_TIMESTAMP(), '', 1, 1, 1, 1);
INSERT INTO roletemplate (Interface, Module, Template, Cust, AccessLevel, AuthType, TS, RoleTemplateID, Log, LogReferer, LogUserAgent, OnlyAllowInternUser) 
VALUES ('lodo', 'filarkiv', 'lagre_fil', NULL, 1, 'web', CURRENT_TIMESTAMP(), '', 1, 1, 1, 1);
INSERT INTO roletemplate (Interface, Module, Template, Cust, AccessLevel, AuthType, TS, RoleTemplateID, Log, LogReferer, LogUserAgent, OnlyAllowInternUser) 
VALUES ('lodo', 'filarkiv', 'vis_fil', NULL, 1, 'web', CURRENT_TIMESTAMP(), '', 1, 1, 1, 1);

