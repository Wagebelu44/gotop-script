#### Downloading composer package and dumping
~~~bash
composer install
composer dump-autoload
~~~

#### Downloading npm packages
~~~bash
npm install
~~~

#### Building npm bundle for vue
~~~bash
npm run prod
~~~

### Copy code from `.env.example` to `.env` file

#### Configure project
~~~php
php artisan cache:clear
php artisan config:cache
php artisan key:generate
~~~

### Create a database name and change credential in `.env` file

### migrate and seed database
~~~bash
php artisan migrate --seed
~~~

### Serving laravel project
~~~
php artisan serve
~~~


### login user:
* Panel Admin Login:
~~~
Url: {project_url}/admin
Email: admin@gmail.com
Password: 12345678
~~~

* User Login:
~~~
Url: {project_url}/login
Email: user@gmail.com
Password: 12345678
~~~ 

### Check project log (Only for PanelAdmin access)
~~~
{project_url}/log-viewer
~~~

### Project helper function file location
`app/Http/Helpers.php`
   
