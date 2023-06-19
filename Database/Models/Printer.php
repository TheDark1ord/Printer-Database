<?php
    namespace Database\Models;

    use Aternos\Model\Driver\Mysqli\Mysqli;
    use Alternos\Model\GenericModel;

    class Printer extends GenericModel {
        protected static bool $registry = true;
        protected static array $drivers = [
            Mysqli::ID,
        ];
    }
?>