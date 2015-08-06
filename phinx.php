<?php
$dbconfig = include __DIR__ . "/config/db.include.php";
$realConfig = array(
    "adapter" => "pgsql",
    "host" => $dbconfig["host"],
    "name" => $dbconfig["dbname"],
    "user" => $dbconfig["user"],
    "pass" => $dbconfig["password"]
);
return array(
    "paths" => array(
        "migrations" => "app/migrations"
    ),
    "environments" => array(
        "default_migration_table" => "phinxlog",
        "default_database" => "prod",
        "prod" => array(
            "adapter" => "pgsql",
            "host" => "localhost",
            "name" => "Wikidata",
            "user" => "postgres",
            "pass" => "postgres",
        )
    )
);
