<?php

use Laminas\Diactoros\ServerRequest;
use Laminas\Diactoros\Response\JsonResponse;
use Laminas\Diactoros\Response\TextResponse;

use Aternos\Model\Query\SelectQuery;

use Database\Models\Printer;
use Database\Models\Part;
use Database\Models\PartType;

require_once("Database\\Models\\Printer.php");
require_once("Database\\Models\\Part.php");
require_once("Database\\Models\\PartType.php");

# This controller handles post requests which add new entries to the tables
class CreationController
{
    # Test if all of the required attributes provided by the $fields variable,
    # which is an array of strings, are present in the json $data variable and not null
    private function testRequired($data, $fields): bool {
        foreach($fields as $field) {
            if (!array_key_exists($field, $data) or $data[$field] == null) {
                return false;
            }
        }
        return true;
    }

    public function index(ServerRequest $request) {

    }

    # Add new printer to the database
    public function printer(ServerRequest $request) {
        $data = $request->getParsedBody();

        if (!$this->testRequired($data, ["Model"])) {
            return new TextResponse("Request missing required field(s)", 400);
        }

        if (count(Printer::select(["Model" => $data["Model"]])) > 0) {
            return new TextResponse("Данная модель уже добавлена в базу", 400);
        }

        $printer = new Printer();
        $printer->Model = $data["Model"];

        $printer->save();
    }

    #Add new part to the database
    public function part(ServerRequest $request) {
        $data = $request->getParsedBody();
        # This field is in json form but transmitted as string, thus we should decode it first
        $data["Supported"] = json_decode($data["Supported"]);

        if (!$this->testRequired($data, ["PartName", "ShipmentDate", "PartType", "Count", "Supported"])) {
            return new TextResponse("Request missing required field(s)", 400);
        }

        foreach ($data["Supported"][0] as $model => $original) {
        }

        $same_parts = Part::select(["PartName" => $data["PartName"]]);
        # If the part with the same name has the same supported printers, then we increace the count of this part
        # Else we create different part with the same name
        if (count($same_parts) > 0) {
            # Fetch the list of all printers, that this part supports
            $matched_printers_query_raw = file_get_contents("Database\\Querries\\select_matched_printers.sql");

            foreach($same_parts as $part) {
                $matched_printers_query = sprintf($matched_printers_query_raw, $data["PartName"]);

                global $conn;
                $matched_printers = $conn->query($matched_printers_query)->fetch_all($mode=2);

                $have_matched = true;
                //TODO: Добавить проверку соответствия поддерживаемых принтеров
                foreach($matched_printers as $printer) {
                    echo $printer . "<br>";
                    if ()
                    $have_matched = false;
                    break;
                }
            }
        }

        $part = new Part();

        $part->PartName = $data["PartName"];
        $part->ShipmentDate = $data["ShipmentDate"];
        $part->Count = $data["Count"];

        $part_type = PartType::select(["PartType" => $data["PartType"]], null, ["PartType", "ID"]);

        foreach ($part_type as $type) {
            $part->PartType = $type->ID;
        }

        try {
            $part->save();
        } catch (Exception $e) {
            echo $e->getMessage() . "<br>";
        }

        echo "Новая поставка детали успешно зарегестрированна";
    }

    public function part_type(ServerRequest $request) {
        echo $request->getAttributes();
    }

    public function handle() {
        return "Class CreationController.";
    }
}

?>