SELECT CONCAT('http://www.facebook.com/tdil.test.page?sk=app_292861170783253&app_data=new_group|', inv_email, '|')
FROM USER_APP1 
WHERE ORIGIN = 1
AND PARTICIPATION = 0;