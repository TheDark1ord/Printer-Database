<?php

use Laminas\Diactoros\ServerRequest;
use Laminas\Diactoros\Response\JsonResponse;

class CreationController
{
    function index(ServerRequest $request) {
        
    }

    function printer(ServerRequest $request) {
        return new JsonResponse(['error' => 'Unauthorized!'], 401);
    }

    function part(ServerRequest $request) {
        echo $request->getAttributes();
    }

    function part_type(ServerRequest $request) {
        echo $request->getAttributes();
    }

    function handle() {
        return "Class CreationController.";
    }
}

?>