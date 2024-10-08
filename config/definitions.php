<?php

use App\Database;
use Dotenv\Dotenv;

$dotenv = Dotenv::createImmutable(__DIR__ . '/../');
$dotenv->load();

return [
    Database::class => function () {
        return new Database(
            host: $_ENV['DB_HOST'],
            dbname: $_ENV['DB_NAME'],
            user: $_ENV['DB_USER'],
            password: $_ENV['DB_PASSWORD']
        );
    }
];