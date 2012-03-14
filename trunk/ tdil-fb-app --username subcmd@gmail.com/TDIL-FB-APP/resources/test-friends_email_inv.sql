--Este query se usa para generar invitaciones por email

SELECT CONCAT('https://www.facebook.com/tdil.test.page?sk=app_292861170783253&app_data=join_group|', e.groupowner_id, '|', e.groupmember_id, '|')
FROM USER_APP1 u, EMAIL_INV_APP1 e
WHERE u.ORIGIN = 2
AND u.PARTICIPATION = 0
AND u.id = e.groupmember_id;