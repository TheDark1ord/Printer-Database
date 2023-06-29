<?php
    namespace Database\Models;
    use Aternos\Model\Driver\Mysqli\Mysqli;

    class PartUse extends \Aternos\Model\GenericModel {
        protected static bool $registry = true;
        protected static array $drivers = [
            Mysqli::ID,
        ];

        public static function getName(): string {
            return "part_use_log";
        }

        public $ID;
        public $PartName;
        public $PrinterNumber;
        public $UseTime;
    }
?>