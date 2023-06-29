<?php
    namespace Database\Models;
    use Aternos\Model\Driver\Mysqli\Mysqli;

    class Part extends \Aternos\Model\GenericModel {
        protected static bool $registry = true;
        protected static array $drivers = [
            Mysqli::ID,
        ];

        public static function getName(): string {
            return "parts";
        }

        public $ID;
        public $PartName;
        public $ShipmentDate;
        public $PartType;
        public $Count;
    }
?>