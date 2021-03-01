# COVID RELAI API
This project was generated with [Laravel 8](https://laravel.com)

## Libraries used
- Laravel Passport: Used for user authentication [read more here](https://laravel.com/docs/8.x/passport)

## Getting Started
1- Clone or download this project from the repository.

2- After cloning you need to install project dependencies depending on your packages manager by running `composer install`.

3- Copy and rename .env.example file  to .env by runnings (for linux users) `cp .env.example .env`. 

4- Create a new database to use 
5- Fill .env file with database credentials 

    -- DB_DATABASE=your database name
    -- DB_USERNAME=your username
    -- DB_PASSWORD=your password
6- Run `php artisan passport:install`
7- Run migrations with `php artisan migrate`. Then run seeders to generate user with `php artisan db:seed`


## Development server

Run `php artisan serve` for a dev server. Navigate to `http://localhost:8000/`.

[Read the API Documentation here](https://app.swaggerhub.com/apis/bamanan/covidrelaiapi/1.0.0)


## Demo
This project is the back end of another application.
You will find a Demo or the working project [here](https://inforelay.firebaseapp.com/)

## Other details
Don't hesitate to put any comment here 😉.
)

## Team
[Abdel Aziz Mashkour](https://github.com/azizmashkour)
[Abdoul Hamid COULIBALY](https://github.com/bamanan)
[Oriane ZOHOU](https://github.com/OrianeZo)
