<?php

define("DB_HOST", "maria_db");
define("DB_USERNAME", "yaya");
define("DB_PASSWORD", "test");
define("DB_DATABASE_NAME", "internetyaya");

function connectDB() {
    try {
        // Connect to the database.
        return new PDO("mysql:host=" . DB_HOST . ";dbname=" . DB_DATABASE_NAME, DB_USERNAME, DB_PASSWORD);
    } catch(PDOException $e) {
        // Send an error because it could not connect to the database.
        throw new Exception($e->getMessage());
    }
}
