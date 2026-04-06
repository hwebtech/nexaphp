<?php

use core\Application;

class m0001_initial
{
    public function up()
    {
        $db = Application::$app->db;

        // Users
        $db->pdo->exec("CREATE TABLE IF NOT EXISTS users (
            id {$db->pk()},
            uuid VARCHAR(36) NOT NULL UNIQUE,
            email VARCHAR(255) NOT NULL UNIQUE,
            username VARCHAR(255) NOT NULL UNIQUE,
            password VARCHAR(255) NOT NULL,
            first_name VARCHAR(255),
            last_name VARCHAR(255),
            status INTEGER DEFAULT 1,
            created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
        )");
    }

    public function down()
    {
        $db = Application::$app->db;
        $db->pdo->exec("DROP TABLE users");
    }
}
