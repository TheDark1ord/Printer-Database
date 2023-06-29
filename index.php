<?php
require 'vendor/autoload.php';
require 'config.php';

use \Aternos\Model\Driver\Mysqli\Mysqli;
use \Aternos\Model\Driver\DriverRegistry;
use Laminas\Diactoros\Response\JsonResponse;

#Establish database connection
$driver = (new Mysqli(DB_HOST, DB_PORT, DB_USER, DB_PASSWORD, null, DB_NAME))->setId("mysqli");
DriverRegistry::getInstance()->registerDriver($driver);

$conn = mysqli_connect(DB_HOST, DB_USER, DB_PASSWORD, DB_NAME, DB_PORT);
if (!$conn) {
    die('Could not connect to the database');
}

include("routes.php");

$conn->close();
?>