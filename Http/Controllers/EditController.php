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

# Используется для редактирования некоторых свойств уже существующих записей
class EditController
{
    # Функция отвечает за использование запчасти:
    # запчасть с некоторым номером(ID) используют для принтера
    # с некоторым серийным номером(Serial)
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
        // VALIDATION

        $use_log = new PartUse;
        $use_log->PartID = $data["ID"];
        $use_log->PrinterSerial = $data["Serial"];
        $use_log->UseTime = date("Y-m-d H:i:s");
        $use_log->save();

        $part = Part::get($data["ID"]);
        $part->Count -= 1;

        if ($part->Count != 0) {
            return new TextResponse("Запчасти нет на складе", 400);
        } else {
            $part->save();
        }

        return new TextResponse("Log saved succesfully", 200);
    }
}

?>