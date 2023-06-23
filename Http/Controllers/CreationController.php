<?php

use Laminas\Diactoros\ServerRequest;
use Laminas\Diactoros\Response\JsonResponse;
use Laminas\Diactoros\Response\TextResponse;

use Aternos\Model\Query\SelectQuery;

use Database\Models\Printer;
use Database\Models\Part;
use Database\Models\PartType;
use Database\Models\PartAssociasion;

require_once("Database\\Models\\Printer.php");
require_once("Database\\Models\\Part.php");
require_once("Database\\Models\\PartType.php");
require_once("Database\\Models\\PartAssociasion.php");
require_once("utils.php");

# This controller handles post requests which add new entries to the tables
class CreationController
{
    public function index(ServerRequest $request) {

    }

    # Add new printer to the database
    public function printer(ServerRequest $request) {
        $data = $request->getParsedBody();

        if (!testRequired($data, ["Model"])) {
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

        if (!testRequired($data, ["PartName", "ShipmentDate", "PartType", "Count", "Supported"])) {
            #return new JsonResponse($data, 400);
            return new TextResponse("Request missing required field(s)", 400);
        }

        $same_parts = Part::select(["PartName" => $data["PartName"]]);
        $supported_printers = [];
        foreach ($data["Supported"] as $supported) {
            array_push($supported_printers, $supported->Model);
        }

        # If the part with the same name has the same supported printers, then we increace the count of this part
        # Else we create different part with the same name
        $matched_part = null;
        if (count($same_parts) > 0) {
            foreach ($same_parts as $part) {
                $matched_printes = PartAssociasion::select(["PartID" => $part->ID]);

                $full_match = true;
                if (count($matched_printes) != count($supported_printers)) {
                    $full_match = false;
                    continue;
                }
                foreach ($matched_printes as $matched) {
                    if (!in_array($matched->PrinterModel, $supported_printers)) {
                        break;
                    }
                }
                if ($full_match) {
                    $matched_part = $part;
                    break;
                }
            }

            # Simply add shipment to count
            if ($matched_part != null) {
                $matched_part->Count += $data["Count"];
                $matched_part->save();
            }
        }

        if ($matched_part == null) {
            $new_part = new Part();

            $new_part->PartName = $data["PartName"];
            $new_part->ShipmentDate = $data["ShipmentDate"];
            $new_part->Count = $data["Count"];

            $part_type = PartType::select(["PartType" => $data["PartType"]], null, ["PartType", "ID"]);
            $new_part->PartType =  getFirstMatch($part_type)->ID;

            try {
                $new_part->save();
            } catch (Exception $e) {
                return new TextResponse($e->getMessage() . "in new_part", 500);
            }

            $new_part_id = getFirstMatch(Part::select(
                ["PartName" => $new_part->PartName],
                ["ID" => "DESC"],
                ["ID"],
                1
            ))->ID;

            #Create new assosiacions
            foreach ($data["Supported"] as $supported) {
                $new_association = new PartAssociasion();

                $new_association->PartID = $new_part_id;
                $new_association->PrinterModel = $supported->Model;

                $model_match = getFirstMatch(Printer::select(["Model" => $supported->Model]));
                if ($model_match != null) {
                    #Get first element
                    $new_association->PrinterID = $model_match->ID;
                }
                $new_association->IsOriginal = $supported->Original === 'true' ? 1 : 0;

                try {
                    $new_association->save();
                } catch (Exception $e) {
                    return new TextResponse($e->getMessage() . "in new_association", 500);
                }
            }
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