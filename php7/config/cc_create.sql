
CREATE DATABASE IF NOT EXISTS witchercc;

USE witchercc;

CREATE TABLE `page` (
  `pagename` varchar(512),
  `execs` integer DEFAULT 1,
  `create_ts` timestamp DEFAULT CURRENT_TIMESTAMP(),
  `last_exec_ts` timestamp DEFAULT CURRENT_TIMESTAMP(),
  PRIMARY KEY (`pagename`)
);

CREATE TABLE `script` (
  `scriptname` varchar(512),
  `last_updated` timestamp,
  PRIMARY KEY  (`scriptname`)
);

CREATE TABLE `pagescript` (
  `FK_pagename` varchar(512),
  `FK_scriptname` varchar(512),
  FOREIGN KEY (`FK_pagename`) REFERENCES `page`(`pagename`),
  FOREIGN KEY (`FK_scriptname`) REFERENCES `script`(`scriptname`),
  PRIMARY KEY (`FK_pagename`, `FK_scriptname`)
);

CREATE TABLE `codecov` (
  `FK_scriptname` varchar(512),
  `lineno` integer,
  `ccval` integer,
  FOREIGN KEY (`FK_scriptname`) REFERENCES `script`(`scriptname`),
  PRIMARY KEY (`FK_scriptname`, `lineno`)
);

CREATE USER IF NOT EXISTS 'witcher'@'localhost' IDENTIFIED BY 'witcherpw';
GRANT ALL PRIVILEGES ON *.* TO 'witcher'@'localhost';
