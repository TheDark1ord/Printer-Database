<?php
    namespace Database\Models;

    use Aternos\Model\Driver\Mysqli\Mysqli;

    class PartType extends \Aternos\Model\GenericModel {
        protected static bool $registry = true;
        protected static array $drivers = [
            Mysqli::ID,
        ];

        public static function getName(): string {
            return "part_types";
        }

        public $ID;
        public $PartType;
    }
?>