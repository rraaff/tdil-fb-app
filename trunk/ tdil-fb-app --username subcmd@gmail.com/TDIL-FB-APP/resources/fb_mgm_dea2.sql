DROP TABLE IF EXISTS CONFIG_APP2;
DROP TABLE IF EXISTS USER_APP2;
DROP TABLE IF EXISTS GROUP_APP2;
DROP TABLE IF EXISTS ACTION_APP2;
DROP TABLE IF EXISTS EMAIL_INV_APP2;
DROP TABLE IF EXISTS FB_INV_APP2;

CREATE TABLE `USER_APP2` (
  `id` INT NOT NULL AUTO_INCREMENT ,
  `inv_email` VARCHAR(255) NULL ,
  `fbid` VARCHAR(255) NULL ,
  `fbemail` VARCHAR(255) NULL ,
  `fbname` VARCHAR(255) NULL ,
  `fbusername` VARCHAR(250) NULL ,
  `fbgender` VARCHAR(100) NULL ,
  `origin` INT NULL ,
  `participation` INT NULL ,
  `participation_code` VARCHAR(100) NULL ,
  `invitation_code` VARCHAR(100) NULL ,
  PRIMARY KEY (`id`) ,
  INDEX `fbidasc` (`fbid` ASC) );
  
CREATE TABLE `GROUP_APP2` (
  `id` INT NOT NULL AUTO_INCREMENT ,
  `groupowner_fbid` VARCHAR(255) NULL ,
  `groupmember_fbid` VARCHAR(255) NULL ,
  `creation_date` DATETIME NOT NULL,
  PRIMARY KEY (`id`) ,
  INDEX `groupowner_fbidasc` (`groupowner_fbid` ASC) );

CREATE TABLE `ACTION_APP2` (
  `id` INT NOT NULL AUTO_INCREMENT ,
  `fbid` VARCHAR(255) NULL ,
  `userid` INT NOT NULL ,
  `action` VARCHAR(255) NULL ,
  `dataid` INT NOT NULL ,
  `data` VARCHAR(255) NULL ,
  `completed` INT NULL ,
  PRIMARY KEY (`id`) ,
  INDEX `fbidasc` (`fbid` ASC) );
  
CREATE TABLE `EMAIL_INV_APP2` (
  `id` INT NOT NULL AUTO_INCREMENT ,
  `groupowner_id` VARCHAR(255) NULL ,
  `groupmember_id` VARCHAR(255) NULL ,
  `followed` INT NULL ,
  `completed` INT NULL ,
  `creation_date` DATETIME NOT NULL,
  PRIMARY KEY (`id`) ,
  INDEX `groupowner_idasc` (`groupowner_id` ASC) );
  
CREATE TABLE `FB_INV_APP2` (
  `id` INT NOT NULL AUTO_INCREMENT ,
  `groupowner_fbid` VARCHAR(255) NULL ,
  `groupmember_fbid` VARCHAR(255) NULL ,
  `request_id` VARCHAR(255) NULL ,
  `followed` INT NULL ,
  `completed` INT NULL ,
  `creation_date` DATETIME NOT NULL,
  PRIMARY KEY (`id`) ,
  INDEX `groupowner_fbiddasc` (`groupowner_fbid` ASC) );

CREATE TABLE `CONFIG_APP2` (
  `id` INT NOT NULL AUTO_INCREMENT ,
  `invitation_days` INT NOT NULL ,
  `email_daily_quota` INT NOT NULL ,
  `fb_daily_quota` INT NOT NULL ,
  `show_winner` INT NULL ,
  `winner_pubdate` DATE NULL,
  PRIMARY KEY (`id`));
  
INSERT INTO CONFIG_APP2 (invitation_days, email_daily_quota, fb_daily_quota,show_winner) VALUES(1,1,1,0);