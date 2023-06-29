<?php

use Database\Models\Part;
use Database\Models\PartUse;
use Database\Models\Printer;
use Laminas\Diactoros\Response\JsonResponse;
use Laminas\Diactoros\Response\TextResponse;
use Laminas\Diactoros\ServerRequest;
use Rakit\Validation\Rules\Json;
use Rakit\Validation\Validator;

require_once("Database\\Models\\Part.php");
require_once("Database\\Models\\PartUse.php");
require_once("utils.php");

class EditController
{
    public function take_part(ServerRequest $request)
    {
        // VALIDATION
        $data = $request->getQueryParams();
        $validatior = new Validator;
        $validation = $validatior->make($data, [
            'ID' => 'required',
            'Serial' => 'required'
        ]);
        $validation->validate();
        if ($validation->fails()) {
            return new JsonResponse($validation->errors()->firstOfAll(), 500);
        }
        if (getFirstMatch(Printer::select(["SerialNumber" => $data["Serial"]])) == null) {
            return new TextResponse("Printer not found", 400);
        }
        $part = Part::get($data["ID"]);
        if ($part == null) {
            return new TextResponse("Part with ID {$data['ID']} does not exist", 400);
        }
        // VALIDATION

        $part->Count -= 1;

        $use_log = new PartUse;

        $use_log->PartName = $part->PartName;
        $use_log->PrinterNumber = $data["Serial"];
        $use_log->UseTime = date("Y-m-d H:i:s");
        $use_log->save();

        if ($part->Count == 0) {
            $query = "DELETE FROM `parts` WHERE `parts`.`ID` = ?";
            global $conn;
            $stmt = $conn->prepare($query);
            $stmt->bind_param("i", $data["ID"]);
            $stmt->execute();

            $err = $stmt->errors();
            if ($err != null) {
                return new JsonResponse($err, 500);
            }
            //$part->delete();
        } else {
            $part->save();
        }

        return new TextResponse("Log saved succesfully");
    }
}

?>