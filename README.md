<p align="center"><a href="https://laravel.com" target="_blank"><img src="https://raw.githubusercontent.com/laravel/art/master/logo-lockup/5%20SVG/2%20CMYK/1%20Full%20Color/laravel-logolockup-cmyk-red.svg" width="400" alt="Laravel Logo"></a></p>

## Description

This is a simple project aimed at bettering my skills in the Laravel framework. This web application allows users to create, edit, and delete their posts while being able to see all posts created by other users. More functions to follow.

## How to install the system

-   Clone github repository
-   Run 'composer install'
-   Create a .env file following the example provided in the .env.example file
-   Edit .env variables as necessary (timezone, database details)
-   Run 'php artisan key:generate'
-   Create a database in your server choice following the defined database variables in .env
-   Create another database for features testing purposes named 'MyFirstProjectLaravel.testing' (editable in the phpunit.xml file)
-   Run 'php artisan migrate --seed'

The default login credentials for an admin user is:

Email: admin@admin.com

Password: admin

## TODO list

-   UPDATE CATEGORIES PAGE TO MATCH STYLING OF POSTS PAGE -> IMPLEMENT LIVEWIRE
-   SHOW LIST OF PEOPLE WHO LIKED POSTS(?) -> USE CHILD MODALS
-   ALLOW USERS TO TAG MULTIPLE CATEGORIES ONTO ONE POST -> SEARCHABLE WITH CHECKBOXES(?)

-   MAKE POSTS PAGE ACCESSIBLE TO GUEST USERS BUT REDUCED FUNCTIONALITIES -> NO LIKE, CRUD POSTS
-   ADD COMMENTS
