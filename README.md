# Symfony Movie Management System

This project is a web application developed using the Symfony framework.

It allows administrators to manage a movie catalog through an admin dashboard with full CRUD operations.

## Features

* Movie management (Create, Read, Update, Delete)
* Admin dashboard
* User authentication (login / registration)
* Profile management
* Database integration with Doctrine ORM
* Dynamic pages using Twig templates

## Technologies

* PHP
* Symfony
* MySQL
* Doctrine ORM
* Twig
* Composer
* HTML / CSS

## Project Structure

src/ → Controllers and application logic
templates/ → Twig templates (views)
config/ → Symfony configuration
migrations/ → Database migration files
public/ → Public entry point

## Setup

1. Install dependencies with Composer
2. Configure the database in `.env`
3. Run database migrations
4. Start the Symfony server

Example commands:

composer install
php bin/console doctrine:migrations:migrate
symfony server:start

Open the project in your browser:

http://localhost:8000

## Author

Computer Science Engineering Student
