CREATE TABLE `altinn_config` (
 `config_id` int(11) NOT NULL auto_increment,
 `mvabankaccount` varchar(11) NOT NULL default '',
 `termintype` int(11) NOT NULL default '4',
 `fagsystemid` int(11) NOT NULL default '0',
 `password` varchar(100) NOT NULL default '',
 `batchsubno` int(11) NOT NULL default '0',
 PRIMARY KEY  (`config_id`)
);

CREATE TABLE `altinn_packet` (
 `packet_id` int(11) NOT NULL auto_increment,
 `customer_id` int(11) NOT NULL default '0',
 `status` int(11) NOT NULL default '0',
 `ts_created` int(11) NOT NULL default '0',
 `ts_modified` int(11) NOT NULL default '0',
 `modified_by` varchar(100) NOT NULL default '',
 `packettype` int(11) NOT NULL default '0',
 `termin` int(11) NOT NULL default '0',
 `termintype` int(11) NOT NULL default '0',
 `year` int(11) NOT NULL default '0',
 PRIMARY KEY  (`packet_id`)
);

CREATE TABLE `altinn_schema` (
 `instance_id` int(11) NOT NULL auto_increment,
 `packet_id` int(11) NOT NULL default '0',
 `schematype` int(11) NOT NULL default '0',
 `schemarevision` int(11) NOT NULL default '0',
 `data` text,
 PRIMARY KEY  (`instance_id`)
);
