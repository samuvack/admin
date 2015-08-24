<?php
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
