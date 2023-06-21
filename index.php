<?php
require 'vendor/autoload.php';
require 'config.php';

use \Aternos\Model\Driver\Mysqli\Mysqli;
use \Aternos\Model\Driver\DriverRegistry;

ini_set('display_errors', 1);

#Establish database connection
$driver = (new Mysqli(DB_HOST, DB_PORT, DB_USER, DB_PASSWORD, null, DB_NAME))->setId("mysqli");
DriverRegistry::getInstance()->registerDriver($driver);

$conn = mysqli_connect(DB_HOST, DB_USER, DB_PASSWORD, DB_NAME, DB_PORT);
if (!$conn) {
    die('Could not connect to the database');
}
#execute migrations for missing tables
$database_tables = [
    "printers",
    "part_types",
    "parts",
    "parts_association",
];

$table_check_query = "SELECT TABLE_NAME FROM information_schema.tables WHERE table_schema = '%s'";
$tables = $conn->query(sprintf($table_check_query, DB_NAME))->fetch_all($mode=2);
foreach ($database_tables as $table) {
    if(!in_array($table, array_column($tables, 0))) {
        $migration_file_name = "create_{$table}_table.sql";
        $migration_querry = file_get_contents("Database\\Querries\\Migrations\\" . $migration_file_name);
        $conn->multi_query($migration_querry);

        $err = mysqli_error($conn);
        if ($err != null) {
            echo $err . "<br>";
        }
    }
}

include("routes.php");
?>