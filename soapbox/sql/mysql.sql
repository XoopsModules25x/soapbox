# phpMyAdmin SQL Dump
# version 2.5.3
# http://www.phpmyadmin.net
#
# Host: localhost
# Generation Time: Dec 12, 2003 at 06:03 AM
# Server version: 3.23.56
# PHP Version: 4.3.3
# 
# Database : `xoops`
# 

# --------------------------------------------------------

#
# Table structure for table `sbcolumns`
#

CREATE TABLE `sbcolumns` (
	`columnID` tinyint(4) NOT NULL auto_increment,
	`author` int(8) NOT NULL,
	`name` varchar(255) NOT NULL default '',
	`description` text NOT NULL,
	`total` int(11) NOT NULL default '0',
	`weight` int(11) NOT NULL default '1',
	`colimage` varchar(255) NOT NULL default 'blank.png',
	`created` int(11) NOT NULL default '1033141070',
	PRIMARY KEY  (`columnID`),
	UNIQUE KEY columnID (`columnID`)
) ENGINE=MyISAM COMMENT='Soapbox by hsalazar and domifara';

#
# Dumping data for table `sbcolumns`
#

# --------------------------------------------------------

#
# Table structure for table `sbarticles`
#

CREATE TABLE `sbarticles` (
	`articleID` int(8) NOT NULL auto_increment,
	`columnID` tinyint(4) NOT NULL default '0',
	`headline` varchar(255) NOT NULL default '0',
	`lead` text NOT NULL,
	`bodytext` text NOT NULL,
	`teaser` text NOT NULL,
	`uid` int(6) default '1',
	`submit` int(1) NOT NULL default '0',
	`datesub` int(11) NOT NULL default '1033141070',
	`counter` int(8) unsigned NOT NULL default '0',
	`weight` int(11) NOT NULL default '1',
	`html` int(11) NOT NULL default '0',
	`smiley` int(11) NOT NULL default '0',
	`xcodes` int(11) NOT NULL default '0',
	`breaks` int(11) NOT NULL default '1',
	`block` int(11) NOT NULL default '0',
	`artimage` varchar(255) NOT NULL default '',
	`votes` int(11) NOT NULL default '0',
	`rating` double(6,4) NOT NULL default '0.0000',
	`commentable` int(11) NOT NULL default '0',
	`offline` int(11) NOT NULL default '0',
	`notifypub` int(11) NOT NULL default '0',
	PRIMARY KEY  (`articleID`),
	UNIQUE KEY articleID (`articleID`),
	FULLTEXT KEY bodytext (`bodytext`)
) ENGINE=MyISAM COMMENT='Soapbox by hsalazar and domifara';

#
# Dumping data for table `sbarticles`
#

# --------------------------------------------------------

#
# Table structure for table `sbvotedata`
#

CREATE TABLE `sbvotedata` (
	`ratingid` int(11) unsigned NOT NULL auto_increment,
	`lid` int(11) unsigned NOT NULL default '0',
	`ratinguser` int(11) NOT NULL default '0',
	`rating` tinyint(3) unsigned NOT NULL default '0',
	`ratinghostname` varchar(60) NOT NULL default '',
	`ratingtimestamp` int(10) NOT NULL default '0',
	PRIMARY KEY  (`ratingid`),
	KEY ratinguser (`ratinguser`),
	KEY ratinghostname (`ratinghostname`),
	KEY lid (`lid`)
) ENGINE=MyISAM;

#
# Dumping data for table `sbvotedata`
#