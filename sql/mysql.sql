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
  `columnID`    TINYINT(4)   NOT NULL AUTO_INCREMENT,
  `author`      INT(8)       NOT NULL,
  `name`        VARCHAR(255) NOT NULL DEFAULT '',
  `description` TEXT         NOT NULL,
  `total`       INT(11)      NOT NULL DEFAULT '0',
  `weight`      INT(11)      NOT NULL DEFAULT '1',
  `colimage`    VARCHAR(255) NOT NULL DEFAULT 'blank.png',
  `created`     INT(11)      NOT NULL DEFAULT '1033141070',
  PRIMARY KEY (`columnID`),
  UNIQUE KEY columnID (`columnID`)
)
  ENGINE = MyISAM
  COMMENT = 'Soapbox by hsalazar and domifara';

#
# Dumping data for table `sbcolumns`
#

# --------------------------------------------------------

#
# Table structure for table `sbarticles`
#

CREATE TABLE `sbarticles` (
  `articleID`   INT(8)          NOT NULL AUTO_INCREMENT,
  `columnID`    TINYINT(4)      NOT NULL DEFAULT '0',
  `headline`    VARCHAR(255)    NOT NULL DEFAULT '0',
  `lead`        TEXT            NOT NULL,
  `bodytext`    TEXT            NOT NULL,
  `teaser`      TEXT            NOT NULL,
  `uid`         INT(6)                   DEFAULT '1',
  `submit`      INT(1)          NOT NULL DEFAULT '0',
  `datesub`     INT(11)         NOT NULL DEFAULT '1033141070',
  `counter`     INT(8) UNSIGNED NOT NULL DEFAULT '0',
  `weight`      INT(11)         NOT NULL DEFAULT '1',
  `html`        INT(11)         NOT NULL DEFAULT '0',
  `smiley`      INT(11)         NOT NULL DEFAULT '0',
  `xcodes`      INT(11)         NOT NULL DEFAULT '0',
  `breaks`      INT(11)         NOT NULL DEFAULT '1',
  `block`       INT(11)         NOT NULL DEFAULT '0',
  `artimage`    VARCHAR(255)    NOT NULL DEFAULT '',
  `votes`       INT(11)         NOT NULL DEFAULT '0',
  `rating`      DOUBLE(6, 4)    NOT NULL DEFAULT '0.0000',
  `commentable` INT(11)         NOT NULL DEFAULT '0',
  `offline`     INT(11)         NOT NULL DEFAULT '0',
  `notifypub`   INT(11)         NOT NULL DEFAULT '0',
  PRIMARY KEY (`articleID`),
  UNIQUE KEY articleID (`articleID`),
  FULLTEXT KEY bodytext (`bodytext`)
)
  ENGINE = MyISAM
  COMMENT = 'Soapbox by hsalazar and domifara';

#
# Dumping data for table `sbarticles`
#

# --------------------------------------------------------

#
# Table structure for table `sbvotedata`
#

CREATE TABLE `sbvotedata` (
  `ratingid`        INT(11) UNSIGNED    NOT NULL AUTO_INCREMENT,
  `lid`             INT(11) UNSIGNED    NOT NULL DEFAULT '0',
  `ratinguser`      INT(11)             NOT NULL DEFAULT '0',
  `rating`          TINYINT(3) UNSIGNED NOT NULL DEFAULT '0',
  `ratinghostname`  VARCHAR(60)         NOT NULL DEFAULT '',
  `ratingtimestamp` INT(10)             NOT NULL DEFAULT '0',
  PRIMARY KEY (`ratingid`),
  KEY ratinguser (`ratinguser`),
  KEY ratinghostname (`ratinghostname`),
  KEY lid (`lid`)
)
  ENGINE = MyISAM;

#
# Dumping data for table `sbvotedata`
#
