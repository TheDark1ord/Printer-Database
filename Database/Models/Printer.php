<?php
    namespace Database\Models;

    use Aternos\Model\Driver\Mysqli\Mysqli;

    class Printer extends \Aternos\Model\GenericModel {
        protected static bool $registry = true;
        protected static array $drivers = [
            Mysqli::ID,
        ];

        public static function getName(): string {
            return "printers";
        }

        public function getId(): mixed {
            return 1;
        }

        public $SerialNumber;
        public $Model;
        public $Description;
    }
?>