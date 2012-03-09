DROP TABLE IF EXISTS BOUSER;
DROP TABLE IF EXISTS USER_APP1;
DROP TABLE IF EXISTS GROUP_APP1;
DROP TABLE IF EXISTS ACTION_APP1;
DROP TABLE IF EXISTS WINNER_APP1;
DROP TABLE IF EXISTS EMAIL_INV_APP1;
DROP TABLE IF EXISTS FB_INV_APP1;

CREATE  TABLE `BOUSER` (
  `id` INT NOT NULL AUTO_INCREMENT ,
  `nombre` VARCHAR(255) NULL ,
  `apellido` VARCHAR(255) NULL ,
  `email` VARCHAR(150) NULL ,
  `usuario` VARCHAR(100) NULL ,
  `password` VARCHAR(4000) NULL ,
  PRIMARY KEY (`id`) ,
  INDEX `BOUNAME` (`usuario` ASC) );

INSERT INTO BOUSER(nombre, apellido, email, usuario, password)
VALUES ('fb', 'fb', 'fb@ssdd.com', 'fb', 'fb'); 

INSERT INTO BOUSER(nombre, apellido, email, usuario, password)
VALUES ('pablo', 'mendoza', 'aas@ssdd.com', 'mendoza', 'mendoza'); 


CREATE TABLE `USER_APP1` (
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

INSERT INTO USER_APP1(inv_email,origin,participation) VALUES ('a',1,0);
INSERT INTO USER_APP1(inv_email,origin,participation) VALUES ('b',1,0);  
INSERT INTO USER_APP1(inv_email,origin,participation) VALUES ('c',1,0);
  
CREATE TABLE `GROUP_APP1` (
  `id` INT NOT NULL AUTO_INCREMENT ,
  `groupowner_fbid` VARCHAR(255) NULL ,
  `groupmember_fbid` VARCHAR(255) NULL ,
  PRIMARY KEY (`id`) ,
  INDEX `groupowner_fbidasc` (`groupowner_fbid` ASC) );
  
CREATE TABLE `WINNER_APP1` (
  `id` INT NOT NULL AUTO_INCREMENT ,
  `active` INT NULL,
  `groupowner_fbid` VARCHAR(255) NULL,
  PRIMARY KEY (`id`));

INSERT INTO WINNER_APP1 (active) values (1);
  
CREATE TABLE `ACTION_APP1` (
  `id` INT NOT NULL AUTO_INCREMENT ,
  `fbid` VARCHAR(255) NULL ,
  `userid` INT NOT NULL ,
  `action` VARCHAR(255) NULL ,
  `dataid` INT NOT NULL ,
  `data` VARCHAR(255) NULL ,
  `completed` INT NULL ,
  PRIMARY KEY (`id`) ,
  INDEX `fbidasc` (`fbid` ASC) );
  
CREATE TABLE `EMAIL_INV_APP1` (
  `id` INT NOT NULL AUTO_INCREMENT ,
  `groupid` VARCHAR(255) NULL ,
  `friend_email` VARCHAR(255) NULL ,
  `followed` INT NULL ,
  `completed` INT NULL ,
  PRIMARY KEY (`id`) ,
  INDEX `groupowner_fbiddasc` (`groupowner_fbid` ASC) );
  
CREATE TABLE `FB_INV_APP1` (
  `id` INT NOT NULL AUTO_INCREMENT ,
  `groupowner_fbid` VARCHAR(255) NULL ,
  `groupmember_fbid` VARCHAR(255) NULL ,
  `followed` INT NULL ,
  `completed` INT NULL ,
  PRIMARY KEY (`id`) ,
  INDEX `groupowner_fbiddasc` (`groupowner_fbid` ASC) );
