<?php
$config = array();

require "./app/config/database.php";

$adaper = $config['database']['default'];
$connection = $config['database']['connections'][$adaper];

return array(
    "paths" => array(
        "migrations" => "app/migrations"
    ),
    "environments" => array(
        "default_migration_table" => "migration",
        "default_database" => "dev",
        "dev" => array(
            "adapter" => $adaper,
            "host" => $connection['host'],
            "name" => $connection['database'],
            "user" => $connection['username'],
            "pass" => $connection['password'],
            "port" => $connection['port']
        )
    )
);