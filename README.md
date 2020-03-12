# README #

This app is in development, **DO NOT USE** !!

VERSION | DATE
--------|-----------
0.0.1   | 2020/03/12

## TO DO BEFORE CODING ##
1. Create an .env.local file in the root directory of the app (here !) and set the database access config with the following line (for a mysql db) :
```DATABASE_URL=mysql://username:password@server:3306/dbName?serverVersion=mysql_version.eg:8.0```
2. Install dependancies using composer
```composer update```
3. Create and install the database :
	+ ```symfony doctrine:database:create```
	+ ```symfony doctrine:schema:update --force```