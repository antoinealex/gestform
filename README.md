# README #

This app is in development, **DO NOT USE** !!

VERSION | DATE
--------|-----------
0.1     | 2020/04/08
**0.2** | 2020/04/17

## TO DO BEFORE CODING ##
1. Create an .env.local file in the root directory of the app (here !) and set the database access config with the following line (for a mysql db) :
```DATABASE_URL=mysql://username:password@server:3306/dbName```
2. Install dependancies using composer
```composer update```
3. Create and install the database :
	+ ```php bin/console doctrine:database:create```
	+ ```php bin/console doctrine:schema:update --force```