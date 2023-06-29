<?php

use \Aternos\Model\Driver\Mysqli\Mysqli;

require 'config.php';

$conn = mysqli_connect(DB_HOST, DB_USER, DB_PASSWORD, DB_NAME, DB_PORT);
if (!$conn) {
    die('Could not connect to the database');
}

$database_tables = [
    "printer_models",
    "part_types",
    "parts",
    "parts_association",
    "part_use_log",
    "printers"
];
# If you wat to add the check for missing tables
#$table_check_query = "SELECT TABLE_NAME FROM information_schema.tables WHERE table_schema = '%s'";
#$tables = $conn->query(sprintf($table_check_query, DB_NAME))->fetch_all($mode = 2);

foreach ($database_tables as $table) {
    //if (!in_array($table, array_column($tables, 0))) {
    $migration_file_name = "create_{$table}_table.sql";
    $migration_query = file_get_contents("Database\\Querries\\Migrations\\" . $migration_file_name);

    $conn->multi_query($migration_query);

    $err = mysqli_error($conn);
    if ($err != null) {
        echo $err . "<br>";
    }

    mysqli_next_result($conn);
    //}
}

// Create Trigger
$conn->multi_query(file_get_contents("Database\\Querries\\Migrations\\Triggers\\create_printer_insert_trigger.sql"));

$err = mysqli_error($conn);
if ($err != null) {
    echo $err . "<br>";
}

$conn->close();

?>