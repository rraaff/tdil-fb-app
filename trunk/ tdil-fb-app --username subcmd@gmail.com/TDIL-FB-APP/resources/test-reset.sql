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
  
CREATE TABLE `GROUP_APP1` (
  `id` INT NOT NULL AUTO_INCREMENT ,
  `groupowner_fbid` VARCHAR(255) NULL ,
  `groupmember_fbid` VARCHAR(255) NULL ,
  `creation_date` DATETIME NOT NULL,
  PRIMARY KEY (`id`) ,
  INDEX `groupowner_fbidasc` (`groupowner_fbid` ASC) );
  
CREATE TABLE `WINNER_APP1` (
  `id` INT NOT NULL AUTO_INCREMENT ,
  `active` INT NULL,
  `groupowner_fbid` VARCHAR(255) NULL,
  `win_date` DATETIME NOT NULL,
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
  `groupowner_id` VARCHAR(255) NULL ,
  `groupmember_id` VARCHAR(255) NULL ,
  `followed` INT NULL ,
  `completed` INT NULL ,
  `creation_date` DATETIME NOT NULL,
  PRIMARY KEY (`id`) ,
  INDEX `groupowner_idasc` (`groupowner_id` ASC) );
  
CREATE TABLE `FB_INV_APP1` (
  `id` INT NOT NULL AUTO_INCREMENT ,
  `groupowner_fbid` VARCHAR(255) NULL ,
  `groupmember_fbid` VARCHAR(255) NULL ,
  `request_id` VARCHAR(255) NULL ,
  `followed` INT NULL ,
  `completed` INT NULL ,
  `creation_date` DATETIME NOT NULL,
  PRIMARY KEY (`id`) ,
  INDEX `groupowner_fbiddasc` (`groupowner_fbid` ASC) );

  
INSERT INTO BOUSER(nombre, apellido, email, usuario, password)
VALUES ('fb', 'fb', 'fb@ssdd.com', 'fb', 'fb'); 

INSERT INTO BOUSER(nombre, apellido, email, usuario, password)
VALUES ('pablo', 'mendoza', 'aas@ssdd.com', 'pm', 'pm'); 

INSERT INTO USER_APP1(fbid,inv_email,origin,participation) VALUES (100003557187940,'a',1,0);
INSERT INTO USER_APP1(inv_email,origin,participation) VALUES ('b',1,0);  
INSERT INTO USER_APP1(inv_email,origin,participation) VALUES ('c',1,0);
INSERT INTO USER_APP1(inv_email,origin,participation) VALUES ('d',1,0);
INSERT INTO USER_APP1(inv_email,origin,participation) VALUES ('e',1,0);  
INSERT INTO USER_APP1(inv_email,origin,participation) VALUES ('f',1,0);
INSERT INTO USER_APP1(inv_email,origin,participation) VALUES ('g',1,0);
INSERT INTO USER_APP1(inv_email,origin,participation) VALUES ('h',1,0);  
INSERT INTO USER_APP1(inv_email,origin,participation) VALUES ('i',1,0);
INSERT INTO USER_APP1(inv_email,origin,participation) VALUES ('j',1,0);
INSERT INTO USER_APP1(inv_email,origin,participation) VALUES ('k',1,0);  
INSERT INTO USER_APP1(inv_email,origin,participation) VALUES ('h',1,0);
INSERT INTO USER_APP1(inv_email,origin,participation) VALUES ('i',1,0);
INSERT INTO USER_APP1(inv_email,origin,participation) VALUES ('l',1,0);  
INSERT INTO USER_APP1(inv_email,origin,participation) VALUES ('m',1,0);

INSERT INTO GROUP_APP1(groupowner_fbid,groupmember_fbid) VALUES(100003557187940,0);

INSERT INTO USER_APP1(inv_email,fbid,fbname, fbusername, origin,participation)
SELECT CONCAT('email', MAX(id) + 1), MAX(id) + 1, CONCAT('name',MAX(id) + 1),CONCAT('username',MAX(id) + 1),2,1
FROM USER_APP1
WHERE id = (SELECT max(id) from USER_APP1); 
INSERT INTO GROUP_APP1(groupowner_fbid,groupmember_fbid)
SELECT 100003557187940, (SELECT MAX(fbid) from USER_APP1)
FROM USER_APP1
WHERE inv_email = 'a'; 

INSERT INTO USER_APP1(inv_email,fbid,fbname, fbusername, origin,participation)
SELECT CONCAT('email', MAX(id) + 1), MAX(id) + 1, CONCAT('name',MAX(id) + 1),CONCAT('username',MAX(id) + 1),2,1
FROM USER_APP1
WHERE id = (SELECT max(id) from USER_APP1); 
INSERT INTO GROUP_APP1(groupowner_fbid,groupmember_fbid)
SELECT 100003557187940, (SELECT MAX(fbid) from USER_APP1)
FROM USER_APP1
WHERE inv_email = 'a'; 

INSERT INTO USER_APP1(inv_email,fbid,fbname, fbusername, origin,participation)
SELECT CONCAT('email', MAX(id) + 1), MAX(id) + 1, CONCAT('name',MAX(id) + 1),CONCAT('username',MAX(id) + 1),2,1
FROM USER_APP1
WHERE id = (SELECT max(id) from USER_APP1); 
INSERT INTO GROUP_APP1(groupowner_fbid,groupmember_fbid)
SELECT 100003557187940, (SELECT MAX(fbid) from USER_APP1)
FROM USER_APP1
WHERE inv_email = 'a'; 

INSERT INTO USER_APP1(inv_email,fbid,fbname, fbusername, origin,participation)
SELECT CONCAT('email', MAX(id) + 1), MAX(id) + 1, CONCAT('name',MAX(id) + 1),CONCAT('username',MAX(id) + 1),2,1
FROM USER_APP1
WHERE id = (SELECT max(id) from USER_APP1); 
INSERT INTO GROUP_APP1(groupowner_fbid,groupmember_fbid)
SELECT 100003557187940, (SELECT MAX(fbid) from USER_APP1)
FROM USER_APP1
WHERE inv_email = 'a'; 

INSERT INTO USER_APP1(inv_email,fbid,fbname, fbusername, origin,participation)
SELECT CONCAT('email', MAX(id) + 1), MAX(id) + 1, CONCAT('name',MAX(id) + 1),CONCAT('username',MAX(id) + 1),2,1
FROM USER_APP1
WHERE id = (SELECT max(id) from USER_APP1); 
INSERT INTO GROUP_APP1(groupowner_fbid,groupmember_fbid)
SELECT 100003557187940, (SELECT MAX(fbid) from USER_APP1)
FROM USER_APP1
WHERE inv_email = 'a'; 

INSERT INTO USER_APP1(inv_email,fbid,fbname, fbusername, origin,participation)
SELECT CONCAT('email', MAX(id) + 1), MAX(id) + 1, CONCAT('name',MAX(id) + 1),CONCAT('username',MAX(id) + 1),2,1
FROM USER_APP1
WHERE id = (SELECT max(id) from USER_APP1); 
INSERT INTO GROUP_APP1(groupowner_fbid,groupmember_fbid)
SELECT 100003557187940, (SELECT MAX(fbid) from USER_APP1)
FROM USER_APP1
WHERE inv_email = 'a'; 

INSERT INTO USER_APP1(inv_email,fbid,fbname, fbusername, origin,participation)
SELECT CONCAT('email', MAX(id) + 1), MAX(id) + 1, CONCAT('name',MAX(id) + 1),CONCAT('username',MAX(id) + 1),2,1
FROM USER_APP1
WHERE id = (SELECT max(id) from USER_APP1); 
INSERT INTO GROUP_APP1(groupowner_fbid,groupmember_fbid)
SELECT 100003557187940, (SELECT MAX(fbid) from USER_APP1)
FROM USER_APP1
WHERE inv_email = 'a'; 

INSERT INTO USER_APP1(inv_email,fbid,fbname, fbusername, origin,participation)
SELECT CONCAT('email', MAX(id) + 1), MAX(id) + 1, CONCAT('name',MAX(id) + 1),CONCAT('username',MAX(id) + 1),2,1
FROM USER_APP1
WHERE id = (SELECT max(id) from USER_APP1); 
INSERT INTO GROUP_APP1(groupowner_fbid,groupmember_fbid)
SELECT 100003557187940, (SELECT MAX(fbid) from USER_APP1)
FROM USER_APP1
WHERE inv_email = 'a'; 

INSERT INTO USER_APP1(inv_email,fbid,fbname, fbusername, origin,participation)
SELECT CONCAT('email', MAX(id) + 1), MAX(id) + 1, CONCAT('name',MAX(id) + 1),CONCAT('username',MAX(id) + 1),2,1
FROM USER_APP1
WHERE id = (SELECT max(id) from USER_APP1); 
INSERT INTO GROUP_APP1(groupowner_fbid,groupmember_fbid)
SELECT 100003557187940, (SELECT MAX(fbid) from USER_APP1)
FROM USER_APP1
WHERE inv_email = 'a'; 

INSERT INTO USER_APP1(inv_email,fbid,fbname, fbusername, origin,participation)
SELECT 'email10', MAX(id) + 1, CONCAT('name',MAX(id) + 1),CONCAT('username',MAX(id) + 1),2,0
FROM USER_APP1
WHERE id = (SELECT max(id) from USER_APP1); 

INSERT INTO EMAIL_INV_APP1(groupowner_id, groupmember_id)
SELECT U1.ID, U2.ID
FROM USER_APP1 U1, USER_APP1 U2
WHERE U1.inv_email = 'a'
AND U2.inv_email = 'email10';

INSERT INTO USER_APP1(inv_email,fbid,fbname, fbusername, origin,participation)
SELECT 'email11', MAX(id) + 1, CONCAT('name',MAX(id) + 1),CONCAT('username',MAX(id) + 1),2,0
FROM USER_APP1
WHERE id = (SELECT max(id) from USER_APP1); 

INSERT INTO EMAIL_INV_APP1(groupowner_id, groupmember_id)
SELECT U1.ID, U2.ID
FROM USER_APP1 U1, USER_APP1 U2
WHERE U1.inv_email = 'b'
AND U2.inv_email = 'email11';

INSERT INTO USER_APP1(inv_email,fbid,fbname, fbusername, origin,participation)
SELECT 'email12', MAX(id) + 1, CONCAT('name',MAX(id) + 1),CONCAT('username',MAX(id) + 1),2,0
FROM USER_APP1
WHERE id = (SELECT max(id) from USER_APP1); 

INSERT INTO EMAIL_INV_APP1(groupowner_id, groupmember_id)
SELECT U1.ID, U2.ID
FROM USER_APP1 U1, USER_APP1 U2
WHERE U1.inv_email = 'b'
AND U2.inv_email = 'email12';
