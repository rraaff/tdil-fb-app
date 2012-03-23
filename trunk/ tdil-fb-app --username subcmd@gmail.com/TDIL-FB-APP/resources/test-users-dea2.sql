DELETE FROM USER_APP2;
DELETE FROM GROUP_APP2;
;
INSERT INTO USER_APP2(fbid, fbname,fbgender,firstname,lastname,address,phone, participation) 
VALUES(1, 'MarcosG','male','Marcos','Godoy','73','6412772',1);

INSERT INTO USER_APP2(fbid, fbname,fbgender,firstname,lastname,address,phone, participation) 
VALUES(2, 'MarceC','female','Marcela','Cioma','73','6412772',1);


INSERT INTO USER_APP2(fbid, fbname,fbgender,firstname,lastname,address,phone, participation) 
VALUES(3, 'FerC','male','Fernando','Cioma','73','6412772',0);

INSERT INTO GROUP_APP2 (groupowner_fbid,groupmember_fbid)
VALUES(1,2);

INSERT INTO GROUP_APP2 (groupowner_fbid,groupmember_fbid)
VALUES(2,3);
