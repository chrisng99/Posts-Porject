<p align="center"><a href="https://laravel.com" target="_blank"><img src="https://raw.githubusercontent.com/laravel/art/master/logo-lockup/5%20SVG/2%20CMYK/1%20Full%20Color/laravel-logolockup-cmyk-red.svg" width="400" alt="Laravel Logo"></a></p>

## Description

This is a simple project aimed at bettering my skills in the Laravel framework. This web application allows users to create, edit, and delete their posts while being able to see all posts created by other users. More functions to follow.

## How to install the system

-   Clone github repository
-   Run 'composer install'
-   Run 'npm install'
-   Run 'npm run build'
-   Run 'cp .env.example .env' or 'copy .env.example .env' to create a .env file based on the example file
-   Edit .env variables as necessary (app_url, timezone, database details)
-   Run 'php artisan key:generate'
-   Create a database in your server choice following the defined database variables in .env
-   If you wish to run automated tests with 'php artisan test', create another database following the defined database variables in phpunit.xml
-   Run 'php artisan migrate' or 'php artisan migrate --seed' if you wish to automatically seed 10 fake users and an admin user with the credentials listed below
-   Run 'php artisan db:seed --class=CategorySeeder' if you wish to seed 5 fake categories
-   Run 'php artisan db:seed --class=PostSeeder' if you wish to seed 200 fake posts (Ensure at least one user and one category has been created before seeding posts)
-   Run 'php artisan serve' to host the application

The default login credentials for an admin user is:

Email: admin@admin.com

Password: admin
