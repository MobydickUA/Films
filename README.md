### Films storage

Simple web application to store info about films and actors. Written using MVC arcitecture without any framework.
Requires PHP >=5.3 and MySql server.

### How to deploy:
* clone project;
* config your server(example for nginx below);
* edit config/DB.php - set proper user and password to mysql server;
* open your browser and visit "sitename/migration" page to create database and tables;
* go to "sitename/films";

nginx config example('/etc/nginx/sites-available/sitename', '/etc/nginx/sites-enabled/sitename')
```
server {
	listen 80;
	root /path/to/index/file;
	index index.php;
	charset utf-8;
	server_name sitename;
	location / {
		try_files $uri $uri/ /index.php$is_args$args;
	}
	location ~ ^/assets/.*\.php$ {
		deny all;
	}
	location ~ \.php$ {
		include fastcgi_params;
		fastcgi_param SCRIPT_FILENAME $document_root$fastcgi_script_name;
		#fastcgi_pass 127.0.0.1:9000;
		fastcgi_pass unix:/var/run/php/php7.0-fpm.sock;
		try_files $uri =404;
	}
	location ~* /\. {
		deny all;
	}
}
```
