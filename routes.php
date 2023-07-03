<?php
require 'vendor/autoload.php';

use Laminas\Diactoros\ServerRequest;
use MiladRahimi\PhpRouter\Router;
use MiladRahimi\PhpRouter\Exceptions\RouteNotFoundException;

use Laminas\Diactoros\Response\EmptyResponse;
use Laminas\Diactoros\Response\HtmlResponse;
use Laminas\Diactoros\Response\JsonResponse;
use Laminas\Diactoros\Response\TextResponse;
use Laminas\Diactoros\Response\RedirectResponse;
use MiladRahimi\PhpRouter\View\View;

require_once(__DIR__ . "\\Http\\Controllers\\CreationController.php");
require_once(__DIR__ . "\\Http\\Controllers\\RequestController.php");
require_once(__DIR__ . "\\Http\\Controllers\\SearchController.php");
require_once(__DIR__ . "\\Http\\Controllers\\EditController.php");

$router = Router::create();
$router->setupView(__DIR__ . "/views/");

# ROUTE DEFINITION

$router->post('/create/printer_model', [CreationController::class, 'printer_model']);
$router->post('/create/printer', [CreationController::class, 'printer']);
$router->post('/create/shipment', [CreationController::class, 'shipment']);
$router->post('/create/part-type', [CreationController::class, 'part_type']);

$router->post('/change/take_part', [EditController::class, 'take_part']);

$router->get('/get/part', [RequestController::class, 'part']);
$router->get('/get/printer', [RequestController::class, 'printer']);
$router->get("/get/parts_for_printer", [RequestController::class, 'parts_for_printer']);
$router->get('/get/types', [RequestController::class, 'types']);

$router->get('/get/search/parts', [SearchController::class, 'part_search']);
$router->get('/get/search/printer_models', [SearchController::class, 'printer_model_search']);
$router->get('/get/search/printers', [SearchController::class, 'printer_search']);

# ROUTE DEFINITION

#TEST ROUTES

$router->get('/create/printer', function (View $view) {
    return $view->make('create_printer_form');
});

$router->get('/create/part', function (View $view) {
    return $view->make('create_part_form');
});

$router->get('/', function (View $view) {
    return $view->make('part_search.search_parts_view');
});

#TEST ROUTES

try {
    $router->dispatch();
} catch (RouteNotFoundException $e) {
    // It's 404!
    $router->getPublisher()->publish(new HtmlResponse('Not found.', 404));
} catch (Throwable $e) {
    // Log and report...
    $router->getPublisher()->publish(new JsonResponse($e->getMessage(), 500));
}

?>