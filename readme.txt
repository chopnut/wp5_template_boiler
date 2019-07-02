* ----------------------------------------------*
|               Requirements                    |
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

