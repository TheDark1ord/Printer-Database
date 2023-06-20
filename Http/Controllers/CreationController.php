<?php

use Laminas\Diactoros\ServerRequest;
use Laminas\Diactoros\Response\JsonResponse;
use Laminas\Diactoros\Response\TextResponse;

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

    public function part(ServerRequest $request) {
        $data = $request->getParsedBody();
        if (!$this->testRequired($data, ["PartName", "ShipmentDate", "PartType", "Count"])) {
            return new TextResponse("Request missing required field(s)", 400);
            #return new JsonResponse($date, 400);
        }

        $matched_printers_querry = file_get_contents("Database\\Querries\\select_matched_printers.sql");
        $matched_printers_querry = sprintf($matched_printers_querry, $data["PartName"]);

        $part = new Part();

        $part->PartName = $data["PartName"];
        $part->ShipmentDate = $data["ShipmentDate"];
        $part->Count = $data["Count"];

        #$part_type = PartType::select(["PartType" => $data["PartType"]]);
        #$part->PartType = $part_type[0]["ID"];
        $part->PartType = 1;

        try {
            $part->save();
        } catch (Exception $e) {
            echo $e->getMessage() . "<br>";
        }
    }

    public function part_type(ServerRequest $request) {
        echo $request->getAttributes();
    }

    public function handle() {
        return "Class CreationController.";
    }
}

?>