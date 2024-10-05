<?php

use App\Database;

return[
    Database::class => function(){
        return new Database(
            host:'127.0.0.1',
            dbname:'chat_api_db',
            user:'user',
            password:'password');
    }
];