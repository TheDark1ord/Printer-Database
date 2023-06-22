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

        # Get an array of all printers int which this part can be installed
        # pertain specifies if only original, non original or both types of
        # parts should be selected
        public function getPrinters() {
        }

        public $ID;
        public $PartName;
        public $ShipmentDate;
        public $PartType;
        public $Count;
    }
?>