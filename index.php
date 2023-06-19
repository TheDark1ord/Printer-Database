<?php
require 'vendor/autoload.php';
require 'config.php';

use \Aternos\Model\Driver\Mysqli\Mysqli;
use \Aternos\Model\Driver\DriverRegistry;

#Establish database connection
$driver = (new Mysqli(DB_HOST, DB_PORT, DB_USER, DB_PASSWORD, null, DB_NAME))->setId("mysqli");
DriverRegistry::getInstance()->registerDriver($driver);

if (!$conn) {
    echo 'Could not connect to the database';
}

# Creates required tables if they do not exist
$database_definition_commands = file_get_contents("./Database/Querries/database-definition.sql");
$conn->multi_query($database_definition_commands);

include("routes.php");
?>