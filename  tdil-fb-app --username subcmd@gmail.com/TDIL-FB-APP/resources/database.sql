CREATE DATABASE FB_MGM CHARACTER SET utf8 COLLATE utf8_unicode_ci;
CREATE USER FB_MGM_USER IDENTIFIED BY 'FB_MGM_USER';
GRANT ALL ON FB_MGM.* TO FB_MGM_USER IDENTIFIED BY 'FB_MGM_USER';