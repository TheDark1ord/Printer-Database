<?php
require 'vendor/autoload.php';

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

$router = Router::create();
$router->setupView(__DIR__ . "/views/");

# ROUTE DEFINITION

$router->post('/create/printer', [CreationController::class, 'printer']);
$router->post('/create/part', [CreationController::class, 'part']);
$router->post('/create/part-type', [CreationController::class, 'part_type']);

$router->get("/get/parts", [RequestController::class, 'parts']);

# ROUTE DEFINITION

#TEST ROUTES

$router->get('/create/printer', function (View $view) {
    return $view->make('create_printer_form');
});

$router->get('/create/part', function (View $view) {
    return $view->make('create_part_form');
});

$router->get('/', function (View $view) {
    return $view->make('default_page_view');
});

#TEST ROUTES

try {
    $router->dispatch();
} catch (RouteNotFoundException $e) {
    // It's 404!
    $router->getPublisher()->publish(new HtmlResponse('Not found.', 404));
} catch (Throwable $e) {
    // Log and report...
    $router->getPublisher()->publish(new HtmlResponse('Internal error.', 500));
}

?>