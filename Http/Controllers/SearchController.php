<?php

header("Access-Control-Allow-Origin: *");
header("Access-Control-Allow-Headers: *");

use Laminas\Diactoros\Response\JsonResponse;
use Laminas\Diactoros\ServerRequest;

require_once("Database\\Models\\PrinterModel.php");
require_once("Database\\Models\\Printer.php");
require_once("Database\\Models\\Part.php");
require_once("utils.php");

# По функциональности похож на RequestController, но в данном случае вместо поиска
# точного соответствия некоторого поля используется директива LIKE
class SearchController
{
    public function printer_search(ServerRequest $request)
    {
        $data = $request->getQueryParams();
        $query = 'SELECT `printers`.*, `printer_models`.`Model` FROM `printers`
            INNER JOIN `printer_models` ON `printer_models`.`ID` = `printers`.`Model`
            WHERE (`printer_models`.`Model` = ? OR ? = "" ) AND (`printers`.`SerialNumber` LIKE ?)
            LIMIT ?';

        global $conn;
        $stmt = $conn->prepare($query);

        $limit = 10;
        $search = $data["Search"] . "%";
        $stmt->bind_param("sssi", $data["Model"], $data["Model"], $search, $limit);
        $stmt->execute();

        $result = $stmt->get_result();

        $printers = [];
        foreach ($result as $r) {
            $printers[] = $r;
        }

        return new JsonResponse($printers, 200);
    }

    public function printer_model_search(ServerRequest $request)
    {
        $data = $request->getQueryParams();
        $query = "SELECT `printer_models`.`Model` FROM `printer_models` WHERE UPPER(`printer_models`.`MODEL`) LIKE UPPER(?) LIMIT ?";

        global $conn;
        $stmt = $conn->prepare($query);

        $limit = 10;
        $search = $data["Search"] . "%";
        $stmt->bind_param("si", $search, $limit);
        $stmt->execute();

        $result = $stmt->get_result();

        $printers = [];
        foreach ($result as $r) {
            $printers[] = $r;
        }

        return new JsonResponse($printers, 200);
    }
}

?>