-- Este query se usa para agregarle amigos a un grupo
-- Modificar el inv_email

INSERT INTO USER_APP1(inv_email,fbid,fbname, fbusername, origin,participation)
SELECT CONCAT('email', MAX(id) + 1), MAX(id) + 1, CONCAT('name',MAX(id) + 1),CONCAT('username',MAX(id) + 1),2,1
FROM USER_APP1
WHERE id = (SELECT max(id) from USER_APP1); 
INSERT INTO GROUP_APP1(groupowner_fbid,groupmember_fbid)
SELECT fbid, (SELECT MAX(fbid) from USER_APP1)
FROM USER_APP1
WHERE inv_email = 'm'; 