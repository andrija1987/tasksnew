# tasks
Tasks project manager (php,mysql,jquery...)
DEMO: http://andrija1987.eu/tasksdemo
NOT READY FOR PRODUCTION BECAUSE security vulnerabilities and exposures. !!

Tasks is based on PHP,mysql,jquery programming languages. It's main goal is to help you insert/list your tasks/milestones/projects.
This is a very basic version, 1.0. Newer versions will have a DASHBOARD with statistics :) (CRUD data)


.) Requirements

before you start git cloning/copying tasks, please make sure you meet following requirements:

Apache2/Nginx webserver with php support
Mysql server (5.1+)
php version 5.3 or later with following php modules enabled:
mysqli : Adds support for the improved mySQL libraries
session : Adds persistent session support
php PEAR support
Usually most php modules all are built into default php installation. If some required modules are missing tasks will fail with warning and notify you about them.

You can check which php modules are enabled by issuing php -m in command line.

.) How to install

Just clone the git or copy folder in htdocs, and set the DB,port,username,password(commented line) parameters in phpgen_settings.php file.
Insert tasks.sql into your MySql Database( heidiSQL(opensource),phpmyadmin,MySql Workbench etc...) or just a terminal(bash,zsh...)

mysql -u root -p
create database [] ;
mysql -u root -p[root_password] [database_name] < tasks.sql

Don't forget about settings permissions (chmod) and vhosts in apache2.conf/ (nginx/conf)

How to use:

Very simple, just insert the data :)

DEMO: http://andrija1987.eu/tasksdemo

License:

Permissions is licensed under the MIT license.

Thank you for using my APP.  Should you encouter any problems, please submit them to my mail(andrija.barbarosa@gmail.com) and they shall be dealt with promptly.

