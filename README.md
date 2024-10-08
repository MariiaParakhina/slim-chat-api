# Slim Chat API

This is a simple chat API project built with PHP, using:
- Composer for dependency management 
- Slim Framework for the API
- Ratchet for the WebSocket server
- PHPUnit for testing


## Requirements

- PHP 7.4 or higher
- Composer
- MySQL

## Installation

1. Clone the repository:
   ```sh
   git clone https://github.com/yourusername/chat-api.git
   cd chat-api# slim-chat-api
    ```
2. Install dependencies:
    ```sh
    composer install
    ```
3. Create a .env file in the root directory and add your database configuration: 
    ```sh
    DB_HOST=localhost
    DB_DATABASE=chat
    DB_USERNAME=root
    DB_PASSWORD=
    ```
4. Create the database:
    ```sh
   mysql -u DB_USERNAME -p DB_DATABASE < database.sql
    ```
   
## Project Structure
- config/definitions.php: Configuration file for dependency injection.
- database.sql: SQL file to set up the database schema.
- src/App/Database.php: Database connection class.
- tests/Controllers/GroupsTest.php: PHPUnit tests for the Groups controller.
- public/index.html: WebSocket chat client. 

