<?php

use Database\Models\Printer;
use Database\Models\Shipment;
use Database\Models\ShipmentsRelation;
use Laminas\Diactoros\ServerRequest;
use Laminas\Diactoros\Response\JsonResponse;
use Laminas\Diactoros\Response\TextResponse;

use Aternos\Model\Query\SelectQuery;

use Database\Models\PrinterModel;
use Database\Models\Part;
use Database\Models\PartType;
use Database\Models\PartAssociasion;
use Rakit\Validation\Validator;

require_once("Database\\Models\\PrinterModel.php");
require_once("Database\\Models\\Printer.php");
require_once("Database\\Models\\Part.php");
require_once("Database\\Models\\PartType.php");
require_once("Database\\Models\\PartAssociasion.php");
require_once("Database\\Models\\Shipment.php");
require_once("Database\\Models\\ShipmentRelation.php");
require_once("utils.php");

# This controller handles post requests which add new entries to the tables
class CreationController
{
    # Add new printer to the database
    public function printer_model(ServerRequest $request)
    {
        $data = $request->getParsedBody();

        if (count(PrinterModel::select(["Model" => $data["Model"]])) > 0) {
            return new TextResponse("Данная модель уже добавлена в базу", 400);
        }
        $model = new PrinterModel();
        $model->Model = $data["Model"];

        $model->save();
    }

    public function printer(ServerRequest $request)
    {
        $data = $request->getParsedBody();
        $validatior = new Validator;
        $validation = $validatior->make($data, [
            'Serial' => 'required|alpha_num',
            'Model' => 'required'
        ]);
        $validation->validate();
        if ($validation->fails()) {
            return new TextResponse(print_r($validation->errors()->firstOfAll(), true), 500);
        }

        $printer = new Printer;
        $model = getFirstMatch(PrinterModel::select(["Model" => $data["Model"]]));

        $printer->SerialNumber = $data["Serial"];
        $printer->Model = $model->ID;
        $printer->Description = $data["Description"];

        $printer->save();
    }

    #Add new part to the database
    public function shipment(ServerRequest $request)
    {
        $data = $request->getParsedBody();
        $data["Parts"] = json_decode($data["Parts"], true);

        $validatior = new Validator;
        $validation = $validatior->make($data, [
            'ShipmentDate' => 'required|date',
            'Parts' => 'array|required',
            'Parts.*.PartName' => 'required',
            'Parts.*.Manufacturer' => 'required',
            'Parts.*.PartType' => 'required|numeric',
            'Parts.*.Count' => 'required|numeric|min:1',
            'Parts.*.Supported' => 'array|required',
            'Parts.*.Supported.*.Model' => 'required',
            'Parts.*Supported.*.Original' => 'required'
        ]);
        $validation->validate();
        if ($validation->fails()) {
            return new TextResponse(print_r($validation->errors()->firstOfAll(), true), 400);
        }

        $shipment = new Shipment;
        $shipment->ShipmentDate = $data["ShipmentDate"];
        // TODO: Может быть в будующем добавить поддержку этого поля
        $shipment->Info = null;
        $shipment->save();

        # Так как ID задается автоматически и не обновляется в модели после ее сохранения,
        # приходится вот так искать ID новой поставки в базе
        $new_shipment_id = getFirstMatch(
            Shipment::select(
                ["ShipmentDate" => $data["ShipmentDate"]],
                ["ID" => "DESC"],
                ["ID"],
                1
            )
        )->ID;

        foreach ($data["Parts"] as $part) {
            if (PartType::get($part["PartType"]) == null) {
                return new TextResponse("PartType with ID {$part['PartType']} does not exist", 400);
            }

            $same_parts = Part::select(["PartName" => $part["PartName"], "Manufacturer" => $part["Manufacturer"]]);

            # Если запчасть с этим названием уже была найдена
            if (count($same_parts) > 0) {
                $matched_part = getFirstMatch($same_parts);

                $matched_part->Count += $part["Count"];
                $matched_part->save();

                $shipment_relation_part_id = $matched_part->ID;
            } else {
                $new_part = new Part();

                $new_part->PartName = $part["PartName"];
                $new_part->Manufacturer = $part["Manufacturer"];
                $new_part->Count = $part["Count"];

                $new_part->Description = $part["Description"];

                #Если в качестве параметра передается название типа, а не ID
                #$part_type = PartType::select(["PartType" => $data["PartType"]], null, ["PartType", "ID"]);

                $new_part->PartType = $part["PartType"];
                try {
                    $new_part->save();
                } catch (Exception $e) {
                    return new TextResponse($e->getMessage() . " in new_part", 500);
                }

                $new_part_id = getFirstMatch(
                    Part::select(
                        ["PartName" => $new_part->PartName],
                        ["ID" => "DESC"],
                        ["ID"],
                        1
                    )
                )->ID;
                $shipment_relation_part_id = $new_part_id;

                #Create new assosiacions
                foreach ($part["Supported"] as $supported) {
                    $new_association = new PartAssociasion();

                    $new_association->PartID = $new_part_id;
                    $new_association->PrinterModel = $supported["Model"];

                    $model_match = getFirstMatch(PrinterModel::select(["Model" => $supported["Model"]]));
                    if ($model_match != null) {
                        #Get first element
                        $new_association->PrinterID = $model_match->ID;
                    }
                    $new_association->IsOriginal = $supported["Original"] === 'true' ? 1 : 0;

                    try {
                        $new_association->save();
                    } catch (Exception $e) {
                        return new TextResponse($e->getMessage() . "in new_association", 500);
                    }
                }
            }
            $shipment_relation = new ShipmentsRelation;
            $shipment_relation->Count = $part["Count"];
            $shipment_relation->PartID = $shipment_relation_part_id;
            $shipment_relation->ShipmentID = $new_shipment_id;
            $shipment_relation->save();
        }
    }

    public function part_type(ServerRequest $request)
    {
        // TODO
        echo $request->getAttributes();
    }

    public function handle()
    {
        return "Class CreationController.";
    }
}

?>