* ----------------------------------------------*
|               REQUIREMENTS                    |
* ----------------------------------------------*
- Install Docker 
- Install Node/npm

* ----------------------------------------------*
|  TO CREATE WP THEME FROM SCRATCH WITH DOCKER  |
* ----------------------------------------------*
1. Open the console/terminal under this project root directory and type:
   docker-compose up -d

::: Start creating your theme using the "urbosa" template.

* ----------------------------------------------*
|  MIGRATING EXISTING WP SITE WITH DOCKER       |
* ----------------------------------------------*

1. From existing website , export the database as .sql file. 
   - Save it inside /db folder and name it as "export.sql"
2. Download the wp-content of the existing WP website. 
   Move it inside the /wp folder.
3. Open the /.env file and change the WP_VERSION to match the existing website wordpress version.
4. Open the console/terminal under this project root directory and type:
   docker-compose up -d
5. Extra step: Change the site name option in phpMyAdmin pointing to http://localhost:8182

::: Accessing the website
- Wordpress site http://localhost:8182
- phpMyAdmin site http://localhost:8183

Note: You can change the port number by editing the .env file.

* ----------------------------------------------*
|  ADDING ADMIN USER TO THE SITE                |
* ----------------------------------------------*

INSERT INTO `dev_db`.`wp_users` (`ID`, `user_login`, `user_pass`, `user_nicename`, `user_email`, `user_url`, `user_registered`, `user_activation_key`, `user_status`, `display_name`) VALUES ('4', 'admin', MD5('dev_2019!'), 'admin', 'test@yourdomain.com', 'http://www.test.com/', '2011-06-07 00:00:00', '', '0', 'admin');
INSERT INTO `dev_db`.`wp_usermeta` (`umeta_id`, `user_id`, `meta_key`, `meta_value`) VALUES (NULL, '4', 'wp_capabilities', 'a:1:{s:13:"administrator";s:1:"1";}');
INSERT INTO `dev_db`.`wp_usermeta` (`umeta_id`, `user_id`, `meta_key`, `meta_value`) VALUES (NULL, '4', 'wp_user_level', '10');

* ----------------------------------------------*
|  EXPOSE LOCAL SERVER TO THE OUTSIDE           |
* ----------------------------------------------*
- Install ngrok
ngrok http -region=au -host-header=rewrite localhost:8191

* ----------------------------------------------*
|              TROUBLESHOOTING                  |
* ----------------------------------------------*
- If you get ERROR regarding node-sass/ when building
  on your local project run: npm rebuild node-sass