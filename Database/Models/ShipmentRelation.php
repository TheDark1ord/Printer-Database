<?php
    namespace Database\Models;

    use Aternos\Model\Driver\Mysqli\Mysqli;

    class ShipmentsRelation extends \Aternos\Model\GenericModel {
        protected static bool $registry = true;
        protected static array $drivers = [
            Mysqli::ID,
        ];

        public static function getName(): string {
            return "shipments_relation";
        }

        public $ID;
        public $ShipmentID;
        public $PartID;
        public $Count;
    }
?>