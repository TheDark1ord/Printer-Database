<?php
    namespace Database\Models;

    use Aternos\Model\Driver\Mysqli\Mysqli;

    class OriginalPart extends \Aternos\Model\GenericModel {
        protected static bool $registry = true;
        protected static array $drivers = [
            Mysqli::ID,
        ];

        public static function getName(): string {
            return "original_parts";
        }
        public $PartName;
        public $PrinterID;
    }
?>