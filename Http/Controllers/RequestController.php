<?php

use Database\Models\Printer;
use Laminas\Diactoros\Response\TextResponse;
use Rakit\Validation\Rules\Json;
use Rakit\Validation\Validator;

use Laminas\Diactoros\Response\JsonResponse;
use Laminas\Diactoros\ServerRequest;
use Database\Models\Part;

require_once("Database\\Models\\PrinterModel.php");
require_once("Database\\Models\\Printer.php");
require_once("Database\\Models\\Part.php");
require_once("utils.php");

//TODO: Сделать приведение всех названий к lowercase при создании записей

# Используется для поиска в базе данных записей по точному соответствию некоторого поля
# Например поиск принтера по его серийному номеру
class RequestController
{
    # Возвращает в том числе и запчасти, которых нет на складе(count = 0)
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
            return new JsonResponse(print_r($validation->errors()->firstOfAll()), 400);
        }
        $result = Part::select(["PartName" => $data["PartName"], "Manufacturer" => $data["Manufacturer"]]);

        return new JsonResponse(getFirstMatch($result));
    }

    # Позволяет получить информацию о запчасти и список принтеров, к которым эта запчасть подходит
    public function part_search(ServerRequest $request)
    {
        global $conn;

        $data = $request->getQueryParams();
        # Информация о запчасти
        $query_info = "SELECT `parts`.*, `part_types`.`PartType` FROM `parts`
            LEFT JOIN `part_types` ON `parts`.`PartType` = `part_types`.`ID` WHERE `parts`.`ID` = ?";
        # Поддерживаемые принтеры
        $query_printers = 'SELECT `PrinterModel`, `isOriginal`, (`PrinterID` IS NOT NULL) AS `InDatabase`
            FROM `parts_association` WHERE `PartID` = ?';

        $stmt = $conn->prepare($query_info);

        $stmt->bind_param("i", $data["ID"]);
        $stmt->execute();

        $result = $stmt->get_result();
        $info = getFirstMatch($result);

        mysqli_next_result($conn);

        $stmt = $conn->prepare($query_printers);
        if (gettype($stmt) == "boolean") {
            throw new JsonException($conn->error);
        }
        $stmt->bind_param("i", $data["ID"]);

        $stmt->execute();
        $result = $stmt->get_result();

        $printer_models = [];
        foreach ($result as $r) {
            $printer_models[] = $r;
        }

        return new JsonResponse(["info" => $info, "models" => $printer_models]);
    }

    public function use_log(ServerRequest $request)
    {
        $data = $request->getQueryParams();

        $validator = new Validator;
        $validation = $validator->make($data, [
            # Либо PartID, либо PartName и Manufacturer
            'PartID' => 'required_without:PartName,Manufacturer',
            'PartName' => 'required_without:PartID',
            'Manufacturer' => 'required_without:PartID',
            'DateFrom' => 'required|date',
            'DateTo' => 'required|date'
        ]);
        if ($validation->fails()) {
            return new JsonResponse(print_r($validation->errors()->firstOfAll()), 400);
        }

        if (array_key_exists('PartID', $data)) {
            $data['PartName'] = null;
            $data['Manufacturer'] = null;
        } else {
            $data['PartID'] = null;
        }

        $query = file_get_contents("Database\\Querries\\use_log_select_query.sql");

        global $conn;
        $stmt = $conn->prepare($query);

        $stmt->bind_param(
            "issss",
            $data['PartID'], $data['PartName'],
            $data['Manufacturer'],
            $data['DateFrom'], $data['DateTo']
        );
        $stmt->execute();
        $result = $stmt->get_result();

        $logs = [];
        foreach ($result as $log) {
            $logs[] = $log;
        }
        return $logs;
    }

    public function shipments(ServerRequest $request)
    {
        global $conn;
        $data = $request->getQueryParams();

        $validator = new Validator;
        $validation = $validator->make($data, [
            'PartID' => 'required',
            'DateFrom' => 'date|nullable',
            # Пустая строка означает, что лимита нет
            'DateTo' => 'date|nullable'
        ]);
        $validation->validate();
        if ($validation->fails()) {
            return new JsonResponse(print_r($validation->errors()->firstOfAll()), 400);
        }

        $query = "SELECT `ShipmentDate`, `shipments_relation`.`Count` FROM `parts`
            INNER JOIN `shipments_relation` ON `parts`.`ID` = `shipments_relation`.`PartID`
            INNER JOIN `shipments` ON `shipments_relation`.`ShipmentID` = `shipments`.`ID`
            WHERE `parts`.`ID` = ? %s";

        if ($data['DateFrom'] === "") {
            if ($data['DateTo'] === "") {
                # Взять поставки за все даты
                $query = sprintf($query, "");


                $stmt = $conn->prepare($query);
                $stmt->bind_param("i", $data["PartID"]);
            } else {
                # Поставки только до определенной даты
                $query = sprintf($query, "AND `ShipmentDate` < ?");

                $stmt = $conn->prepare($query);
                $stmt->bind_param("is", $data["PartID"], $date["DateTo"]);
            }
        } else if ($data['DateTo'] === "") {
            # Поставки начиная с определенной даты
            $query = sprintf($query, "AND `ShipmentDate` > ?");

            $stmt = $conn->prepare($query);
            $stmt->bind_param("is", $data["PartID"], $date["DateFrom"]);
        } else {
            # Поставки за период времени
            $query = sprintf($query, "AND `ShipmentDate` BETWEEN ? AND ?");

            $stmt = $conn->prepare($query);
            $stmt->bind_param("iss", $data["PartID"], $data["DateFrom"], $data["DateTo"]);
        }

        $stmt->execute();
        $result = $stmt->get_result();

        $shipments = [];
        foreach ($result as $r) {
            $shipments[] = $r;
        }

        return new JsonResponse($shipments, 200);
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
            return new JsonResponse(print_r($validation->errors()->firstOfAll()), 400);
        }

        $select_query_raw = file_get_contents("Database\\Querries\\parts_select_query.sql");
        global $conn;
        $select_query = $conn->prepare($select_query_raw);

        $model = $data['PrinterModel'];
        $part_type = $data['PartType'];

        $select_query->bind_param('sss', $model, $part_type, $part_type);
        $select_query->execute();

        $result = $select_query->get_result();

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
            return new JsonResponse(print_r($validation->errors()->firstOfAll()), 400);
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