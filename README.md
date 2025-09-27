# Simple Blog Application

A full-stack blog application built with **Laravel** (backend) and **Vue.js** (frontend), containerized with **Docker**. Users can register, login, create/edit/delete posts, and comment on posts.

---

## Features

- User registration and authentication
- CRUD for posts (create, read, update, delete)
- Comments on posts (add, delete)
- Role-based authorization for post and comment actions
- Full frontend integration using Vue.js
- Dockerized development environment
- Seeder with sample data

---

## Requirements

- [Docker](https://www.docker.com/get-started)
- [Docker Compose](https://docs.docker.com/compose/)

> No need to install PHP, Node, or MySQL locally — Docker handles it.

---

## Getting Started

### 1. Clone the repository

```bash
git clone https://github.com/Busuyem/blogapp
cd blogapp


2. Copy .env and configure

cp .env.example .env


Update .env if needed (database credentials, ports, etc.)

Default Docker setup:

DB_HOST=mysql_db
DB_PORT=3306
DB_DATABASE=blog
DB_USERNAME=root
DB_PASSWORD=root

3. Build and run Docker containers

docker-compose up -d --build

Containers started:

laravel_app: PHP backend

laravel_web: Nginx web server

mysql_db: MySQL database

4. Install dependencies inside container

docker exec -it laravel_app bash
composer install
npm install
npm run build
exit


5. Migrate and seed the database

docker exec -it laravel_app bash
php artisan migrate --seed
exit

