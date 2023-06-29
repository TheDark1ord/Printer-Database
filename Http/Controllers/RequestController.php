<?php
use Database\Models\Printer;
use Rakit\Validation\Rules\Json;
use Rakit\Validation\Validator;

header("Access-Control-Allow-Origin: *");
header("Access-Control-Allow-Headers: *");

use Laminas\Diactoros\Response\JsonResponse;
use Laminas\Diactoros\ServerRequest;
use Database\Models\Part;

require_once("Database\\Models\\PrinterModel.php");
require_once("Database\\Models\\Printer.php");
require_once("Database\\Models\\Part.php");
require_once("utils.php");

class RequestController
{
    public function part(ServerRequest $request)
    {
        $data = $request->getQueryParams();
        $validatior = new Validator;
        $validation = $validatior->make($data, [
            'PartName' => 'required',
            'ShipmentDate' => 'required|date:Y-m-d'
        ]);
        $validation->validate();
        if ($validation->fails()) {
            return new JsonResponse($validation->errors()->firstOfAll(), 500);
        }
        $result = Part::select(["PartName" => $data["PartName"], "ShipmentDate" => $data["ShipmentDate"]]);

        return new JsonResponse(getFirstMatch($result));
    }

    public function parts_for_printer(ServerRequest $request)
    {
        $data = $request->getQueryParams();
        $validatior = new Validator;
        $validation = $validatior->make($data, [
            'PrinterModel' => 'required',
            'PartType' => 'present'
        ]);
        $validation->validate();
        if ($validation->fails()) {
            return new JsonResponse($validation->errors()->firstOfAll(), 500);
        }

        $select_query_raw = file_get_contents("Database\\Querries\\parts_select_query.sql");
        global $conn;
        $select_query = $conn->prepare($select_query_raw);

        $model = $data['PrinterModel'];
        $part_type = $data['PartType'];

        $select_query->bind_param('sss', $model, $part_type, $part_type);
        $select_query->execute();

        $result = $select_query->get_result();
        $result->fetch_all($mode = 2);

        $parts = [];
        foreach ($result as $r) {
            $parts[] = $r;
        }

        return new JsonResponse($parts, 200);
    }

    public function types(ServerRequest $request)
    {
        global $conn;
        $result = $conn->query(
            "SELECT * FROM part_types ORDER BY PartType LIMIT 15"
        )->fetch_all();

        $part_types = [];
        foreach ($result as $r) {
            $part_types[] = $r;
        }

        return new JsonResponse($part_types, 200);
    }

    public function printer(ServerRequest $request)
    {
        $data = $request->getQueryParams();
        $validatior = new Validator;
        $validation = $validatior->make($data, [
            'Serial' => 'required|alpha_num',
        ]);
        $validation->validate();
        if ($validation->fails()) {
            return new JsonResponse($validation->errors()->firstOfAll(), 500);
        }
        $result = Printer::select(["SerialNumber" => $data["Serial"]]);

        $printers = [];
        foreach ($result as $r) {
            $printers[] = $r;
        }
        return $printers;
    }
}

?>