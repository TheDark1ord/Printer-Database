<?php

use Database\Models\Printer;
use Rakit\Validation\Rules\Json;
use Rakit\Validation\Validator;

use Laminas\Diactoros\Response\JsonResponse;
use Laminas\Diactoros\ServerRequest;
use Database\Models\Part;

require_once("Database\\Models\\PrinterModel.php");
require_once("Database\\Models\\Printer.php");
require_once("Database\\Models\\Part.php");
require_once("utils.php");

# Используется для поиска в базе данных записей по точному соответствию некоторого поля
# Например поиск принтера по его серийному номеру
class RequestController
{
    public function part(ServerRequest $request)
    {
        $data = $request->getQueryParams();
        $validatior = new Validator;
        $validation = $validatior->make($data, [
            'PartName' => 'required',
            'Manufacturer' => 'required'
        ]);
        $validation->validate();
        if ($validation->fails()) {
            return new JsonResponse($validation->errors()->firstOfAll(), 500);
        }
        $result = Part::select(["PartName" => $data["PartName"], "Manufacturer" => $data["Manufacturer"]]);

        return new JsonResponse(getFirstMatch($result));
    }


    # Позволяет получить все запчасти, которые подходят для данного принтера
    public function parts_for_printer(ServerRequest $request)
    {
        $data = $request->getQueryParams();
        $validatior = new Validator;
        $validation = $validatior->make($data, [
            'PrinterModel' => 'required',
            # Необязательное поле, если оно присутствует,
            # То поиск будет вестись только для заданного типа запчастей
            # Если это поле равно пустой строке, то поиск будет вестись по всем запчастям
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

    #Данная функция не принимает аргументов и возвращает все типы запчастей, заданные в базе
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