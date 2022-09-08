# Necessary preparation
Create your .env file with all the DB connection data.  

Create a DB with matching name on desired Postgres instance.  

Migrate. ```php artisan migrate:fresh --seed``` Seeding is optional, but this requires manually registering. Make sure the migraton is done in an isolated DB for the purpose of testing the app, it will drop all existing tables.  

Make sure the following folder exists: /storage/app/public/images/upload  

Create a storage symlink:  ```php artisan storage:link```  
