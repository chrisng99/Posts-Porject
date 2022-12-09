<p align="center"><a href="https://laravel.com" target="_blank"><img src="https://raw.githubusercontent.com/laravel/art/master/logo-lockup/5%20SVG/2%20CMYK/1%20Full%20Color/laravel-logolockup-cmyk-red.svg" width="400" alt="Laravel Logo"></a></p>

## Description

This is a simple project aimed at bettering my skills in the Laravel framework. This web application allows users to create, edit, and delete their posts while being able to see all posts created by other users. More functions to follow.

## How to install the system

- Clone github repository
- Open the project folder in CLI and run 'composer install'
- Create a .env file following the example provided in the .env.example file
- Edit .env variables as necessary (timezone, database details)
- Run 'php artisan key:generate' in the CLI
- Create a database in MySQL server following the defined DB_DATABASE variable in .env (default is 'MyFirstProjectLaravel')
- Create another database for features testing purposes named 'MyFirstProjectLaravel.testing' (editable in the phpunit.xml file)
- Run 'php artisan migrate --seed' in the CLI


The default login credentials for an admin user is:
Email: admin@example.com
Password: admin

## TODO list

- MAKE POSTS PAGE SPLIT USING LIVEWIRE -> ALL POSTS (NO CRUD BUTTONS) | USER'S POSTS (CRUD BUTTONS)

- ADD COMMENTS & RATING FUNCTIONS