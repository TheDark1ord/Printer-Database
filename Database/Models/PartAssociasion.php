<?php
    namespace Database\Models;

    use Aternos\Model\Driver\Mysqli\Mysqli;

    class PartAssociasion extends \Aternos\Model\GenericModel {
        protected static bool $registry = true;
        protected static array $drivers = [
            Mysqli::ID,
        ];

        public static function getName(): string {
            return "parts_association";
        }
        public $PartID;
        public $PrinterID;
        public $PrinterModel;
        public $IsOriginal;

        public function getId(): mixed {
            return 1;
        }
    }

?>