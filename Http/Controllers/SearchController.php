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
    public function part_search(ServerRequest $request)
    {
        global $conn;

        $data = $request->getQueryParams();
        $query_info = "SELECT `parts`.*, `part_types`.`PartType` FROM `parts`
            LEFT JOIN `part_types` ON `parts`.`PartType` = `part_types`.`ID` WHERE `parts`.`ID` = ?";
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