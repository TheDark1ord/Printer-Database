<?php
namespace Database\Models;

use Aternos\Model\Driver\Mysqli\Mysqli;

class Shipment extends \Aternos\Model\GenericModel
{
    protected static bool $registry = true;
    protected static array $drivers = [
        Mysqli::ID,
    ];

    public static function getName(): string
    {
        return "shipments";
    }

    public $ID;
    public $ShipmentDate;
    public $Info;
}
?>