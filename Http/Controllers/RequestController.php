<?php

use Database\Models\PartAssociasion;
use Laminas\Diactoros\Response\JsonResponse;
use Laminas\Diactoros\Response\TextResponse;
use Laminas\Diactoros\ServerRequest;
use Database\Models\Part;
use Database\Models\Printer;
use NilPortugues\Sql\QueryBuilder\Builder\GenericBuilder;
use NilPortugues\Sql\QueryBuilder\Syntax\OrderBy;


require_once("Database\\Models\\Part.php");
require_once("Database\\Models\\Printer.php");
require_once("utils.php");

class RequestController {
    public function parts(ServerRequest $request) {
        $data = $request->getQueryParams();

        if (!testRequired($data, ["PrinterModel"])) {
            #return new JsonResponse($data, 400);
            return new TextResponse("Request missing required field(s)", 400);
        }
        $builder = new GenericBuilder();
        $parts = [];

        $select_query_raw = file_get_contents("Database\\Querries\\printer_select_query.sql");
        global $conn;
        $select_query = $conn->prepare($select_query_raw);

        $model = $data['PrinterModel'];
        $part_type = $data['PartType'];

        $select_query->bind_param('sss', $model, $part_type, $part_type);
        #$select_query->bind_param('s', $model);
        $select_query->execute();

        $result = $select_query->get_result();
        $result->fetch_all($mode=2);

        foreach ($result as $r) {
            $parts[] = $r;
        }

        return new JsonResponse($parts, 200);
    }
}

?>