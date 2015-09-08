<?php
$config = include __DIR__ . "/app/config/db.include.php";
return array(
    "paths" => array(
        "migrations" => "app/migrations"
    ),
    "environments" => array(
        "default_migration_table" => "phinxlog",
        "default_database" => "prod",
        "prod" => array(
            "adapter" => "pgsql",
            "host" => $config['host'],
            "name" => $config['dbname'],
            "user" => $config['user'],
            "pass" => $config['password'],
        )
    )
);
